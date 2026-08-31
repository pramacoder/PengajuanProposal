<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\Proposal;
use App\Models\HasilFinal;
use App\Models\HasilSemiFinal;
use App\Models\Dokumen;
use App\Models\NilaiSubstantif;
use App\Models\ProposalRevisi;
use App\Models\RuangKontrol;
use App\Models\FormPenilaian;
use App\Models\SimbelmawaReport;
use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Helpers\ProposalHelper;
use App\Helpers\StorageHelper;
use App\Helpers\TahunAjaranHelper;

class PimpinanPTController extends Controller
{
    /**
     * Dashboard Pimpinan PT - Menampilkan statistik lengkap + proposal yang perlu dinilai final
     */
    public function dashboard(Request $request)
    {
        $pimpinanPT = auth()->user();
        
        if ($pimpinanPT->role !== 'pimpinan_pt') {
            abort(403, 'Akses ditolak. Hanya Pimpinan PT yang dapat mengakses halaman ini.');
        }

        // ============================================================
        // Data Statistik (sama seperti operator dashboard)
        // ============================================================
        $tahunAjaranTerpilih = $request->input('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        
        $tahunAjaranList = Proposal::whereNotNull('tahun_ajaran')
            ->whereRaw("tahun_ajaran LIKE '%/%'")
            ->pluck('tahun_ajaran')
            ->filter(function($item) {
                return strpos($item, '/') !== false && count(explode('/', $item)) === 2;
            })
            ->unique()
            ->sortByDesc(function($item) {
                return (int) explode('/', $item)[0];
            })
            ->values();

        if ($tahunAjaranList->isEmpty()) {
            $tahunAjaranList = collect([TahunAjaranHelper::getTahunAjaranTerbaru()]);
        }

        // Data PKM-8 Bidang
        $skims8 = ['RE', 'RSH', 'KC', 'PM', 'PI', 'K', 'KI', 'VGK'];
        $pkm8Bidang = collect($skims8)->map(function($skim) use ($tahunAjaranTerpilih) {
            return [
                'skim' => $skim,
                'jumlah' => Proposal::where('skim', $skim)->where('tahun_ajaran', $tahunAjaranTerpilih)->count(),
                'sudah_valid' => Proposal::where('skim', $skim)->where('status_validasi', 'valid')->where('tahun_ajaran', $tahunAjaranTerpilih)->count(),
                'belum_valid' => Proposal::where('skim', $skim)->where('status_validasi', 'pending')->where('tahun_ajaran', $tahunAjaranTerpilih)->count(),
                'tolak_valid' => Proposal::where('skim', $skim)->where('status_validasi', 'tidak_valid')->where('tahun_ajaran', $tahunAjaranTerpilih)->count(),
                'sedang_review' => Proposal::where('skim', $skim)->whereIn('status', ['submitted', 'review_administratif', 'review_substantif', 'revisi'])->where('tahun_ajaran', $tahunAjaranTerpilih)->count(),
                'selesai_review' => Proposal::where('skim', $skim)->whereIn('status', ['lolos', 'tidak_lolos'])->where('tahun_ajaran', $tahunAjaranTerpilih)->count(),
            ];
        });

        // Data PKM Insentif
        $skimsInsentif = ['AI', 'GFT'];
        $pkmInsentif = collect($skimsInsentif)->map(function($skim) use ($tahunAjaranTerpilih) {
            return [
                'skim' => $skim,
                'jumlah' => Proposal::where('skim', $skim)->where('tahun_ajaran', $tahunAjaranTerpilih)->count(),
                'sudah_valid' => Proposal::where('skim', $skim)->where('status_validasi', 'valid')->where('tahun_ajaran', $tahunAjaranTerpilih)->count(),
                'belum_valid' => Proposal::where('skim', $skim)->where('status_validasi', 'pending')->where('tahun_ajaran', $tahunAjaranTerpilih)->count(),
                'tolak_valid' => Proposal::where('skim', $skim)->where('status_validasi', 'tidak_valid')->where('tahun_ajaran', $tahunAjaranTerpilih)->count(),
                'sedang_review' => Proposal::where('skim', $skim)->whereIn('status', ['submitted', 'review_administratif', 'review_substantif', 'revisi'])->where('tahun_ajaran', $tahunAjaranTerpilih)->count(),
                'selesai_review' => Proposal::where('skim', $skim)->whereIn('status', ['lolos', 'tidak_lolos'])->where('tahun_ajaran', $tahunAjaranTerpilih)->count(),
            ];
        });

        $totalKeseluruhan = $pkm8Bidang->sum('jumlah');
        $totalInsentif = $pkmInsentif->sum('jumlah');

        // Data Chart
        $tahunAjaranChart = Proposal::whereNotNull('tahun_ajaran')
            ->whereRaw("tahun_ajaran LIKE '%/%'")
            ->pluck('tahun_ajaran')
            ->filter(function($item) {
                return strpos($item, '/') !== false && count(explode('/', $item)) === 2;
            })
            ->unique()->sortByDesc(fn($i) => (int) explode('/', $i)[0])->take(3)->values();

        $chartData = [
            'proposal_per_tahun' => $tahunAjaranChart->map(fn($ta) => ['tahun' => $ta, 'jumlah' => Proposal::where('tahun_ajaran', $ta)->count()])->toArray(),
            'proposal_per_skim' => Proposal::where('tahun_ajaran', $tahunAjaranTerpilih)->selectRaw('skim, COUNT(*) as jumlah')->groupBy('skim')->orderBy('jumlah', 'desc')->get()->map(fn($i) => ['skim' => $i->skim, 'jumlah' => $i->jumlah]),
            'proposal_per_fakultas' => Proposal::where('tahun_ajaran', $tahunAjaranTerpilih)->join('users', 'proposals.id_mahasiswa', '=', 'users.id')->where('users.role', 'mahasiswa')->selectRaw("users.metadata->>'fakultas_name' as nama_fakultas, COUNT(*) as jumlah")->groupByRaw("users.metadata->>'fakultas_name'")->orderBy('jumlah', 'desc')->get()->map(fn($i) => ['fakultas' => $i->nama_fakultas, 'jumlah' => $i->jumlah]),
        ];

        // Top 10 proposal
        $topProposals = Proposal::with(['mahasiswa', 'hasilFinal'])
            ->where('tahun_ajaran', $tahunAjaranTerpilih)
            ->whereHas('hasilFinal')
            ->join('hasil_finals', 'proposals.id_proposal', '=', 'hasil_finals.id_proposal')
            ->orderBy('hasil_finals.nilai', 'desc')
            ->limit(10)
            ->get()
            ->map(function($proposal, $index) {
                return [
                    'ranking' => $index + 1,
                    'judul' => $proposal->judul,
                    'skim' => $proposal->skim,
                    'mahasiswa' => $proposal->mahasiswa->name ?? 'N/A',
                    'nilai' => $proposal->hasilFinal->nilai ?? 0,
                    'status' => $proposal->hasilFinal->status_final ?? 'N/A',
                ];
            });

        // Filtered proposals
        $filteredProposals = collect([]);
        $hasFilter = $request->filled('filter_fakultas') || $request->filled('filter_prodi') || $request->filled('filter_skim') || $request->filled('filter_status');
        if ($hasFilter) {
            $query = Proposal::with(['mahasiswa', 'hasilFinal'])->where('tahun_ajaran', $tahunAjaranTerpilih);
            if ($request->filled('filter_fakultas')) {
                $fak = Fakultas::find($request->filter_fakultas);
                if ($fak) $query->whereHas('mahasiswa', fn($q) => $q->whereRaw("metadata->>'fakultas_name' = ?", [$fak->nama_fakultas]));
            }
            if ($request->filled('filter_prodi')) {
                $pr = Prodi::find($request->filter_prodi);
                if ($pr) $query->whereHas('mahasiswa', fn($q) => $q->whereRaw("metadata->>'prodi_name' = ?", [$pr->nama_prodi]));
            }
            if ($request->filled('filter_skim')) $query->where('skim', $request->filter_skim);
            if ($request->filled('filter_status')) {
                if ($request->filter_status === 'lolos') $query->whereHas('hasilFinal', fn($q) => $q->where('status_final', 'lolos'));
                elseif ($request->filter_status === 'tidak_lolos') $query->whereHas('hasilFinal', fn($q) => $q->where('status_final', 'tidak_lolos'));
                elseif ($request->filter_status === 'belum_final') $query->whereDoesntHave('hasilFinal');
            }
            if (!$request->has('show_all')) $query->limit(20);
            $filteredProposals = $query->orderBy('tanggal_pengajuan', 'desc')->get();
        }

        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        $skims = Proposal::where('tahun_ajaran', $tahunAjaranTerpilih)->distinct()->pluck('skim')->filter()->sort()->values();

        // ============================================================
        // Data khusus Pimpinan PT - Proposal perlu dinilai final
        // ============================================================
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'hasilFinal', 'hasilSemiFinal'])
            ->where('status', 'pimpinan_pt')
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        $proposalsBelumDinilai = $proposals->filter(fn($p) => !$p->hasilFinal);
        $proposalsSudahDinilai = $proposals->filter(fn($p) => $p->hasilFinal);

