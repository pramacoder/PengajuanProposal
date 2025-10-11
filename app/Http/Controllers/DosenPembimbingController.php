<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\Proposal;
use App\Helpers\TahunAjaranHelper;
use Illuminate\Support\Facades\Auth;

class DosenPembimbingController extends Controller
{
    /**
     * Dashboard dosen pembimbing - melihat proposal mahasiswa bimbingan
     */
    public function dashboard()
    {
        $dosen = Auth::guard('dosen')->user();
        
        // Ambil semua mahasiswa bimbingan dengan proposal tahun terbaru
        $tahunAjaranTerbaru = '2024/2025';
        $mahasiswaBimbingan = $dosen->mahasiswaBimbingan()->with(['proposalTahunTerbaru' => function($query) use ($tahunAjaranTerbaru) {
            $query->where('tahun_ajaran', $tahunAjaranTerbaru)
                  ->with(['dokumen', 'nilaiAdministratif', 'nilaiSubstantif', 'hasilFinal']);
        }])->get();

        // Hitung statistik berdasarkan proposal tahun terbaru
        $totalMahasiswa = $mahasiswaBimbingan->count();
        $totalProposal = $mahasiswaBimbingan->where('proposalTahunTerbaru', '!=', null)->count();
        $proposalPending = $mahasiswaBimbingan->where('proposalTahunTerbaru.status_validasi', 'pending')->count();
        $proposalValid = $mahasiswaBimbingan->where('proposalTahunTerbaru.status_validasi', 'valid')->count();

        return view('dosen.pembimbing.dashboard', compact(
            'mahasiswaBimbingan', 
            'totalMahasiswa', 
            'totalProposal', 
            'proposalPending', 
            'proposalValid',
            'tahunAjaranTerbaru'
        ));
    }

    /**
     * Detail proposal mahasiswa bimbingan
     */
    public function detailProposal($id)
    {
        $dosen = Auth::guard('dosen')->user();
        
        $proposal = Proposal::with([
            'mahasiswa',
            'dokumen',
            'nilaiAdministratif',
            'nilaiSubstantif',
            'hasilFinal',
            'dosen'
        ])->findOrFail($id);

        // Pastikan proposal ini dari mahasiswa bimbingan dosen
        if ($proposal->mahasiswa->id_dosen_pembimbing !== $dosen->id_dosen) {
            abort(403, 'Anda tidak memiliki akses ke proposal ini.');
        }

        return view('dosen.pembimbing.detail_proposal', compact('proposal'));
    }

    /**
     * List semua mahasiswa bimbingan
     */
    public function mahasiswaBimbingan()
    {
        $dosen = Auth::guard('dosen')->user();
        
        // Ambil mahasiswa bimbingan dengan proposal tahun terbaru
        $tahunAjaranTerbaru = '2024/2025';
        $mahasiswaBimbingan = $dosen->mahasiswaBimbingan()->with(['proposalTahunTerbaru' => function($query) use ($tahunAjaranTerbaru) {
            $query->where('tahun_ajaran', $tahunAjaranTerbaru);
        }])->get();

        return view('dosen.pembimbing.mahasiswa_bimbingan', compact('mahasiswaBimbingan'));
    }
}
