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
            'dokumen',
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
            'catatan' => 'nullable|string|max:1000'
        ]);

        $dosen = Auth::guard('dosen')->user();
        $proposal = Proposal::findOrFail($id);

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

        $proposal->update([
            'status_validasi' => $statusValidasi,
            'catatan' => $request->catatan, // Menggunakan field 'catatan' bukan 'catatan_validasi'
            'tanggal_validasi' => now(),
            'status' => $statusValidasi === 'valid' ? 'submitted' : 'tidak_valid' // Update status proposal juga
        ]);

        // Kirim notifikasi ke mahasiswa
        // TODO: Implementasi notifikasi

        $message = $statusValidasi === 'valid' 
            ? 'Proposal berhasil divalidasi dan diteruskan ke reviewer.' 
            : 'Proposal ditolak dengan alasan yang telah diberikan.';

        return redirect()->route('dosen.pendamping.dashboard')
            ->with('success', $message);
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
