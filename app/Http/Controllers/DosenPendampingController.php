<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\TahunAjaranHelper;

class DosenPendampingController extends Controller
{
    /**
     * Dashboard dosen pendamping - validasi proposal
     */
    public function dashboard(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();
        
        // Ambil tahun ajaran yang dipilih (default: tahun ajaran terbaru)
        $tahunAjaranTerpilih = $request->input('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        
        // Ambil semua proposal yang ditugaskan ke dosen ini dengan filter tahun ajaran
        $proposalsQuery = $dosen->proposals()->with([
            'mahasiswa',
            'dokumen',
            'nilaiAdministratif',
            'nilaiSubstantif',
            'hasilFinal'
        ]);
        
        // Filter berdasarkan tahun ajaran
        if ($tahunAjaranTerpilih) {
            $proposalsQuery->where('tahun_ajaran', $tahunAjaranTerpilih);
        }
        
        $proposals = $proposalsQuery->orderBy('tanggal_pengajuan', 'desc')->get();

        // Hitung statistik
        $totalProposal = $proposals->count();
        $proposalPending = $proposals->where('status_validasi', 'pending')->count();
        $proposalValid = $proposals->where('status_validasi', 'valid')->count();
        $proposalTidakValid = $proposals->where('status_validasi', 'tidak_valid')->count();

        // Ambil daftar tahun ajaran yang tersedia dari proposal dosen ini
        $tahunAjaranList = $dosen->proposals()
            ->whereNotNull('tahun_ajaran')
            ->whereRaw("tahun_ajaran LIKE '%/%'") // Hanya format YYYY/YYYY
            ->distinct()
            ->orderByRaw("CAST(SUBSTRING_INDEX(tahun_ajaran, '/', 1) AS UNSIGNED) DESC")
            ->pluck('tahun_ajaran')
            ->filter(function($item) {
                // Pastikan format benar (mengandung slash dan memiliki 2 bagian)
                return strpos($item, '/') !== false && 
                       count(explode('/', $item)) === 2;
            })
            ->values();

        // Jika belum ada proposal, set default tahun ajaran terbaru
        if ($tahunAjaranList->isEmpty()) {
            $tahunAjaranList = collect([TahunAjaranHelper::getTahunAjaranTerbaru()]);
        }

        return view('dosen.pendamping.dashboard', compact(
            'proposals', 
            'totalProposal', 
            'proposalPending', 
            'proposalValid', 
            'proposalTidakValid',
            'tahunAjaranTerpilih',
            'tahunAjaranList'
        ));
    }

    /**
     * Detail proposal untuk validasi
     */
    public function detailProposal($id)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $proposal = Proposal::with([
            'mahasiswa',
            'dokumen',
            'nilaiAdministratif',
            'nilaiSubstantif',
            'hasilFinal'
        ])->findOrFail($id);

        // Pastikan proposal ini ditugaskan ke dosen ini
        if ($proposal->id_dosen !== $dosen->id_dosen) {
            abort(403, 'Anda tidak memiliki akses ke proposal ini.');
        }

        return view('dosen.pendamping.detail_proposal', compact('proposal'));
    }

