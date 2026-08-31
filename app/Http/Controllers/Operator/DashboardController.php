<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Helpers\TahunAjaranHelper;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        // Ambil tahun ajaran yang dipilih (default: tahun ajaran terbaru)
        $tahunAjaranTerpilih = $request->input('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        
        // Ambil daftar tahun ajaran yang tersedia dari proposal
        $tahunAjaranList = Proposal::whereNotNull('tahun_ajaran')
            ->whereRaw("tahun_ajaran LIKE '%/%'")
            ->pluck('tahun_ajaran')
            ->filter(function($item) {
                return strpos($item, '/') !== false &&
                       count(explode('/', $item)) === 2;
            })
            ->unique()
            ->sortByDesc(function($item) {
                return (int) explode('/', $item)[0];
            })
            ->values();
        
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
        
        $query = Proposal::with(['mahasiswa', 'hasilFinal'])
            ->where('tahun_ajaran', $tahunAjaran);
        
        // Filter berdasarkan Fakultas
        if ($request->filled('filter_fakultas')) {
            $fakultas = Fakultas::find($request->filter_fakultas);
            if ($fakultas) {
                $query->where(function($q) use ($fakultas) {
                    $q->whereHas('mahasiswa', function($subQ) use ($fakultas) {
                        $subQ->whereRaw("metadata->>'fakultas_name' = ?", [$fakultas->nama_fakultas]);
                    })->orWhereRaw("EXISTS (SELECT 1 FROM users u WHERE (u.metadata->>'team_id') = proposals.team_id AND u.role = 'mahasiswa' AND u.metadata->>'fakultas_name' = ?)", [$fakultas->nama_fakultas]);
                });
            }
        }
        
        // Filter berdasarkan Prodi
        if ($request->filled('filter_prodi')) {
            $prodi = Prodi::find($request->filter_prodi);
            if ($prodi) {
                $query->where(function($q) use ($prodi) {
                    $q->whereHas('mahasiswa', function($subQ) use ($prodi) {
                        $subQ->whereRaw("metadata->>'prodi_name' = ?", [$prodi->nama_prodi]);
                    })->orWhereRaw("EXISTS (SELECT 1 FROM users u WHERE (u.metadata->>'team_id') = proposals.team_id AND u.role = 'mahasiswa' AND u.metadata->>'prodi_name' = ?)", [$prodi->nama_prodi]);
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

    private function getPKM8BidangData($tahunAjaran)
    {
        $skims = ['RE', 'RSH', 'KC', 'PM', 'PI', 'K', 'KI', 'VGK'];
        
        return collect($skims)->map(function($skim) use ($tahunAjaran) {
            $total = Proposal::where('skim', $skim)->where('tahun_ajaran', $tahunAjaran)->count();
            $sudahValid = Proposal::where('skim', $skim)->where('status_validasi', 'valid')->where('tahun_ajaran', $tahunAjaran)->count();
            $belumValid = Proposal::where('skim', $skim)->where('status_validasi', 'pending')->where('tahun_ajaran', $tahunAjaran)->count();
            $tolakValid = Proposal::where('skim', $skim)->where('status_validasi', 'tidak_valid')->where('tahun_ajaran', $tahunAjaran)->count();
            $sedangReview = Proposal::where('skim', $skim)->whereIn('status', ['submitted', 'review_administratif', 'review_substantif', 'revisi'])->where('tahun_ajaran', $tahunAjaran)->count();
            $selesaiReview = Proposal::where('skim', $skim)->whereIn('status', ['lolos', 'tidak_lolos'])->where('tahun_ajaran', $tahunAjaran)->count();
            
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
            $total = Proposal::where('skim', $skim)->where('tahun_ajaran', $tahunAjaran)->count();
            $sudahValid = Proposal::where('skim', $skim)->where('status_validasi', 'valid')->where('tahun_ajaran', $tahunAjaran)->count();
            $belumValid = Proposal::where('skim', $skim)->where('status_validasi', 'pending')->where('tahun_ajaran', $tahunAjaran)->count();
            $tolakValid = Proposal::where('skim', $skim)->where('status_validasi', 'tidak_valid')->where('tahun_ajaran', $tahunAjaran)->count();
            $sedangReview = Proposal::where('skim', $skim)->whereIn('status', ['submitted', 'review_administratif', 'review_substantif', 'revisi'])->where('tahun_ajaran', $tahunAjaran)->count();
            $selesaiReview = Proposal::where('skim', $skim)->whereIn('status', ['lolos', 'tidak_lolos'])->where('tahun_ajaran', $tahunAjaran)->count();
            
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

    private function getChartData($tahunAjaran)
    {
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
            ->take(3)
            ->values();
        
        $proposalPerTahun = $tahunAjaranList->map(function($ta) {
            return [
                'tahun' => $ta,
                'jumlah' => Proposal::where('tahun_ajaran', $ta)->count()
            ];
        })->toArray();
        
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
        
        $proposalPerFakultas = Proposal::where('tahun_ajaran', $tahunAjaran)
            ->join('users', 'proposals.id_mahasiswa', '=', 'users.id')
            ->where('users.role', 'mahasiswa')
            ->selectRaw("users.metadata->>'fakultas_name' as nama_fakultas, COUNT(*) as jumlah")
            ->groupByRaw("users.metadata->>'fakultas_name'")
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
                    'judul' => $proposal->judul,
                    'skim' => $proposal->skim,
                    'mahasiswa' => $proposal->mahasiswa->name ?? 'N/A',
                    'nilai' => $proposal->hasilFinal->nilai ?? 0,
                    'status' => $proposal->hasilFinal->status_final ?? 'N/A'
                ];
            });
    }
}
