<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Proposal;
use App\Models\Dosen;
use App\Models\Reviewer;
use App\Models\RuangKontrol;
use App\Models\HasilFinal;
use App\Models\HasilSemiFinal;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use App\Models\Dokumen;
use App\Models\PT;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\ProposalRevisi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Services\NotificationService;
use App\Helpers\TahunAjaranHelper;

class OperatorController extends Controller
{
    public function dashboard(Request $request)
    {
        // Ambil tahun ajaran yang dipilih (default: tahun ajaran terbaru)
        $tahunAjaranTerpilih = $request->input('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        
        // Ambil daftar tahun ajaran yang tersedia dari proposal
        $tahunAjaranList = Proposal::whereNotNull('tahun_ajaran')
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
        
        // Data untuk PKM-8 Bidang
        $pkm8Bidang = $this->getPKM8BidangData($tahunAjaranTerpilih);
        
        // Data untuk PKM Insentif
        $pkmInsentif = $this->getPKMInsentifData($tahunAjaranTerpilih);
        
        // Total keseluruhan
        $totalKeseluruhan = $pkm8Bidang->sum('jumlah');
        $totalInsentif = $pkmInsentif->sum('jumlah');
        
        // Data untuk grafik
        $chartData = $this->getChartData($tahunAjaranTerpilih);
        
        // Data perangkingan proposal terbaik
        $topProposals = $this->getTopProposals($tahunAjaranTerpilih);
        
        // Data untuk filter proposal
        $filteredProposals = $this->getFilteredProposals($request, $tahunAjaranTerpilih);
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        
        // Get unique skims from proposals berdasarkan tahun ajaran
        $skims = Proposal::where('tahun_ajaran', $tahunAjaranTerpilih)
            ->distinct()
            ->pluck('skim')
            ->filter()
            ->sort()
            ->values();
        
        return view('operator.dashboard', compact(
            'pkm8Bidang', 
            'pkmInsentif', 
            'totalKeseluruhan', 
            'totalInsentif', 
            'tahunAjaranTerpilih', 
            'tahunAjaranList',
            'chartData', 
            'topProposals',
            'filteredProposals',
            'fakultas',
            'prodis',
            'skims'
        ));
    }
    
    /**
     * Get filtered proposals based on request filters
     */
    private function getFilteredProposals(Request $request, $tahunAjaran)
    {
        // Jika tidak ada filter yang dipilih, return empty collection
        if (!$request->filled('filter_fakultas') && 
            !$request->filled('filter_prodi') && 
            !$request->filled('filter_skim') && 
            !$request->filled('filter_status')) {
            return collect([]);
        }
        
        $query = Proposal::with(['mahasiswa', 'semuaAnggotaTim', 'hasilFinal'])
            ->where('tahun_ajaran', $tahunAjaran);
        
        // Filter berdasarkan Fakultas
        if ($request->filled('filter_fakultas')) {
            $fakultas = Fakultas::find($request->filter_fakultas);
            if ($fakultas) {
                $query->where(function($q) use ($fakultas) {
                    $q->whereHas('mahasiswa', function($subQ) use ($fakultas) {
                        $subQ->where('fakultas_mhs', $fakultas->nama_fakultas);
                    })->orWhereHas('semuaAnggotaTim', function($subQ) use ($fakultas) {
                        $subQ->where('fakultas_mhs', $fakultas->nama_fakultas);
                    });
                });
            }
        }
        
        // Filter berdasarkan Prodi
        if ($request->filled('filter_prodi')) {
            $prodi = Prodi::find($request->filter_prodi);
            if ($prodi) {
                $query->where(function($q) use ($prodi) {
                    $q->whereHas('mahasiswa', function($subQ) use ($prodi) {
                        $subQ->where('prodi_mhs', $prodi->nama_prodi);
                    })->orWhereHas('semuaAnggotaTim', function($subQ) use ($prodi) {
                        $subQ->where('prodi_mhs', $prodi->nama_prodi);
                    });
                });
            }
        }
        
        // Filter berdasarkan Skim
        if ($request->filled('filter_skim')) {
            $query->where('skim', $request->filter_skim);
        }
        
        // Filter berdasarkan Status Lolos
        if ($request->filled('filter_status')) {
            if ($request->filter_status === 'lolos') {
                $query->whereHas('hasilFinal', function($q) {
                    $q->where('status_final', 'lolos');
                });
            } elseif ($request->filter_status === 'tidak_lolos') {
                $query->whereHas('hasilFinal', function($q) {
                    $q->where('status_final', 'tidak_lolos');
                });
            } elseif ($request->filter_status === 'belum_final') {
                $query->whereDoesntHave('hasilFinal');
            }
        }
        
        // Order by tanggal pengajuan
        $proposals = $query->orderBy('tanggal_pengajuan', 'desc');
        
        // Limit to 20 by default unless "show_all" is requested
        if (!$request->has('show_all') || $request->show_all != '1') {
            $proposals = $proposals->limit(20);
        }
        
        return $proposals->get();
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
                'note_administratif' => null, // Tidak ada note default, reviewer harus mengisi
                'checklist' => json_encode([])
            ]);
            
            $nilaiSub1 = NilaiSubstantif::create([
                'id_proposal' => $request->proposal_id,
                'id_reviewer' => $request->reviewer_substantif_1,
                'note_substantif' => null // Tidak ada note default, reviewer harus mengisi
            ]);
            
            $nilaiSub2 = NilaiSubstantif::create([
                'id_proposal' => $request->proposal_id,
                'id_reviewer' => $request->reviewer_substantif_2,
                'note_substantif' => null // Tidak ada note default, reviewer harus mengisi
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
        // Gunakan TahunAjaranHelper untuk format tahun akademik
        $tahunAjaranTerpilih = request('tahun', TahunAjaranHelper::getTahunAjaranTerbaru());
        
        // Ambil ruang kontrol aktif untuk tahun akademik yang dipilih
        $ruangKontrolAktif = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerpilih)
            ->where('is_active', true)
            ->first();
        
        // Jika tidak ada aktif, ambil yang pertama atau buat baru
        if (!$ruangKontrolAktif) {
            $ruangKontrolAktif = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerpilih)->first();
            
            if (!$ruangKontrolAktif) {
                $ruangKontrolAktif = RuangKontrol::create([
                    'status_pendaftaran' => 'tertutup',
                    'status_perbaikan' => 'tertutup',
                    'tahun_ajaran' => $tahunAjaranTerpilih,
                    'nama_history' => 'Jadwal ' . $tahunAjaranTerpilih,
                    'is_active' => true,
                    'id_pt' => auth('operator')->id()
                ]);
            } else {
                // Set yang pertama sebagai aktif jika belum ada yang aktif
                $ruangKontrolAktif->update(['is_active' => true]);
            }
        }
        
