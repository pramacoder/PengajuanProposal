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

    public function showRevisiForm($id)
    {
        if (!auth()->check()) {
            return redirect('/login')->withErrors(['email' => 'Silakan login terlebih dahulu.']);
        }

        $user = auth()->user();
        
        $proposal = Proposal::with([
            'mahasiswa', 
            'dosen', 
            'dokumen',
            'nilaiAdministratif.reviewer',
            'nilaiSubstantif.reviewer',
            'hasilSemiFinal',
            'dosenPendampingUniversitas'
        ])
            ->where('id_proposal', $id)
            ->where(function($query) use ($user) {
                $query->where('id_mahasiswa', $user->id)
                      ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE users.role = 'mahasiswa' AND users.metadata->>'team_id' = proposals.team_id::text AND users.identifier = ?)", [$user->identifier]);
            })
            ->firstOrFail();

        if ($proposal->status !== 'revisi') {
            return redirect()->route('mahasiswa.proposal.show', $id)
                ->with('error', 'Proposal belum siap untuk direvisi. Status saat ini: ' . ucfirst(str_replace('_', ' ', $proposal->status)));
        }

        $tahunAjaranTerbaru = \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru();
        $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
            ->where('is_active', true)
            ->first();
        
        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        $revisi = ProposalRevisi::where('id_proposal', $proposal->id_proposal)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.revisi_proposal', compact('proposal', 'user', 'ruangKontrol', 'revisi'));
    }

    public function submitRevisi(Request $request, $id)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.'
                ], 401);
            }

            $user = auth()->user();
            
            $proposal = Proposal::with(['dokumen'])
                ->where('id_proposal', $id)
                ->where(function($query) use ($user) {
                    $query->where('id_mahasiswa', $user->id)
                          ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE users.role = 'mahasiswa' AND users.metadata->>'team_id' = proposals.team_id::text AND users.identifier = ?)", [$user->identifier]);
                })
                ->firstOrFail();

            if ($proposal->status !== 'revisi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Proposal belum siap untuk direvisi.'
                ], 400);
            }

            $request->validate([
                'revisi_file' => 'required|file|mimes:pdf|max:5120', 
            ], [
                'revisi_file.required' => 'File revisi proposal wajib diupload.',
                'revisi_file.file' => 'File revisi harus berupa file.',
                'revisi_file.mimes' => 'File revisi harus berformat PDF.',
                'revisi_file.max' => 'Ukuran file revisi maksimal 5MB.',
            ]);

            \Illuminate\Support\Facades\DB::beginTransaction();

            $revisiFile = $request->file('revisi_file');
            $fileName = 'revisi_' . time() . '_' . $revisiFile->getClientOriginalName();
            $filePath = StorageHelper::store('proposals/revisi', $revisiFile, $fileName);

            $proposalRevisi = $proposal->proposalRevisi()->create([
                'nama_file' => $fileName,
                'path_file' => $filePath,
                'tanggal_submit' => now(),
                'jenis_revisi' => 'revisi_biasa', 
            ]);

            $proposal->update([
                'status' => 'revisi_submitted',
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Revisi proposal berhasil diupload! Proposal akan direview kembali oleh reviewer.',
                'data' => [
                    'proposal_id' => $proposal->id_proposal,
                    'file_name' => $fileName,
                    'file_size' => $revisiFile->getSize(),
                    'status' => 'revisi_submitted'
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors()),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollback();
            
            \Illuminate\Support\Facades\Log::error('Error in submitRevisi', [
                'proposal_id' => $id,
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showRevisiAkhirForm($id)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $proposal = Proposal::with(['dokumen', 'dosenPendampingUniversitas', 'hasilSemiFinal'])
            ->where('id_proposal', $id)
            ->where(function($query) use ($user) {
                $query->where('id_mahasiswa', $user->id)
                      ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE users.role = 'mahasiswa' AND users.metadata->>'team_id' = proposals.team_id::text AND users.identifier = ?)", [$user->identifier]);
            })
            ->firstOrFail();

        if ($proposal->status !== 'revisi_akhir') {
            return redirect()->route('mahasiswa.proposal.index')
                ->with('error', 'Proposal belum siap untuk revisi akhir. Status saat ini: ' . ucfirst(str_replace('_', ' ', $proposal->status)));
        }

        $revisiAkhir = ProposalRevisi::where('id_proposal', $proposal->id_proposal)
            ->where('path_file', 'like', '%revisi_akhir%')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.revisi_akhir_proposal', compact('proposal', 'user', 'revisiAkhir'));
    }

    public function submitRevisiAkhir(Request $request, $id)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.'
                ], 401);
            }

            $user = auth()->user();
            
            $proposal = Proposal::with(['dokumen', 'dosenPendampingUniversitas'])
                ->where('id_proposal', $id)
                ->where(function($query) use ($user) {
                    $query->where('id_mahasiswa', $user->id)
                          ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE users.role = 'mahasiswa' AND users.metadata->>'team_id' = proposals.team_id::text AND users.identifier = ?)", [$user->identifier]);
                })
                ->firstOrFail();

            if ($proposal->status !== 'revisi_akhir') {
                return response()->json([
                    'success' => false,
                    'message' => 'Proposal belum siap untuk revisi akhir.'
                ], 400);
            }

            $request->validate([
                'revisi_file' => 'required|file|mimes:pdf|max:5120', 
            ], [
                'revisi_file.required' => 'File revisi akhir proposal wajib diupload.',
                'revisi_file.file' => 'File revisi harus berupa file.',
                'revisi_file.mimes' => 'File revisi harus berformat PDF.',
                'revisi_file.max' => 'Ukuran file revisi maksimal 5MB.',
            ]);

            \Illuminate\Support\Facades\DB::beginTransaction();

            $revisiFile = $request->file('revisi_file');
            $fileName = 'revisi_akhir_' . time() . '_' . $revisiFile->getClientOriginalName();
            $filePath = StorageHelper::store('proposals/revisi_akhir', $revisiFile, $fileName);

            $proposalRevisi = $proposal->proposalRevisi()->create([
                'nama_file' => $fileName,
                'path_file' => $filePath,
                'tanggal_submit' => now(),
                'jenis_revisi' => 'revisi_akhir', 
            ]);

            $proposal->update([
                'status' => 'validasi_akhir_dosen_univ',
                'status_final' => 'validasi_akhir_dosen_univ',
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Revisi akhir proposal berhasil diupload! Proposal akan divalidasi oleh dosen pendamping universitas.',
                'data' => [
                    'proposal_id' => $proposal->id_proposal,
                    'file_name' => $fileName,
                    'file_size' => $revisiFile->getSize(),
                    'status' => 'validasi_akhir_dosen_univ'
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors()),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollback();
            
            \Illuminate\Support\Facades\Log::error('Error in submitRevisiAkhir', [
                'proposal_id' => $id,
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
