<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Proposal;
use App\Models\Dokumen;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use App\Models\HasilFinal;
use App\Models\HasilSemiFinal;
use App\Models\User;
use App\Models\ProposalRevisi;
use App\Services\NotificationService;
use App\Helpers\StorageHelper;
use Illuminate\Support\Facades\DB;

class DosenController extends Controller
{
    // Method test untuk memastikan authentication berfungsi
    public function test()
    {
        if (auth()->check()) {
            $dosen = auth()->user();
            return response()->json([
                'status' => 'success',
                'message' => 'Dosen berhasil login',
                'data' => [
                    'id' => $dosen->id,
                    'nama' => $dosen->nama_dosen,
                    'email' => $dosen->email_dosen,
                    'role' => $dosen->role
                ]
            ]);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Dosen tidak login'
        ], 401);
    }

    // Dashboard dosen - redirect ke dashboard pendamping (legacy)
    public function dashboard()
    {
        return redirect()->route('dosen.pendamping.dashboard');
    }

    // 1.1 Validasi Proposal
    public function validasiProposal()
    {
        $dosen = auth()->user();
        
        // Ambil proposal yang belum divalidasi dan terikat dengan dosen ini
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'mahasiswa.prodi', 'mahasiswa.fakultas'])
            ->where('id_dosen', $dosen->id)
            ->where('status_validasi', 'pending')
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        return view('dosen.validasi_proposal', compact('proposals', 'dosen'));
    }

    // Detail proposal untuk validasi
    public function detailProposal($id)
    {
        $dosen = auth()->user();
        $proposal = Proposal::with(['mahasiswa', 'dokumen', 'mahasiswa.prodi', 'mahasiswa.fakultas'])
            ->where('id_dosen', $dosen->id)
            ->findOrFail($id);

        return view('dosen.detail_proposal', compact('proposal', 'dosen'));
    }

    // Proses validasi proposal
    public function validasiProposalAction(Request $request, $id)
    {
        try {
            // Log request data untuk debugging
            \Log::info('Validasi proposal request', [
                'proposal_id' => $id,
                'action' => $request->action,
                'catatan' => $request->catatan,
                'dosen_id' => auth()->id(),
                'is_authenticated' => auth()->check(),
                'user_role' => auth()->user()?->role,
                'session_id' => session()->getId()
            ]);

            $request->validate([
                'action' => 'required|in:valid,tolak',
                'catatan' => 'required_if:action,tolak'
            ]);

            $dosen = auth()->user();
            $proposal = Proposal::where('id_dosen', $dosen->id)
                ->findOrFail($id);

            // Log proposal data sebelum update
            \Log::info('Proposal sebelum update', [
                'proposal_id' => $proposal->id_proposal,
                'status_validasi' => $proposal->status_validasi,
                'status' => $proposal->status,
                'id_dosen' => $proposal->id_dosen
            ]);

            if ($request->action === 'valid') {
                $proposal->status_validasi = 'valid';
                $proposal->status = 'valid';
                $proposal->catatan = null;
                $proposal->tanggal_validasi = now();
                $message = 'Proposal berhasil divalidasi dan dikirim ke operator untuk review.';
            } else {
                $proposal->status_validasi = 'tidak_valid';
                $proposal->status = 'tidak_valid';
                $proposal->catatan = $request->catatan;
                $proposal->tanggal_validasi = now();
                $message = 'Proposal ditolak dan notifikasi telah dikirim ke mahasiswa.';
            }

            $proposal->save();

            // Kirim notifikasi ke mahasiswa
            try {
                $notificationService = app(NotificationService::class);
                $statusValidasi = $request->action === 'valid' ? 'valid' : 'tidak_valid';
                $notificationService->notifyValidasiDosen(
                    $proposal,
                    $statusValidasi,
                    $request->catatan ?? null
                );
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim notifikasi validasi dosen: ' . $e->getMessage());
            }

            // Log proposal data setelah update
            \Log::info('Proposal setelah update', [
                'proposal_id' => $proposal->id_proposal,
                'status_validasi' => $proposal->status_validasi,
                'status' => $proposal->status,
                'tanggal_validasi' => $proposal->tanggal_validasi
            ]);

            // Log redirect info
            \Log::info('Redirecting after validasi', [
                'route' => 'dosen.validasi.proposal',
                'message' => $message,
                'dosen_id' => $dosen->id
            ]);

            // Log redirect info
            \Log::info('Redirecting after validasi', [
                'route' => 'dosen.validasi.proposal',
                'message' => $message,
                'dosen_id' => $dosen->id
            ]);

            // Log session info sebelum redirect
            \Log::info('Session info before redirect', [
                'session_id' => session()->getId(),
                'session_data' => session()->all(),
                'auth_user_role' => auth()->user()?->role
            ]);

            // Redirect dengan URL yang eksplisit untuk memastikan tidak ada masalah route
            // Gunakan back() untuk kembali ke halaman sebelumnya, lalu redirect ke validasi
            return redirect()->back()
                ->with('success', $message)
                ->with('redirect_to', '/dosen/validasi-proposal');

        } catch (\Exception $e) {
            \Log::error('Error dalam validasi proposal: ' . $e->getMessage(), [
                'proposal_id' => $id,
                'action' => $request->action ?? 'null',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memvalidasi proposal: ' . $e->getMessage());
        }
    }

    // 1.2 Hasil Review
    public function hasilReview()
    {
        $dosen = auth()->user();
        
        // Debug: Log informasi dosen yang login
        \Log::info('Dosen yang login:', [
            'id_dosen' => $dosen->id,
            'nama_dosen' => $dosen->nama_dosen,
            'email_dosen' => $dosen->email_dosen
        ]);
        
        // Ambil proposal yang sudah divalidasi oleh dosen ini (tidak hanya yang sedang review)
        // Query yang lebih fleksibel untuk menampilkan semua proposal yang sudah divalidasi
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif'])
            ->where('id_dosen', $dosen->id)
            ->where('status_validasi', 'valid')
            ->whereNotIn('status', ['pending', 'tidak_valid']) // Exclude proposal yang belum divalidasi atau tidak valid
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        // Debug: Log jumlah proposal yang ditemukan
        \Log::info('Proposal yang ditemukan untuk dosen:', [
            'dosen_id' => $dosen->id,
            'total_proposals' => $proposals->count(),
            'proposals' => $proposals->map(function($proposal) {
                return [
                    'id_proposal' => $proposal->id_proposal,
                    'judul_proposal' => $proposal->judul_proposal,
                    'status' => $proposal->status,
                    'status_validasi' => $proposal->status_validasi,
                    'id_dosen' => $proposal->id_dosen,
                    'mahasiswa' => $proposal->mahasiswa ? $proposal->mahasiswa->nama_mahasiswa : 'N/A'
                ];
            })
        ]);

        // Jika tidak ada proposal, coba cek semua proposal yang terkait dengan dosen ini
        if ($proposals->count() == 0) {
            $allProposals = Proposal::with(['mahasiswa'])
                ->where('id_dosen', $dosen->id)
                ->get();
                
            \Log::info('Semua proposal untuk dosen (untuk debugging):', [
                'dosen_id' => $dosen->id,
                'total_all_proposals' => $allProposals->count(),
                'all_proposals' => $allProposals->map(function($proposal) {
                    return [
                        'id_proposal' => $proposal->id_proposal,
                        'judul_proposal' => $proposal->judul_proposal,
                        'status' => $proposal->status,
                        'status_validasi' => $proposal->status_validasi,
                        'id_dosen' => $proposal->id_dosen,
                        'mahasiswa' => $proposal->mahasiswa ? $proposal->mahasiswa->nama_mahasiswa : 'N/A'
                    ];
                })
            ]);
            
            // Jika masih tidak ada proposal, coba cek apakah ada proposal dengan status yang berbeda
            if ($allProposals->count() > 0) {
                $validProposals = $allProposals->where('status_validasi', 'valid');
                $submittedProposals = $allProposals->whereIn('status', ['submitted', 'review_administratif', 'review_substantif', 'revisi']);
                
                \Log::info('Analisis proposal untuk dosen:', [
                    'dosen_id' => $dosen->id,
                    'valid_proposals_count' => $validProposals->count(),
                    'submitted_proposals_count' => $submittedProposals->count(),
                    'valid_proposals' => $validProposals->map(function($proposal) {
                        return [
                            'id_proposal' => $proposal->id_proposal,
                            'status' => $proposal->status,
                            'status_validasi' => $proposal->status_validasi
                        ];
                    }),
                    'submitted_proposals' => $submittedProposals->map(function($proposal) {
                        return [
                            'id_proposal' => $proposal->id_proposal,
                            'status' => $proposal->status,
                            'status_validasi' => $proposal->status_validasi
                        ];
                    })
                ]);
            }
        }

        return view('dosen.pendamping.hasil_review', compact('proposals', 'dosen'));
    }

    // Detail hasil review
    public function detailHasilReview($id)
    {
        $dosen = auth()->user();
        $proposal = Proposal::with([
            'mahasiswa', 
            'dokumen', 
            'nilaiAdministratif', 
            'nilaiSubstantif',
            'nilaiSubstantif.reviewer'
        ])
            ->where('id_dosen', $dosen->id)
            ->findOrFail($id);

        return view('dosen.pendamping.detail_hasil_review', compact('proposal', 'dosen'));
    }

    // 1.3 Hasil Final
    public function hasilFinal()
    {
        $dosen = auth()->user();
        
        // Debug: Log informasi dosen yang login
        \Log::info('Dosen yang login (Hasil Final):', [
            'id_dosen' => $dosen->id,
            'nama_dosen' => $dosen->nama_dosen,
            'email_dosen' => $dosen->email_dosen
        ]);
        
        // Ambil proposal yang sudah selesai review dan ada hasil final (hanya yang terikat dengan dosen ini)
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'hasilFinal'])
            ->where('id_dosen', $dosen->id)
            ->where('status_validasi', 'valid')
            ->whereIn('status', ['lolos', 'tidak_lolos'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        // Debug: Log jumlah proposal yang ditemukan
        \Log::info('Proposal final yang ditemukan untuk dosen:', [
            'dosen_id' => $dosen->id,
            'total_proposals' => $proposals->count(),
            'proposals' => $proposals->map(function($proposal) {
                return [
                    'id_proposal' => $proposal->id_proposal,
                    'judul_proposal' => $proposal->judul_proposal,
                    'status' => $proposal->status,
                    'status_validasi' => $proposal->status_validasi,
                    'id_dosen' => $proposal->id_dosen,
                    'mahasiswa' => $proposal->mahasiswa ? $proposal->mahasiswa->nama_mahasiswa : 'N/A'
                ];
            })
        ]);

        return view('dosen.pendamping.hasil_final', compact('proposals', 'dosen'));
    }

    // Detail hasil final
    public function detailHasilFinal($id)
    {
        $dosen = auth()->user();
        $proposal = Proposal::with([
            'mahasiswa', 
            'dokumen', 
            'nilaiAdministratif', 
            'nilaiSubstantif',
            'nilaiSubstantif.reviewer',
            'hasilFinal'
        ])
            ->where('id_dosen', $dosen->id)
            ->findOrFail($id);

        return view('dosen.pendamping.detail_hasil_final', compact('proposal', 'dosen'));
    }

    // Download dokumen proposal
    public function downloadDokumen($id, $jenis)
    {
        $dosen = auth()->user();
        $proposal = Proposal::where('id_dosen', $dosen->id)
            ->with('dokumen')
            ->findOrFail($id);

        if (!$proposal->dokumen) {
            abort(404, 'Dokumen tidak ditemukan');
        }

        // Debug info
        \Log::info('Download request', [
            'proposal_id' => $id,
            'jenis' => $jenis,
            'dokumen' => $proposal->dokumen,
            'path_file' => $proposal->dokumen->path_file ?? 'null'
        ]);

        // Gunakan path_file yang ada di tabel dokumens
        $path = $proposal->dokumen->path_file;
        
        if (empty($path)) {
            abort(404, 'File tidak ditemukan - path_file kosong');
        }

        // Cek apakah file ada di storage (public disk)
        if (!StorageHelper::exists($path)) {
            abort(404, 'File tidak ditemukan di storage: ' . $path);
        }

        // Download file dari public disk
        return StorageHelper::download($path);
    }

    // Menampilkan PDF secara langsung untuk iframe
    public function viewPdf($id)
    {
        try {
            $dosen = auth()->user();
            $proposal = Proposal::where('id_dosen', $dosen->id)
                ->with('dokumen')
                ->findOrFail($id);

            if (!$proposal->dokumen || !$proposal->dokumen->path_file) {
                abort(404, 'Dokumen tidak ditemukan.');
            }

            $path = $proposal->dokumen->path_file;
            $filename = basename($path);

            return StorageHelper::response($path, $filename);
        } catch (\Exception $e) {
            \Log::error('Error in viewPdf (Dosen): ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat memuat PDF: ' . $e->getMessage());
        }
    }

    // ============================================
    // DOSEN UNIVERSITAS - Menu Khusus
    // ============================================

    /**
     * Dashboard Dosen Universitas - Menampilkan proposal yang didampinginya
     */
    public function dashboardUniversitas()
    {
        $dosen = auth()->user();
        
        // Ambil proposal yang didampingi sebagai dosen universitas
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'hasilSemiFinal', 'proposalRevisi'])
            ->where('id_dosen_pendamping_universitas', $dosen->id)
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        // Kategorikan proposal berdasarkan status
        $proposalsValidasi = $proposals->where('status', 'validasi_akhir_dosen_univ');
        $proposalsValid = $proposals->where('status_validasi', 'valid')->where('status', '!=', 'validasi_akhir_dosen_univ');
        $proposalsTidakValid = $proposals->where('status_validasi', 'tidak_valid');

        return view('dosen.universitas.dashboard', compact('proposals', 'proposalsValidasi', 'proposalsValid', 'proposalsTidakValid', 'dosen'));
    }

    /**
     * Validasi Akhir Proposal oleh Dosen Universitas
     */
    public function validasiAkhirProposal()
    {
        $dosen = auth()->user();
        
        // Ambil proposal yang perlu divalidasi akhir (status: validasi_akhir_dosen_univ)
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'hasilSemiFinal', 'proposalRevisi'])
            ->where('id_dosen_pendamping_universitas', $dosen->id)
            ->where('status', 'validasi_akhir_dosen_univ')
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        return view('dosen.universitas.validasi_akhir', compact('proposals', 'dosen'));
    }

    /**
     * Detail Proposal untuk Validasi Akhir
     */
    public function detailValidasiAkhir($id)
    {
        $dosen = auth()->user();
        
        $proposal = Proposal::with([
            'mahasiswa',
            'dokumen',
            'hasilSemiFinal',
            'proposalRevisi' => function($query) {
                $query->where('path_file', 'like', '%revisi_akhir%')
                      ->orderBy('tanggal_submit', 'desc');
            }
        ])
            ->where('id_dosen_pendamping_universitas', $dosen->id)
            ->where(function($query) {
                $query->where('status', 'validasi_akhir_dosen_univ')
                      ->orWhere('status', 'pimpinan_pt')
                      ->orWhere('status', 'revisi_akhir');
            })
            ->findOrFail($id);

        return view('dosen.universitas.detail_validasi_akhir', compact('proposal', 'dosen'));
    }

    /**
     * Proses Validasi Akhir Proposal
     */
    public function submitValidasiAkhir(Request $request, $id)
    {
        try {
            $request->validate([
                'action' => 'required|in:valid,tolak',
                'catatan' => 'required_if:action,tolak',
                'file_review' => 'nullable|file|mimes:pdf|max:5120' // Optional PDF untuk review
            ]);

            $dosen = auth()->user();
            $proposal = Proposal::where('id_dosen_pendamping_universitas', $dosen->id)
                ->where(function($query) {
                    $query->where('status', 'validasi_akhir_dosen_univ')
                          ->orWhere('status', 'revisi_akhir');
                })
                ->findOrFail($id);

            DB::beginTransaction();

            if ($request->action === 'valid') {
                // Validasi berhasil - proposal masuk ke Pimpinan PT
                $proposal->status_validasi = 'valid';
                $proposal->status = 'pimpinan_pt';
                $proposal->status_final = 'pimpinan_pt';
                $proposal->catatan = null;
                $proposal->tanggal_validasi = now();
                $message = 'Proposal berhasil divalidasi dan dikirim ke Pimpinan PT untuk penilaian final.';
            } else {
                // Validasi ditolak - kembali ke revisi akhir
                $proposal->status_validasi = 'tidak_valid';
                $proposal->status = 'revisi_akhir';
                $proposal->status_final = 'revisi_akhir';
                $proposal->catatan = $request->catatan;
                $message = 'Proposal ditolak. Mahasiswa akan diminta untuk melakukan revisi ulang.';
                
                // Upload file review jika ada
                if ($request->hasFile('file_review')) {
                    $reviewFile = $request->file('file_review');
                    $fileName = 'review_akhir_' . time() . '_' . $reviewFile->getClientOriginalName();
                    $path = StorageHelper::store('proposals/review_akhir', $reviewFile, $fileName);
                    
                    $proposal->path_review_dosen = $path;
                    $proposal->nama_file_review_dosen = $fileName;
                    $proposal->tanggal_review_dosen = now();
                }
            }

            $proposal->save();

            // Kirim notifikasi ke mahasiswa
            try {
                $notificationService = app(NotificationService::class);
                $statusValidasi = $request->action === 'valid' ? 'valid' : 'tidak_valid';
                $notificationService->notifyValidasiAkhirDosen(
                    $proposal,
                    $statusValidasi,
                    $request->catatan ?? null
                );
            } catch (\Exception $e) {
                \Log::error('Gagal mengirim notifikasi validasi akhir dosen: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('dosen.universitas.validasi.akhir')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error in submitValidasiAkhir', [
                'proposal_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memvalidasi proposal: ' . $e->getMessage());
        }
    }

    /**
     * View PDF Proposal untuk Dosen Universitas
     */
    public function viewPdfUniversitas($id)
    {
        try {
            $dosen = auth()->user();
            $proposal = Proposal::where('id_dosen_pendamping_universitas', $dosen->id)
                ->with('dokumen')
                ->findOrFail($id);

            if (!$proposal->dokumen || !$proposal->dokumen->path_file) {
                abort(404, 'Dokumen tidak ditemukan.');
            }

            $pathFile = $proposal->dokumen->path_file;
            $filename = basename($pathFile);

            return StorageHelper::response($pathFile, $filename);
        } catch (\Exception $e) {
            \Log::error('Error in viewPdfUniversitas: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Terjadi kesalahan saat memuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * View PDF Revisi Akhir untuk Dosen Universitas
     */
    public function viewPdfRevisiAkhir($id)
    {
        try {
            $dosen = auth()->user();
            
            // $id adalah ID proposal, bukan ID revisi
            $proposal = Proposal::where('id_dosen_pendamping_universitas', $dosen->id)
                ->with(['proposalRevisi' => function($query) {
                    $query->where('path_file', 'like', '%revisi_akhir%')
                          ->orderBy('tanggal_submit', 'desc');
                }])
                ->findOrFail($id);

            $revisi = $proposal->proposalRevisi->first();
            
            if (!$revisi) {
                abort(404, 'File revisi akhir tidak ditemukan.');
            }

            return StorageHelper::response($revisi->path_file, $revisi->nama_file);
        } catch (\Exception $e) {
            \Log::error('Error in viewPdfRevisiAkhir: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Terjadi kesalahan saat memuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Download File Revisi Akhir
     */
    public function downloadRevisiAkhir($id)
    {
        try {
            $dosen = auth()->user();
            
            // $id adalah ID proposal, bukan ID revisi
            $proposal = Proposal::where('id_dosen_pendamping_universitas', $dosen->id)
                ->with(['proposalRevisi' => function($query) {
                    $query->where('path_file', 'like', '%revisi_akhir%')
                          ->orderBy('tanggal_submit', 'desc');
                }])
                ->findOrFail($id);

            $revisi = $proposal->proposalRevisi->first();
            
            if (!$revisi) {
                return redirect()->back()->with('error', 'File revisi akhir tidak ditemukan.');
            }

            if (!StorageHelper::exists($revisi->path_file)) {
                return redirect()->back()->with('error', 'File revisi tidak ditemukan di server.');
            }

            return StorageHelper::download($revisi->path_file);
        } catch (\Exception $e) {
            \Log::error('Error in downloadRevisiAkhir: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunduh file revisi: ' . $e->getMessage());
        }
    }

    // Method untuk mendapatkan data review yang lebih detail
    public function getReviewData($proposalId)
    {
        try {
            $dosen = auth()->user();
            $proposal = Proposal::where('id_dosen', $dosen->id)
                ->with(['nilaiAdministratif', 'nilaiSubstantif.reviewer', 'mahasiswa', 'hasilFinal'])
                ->findOrFail($proposalId);

            // Format data administratif
            $administratifData = null;
            if ($proposal->nilaiAdministratif && $proposal->nilaiAdministratif->count() > 0) {
                $adminReview = $proposal->nilaiAdministratif->first();
                
                // Format checklist to show actual values (these are the selected errors)
                $formattedChecklist = [];
                if ($adminReview->checklist && is_array($adminReview->checklist)) {
                    $formattedChecklist = $adminReview->checklist;
                }
                
                $administratifData = [
                    'catatan' => $adminReview->note_administratif,
                    'checklist' => $formattedChecklist,
                    'reviewer' => 'Reviewer 1', // Anonymize reviewer
                    'created_at' => $adminReview->created_at->format('d F Y, H:i')
                ];
            }

            // Format data substantif
            $substantifData = [];
            if ($proposal->nilaiSubstantif && $proposal->nilaiSubstantif->count() > 0) {
                foreach ($proposal->nilaiSubstantif as $index => $review) {
                    $substantifData[] = [
                        'catatan' => $review->note_substantif,
                        'reviewer' => 'Reviewer ' . ($index + 1), // Anonymize reviewer
                        'created_at' => $review->created_at->format('d F Y, H:i')
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'proposal_info' => [
                    'judul' => $proposal->judul_proposal,
                    'skim' => $proposal->skim,
                    'mahasiswa' => $proposal->mahasiswa->nama_mahasiswa,
                    'status' => $proposal->status,
                    'status_final' => $proposal->status_final
                ],
                'administratif' => $administratifData,
                'substantif' => $substantifData,
                'hasil_final' => $proposal->hasilFinal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data review: ' . $e->getMessage()
            ], 500);
        }
    }
}
