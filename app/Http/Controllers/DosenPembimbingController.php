<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;

class DosenPembimbingController extends Controller
{
    /**
     * Dashboard dosen pembimbing - melihat proposal mahasiswa bimbingan
     */
    public function dashboard()
    {
        $dosen = Auth::guard('dosen')->user();
        
        // Ambil semua mahasiswa bimbingan
        $mahasiswaBimbingan = $dosen->mahasiswaBimbingan()->with(['proposal' => function($query) {
            $query->with(['dokumen', 'nilaiAdministratif', 'nilaiSubstantif', 'hasilFinal']);
        }])->get();

        // Hitung statistik
        $totalMahasiswa = $mahasiswaBimbingan->count();
        $totalProposal = $mahasiswaBimbingan->where('proposal', '!=', null)->count();
        $proposalPending = $mahasiswaBimbingan->where('proposal.status_validasi', 'pending')->count();
        $proposalValid = $mahasiswaBimbingan->where('proposal.status_validasi', 'valid')->count();

        return view('dosen.pembimbing.dashboard', compact(
            'mahasiswaBimbingan', 
            'totalMahasiswa', 
            'totalProposal', 
            'proposalPending', 
            'proposalValid'
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
        
        $mahasiswaBimbingan = $dosen->mahasiswaBimbingan()->with(['proposal'])->get();

        return view('dosen.pembimbing.mahasiswa_bimbingan', compact('mahasiswaBimbingan'));
    }
}
