<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Proposal;
use App\Models\HasilFinal;
use App\Models\HasilSemiFinal;
use App\Models\Dokumen;
use App\Models\NilaiSubstantif;
use App\Models\ProposalRevisi;
use App\Models\PT;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Reviewer;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Helpers\ProposalHelper;

class PimpinanPTController extends Controller
{
    /**
     * Dashboard Pimpinan PT - Menampilkan proposal yang perlu dinilai final
     */
    public function dashboard()
    {
        $pimpinanPT = Auth::guard('operator')->user();
        
        // Pastikan user adalah Pimpinan PT
        if ($pimpinanPT->role !== 'pimpinan_pt') {
            abort(403, 'Akses ditolak. Hanya Pimpinan PT yang dapat mengakses halaman ini.');
        }

        // Ambil proposal yang sudah divalidasi dosen universitas (status: pimpinan_pt)
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'hasilFinal', 'hasilSemiFinal'])
            ->where('status', 'pimpinan_pt')
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        // Kategorikan proposal berdasarkan status hasil final
        $proposalsBelumDinilai = $proposals->filter(function($proposal) {
            return !$proposal->hasilFinal;
        });
        
        $proposalsSudahDinilai = $proposals->filter(function($proposal) {
            return $proposal->hasilFinal;
        });

        return view('pimpinan_pt.dashboard', compact('proposals', 'proposalsBelumDinilai', 'proposalsSudahDinilai', 'pimpinanPT'));
    }

    /**
     * Detail Hasil Final untuk Pimpinan PT
     */
    public function detailHasilFinal($id)
    {
        $pimpinanPT = Auth::guard('operator')->user();
        
        // Pastikan user adalah Pimpinan PT
        if ($pimpinanPT->role !== 'pimpinan_pt') {
            abort(403, 'Akses ditolak. Hanya Pimpinan PT yang dapat mengakses halaman ini.');
        }

        $proposal = Proposal::with([
            'mahasiswa',
            'semuaAnggotaTim',
            'dokumen',
            'hasilFinal',
            'hasilSemiFinal',
            'nilaiSubstantif.reviewer',
            'proposalRevisi' => function($query) {
                $query->orderBy('tanggal_submit', 'desc');
            }
        ])
            ->where(function($query) use ($pimpinanPT) {
                $query->where('status', 'pimpinan_pt')
                      ->orWhereHas('hasilFinal', function($q) use ($pimpinanPT) {
                          $q->where('id_pimpinan_pt', $pimpinanPT->id_pt);
                      });
            })
            ->findOrFail($id);

        // Tentukan file proposal yang harus ditampilkan
        // Prioritas: revisi akhir > revisi biasa > proposal awal
        $fileProposal = null;
        $jenisFile = 'proposal_awal';
        
        // Cek revisi akhir (file dengan path mengandung 'revisi_akhir')
        $revisiAkhir = $proposal->proposalRevisi->filter(function($revisi) {
            return strpos($revisi->path_file, 'revisi_akhir') !== false;
        })->first();
        
        if ($revisiAkhir) {
            $fileProposal = $revisiAkhir;
            $jenisFile = 'revisi_akhir';
        } else {
            // Cek revisi biasa (file dengan path mengandung 'revisi' tapi bukan 'revisi_akhir')
            $revisiBiasa = $proposal->proposalRevisi->filter(function($revisi) {
                return strpos($revisi->path_file, 'revisi') !== false && 
                       strpos($revisi->path_file, 'revisi_akhir') === false;
            })->first();
            
            if ($revisiBiasa) {
                $fileProposal = $revisiBiasa;
                $jenisFile = 'revisi';
            } else if ($proposal->dokumen && $proposal->dokumen->path_file) {
                // Gunakan file proposal awal
                $fileProposal = $proposal->dokumen;
                $jenisFile = 'proposal_awal';
            }
        }

        // Ambil hanya revisi akhir untuk ditampilkan di section "File Revisi yang Dikumpulkan"
        $revisiAkhirList = $proposal->proposalRevisi->filter(function($revisi) {
            return strpos($revisi->path_file, 'revisi_akhir') !== false;
        });

        // Ambil kriteria penilaian substantif berdasarkan skim proposal
        $criteria = ProposalHelper::getSubstantifCriteria($proposal->skim);
        
        // Ambil nilai substantif dari 2 reviewer
        $nilaiSubstantif1 = null;
        $nilaiSubstantif2 = null;
        
        if ($proposal->id_reviewer_substantif_1) {
            $nilaiSubstantif1 = $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_1)->first();
        }
        
        if ($proposal->id_reviewer_substantif_2) {
            $nilaiSubstantif2 = $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_2)->first();
        }

        return view('pimpinan_pt.detail_hasil_final', compact('proposal', 'criteria', 'nilaiSubstantif1', 'nilaiSubstantif2', 'pimpinanPT', 'fileProposal', 'jenisFile', 'revisiAkhirList'));
    }

    /**
     * Update Hasil Final oleh Pimpinan PT
     */
    public function updateHasilFinal(Request $request)
    {
        Log::info('PimpinanPT updateHasilFinal called', [
            'request_data' => $request->all(),
            'user_id' => auth('operator')->id()
        ]);

        $pimpinanPT = Auth::guard('operator')->user();
        
        // Pastikan user adalah Pimpinan PT
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
            'dana_yang_didapatkan' => 'required_if:status_pendanaan,lolos|nullable|numeric|min:0',
            'catatan_final' => 'nullable|string',
            'nilai' => 'required|numeric|min:0|max:100',
            'skor' => 'required|array',
            'skor.*' => 'required|numeric|min:0|max:10',
        ]);

        try {
            DB::beginTransaction();
            
            $proposal = Proposal::findOrFail($request->proposal_id);
            
            // Validasi proposal harus berstatus pimpinan_pt
            if ($proposal->status !== 'pimpinan_pt') {
                return response()->json([
                    'success' => false,
                    'message' => 'Proposal belum siap untuk penilaian final.'
                ], 400);
            }
            
            // Ambil kriteria untuk validasi jumlah skor
            $criteria = ProposalHelper::getSubstantifCriteria($proposal->skim);
            $actualCriteriaCount = ProposalHelper::countActualCriteria($criteria);
            
            // Ambil skor per kriteria
            $skorPerKriteria = $request->input('skor', []);
            
            // Jika skor dikirim sebagai JSON string, decode terlebih dahulu
            if (is_string($skorPerKriteria)) {
                $skorPerKriteria = json_decode($skorPerKriteria, true) ?? [];
            }
            
            // Normalize skor: convert string keys to integers and sort
            $normalizedSkor = [];
            foreach ($skorPerKriteria as $key => $value) {
                $index = (int) $key;
                $normalizedSkor[$index] = (float) $value;
            }
            ksort($normalizedSkor);
            
            // Validasi jumlah skor harus sesuai dengan jumlah kriteria yang bisa di-score
            if (count($normalizedSkor) !== $actualCriteriaCount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah skor tidak sesuai dengan jumlah kriteria penilaian. Diharapkan: ' . $actualCriteriaCount . ', Diterima: ' . count($normalizedSkor)
                ], 422);
            }
            
            // Update status proposal berdasarkan status pimnas dan pendanaan
            $statusProposal = 'lolos';
            if ($request->status_pimnas === 'tidak_lolos' || $request->status_pendanaan === 'tidak_lolos') {
                $statusProposal = 'tidak_lolos';
            }
            
            // Tentukan status final yang lebih spesifik
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
            
            // Update atau buat hasil final
            HasilFinal::updateOrCreate(
                ['id_proposal' => $request->proposal_id],
                [
                    'status_pimnas' => $request->status_pimnas,
                    'status_pendanaan' => $request->status_pendanaan,
                    'dana_yang_didapatkan' => $request->input('dana_yang_didapatkan', 0),
                    'catatan_final' => $request->catatan_final,
                    'nilai' => $request->nilai,
                    'skor_per_kriteria' => $normalizedSkor,
                    'id_pimpinan_pt' => $pimpinanPT->id_pt
                ]
            );
            
            // TODO: Kirim notifikasi ke mahasiswa dan dosen
            // $notificationService = new NotificationService();
            // $notificationService->notifyHasilFinal(...);
            
            DB::commit();
            
            Log::info('Hasil final updated successfully by Pimpinan PT', [
                'proposal_id' => $request->proposal_id,
                'status_pimnas' => $request->status_pimnas,
                'status_pendanaan' => $request->status_pendanaan
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Hasil final berhasil diperbarui'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating hasil final by Pimpinan PT', [
                'error' => $e->getMessage(),
                'proposal_id' => $request->proposal_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View PDF Proposal untuk Pimpinan PT
     * Mengambil revisi akhir jika ada, jika tidak ambil dokumen original
     */
    public function viewPdf($id)
    {
        try {
            $pimpinanPT = Auth::guard('operator')->user();
            
            // Pastikan user adalah Pimpinan PT
            if ($pimpinanPT->role !== 'pimpinan_pt') {
                abort(403, 'Akses ditolak.');
            }

            $proposal = Proposal::with(['dokumen', 'proposalRevisi' => function($query) {
                $query->orderBy('tanggal_submit', 'desc');
            }])
                ->where(function($query) use ($pimpinanPT) {
                    $query->where('status', 'pimpinan_pt')
                          ->orWhereHas('hasilFinal', function($q) use ($pimpinanPT) {
                              $q->where('id_pimpinan_pt', $pimpinanPT->id_pt);
                          });
                })
                ->findOrFail($id);

            // Tentukan file proposal yang harus ditampilkan
            // Prioritas: revisi akhir > revisi biasa > proposal awal
            $fileToView = null;
            
            // Cek revisi akhir (file dengan path mengandung 'revisi_akhir')
            $revisiAkhir = $proposal->proposalRevisi->filter(function($revisi) {
                return strpos($revisi->path_file, 'revisi_akhir') !== false;
            })->first();
            
            if ($revisiAkhir) {
                $fileToView = $revisiAkhir;
            } else {
                // Cek revisi biasa (file dengan path mengandung 'revisi' tapi bukan 'revisi_akhir')
                $revisiBiasa = $proposal->proposalRevisi->filter(function($revisi) {
                    return strpos($revisi->path_file, 'revisi') !== false && 
                           strpos($revisi->path_file, 'revisi_akhir') === false;
                })->first();
                
                if ($revisiBiasa) {
                    $fileToView = $revisiBiasa;
                } else if ($proposal->dokumen && $proposal->dokumen->path_file) {
                    // Gunakan file proposal awal
                    $fileToView = $proposal->dokumen;
                }
            }
            
            if ($fileToView) {
                // Tentukan path file berdasarkan jenis
                if ($fileToView instanceof \App\Models\ProposalRevisi) {
                    // File revisi
                    $pathFile = $fileToView->path_file;
                    
                    // Cek apakah path_file sudah termasuk 'public/' atau tidak
                    if (strpos($pathFile, 'public/') === 0) {
                        $path = storage_path('app/' . $pathFile);
                    } else {
                        $path = storage_path('app/public/' . $pathFile);
                    }
                    
                    Log::info('View PDF Revisi (Pimpinan PT)', [
                        'proposal_id' => $id,
                        'revisi_id' => $fileToView->id_revisi,
                        'path_file' => $pathFile,
                        'full_path' => $path,
                        'file_exists' => file_exists($path)
                    ]);
                } else {
                    // File dokumen proposal awal
                    $pathFile = $fileToView->path_file;
                    
                    // Cek apakah path_file sudah termasuk 'public/' atau tidak
                    if (strpos($pathFile, 'public/') === 0) {
                        $path = storage_path('app/' . $pathFile);
                    } else {
                        $path = storage_path('app/public/' . $pathFile);
                    }
                    
                    Log::info('View PDF Proposal Awal (Pimpinan PT)', [
                        'proposal_id' => $id,
                        'dokumen_id' => $fileToView->id_dokumen,
                        'path_file' => $pathFile,
                        'full_path' => $path,
                        'file_exists' => file_exists($path)
                    ]);
                }
                
                if (!file_exists($path)) {
                    $altPath = storage_path('app/' . $pathFile);
                    if (file_exists($altPath)) {
                        $path = $altPath;
                    } else {
                        abort(404, 'File tidak ditemukan: ' . $path);
                    }
                }
                
                // Tentukan nama file untuk response
                $fileName = $fileToView instanceof \App\Models\ProposalRevisi 
                    ? $fileToView->nama_file 
                    : basename($path);
                
                return response()->file($path, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $fileName . '"'
                ]);
            } else {
                abort(404, 'Dokumen proposal tidak ditemukan.');
            }
        } catch (\Exception $e) {
            Log::error('Error in viewPdf (Pimpinan PT): ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Terjadi kesalahan saat memuat PDF: ' . $e->getMessage());
        }
    }

    /**
     * Manajemen Akun - Pimpinan PT dapat mengelola semua jenis user
     */
    public function manageAccounts(Request $request)
    {
        $pimpinanPT = Auth::guard('operator')->user();
        
        // Pastikan user adalah Pimpinan PT
        if ($pimpinanPT->role !== 'pimpinan_pt') {
            abort(403, 'Akses ditolak. Hanya Pimpinan PT yang dapat mengakses halaman ini.');
        }

        // Filter untuk Mahasiswa
        $query = Mahasiswa::query();
        
        // Filter berdasarkan Fakultas
        if ($request->filled('filter_fakultas')) {
            $fakultas = Fakultas::find($request->filter_fakultas);
            if ($fakultas) {
                $query->where('fakultas_mhs', $fakultas->nama_fakultas);
            }
        }
        
        // Filter berdasarkan Prodi
        if ($request->filled('filter_prodi')) {
            $prodi = Prodi::find($request->filter_prodi);
            if ($prodi) {
                $query->where('prodi_mhs', $prodi->nama_prodi);
            }
        }
        
        // Filter berdasarkan NIM (search)
        if ($request->filled('filter_nim')) {
            $query->where('nim', 'like', '%' . $request->filter_nim . '%');
        }
        
        $mahasiswas = $query->orderBy('created_at', 'desc')->get();
        
        // Data lainnya
        $dosens = Dosen::orderBy('created_at', 'desc')->limit(100)->get();
        $reviewers = Reviewer::orderBy('created_at', 'desc')->limit(100)->get();
        $operators = PT::where('role', 'operator')->orderBy('created_at', 'desc')->limit(100)->get();
        $pimpinanPTs = PT::where('role', 'pimpinan_pt')->orderBy('created_at', 'desc')->limit(100)->get();
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();

        return view('pimpinan_pt.manajemen_akun', compact('mahasiswas', 'dosens', 'reviewers', 'operators', 'pimpinanPTs', 'fakultas', 'prodis'));
    }

    /**
     * Store Account - Support semua jenis user
     */
    public function storeAccount(Request $request, $type)
    {
        $pimpinanPT = Auth::guard('operator')->user();
        
        // Pastikan user adalah Pimpinan PT
        if ($pimpinanPT->role !== 'pimpinan_pt') {
            return back()->with('error', 'Akses ditolak.');
        }

        switch ($type) {
            case 'mahasiswa':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nim' => 'required|string|max:20|unique:mahasiswas,nim',
                    'email' => 'required|email|max:255|unique:mahasiswas,email_mhs',
                    'no_hp' => 'required|string|max:15',
                    'prodi' => 'required|exists:prodis,id_prodi',
                    'fakultas' => 'required|exists:fakultas,id_fakultas',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                $prodi = Prodi::find($request->prodi);
                $fakultas = Fakultas::find($request->fakultas);
                Mahasiswa::create([
                    'nama_mhs' => $request->nama,
                    'nim' => $request->nim,
                    'email_mhs' => $request->email,
                    'no_hp_mhs' => $request->no_hp,
                    'prodi_mhs' => $prodi->nama_prodi,
                    'fakultas_mhs' => $fakultas->nama_fakultas,
                    'password' => Hash::make($request->password),
                    'is_active' => true,
                ]);
                break;
            case 'dosen':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:dosens,email_dosen',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                Dosen::create([
                    'nama_dosen' => $request->nama,
                    'email_dosen' => $request->email,
                    'no_hp_dosen' => $request->no_hp,
                    'password' => Hash::make($request->password),
                    'is_active' => true,
                ]);
                break;
            case 'reviewer':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:reviewers,email_reviewer',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                Reviewer::create([
                    'nama_reviewer' => $request->nama,
                    'email_reviewer' => $request->email,
                    'no_hp_reviewer' => $request->no_hp,
                    'password' => Hash::make($request->password),
                    'is_active' => true,
                ]);
                break;
            case 'operator':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:pts,email_pt',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                PT::create([
                    'nama_pt' => $request->nama,
                    'email_pt' => $request->email,
                    'no_hp_pt' => $request->no_hp,
                    'password' => Hash::make($request->password),
                    'role' => 'operator',
                    'is_active' => true,
                ]);
                break;
            case 'pimpinan_pt':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:pts,email_pt',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                PT::create([
                    'nama_pt' => $request->nama,
                    'email_pt' => $request->email,
                    'no_hp_pt' => $request->no_hp,
                    'password' => Hash::make($request->password),
                    'role' => 'pimpinan_pt',
                    'is_active' => true,
                ]);
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }
        return redirect()->route('pimpinan_pt.manage.accounts')->with('success', 'Akun berhasil dibuat.');
    }

    /**
     * Update Account - Support semua jenis user
     */
    public function updateAccount(Request $request, $type, $id)
    {
        $pimpinanPT = Auth::guard('operator')->user();
        
        // Pastikan user adalah Pimpinan PT
        if ($pimpinanPT->role !== 'pimpinan_pt') {
            return back()->with('error', 'Akses ditolak.');
        }

        switch ($type) {
            case 'mahasiswa':
                $mahasiswa = Mahasiswa::findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nim' => 'required|string|max:20|unique:mahasiswas,nim,' . $mahasiswa->id_mahasiswa . ',id_mahasiswa',
                    'email' => 'required|email|max:255|unique:mahasiswas,email_mhs,' . $mahasiswa->id_mahasiswa . ',id_mahasiswa',
                    'no_hp' => 'required|string|max:15',
                    'prodi' => 'required|exists:prodis,id_prodi',
                    'fakultas' => 'required|exists:fakultas,id_fakultas',
                    'password' => 'nullable|string|min:8|confirmed',
                ]);
                $prodi = Prodi::find($request->prodi);
                $fakultas = Fakultas::find($request->fakultas);
                $updateData = [
                    'nama_mhs' => $request->nama,
                    'nim' => $request->nim,
                    'email_mhs' => $request->email,
                    'no_hp_mhs' => $request->no_hp,
                    'prodi_mhs' => $prodi->nama_prodi,
                    'fakultas_mhs' => $fakultas->nama_fakultas,
                ];
                if ($request->filled('password')) {
                    $updateData['password'] = Hash::make($request->password);
                }
                $mahasiswa->update($updateData);
                break;
            case 'dosen':
                $dosen = Dosen::findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:dosens,email_dosen,' . $dosen->id_dosen . ',id_dosen',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'nullable|string|min:8|confirmed',
                ]);
                $updateData = [
                    'nama_dosen' => $request->nama,
                    'email_dosen' => $request->email,
                    'no_hp_dosen' => $request->no_hp,
                ];
                if ($request->filled('password')) {
                    $updateData['password'] = Hash::make($request->password);
                }
                $dosen->update($updateData);
                break;
            case 'reviewer':
                $reviewer = Reviewer::findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:reviewers,email_reviewer,' . $reviewer->id_reviewer . ',id_reviewer',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'nullable|string|min:8|confirmed',
                ]);
                $updateData = [
                    'nama_reviewer' => $request->nama,
                    'email_reviewer' => $request->email,
                    'no_hp_reviewer' => $request->no_hp,
                ];
                if ($request->filled('password')) {
                    $updateData['password'] = Hash::make($request->password);
                }
                $reviewer->update($updateData);
                break;
            case 'operator':
                $pt = PT::where('id_pt', $id)->where('role', 'operator')->firstOrFail();
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:pts,email_pt,' . $pt->id_pt . ',id_pt',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'nullable|string|min:8|confirmed',
                ]);
                $updateData = [
                    'nama_pt' => $request->nama,
                    'email_pt' => $request->email,
                    'no_hp_pt' => $request->no_hp,
                ];
                if ($request->filled('password')) {
                    $updateData['password'] = Hash::make($request->password);
                }
                $pt->update($updateData);
                break;
            case 'pimpinan_pt':
                $pt = PT::where('id_pt', $id)->where('role', 'pimpinan_pt')->firstOrFail();
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:pts,email_pt,' . $pt->id_pt . ',id_pt',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'nullable|string|min:8|confirmed',
                ]);
                $updateData = [
                    'nama_pt' => $request->nama,
                    'email_pt' => $request->email,
                    'no_hp_pt' => $request->no_hp,
                ];
                if ($request->filled('password')) {
                    $updateData['password'] = Hash::make($request->password);
                }
                $pt->update($updateData);
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }
        return redirect()->route('pimpinan_pt.manage.accounts')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Delete Account - Support semua jenis user
     */
    public function deleteAccount($type, $id)
    {
        $pimpinanPT = Auth::guard('operator')->user();
        
        // Pastikan user adalah Pimpinan PT
        if ($pimpinanPT->role !== 'pimpinan_pt') {
            return back()->with('error', 'Akses ditolak.');
        }

        // Jangan izinkan menghapus diri sendiri untuk Pimpinan PT
        if ($type === 'pimpinan_pt' && $pimpinanPT->id_pt == $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        try {
            switch ($type) {
                case 'mahasiswa':
                    Mahasiswa::where('id_mahasiswa', $id)->delete();
                    break;
                case 'dosen':
                    Dosen::where('id_dosen', $id)->delete();
                    break;
                case 'reviewer':
                    Reviewer::where('id_reviewer', $id)->delete();
                    break;
                case 'operator':
                    PT::where('id_pt', $id)->where('role', 'operator')->delete();
                    break;
                case 'pimpinan_pt':
                    PT::where('id_pt', $id)->where('role', 'pimpinan_pt')->delete();
                    break;
                default:
                    return back()->with('error', 'Tipe akun tidak dikenal.');
            }
            return redirect()->route('pimpinan_pt.manage.accounts')->with('success', 'Akun berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }

    /**
     * Bulk Delete Mahasiswa
     */
    public function bulkDeleteMahasiswa(Request $request)
    {
        $pimpinanPT = Auth::guard('operator')->user();
        
        // Pastikan user adalah Pimpinan PT
        if ($pimpinanPT->role !== 'pimpinan_pt') {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:mahasiswas,id_mahasiswa',
        ]);
        
        try {
            $count = Mahasiswa::whereIn('id_mahasiswa', $request->ids)->delete();
            return redirect()->route('pimpinan_pt.manage.accounts')->with('success', "Berhasil menghapus {$count} akun mahasiswa.");
        } catch (\Exception $e) {
            return redirect()->route('pimpinan_pt.manage.accounts')->with('error', 'Gagal menghapus akun mahasiswa: ' . $e->getMessage());
        }
    }
}



