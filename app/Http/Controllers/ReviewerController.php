<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use App\Models\Dosen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewerController extends Controller
{
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
        $tahun = request('tahun', date('Y'));
        
        \Log::info('Reviewer accessing dashboard', [
            'reviewer_id' => $reviewer->id_reviewer,
            'tahun' => $tahun
        ]);
        
        // Ambil semua proposal yang ditugaskan ke reviewer ini (administratif atau substantif)
        // Logic sama dengan halaman review: berdasarkan assignment reviewer
        $proposals = Proposal::with(['mahasiswa', 'dosen', 'nilaiAdministratif', 'nilaiSubstantif'])
            ->where(function($query) use ($reviewer) {
                $query->where('id_reviewer_administratif', $reviewer->id_reviewer)
                      ->orWhere('id_reviewer_substantif_1', $reviewer->id_reviewer)
                      ->orWhere('id_reviewer_substantif_2', $reviewer->id_reviewer);
            })
            ->where('status_validasi', 'valid')
            ->whereYear('created_at', $tahun)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        \Log::info('Proposals found for dashboard', [
            'reviewer_id' => $reviewer->id_reviewer,
            'proposal_count' => $proposals->count(),
            'proposals' => $proposals->map(function($p) use ($reviewer) {
                $isAdminReviewer = $p->id_reviewer_administratif == $reviewer->id_reviewer;
                $isSubstantifReviewer = $p->id_reviewer_substantif_1 == $reviewer->id_reviewer || 
                                       $p->id_reviewer_substantif_2 == $reviewer->id_reviewer;
                
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
            if ($proposal->id_reviewer_administratif == $reviewer->id_reviewer) {
                $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', $reviewer->id_reviewer)->first();
                return $adminReview && $adminReview->note_administratif && !empty($adminReview->checklist);
            }
            
            // Cek apakah reviewer ini adalah reviewer substantif
            if ($proposal->id_reviewer_substantif_1 == $reviewer->id_reviewer || 
                $proposal->id_reviewer_substantif_2 == $reviewer->id_reviewer) {
                $substantifReview = $proposal->nilaiSubstantif->where('id_reviewer', $reviewer->id_reviewer)->first();
                return $substantifReview && $substantifReview->note_substantif && 
                       $substantifReview->note_substantif !== 'Review substantif dimulai';
            }
            
            return false;
        })->count();
        $pendingReview = $totalAssigned - $completedReview;
        
        \Log::info('Dashboard statistics calculated', [
            'reviewer_id' => $reviewer->id_reviewer,
            'total_assigned' => $totalAssigned,
            'completed_review' => $completedReview,
            'pending_review' => $pendingReview
        ]);

        return view('reviewer.dashboard', compact('proposals', 'totalAssigned', 'completedReview', 'pendingReview', 'tahun'));
    }

    public function reviewAdministratif()
    {
        $reviewer = Auth::user();
        $tahun = request('tahun', date('Y'));
        
        \Log::info('Reviewer accessing administratif review page', [
            'reviewer_id' => $reviewer->id_reviewer,
            'tahun' => $tahun
        ]);
        
        // Ambil proposal yang ditugaskan untuk review administratif
        // Logic sama dengan dashboard: proposal dimana reviewer ini adalah id_reviewer_administratif
        $proposals = Proposal::with(['mahasiswa', 'dosen', 'nilaiAdministratif'])
            ->where('id_reviewer_administratif', $reviewer->id_reviewer)
            ->where('status_validasi', 'valid')
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'desc')
            ->get();
            
        \Log::info('Proposals found for administratif review', [
            'reviewer_id' => $reviewer->id_reviewer,
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

        return view('reviewer.review_administratif', compact('proposals', 'tahun'));
    }

    public function reviewSubstantif()
    {
        $reviewer = Auth::user();
        $tahun = request('tahun', date('Y'));
        
        \Log::info('Reviewer accessing substantif review page', [
            'reviewer_id' => $reviewer->id_reviewer,
            'tahun' => $tahun
        ]);
        
        // Ambil proposal yang ditugaskan untuk review substantif
        // Logic sama dengan dashboard: proposal dimana reviewer ini adalah id_reviewer_substantif_1 atau id_reviewer_substantif_2
        $proposals = Proposal::with(['mahasiswa', 'dosen', 'nilaiSubstantif'])
            ->where(function($query) use ($reviewer) {
                $query->where('id_reviewer_substantif_1', $reviewer->id_reviewer)
                      ->orWhere('id_reviewer_substantif_2', $reviewer->id_reviewer);
            })
            ->where('status_validasi', 'valid')
            ->whereYear('created_at', $tahun)
            ->orderBy('created_at', 'desc')
            ->get();
            
        \Log::info('Proposals found for substantif review', [
            'reviewer_id' => $reviewer->id_reviewer,
            'proposal_count' => $proposals->count(),
            'proposals' => $proposals->map(function($p) use ($reviewer) {
                $isSubstantif1 = $p->id_reviewer_substantif_1 == $reviewer->id_reviewer;
                $isSubstantif2 = $p->id_reviewer_substantif_2 == $reviewer->id_reviewer;
                
                return [
                    'id' => $p->id_proposal,
                    'status' => $p->status,
                    'judul' => $p->judul_proposal,
                    'is_substantif_reviewer_1' => $isSubstantif1,
                    'is_substantif_reviewer_2' => $isSubstantif2
                ];
            })
        ]);

        return view('reviewer.review_substantif', compact('proposals', 'tahun'));
    }

    public function detailProposalSubstantif($id)
    {
        try {
            $reviewer = Auth::user();
            \Log::info('Reviewer accessing substantif detail proposal', [
                'reviewer_id' => $reviewer->id_reviewer,
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
            $isSubstantif = $proposal->id_reviewer_substantif_1 == $reviewer->id_reviewer || 
                           $proposal->id_reviewer_substantif_2 == $reviewer->id_reviewer;
            
            if (!$isSubstantif) {
                \Log::warning('Reviewer not assigned for substantif review', [
                    'reviewer_id' => $reviewer->id_reviewer,
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
                'reviewer_id' => $reviewer->id_reviewer,
                'admin_review_completed' => $adminReviewCompleted
            ]);

            return view('reviewer.detail_proposal_substantif', compact('proposal', 'adminReviewCompleted'));
            
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
                'reviewer_id' => $reviewer->id_reviewer,
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
            $isAdministratif = $proposal->id_reviewer_administratif == $reviewer->id_reviewer;
            $isSubstantif = $proposal->id_reviewer_substantif_1 == $reviewer->id_reviewer || 
                           $proposal->id_reviewer_substantif_2 == $reviewer->id_reviewer;
            
            \Log::info('Reviewer assignment check', [
                'isAdministratif' => $isAdministratif,
                'isSubstantif' => $isSubstantif,
                'reviewer_id' => $reviewer->id_reviewer
            ]);
            
            if (!$isAdministratif && !$isSubstantif) {
                \Log::warning('Reviewer not assigned to proposal', [
                    'reviewer_id' => $reviewer->id_reviewer,
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
                'reviewer_id' => $reviewer->id_reviewer
            ]);

            // Redirect ke halaman yang sesuai berdasarkan tipe review
            if ($isAdministratif) {
                return view('reviewer.detail_proposal_administratif', compact('proposal'));
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
                'reviewer_id' => $reviewer->id_reviewer,
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
        if ($proposal->id_reviewer_administratif != $reviewer->id_reviewer) {
                \Log::warning('Reviewer not assigned for administratif review', [
                    'reviewer_id' => $reviewer->id_reviewer,
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
                'id_reviewer' => $reviewer->id_reviewer
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
                'reviewer_id' => $reviewer->id_reviewer,
                'note_length' => strlen($catatan),
                'checklist_count' => is_array($kesalahanAdministratif) ? count($kesalahanAdministratif) : 'not_array',
                'saved_data' => [
                    'note_administratif' => $nilaiAdmin->note_administratif,
                    'checklist' => $nilaiAdmin->checklist
                ]
            ]);

        // Cek apakah semua review administratif sudah selesai
            $this->updateProposalStatus($proposal);

        return response()->json([
            'success' => true,
                'message' => 'Review administratif berhasil disimpan',
                'data' => [
                    'id' => $nilaiAdmin->id,
                    'proposal_id' => $id,
                    'reviewer_id' => $reviewer->id_reviewer,
                    'saved_catatan' => $catatan,
                    'saved_kesalahan' => $kesalahanAdministratif,
                    'new_status' => $proposal->fresh()->status
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in administratif review', [
                'proposal_id' => $id,
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $this->arrayFlatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Error in submitReviewAdministratif', [
                'proposal_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
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

            // Validasi input
        $request->validate([
                'catatan' => 'required|string|min:50|max:1000'
        ]);

        $reviewer = Auth::user();
        $proposal = Proposal::findOrFail($id);

            \Log::info('Reviewer and proposal found', [
                'reviewer_id' => $reviewer->id_reviewer,
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
            $isSubstantifReviewer = in_array($reviewer->id_reviewer, [
                $proposal->id_reviewer_substantif_1,
                $proposal->id_reviewer_substantif_2
            ]);

            if (!$isSubstantifReviewer) {
                \Log::warning('Reviewer not assigned for substantif review', [
                    'reviewer_id' => $reviewer->id_reviewer,
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
            
            \Log::info('Data to be saved', [
                'catatan' => $catatan,
                'catatan_length' => strlen($catatan)
            ]);

            // Update atau create nilai substantif
            $nilaiSubstantif = NilaiSubstantif::updateOrCreate(
            [
                'id_proposal' => $id,
                'id_reviewer' => $reviewer->id_reviewer
            ],
            [
                    'note_substantif' => $catatan,
                'updated_at' => now()
            ]
        );

            \Log::info('Nilai substantif saved successfully', [
                'nilai_id' => $nilaiSubstantif->id,
                'proposal_id' => $id,
                'reviewer_id' => $reviewer->id_reviewer,
                'note_length' => strlen($catatan),
                'saved_data' => [
                    'note_substantif' => $nilaiSubstantif->note_substantif
                ]
            ]);

        // Cek apakah semua review substantif sudah selesai
        $this->checkReviewCompletion($proposal);

        return response()->json([
            'success' => true,
                'message' => 'Review substantif berhasil disimpan',
                'data' => [
                    'id' => $nilaiSubstantif->id,
                    'proposal_id' => $id,
                    'reviewer_id' => $reviewer->id_reviewer,
                    'saved_catatan' => $catatan
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in substantif review', [
                'proposal_id' => $id,
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $this->arrayFlatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Error in submitReviewSubstantif', [
                'proposal_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buka fase perbaikan secara otomatis ketika review selesai
     */
    private function openRevisionPhase()
    {
        try {
            $ruangKontrol = \App\Models\RuangKontrol::first();
            
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
            $notificationService = new \App\Services\NotificationService();
            
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
                        'deadline' => \App\Models\RuangKontrol::first()->tanggal_perbaikan_selesai ?? null
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
                        'deadline' => \App\Models\RuangKontrol::first()->tanggal_perbaikan_selesai ?? null
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
            $notificationService = new \App\Services\NotificationService();
            $ruangKontrol = \App\Models\RuangKontrol::first();
            
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
                
                $substantif1Completed = $substantifReview1 && $substantifReview1->note_substantif && 
                                      $substantifReview1->note_substantif !== 'Review substantif dimulai';
                $substantif2Completed = $substantifReview2 && $substantifReview2->note_substantif && 
                                      $substantifReview2->note_substantif !== 'Review substantif dimulai';
                
                \Log::info('Substantif review completion status', [
                    'proposal_id' => $proposal->id_proposal,
                    'substantif1_completed' => $substantif1Completed,
                    'substantif2_completed' => $substantif2Completed
                ]);
                
                // Jika semua review selesai, update ke revisi
                if ($adminCompleted && $substantif1Completed && $substantif2Completed) {
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
}