        return view('pimpinan_pt.dashboard', compact(
            'pimpinanPT',
            'pkm8Bidang', 'pkmInsentif', 'totalKeseluruhan', 'totalInsentif',
            'tahunAjaranTerpilih', 'tahunAjaranList',
            'chartData', 'topProposals',
            'filteredProposals', 'fakultas', 'prodis', 'skims',
            'proposals', 'proposalsBelumDinilai', 'proposalsSudahDinilai'
        ));
    }

    /**
     * Detail Hasil Final untuk Pimpinan PT
     */
    public function detailHasilFinal($id)
    {
        $pimpinanPT = auth()->user();
        
        if ($pimpinanPT->role !== 'pimpinan_pt') {
            abort(403, 'Akses ditolak. Hanya Pimpinan PT yang dapat mengakses halaman ini.');
        }

        $proposal = Proposal::with([
            'mahasiswa',
            'dokumen',
            'hasilFinal',
            'hasilSemiFinal',
            'nilaiSubstantif.reviewer',
            'proposalRevisi' => function($query) {
                if (Schema::hasColumn('proposal_revisi', 'jenis_revisi')) {
                    $query->orderByRaw("CASE WHEN jenis_revisi = 'revisi_akhir' THEN 0 ELSE 1 END")
                          ->orderBy('tanggal_submit', 'desc');
                } else {
                    $query->orderByRaw("CASE WHEN path_file LIKE '%revisi_akhir%' THEN 0 ELSE 1 END")
                          ->orderBy('tanggal_submit', 'desc');
                }
            }
        ])
            ->where(function($query) use ($pimpinanPT) {
                $query->where('status', 'pimpinan_pt')
                      ->orWhereHas('hasilFinal', function($q) use ($pimpinanPT) {
                          $q->where('id_pimpinan_pt', $pimpinanPT->id);
                      });
            })
            ->findOrFail($id);

        $criteria = ProposalHelper::getSubstantifCriteria($proposal->skim);
        
        $nilaiSubstantif1 = null;
        $nilaiSubstantif2 = null;
        
        if ($proposal->id_reviewer_substantif_1) {
            $nilaiSubstantif1 = $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_1)->first();
        }
        
        if ($proposal->id_reviewer_substantif_2) {
            $nilaiSubstantif2 = $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_2)->first();
        }

        $latestProposals = Proposal::with(['dokumen', 'hasilFinal'])
            ->where('id_mahasiswa', $proposal->id_mahasiswa)
            ->where('id_proposal', '!=', $proposal->id_proposal)
            ->orderBy('tanggal_pengajuan', 'desc')
            ->limit(5)
            ->get();

        return view('pimpinan_pt.detail_hasil_final', compact('proposal', 'criteria', 'nilaiSubstantif1', 'nilaiSubstantif2', 'pimpinanPT', 'latestProposals'));
    }

    /**
     * Update Hasil Final oleh Pimpinan PT
     */
    public function updateHasilFinal(Request $request)
    {
        Log::info('PimpinanPT updateHasilFinal called', [
            'request_data' => $request->all(),
            'user_id' => auth()->id()
        ]);

        $request->merge([
            'dana_didapatkan_belmawa' => \App\Helpers\ProposalHelper::parseAngka($request->input('dana_didapatkan_belmawa')),
            'dana_didapatkan_operator' => \App\Helpers\ProposalHelper::parseAngka($request->input('dana_didapatkan_operator')),
        ]);

        $pimpinanPT = auth()->user();
        
        if ($pimpinanPT->role !== 'pimpinan_pt') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya Pimpinan PT yang dapat mengakses halaman ini.'
            ], 403);
        }

        $request->validate([
            'proposal_id' => 'required|exists:proposals,id_proposal',
            'status_pimnas' => 'required|in:lolos,tidak_lolos',
            'status_pendanaan' => 'required|in:lolos,tidak_lolos',
            'dana_didapatkan_belmawa' => 'nullable|numeric|min:0|max:15000000',
            'dana_didapatkan_operator' => 'nullable|numeric|min:0|max:15000000',
            'catatan_final' => 'nullable|string',
            'nilai' => 'required|numeric|min:0|max:100',
            'skor' => 'required|array',
            'skor.*' => 'required|numeric|min:0|max:10',
        ]);

        try {
            DB::beginTransaction();
            
            $proposal = Proposal::findOrFail($request->proposal_id);
            
            if ($proposal->status !== 'pimpinan_pt') {
                return response()->json([
                    'success' => false,
                    'message' => 'Proposal belum siap untuk penilaian final.'
                ], 400);
            }
            
            $criteria = ProposalHelper::getSubstantifCriteria($proposal->skim);
            $actualCriteriaCount = ProposalHelper::countActualCriteria($criteria);
            
            $skorPerKriteria = $request->input('skor', []);
            
            if (is_string($skorPerKriteria)) {
                $skorPerKriteria = json_decode($skorPerKriteria, true) ?? [];
            }
            
            $normalizedSkor = [];
            foreach ($skorPerKriteria as $key => $value) {
                $index = (int) $key;
                $normalizedSkor[$index] = (float) $value;
            }
            ksort($normalizedSkor);
            
            if (count($normalizedSkor) !== $actualCriteriaCount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah skor tidak sesuai dengan jumlah kriteria penilaian. Diharapkan: ' . $actualCriteriaCount . ', Diterima: ' . count($normalizedSkor)
                ], 422);
            }
            
            $statusProposal = 'lolos';
            if ($request->status_pimnas === 'tidak_lolos' || $request->status_pendanaan === 'tidak_lolos') {
                $statusProposal = 'tidak_lolos';
            }
            
            $statusFinal = 'lolos';
            if ($request->status_pimnas === 'lolos' && $request->status_pendanaan === 'lolos') {
                $statusFinal = 'lolos_pimnas_pendanaan';
            } elseif ($request->status_pimnas === 'lolos' && $request->status_pendanaan === 'tidak_lolos') {
                $statusFinal = 'lolos_pimnas_tidak_pendanaan';
            } elseif ($request->status_pimnas === 'tidak_lolos' && $request->status_pendanaan === 'lolos') {
                $statusFinal = 'tidak_lolos_pimnas_lolos_pendanaan';
            } else {
                $statusFinal = 'tidak_lolos';
            }
            
            $proposal->update([
                'status' => $statusProposal,
                'status_final' => $statusFinal
            ]);
            
            $danaDidapatkanBelmawa = 0;
            $danaDidapatkanOperator = 0;
            if ($request->status_pendanaan === 'lolos') {
                $danaInputBelmawa = $request->input('dana_didapatkan_belmawa', 0);
                if (is_string($danaInputBelmawa)) {
                    $danaInputBelmawa = preg_replace('/[^0-9.]/', '', $danaInputBelmawa);
                }
                $danaDidapatkanBelmawa = max(0, min((float) $danaInputBelmawa, 15000000));
                
                $danaInputOperator = $request->input('dana_didapatkan_operator', 0);
                if (is_string($danaInputOperator)) {
                    $danaInputOperator = preg_replace('/[^0-9.]/', '', $danaInputOperator);
                }
                $danaDidapatkanOperator = max(0, min((float) $danaInputOperator, 15000000));
            }
            
            HasilFinal::updateOrCreate(
                ['id_proposal' => $request->proposal_id],
                [
                    'status_pimnas' => $request->status_pimnas,
                    'status_pendanaan' => $request->status_pendanaan,
                    'dana_didapatkan_belmawa' => $danaDidapatkanBelmawa,
                    'dana_didapatkan_operator' => $danaDidapatkanOperator,
                    'catatan_final' => $request->catatan_final,
                    'nilai' => $request->nilai,
                    'skor_per_kriteria' => $normalizedSkor,
                    'id_pimpinan_pt' => $pimpinanPT->id
                ]
            );
            
            try {
                $notificationService = app(\App\Services\NotificationService::class);
                $danaYangDidapatkan = 0;
                if ($request->status_pendanaan === 'lolos') {
                    $danaInput = $request->input('dana_yang_didapatkan', 0);
                    if (is_string($danaInput)) {
                        $danaInput = preg_replace('/[^0-9.]/', '', $danaInput);
                    }
                    $danaYangDidapatkan = (float) $danaInput;
                    if ($danaYangDidapatkan < 0) $danaYangDidapatkan = 0;
                    if ($danaYangDidapatkan > 15000000) $danaYangDidapatkan = 15000000;
                }
                $notificationService->notifyHasilFinalLengkap(
                    $proposal->fresh(),
                    $request->status_pimnas,
                    $request->status_pendanaan,
                    $request->nilai,
                    $danaYangDidapatkan,
                    $request->catatan_final ?? null
                );
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi hasil final: ' . $e->getMessage());
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Hasil final berhasil diperbarui'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', array_map(fn($errors) => implode(', ', $errors), $e->errors())),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating hasil final by Pimpinan PT', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'proposal_id' => $request->proposal_id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan hasil final: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View PDF Proposal untuk Pimpinan PT
     */
    public function viewPdf($id)
    {
        try {
            $pimpinanPT = auth()->user();
            
            if ($pimpinanPT->role !== 'pimpinan_pt') {
                abort(403, 'Akses ditolak.');
            }

            $proposal = Proposal::with(['dokumen', 'proposalRevisi' => function($query) {
                if (Schema::hasColumn('proposal_revisi', 'jenis_revisi')) {
                    $query->orderByRaw("CASE WHEN jenis_revisi = 'revisi_akhir' THEN 0 ELSE 1 END")
                          ->orderBy('tanggal_submit', 'desc');
                } else {
                    $query->orderByRaw("CASE WHEN path_file LIKE '%revisi_akhir%' THEN 0 ELSE 1 END")
                      ->orderBy('tanggal_submit', 'desc');
                }
            }])
                ->where(function($query) use ($pimpinanPT) {
                    $query->where('status', 'pimpinan_pt')
                          ->orWhereHas('hasilFinal', function($q) use ($pimpinanPT) {
                              $q->where('id_pimpinan_pt', $pimpinanPT->id);
                          });
                })
                ->findOrFail($id);

            $revisiAkhir = $proposal->proposalRevisi->first();
            
            if ($revisiAkhir) {
                return StorageHelper::response($revisiAkhir->path_file, $revisiAkhir->nama_file);
            } else {
                if (!$proposal->dokumen || !$proposal->dokumen->path_file) {
                    abort(404, 'Dokumen tidak ditemukan.');
                }
                $pathFile = $proposal->dokumen->path_file;
                $filename = basename($pathFile);
                return StorageHelper::response($pathFile, $filename);
            }
        } catch (\Exception $e) {
            Log::error('Error in viewPdf (Pimpinan PT): ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat memuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Manajemen Akun - Pimpinan PT dapat mengelola semua jenis user
     */
    public function manageAccounts(Request $request)
    {
        $pimpinanPT = auth()->user();

        if ($pimpinanPT->role !== 'pimpinan_pt') {
            abort(403, 'Akses ditolak. Hanya Pimpinan PT yang dapat mengakses halaman ini.');
        }

        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();

        $hasFilter = $request->filled('filter_fakultas') ||
                     $request->filled('filter_prodi') ||
                     $request->filled('filter_nim') ||
                     $request->filled('filter_nama') ||
                     $request->filled('filter_nama_dosen') ||
                     $request->filled('filter_nama_reviewer') ||
                     $request->filled('filter_nama_operator') ||
                     $request->filled('filter_nama_pimpinan_pt');

        $mahasiswaQuery = User::mahasiswa();
        if ($hasFilter) {
            if ($request->filled('filter_fakultas')) {
                $fakultasModel = Fakultas::find($request->filter_fakultas);
                if ($fakultasModel) {
                    $mahasiswaQuery->whereRaw("metadata->>'fakultas_name' = ?", [$fakultasModel->nama_fakultas]);
                }
            }
            if ($request->filled('filter_prodi')) {
                $prodiModel = Prodi::find($request->filter_prodi);
                if ($prodiModel) {
                    $mahasiswaQuery->whereRaw("metadata->>'prodi_name' = ?", [$prodiModel->nama_prodi]);
                }
            }
            if ($request->filled('filter_nim')) {
                $mahasiswaQuery->where('identifier', 'like', '%' . $request->filter_nim . '%');
            }
            if ($request->filled('filter_nama')) {
                $mahasiswaQuery->where('name', 'like', '%' . $request->filter_nama . '%');
            }
        }
        $mahasiswas = $hasFilter ? $mahasiswaQuery->orderBy('created_at', 'desc')->get() : collect();

        $dosenQuery = User::dosen();
        if ($hasFilter && $request->filled('filter_nama_dosen')) {
            $dosenQuery->where('name', 'like', '%' . $request->filter_nama_dosen . '%');
        }
        $dosens = $hasFilter ? $dosenQuery->orderBy('created_at', 'desc')->get() : collect();

        $reviewerQuery = User::reviewer();
        if ($hasFilter && $request->filled('filter_nama_reviewer')) {
            $reviewerQuery->where('name', 'like', '%' . $request->filter_nama_reviewer . '%');
        }
        $reviewers = $hasFilter ? $reviewerQuery->orderBy('created_at', 'desc')->get() : collect();

        $operatorQuery = User::operator();
        if ($hasFilter && $request->filled('filter_nama_operator')) {
            $operatorQuery->where('name', 'like', '%' . $request->filter_nama_operator . '%');
        }
        $operators = $hasFilter ? $operatorQuery->orderBy('created_at', 'desc')->get() : collect();

        $pimpinanPTQuery = User::pimpinanPT();
        if ($hasFilter && $request->filled('filter_nama_pimpinan_pt')) {
            $pimpinanPTQuery->where('name', 'like', '%' . $request->filter_nama_pimpinan_pt . '%');
        }
        $pimpinanPTs = $hasFilter ? $pimpinanPTQuery->orderBy('created_at', 'desc')->get() : collect();

        return view('pimpinan_pt.manajemen_akun', compact('mahasiswas', 'dosens', 'reviewers', 'operators', 'pimpinanPTs', 'fakultas', 'prodis', 'hasFilter'));
    }

    /**
     * Store Account - Support semua jenis user
     */
    public function storeAccount(Request $request, $type)
    {
        $pimpinanPT = auth()->user();

        if ($pimpinanPT->role !== 'pimpinan_pt') {
            return back()->with('error', 'Akses ditolak.');
        }

        $baseRules = [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'no_hp' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ];

        $metadata = [];

        switch ($type) {
            case 'mahasiswa':
                $request->validate(array_merge($baseRules, [
                    'nim' => 'required|string|max:20|unique:users,identifier',
                    'prodi' => 'required|exists:prodis,id_prodi',
                    'fakultas' => 'required|exists:fakultas,id_fakultas',
                ]));
                $prodi = Prodi::find($request->prodi);
                $fakultas = Fakultas::find($request->fakultas);
                $metadata = [
                    'prodi_name' => $prodi->nama_prodi,
                    'fakultas_name' => $fakultas->nama_fakultas,
                    'prodi_id' => $prodi->id_prodi,
                    'fakultas_id' => $fakultas->id_fakultas,
                ];
                $identifier = $request->nim;
                $role = 'mahasiswa';
                break;
            case 'dosen':
                $request->validate(array_merge($baseRules, [
                    'identifier' => 'nullable|string|max:20|unique:users,identifier',
                ]));
                $identifier = $request->identifier;
                $role = 'dosen';
                break;
            case 'reviewer':
                $request->validate(array_merge($baseRules, [
                    'identifier' => 'nullable|string|max:20|unique:users,identifier',
                ]));
                $identifier = $request->identifier;
                $role = 'reviewer';
                break;
            case 'operator':
                $request->validate($baseRules);
                $identifier = null;
                $role = 'operator';
                break;
            case 'pimpinan_pt':
                $request->validate($baseRules);
                $identifier = null;
                $role = 'pimpinan_pt';
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }

        User::create([
            'name' => $request->nama,
            'identifier' => $identifier,
            'email' => $request->email,
            'phone' => $request->no_hp,
            'password' => Hash::make($request->password),
            'role' => $role,
            'is_active' => true,
            'metadata' => $metadata,
        ]);

        return redirect()->route('pimpinan_pt.manage.accounts')->with('success', 'Akun berhasil dibuat.');
    }

    /**
     * Update Account - Support semua jenis user
     */
    public function updateAccount(Request $request, $type, $id)
    {
        $pimpinanPT = auth()->user();

        if ($pimpinanPT->role !== 'pimpinan_pt') {
            return back()->with('error', 'Akses ditolak.');
        }

        $user = User::where('role', $type)->findOrFail($id);

        $baseRules = [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'no_hp' => 'required|string|max:15',
            'password' => 'nullable|string|min:8|confirmed',
        ];

        switch ($type) {
            case 'mahasiswa':
                $request->validate(array_merge($baseRules, [
                    'nim' => 'required|string|max:20|unique:users,identifier,' . $id,
                    'prodi' => 'required|exists:prodis,id_prodi',
                    'fakultas' => 'required|exists:fakultas,id_fakultas',
                ]));
                $prodi = Prodi::find($request->prodi);
                $fakultas = Fakultas::find($request->fakultas);
                $metadata = array_merge($user->metadata ?? [], [
                    'prodi_name' => $prodi->nama_prodi,
                    'fakultas_name' => $fakultas->nama_fakultas,
                    'prodi_id' => $prodi->id_prodi,
                    'fakultas_id' => $fakultas->id_fakultas,
                ]);
                $user->identifier = $request->nim;
                $user->metadata = $metadata;
                break;
            case 'dosen':
            case 'reviewer':
                $request->validate(array_merge($baseRules, [
                    'identifier' => 'nullable|string|max:20|unique:users,identifier,' . $id,
                ]));
                $user->identifier = $request->identifier;
                break;
            case 'operator':
            case 'pimpinan_pt':
                $request->validate($baseRules);
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }

        $user->name = $request->nama;
        $user->email = $request->email;
        $user->phone = $request->no_hp;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('pimpinan_pt.manage.accounts')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Delete Account - Support semua jenis user
     */
    public function deleteAccount($type, $id)
    {
        $pimpinanPT = auth()->user();

        if ($pimpinanPT->role !== 'pimpinan_pt') {
            return back()->with('error', 'Akses ditolak.');
        }

        if ($pimpinanPT->id == $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        try {
            User::where('id', $id)->where('role', $type)->delete();
            return redirect()->route('pimpinan_pt.manage.accounts')->with('success', 'Akun berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }

    public function bulkDeleteMahasiswa(Request $request)
    {
        $pimpinanPT = auth()->user();

        if ($pimpinanPT->role !== 'pimpinan_pt') {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        try {
            $count = User::where('role', 'mahasiswa')->whereIn('id', $request->ids)->delete();
            return redirect()->route('pimpinan_pt.manage.accounts')->with('success', "Berhasil menghapus {$count} akun mahasiswa.");
        } catch (\Exception $e) {
            return redirect()->route('pimpinan_pt.manage.accounts')->with('error', 'Gagal menghapus akun mahasiswa: ' . $e->getMessage());
        }
    }

    // ================================================================
    // FITUR OPERATOR YANG TERSEDIA UNTUK PIMPINAN PT (READ-ONLY/VIEW)
    // ================================================================

    /**
     * Pilih Reviewer - Read-only view untuk monitoring
     */
    public function pilihReviewer(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $filter = $request->get('filter', 'all');
        
        $proposals = \App\Models\Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif',
            'reviewerAdministratif', 'reviewerSubstantif1', 'reviewerSubstantif2'])
            ->where('status_validasi', 'valid')
            ->whereRaw("EXTRACT(YEAR FROM tanggal_pengajuan) = ?", [$tahun])
            ->when($filter !== 'all', fn($q) => $q->where('skim', $filter))
            ->get();
        
        $reviewers = User::reviewer()->where('is_active', true)->get();
        $isReadOnly = true;
        
        return view('pimpinan_pt.pilih_reviewer', compact('proposals', 'reviewers', 'tahun', 'filter', 'isReadOnly'));
    }

    /**
     * Pilih Reviewer Seleksi - Read-only view untuk monitoring
     */
    public function pilihReviewerSeleksi(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $filter = $request->get('filter', 'all');

        $proposals = \App\Models\Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif',
            'reviewerSubstantifSeleksi1', 'reviewerSubstantifSeleksi2'])
            ->whereIn('status', ['revisi', 'revisi_submitted', 'review_substantif_seleksi'])
            ->whereRaw("EXTRACT(YEAR FROM tanggal_pengajuan) = ?", [$tahun])
            ->when($filter !== 'all', fn($q) => $q->where('skim', $filter))
            ->get();

        $reviewers = User::reviewer()->where('is_active', true)->get();
        $isReadOnly = true;

        return view('pimpinan_pt.pilih_reviewer_seleksi', compact('proposals', 'reviewers', 'tahun', 'filter', 'isReadOnly'));
    }

    /**
     * Get Assigned Proposals (JSON) untuk monitoring
     */
    public function getAssignedProposals(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $filter = $request->get('filter', 'all');
        
        try {
            $proposals = \App\Models\Proposal::with([
                'mahasiswa', 'dokumen',
                'reviewerAdministratif', 'reviewerSubstantif1', 'reviewerSubstantif2'
            ])
            ->where('status_validasi', 'valid')
            ->whereNotNull('id_reviewer_administratif')
            ->whereNotNull('id_reviewer_substantif_1')
            ->whereNotNull('id_reviewer_substantif_2')
            ->whereRaw("EXTRACT(YEAR FROM tanggal_pengajuan) = ?", [$tahun])
            ->when($filter !== 'all', fn($q) => $q->where('skim', $filter))
            ->orderBy('updated_at', 'desc')
            ->get();
            
            return response()->json(['success' => true, 'proposals' => $proposals, 'count' => $proposals->count()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get Assigned Proposals Seleksi (JSON) untuk monitoring
     */
    public function getAssignedProposalsSeleksi(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $filter = $request->get('filter', 'all');

        try {
            $proposals = \App\Models\Proposal::with([
                'mahasiswa',
                'reviewerSubstantifSeleksi1', 'reviewerSubstantifSeleksi2'
            ])
                ->whereNotNull('id_reviewer_substantif_seleksi_1')
                ->whereNotNull('id_reviewer_substantif_seleksi_2')
                ->whereRaw("EXTRACT(YEAR FROM tanggal_pengajuan) = ?", [$tahun])
                ->when($filter !== 'all', fn($q) => $q->where('skim', $filter))
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json(['success' => true, 'proposals' => $proposals, 'count' => $proposals->count()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Ruang Kontrol - Read-only view untuk monitoring
     */
    public function ruangKontrol(Request $request)
    {
        $tahunAjaranTerpilih = $request->get('tahun', TahunAjaranHelper::getTahunAjaranTerbaru());
        
        $ruangKontrolAktif = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerpilih)
            ->where('is_active', true)
            ->first();
        
        if (!$ruangKontrolAktif) {
            $ruangKontrolAktif = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerpilih)->first();
        }
        
        $histories = RuangKontrol::whereNotNull('tahun_ajaran')
            ->whereRaw("tahun_ajaran LIKE '%/%'")
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(fn($item) => strpos($item->tahun_ajaran, '/') !== false && count(explode('/', $item->tahun_ajaran)) === 2)
            ->sortByDesc(fn($item) => (int) explode('/', $item->tahun_ajaran)[0])
            ->groupBy('tahun_ajaran');
        
        $jadwalTahun = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerpilih)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $isReadOnly = true;
        
        return view('pimpinan_pt.ruang_kontrol', compact('ruangKontrolAktif', 'histories', 'jadwalTahun', 'tahunAjaranTerpilih', 'isReadOnly'));
    }

    /**
     * Get Active Phase (JSON) untuk monitoring
     */
    public function getActivePhase(Request $request)
    {
        $tahunAjaran = $request->get('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        
        $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaran)
            ->where('is_active', true)
            ->first();
        
        if (!$ruangKontrol) {
            return response()->json(['phase' => null, 'message' => 'Tidak ada fase aktif']);
        }
        
        $activePhase = null;
        $phaseFields = ['status_pendaftaran', 'status_review', 'status_perbaikan', 'status_penilaian_akhir'];
        $phaseNames = ['pendaftaran', 'review', 'perbaikan', 'penilaian_akhir'];
        
        foreach ($phaseFields as $i => $field) {
            if ($ruangKontrol->$field === 'terbuka') {
                $activePhase = $phaseNames[$i];
                break;
            }
        }
        
        return response()->json(['phase' => $activePhase, 'ruang_kontrol' => $ruangKontrol]);
    }

    /**
     * Hasil Semi Final - View untuk monitoring
     */
    public function hasilSemiFinal(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $filter = $request->get('filter', 'all');
        $statusRevisi = $request->get('status_revisi', 'all');
        
        $proposals = \App\Models\Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif', 'hasilSemiFinal', 'proposalRevisi'])
            ->whereIn('status', ['revisi', 'revisi_submitted', 'lolos', 'tidak_lolos', 'pimpinan_pt'])
            ->whereRaw("EXTRACT(YEAR FROM tanggal_pengajuan) = ?", [$tahun])
            ->when($filter !== 'all', fn($q) => $q->where('skim', $filter))
            ->when($statusRevisi === 'belum', fn($q) => $q->whereDoesntHave('proposalRevisi'))
            ->when($statusRevisi === 'sudah', fn($q) => $q->whereHas('proposalRevisi'))
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();
        
        return view('pimpinan_pt.hasil_semi_final', compact('proposals', 'tahun', 'filter', 'statusRevisi'));
    }

    /**
     * Detail Hasil Semi Final untuk Pimpinan PT
     */
    public function detailHasilSemiFinal($id)
    {
        $proposal = \App\Models\Proposal::with([
            'mahasiswa', 'dosen', 'dokumen',
            'nilaiAdministratif.reviewer',
            'nilaiSubstantif.reviewer',
            'hasilSemiFinal',
            'proposalRevisi' => fn($q) => $q->orderBy('tanggal_submit', 'desc')
        ])->findOrFail($id);

        return view('pimpinan_pt.detail_hasil_semi_final', compact('proposal'));
    }

    /**
     * Detail Proposal untuk Pimpinan PT
     */
    public function proposalDetail($id)
    {
        $proposal = \App\Models\Proposal::with([
            'mahasiswa', 'dosen', 'dokumen',
            'nilaiAdministratif.reviewer',
            'nilaiSubstantif.reviewer',
            'hasilFinal',
            'proposalRevisi' => fn($q) => $q->orderBy('tanggal_submit', 'desc')
        ])->findOrFail($id);

        return view('pimpinan_pt.proposal_detail', compact('proposal'));
    }

    /**
     * Form Penilaian - Read-only view untuk Pimpinan PT
     */
    public function formPenilaian(Request $request)
    {
        $query = \App\Models\FormPenilaian::with('creator')->latest();
        
        if ($request->filled('jenis_form')) {
            $query->where('jenis_form', $request->jenis_form);
        }
        if ($request->filled('skim')) {
            $query->where('skim', $request->skim);
        }
        
        $formPenilaians = $query->paginate(15);
        $isReadOnly = true;
        
        return view('pimpinan_pt.form_penilaian', compact('formPenilaians', 'isReadOnly'));
    }

    /**
     * Laporan SIMBELMAWA - Read-only view untuk Pimpinan PT
     */
    public function laporanSimbelmawa(Request $request)
    {
        $query = \App\Models\SimbelmawaReport::latest();
        
        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }
        
        $reports = $query->paginate(15);
        $isReadOnly = true;
        
        return view('pimpinan_pt.laporan_simbelmawa', compact('reports', 'isReadOnly'));
    }
}
