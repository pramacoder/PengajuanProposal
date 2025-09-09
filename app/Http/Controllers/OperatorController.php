<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Proposal;
use App\Models\Dosen;
use App\Models\Reviewer;
use App\Models\RuangKontrol;
use App\Models\HasilFinal;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use App\Models\Dokumen;
use App\Models\PT;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\ProposalRevisi;
use Illuminate\Support\Facades\Storage;

class OperatorController extends Controller
{
    public function dashboard()
    {
        // Ambil data untuk dashboard
        $tahun = request('tahun', '2025');
        
        // Data untuk PKM-8 Bidang
        $pkm8Bidang = $this->getPKM8BidangData($tahun);
        
        // Data untuk PKM Insentif
        $pkmInsentif = $this->getPKMInsentifData($tahun);
        
        // Total keseluruhan
        $totalKeseluruhan = $pkm8Bidang->sum('jumlah');
        $totalInsentif = $pkmInsentif->sum('jumlah');
        
        return view('operator.dashboard', compact('pkm8Bidang', 'pkmInsentif', 'totalKeseluruhan', 'totalInsentif', 'tahun'));
    }

    public function pilihReviewer()
    {
        $tahun = request('tahun', '2025');
        $filter = request('filter', 'all');
        
        // Ambil proposal yang sudah divalidasi dosen dan BELUM memiliki reviewer
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif'])
            ->where('status_validasi', 'valid')
            ->where(function($query) {
                $query->whereNull('id_reviewer_administratif')
                      ->orWhereNull('id_reviewer_substantif_1')
                      ->orWhereNull('id_reviewer_substantif_2');
            })
            ->whereYear('tanggal_pengajuan', $tahun)
            ->when($filter !== 'all', function($query) use ($filter) {
                $query->where('skim', $filter);
            })
            ->get();
        
        // Debug: Log jumlah proposal yang ditemukan
        \Log::info('Proposals found for pilih reviewer:', [
            'count' => $proposals->count(),
            'tahun' => $tahun,
            'filter' => $filter
        ]);
        
        // Ambil semua reviewer yang tersedia
        $reviewers = Reviewer::where('is_active', true)->get();
        
        return view('operator.pilih_reviewer', compact('proposals', 'reviewers', 'tahun', 'filter'));
    }

