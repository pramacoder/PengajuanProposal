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
        return view('dosen.pembimbing.dashboard');
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

    /**
     * Halaman Testing Child 1
     */
    public function testingChild1()
    {
        return view('dosen.pembimbing.testing.child1');
    }

    /**
     * Halaman Testing Child 2
     */
    public function testingChild2()
    {
        return view('dosen.pembimbing.testing.child2');
    }
}
