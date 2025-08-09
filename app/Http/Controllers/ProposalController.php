<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\Dokumen;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProposalController extends Controller
{
    /**
     * Menampilkan form pengajuan proposal
     */
    public function create()
    {
        $user = Auth::guard('mahasiswa')->user();
        return view('mahasiswa.ajukanproposal', compact('user'));
    }

    /**
     * Menyimpan proposal baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'skim' => 'required|string|in:KC,RE,RSH,PI,PM,K,VGK,GFT',
            'dana_diajukan' => 'required|numeric|min:0|max:15000000',
            'dosen_pembimbing' => 'required|string|max:255',
            'ketua_nama' => 'required|string|max:255',
            'ketua_nim' => 'required|string|min:8|max:20',
            'ketua_prodi' => 'required|string|max:255',
            'ketua_fakultas' => 'required|string|max:255',
            'ketua_email' => 'required|email|max:255',
            'ketua_no_hp' => 'required|string|min:10|max:15',
            'proposal_file' => 'required|file|mimes:pdf|max:5120', // 5MB
            'persetujuan_file' => 'required|file|mimes:pdf|max:5120', // 5MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Upload file proposal
            $proposalPath = $request->file('proposal_file')->store('proposals', 'public');
            
            // Upload file persetujuan
            $persetujuanPath = $request->file('persetujuan_file')->store('persetujuan', 'public');

            // Simpan data proposal
            $proposal = Proposal::create([
                'judul' => $request->judul,
                'skim' => $request->skim,
                'tahun_ajaran' => $request->tahun_ajaran,
                'tanggal_pengajuan' => $request->tanggal_pengajuan,
                'dana_diajukan' => $request->dana_diajukan,
                'dosen_pembimbing' => $request->dosen_pembimbing,
                'ketua_nama' => $request->ketua_nama,
                'ketua_nim' => $request->ketua_nim,
                'ketua_prodi' => $request->ketua_prodi,
                'ketua_fakultas' => $request->ketua_fakultas,
                'ketua_email' => $request->ketua_email,
                'ketua_no_hp' => $request->ketua_no_hp,
                'anggota1_nama' => $request->anggota1_nama,
                'anggota1_nim' => $request->anggota1_nim,
                'anggota1_prodi' => $request->anggota1_prodi,
                'anggota1_fakultas' => $request->anggota1_fakultas,
                'anggota1_email' => $request->anggota1_email,
                'anggota1_no_hp' => $request->anggota1_no_hp,
                'anggota2_nama' => $request->anggota2_nama,
                'anggota2_nim' => $request->anggota2_nim,
                'anggota2_prodi' => $request->anggota2_prodi,
                'anggota2_fakultas' => $request->anggota2_fakultas,
                'anggota2_email' => $request->anggota2_email,
                'anggota2_no_hp' => $request->anggota2_no_hp,
                'anggota3_nama' => $request->anggota3_nama,
                'anggota3_nim' => $request->anggota3_nim,
                'anggota3_prodi' => $request->anggota3_prodi,
                'anggota3_fakultas' => $request->anggota3_fakultas,
                'anggota3_email' => $request->anggota3_email,
                'anggota3_no_hp' => $request->anggota3_no_hp,
                'status' => 'pending',
                'mahasiswa_id' => Auth::guard('mahasiswa')->id(),
            ]);

            // Simpan dokumen
            Dokumen::create([
                'proposal_id' => $proposal->id,
                'jenis' => 'proposal',
                'file_path' => $proposalPath,
                'nama_file' => $request->file('proposal_file')->getClientOriginalName(),
            ]);

            Dokumen::create([
                'proposal_id' => $proposal->id,
                'jenis' => 'persetujuan',
                'file_path' => $persetujuanPath,
                'nama_file' => $request->file('persetujuan_file')->getClientOriginalName(),
            ]);

            return redirect()->route('mahasiswa.proposal.index')
                ->with('success', 'Proposal berhasil diajukan!');

        } catch (\Exception $e) {
            // Hapus file yang sudah diupload jika terjadi error
            if (isset($proposalPath)) {
                Storage::disk('public')->delete($proposalPath);
            }
            if (isset($persetujuanPath)) {
                Storage::disk('public')->delete($persetujuanPath);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengajukan proposal. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Menampilkan daftar proposal
     */
    public function index()
    {
        $proposals = Proposal::where('mahasiswa_id', Auth::guard('mahasiswa')->id())
            ->with(['dokumen'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.proposal_list', compact('proposals'));
    }

    /**
     * Menampilkan detail proposal
     */
    public function show($id)
    {
        $proposal = Proposal::where('mahasiswa_id', Auth::guard('mahasiswa')->id())
            ->with(['dokumen'])
            ->findOrFail($id);

        return view('mahasiswa.proposal_detail', compact('proposal'));
    }

    /**
     * Download file proposal
     */
    public function download($id, $jenis)
    {
        $proposal = Proposal::where('mahasiswa_id', Auth::guard('mahasiswa')->id())->findOrFail($id);
        $dokumen = $proposal->dokumen()->where('jenis', $jenis)->firstOrFail();

        if (!Storage::disk('public')->exists($dokumen->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($dokumen->file_path, $dokumen->nama_file);
    }

    /**
     * Menampilkan form edit proposal
     */
    public function edit($id)
    {
        $proposal = Proposal::where('mahasiswa_id', Auth::guard('mahasiswa')->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        return view('mahasiswa.edit_proposal', compact('proposal'));
    }

    /**
     * Update proposal
     */
    public function update(Request $request, $id)
    {
        $proposal = Proposal::where('mahasiswa_id', Auth::guard('mahasiswa')->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        // Validasi input
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'skim' => 'required|string|in:KC,RE,RSH,PI,PM,K,VGK,GFT',
            'dana_diajukan' => 'required|numeric|min:0|max:15000000',
            'dosen_pembimbing' => 'required|string|max:255',
            'ketua_nama' => 'required|string|max:255',
            'ketua_nim' => 'required|string|min:8|max:20',
            'ketua_prodi' => 'required|string|max:255',
            'ketua_fakultas' => 'required|string|max:255',
            'ketua_email' => 'required|email|max:255',
            'ketua_no_hp' => 'required|string|min:10|max:15',
            'proposal_file' => 'nullable|file|mimes:pdf|max:5120',
            'persetujuan_file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Update data proposal
            $proposal->update([
                'judul' => $request->judul,
                'skim' => $request->skim,
                'dana_diajukan' => $request->dana_diajukan,
                'dosen_pembimbing' => $request->dosen_pembimbing,
                'ketua_nama' => $request->ketua_nama,
                'ketua_nim' => $request->ketua_nim,
                'ketua_prodi' => $request->ketua_prodi,
                'ketua_fakultas' => $request->ketua_fakultas,
                'ketua_email' => $request->ketua_email,
                'ketua_no_hp' => $request->ketua_no_hp,
                'anggota1_nama' => $request->anggota1_nama,
                'anggota1_nim' => $request->anggota1_nim,
                'anggota1_prodi' => $request->anggota1_prodi,
                'anggota1_fakultas' => $request->anggota1_fakultas,
                'anggota1_email' => $request->anggota1_email,
                'anggota1_no_hp' => $request->anggota1_no_hp,
                'anggota2_nama' => $request->anggota2_nama,
                'anggota2_nim' => $request->anggota2_nim,
                'anggota2_prodi' => $request->anggota2_prodi,
                'anggota2_fakultas' => $request->anggota2_fakultas,
                'anggota2_email' => $request->anggota2_email,
                'anggota2_no_hp' => $request->anggota2_no_hp,
                'anggota3_nama' => $request->anggota3_nama,
                'anggota3_nim' => $request->anggota3_nim,
                'anggota3_prodi' => $request->anggota3_prodi,
                'anggota3_fakultas' => $request->anggota3_fakultas,
                'anggota3_email' => $request->anggota3_email,
                'anggota3_no_hp' => $request->anggota3_no_hp,
            ]);

            // Update file jika ada
            if ($request->hasFile('proposal_file')) {
                $proposalPath = $request->file('proposal_file')->store('proposals', 'public');
                
                // Hapus file lama
                $oldDokumen = $proposal->dokumen()->where('jenis', 'proposal')->first();
                if ($oldDokumen) {
                    Storage::disk('public')->delete($oldDokumen->file_path);
                    $oldDokumen->update([
                        'file_path' => $proposalPath,
                        'nama_file' => $request->file('proposal_file')->getClientOriginalName(),
                    ]);
                }
            }

            if ($request->hasFile('persetujuan_file')) {
                $persetujuanPath = $request->file('persetujuan_file')->store('persetujuan', 'public');
                
                // Hapus file lama
                $oldDokumen = $proposal->dokumen()->where('jenis', 'persetujuan')->first();
                if ($oldDokumen) {
                    Storage::disk('public')->delete($oldDokumen->file_path);
                    $oldDokumen->update([
                        'file_path' => $persetujuanPath,
                        'nama_file' => $request->file('persetujuan_file')->getClientOriginalName(),
                    ]);
                }
            }

            return redirect()->route('mahasiswa.proposal.index')
                ->with('success', 'Proposal berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui proposal. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Hapus proposal
     */
    public function destroy($id)
    {
        $proposal = Proposal::where('mahasiswa_id', Auth::guard('mahasiswa')->id())
            ->where('status', 'pending')
            ->findOrFail($id);

        try {
            // Hapus file dokumen
            foreach ($proposal->dokumen as $dokumen) {
                Storage::disk('public')->delete($dokumen->file_path);
            }

            // Hapus proposal
            $proposal->delete();

            return redirect()->route('mahasiswa.proposal.index')
                ->with('success', 'Proposal berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus proposal. Silakan coba lagi.');
        }
    }
} 