        // Ambil semua history untuk dropdown (hanya format tahun akademik YYYY/YYYY)
        $histories = RuangKontrol::whereNotNull('tahun_ajaran')
            ->whereRaw("tahun_ajaran LIKE '%/%'") // Hanya format YYYY/YYYY
            ->orderByRaw("CAST(SUBSTRING_INDEX(tahun_ajaran, '/', 1) AS UNSIGNED) DESC")
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function($item) {
                // Double check: pastikan format benar (mengandung slash dan memiliki 2 bagian)
                return strpos($item->tahun_ajaran, '/') !== false && 
                       count(explode('/', $item->tahun_ajaran)) === 2;
            })
            ->groupBy('tahun_ajaran');
        
        // Ambil semua jadwal untuk tahun akademik yang dipilih
        $jadwalTahun = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerpilih)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Auto-check dan update status berdasarkan tanggal (hanya untuk jadwal aktif)
        if ($ruangKontrolAktif && $ruangKontrolAktif->is_active) {
            $this->checkAndUpdateAutoActivation($ruangKontrolAktif);
            // Reload untuk mendapatkan status terbaru
            $ruangKontrolAktif->refresh();
        }
        
        return view('operator.ruang_kontrol', compact('ruangKontrolAktif', 'histories', 'jadwalTahun', 'tahunAjaranTerpilih'));
    }
    
    /**
     * Extract year from tahun ajaran format (support "2025/2026")
     * Untuk tahun akademik, ambil tahun pertama sebagai referensi
     */
    private function extractYearFromTahunAjaran($tahunAjaran)
    {
        if (strpos($tahunAjaran, '/') !== false) {
            // Format "2025/2026" - ambil tahun pertama
            $parts = explode('/', $tahunAjaran);
            return (int) $parts[0];
        }
        // Jika format lama (tanpa slash), kembalikan sebagai integer
        return (int) $tahunAjaran;
    }
    
    /**
     * Check apakah tahun akademik adalah tahun masa lalu
     */
    private function isTahunAkademikMasaLalu($tahunAjaran)
    {
        $tahunAkademikSekarang = TahunAjaranHelper::getTahunAjaranTerbaru();
        $tahunPertamaSekarang = (int) explode('/', $tahunAkademikSekarang)[0];
        $tahunPertama = $this->extractYearFromTahunAjaran($tahunAjaran);
        
        return $tahunPertama < $tahunPertamaSekarang;
    }

    /**
     * Check and auto-activate phases based on dates
     */
    private function checkAndUpdateAutoActivation($ruangKontrol)
    {
        $now = now();
        $updated = false;
        
        // Check Fase 1: Pengajuan Proposal
        if ($ruangKontrol->tanggal_pendaftaran_mulai && 
            $ruangKontrol->tanggal_pendaftaran_selesai) {
            
            $mulai = \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_mulai);
            $selesai = \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_selesai);
            
            // Jika sekarang berada dalam rentang tanggal fase 1 dan fase 1 belum terbuka
            if ($now->gte($mulai) && $now->lte($selesai)) {
                if ($ruangKontrol->status_pendaftaran !== 'terbuka') {
                    $ruangKontrol->status_pendaftaran = 'terbuka';
                    $ruangKontrol->status_perbaikan = 'tertutup'; // Tutup fase 2 jika fase 1 aktif
                    $updated = true;
                }
            } 
            // Jika sudah lewat tanggal selesai, tutup fase 1
            elseif ($now->gt($selesai) && $ruangKontrol->status_pendaftaran === 'terbuka') {
                $ruangKontrol->status_pendaftaran = 'tertutup';
                $updated = true;
            }
        }
        
        // Check Fase 2: Perbaikan Proposal (hanya jika fase 1 tidak aktif)
        if ($ruangKontrol->status_pendaftaran !== 'terbuka' &&
            $ruangKontrol->tanggal_perbaikan_mulai && 
            $ruangKontrol->tanggal_perbaikan_selesai) {
            
            $mulai = \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_mulai);
            $selesai = \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai);
            
            // Jika sekarang berada dalam rentang tanggal fase 2 dan fase 2 belum terbuka
            if ($now->gte($mulai) && $now->lte($selesai)) {
                if ($ruangKontrol->status_perbaikan !== 'terbuka') {
                    $ruangKontrol->status_perbaikan = 'terbuka';
                    $updated = true;
                }
            } 
            // Jika sudah lewat tanggal selesai, tutup fase 2
            elseif ($now->gt($selesai) && $ruangKontrol->status_perbaikan === 'terbuka') {
                $ruangKontrol->status_perbaikan = 'tertutup';
                $updated = true;
            }
        }
        
        if ($updated) {
            $ruangKontrol->save();
            \Log::info('Auto-activation updated', [
                'ruang_kontrol_id' => $ruangKontrol->id_ruang_kontrol,
                'status_pendaftaran' => $ruangKontrol->status_pendaftaran,
                'status_perbaikan' => $ruangKontrol->status_perbaikan
            ]);
        }
    }

    /**
     * Get current active phase status
     */
    public function getActivePhase()
    {
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
        
        if (!$ruangKontrol) {
            return response()->json([
                'success' => true,
                'active_phase' => null,
                'status_pendaftaran' => 'tertutup',
                'status_perbaikan' => 'tertutup'
            ]);
        }
        
        $activePhase = null;
        if ($ruangKontrol->status_pendaftaran === 'terbuka') {
            $activePhase = 'pendaftaran';
        } elseif ($ruangKontrol->status_perbaikan === 'terbuka') {
            $activePhase = 'perbaikan';
        }
        
        return response()->json([
            'success' => true,
            'active_phase' => $activePhase,
            'status_pendaftaran' => $ruangKontrol->status_pendaftaran,
            'status_perbaikan' => $ruangKontrol->status_perbaikan
        ]);
    }

    public function updateRuangKontrol(Request $request)
    {
        // Log request untuk debugging
        \Log::info('Update Ruang Kontrol Request', [
            'data' => $request->all(),
            'user_id' => auth('operator')->id()
        ]);

        $request->validate([
            'id_ruang_kontrol' => 'nullable|exists:ruang_kontrols,id_ruang_kontrol',
            'status_pendaftaran' => 'required|in:terbuka,tertutup',
            'status_perbaikan' => 'required|in:terbuka,tertutup',
            'tanggal_pendaftaran_mulai' => 'required|date',
            'tanggal_pendaftaran_selesai' => 'required|date|after:tanggal_pendaftaran_mulai',
            'tanggal_perbaikan_mulai' => 'required|date',
            'tanggal_perbaikan_selesai' => 'required|date|after:tanggal_perbaikan_mulai',
            'tahun_ajaran' => 'nullable|string',
            'nama_history' => 'nullable|string'
        ]);

        // Implement mutual exclusive logic
        $statusPendaftaran = $request->status_pendaftaran;
        $statusPerbaikan = $request->status_perbaikan;
        
        // If both phases are set to 'terbuka', this is invalid
        if ($statusPendaftaran === 'terbuka' && $statusPerbaikan === 'terbuka') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat membuka kedua fase secara bersamaan. Hanya satu fase yang dapat aktif pada satu waktu.'
            ], 422);
        }

        try {
            // Tentukan tahun akademik dari tanggal fase 1 (menggunakan logika yang sama dengan TahunAjaranHelper)
            $tanggalFase1Mulai = \Carbon\Carbon::parse($request->tanggal_pendaftaran_mulai);
            $tahunFase1 = (int) $tanggalFase1Mulai->format('Y');
            $bulanFase1 = (int) $tanggalFase1Mulai->format('n');
            
            if ($bulanFase1 >= 7) {
                // Juli-Desember: tahun akademik = tahun / tahun+1 (misal: Des 2025 = 2025/2026)
                $tahunAjaran = $tahunFase1 . '/' . ($tahunFase1 + 1);
            } else {
                // Januari-Juni: tahun akademik = tahun-1 / tahun (misal: Jan 2026 = 2025/2026)
                $tahunAjaran = ($tahunFase1 - 1) . '/' . $tahunFase1;
            }
            
            // Cek apakah tahun akademik adalah tahun masa lalu (tidak bisa digunakan)
            if ($this->isTahunAkademikMasaLalu($tahunAjaran)) {
                $tahunAkademikSekarang = TahunAjaranHelper::getTahunAjaranTerbaru();
                return response()->json([
                    'success' => false,
                    'message' => 'Tahun akademik masa lalu tidak dapat digunakan. Hanya tahun akademik sekarang (' . $tahunAkademikSekarang . ') dan tahun akademik depan yang bisa digunakan untuk mengatur fase.'
                ], 422);
            }
            
            // Jika ID jadwal dikirim, update jadwal tersebut
            if ($request->filled('id_ruang_kontrol')) {
                $ruangKontrol = RuangKontrol::findOrFail($request->id_ruang_kontrol);
                
                // Pastikan jadwal ini untuk tahun ajaran yang sama
                if ($ruangKontrol->tahun_ajaran !== $tahunAjaran) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jadwal yang dipilih tidak sesuai dengan tahun ajaran yang dipilih.'
                    ], 422);
                }
                
                // Update jadwal yang sudah ada (hanya status, tidak membuat history)
                $ruangKontrol->update([
                    'status_pendaftaran' => $statusPendaftaran,
                    'status_perbaikan' => $statusPerbaikan,
                    'tanggal_pendaftaran_mulai' => $request->tanggal_pendaftaran_mulai,
                    'tanggal_pendaftaran_selesai' => $request->tanggal_pendaftaran_selesai,
                    'tanggal_perbaikan_mulai' => $request->tanggal_perbaikan_mulai,
                    'tanggal_perbaikan_selesai' => $request->tanggal_perbaikan_selesai,
                    'nama_history' => $request->nama_history ?? $ruangKontrol->nama_history
                ]);
            } else {
                // Jika ID tidak dikirim, cari jadwal aktif untuk tahun ajaran tersebut
            $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaran)
                ->where('is_active', true)
                ->first();
            
            if (!$ruangKontrol) {
                    // Jika tidak ada yang aktif, cari jadwal yang tidak aktif untuk tahun ajaran tersebut
                    $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaran)
                        ->orderBy('created_at', 'desc')
                        ->first();
                }
                
                if (!$ruangKontrol) {
                    // Buat record baru jika benar-benar belum ada jadwal untuk tahun ajaran tersebut
                $ruangKontrol = RuangKontrol::create([
                    'status_pendaftaran' => $statusPendaftaran,
                    'status_perbaikan' => $statusPerbaikan,
                    'tanggal_pendaftaran_mulai' => $request->tanggal_pendaftaran_mulai,
                    'tanggal_pendaftaran_selesai' => $request->tanggal_pendaftaran_selesai,
                    'tanggal_perbaikan_mulai' => $request->tanggal_perbaikan_mulai,
                    'tanggal_perbaikan_selesai' => $request->tanggal_perbaikan_selesai,
                    'tahun_ajaran' => $tahunAjaran,
                    'nama_history' => $request->nama_history ?? 'Jadwal ' . $tahunAjaran,
                    'is_active' => true,
                    'id_pt' => auth('operator')->id()
                ]);
            } else {
                    // Update jadwal yang sudah ada (hanya status, tidak membuat history)
                $ruangKontrol->update([
                    'status_pendaftaran' => $statusPendaftaran,
                    'status_perbaikan' => $statusPerbaikan,
                    'tanggal_pendaftaran_mulai' => $request->tanggal_pendaftaran_mulai,
                    'tanggal_pendaftaran_selesai' => $request->tanggal_pendaftaran_selesai,
                    'tanggal_perbaikan_mulai' => $request->tanggal_perbaikan_mulai,
                    'tanggal_perbaikan_selesai' => $request->tanggal_perbaikan_selesai,
                        'nama_history' => $request->nama_history ?? $ruangKontrol->nama_history,
                        'is_active' => true // Pastikan jadwal aktif
                ]);
                }
            }
            
            // Log success
            \Log::info('Ruang Kontrol Updated Successfully', [
                'ruang_kontrol_id' => $ruangKontrol->id_ruang_kontrol,
                'status_pendaftaran' => $ruangKontrol->status_pendaftaran,
                'status_perbaikan' => $ruangKontrol->status_perbaikan,
                'mutual_exclusive_applied' => true
            ]);
            
            // Determine which phase is active for response message
            $activePhase = '';
            if ($statusPendaftaran === 'terbuka') {
                $activePhase = 'Fase 1 (Pengajuan Proposal)';
            } elseif ($statusPerbaikan === 'terbuka') {
                $activePhase = 'Fase 2 (Perbaikan Proposal)';
            } else {
                $activePhase = 'Tidak ada fase aktif';
            }
            
            return response()->json([
                'success' => true,
                'message' => "Pengaturan ruang kontrol berhasil diperbarui. Status aktif: {$activePhase}",
                'data' => [
                    'status_pendaftaran' => $statusPendaftaran,
                    'status_perbaikan' => $statusPerbaikan,
                    'active_phase' => $activePhase
                ]
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

    /**
     * Create new schedule for future year
     */
    public function createJadwal(Request $request)
    {
        try {
            // Log request untuk debugging
            \Log::info('Create Jadwal Request', [
                'data' => $request->all(),
                'user_id' => auth('operator')->id()
            ]);

            // Validasi dengan custom messages (tahun_ajaran dari form adalah referensi saja)
            // Tahun akademik yang sebenarnya akan di-determine dari tanggal fase 1
            $validated = $request->validate([
                'tahun_ajaran' => 'nullable|string', // Optional, hanya referensi untuk naming
                'nama_history' => 'required|string|max:255',
                'tanggal_pendaftaran_mulai' => 'required|date',
                'tanggal_pendaftaran_selesai' => 'required|date|after:tanggal_pendaftaran_mulai',
                'tanggal_perbaikan_mulai' => 'required|date',
                'tanggal_perbaikan_selesai' => 'required|date|after:tanggal_perbaikan_mulai',
            ], [
                // 'tahun_ajaran' tidak ada required message karena optional
                'nama_history.required' => 'Nama jadwal wajib diisi.',
                'nama_history.max' => 'Nama jadwal maksimal 255 karakter.',
                'tanggal_pendaftaran_mulai.required' => 'Tanggal mulai pendaftaran wajib diisi.',
                'tanggal_pendaftaran_mulai.date' => 'Format tanggal mulai pendaftaran tidak valid.',
                'tanggal_pendaftaran_selesai.required' => 'Tanggal selesai pendaftaran wajib diisi.',
                'tanggal_pendaftaran_selesai.date' => 'Format tanggal selesai pendaftaran tidak valid.',
                'tanggal_pendaftaran_selesai.after' => 'Tanggal selesai pendaftaran harus setelah tanggal mulai.',
                'tanggal_perbaikan_mulai.required' => 'Tanggal mulai perbaikan wajib diisi.',
                'tanggal_perbaikan_mulai.date' => 'Format tanggal mulai perbaikan tidak valid.',
                'tanggal_perbaikan_selesai.required' => 'Tanggal selesai perbaikan wajib diisi.',
                'tanggal_perbaikan_selesai.date' => 'Format tanggal selesai perbaikan tidak valid.',
                'tanggal_perbaikan_selesai.after' => 'Tanggal selesai perbaikan harus setelah tanggal mulai.',
            ]);

            // Validasi tambahan: pastikan tanggal perbaikan setelah tanggal pendaftaran selesai
            if ($request->tanggal_perbaikan_mulai && $request->tanggal_pendaftaran_selesai) {
                if (strtotime($request->tanggal_perbaikan_mulai) < strtotime($request->tanggal_pendaftaran_selesai)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tanggal mulai perbaikan harus setelah tanggal selesai pendaftaran.'
                    ], 422);
                }
            }

            // Determine tahun akademik dari tanggal mulai fase 1
            // Menggunakan logika yang sama dengan TahunAjaranHelper:
            // - Jika bulan Juli-Desember: tahun akademik = tahun / tahun+1
            // - Jika bulan Januari-Juni: tahun akademik = tahun-1 / tahun
            $tanggalFase1Mulai = \Carbon\Carbon::parse($request->tanggal_pendaftaran_mulai);
            $tahunFase1 = (int) $tanggalFase1Mulai->format('Y');
            $bulanFase1 = (int) $tanggalFase1Mulai->format('n');
            
            if ($bulanFase1 >= 7) {
                // Juli-Desember: tahun akademik = tahun / tahun+1 (misal: Des 2025 = 2025/2026)
                $tahunAjaranFormatted = $tahunFase1 . '/' . ($tahunFase1 + 1);
            } else {
                // Januari-Juni: tahun akademik = tahun-1 / tahun (misal: Jan 2026 = 2025/2026)
                $tahunAjaranFormatted = ($tahunFase1 - 1) . '/' . $tahunFase1;
            }
            
            // Validasi: tahun akademik harus >= tahun akademik sekarang
            $tahunAjaranSekarang = TahunAjaranHelper::getTahunAjaranTerbaru();
            $tahunPertamaSekarang = (int) explode('/', $tahunAjaranSekarang)[0];
            
            if ($tahunFase1 < $tahunPertamaSekarang || 
                ($bulanFase1 < 7 && ($tahunFase1 - 1) < $tahunPertamaSekarang)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tahun akademik dari fase pertama tidak boleh lebih kecil dari tahun akademik sekarang (' . $tahunAjaranSekarang . ').'
                ], 422);
            }
            
            // Cek apakah sudah ada jadwal dengan nama yang sama untuk tahun akademik yang sama
            $existing = RuangKontrol::where('tahun_ajaran', $tahunAjaranFormatted)
                ->where('nama_history', $request->nama_history)
                ->first();
            
            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal dengan nama yang sama sudah ada untuk tahun akademik ' . $tahunAjaranFormatted . '.'
                ], 422);
            }

            // Get operator ID
            $operatorId = auth('operator')->id();
            if (!$operatorId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi Anda telah berakhir. Silakan login ulang.'
                ], 401);
            }

            $ruangKontrol = RuangKontrol::create([
                'status_pendaftaran' => 'tertutup',
                'status_perbaikan' => 'tertutup',
                'tanggal_pendaftaran_mulai' => $request->tanggal_pendaftaran_mulai,
                'tanggal_pendaftaran_selesai' => $request->tanggal_pendaftaran_selesai,
                'tanggal_perbaikan_mulai' => $request->tanggal_perbaikan_mulai,
                'tanggal_perbaikan_selesai' => $request->tanggal_perbaikan_selesai,
                'tahun_ajaran' => $tahunAjaranFormatted, // Gunakan tahun yang di-determine dari fase 1
                'nama_history' => $request->nama_history,
                'is_active' => false,
                'id_pt' => $operatorId
            ]);

            \Log::info('Jadwal berhasil dibuat', [
                'ruang_kontrol_id' => $ruangKontrol->id_ruang_kontrol,
                'tahun_ajaran' => $ruangKontrol->tahun_ajaran,
                'nama_history' => $ruangKontrol->nama_history
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dibuat.',
                'data' => $ruangKontrol
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            $errors = $e->validator->errors()->all();
            return response()->json([
                'success' => false,
                'message' => implode(' ', $errors)
            ], 422);
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error creating jadwal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get schedule by ID
     */
    public function getJadwal($id)
    {
        try {
            $ruangKontrol = RuangKontrol::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $ruangKontrol
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update schedule (only if not expired based on tahun ajaran)
     */
    public function updateJadwal(Request $request, $id)
    {
        $ruangKontrol = RuangKontrol::findOrFail($id);
        
        // Cek apakah tahun ajaran adalah tahun ajaran yang sudah lewat (tidak bisa diupdate)
        $tahunAjaranTerbaru = TahunAjaranHelper::getTahunAjaranTerbaru();
        $tahunAjaranJadwal = $ruangKontrol->tahun_ajaran;
        
        // Extract tahun pertama dari tahun ajaran untuk perbandingan
        $tahunPertamaJadwal = $this->extractYearFromTahunAjaran($tahunAjaranJadwal);
        $tahunPertamaTerbaru = $this->extractYearFromTahunAjaran($tahunAjaranTerbaru);
        
        // Jika tahun ajaran jadwal < tahun ajaran terbaru, berarti sudah kedaluwarsa
        if ($tahunPertamaJadwal < $tahunPertamaTerbaru) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal dengan tahun ajaran yang sudah lewat tidak dapat diupdate. Hanya jadwal tahun ajaran sekarang (' . $tahunAjaranTerbaru . ') dan tahun ajaran depan yang bisa diupdate.'
            ], 422);
        }

        $request->validate([
            'nama_history' => 'required|string|max:255',
            'tanggal_pendaftaran_mulai' => 'required|date',
            'tanggal_pendaftaran_selesai' => 'required|date|after:tanggal_pendaftaran_mulai',
            'tanggal_perbaikan_mulai' => 'required|date',
            'tanggal_perbaikan_selesai' => 'required|date|after:tanggal_perbaikan_mulai',
        ]);

        try {
            $ruangKontrol->update([
                'tanggal_pendaftaran_mulai' => $request->tanggal_pendaftaran_mulai,
                'tanggal_pendaftaran_selesai' => $request->tanggal_pendaftaran_selesai,
                'tanggal_perbaikan_mulai' => $request->tanggal_perbaikan_mulai,
                'tanggal_perbaikan_selesai' => $request->tanggal_perbaikan_selesai,
                'nama_history' => $request->nama_history,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diperbarui.',
                'data' => $ruangKontrol
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete schedule (only if not expired based on tahun ajaran)
     */
    public function deleteJadwal($id)
    {
        $ruangKontrol = RuangKontrol::findOrFail($id);
        
        // Cek apakah tahun ajaran adalah tahun ajaran yang sudah lewat (tidak bisa dihapus)
        $tahunAjaranTerbaru = TahunAjaranHelper::getTahunAjaranTerbaru();
        $tahunAjaranJadwal = $ruangKontrol->tahun_ajaran;
        
        // Extract tahun pertama dari tahun ajaran untuk perbandingan
        $tahunPertamaJadwal = $this->extractYearFromTahunAjaran($tahunAjaranJadwal);
        $tahunPertamaTerbaru = $this->extractYearFromTahunAjaran($tahunAjaranTerbaru);
        
        // Jika tahun ajaran jadwal < tahun ajaran terbaru, berarti sudah kedaluwarsa dan tidak bisa dihapus
        if ($tahunPertamaJadwal < $tahunPertamaTerbaru) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal dengan tahun ajaran yang sudah lewat tidak dapat dihapus. Jadwal ini hanya untuk melihat history.'
            ], 422);
        }
        
        // Jika tahun ajaran sama dengan tahun terbaru, bisa dihapus (termasuk yang aktif)
        // Ini memungkinkan operator menghapus jadwal yang salah input di tahun sekarang

        try {
            $ruangKontrol->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate schedule for a year
     */
    public function activateJadwal($id)
    {
        try {
            $ruangKontrol = RuangKontrol::findOrFail($id);
            
            // Cek apakah tahun ajaran adalah tahun masa lalu (tidak bisa diaktifkan)
            // Support format "2025" dan "2025/2026"
            $tahunSekarang = (int) date('Y');
            $tahunAjaran = $this->extractYearFromTahunAjaran($ruangKontrol->tahun_ajaran);
            
            if ($tahunAjaran < $tahunSekarang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tahun masa lalu tidak dapat diaktifkan. Hanya jadwal tahun sekarang (' . $tahunSekarang . ') dan tahun depan yang bisa diaktifkan.'
                ], 422);
            }
            
            // Set semua jadwal untuk tahun yang sama menjadi tidak aktif
            RuangKontrol::where('tahun_ajaran', $ruangKontrol->tahun_ajaran)
                ->update(['is_active' => false]);
            
            // Set jadwal yang dipilih sebagai aktif
            $ruangKontrol->update(['is_active' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diaktifkan.',
                'data' => $ruangKontrol
            ]);
        } catch (\Exception $e) {
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
            'semuaAnggotaTim', 
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

        try {
            // Validasi dengan pesan error yang jelas
            $validated = $request->validate([
            'proposal_id' => 'required|exists:proposals,id_proposal',
            'status_final' => 'required|in:lolos,tidak_lolos',
            'catatan_final' => 'nullable|string',
                'nilai' => 'required|numeric|min:0|max:100',
                'skor' => 'required|array|min:1',
                'skor.*' => 'required|numeric|min:0|max:10',
                'dana_yang_dapat_diberikan' => 'nullable|numeric|min:0'
            ], [
                'proposal_id.required' => 'ID proposal wajib diisi',
                'proposal_id.exists' => 'Proposal tidak ditemukan',
                'status_final.required' => 'Status final wajib dipilih',
                'status_final.in' => 'Status final harus Lolos atau Tidak Lolos',
                'nilai.required' => 'Nilai final wajib diisi',
                'nilai.numeric' => 'Nilai final harus berupa angka',
                'nilai.min' => 'Nilai final minimal 0',
                'nilai.max' => 'Nilai final maksimal 100',
                'skor.required' => 'Skor penilaian wajib diisi',
                'skor.array' => 'Skor penilaian harus berupa array',
                'skor.min' => 'Minimal ada 1 skor penilaian',
                'skor.*.required' => 'Semua skor penilaian wajib diisi',
                'skor.*.numeric' => 'Skor penilaian harus berupa angka',
                'skor.*.min' => 'Skor penilaian minimal 0',
                'skor.*.max' => 'Skor penilaian maksimal 10',
                'dana_yang_dapat_diberikan.numeric' => 'Dana yang dapat diberikan harus berupa angka',
                'dana_yang_dapat_diberikan.min' => 'Dana yang dapat diberikan minimal 0'
            ]);

            DB::beginTransaction();
            
            $proposal = Proposal::findOrFail($request->proposal_id);
            
            // Ambil kriteria untuk validasi jumlah skor
            $criteria = \App\Helpers\ProposalHelper::getSubstantifCriteria($proposal->skim);
            
            // Hitung jumlah kriteria yang sebenarnya (hanya yang bisa di-score, bukan header)
            $actualCriteriaCount = \App\Helpers\ProposalHelper::countActualCriteria($criteria);
            
            // Ambil skor per kriteria
            $skorPerKriteria = $request->input('skor', []);
            
            // Jika skor dikirim sebagai JSON string, decode terlebih dahulu
            if (is_string($skorPerKriteria)) {
                $skorPerKriteria = json_decode($skorPerKriteria, true) ?? [];
            }
            
            // Normalize skor: convert string keys to integers and sort
            $normalizedSkor = [];
            foreach ($skorPerKriteria as $key => $value) {
                $index = (int) $key;
                $normalizedSkor[$index] = (float) $value;
            }
            ksort($normalizedSkor);
            
            // Validasi jumlah skor harus sesuai dengan jumlah kriteria yang bisa di-score
            if (count($normalizedSkor) !== $actualCriteriaCount) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah skor tidak sesuai dengan jumlah kriteria penilaian. Diharapkan: ' . $actualCriteriaCount . ', Diterima: ' . count($normalizedSkor) . '. Pastikan semua kriteria penilaian telah diisi.'
                ], 422);
            }
            
            // Validasi catatan final minimal 50 karakter jika diisi
            if ($request->filled('catatan_final') && strlen(trim($request->catatan_final)) < 50) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Catatan final minimal 50 karakter. Teks yang Anda berikan di catatan kurang dari 50 karakter.'
                ], 422);
            }
            
            // Update status proposal
            $proposal->update(['status' => $request->status_final]);
            
            // Cek apakah model HasilFinal masih mendukung field status_final dan dana_yang_dapat_diberikan
            // Jika tidak, kita perlu menggunakan model yang berbeda atau menyesuaikan
            try {
            // Update atau buat hasil final
                // Catatan: Model HasilFinal sekarang untuk Pimpinan PT, jadi mungkin perlu menggunakan model lain
                // Tapi untuk sementara kita coba dulu dengan model HasilFinal
            HasilFinal::updateOrCreate(
                ['id_proposal' => $request->proposal_id],
                [
                    'status_final' => $request->status_final,
                    'catatan_final' => $request->catatan_final,
                    'nilai' => $request->nilai,
                        'skor_per_kriteria' => $normalizedSkor,
                        'dana_yang_dapat_diberikan' => $request->input('dana_yang_dapat_diberikan', 0),
                    'id_pt' => auth('operator')->id()
                ]
            );
            } catch (\Exception $modelError) {
                DB::rollBack();
                \Log::error('Error saving HasilFinal model', [
                    'error' => $modelError->getMessage(),
                    'trace' => $modelError->getTraceAsString(),
                    'proposal_id' => $request->proposal_id
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data hasil final. Error: ' . $modelError->getMessage() . '. Silakan hubungi administrator.'
                ], 500);
            }
            
            // Kirim notifikasi ke mahasiswa dan dosen (jika service tersedia)
            try {
            $notificationService = new NotificationService();
            $notificationService->notifyHasilFinal(
                $proposal,
                $request->status_final,
                $request->nilai,
                $request->catatan_final
            );
            } catch (\Exception $notifError) {
                // Log error notifikasi tapi jangan gagalkan proses
                \Log::warning('Error sending notification', [
                    'error' => $notifError->getMessage(),
                    'proposal_id' => $request->proposal_id
                ]);
            }
            
            DB::commit();
            
            // Clear cache for this proposal
            $cacheKey = "operator_detail_hasil_final_{$request->proposal_id}";
            Cache::forget($cacheKey);
            
            \Log::info('Hasil final updated successfully', [
                'proposal_id' => $request->proposal_id,
                'status_final' => $request->status_final,
                'nilai' => $request->nilai,
                'skor_count' => count($normalizedSkor)
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Hasil final berhasil diperbarui'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            $errors = $e->errors();
            $firstError = collect($errors)->flatten()->first();
            
            \Log::warning('Validation error in updateHasilFinal', [
                'errors' => $errors,
                'proposal_id' => $request->proposal_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $firstError ?? 'Validasi gagal. Silakan periksa kembali data yang diinput.',
                'errors' => $errors
            ], 422);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error updating hasil final', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'proposal_id' => $request->proposal_id,
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage() . '. Silakan hubungi administrator jika masalah berlanjut.'
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

    /**
     * Menampilkan PDF revisi secara langsung untuk iframe
     */
    public function viewRevisi($id)
    {
        try {
            $revisi = ProposalRevisi::findOrFail($id);
            
            $pathFile = $revisi->path_file;
            
            // Cek apakah path_file sudah termasuk 'public/' atau tidak
            if (strpos($pathFile, 'public/') === 0) {
                $path = storage_path('app/' . $pathFile);
            } else {
                $path = storage_path('app/public/' . $pathFile);
            }
            
            \Log::info('View PDF Revisi (Operator)', [
                'revisi_id' => $id,
                'path_file' => $pathFile,
                'full_path' => $path,
                'file_exists' => file_exists($path)
            ]);
            
            if (!file_exists($path)) {
                $altPath = storage_path('app/' . $pathFile);
                if (file_exists($altPath)) {
                    $path = $altPath;
                } else {
                    abort(404, 'File revisi tidak ditemukan: ' . $path);
                }
            }
            
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $revisi->nama_file . '"'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in viewRevisi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Terjadi kesalahan saat memuat PDF revisi: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan PDF proposal secara langsung untuk iframe
     */
    public function viewPdf($id)
    {
        try {
            $proposal = Proposal::with('dokumen')->findOrFail($id);

            if (!$proposal->dokumen || !$proposal->dokumen->path_file) {
                abort(404, 'Dokumen tidak ditemukan.');
            }

            $path = storage_path('app/public/' . $proposal->dokumen->path_file);
            
            if (!file_exists($path)) {
                abort(404, 'File tidak ditemukan: ' . $path);
            }

            // Return PDF dengan content-type yang tepat untuk iframe
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in viewPdf: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat memuat PDF: ' . $e->getMessage());
        }
    }

    public function detailHasilFinal($id)
    {
        // Use caching to improve performance
        $cacheKey = "operator_detail_hasil_final_{$id}";
        
        $proposal = Cache::remember($cacheKey, 300, function() use ($id) { // Cache for 5 minutes
            return Proposal::with([
                'mahasiswa', 
                'dosen', 
                'semuaAnggotaTim', 
                'dokumen', 
                'hasilFinal',
                'nilaiSubstantif.reviewer',
                'proposalRevisi' => function($query) {
                    $query->orderBy('tanggal_submit', 'desc');
                }
            ])->findOrFail($id);
        });

        // Ambil kriteria penilaian substantif berdasarkan skim proposal
        $criteria = \App\Helpers\ProposalHelper::getSubstantifCriteria($proposal->skim);
        
        // Ambil nilai substantif dari 2 reviewer
        $nilaiSubstantif1 = null;
        $nilaiSubstantif2 = null;
        
        if ($proposal->id_reviewer_substantif_1) {
            $nilaiSubstantif1 = $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_1)->first();
        }
        
        if ($proposal->id_reviewer_substantif_2) {
            $nilaiSubstantif2 = $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_2)->first();
        }

        return view('operator.detail_hasil_final', compact('proposal', 'criteria', 'nilaiSubstantif1', 'nilaiSubstantif2'));
    }

    /**
     * Hasil Semi Final - List proposal yang sudah selesai review substantif
     */
    public function hasilSemiFinal()
    {
        $tahun = request('tahun', '2025');
        $filter = request('filter', 'all');
        $statusRevisi = request('status_revisi', 'all');
        
        // Ambil proposal yang sudah selesai review substantif (status "revisi" atau sudah direvisi)
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif', 'hasilSemiFinal', 'proposalRevisi'])
            ->whereIn('status', ['revisi', 'hasil_semi_final'])
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
                }
            })
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();
        
        return view('operator.hasil_semi_final', compact('proposals', 'tahun', 'filter', 'statusRevisi'));
    }

    /**
     * Detail Hasil Semi Final
     */
    public function detailHasilSemiFinal($id)
    {
        $cacheKey = "operator_detail_hasil_semi_final_{$id}";
        
        $proposal = Cache::remember($cacheKey, 300, function() use ($id) {
            return Proposal::with([
                'mahasiswa', 
                'dosen', 
                'semuaAnggotaTim', 
                'dokumen', 
                'hasilSemiFinal',
                'nilaiSubstantif.reviewer',
                'proposalRevisi' => function($query) {
                    $query->orderBy('tanggal_submit', 'desc');
                }
            ])->findOrFail($id);
        });

        // Ambil kriteria penilaian substantif berdasarkan skim proposal
        $criteria = \App\Helpers\ProposalHelper::getSubstantifCriteria($proposal->skim);
        
        // Ambil nilai substantif dari 2 reviewer
        $nilaiSubstantif1 = null;
        $nilaiSubstantif2 = null;
        
        if ($proposal->id_reviewer_substantif_1) {
            $nilaiSubstantif1 = $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_1)->first();
        }
        
        if ($proposal->id_reviewer_substantif_2) {
            $nilaiSubstantif2 = $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_2)->first();
        }

        // Ambil semua dosen untuk dropdown pilih dosen universitas
        $dosens = Dosen::where('is_active', true)->orderBy('nama_dosen')->get();

        return view('operator.detail_hasil_semi_final', compact('proposal', 'criteria', 'nilaiSubstantif1', 'nilaiSubstantif2', 'dosens'));
    }

    /**
     * Update Hasil Semi Final
     */
    public function updateHasilSemiFinal(Request $request)
    {
        \Log::info('UpdateHasilSemiFinal called', [
            'request_data' => $request->all(),
            'user_id' => auth('operator')->id()
        ]);

        $request->validate([
            'proposal_id' => 'required|exists:proposals,id_proposal',
            'status_final' => 'required|in:lolos_tingkat_universitas,tidak_lolos_tingkat_universitas',
            'catatan_final' => 'nullable|string',
            'nilai' => 'required|numeric|min:0|max:100',
            'skor' => 'required|array',
            'skor.*' => 'required|numeric|min:0|max:10',
            'id_dosen_pendamping_universitas' => 'required_if:status_final,lolos_tingkat_universitas|exists:dosens,id_dosen'
        ]);

        try {
            DB::beginTransaction();
            
            $proposal = Proposal::findOrFail($request->proposal_id);
            
            // Ambil kriteria untuk validasi jumlah skor
            $criteria = \App\Helpers\ProposalHelper::getSubstantifCriteria($proposal->skim);
            
            // Hitung jumlah kriteria yang sebenarnya (hanya yang bisa di-score, bukan header)
            $actualCriteriaCount = \App\Helpers\ProposalHelper::countActualCriteria($criteria);
            
            // Ambil skor per kriteria
            $skorPerKriteria = $request->input('skor', []);
            
            // Normalize skor: convert string keys to integers and sort
            $normalizedSkor = [];
            foreach ($skorPerKriteria as $key => $value) {
                $index = (int) $key;
                $normalizedSkor[$index] = (float) $value;
            }
            ksort($normalizedSkor);
            
            // Validasi jumlah skor harus sesuai dengan jumlah kriteria yang bisa di-score
            if (count($normalizedSkor) !== $actualCriteriaCount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah skor tidak sesuai dengan jumlah kriteria penilaian. Diharapkan: ' . $actualCriteriaCount . ', Diterima: ' . count($normalizedSkor)
                ], 422);
            }
            
            // Update proposal
            $updateData = [
                'status' => 'revisi_akhir',
                'status_final' => 'revisi_akhir'
            ];
            
            // Jika lolos tingkat universitas, set dosen pendamping universitas
            if ($request->status_final === 'lolos_tingkat_universitas') {
                $updateData['id_dosen_pendamping_universitas'] = $request->id_dosen_pendamping_universitas;
            }
            
            $proposal->update($updateData);
            
            // Update atau buat hasil semi final
            HasilSemiFinal::updateOrCreate(
                ['id_proposal' => $request->proposal_id],
                [
                    'status_final' => $request->status_final,
                    'catatan_final' => $request->catatan_final,
                    'nilai' => $request->nilai,
                    'skor_per_kriteria' => $normalizedSkor,
                    'id_pt' => auth('operator')->id()
                ]
            );
            
            // Kirim notifikasi ke mahasiswa dan dosen
            $notificationService = new NotificationService();
            // TODO: Buat method notifyHasilSemiFinal jika diperlukan
            
            DB::commit();
            
            // Clear cache for this proposal
            $cacheKey = "operator_detail_hasil_semi_final_{$request->proposal_id}";
            Cache::forget($cacheKey);
            
            \Log::info('Hasil semi final updated successfully', [
                'proposal_id' => $request->proposal_id,
                'status_final' => $request->status_final
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Hasil semi final berhasil diperbarui'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error updating hasil semi final', [
                'error' => $e->getMessage(),
                'proposal_id' => $request->proposal_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Manajemen Akun
    public function manageAccounts(Request $request)
    {
        // Filter untuk Mahasiswa
        $query = Mahasiswa::query();
        
        // Filter berdasarkan Fakultas
        if ($request->filled('filter_fakultas')) {
            $fakultas = Fakultas::find($request->filter_fakultas);
            if ($fakultas) {
                $query->where('fakultas_mhs', $fakultas->nama_fakultas);
            }
        }
        
        // Filter berdasarkan Prodi
        if ($request->filled('filter_prodi')) {
            $prodi = Prodi::find($request->filter_prodi);
            if ($prodi) {
                $query->where('prodi_mhs', $prodi->nama_prodi);
            }
        }
        
        // Filter berdasarkan NIM (search)
        if ($request->filled('filter_nim')) {
            $query->where('nim', 'like', '%' . $request->filter_nim . '%');
        }
        
        $mahasiswas = $query->orderBy('created_at', 'desc')->get();
        
        // Data lainnya tetap sama
        $dosens = Dosen::orderBy('created_at', 'desc')->limit(100)->get();
        $reviewers = Reviewer::orderBy('created_at', 'desc')->limit(100)->get();
        $operators = PT::orderBy('created_at', 'desc')->limit(100)->get();
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        
        return view('operator.manajemen_akun', compact('mahasiswas', 'dosens', 'reviewers', 'operators', 'fakultas', 'prodis'));
    }
    
    // Bulk Delete Mahasiswa
    public function bulkDeleteMahasiswa(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:mahasiswas,id_mahasiswa',
        ]);
        
        try {
            $count = Mahasiswa::whereIn('id_mahasiswa', $request->ids)->delete();
            return redirect()->route('operator.manage.accounts')->with('success', "Berhasil menghapus {$count} akun mahasiswa.");
        } catch (\Exception $e) {
            return redirect()->route('operator.manage.accounts')->with('error', 'Gagal menghapus akun mahasiswa: ' . $e->getMessage());
        }
    }

    public function storeAccount(Request $request, $type)
    {
        switch ($type) {
            case 'mahasiswa':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nim' => 'required|string|max:20|unique:mahasiswas,nim',
                    'email' => 'required|email|max:255|unique:mahasiswas,email_mhs',
                    'no_hp' => 'required|string|max:15',
                    'prodi' => 'required|exists:prodis,id_prodi',
                    'fakultas' => 'required|exists:fakultas,id_fakultas',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                $prodi = Prodi::find($request->prodi);
                $fakultas = Fakultas::find($request->fakultas);
                Mahasiswa::create([
                    'nama_mhs' => $request->nama,
                    'nim' => $request->nim,
                    'email_mhs' => $request->email,
                    'no_hp_mhs' => $request->no_hp,
                    'prodi_mhs' => $prodi->nama_prodi,
                    'fakultas_mhs' => $fakultas->nama_fakultas,
                    'password' => Hash::make($request->password),
                    'is_active' => true,
                ]);
                break;
            case 'dosen':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nuptk' => 'required|string|max:20|unique:dosens,nuptk',
                    'email' => 'required|email|max:255|unique:dosens,email_dosen',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                Dosen::create([
                    'nama_dosen' => $request->nama,
                    'nuptk' => $request->nuptk,
                    'email_dosen' => $request->email,
                    'no_hp_dosen' => $request->no_hp,
                    'password' => Hash::make($request->password),
                    'is_active' => true,
                ]);
                break;
            case 'reviewer':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:reviewers,email_reviewer',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                Reviewer::create([
                    'nama_reviewer' => $request->nama,
                    'email_reviewer' => $request->email,
                    'no_hp_reviewer' => $request->no_hp,
                    'password' => Hash::make($request->password),
                    'role' => 'reviewer',
                    'is_active' => true,
                ]);
                break;
            case 'operator':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:pts,email_pt',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                PT::create([
                    'nama_pt' => $request->nama,
                    'email_pt' => $request->email,
                    'no_hp_pt' => $request->no_hp,
                    'password' => Hash::make($request->password),
                    'role' => 'operator',
                    'is_active' => true,
                ]);
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }
        return redirect()->route('operator.manage.accounts')->with('success', 'Akun berhasil dibuat.');
    }

    public function updateAccount(Request $request, $type, $id)
    {
        switch ($type) {
            case 'mahasiswa':
                $mahasiswa = Mahasiswa::findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nim' => 'required|string|max:20|unique:mahasiswas,nim,' . $mahasiswa->id_mahasiswa . ',id_mahasiswa',
                    'email' => 'required|email|max:255|unique:mahasiswas,email_mhs,' . $mahasiswa->id_mahasiswa . ',id_mahasiswa',
                    'no_hp' => 'required|string|max:15',
                    'prodi' => 'nullable|exists:prodis,id_prodi',
                    'fakultas' => 'nullable|exists:fakultas,id_fakultas',
                    'password' => 'nullable|string|min:8|confirmed',
                    'is_active' => 'nullable|boolean',
                ]);
                $mahasiswa->nama_mhs = $request->nama;
                $mahasiswa->nim = $request->nim;
                $mahasiswa->email_mhs = $request->email;
                $mahasiswa->no_hp_mhs = $request->no_hp;
                if ($request->filled('prodi')) { $mahasiswa->prodi_mhs = Prodi::find($request->prodi)->nama_prodi; }
                if ($request->filled('fakultas')) { $mahasiswa->fakultas_mhs = Fakultas::find($request->fakultas)->nama_fakultas; }
                if ($request->filled('password')) { $mahasiswa->password = Hash::make($request->password); }
                if ($request->has('is_active')) { $mahasiswa->is_active = (bool)$request->is_active; }
                $mahasiswa->save();
                break;
            case 'dosen':
                $dosen = Dosen::findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nuptk' => 'required|string|max:20|unique:dosens,nuptk,' . $dosen->id_dosen . ',id_dosen',
                    'email' => 'required|email|max:255|unique:dosens,email_dosen,' . $dosen->id_dosen . ',id_dosen',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'nullable|string|min:8|confirmed',
                    'is_active' => 'nullable|boolean',
                ]);
                $dosen->nama_dosen = $request->nama;
                $dosen->nuptk = $request->nuptk;
                $dosen->email_dosen = $request->email;
                $dosen->no_hp_dosen = $request->no_hp;
                if ($request->filled('password')) { $dosen->password = Hash::make($request->password); }
                if ($request->has('is_active')) { $dosen->is_active = (bool)$request->is_active; }
                $dosen->save();
                break;
            case 'reviewer':
                $reviewer = Reviewer::findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:reviewers,email_reviewer,' . $reviewer->id_reviewer . ',id_reviewer',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'nullable|string|min:8|confirmed',
                    'is_active' => 'nullable|boolean',
                ]);
                $reviewer->nama_reviewer = $request->nama;
                $reviewer->email_reviewer = $request->email;
                $reviewer->no_hp_reviewer = $request->no_hp;
                if ($request->filled('password')) { $reviewer->password = Hash::make($request->password); }
                if ($request->has('is_active')) { $reviewer->is_active = (bool)$request->is_active; }
                $reviewer->save();
                break;
            case 'operator':
                $operator = PT::findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:pts,email_pt,' . $operator->id_pt . ',id_pt',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'nullable|string|min:8|confirmed',
                    'is_active' => 'nullable|boolean',
                ]);
                $operator->nama_pt = $request->nama;
                $operator->email_pt = $request->email;
                $operator->no_hp_pt = $request->no_hp;
                if ($request->filled('password')) { $operator->password = Hash::make($request->password); }
                if ($request->has('is_active')) { $operator->is_active = (bool)$request->is_active; }
                $operator->save();
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }
        return redirect()->route('operator.manage.accounts')->with('success', 'Akun berhasil diperbarui.');
    }

    public function deleteAccount($type, $id)
    {
        switch ($type) {
            case 'mahasiswa':
                Mahasiswa::where('id_mahasiswa', $id)->delete();
                break;
            case 'dosen':
                Dosen::where('id_dosen', $id)->delete();
                break;
            case 'reviewer':
                Reviewer::where('id_reviewer', $id)->delete();
                break;
            case 'operator':
                PT::where('id_pt', $id)->delete();
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }
        return redirect()->route('operator.manage.accounts')->with('success', 'Akun berhasil dihapus.');
    }

    private function getPKM8BidangData($tahunAjaran)
    {
        $skims = ['RE', 'RSH', 'KC', 'PM', 'PI', 'K', 'KI', 'VGK'];
        
        return collect($skims)->map(function($skim) use ($tahunAjaran) {
            // Total proposal berdasarkan skim
            $total = Proposal::where('skim', $skim)
                ->where('tahun_ajaran', $tahunAjaran)
                ->count();
            
            // Proposal yang sudah divalidasi (status_validasi = 'valid')
            $sudahValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'valid')
                ->where('tahun_ajaran', $tahunAjaran)
                ->count();
            
            // Proposal yang belum divalidasi (status_validasi = 'pending')
            $belumValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'pending')
                ->where('tahun_ajaran', $tahunAjaran)
                ->count();
            
            // Proposal yang ditolak validasi (status_validasi = 'tidak_valid')
            $tolakValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'tidak_valid')
                ->where('tahun_ajaran', $tahunAjaran)
                ->count();
            
            // Proposal yang sedang dalam proses review
            $sedangReview = Proposal::where('skim', $skim)
                ->whereIn('status', ['submitted', 'review_administratif', 'review_substantif', 'revisi'])
                ->where('tahun_ajaran', $tahunAjaran)
                ->count();
            
            // Proposal yang sudah selesai review (lolos/tidak_lolos)
            $selesaiReview = Proposal::where('skim', $skim)
                ->whereIn('status', ['lolos', 'tidak_lolos'])
                ->where('tahun_ajaran', $tahunAjaran)
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

    private function getPKMInsentifData($tahunAjaran)
    {
        $skims = ['AI', 'GFT'];
        
        return collect($skims)->map(function($skim) use ($tahunAjaran) {
            // Total proposal berdasarkan skim
            $total = Proposal::where('skim', $skim)
                ->where('tahun_ajaran', $tahunAjaran)
                ->count();
            
            // Proposal yang sudah divalidasi (status_validasi = 'valid')
            $sudahValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'valid')
                ->where('tahun_ajaran', $tahunAjaran)
                ->count();
            
            // Proposal yang belum divalidasi (status_validasi = 'pending')
            $belumValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'pending')
                ->where('tahun_ajaran', $tahunAjaran)
                ->count();
            
            // Proposal yang ditolak validasi (status_validasi = 'tidak_valid')
            $tolakValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'tidak_valid')
                ->where('tahun_ajaran', $tahunAjaran)
                ->count();
            
            // Proposal yang sedang dalam proses review
            $sedangReview = Proposal::where('skim', $skim)
                ->whereIn('status', ['submitted', 'review_administratif', 'review_substantif', 'revisi'])
                ->where('tahun_ajaran', $tahunAjaran)
                ->count();
            
            // Proposal yang sudah selesai review (lolos/tidak_lolos)
            $selesaiReview = Proposal::where('skim', $skim)
                ->whereIn('status', ['lolos', 'tidak_lolos'])
                ->where('tahun_ajaran', $tahunAjaran)
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

    /**
     * Get chart data for dashboard
     */
    private function getChartData($tahunAjaran)
    {
        // Ambil 3 tahun ajaran terakhir untuk chart
        $tahunAjaranList = Proposal::whereNotNull('tahun_ajaran')
            ->whereRaw("tahun_ajaran LIKE '%/%'")
            ->distinct()
            ->orderByRaw("CAST(SUBSTRING_INDEX(tahun_ajaran, '/', 1) AS UNSIGNED) DESC")
            ->pluck('tahun_ajaran')
            ->filter(function($item) {
                return strpos($item, '/') !== false && count(explode('/', $item)) === 2;
            })
            ->take(3)
            ->values();
        
        // Data proposal per tahun ajaran (3 tahun ajaran terakhir)
        $proposalPerTahun = $tahunAjaranList->map(function($ta) {
            $count = Proposal::where('tahun_ajaran', $ta)->count();
            return [
                'tahun' => $ta,
                'jumlah' => $count
            ];
        })->toArray();
        
        // Data proposal per skim di tahun ajaran terpilih
        $proposalPerSkim = Proposal::where('tahun_ajaran', $tahunAjaran)
            ->selectRaw('skim, COUNT(*) as jumlah')
            ->groupBy('skim')
            ->orderBy('jumlah', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'skim' => $item->skim,
                    'jumlah' => $item->jumlah
                ];
            });
        
        // Data proposal per fakultas di tahun ajaran terpilih
        $proposalPerFakultas = Proposal::where('tahun_ajaran', $tahunAjaran)
            ->join('mahasiswas', 'proposals.id_mahasiswa', '=', 'mahasiswas.id_mahasiswa')
            ->selectRaw('mahasiswas.fakultas_mhs as nama_fakultas, COUNT(*) as jumlah')
            ->groupBy('mahasiswas.fakultas_mhs')
            ->orderBy('jumlah', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'fakultas' => $item->nama_fakultas,
                    'jumlah' => $item->jumlah
                ];
            });
        
        return [
            'proposal_per_tahun' => $proposalPerTahun,
            'proposal_per_skim' => $proposalPerSkim,
            'proposal_per_fakultas' => $proposalPerFakultas
        ];
    }

    /**
     * Get top 10 proposals by nilai
     */
    private function getTopProposals($tahunAjaran)
    {
        return Proposal::with(['mahasiswa', 'hasilFinal'])
            ->where('tahun_ajaran', $tahunAjaran)
            ->whereHas('hasilFinal')
            ->join('hasil_finals', 'proposals.id_proposal', '=', 'hasil_finals.id_proposal')
            ->orderBy('hasil_finals.nilai', 'desc')
            ->limit(10)
            ->get()
            ->map(function($proposal, $index) {
                return [
                    'ranking' => $index + 1,
                    'judul' => $proposal->judul_proposal,
                    'skim' => $proposal->skim,
                    'mahasiswa' => $proposal->mahasiswa->nama_mahasiswa ?? 'N/A',
                    'nilai' => $proposal->hasilFinal->nilai ?? 0,
                    'status' => $proposal->hasilFinal->status_final ?? 'N/A'
                ];
            });
    }
}

