<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\ProposalRevisi;
use App\Models\RuangKontrol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\StorageHelper;
use Illuminate\Support\Str;

class ProposalRevisiController extends Controller
{
    public function index()
    {
        $mahasiswa = auth()->user();
        
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.proposal.index')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // Cek apakah status perbaikan terbuka
        // Ambil ruang kontrol aktif untuk tahun akademik terbaru
        $tahunAjaranTerbaru = \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru();
        $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
            ->where('is_active', true)
            ->first();
        
        // Fallback: jika tidak ada yang aktif, ambil yang pertama untuk tahun ajaran terbaru
        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        if (!$ruangKontrol || $ruangKontrol->status_perbaikan !== 'terbuka') {
            return redirect()->route('mahasiswa.proposal.index')->with('error', 'Sistem perbaikan proposal sedang ditutup.');
        }

        // Ambil proposal mahasiswa yang berstatus revisi
        // Cek dengan berbagai cara untuk menemukan proposal mahasiswa
        $proposal = Proposal::where(function($query) use ($mahasiswa) {
            // Ketua tim berdasarkan nim
            $query->where('ketua_nim', $mahasiswa->identifier)
                  // ATAU anggota tim lain (berbasis team_id)
                  ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE users.role = 'mahasiswa' AND users.metadata->>'team_id' = proposals.team_id::text AND users.identifier = ?)", [$mahasiswa->identifier])
                  // ATAU sebagai mahasiswa pengaju
                  ->orWhere('id_mahasiswa', $mahasiswa->id);
        })
        ->where('status', 'revisi')
        ->first();

        if (!$proposal) {
            // Jika tidak ada proposal dengan status revisi, cek apakah ada proposal yang sedang direview
            $proposalInReview = Proposal::where(function($query) use ($mahasiswa) {
                $query->where('ketua_nim', $mahasiswa->identifier)
                      ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE users.role = 'mahasiswa' AND users.metadata->>'team_id' = proposals.team_id::text AND users.identifier = ?)", [$mahasiswa->identifier])
                      ->orWhere('id_mahasiswa', $mahasiswa->id);
            })
            ->whereIn('status', ['review_administratif', 'review_substantif', 'review_completed'])
            ->first();
            
            if ($proposalInReview) {
                return redirect()->route('mahasiswa.proposal.index')->with('info', 'Proposal Anda masih dalam proses review. Silakan tunggu hingga review selesai untuk melakukan revisi.');
            }
            