    /**
     * Validasi proposal
     */
    public function validasi(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:valid,tolak',
            'catatan' => 'nullable|string|max:1000',
            'review_pdf' => 'nullable|file|mimes:pdf|max:5120', // 5MB max, optional
            'file_koreksi' => 'nullable|file|mimes:pdf|max:5120' // File koreksi untuk penolakan: PDF saja, 5MB max
        ]);

        $dosen = Auth::guard('dosen')->user();
        $proposal = Proposal::with('dokumen')->findOrFail($id);

        // Pastikan proposal ini ditugaskan ke dosen ini
        if ($proposal->id_dosen !== $dosen->id_dosen) {
            abort(403, 'Anda tidak memiliki akses ke proposal ini.');
        }

        // Tentukan status validasi berdasarkan action
        $statusValidasi = $request->action === 'valid' ? 'valid' : 'tidak_valid';
        
        // Jika action adalah 'tolak', pastikan ada catatan
        if ($request->action === 'tolak' && empty($request->catatan)) {
            return back()
                ->withErrors(['catatan' => 'Alasan penolakan harus diisi.'])
                ->withInput();
        }

        $updateData = [
            'status_validasi' => $statusValidasi,
            'catatan' => $request->catatan, // Menggunakan field 'catatan' bukan 'catatan_validasi'
            'tanggal_validasi' => now(),
            'status' => $statusValidasi === 'valid' ? 'submitted' : 'tidak_valid' // Update status proposal juga
        ];

        // Handle PDF review upload (optional - untuk validasi)
        if ($request->hasFile('review_pdf')) {
            $file = $request->file('review_pdf');
            $fileName = 'review_dosen_' . $id . '_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('dosen_reviews', $fileName, 'public');
            
            // Delete old review PDF if exists
            if ($proposal->path_review_dosen && Storage::disk('public')->exists($proposal->path_review_dosen)) {
                Storage::disk('public')->delete($proposal->path_review_dosen);
            }
            
            $updateData['path_review_dosen'] = $path;
            $updateData['nama_file_review_dosen'] = $file->getClientOriginalName();
            $updateData['tanggal_review_dosen'] = now();
        }

        // Handle file koreksi untuk penolakan (PDF atau Word)
        if ($request->action === 'tolak' && $request->hasFile('file_koreksi')) {
            $file = $request->file('file_koreksi');
            $fileName = 'koreksi_dosen_' . $id . '_' . time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('proposals', $fileName, 'public');
            
            // Jika proposal memiliki dokumen, backup file asli dan replace dengan file koreksi
            if ($proposal->dokumen) {
                // Backup path file asli jika belum ada backup
                if (!$proposal->dokumen->path_file_original && $proposal->dokumen->path_file) {
                    $proposal->dokumen->path_file_original = $proposal->dokumen->path_file;
                    $proposal->dokumen->save();
                }
                
                // Hapus file proposal asli jika berbeda dengan file koreksi
                if ($proposal->dokumen->path_file && 
                    $proposal->dokumen->path_file !== $path && 
                    Storage::disk('public')->exists($proposal->dokumen->path_file)) {
                    // File asli sudah di-backup di path_file_original, jadi bisa dihapus jika bukan file koreksi sebelumnya
                    if ($proposal->dokumen->path_file_original && $proposal->dokumen->path_file !== $proposal->dokumen->path_file_original) {
                        Storage::disk('public')->delete($proposal->dokumen->path_file);
                    }
                }
                
                // Update path_file dengan file koreksi baru
                $proposal->dokumen->path_file = $path;
                $proposal->dokumen->tgl_upload = now(); // Update tanggal upload
                $proposal->dokumen->save();
            } else {
                // Jika tidak ada dokumen, buat baru
                $proposal->dokumen()->create([
                    'path_file' => $path,
                    'skim' => $proposal->skim,
                    'tgl_upload' => now(),
                ]);
            }
        }

        $proposal->update($updateData);

        // Kirim notifikasi ke mahasiswa
        // TODO: Implementasi notifikasi

        $message = $statusValidasi === 'valid' 
            ? 'Proposal berhasil divalidasi dan diteruskan ke reviewer.' 
            : 'Proposal ditolak dengan alasan yang telah diberikan.';

        if ($request->hasFile('review_pdf')) {
            $message .= ' Review PDF telah diupload.';
        }

        if ($request->action === 'tolak' && $request->hasFile('file_koreksi')) {
            $message .= ' File koreksi telah diupload dan menggantikan file proposal asli.';
        }

        return redirect()->route('dosen.pendamping.dashboard')
            ->with('success', $message);
    }

    /**
     * List proposal yang perlu divalidasi
     */
    public function proposalValidasi(Request $request)
    {
        $dosen = Auth::guard('dosen')->user();
        
        // Ambil tahun ajaran yang dipilih (default: tahun ajaran terbaru)
        $tahunAjaranTerpilih = $request->input('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        
        // Query proposal dengan filter tahun ajaran
        $proposalsQuery = $dosen->proposals()->with([
            'mahasiswa',
            'mahasiswa.prodi',
            'mahasiswa.fakultas'
        ]);
        
        // Filter berdasarkan tahun ajaran
        // Jika tahun_ajaran di database null atau tidak sesuai, gunakan tahun ajaran dari tanggal_pengajuan
        if ($tahunAjaranTerpilih) {
            $proposalsQuery->where(function($query) use ($tahunAjaranTerpilih) {
                // Filter berdasarkan tahun_ajaran yang ada di database
                $query->where('tahun_ajaran', $tahunAjaranTerpilih)
                      // Atau jika tahun_ajaran null/kosong, hitung dari tanggal_pengajuan
                      ->orWhere(function($q) use ($tahunAjaranTerpilih) {
                          $q->where(function($subQ) {
                              $subQ->whereNull('tahun_ajaran')
                                   ->orWhere('tahun_ajaran', '');
                          })
                          ->whereRaw("CONCAT(
                              IF(MONTH(tanggal_pengajuan) >= 7, 
                                  YEAR(tanggal_pengajuan), 
                                  YEAR(tanggal_pengajuan) - 1
                              ), 
                              '/', 
                              IF(MONTH(tanggal_pengajuan) >= 7, 
                                  YEAR(tanggal_pengajuan) + 1, 
                                  YEAR(tanggal_pengajuan)
                              )
                          ) = ?", [$tahunAjaranTerpilih]);
                      });
            });
        }
        
        $proposals = $proposalsQuery->orderBy('tanggal_pengajuan', 'desc')->get();
        
        // Perbaiki tahun ajaran proposal yang null atau tidak sesuai dengan tanggal pengajuan
        foreach ($proposals as $proposal) {
            if (empty($proposal->tahun_ajaran) || 
                $proposal->tahun_ajaran !== TahunAjaranHelper::getTahunAjaranByDate($proposal->tanggal_pengajuan)) {
                // Hitung tahun ajaran dari tanggal pengajuan
                $tahunAjaranDariTanggal = TahunAjaranHelper::getTahunAjaranByDate($proposal->tanggal_pengajuan);
                
                // Update tahun ajaran di database jika berbeda
                if ($proposal->tahun_ajaran !== $tahunAjaranDariTanggal) {
                    $proposal->tahun_ajaran = $tahunAjaranDariTanggal;
                    $proposal->save();
                }
            }
        }

        // Ambil daftar tahun ajaran yang tersedia dari proposal dosen ini
        // Termasuk yang dihitung dari tanggal_pengajuan jika tahun_ajaran null
        $tahunAjaranList = $dosen->proposals()
            ->get()
            ->map(function($proposal) {
                // Jika tahun_ajaran null, hitung dari tanggal_pengajuan
                if (empty($proposal->tahun_ajaran)) {
                    return TahunAjaranHelper::getTahunAjaranByDate($proposal->tanggal_pengajuan);
                }
                return $proposal->tahun_ajaran;
            })
            ->filter(function($item) {
                // Pastikan format benar (mengandung slash dan memiliki 2 bagian)
                return !empty($item) && 
                       strpos($item, '/') !== false && 
                       count(explode('/', $item)) === 2;
            })
            ->unique()
            ->sort(function($a, $b) {
                $yearA = (int) explode('/', $a)[0];
                $yearB = (int) explode('/', $b)[0];
                return $yearB - $yearA; // Sort descending
            })
            ->values();

        // Jika belum ada proposal, set default tahun ajaran terbaru
        if ($tahunAjaranList->isEmpty()) {
            $tahunAjaranList = collect([TahunAjaranHelper::getTahunAjaranTerbaru()]);
        }

        return view('dosen.pendamping.proposal_validasi', compact('proposals', 'tahunAjaranTerpilih', 'tahunAjaranList'));
    }

    /**
     * Hasil review proposal yang sudah dinilai reviewer
     */
    public function hasilReview()
    {
        $dosen = Auth::guard('dosen')->user();
        
        // Ambil proposal yang sudah divalidasi dan sudah direview
        $proposals = $dosen->proposals()
            ->whereIn('status', ['review_administratif', 'review_substantif', 'revisi', 'lolos', 'tidak_lolos'])
            ->with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif'])
            ->get();

        return view('dosen.pendamping.hasil_review', compact('proposals'));
    }

    /**
     * Hasil final proposal
     */
    public function hasilFinal()
    {
        $dosen = Auth::guard('dosen')->user();
        
        // Ambil proposal yang sudah selesai review dan ada hasil final
        $proposals = $dosen->proposals()
            ->whereIn('status', ['lolos', 'tidak_lolos'])
            ->with(['mahasiswa', 'dokumen', 'hasilFinal'])
            ->get();

        return view('dosen.pendamping.hasil_final', compact('proposals'));
    }

    /**
     * Get review data untuk modal
     */
    public function getReviewData($proposalId)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $proposal = Proposal::with([
            'nilaiAdministratif', 
            'nilaiSubstantif.reviewer', 
            'mahasiswa', 
            'hasilFinal'
        ])->findOrFail($proposalId);

        // Pastikan proposal ini ditugaskan ke dosen ini
        if ($proposal->id_dosen !== $dosen->id_dosen) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke proposal ini.'
            ], 403);
        }

        // Ambil data review administratif
        $administratif = $proposal->nilaiAdministratif->first();
        
        // Ambil data review substantif
        $substantif = $proposal->nilaiSubstantif->map(function($item) {
            return [
                'reviewer' => $item->reviewer ? $item->reviewer->nama_reviewer : 'Unknown',
                'catatan' => $item->komentar,
                'created_at' => $item->created_at ? $item->created_at->format('d F Y, H:i') : 'N/A'
            ];
        });

        return response()->json([
            'success' => true,
            'administratif' => $administratif ? [
                'reviewer' => $administratif->reviewer ? $administratif->reviewer->nama_reviewer : 'Unknown',
                'catatan' => $administratif->komentar,
                'checklist' => $administratif->checklist ? json_decode($administratif->checklist, true) : [],
                'created_at' => $administratif->created_at ? $administratif->created_at->format('d F Y, H:i') : 'N/A'
            ] : null,
            'substantif' => $substantif,
            'proposal_info' => [
                'judul' => $proposal->judul_proposal,
                'skim' => $proposal->skim,
                'mahasiswa' => $proposal->mahasiswa->nama_mhs,
                'status' => $proposal->status
            ],
            'hasil_final' => $proposal->hasilFinal
        ]);
    }
}
