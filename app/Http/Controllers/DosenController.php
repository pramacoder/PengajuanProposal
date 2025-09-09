<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Proposal;
use App\Models\Dokumen;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use App\Models\HasilFinal;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Support\Facades\Storage;

class DosenController extends Controller
{
    // Method test untuk memastikan authentication berfungsi
    public function test()
    {
        if (Auth::guard('dosen')->check()) {
            $dosen = Auth::guard('dosen')->user();
            return response()->json([
                'status' => 'success',
                'message' => 'Dosen berhasil login',
                'data' => [
                    'id' => $dosen->id_dosen,
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

    // Dashboard dosen - redirect ke menu PKM
    public function dashboard()
    {
        return redirect()->route('dosen.validasi.proposal');
    }

    // 1.1 Validasi Proposal
    public function validasiProposal()
    {
        $dosen = Auth::guard('dosen')->user();
        
        // Ambil proposal yang belum divalidasi dan terikat dengan dosen ini
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'mahasiswa.prodi', 'mahasiswa.fakultas'])
            ->where('id_dosen', $dosen->id_dosen)
            ->where('status_validasi', 'pending')
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        return view('dosen.validasi_proposal', compact('proposals', 'dosen'));
    }

    // Detail proposal untuk validasi
    public function detailProposal($id)
    {
        $dosen = Auth::guard('dosen')->user();
        $proposal = Proposal::with(['mahasiswa', 'dokumen', 'mahasiswa.prodi', 'mahasiswa.fakultas'])
            ->where('id_dosen', $dosen->id_dosen)
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
                'dosen_id' => Auth::guard('dosen')->id(),
                'current_guard' => Auth::getDefaultDriver(),
                'is_dosen_authenticated' => Auth::guard('dosen')->check(),
                'session_id' => session()->getId()
            ]);

            $request->validate([
                'action' => 'required|in:valid,tolak',
                'catatan' => 'required_if:action,tolak'
            ]);

            $dosen = Auth::guard('dosen')->user();
            $proposal = Proposal::where('id_dosen', $dosen->id_dosen)
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
                'dosen_id' => $dosen->id_dosen
            ]);

            // Log redirect info
            \Log::info('Redirecting after validasi', [
                'route' => 'dosen.validasi.proposal',
                'message' => $message,
                'dosen_id' => $dosen->id_dosen
            ]);

            // Log session info sebelum redirect
            \Log::info('Session info before redirect', [
                'session_id' => session()->getId(),
                'session_data' => session()->all(),
                'auth_guards' => [
                    'dosen' => Auth::guard('dosen')->check(),
                    'mahasiswa' => Auth::guard('mahasiswa')->check(),
                    'operator' => Auth::guard('operator')->check(),
                    'reviewer' => Auth::guard('reviewer')->check()
                ]
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
        $dosen = Auth::guard('dosen')->user();
        
        // Debug: Log informasi dosen yang login
        \Log::info('Dosen yang login:', [
            'id_dosen' => $dosen->id_dosen,
            'nama_dosen' => $dosen->nama_dosen,
            'email_dosen' => $dosen->email_dosen
        ]);
        
        // Ambil proposal yang sudah divalidasi oleh dosen ini (tidak hanya yang sedang review)
        // Query yang lebih fleksibel untuk menampilkan semua proposal yang sudah divalidasi
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif'])
            ->where('id_dosen', $dosen->id_dosen)
            ->where('status_validasi', 'valid')
            ->whereNotIn('status', ['pending', 'tidak_valid']) // Exclude proposal yang belum divalidasi atau tidak valid
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        // Debug: Log jumlah proposal yang ditemukan
        \Log::info('Proposal yang ditemukan untuk dosen:', [
            'dosen_id' => $dosen->id_dosen,
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
                ->where('id_dosen', $dosen->id_dosen)
                ->get();
                
            \Log::info('Semua proposal untuk dosen (untuk debugging):', [
                'dosen_id' => $dosen->id_dosen,
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
                    'dosen_id' => $dosen->id_dosen,
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

        return view('dosen.hasil_review', compact('proposals', 'dosen'));
    }

    // Detail hasil review
    public function detailHasilReview($id)
    {
        $dosen = Auth::guard('dosen')->user();
        $proposal = Proposal::with([
            'mahasiswa', 
            'dokumen', 
            'nilaiAdministratif', 
            'nilaiSubstantif',
            'nilaiSubstantif.reviewer'
        ])
            ->where('id_dosen', $dosen->id_dosen)
            ->findOrFail($id);

        return view('dosen.detail_hasil_review', compact('proposal', 'dosen'));
    }

    // 1.3 Hasil Final
    public function hasilFinal()
    {
        $dosen = Auth::guard('dosen')->user();
        
        // Debug: Log informasi dosen yang login
        \Log::info('Dosen yang login (Hasil Final):', [
            'id_dosen' => $dosen->id_dosen,
            'nama_dosen' => $dosen->nama_dosen,
            'email_dosen' => $dosen->email_dosen
        ]);
        
        // Ambil proposal yang sudah selesai review dan ada hasil final (hanya yang terikat dengan dosen ini)
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'hasilFinal'])
            ->where('id_dosen', $dosen->id_dosen)
            ->where('status_validasi', 'valid')
            ->whereIn('status', ['lolos', 'tidak_lolos'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        // Debug: Log jumlah proposal yang ditemukan
        \Log::info('Proposal final yang ditemukan untuk dosen:', [
            'dosen_id' => $dosen->id_dosen,
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

        return view('dosen.hasil_final', compact('proposals', 'dosen'));
    }

    // Detail hasil final
    public function detailHasilFinal($id)
    {
        $dosen = Auth::guard('dosen')->user();
        $proposal = Proposal::with([
            'mahasiswa', 
            'dokumen', 
            'nilaiAdministratif', 
            'nilaiSubstantif',
            'nilaiSubstantif.reviewer',
            'hasilFinal'
        ])
            ->where('id_dosen', $dosen->id_dosen)
            ->findOrFail($id);

        return view('dosen.detail_hasil_final', compact('proposal', 'dosen'));
    }

    // Download dokumen proposal
    public function downloadDokumen($id, $jenis)
    {
        $dosen = Auth::guard('dosen')->user();
        $proposal = Proposal::where('id_dosen', $dosen->id_dosen)
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

        // Cek apakah file ada di storage
        if (!Storage::exists($path)) {
            abort(404, 'File tidak ditemukan di storage: ' . $path);
        }

        // Download file
        return Storage::download($path);
    }

    // Method untuk mendapatkan data review yang lebih detail
    public function getReviewData($proposalId)
    {
        try {
            $dosen = Auth::guard('dosen')->user();
            $proposal = Proposal::where('id_dosen', $dosen->id_dosen)
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