            return redirect()->route('mahasiswa.proposal.index')->with('error', 'Tidak ada proposal yang perlu direvisi.');
        }

        // Ambil data revisi yang sudah ada
        $revisi = ProposalRevisi::where('id_proposal', $proposal->id_proposal)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.revisi_proposal', compact('proposal', 'revisi', 'ruangKontrol'));
    }

    public function store(Request $request)
    {
        $mahasiswa = auth()->user();
        
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.proposal.index')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // Cek apakah status perbaikan terbuka
        // Ambil ruang kontrol aktif untuk tahun akademik terbaru
        $tahunAjaranTerbaru = \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru();
        $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
            ->where('is_active', true)
            ->first();
        
        // Fallback: jika tidak ada yang aktif, ambil yang pertama untuk tahun ajaran terbaru
        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        if (!$ruangKontrol || $ruangKontrol->status_perbaikan !== 'terbuka') {
            return redirect()->route('mahasiswa.proposal.index')->with('error', 'Sistem perbaikan proposal sedang ditutup.');
        }

        // Validasi request
        $request->validate([
            'file_revisi' => 'required|file|mimes:pdf|max:5120', // 5MB max
        ], [
            'file_revisi.required' => 'File revisi wajib diupload.',
            'file_revisi.file' => 'File revisi harus berupa file.',
            'file_revisi.mimes' => 'File revisi harus berformat PDF.',
            'file_revisi.max' => 'Ukuran file revisi maksimal 5MB.',
        ]);

        // Ambil proposal mahasiswa yang berstatus revisi
        $proposal = Proposal::where(function($query) use ($mahasiswa) {
            $query->where('ketua_nim', $mahasiswa->identifier)
                  ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE users.role = 'mahasiswa' AND users.metadata->>'team_id' = proposals.team_id::text AND users.identifier = ?)", [$mahasiswa->identifier])
                  ->orWhere('id_mahasiswa', $mahasiswa->id);
        })
        ->where('status', 'revisi')
        ->first();

        if (!$proposal) {
            return redirect()->route('mahasiswa.proposal.index')->with('error', 'Tidak ada proposal yang perlu direvisi.');
        }

        try {
            // Hapus file revisi sebelumnya jika ada (hanya 1 file revisi aktif per proposal)
            $revisiLama = ProposalRevisi::where('id_proposal', $proposal->id_proposal)
                ->first();
            
            if ($revisiLama) {
                // Hapus file fisik dari storage
                if (StorageHelper::exists($revisiLama->path_file)) {
                    StorageHelper::delete($revisiLama->path_file);
                }
                
                // Hapus data dari database
                $revisiLama->delete();
            }

            // Upload file revisi baru
            $file = $request->file('file_revisi');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = 'revisi_' . $proposal->id_proposal . '_' . time() . '_' . Str::random(10) . '.' . $extension;
            $path = StorageHelper::store('proposal_revisi', $file, $fileName);

            // Simpan data revisi baru
            $revisi = new ProposalRevisi();
            $revisi->id_proposal = $proposal->id_proposal;
            $revisi->nama_file = $originalName;
            $revisi->path_file = $path;
            $revisi->tanggal_submit = now();
            $revisi->jenis_revisi = 'revisi_biasa'; // Revisi biasa untuk hasil semi final
            $revisi->save();

            $message = $revisiLama 
                ? 'File revisi berhasil diupload. File revisi sebelumnya telah digantikan.' 
                : 'File revisi berhasil diupload.';

            return redirect()->route('mahasiswa.revisi.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('mahasiswa.revisi.index')->with('error', 'Gagal mengupload file revisi: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        $mahasiswa = auth()->user();
        
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.proposal.index')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $revisi = ProposalRevisi::findOrFail($id);
        
        // Cek apakah revisi ini milik proposal mahasiswa yang login
        $proposal = Proposal::where('id_proposal', $revisi->id_proposal)
            ->where(function($query) use ($mahasiswa) {
                $query->where('ketua_nim', $mahasiswa->identifier)
                      ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE users.role = 'mahasiswa' AND users.metadata->>'team_id' = proposals.team_id::text AND users.identifier = ?)", [$mahasiswa->identifier])
                      ->orWhere('id_mahasiswa', $mahasiswa->id);
            })
            ->first();

        if (!$proposal) {
            return redirect()->route('mahasiswa.proposal.index')->with('error', 'File revisi tidak ditemukan.');
        }

        if (!StorageHelper::exists($revisi->path_file)) {
            return redirect()->route('mahasiswa.revisi.index')->with('error', 'File revisi tidak ditemukan di server.');
        }

        return StorageHelper::download($revisi->path_file);
    }

    public function destroy($id)
    {
        $mahasiswa = auth()->user();
        
        if (!$mahasiswa) {
            return redirect()->route('mahasiswa.proposal.index')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // Cek apakah status perbaikan terbuka
        // Ambil ruang kontrol aktif untuk tahun akademik terbaru
        $tahunAjaranTerbaru = \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru();
        $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
            ->where('is_active', true)
            ->first();
        
        // Fallback: jika tidak ada yang aktif, ambil yang pertama untuk tahun ajaran terbaru
        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        if (!$ruangKontrol || $ruangKontrol->status_perbaikan !== 'terbuka') {
            return redirect()->route('mahasiswa.proposal.index')->with('error', 'Sistem perbaikan proposal sedang ditutup.');
        }

        $revisi = ProposalRevisi::findOrFail($id);
        
        // Cek apakah revisi ini milik proposal mahasiswa yang login
        $proposal = Proposal::where('id_proposal', $revisi->id_proposal)
            ->where(function($query) use ($mahasiswa) {
                $query->where('ketua_nim', $mahasiswa->identifier)
                      ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE users.role = 'mahasiswa' AND users.metadata->>'team_id' = proposals.team_id::text AND users.identifier = ?)", [$mahasiswa->identifier])
                      ->orWhere('id_mahasiswa', $mahasiswa->id);
            })
            ->first();

        if (!$proposal) {
            return redirect()->route('mahasiswa.proposal.index')->with('error', 'File revisi tidak ditemukan.');
        }

        try {
            // Hapus file dari storage
            if (StorageHelper::exists($revisi->path_file)) {
                StorageHelper::delete($revisi->path_file);
            }

            // Hapus data dari database
            $revisi->delete();

            return redirect()->route('mahasiswa.revisi.index')->with('success', 'File revisi berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('mahasiswa.revisi.index')->with('error', 'Gagal menghapus file revisi: ' . $e->getMessage());
        }
    }
}