    public function searchReviewers(Request $request)
    {
        $search = $request->get('search', '');
        
        if (strlen($search) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal 2 karakter untuk pencarian'
            ]);
        }
        
        try {
            $reviewers = Reviewer::where('is_active', true)
                ->where(function($query) use ($search) {
                    $query->where('nama_reviewer', 'LIKE', "%{$search}%")
                          ->orWhere('email_reviewer', 'LIKE', "%{$search}%");
                })
                ->select('id_reviewer', 'nama_reviewer', 'email_reviewer', 'no_hp_reviewer')
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'reviewers' => $reviewers
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari reviewer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignReviewer(Request $request)
    {
        \Log::info('Assigning reviewers to proposal', [
            'proposal_id' => $request->proposal_id,
            'reviewer_administratif' => $request->reviewer_administratif,
            'reviewer_substantif_1' => $request->reviewer_substantif_1,
            'reviewer_substantif_2' => $request->reviewer_substantif_2,
            'request_data' => $request->all()
        ]);

        try {
            $request->validate([
                'proposal_id' => 'required|exists:proposals,id_proposal',
                'reviewer_administratif' => 'required|exists:reviewers,id_reviewer',
                'reviewer_substantif_1' => 'required|exists:reviewers,id_reviewer',
                'reviewer_substantif_2' => 'required|exists:reviewers,id_reviewer|different:reviewer_substantif_1'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed for reviewer assignment', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', array_flatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            // Update proposal status dan assignment reviewer
            $proposal = Proposal::findOrFail($request->proposal_id);
            
            \Log::info('Updating proposal', [
                'proposal_id' => $proposal->id_proposal,
                'current_status' => $proposal->status,
                'current_status_validasi' => $proposal->status_validasi,
                'new_status' => 'review_administratif'
            ]);
            
            // Update proposal dengan reviewer assignment
            $updateData = [
                'status' => 'review_administratif',
                'id_reviewer_administratif' => $request->reviewer_administratif,
                'id_reviewer_substantif_1' => $request->reviewer_substantif_1,
                'id_reviewer_substantif_2' => $request->reviewer_substantif_2
            ];
            
            $proposal->update($updateData);
            
            \Log::info('Proposal updated successfully', [
                'proposal_id' => $proposal->id_proposal,
                'new_status' => $proposal->status,
                'reviewers_assigned' => [
                    'administratif' => $proposal->id_reviewer_administratif,
                    'substantif_1' => $proposal->id_reviewer_substantif_1,
                    'substantif_2' => $proposal->id_reviewer_substantif_2
                ]
            ]);
            
            // Hapus assignment lama jika ada
            NilaiAdministratif::where('id_proposal', $request->proposal_id)->delete();
            NilaiSubstantif::where('id_proposal', $request->proposal_id)->delete();
            
            \Log::info('Old review records deleted', [
                'proposal_id' => $request->proposal_id
            ]);
            
            // Buat assignment baru di tabel nilai
            $nilaiAdmin = NilaiAdministratif::create([
                'id_proposal' => $request->proposal_id,
                'id_reviewer' => $request->reviewer_administratif,
                'note_administratif' => 'Review administratif dimulai',
                'checklist' => json_encode([])
            ]);
            
            $nilaiSub1 = NilaiSubstantif::create([
                'id_proposal' => $request->proposal_id,
                'id_reviewer' => $request->reviewer_substantif_1,
                'note_substantif' => 'Review substantif dimulai'
            ]);
            
            $nilaiSub2 = NilaiSubstantif::create([
                'id_proposal' => $request->proposal_id,
                'id_reviewer' => $request->reviewer_substantif_2,
                'note_substantif' => 'Review substantif dimulai'
            ]);
            
            \Log::info('Created review records', [
                'nilai_admin_id' => $nilaiAdmin->id,
                'nilai_sub1_id' => $nilaiSub1->id,
                'nilai_sub2_id' => $nilaiSub2->id
            ]);
            
            DB::commit();
            
            \Log::info('Reviewer assignment completed successfully', [
                'proposal_id' => $request->proposal_id,
                'reviewers' => [
                    'administratif' => $request->reviewer_administratif,
                    'substantif_1' => $request->reviewer_substantif_1,
                    'substantif_2' => $request->reviewer_substantif_2
                ]
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Reviewer berhasil ditugaskan',
                'data' => [
                    'proposal_id' => $request->proposal_id,
                    'status' => 'review_administratif',
                    'reviewers' => [
                        'administratif' => $request->reviewer_administratif,
                        'substantif_1' => $request->reviewer_substantif_1,
                        'substantif_2' => $request->reviewer_substantif_2
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error assigning reviewers', [
                'proposal_id' => $request->proposal_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function getAssignedProposals()
    {
        $tahun = request('tahun', '2025');
        $filter = request('filter', 'all');
        
        \Log::info('Getting assigned proposals', [
            'tahun' => $tahun,
            'filter' => $filter
        ]);
        
        try {
            // Ambil proposal yang sudah ditugaskan reviewer (semua field reviewer harus terisi)
            $proposals = Proposal::with([
                'mahasiswa', 
                'dokumen', 
                'reviewerAdministratif',
                'reviewerSubstantif1',
                'reviewerSubstantif2'
            ])
            ->where('status_validasi', 'valid')
            ->whereNotNull('id_reviewer_administratif')
            ->whereNotNull('id_reviewer_substantif_1')
            ->whereNotNull('id_reviewer_substantif_2')
            ->whereYear('tanggal_pengajuan', $tahun)
            ->when($filter !== 'all', function($query) use ($filter) {
                $query->where('skim', $filter);
            })
            ->orderBy('updated_at', 'desc')
            ->get();
            
            \Log::info('Found assigned proposals', [
                'count' => $proposals->count(),
                'proposals' => $proposals->map(function($p) {
                    return [
                        'id' => $p->id_proposal,
                        'status' => $p->status,
                        'skim' => $p->skim,
                        'has_reviewers' => [
                            'administratif' => !is_null($p->id_reviewer_administratif),
                            'substantif_1' => !is_null($p->id_reviewer_substantif_1),
                            'substantif_2' => !is_null($p->id_reviewer_substantif_2)
                        ]
                    ];
                })
            ]);
            
            return response()->json([
                'success' => true,
                'proposals' => $proposals,
                'count' => $proposals->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting assigned proposals', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ruangKontrol()
    {
        $ruangKontrol = RuangKontrol::first();
        
        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::create([
                'status_pendaftaran' => 'tertutup',
                'status_perbaikan' => 'tertutup',
                'id_pt' => auth('operator')->id()
            ]);
        }
        
        return view('operator.ruang_kontrol', compact('ruangKontrol'));
    }

    public function updateRuangKontrol(Request $request)
    {
        // Log request untuk debugging
        \Log::info('Update Ruang Kontrol Request', [
            'data' => $request->all(),
            'user_id' => auth('operator')->id()
        ]);

        $request->validate([
            'status_pendaftaran' => 'required|in:terbuka,tertutup',
            'status_perbaikan' => 'required|in:terbuka,tertutup',
            'tanggal_pendaftaran_mulai' => 'required|date',
            'tanggal_pendaftaran_selesai' => 'required|date|after:tanggal_pendaftaran_mulai',
            'tanggal_perbaikan_mulai' => 'required|date',
            'tanggal_perbaikan_selesai' => 'required|date|after:tanggal_perbaikan_mulai'
        ]);

        try {
            $ruangKontrol = RuangKontrol::first();
            
            if (!$ruangKontrol) {
                // Buat record baru jika belum ada
                $ruangKontrol = RuangKontrol::create([
                    'status_pendaftaran' => $request->status_pendaftaran,
                    'status_perbaikan' => $request->status_perbaikan,
                    'tanggal_pendaftaran_mulai' => $request->tanggal_pendaftaran_mulai,
                    'tanggal_pendaftaran_selesai' => $request->tanggal_pendaftaran_selesai,
                    'tanggal_perbaikan_mulai' => $request->tanggal_perbaikan_mulai,
                    'tanggal_perbaikan_selesai' => $request->tanggal_perbaikan_selesai,
                    'id_pt' => auth('operator')->id()
                ]);
            } else {
                // Update record yang sudah ada
                $ruangKontrol->update([
                    'status_pendaftaran' => $request->status_pendaftaran,
                    'status_perbaikan' => $request->status_perbaikan,
                    'tanggal_pendaftaran_mulai' => $request->tanggal_pendaftaran_mulai,
                    'tanggal_pendaftaran_selesai' => $request->tanggal_pendaftaran_selesai,
                    'tanggal_perbaikan_mulai' => $request->tanggal_perbaikan_mulai,
                    'tanggal_perbaikan_selesai' => $request->tanggal_perbaikan_selesai
                ]);
            }
            
            // Log success
            \Log::info('Ruang Kontrol Updated Successfully', [
                'ruang_kontrol_id' => $ruangKontrol->id_ruang_kontrol,
                'status_pendaftaran' => $ruangKontrol->status_pendaftaran,
                'status_perbaikan' => $ruangKontrol->status_perbaikan
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan ruang kontrol berhasil diperbarui'
            ]);
            
        } catch (\Exception $e) {
            // Log error
            \Log::error('Error updating Ruang Kontrol', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function hasilFinal()
    {
        $tahun = request('tahun', '2025');
        $filter = request('filter', 'all');
        $statusRevisi = request('status_revisi', 'all');
        
        // Ambil proposal yang sudah selesai review (status minimal "revisi")
        // Hanya proposal yang sudah masuk tahap revisi atau final yang ditampilkan
        // Status yang diizinkan: revisi, revisi_submitted, lolos, tidak_lolos
        // EXCLUDE: submitted, review_administratif, review_substantif, review_completed, draft, pending, tidak_valid
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif', 'hasilFinal', 'proposalRevisi'])
            ->whereIn('status', ['revisi', 'revisi_submitted', 'lolos', 'tidak_lolos'])
            ->whereYear('tanggal_pengajuan', $tahun)
            ->when($filter !== 'all', function($query) use ($filter) {
                $query->where('skim', $filter);
            })
            ->when($statusRevisi !== 'all', function($query) use ($statusRevisi) {
                switch($statusRevisi) {
                    case 'belum':
                        $query->whereDoesntHave('proposalRevisi');
                        break;
                    case 'sudah':
                        $query->whereHas('proposalRevisi');
                        break;
                    case 'tidak_revisi':
                        // Proposal dengan status "revisi" tapi tidak memiliki file revisi
                        $query->where('status', 'revisi')->whereDoesntHave('proposalRevisi');
                        break;
                    case 'menunggu_review':
                        $query->where('status', 'revisi_submitted');
                        break;
                    case 'selesai':
                        $query->whereIn('status', ['lolos', 'tidak_lolos']);
                        break;
                }
            })
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();
        
        // Debug: Log proposal yang ditampilkan untuk memastikan status yang benar
        \Log::info('Hasil Final - Proposals loaded', [
            'count' => $proposals->count(),
            'statuses' => $proposals->pluck('status')->unique()->toArray(),
            'tahun' => $tahun,
            'filter' => $filter,
            'status_revisi' => $statusRevisi
        ]);
        
        return view('operator.hasil_final', compact('proposals', 'tahun', 'filter', 'statusRevisi'));
    }

    public function proposalDetail($id)
    {
        $proposal = Proposal::with([
            'mahasiswa', 
            'dosen', 
            'teams', 
            'dokumen', 
            'nilaiAdministratif.reviewer', 
            'nilaiSubstantif.reviewer', 
            'hasilFinal',
            'proposalRevisi' => function($query) {
                $query->orderBy('tanggal_submit', 'desc');
            }
        ])->findOrFail($id);

        return view('operator.proposal_detail', compact('proposal'));
    }

    public function updateHasilFinal(Request $request)
    {
        \Log::info('UpdateHasilFinal called', [
            'request_data' => $request->all(),
            'user_id' => auth('operator')->id()
        ]);

        $request->validate([
            'proposal_id' => 'required|exists:proposals,id_proposal',
            'status_final' => 'required|in:lolos,tidak_lolos',
            'catatan_final' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            
            $proposal = Proposal::findOrFail($request->proposal_id);
            
            // Update status proposal
            $proposal->update(['status' => $request->status_final]);
            
            // Update atau buat hasil final
            HasilFinal::updateOrCreate(
                ['id_proposal' => $request->proposal_id],
                [
                    'status_final' => $request->status_final,
                    'catatan_final' => $request->catatan_final,
                    'id_pt' => auth('operator')->id()
                ]
            );
            
            DB::commit();
            
            \Log::info('Hasil final updated successfully', [
                'proposal_id' => $request->proposal_id,
                'status_final' => $request->status_final
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Hasil final berhasil diperbarui'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Error updating hasil final', [
                'error' => $e->getMessage(),
                'proposal_id' => $request->proposal_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadRevisi($id)
    {
        try {
            $revisi = ProposalRevisi::findOrFail($id);
            
            if (!Storage::disk('public')->exists($revisi->path_file)) {
                return redirect()->back()->with('error', 'File revisi tidak ditemukan di server.');
            }

            return Storage::disk('public')->download($revisi->path_file, $revisi->nama_file);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh file revisi: ' . $e->getMessage());
        }
    }

    public function detailHasilFinal($id)
    {
        $proposal = Proposal::with([
            'mahasiswa', 
            'dosen', 
            'teams', 
            'dokumen', 
            'nilaiAdministratif.reviewer', 
            'nilaiSubstantif.reviewer', 
            'hasilFinal',
            'proposalRevisi' => function($query) {
                $query->orderBy('tanggal_submit', 'desc');
            }
        ])->findOrFail($id);

        return view('operator.detail_hasil_final', compact('proposal'));
    }

    private function getPKM8BidangData($tahun)
    {
        $skims = ['RE', 'RSH', 'KC', 'PM', 'PI', 'K', 'KI', 'VGK'];
        
        return collect($skims)->map(function($skim) use ($tahun) {
            // Total proposal berdasarkan skim
            $total = Proposal::where('skim', $skim)
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang sudah divalidasi (status_validasi = 'valid')
            $sudahValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'valid')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang belum divalidasi (status_validasi = 'pending')
            $belumValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'pending')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang ditolak validasi (status_validasi = 'tidak_valid')
            $tolakValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'tidak_valid')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang sedang dalam proses review
            $sedangReview = Proposal::where('skim', $skim)
                ->whereIn('status', ['submitted', 'review_administratif', 'review_substantif', 'revisi'])
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang sudah selesai review (lolos/tidak_lolos)
            $selesaiReview = Proposal::where('skim', $skim)
                ->whereIn('status', ['lolos', 'tidak_lolos'])
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            return [
                'skim' => $skim,
                'jumlah' => $total,
                'sudah_valid' => $sudahValid,
                'belum_valid' => $belumValid,
                'tolak_valid' => $tolakValid,
                'sedang_review' => $sedangReview,
                'selesai_review' => $selesaiReview
            ];
        });
    }

    private function getPKMInsentifData($tahun)
    {
        $skims = ['AI', 'GFT'];
        
        return collect($skims)->map(function($skim) use ($tahun) {
            // Total proposal berdasarkan skim
            $total = Proposal::where('skim', $skim)
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang sudah divalidasi (status_validasi = 'valid')
            $sudahValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'valid')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang belum divalidasi (status_validasi = 'pending')
            $belumValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'pending')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang ditolak validasi (status_validasi = 'tidak_valid')
            $tolakValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'tidak_valid')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang sedang dalam proses review
            $sedangReview = Proposal::where('skim', $skim)
                ->whereIn('status', ['submitted', 'review_administratif', 'review_substantif', 'revisi'])
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang sudah selesai review (lolos/tidak_lolos)
            $selesaiReview = Proposal::where('skim', $skim)
                ->whereIn('status', ['lolos', 'tidak_lolos'])
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            return [
                'skim' => $skim,
                'jumlah' => $total,
                'sudah_valid' => $sudahValid,
                'belum_valid' => $belumValid,
                'tolak_valid' => $tolakValid,
                'sedang_review' => $sedangReview,
                'selesai_review' => $selesaiReview
            ];
        });
    }
}

