<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use App\Models\User;
use App\Models\RuangKontrol;
use App\Helpers\TahunAjaranHelper;
use App\Helpers\ProposalHelper;
use App\Services\NotificationService;
use App\Repositories\Firebase\ReviewDetailRepository;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewerController extends Controller
{
    public function __construct(
        private ReviewDetailRepository $reviewDetailRepository,
        private FirebaseService $firebaseService
    ) {}

    /**
     * Ambil ruang kontrol aktif untuk tahun ajaran terbaru
     */
    private function getActiveRuangKontrol()
    {
        $tahunAjaranTerbaru = TahunAjaranHelper::getTahunAjaranTerbaru();
        
        // Cari yang aktif untuk tahun ajaran terbaru
        $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
            ->where('is_active', true)
            ->first();
        
        // Fallback: jika tidak ada yang aktif, ambil yang pertama untuk tahun ajaran terbaru
        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        return $ruangKontrol;
    }
    
    /**
     * Helper function untuk flatten array
     */
    private function arrayFlatten($array)
    {
        $result = [];
        foreach ($array as $item) {
            if (is_array($item)) {
                $result = array_merge($result, $this->arrayFlatten($item));
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }

    public function dashboard()
    {
        $reviewer = Auth::user();
        $tahunAjaranTerpilih = request('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        $tahunAjaranList = TahunAjaranHelper::getListTahunAjaran();
        
        \Log::info('Reviewer accessing dashboard', [
            'reviewer_id' => $reviewer->id,
            'tahun_ajaran' => $tahunAjaranTerpilih
        ]);
        
        // Ambil semua proposal yang ditugaskan ke reviewer ini (administratif atau substantif)
        // Logic sama dengan halaman review: berdasarkan assignment reviewer
        $proposals = Proposal::with(['mahasiswa', 'dosen', 'nilaiAdministratif', 'nilaiSubstantif'])
            ->where(function($query) use ($reviewer) {
                $query->where('id_reviewer_administratif', $reviewer->id)
                      ->orWhere('id_reviewer_substantif_1', $reviewer->id)
                      ->orWhere('id_reviewer_substantif_2', $reviewer->id);
            })
            ->where('status_validasi', 'valid')
            ->where('tahun_ajaran', $tahunAjaranTerpilih)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        \Log::info('Proposals found for dashboard', [
            'reviewer_id' => $reviewer->id,
            'proposal_count' => $proposals->count(),
            'proposals' => $proposals->map(function($p) use ($reviewer) {
                $isAdminReviewer = $p->id_reviewer_administratif == $reviewer->id;
                $isSubstantifReviewer = $p->id_reviewer_substantif_1 == $reviewer->id || 
                                       $p->id_reviewer_substantif_2 == $reviewer->id;
                
                return [
                    'id' => $p->id_proposal,
                    'status' => $p->status,
                    'is_admin_reviewer' => $isAdminReviewer,
                    'is_substantif_reviewer' => $isSubstantifReviewer
                ];
            })
        ]);

        // Hitung statistik berdasarkan proposal yang valid
        $totalAssigned = $proposals->count();
        $completedReview = $proposals->filter(function($proposal) use ($reviewer) {
            // Cek apakah reviewer ini adalah reviewer administratif
            if ($proposal->id_reviewer_administratif == $reviewer->id) {
                $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', $reviewer->id)->first();
                return $adminReview && $adminReview->note_administratif && !empty($adminReview->checklist);
            }
            
            // Cek apakah reviewer ini adalah reviewer substantif
            if ($proposal->id_reviewer_substantif_1 == $reviewer->id || 
                $proposal->id_reviewer_substantif_2 == $reviewer->id) {
                $substantifReview = $proposal->nilaiSubstantif->where('id_reviewer', $reviewer->id)->first();
                return $substantifReview && $substantifReview->note_substantif && 
                       $substantifReview->note_substantif !== 'Review substantif dimulai';
            }
            
            return false;
        })->count();
        $pendingReview = $totalAssigned - $completedReview;
        
        \Log::info('Dashboard statistics calculated', [
            'reviewer_id' => $reviewer->id,
            'total_assigned' => $totalAssigned,
            'completed_review' => $completedReview,
            'pending_review' => $pendingReview
        ]);

        return view('reviewer.dashboard', compact('proposals', 'totalAssigned', 'completedReview', 'pendingReview', 'tahunAjaranTerpilih', 'tahunAjaranList'));
    }

    public function reviewAdministratif()
    {
        $reviewer = Auth::user();
        $tahunAjaranTerpilih = request('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        $tahunAjaranList = TahunAjaranHelper::getListTahunAjaran();
        
        \Log::info('Reviewer accessing administratif review page', [
            'reviewer_id' => $reviewer->id,
            'tahun_ajaran' => $tahunAjaranTerpilih
        ]);
        
        // Ambil proposal yang ditugaskan untuk review administratif
        // Logic sama dengan dashboard: proposal dimana reviewer ini adalah id_reviewer_administratif
        $proposals = Proposal::with(['mahasiswa', 'dosen', 'nilaiAdministratif'])
            ->where('id_reviewer_administratif', $reviewer->id)
            ->where('status_validasi', 'valid')
            ->where('tahun_ajaran', $tahunAjaranTerpilih)
            ->orderBy('created_at', 'desc')
            ->get();
            
        \Log::info('Proposals found for administratif review', [
            'reviewer_id' => $reviewer->id,
            'proposal_count' => $proposals->count(),
            'proposals' => $proposals->map(function($p) {
                return [
                    'id' => $p->id_proposal,
                    'status' => $p->status,
                    'judul' => $p->judul_proposal,
                    'is_admin_reviewer' => true
                ];
            })
        ]);

        return view('reviewer.review_administratif', compact('proposals', 'tahunAjaranTerpilih', 'tahunAjaranList'));
    }

    public function reviewSubstantif()
    {
        $reviewer = Auth::user();
        $tahunAjaranTerpilih = request('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        $tahunAjaranList = TahunAjaranHelper::getListTahunAjaran();
        
        \Log::info('Reviewer accessing substantif review page', [
            'reviewer_id' => $reviewer->id,
            'tahun_ajaran' => $tahunAjaranTerpilih
        ]);
        
        // Ambil proposal yang ditugaskan untuk review substantif
        // Logic sama dengan dashboard: proposal dimana reviewer ini adalah id_reviewer_substantif_1 atau id_reviewer_substantif_2
        $proposals = Proposal::with(['mahasiswa', 'dosen', 'nilaiSubstantif'])
            ->where(function($query) use ($reviewer) {
                $query->where('id_reviewer_substantif_1', $reviewer->id)
                      ->orWhere('id_reviewer_substantif_2', $reviewer->id);
            })
            ->where('status_validasi', 'valid')
            ->where('tahun_ajaran', $tahunAjaranTerpilih)
            ->orderBy('created_at', 'desc')
            ->get();
            
        \Log::info('Proposals found for substantif review', [
            'reviewer_id' => $reviewer->id,
            'proposal_count' => $proposals->count(),
            'proposals' => $proposals->map(function($p) use ($reviewer) {
                $isSubstantif1 = $p->id_reviewer_substantif_1 == $reviewer->id;
                $isSubstantif2 = $p->id_reviewer_substantif_2 == $reviewer->id;
                
                return [
                    'id' => $p->id_proposal,
                    'status' => $p->status,
                    'judul' => $p->judul_proposal,
                    'is_substantif_reviewer_1' => $isSubstantif1,
                    'is_substantif_reviewer_2' => $isSubstantif2
                ];
            })
        ]);

        return view('reviewer.review_substantif', compact('proposals', 'tahunAjaranTerpilih', 'tahunAjaranList'));
    }

    public function detailProposalSubstantif($id)
    {
        try {
            $reviewer = Auth::user();
            \Log::info('Reviewer accessing substantif detail proposal', [
                'reviewer_id' => $reviewer->id,
                'proposal_id' => $id
            ]);
            
            $proposal = Proposal::with(['mahasiswa', 'dosen', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif'])->findOrFail($id);
            
            \Log::info('Proposal found for substantif review', [
                'proposal_id' => $proposal->id_proposal,
                'status' => $proposal->status,
                'status_validasi' => $proposal->status_validasi,
                'id_reviewer_substantif_1' => $proposal->id_reviewer_substantif_1,
                'id_reviewer_substantif_2' => $proposal->id_reviewer_substantif_2
            ]);
            
            // Cek apakah proposal sudah valid
            if ($proposal->status_validasi !== 'valid') {
                \Log::warning('Proposal not validated for substantif review', ['proposal_id' => $id]);
                abort(403, 'Proposal belum divalidasi dan tidak dapat direview');
            }
            
            // Cek apakah reviewer ditugaskan untuk substantif review
            $isSubstantif = $proposal->id_reviewer_substantif_1 == $reviewer->id || 
                           $proposal->id_reviewer_substantif_2 == $reviewer->id;
            
            if (!$isSubstantif) {
                \Log::warning('Reviewer not assigned for substantif review', [
                    'reviewer_id' => $reviewer->id,
                    'proposal_id' => $id
                ]);
                abort(403, 'Anda tidak ditugaskan untuk review substantif proposal ini');
            }
            
            // Cek apakah review administratif sudah selesai (tidak memblokir akses, hanya untuk informasi)
            $adminReviewCompleted = false;
            if ($proposal->id_reviewer_administratif) {
                $adminReview = $proposal->nilaiAdministratif()
                    ->where('id_reviewer', $proposal->id_reviewer_administratif)
                    ->whereNotNull('note_administratif')
                    ->where('note_administratif', '!=', 'Review administratif dimulai')
                    ->first();
                
                $adminReviewCompleted = $adminReview ? true : false;
                
                \Log::info('Administrative review status check', [
                    'proposal_id' => $id,
                    'admin_reviewer_id' => $proposal->id_reviewer_administratif,
                    'admin_review_completed' => $adminReviewCompleted,
                    'admin_review_note' => $adminReview ? $adminReview->note_administratif : 'No review found'
                ]);
            }

            \Log::info('Successfully accessing substantif proposal detail', [
                'proposal_id' => $id,
                'reviewer_id' => $reviewer->id,
                'admin_review_completed' => $adminReviewCompleted
            ]);

            // Ambil kriteria penilaian substantif berdasarkan skim proposal
            $criteria = ProposalHelper::getSubstantifCriteria($proposal->skim);
            
            // Ambil existing review untuk logging
            $existingReview = $proposal->nilaiSubstantif->where('id_reviewer', $reviewer->id)->first();
            $existingSkor = $existingReview ? ($existingReview->skor_per_kriteria ?? []) : [];
            
            \Log::info('Loading substantif criteria', [
                'proposal_id' => $id,
                'skim' => $proposal->skim,
                'criteria_count' => count($criteria),
                'existing_review_id' => $existingReview ? $existingReview->id : null,
                'existing_skor' => $existingSkor,
                'existing_skor_count' => count($existingSkor),
                'existing_skor_type' => gettype($existingSkor),
                'existing_skor_keys' => is_array($existingSkor) ? array_keys($existingSkor) : []
            ]);

            return view('reviewer.detail_proposal_substantif', compact('proposal', 'adminReviewCompleted', 'criteria'));
            
        } catch (\Exception $e) {
            \Log::error('Error in detailProposalSubstantif', [
                'proposal_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Terjadi kesalahan saat mengakses detail proposal substantif: ' . $e->getMessage());
        }
    }

    public function detailProposal($id)
    {
        try {
            $reviewer = Auth::user();
            \Log::info('Reviewer accessing detail proposal', [
                'reviewer_id' => $reviewer->id,
                'proposal_id' => $id
            ]);
            
            $proposal = Proposal::with(['mahasiswa', 'dosen', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif'])->findOrFail($id);
            
            \Log::info('Proposal found', [
                'proposal_id' => $proposal->id_proposal,
                'status' => $proposal->status,
                'status_validasi' => $proposal->status_validasi,
                'id_reviewer_administratif' => $proposal->id_reviewer_administratif,
                'id_reviewer_substantif_1' => $proposal->id_reviewer_substantif_1,
                'id_reviewer_substantif_2' => $proposal->id_reviewer_substantif_2
            ]);
            
            // Cek apakah proposal sudah valid
            if ($proposal->status_validasi !== 'valid') {
                \Log::warning('Proposal not validated', ['proposal_id' => $id]);
                abort(403, 'Proposal belum divalidasi dan tidak dapat direview');
            }
            
            // Cek apakah reviewer ditugaskan untuk proposal ini
            $isAdministratif = $proposal->id_reviewer_administratif == $reviewer->id;
            $isSubstantif = $proposal->id_reviewer_substantif_1 == $reviewer->id || 
                           $proposal->id_reviewer_substantif_2 == $reviewer->id;
            
            \Log::info('Reviewer assignment check', [
                'isAdministratif' => $isAdministratif,
                'isSubstantif' => $isSubstantif,
                'reviewer_id' => $reviewer->id
            ]);
            
            if (!$isAdministratif && !$isSubstantif) {
                \Log::warning('Reviewer not assigned to proposal', [
                    'reviewer_id' => $reviewer->id,
                    'proposal_id' => $id
                ]);
                abort(403, 'Anda tidak memiliki akses ke proposal ini');
            }
            
            // Cek apakah status proposal sesuai untuk review
            // Untuk administratif review, proposal harus dalam status yang memungkinkan review administratif
            if ($isAdministratif && !in_array($proposal->status, ['review_administratif', 'review_substantif', 'review_completed'])) {
                \Log::warning('Proposal not ready for administratif review', [
                    'proposal_id' => $id,
                    'status' => $proposal->status
                ]);
                abort(403, 'Proposal belum siap untuk review administratif. Status saat ini: ' . ucfirst(str_replace('_', ' ', $proposal->status)));
            }
            
            // Untuk substantif review, proposal bisa dilakukan bersamaan dengan administratif
            if ($isSubstantif && !in_array($proposal->status, ['review_administratif', 'review_substantif', 'review_completed', 'revisi'])) {
                \Log::warning('Proposal status not suitable for substantif review', [
                    'proposal_id' => $id,
                    'current_status' => $proposal->status
                ]);
                
                abort(403, 'Proposal belum siap untuk review substantif. Status saat ini: ' . ucfirst(str_replace('_', ' ', $proposal->status)));
            }

            // Ensure checklist data is properly formatted for administratif reviews
            if ($isAdministratif) {
                $adminReviews = $proposal->nilaiAdministratif;
                foreach ($adminReviews as $review) {
                    if ($review->checklist && !is_array($review->checklist)) {
                        \Log::warning('Invalid checklist data found, attempting to fix', [
                            'review_id' => $review->id,
                            'checklist_type' => gettype($review->checklist),
                            'checklist_value' => $review->checklist
                        ]);
                        
                        // Try to decode if it's a JSON string
                        if (is_string($review->checklist)) {
                            $decoded = json_decode($review->checklist, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $review->checklist = $decoded;
                                \Log::info('Successfully decoded checklist JSON', [
                                    'review_id' => $review->id,
                                    'decoded_checklist' => $decoded
                                ]);
                            } else {
                                // If JSON decode fails, set to empty array
                                $review->checklist = [];
                                \Log::warning('Failed to decode checklist JSON, setting to empty array', [
                                    'review_id' => $review->id,
                                    'json_error' => json_last_error_msg()
                                ]);
                            }
                        } else {
                            // If it's not a string, set to empty array
                            $review->checklist = [];
                            \Log::warning('Checklist is not string or array, setting to empty array', [
                                'review_id' => $review->id,
                                'checklist_type' => gettype($review->checklist)
                            ]);
                        }
                    }
                }
            }

            \Log::info('Successfully accessing proposal detail', [
                'proposal_id' => $id,
                'reviewer_id' => $reviewer->id
            ]);

            // Redirect ke halaman yang sesuai berdasarkan tipe review
            if ($isAdministratif) {
                // Ambil checklist dinamis berdasarkan skim proposal
                $checklist = ProposalHelper::getReviewChecklist($proposal->skim);
                
                return view('reviewer.detail_proposal_administratif', compact('proposal', 'checklist'));
            } else {
                // Untuk substantif review, bisa dilakukan bersamaan dengan administratif
                $adminReviewCompleted = $this->isAdminReviewCompleted($proposal);
                
                return view('reviewer.detail_proposal_substantif', compact('proposal', 'adminReviewCompleted'));
            }
            
        } catch (\Exception $e) {
            \Log::error('Error in detailProposal', [
                'proposal_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Terjadi kesalahan saat mengakses detail proposal: ' . $e->getMessage());
        }
    }

    public function submitReviewAdministratif(Request $request, $id)
    {
        try {
            \Log::info('Starting administratif review submission', [
                'proposal_id' => $id,
                'request_data' => $request->all(),
                'timestamp' => now()
            ]);

            // Validasi input
        $request->validate([
            'catatan' => 'required|string|max:1000',
            'kesalahan_administratif' => 'required|array|min:1',
            'kesalahan_administratif.*' => 'string'
        ]);

        $reviewer = Auth::user();
        $proposal = Proposal::findOrFail($id);

            \Log::info('Reviewer and proposal found', [
                'reviewer_id' => $reviewer->id,
                'proposal_id' => $proposal->id_proposal,
                'proposal_status' => $proposal->status,
                'proposal_validation' => $proposal->status_validasi
            ]);

        // Cek apakah proposal sudah valid
        if ($proposal->status_validasi !== 'valid') {
                \Log::warning('Proposal not validated for administratif review', [
                    'proposal_id' => $id,
                    'status_validasi' => $proposal->status_validasi
                ]);
                
            return response()->json([
                'success' => false,
                'message' => 'Proposal belum divalidasi dan tidak dapat direview'
            ], 403);
        }

        // Cek apakah reviewer ditugaskan untuk review administratif
        if ($proposal->id_reviewer_administratif != $reviewer->id) {
                \Log::warning('Reviewer not assigned for administratif review', [
                    'reviewer_id' => $reviewer->id,
                    'assigned_reviewer' => $proposal->id_reviewer_administratif,
                    'proposal_id' => $id
                ]);
                
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak ditugaskan untuk review administratif proposal ini'
            ], 403);
        }
        
        // Cek apakah status proposal sesuai
            if (!in_array($proposal->status, ['review_administratif', 'review_substantif', 'review_completed'])) {
                \Log::warning('Proposal status not suitable for administratif review', [
                    'proposal_id' => $id,
                    'current_status' => $proposal->status
                ]);
                
            return response()->json([
                'success' => false,
                    'message' => 'Proposal belum siap untuk review administratif. Status saat ini: ' . ucfirst(str_replace('_', ' ', $proposal->status))
            ], 403);
        }

            // Prepare data untuk disimpan
            $catatan = $request->input('catatan');
            $kesalahanAdministratif = $request->input('kesalahan_administratif');
            
            \Log::info('Data to be saved', [
                'catatan' => $catatan,
                'catatan_length' => strlen($catatan),
                'kesalahan_administratif' => $kesalahanAdministratif,
                'kesalahan_count' => is_array($kesalahanAdministratif) ? count($kesalahanAdministratif) : 'not_array'
            ]);

            // Update atau create nilai administratif
            $nilaiAdmin = NilaiAdministratif::updateOrCreate(
            [
                'id_proposal' => $id,
                'id_reviewer' => $reviewer->id
            ],
            [
                    'note_administratif' => $catatan,
                    'checklist' => $kesalahanAdministratif,
                'updated_at' => now()
            ]
        );

            \Log::info('Nilai administratif saved successfully', [
                'nilai_id' => $nilaiAdmin->id,
                'proposal_id' => $id,
                'reviewer_id' => $reviewer->id,
                'note_length' => strlen($catatan),
                'checklist_count' => is_array($kesalahanAdministratif) ? count($kesalahanAdministratif) : 'not_array',
                'saved_data' => [
                    'note_administratif' => $nilaiAdmin->note_administratif,
                    'checklist' => $nilaiAdmin->checklist
                ]
            ]);

            try {
                if ($this->firebaseService->isAvailable()) {
                    $this->reviewDetailRepository->createReviewDetail('administratif', $nilaiAdmin->id, [
                        'checklist_selected' => $kesalahanAdministratif,
                        'catatan' => $catatan,
                        'proposal_id' => (int) $id,
                        'reviewer_id' => $reviewer->id,
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning('Firestore review detail (administratif) sync failed', [
                    'nilai_id' => $nilaiAdmin->id,
                    'proposal_id' => $id,
                    'error' => $e->getMessage(),
                ]);
            }

        // Cek apakah semua review administratif sudah selesai
            $this->updateProposalStatus($proposal);
            
            // Kirim notifikasi jika review administratif selesai
            try {
                $adminCompleted = $this->isAdminReviewCompleted($proposal->fresh());
                if ($adminCompleted) {
                    $notificationService = app(NotificationService::class);
                    // Review administratif dianggap lolos jika ada checklist (tidak kosong)
                    $lolos = !empty($kesalahanAdministratif) && count($kesalahanAdministratif) > 0;
                    $notificationService->notifyReviewAdministratifSelesai(
                        $proposal->fresh(),
                        $lolos,
                        $catatan
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim notifikasi review administratif: ' . $e->getMessage());
            }

        if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
                'message' => 'Review administratif berhasil disimpan',
                'data' => [
                    'id' => $nilaiAdmin->id,
                    'proposal_id' => $id,
                    'reviewer_id' => $reviewer->id,
                    'saved_catatan' => $catatan,
                    'saved_kesalahan' => $kesalahanAdministratif,
                    'new_status' => $proposal->fresh()->status
                ]
            ]);
        }
        
        return redirect()->route('reviewer.detail.proposal', $id)
            ->with('success', 'Review administratif berhasil disimpan.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in administratif review', [
                'proposal_id' => $id,
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $this->arrayFlatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Error in submitReviewAdministratif', [
                'proposal_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function submitReviewSubstantif(Request $request, $id)
    {
        \Log::info('=== SUBMIT REVIEW SUBSTANTIF START ===');
        \Log::info('Request received', [
            'proposal_id' => $id,
            'request_data' => $request->all(),
            'user_id' => Auth::id(),
            'timestamp' => now()
        ]);
        
        try {

            // Ambil kriteria penilaian untuk validasi
            $criteria = ProposalHelper::getSubstantifCriteria($request->skim ?? 'default');
            
            // Buat rules validasi dinamis untuk skor
            $validationRules = [
                'catatan' => 'required|string|min:50|max:1000',
                'skor' => 'required|array',
                'skor.*' => 'required|numeric|min:0|max:10'
            ];

            // Validasi input
            $request->validate($validationRules);

        $reviewer = Auth::user();
        $proposal = Proposal::findOrFail($id);
        
        // Pastikan kriteria sesuai dengan skim proposal
        if (empty($criteria)) {
            $criteria = ProposalHelper::getSubstantifCriteria($proposal->skim);
        }

            \Log::info('Reviewer and proposal found', [
                'reviewer_id' => $reviewer->id,
                'proposal_id' => $proposal->id_proposal,
                'proposal_status' => $proposal->status,
                'proposal_validation' => $proposal->status_validasi
            ]);

        // Cek apakah proposal sudah valid
        if ($proposal->status_validasi !== 'valid') {
                \Log::warning('Proposal not validated for substantif review', [
                    'proposal_id' => $id,
                    'status_validasi' => $proposal->status_validasi
                ]);
                
            return response()->json([
                'success' => false,
                'message' => 'Proposal belum divalidasi dan tidak dapat direview'
            ], 403);
        }

        // Cek apakah reviewer ditugaskan untuk review substantif
            $isSubstantifReviewer = in_array($reviewer->id, [
                $proposal->id_reviewer_substantif_1,
                $proposal->id_reviewer_substantif_2
            ]);

            if (!$isSubstantifReviewer) {
                \Log::warning('Reviewer not assigned for substantif review', [
                    'reviewer_id' => $reviewer->id,
                    'assigned_reviewers' => [
                        'substantif_1' => $proposal->id_reviewer_substantif_1,
                        'substantif_2' => $proposal->id_reviewer_substantif_2
                    ],
                    'proposal_id' => $id
                ]);
                
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak ditugaskan untuk review substantif proposal ini'
            ], 403);
        }

            // Review substantif bisa dilakukan bersamaan dengan administratif
            \Log::info('Substantif review can be done simultaneously with administratif', [
                'proposal_id' => $id,
                'current_status' => $proposal->status
            ]);
        
        // Cek apakah status proposal sesuai - izinkan review substantif bersamaan dengan administratif
        if (!in_array($proposal->status, ['review_administratif', 'review_substantif', 'review_completed'])) {
            \Log::warning('Proposal status not suitable for substantif review', [
                'proposal_id' => $id,
                'current_status' => $proposal->status
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Proposal belum siap untuk review substantif. Status saat ini: ' . ucfirst(str_replace('_', ' ', $proposal->status))
            ], 403);
        }

            // Prepare data untuk disimpan
            $catatan = $request->input('catatan');
            $skorPerKriteriaRaw = $request->input('skor', []);
            
            \Log::info('Raw skor data received', [
                'skor_raw' => $skorPerKriteriaRaw,
                'skor_raw_type' => gettype($skorPerKriteriaRaw),
                'skor_raw_count' => is_array($skorPerKriteriaRaw) ? count($skorPerKriteriaRaw) : 0,
                'all_request_keys' => array_keys($request->all())
            ]);
            
            // Pastikan skor adalah array
            if (!is_array($skorPerKriteriaRaw)) {
                \Log::error('Skor data is not an array', [
                    'skor_raw' => $skorPerKriteriaRaw,
                    'type' => gettype($skorPerKriteriaRaw)
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Data skor tidak valid. Silakan refresh halaman dan coba lagi.'
                ], 422);
            }
            
            // Normalize array index menjadi numerik (0, 1, 2, dst)
            // Form mengirim array dengan key string, kita perlu convert ke numeric array
            $skorPerKriteria = [];
            foreach ($skorPerKriteriaRaw as $key => $value) {
                // Skip jika value kosong atau null
                if ($value === null || $value === '' || $value === false) {
                    continue;
                }
                
                $index = (int) $key; // Convert string key ke integer
                $skorValue = (float) $value; // Convert value ke float
                
                // Validasi skor harus antara 0-10
                if ($skorValue < 0) {
                    $skorValue = 0;
                } elseif ($skorValue > 10) {
                    $skorValue = 10;
                }
                
                $skorPerKriteria[$index] = $skorValue;
            }
            
            // Sort by key untuk memastikan urutan benar
            ksort($skorPerKriteria);
            
            \Log::info('Normalized skor data', [
                'skor_normalized' => $skorPerKriteria,
                'skor_count' => count($skorPerKriteria),
                'skor_keys' => array_keys($skorPerKriteria)
            ]);
            
            // Validasi jumlah skor harus sesuai dengan jumlah kriteria yang sebenarnya
            $expectedCount = ProposalHelper::countActualCriteria($criteria);
            $actualCount = count($skorPerKriteria);
            
            if ($actualCount !== $expectedCount) {
                \Log::warning('Jumlah skor tidak sesuai dengan jumlah kriteria', [
                    'expected_count' => $expectedCount,
                    'actual_count' => $actualCount,
                    'skor_per_kriteria' => $skorPerKriteria,
                    'skor_raw' => $skorPerKriteriaRaw
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => "Jumlah skor tidak sesuai. Diharapkan: {$expectedCount}, Diterima: {$actualCount}. Silakan pastikan semua skor sudah diisi."
                ], 422);
            }
            
            // Hitung total nilai dan nilai akhir
            $scoreCalculation = ProposalHelper::calculateSubstantifScore($criteria, $skorPerKriteria);
            
            \Log::info('Data to be saved', [
                'catatan' => $catatan,
                'catatan_length' => strlen($catatan),
                'skor_per_kriteria_raw' => $skorPerKriteriaRaw,
                'skor_per_kriteria_normalized' => $skorPerKriteria,
                'total_nilai' => $scoreCalculation['total_nilai'],
                'nilai_akhir' => $scoreCalculation['nilai_akhir']
            ]);

            // Update atau create nilai substantif
            // Pastikan semua data terisi dengan benar dan tidak null
            $dataToSave = [
                'note_substantif' => $catatan,
                'skor_per_kriteria' => !empty($skorPerKriteria) ? $skorPerKriteria : null,
                'total_nilai' => $scoreCalculation['total_nilai'] ?? null,
                'nilai_akhir' => $scoreCalculation['nilai_akhir'] ?? null,
                'updated_at' => now()
            ];
            
            // Pastikan skor_per_kriteria adalah array yang valid
            if (empty($dataToSave['skor_per_kriteria']) || !is_array($dataToSave['skor_per_kriteria'])) {
                \Log::error('Invalid skor_per_kriteria data', [
                    'skor_per_kriteria' => $skorPerKriteria,
                    'type' => gettype($skorPerKriteria)
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Data skor tidak valid. Silakan coba lagi.'
                ], 422);
            }
            
            \Log::info('Attempting to save nilai substantif', [
                'proposal_id' => $id,
                'reviewer_id' => $reviewer->id,
                'data_to_save' => $dataToSave,
                'skor_per_kriteria_type' => gettype($dataToSave['skor_per_kriteria']),
                'skor_per_kriteria_count' => count($dataToSave['skor_per_kriteria']),
                'skor_per_kriteria_json' => json_encode($dataToSave['skor_per_kriteria'])
            ]);
            
            try {
                // Pastikan data tidak null sebelum save
                if (empty($dataToSave['skor_per_kriteria']) || empty($dataToSave['note_substantif'])) {
                    \Log::error('Data tidak lengkap sebelum save', [
                        'data_to_save' => $dataToSave,
                        'has_skor' => !empty($dataToSave['skor_per_kriteria']),
                        'has_note' => !empty($dataToSave['note_substantif'])
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Data tidak lengkap. Pastikan semua field terisi.'
                    ], 422);
                }
                
                // Gunakan DB transaction untuk memastikan data tersimpan dengan benar
                DB::beginTransaction();
                
            $nilaiSubstantif = NilaiSubstantif::updateOrCreate(
            [
                'id_proposal' => $id,
                'id_reviewer' => $reviewer->id
            ],
                    $dataToSave
                );
                
                // Refresh model untuk memastikan data ter-load dengan benar
                $nilaiSubstantif->refresh();
                
                // Force reload dari database untuk memastikan data ter-load
                $nilaiSubstantif = $nilaiSubstantif->fresh();
                
                // Verifikasi data sebelum commit
                $verificationPassed = true;
                $verificationErrors = [];
                
                if (empty($nilaiSubstantif->note_substantif)) {
                    $verificationPassed = false;
                    $verificationErrors[] = 'note_substantif is empty';
                }
                
                if (empty($nilaiSubstantif->skor_per_kriteria) || !is_array($nilaiSubstantif->skor_per_kriteria)) {
                    $verificationPassed = false;
                    $verificationErrors[] = 'skor_per_kriteria is empty or not array';
                }
                
                if (is_null($nilaiSubstantif->total_nilai)) {
                    $verificationPassed = false;
                    $verificationErrors[] = 'total_nilai is null';
                }
                
                if (is_null($nilaiSubstantif->nilai_akhir)) {
                    $verificationPassed = false;
                    $verificationErrors[] = 'nilai_akhir is null';
                }
                
                // Jika verifikasi gagal, rollback dan return error
                if (!$verificationPassed) {
                    DB::rollBack();
                    \Log::error('Data verification failed before commit', [
                        'nilai_id' => $nilaiSubstantif->id,
                        'errors' => $verificationErrors,
                        'data_attempted' => $dataToSave
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Data tidak tersimpan dengan benar. Silakan coba lagi. Error: ' . implode(', ', $verificationErrors)
                    ], 500);
                }
                
                // Commit transaction jika verifikasi berhasil
                DB::commit();
                
                // Verifikasi data yang tersimpan langsung dari database setelah commit
                $freshData = NilaiSubstantif::find($nilaiSubstantif->id);
                \Log::info('Data saved to database - Verification', [
                    'nilai_id' => $nilaiSubstantif->id,
                    'verification_passed' => $verificationPassed,
                    'saved_skor_per_kriteria' => $freshData->skor_per_kriteria,
                    'saved_skor_per_kriteria_type' => gettype($freshData->skor_per_kriteria),
                    'saved_skor_per_kriteria_count' => is_array($freshData->skor_per_kriteria) ? count($freshData->skor_per_kriteria) : 0,
                    'saved_total_nilai' => $freshData->total_nilai,
                    'saved_nilai_akhir' => $freshData->nilai_akhir,
                    'raw_skor_per_kriteria' => $freshData->getRawOriginal('skor_per_kriteria'),
                    'raw_total_nilai' => $freshData->getRawOriginal('total_nilai'),
                    'raw_nilai_akhir' => $freshData->getRawOriginal('nilai_akhir')
                ]);
                
            } catch (\Exception $saveException) {
                DB::rollBack();
                
                \Log::error('Error saving nilai substantif', [
                    'error' => $saveException->getMessage(),
                    'trace' => $saveException->getTraceAsString(),
                    'data_attempted' => $dataToSave,
                    'file' => $saveException->getFile(),
                    'line' => $saveException->getLine()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data penilaian: ' . $saveException->getMessage()
                ], 500);
            }

            try {
                if ($this->firebaseService->isAvailable()) {
                    $this->reviewDetailRepository->createReviewDetail('substantif', $nilaiSubstantif->id, [
                        'skor_per_kriteria' => $nilaiSubstantif->skor_per_kriteria,
                        'catatan' => $nilaiSubstantif->note_substantif,
                        'total_nilai' => $nilaiSubstantif->total_nilai,
                        'proposal_id' => (int) $id,
                        'reviewer_id' => $reviewer->id,
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning('Firestore review detail (substantif) sync failed', [
                    'nilai_id' => $nilaiSubstantif->id,
                    'proposal_id' => $id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Verifikasi data yang tersimpan
            $savedSkor = $nilaiSubstantif->skor_per_kriteria;
            $savedSkorArray = is_array($savedSkor) ? $savedSkor : json_decode($savedSkor, true);

            \Log::info('Nilai substantif saved successfully', [
                'nilai_id' => $nilaiSubstantif->id,
                'proposal_id' => $id,
                'reviewer_id' => $reviewer->id,
                'note_length' => strlen($catatan),
                'total_nilai' => $scoreCalculation['total_nilai'],
                'nilai_akhir' => $scoreCalculation['nilai_akhir'],
                'saved_data' => [
                    'note_substantif' => $nilaiSubstantif->note_substantif,
                    'skor_per_kriteria' => $savedSkorArray,
                    'skor_per_kriteria_count' => count($savedSkorArray ?? []),
                    'total_nilai' => $nilaiSubstantif->total_nilai,
                    'nilai_akhir' => $nilaiSubstantif->nilai_akhir
                ],
                'verification' => [
                    'skor_saved_correctly' => !empty($savedSkorArray),
                    'skor_count_matches' => count($savedSkorArray ?? []) === count($skorPerKriteria)
                ]
            ]);

        // Cek apakah semua review substantif sudah selesai
        $this->checkReviewCompletion($proposal);

        if ($request->expectsJson() || $request->ajax()) {
        return response()->json([
            'success' => true,
                'message' => 'Review substantif berhasil disimpan',
                'data' => [
                    'id' => $nilaiSubstantif->id,
                    'proposal_id' => $id,
                    'reviewer_id' => $reviewer->id,
                    'saved_catatan' => $catatan,
                    'total_nilai' => $scoreCalculation['total_nilai'],
                    'nilai_akhir' => $scoreCalculation['nilai_akhir']
                ]
            ]);
        }
        
        return redirect()->route('reviewer.detail.proposal.substantif', $id)
            ->with('success', 'Review substantif berhasil disimpan.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in substantif review', [
                'proposal_id' => $id,
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $this->arrayFlatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
            }
            
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            \Log::error('Error in submitReviewSubstantif', [
                'proposal_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
            }
            
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Buka fase perbaikan secara otomatis ketika review selesai
     */
    private function openRevisionPhase()
    {
        try {
            // Ambil ruang kontrol aktif untuk tahun akademik terbaru
            $tahunAjaranTerbaru = TahunAjaranHelper::getTahunAjaranTerbaru();
            $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                ->where('is_active', true)
                ->first();
            
            // Fallback: jika tidak ada yang aktif, ambil yang pertama untuk tahun ajaran terbaru
            if (!$ruangKontrol) {
                $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
            
            if ($ruangKontrol && $ruangKontrol->status_perbaikan !== 'terbuka') {
                $ruangKontrol->update([
                    'status_perbaikan' => 'terbuka',
                    'tanggal_perbaikan_mulai' => now(),
                    'tanggal_perbaikan_selesai' => now()->addDays(7) // Berikan waktu 7 hari untuk revisi
                ]);
                
                \Log::info('Revision phase opened automatically', [
                    'ruang_kontrol_id' => $ruangKontrol->id_ruang_kontrol,
                    'tanggal_perbaikan_mulai' => $ruangKontrol->tanggal_perbaikan_mulai,
                    'tanggal_perbaikan_selesai' => $ruangKontrol->tanggal_perbaikan_selesai
                ]);
                
                // Kirim notifikasi ke semua mahasiswa yang proposalnya perlu direvisi
                $this->notifyRevisionPhaseOpened();
            }
        } catch (\Exception $e) {
            \Log::error('Error opening revision phase', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Kirim notifikasi ke mahasiswa bahwa fase revisi telah dibuka
     */
    private function notifyRevisionPhaseOpened()
    {
        try {
            $notificationService = app(\App\Services\NotificationService::class);
            
            // Ambil semua proposal yang berstatus revisi
            $proposalsForRevision = \App\Models\Proposal::where('status', 'revisi')->get();
            
            foreach ($proposalsForRevision as $proposal) {
                $notificationService->notifyMahasiswa(
                    $proposal,
                    'revision_opened',
                    'Fase Revisi Proposal Dibuka',
                    "Proposal '{$proposal->judul_proposal}' telah selesai direview dan siap untuk direvisi. Silakan lakukan revisi sesuai catatan reviewer.",
                    [
                        'action_url' => route('mahasiswa.revisi.index'),
                        'deadline' => $this->getActiveRuangKontrol()?->tanggal_perbaikan_selesai ?? null
                    ]
                );
                
                // Juga kirim notifikasi ke dosen pendamping
                $notificationService->notifyDosen(
                    $proposal,
                    'revision_opened',
                    'Fase Revisi Proposal Dibuka',
                    "Proposal '{$proposal->judul_proposal}' telah selesai direview dan mahasiswa dapat melakukan revisi.",
                    [
                        'action_url' => route('dosen.pembimbing.dashboard'),
                        'deadline' => $this->getActiveRuangKontrol()?->tanggal_perbaikan_selesai ?? null
                    ]
                );
            }
            
            \Log::info('Revision phase notifications sent', [
                'proposals_count' => $proposalsForRevision->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error sending revision phase notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Kirim notifikasi khusus untuk proposal yang siap direvisi
     */
    private function notifyProposalReadyForRevision($proposal)
    {
        try {
            $notificationService = app(\App\Services\NotificationService::class);
            // Ambil ruang kontrol aktif untuk tahun akademik terbaru
            $ruangKontrol = $this->getActiveRuangKontrol();
            
            // Notifikasi ke mahasiswa
            $notificationService->notifyMahasiswa(
                $proposal,
                'proposal_ready_revision',
                'Proposal Siap Direvisi',
                "Proposal '{$proposal->judul_proposal}' telah selesai direview oleh semua reviewer. Silakan lakukan revisi sesuai catatan reviewer yang diberikan.",
                [
                    'action_url' => route('mahasiswa.revisi.index'),
                    'deadline' => $ruangKontrol->tanggal_perbaikan_selesai ?? null,
                    'proposal_id' => $proposal->id_proposal
                ]
            );
            
            // Notifikasi ke dosen pendamping
            $notificationService->notifyDosen(
                $proposal,
                'proposal_ready_revision',
                'Proposal Siap Direvisi',
                "Proposal '{$proposal->judul_proposal}' telah selesai direview dan mahasiswa dapat melakukan revisi.",
                [
                    'action_url' => route('dosen.pembimbing.dashboard'),
                    'deadline' => $ruangKontrol->tanggal_perbaikan_selesai ?? null,
                    'proposal_id' => $proposal->id_proposal
                ]
            );
            
            \Log::info('Proposal ready for revision notification sent', [
                'proposal_id' => $proposal->id_proposal
            ]);
        } catch (\Exception $e) {
            \Log::error('Error sending proposal ready for revision notification', [
                'proposal_id' => $proposal->id_proposal,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function checkReviewCompletion($proposal)
    {
        try {
            \Log::info('Checking review completion for proposal', [
                'proposal_id' => $proposal->id_proposal,
                'current_status' => $proposal->status
            ]);
            
            // Cek apakah review administratif sudah selesai
        $adminReviewer = $proposal->id_reviewer_administratif;
        $substantifReviewer1 = $proposal->id_reviewer_substantif_1;
        $substantifReviewer2 = $proposal->id_reviewer_substantif_2;
        
            if ($adminReviewer) {
                // Cek review administratif
                $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', $adminReviewer)->first();
                $adminCompleted = $adminReview && $adminReview->note_administratif && 
                                $adminReview->note_administratif !== 'Review administratif dimulai' &&
                                !empty($adminReview->checklist);
                
                \Log::info('Administrative review status check', [
                    'proposal_id' => $proposal->id_proposal,
                    'admin_completed' => $adminCompleted,
                    'admin_note' => $adminReview ? $adminReview->note_administratif : 'No review',
                    'admin_checklist' => $adminReview ? $adminReview->checklist : 'No checklist'
                ]);
                
                // Jika review administratif selesai dan status masih review_administratif, update ke review_substantif
                if ($adminCompleted && $proposal->status === 'review_administratif') {
                    $oldStatus = $proposal->status;
                    $proposal->update(['status' => 'review_substantif']);
                    
                    \Log::info('Proposal status updated to review_substantif', [
                        'proposal_id' => $proposal->id_proposal,
                        'old_status' => $oldStatus,
                        'new_status' => 'review_substantif',
                        'reason' => 'Administrative review completed'
                    ]);
                }
            }
            
            // Cek apakah semua review sudah selesai (untuk status review_completed)
        if ($adminReviewer && $substantifReviewer1 && $substantifReviewer2) {
                // Cek review substantif
            $substantifReview1 = $proposal->nilaiSubstantif->where('id_reviewer', $substantifReviewer1)->first();
            $substantifReview2 = $proposal->nilaiSubstantif->where('id_reviewer', $substantifReviewer2)->first();
                
                // Cek apakah review substantif selesai dengan mengecek semua field yang diperlukan
                $substantif1Completed = $substantifReview1 && 
                                      !empty($substantifReview1->note_substantif) && 
                                      $substantifReview1->note_substantif !== 'Review substantif dimulai' &&
                                      !empty($substantifReview1->skor_per_kriteria) &&
                                      !is_null($substantifReview1->total_nilai) &&
                                      !is_null($substantifReview1->nilai_akhir);
                
                $substantif2Completed = $substantifReview2 && 
                                      !empty($substantifReview2->note_substantif) && 
                                      $substantifReview2->note_substantif !== 'Review substantif dimulai' &&
                                      !empty($substantifReview2->skor_per_kriteria) &&
                                      !is_null($substantifReview2->total_nilai) &&
                                      !is_null($substantifReview2->nilai_akhir);
                
                \Log::info('Substantif review completion status', [
                    'proposal_id' => $proposal->id_proposal,
                    'substantif1_completed' => $substantif1Completed,
                    'substantif2_completed' => $substantif2Completed,
                    'substantif1_has_note' => $substantifReview1 ? !empty($substantifReview1->note_substantif) : false,
                    'substantif1_has_skor' => $substantifReview1 ? !empty($substantifReview1->skor_per_kriteria) : false,
                    'substantif1_has_total' => $substantifReview1 ? !is_null($substantifReview1->total_nilai) : false,
                    'substantif1_has_akhir' => $substantifReview1 ? !is_null($substantifReview1->nilai_akhir) : false,
                    'substantif2_has_note' => $substantifReview2 ? !empty($substantifReview2->note_substantif) : false,
                    'substantif2_has_skor' => $substantifReview2 ? !empty($substantifReview2->skor_per_kriteria) : false,
                    'substantif2_has_total' => $substantifReview2 ? !is_null($substantifReview2->total_nilai) : false,
                    'substantif2_has_akhir' => $substantifReview2 ? !is_null($substantifReview2->nilai_akhir) : false
                ]);
                
                // Jika semua review selesai, update ke revisi
                if ($adminCompleted && $substantif1Completed && $substantif2Completed) {
                    $oldStatus = $proposal->status;
                    $proposal->update(['status' => 'revisi']);
                    
                    // Buka fase perbaikan secara otomatis
                    $this->openRevisionPhase();
                    
                    // Kirim notifikasi review substantif selesai
                    try {
                        $notificationService = app(NotificationService::class);
                        $nilaiReviewer = [
                            'reviewer1' => $substantifReview1->nilai_akhir ?? null,
                            'reviewer2' => $substantifReview2->nilai_akhir ?? null
                        ];
                        $notificationService->notifyReviewSubstantifSelesai(
                            $proposal->fresh(),
                            $nilaiReviewer
                        );
                    } catch (\Exception $e) {
                        \Log::error('Gagal mengirim notifikasi review substantif: ' . $e->getMessage());
                    }
                    
                    \Log::info('Proposal status updated to revisi', [
                        'proposal_id' => $proposal->id_proposal,
                        'old_status' => $oldStatus,
                        'new_status' => 'revisi',
                        'reason' => 'All reviews completed - proposal ready for revision'
                    ]);
                }
            } else {
                \Log::info('Not all reviewers assigned yet', [
                    'proposal_id' => $proposal->id_proposal,
                    'admin_reviewer' => $adminReviewer,
                    'substantif_reviewer_1' => $substantifReviewer1,
                    'substantif_reviewer_2' => $substantifReviewer2
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('Error in checkReviewCompletion', [
                'proposal_id' => $proposal->id_proposal,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function isAdminReviewCompleted($proposal)
    {
        try {
            // Cek apakah ada review administratif yang sudah selesai
            $adminReview = $proposal->nilaiAdministratif()
                ->where('id_reviewer', $proposal->id_reviewer_administratif)
                ->first();

            if (!$adminReview) {
                return false;
            }

            // Cek apakah review administratif memiliki catatan yang valid
            return !empty($adminReview->note_administratif) && 
                   $adminReview->note_administratif !== 'Review dimulai' &&
                   !empty($adminReview->checklist);

        } catch (\Exception $e) {
            \Log::error('Error checking admin review completion', [
                'proposal_id' => $proposal->id_proposal,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Update status proposal berdasarkan completion review
     */
    private function updateProposalStatus($proposal)
    {
        try {
            \Log::info('Updating proposal status', [
                'proposal_id' => $proposal->id_proposal,
                'current_status' => $proposal->status
            ]);

            // Cek review administratif
            $adminCompleted = $this->isAdminReviewCompleted($proposal);
            
            if ($adminCompleted && $proposal->status === 'review_administratif') {
                // Update ke review_substantif
                $oldStatus = $proposal->status;
                $proposal->update(['status' => 'review_substantif']);
                
                \Log::info('Proposal status updated to review_substantif', [
                    'proposal_id' => $proposal->id_proposal,
                    'old_status' => $oldStatus,
                    'new_status' => 'review_substantif',
                    'reason' => 'Administrative review completed'
                ]);
                
                return 'review_substantif';
            }
            
            // Cek apakah semua review substantif selesai
            if ($proposal->status === 'review_substantif') {
                $substantifReviewer1 = $proposal->id_reviewer_substantif_1;
                $substantifReviewer2 = $proposal->id_reviewer_substantif_2;
                
                if ($substantifReviewer1 && $substantifReviewer2) {
                    $substantifReview1 = $proposal->nilaiSubstantif->where('id_reviewer', $substantifReviewer1)->first();
                    $substantifReview2 = $proposal->nilaiSubstantif->where('id_reviewer', $substantifReviewer2)->first();
                    
                    $substantif1Completed = $substantifReview1 && $substantifReview1->note_substantif && 
                                          $substantifReview1->note_substantif !== 'Review substantif dimulai';
                    $substantif2Completed = $substantifReview2 && $substantifReview2->note_substantif && 
                                          $substantifReview2->note_substantif !== 'Review substantif dimulai';
                    
                    if ($substantif1Completed && $substantif2Completed) {
                        $oldStatus = $proposal->status;
                        $proposal->update(['status' => 'revisi']);
                        
                        // Buka fase perbaikan secara otomatis
                        $this->openRevisionPhase();
                        
                        // Kirim notifikasi khusus untuk proposal ini
                        $this->notifyProposalReadyForRevision($proposal);
                        
                        \Log::info('Proposal status updated to revisi', [
                            'proposal_id' => $proposal->id_proposal,
                            'old_status' => $oldStatus,
                            'new_status' => 'revisi',
                            'reason' => 'All substantif reviews completed - proposal ready for revision'
                        ]);
                        
                        return 'revisi';
                    }
                }
            }
            
            return $proposal->status;
            
        } catch (\Exception $e) {
            \Log::error('Error updating proposal status', [
                'proposal_id' => $proposal->id_proposal,
                'error' => $e->getMessage()
            ]);
            return $proposal->status;
        }
    }

    /**
     * Halaman review substantif seleksi: proposal dimana reviewer ini adalah
     * id_reviewer_substantif_seleksi_1 atau id_reviewer_substantif_seleksi_2.
     */
    public function reviewSubstantifSeleksi(Request $request)
    {
        $reviewer = Auth::user();
        $tahunAjaranTerpilih = request('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        $tahunAjaranList = TahunAjaranHelper::getListTahunAjaran();

        $proposals = Proposal::with(['mahasiswa', 'dosen', 'nilaiSubstantif'])
            ->where(function ($query) use ($reviewer) {
                $query->where('id_reviewer_substantif_seleksi_1', $reviewer->id)
                    ->orWhere('id_reviewer_substantif_seleksi_2', $reviewer->id);
            })
            ->where('tahun_ajaran', $tahunAjaranTerpilih)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reviewer.review_substantif_seleksi', compact('proposals', 'tahunAjaranTerpilih', 'tahunAjaranList'));
    }

    /**
     * Submit review substantif seleksi. Membuat/update NilaiSubstantif dengan jenis_review = 'seleksi'.
     * Jika kedua review seleksi selesai, status proposal diupdate ke hasil_semi_final.
     */
    public function submitReviewSubstantifSeleksi(Request $request, $id)
    {
        \Log::info('Submit review substantif seleksi', [
            'proposal_id' => $id,
            'user_id' => Auth::id(),
            'request_keys' => array_keys($request->all())
        ]);

        try {
            $criteria = ProposalHelper::getSubstantifCriteria($request->skim ?? 'default');
            $validationRules = [
                'catatan' => 'required|string|min:50|max:1000',
                'skor' => 'required|array',
                'skor.*' => 'required|numeric|min:0|max:10'
            ];
            $request->validate($validationRules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in substantif seleksi review', [
                'proposal_id' => $id,
                'errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $this->arrayFlatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
        }

        $reviewer = Auth::user();
        $proposal = Proposal::findOrFail($id);

        if ($proposal->status_validasi !== 'valid') {
            return response()->json([
                'success' => false,
                'message' => 'Proposal belum divalidasi dan tidak dapat direview'
            ], 403);
        }

        $isSeleksiReviewer = in_array($reviewer->id, [
            $proposal->id_reviewer_substantif_seleksi_1,
            $proposal->id_reviewer_substantif_seleksi_2
        ]);
        if (!$isSeleksiReviewer) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak ditugaskan untuk review substantif seleksi proposal ini'
            ], 403);
        }

        if (!in_array($proposal->status, ['review_substantif_seleksi', 'revisi'])) {
            return response()->json([
                'success' => false,
                'message' => 'Proposal belum siap untuk review substantif seleksi. Status: ' . $proposal->status
            ], 403);
        }

        if (empty($criteria)) {
            $criteria = ProposalHelper::getSubstantifCriteria($proposal->skim);
        }

        $catatan = $request->input('catatan');
        $skorPerKriteriaRaw = $request->input('skor', []);
        if (!is_array($skorPerKriteriaRaw)) {
            return response()->json([
                'success' => false,
                'message' => 'Data skor tidak valid.'
            ], 422);
        }

        $skorPerKriteria = [];
        foreach ($skorPerKriteriaRaw as $key => $value) {
            if ($value === null || $value === '' || $value === false) {
                continue;
            }
            $index = (int) $key;
            $skorValue = (float) $value;
            if ($skorValue < 0) {
                $skorValue = 0;
            } elseif ($skorValue > 10) {
                $skorValue = 10;
            }
            $skorPerKriteria[$index] = $skorValue;
        }
        ksort($skorPerKriteria);

        $expectedCount = ProposalHelper::countActualCriteria($criteria);
        if (count($skorPerKriteria) !== $expectedCount) {
            return response()->json([
                'success' => false,
                'message' => "Jumlah skor tidak sesuai. Diharapkan: {$expectedCount}, Diterima: " . count($skorPerKriteria)
            ], 422);
        }

        $scoreCalculation = ProposalHelper::calculateSubstantifScore($criteria, $skorPerKriteria);
        $dataToSave = [
            'note_substantif' => $catatan,
            'skor_per_kriteria' => $skorPerKriteria,
            'total_nilai' => $scoreCalculation['total_nilai'] ?? null,
            'nilai_akhir' => $scoreCalculation['nilai_akhir'] ?? null,
            'jenis_review' => 'seleksi',
            'updated_at' => now()
        ];

        try {
            DB::beginTransaction();

            $nilaiSubstantif = NilaiSubstantif::updateOrCreate(
                [
                    'id_proposal' => $id,
                    'id_reviewer' => $reviewer->id,
                    'jenis_review' => 'seleksi'
                ],
                $dataToSave
            );

            $proposal->refresh();
            $sid1 = $proposal->id_reviewer_substantif_seleksi_1;
            $sid2 = $proposal->id_reviewer_substantif_seleksi_2;

            $reviewsSeleksi = NilaiSubstantif::where('id_proposal', $id)
                ->where('jenis_review', 'seleksi')
                ->whereIn('id_reviewer', array_filter([$sid1, $sid2]))
                ->get();

            $r1 = $reviewsSeleksi->where('id_reviewer', $sid1)->first();
            $r2 = $reviewsSeleksi->where('id_reviewer', $sid2)->first();

            $completed1 = $r1 && !empty($r1->note_substantif) && !empty($r1->skor_per_kriteria) &&
                !is_null($r1->total_nilai) && !is_null($r1->nilai_akhir);
            $completed2 = $r2 && !empty($r2->note_substantif) && !empty($r2->skor_per_kriteria) &&
                !is_null($r2->total_nilai) && !is_null($r2->nilai_akhir);

            if ($sid1 && $sid2 && $completed1 && $completed2) {
                $proposal->update(['status' => 'hasil_semi_final']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review substantif seleksi berhasil disimpan',
                'data' => [
                    'id' => $nilaiSubstantif->id,
                    'proposal_id' => (int) $id,
                    'reviewer_id' => $reviewer->id,
                    'total_nilai' => $scoreCalculation['total_nilai'] ?? null,
                    'nilai_akhir' => $scoreCalculation['nilai_akhir'] ?? null
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving review substantif seleksi', [
                'proposal_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data penilaian: ' . $e->getMessage()
            ], 500);
        }
    }
}

