<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;

class DosenPendampingController extends Controller
{
    /**
     * Dashboard dosen pendamping - validasi proposal
     */
    public function dashboard()
    {
        $dosen = Auth::guard('dosen')->user();
        
        // Ambil semua proposal yang ditugaskan ke dosen ini
        $proposals = $dosen->proposals()->with([
            'mahasiswa',
            'dokumens',
            'nilaiAdministratif',
            'nilaiSubstantif',
            'hasilFinal'
        ])->get();

        // Hitung statistik
        $totalProposal = $proposals->count();
        $proposalPending = $proposals->where('status_validasi', 'pending')->count();
        $proposalValid = $proposals->where('status_validasi', 'valid')->count();
        $proposalTidakValid = $proposals->where('status_validasi', 'tidak_valid')->count();

        return view('dosen.pendamping.dashboard', compact(
            'proposals', 
            'totalProposal', 
            'proposalPending', 
            'proposalValid', 
            'proposalTidakValid'
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
            'dokumens',
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
            'status_validasi' => 'required|in:valid,tidak_valid',
            'catatan' => 'nullable|string|max:1000'
        ]);

        $dosen = Auth::guard('dosen')->user();
        $proposal = Proposal::findOrFail($id);

        // Pastikan proposal ini ditugaskan ke dosen ini
        if ($proposal->id_dosen !== $dosen->id_dosen) {
            abort(403, 'Anda tidak memiliki akses ke proposal ini.');
        }

        $proposal->update([
            'status_validasi' => $request->status_validasi,
            'catatan_validasi' => $request->catatan,
            'tanggal_validasi' => now()
        ]);

        // Kirim notifikasi ke mahasiswa
        // TODO: Implementasi notifikasi

        return redirect()->route('dosen.pendamping.dashboard')
            ->with('success', 'Proposal berhasil divalidasi.');
    }

    /**
     * List proposal yang perlu divalidasi
     */
    public function proposalValidasi()
    {
        $dosen = Auth::guard('dosen')->user();
        
        $proposals = $dosen->proposals()->with(['mahasiswa'])->get();

        return view('dosen.pendamping.proposal_validasi', compact('proposals'));
    }
}
