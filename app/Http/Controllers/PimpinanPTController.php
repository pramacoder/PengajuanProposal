<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\Proposal;
use App\Models\HasilFinal;
use App\Models\HasilSemiFinal;
use App\Models\Dokumen;
use App\Models\NilaiSubstantif;
use App\Models\ProposalRevisi;
use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Helpers\ProposalHelper;
use App\Helpers\StorageHelper;

class PimpinanPTController extends Controller
{
    /**
     * Dashboard Pimpinan PT - Menampilkan proposal yang perlu dinilai final
     */
    public function dashboard()
    {
        $pimpinanPT = auth()->user();
        
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
        $pimpinanPT = auth()->user();
        
        // Pastikan user adalah Pimpinan PT
        if ($pimpinanPT->role !== 'pimpinan_pt') {
            abort(403, 'Akses ditolak. Hanya Pimpinan PT yang dapat mengakses halaman ini.');
        }

        $proposal = Proposal::with([
            'mahasiswa',
            'dokumen',
            'hasilFinal',
            'hasilSemiFinal',
            'nilaiSubstantif.reviewer',
            'proposalRevisi' => function($query) {
                // Prioritas: revisi_akhir dulu, baru revisi_biasa
                // Cek apakah kolom jenis_revisi ada di database
                if (Schema::hasColumn('proposal_revisi', 'jenis_revisi')) {
                    $query->orderByRaw("CASE WHEN jenis_revisi = 'revisi_akhir' THEN 0 ELSE 1 END")
                          ->orderBy('tanggal_submit', 'desc');
                } else {
                    // Fallback jika kolom belum ada (migration belum dijalankan)
                    // Gunakan filter path_file sebagai fallback
                    $query->orderByRaw("CASE WHEN path_file LIKE '%revisi_akhir%' THEN 0 ELSE 1 END")
                          ->orderBy('tanggal_submit', 'desc');
                }
            }
        ])
            ->where(function($query) use ($pimpinanPT) {
                $query->where('status', 'pimpinan_pt')
                      ->orWhereHas('hasilFinal', function($q) use ($pimpinanPT) {
                          $q->where('id_pimpinan_pt', $pimpinanPT->id);
                      });
            })
            ->findOrFail($id);

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

        // Ambil proposal terbaru dari mahasiswa yang sama (selain proposal yang sedang dilihat)
        $latestProposals = Proposal::with(['dokumen', 'hasilFinal'])
            ->where('id_mahasiswa', $proposal->id_mahasiswa)
            ->where('id_proposal', '!=', $proposal->id_proposal)
            ->orderBy('tanggal_pengajuan', 'desc')
            ->limit(5)
            ->get();

        return view('pimpinan_pt.detail_hasil_final', compact('proposal', 'criteria', 'nilaiSubstantif1', 'nilaiSubstantif2', 'pimpinanPT', 'latestProposals'));
    }

    /**
     * Update Hasil Final oleh Pimpinan PT
     */
    public function updateHasilFinal(Request $request)
    {
        Log::info('PimpinanPT updateHasilFinal called', [
            'request_data' => $request->all(),
            'user_id' => auth()->id()
        ]);

        $request->merge([
            'dana_yang_didapatkan' => \App\Helpers\ProposalHelper::parseAngka($request->input('dana_yang_didapatkan')),
        ]);

        $pimpinanPT = auth()->user();
        
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
            'dana_yang_didapatkan' => 'required_if:status_pendanaan,lolos|nullable|numeric|min:0|max:15000000',
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
            
            // Handle dana_yang_didapatkan
            $danaYangDidapatkan = 0;
            if ($request->status_pendanaan === 'lolos') {
                $danaInput = $request->input('dana_yang_didapatkan', 0);
                // Convert to numeric if string (remove any formatting)
                if (is_string($danaInput)) {
                    $danaInput = preg_replace('/[^0-9.]/', '', $danaInput);
                }
                $danaYangDidapatkan = (float) $danaInput;
                // Ensure it's within valid range
                if ($danaYangDidapatkan < 0) {
                    $danaYangDidapatkan = 0;
                }
                if ($danaYangDidapatkan > 15000000) {
                    $danaYangDidapatkan = 15000000;
                }
            }
            
            // Update atau buat hasil final
            HasilFinal::updateOrCreate(
                ['id_proposal' => $request->proposal_id],
                [
                    'status_pimnas' => $request->status_pimnas,
                    'status_pendanaan' => $request->status_pendanaan,
                    'dana_yang_didapatkan' => $danaYangDidapatkan,
                    'catatan_final' => $request->catatan_final,
                    'nilai' => $request->nilai,
                    'skor_per_kriteria' => $normalizedSkor,
                    'id_pimpinan_pt' => $pimpinanPT->id
                ]
            );
            
            // Kirim notifikasi ke mahasiswa
            try {
                $notificationService = app(\App\Services\NotificationService::class);
                $danaYangDidapatkan = 0;
                if ($request->status_pendanaan === 'lolos') {
                    $danaInput = $request->input('dana_yang_didapatkan', 0);
                    if (is_string($danaInput)) {
                        $danaInput = preg_replace('/[^0-9.]/', '', $danaInput);
                    }
                    $danaYangDidapatkan = (float) $danaInput;
                    if ($danaYangDidapatkan < 0) {
                        $danaYangDidapatkan = 0;
                    }
                    if ($danaYangDidapatkan > 15000000) {
                        $danaYangDidapatkan = 15000000;
                    }
                }
                $notificationService->notifyHasilFinalLengkap(
                    $proposal->fresh(),
                    $request->status_pimnas,
                    $request->status_pendanaan,
                    $request->nilai,
                    $danaYangDidapatkan,
                    $request->catatan_final ?? null
                );
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi hasil final: ' . $e->getMessage());
            }
            
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
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            
            Log::error('Validation error updating hasil final by Pimpinan PT', [
                'errors' => $e->errors(),
                'proposal_id' => $request->proposal_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', array_map(function($errors) {
                    return implode(', ', $errors);
                }, $e->errors())),
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating hasil final by Pimpinan PT', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'proposal_id' => $request->proposal_id,
                'request_data' => $request->except(['_token', 'skor'])
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan hasil final: ' . $e->getMessage()
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
            $pimpinanPT = auth()->user();
            
            // Pastikan user adalah Pimpinan PT
            if ($pimpinanPT->role !== 'pimpinan_pt') {
                abort(403, 'Akses ditolak.');
            }

            $proposal = Proposal::with(['dokumen', 'proposalRevisi' => function($query) {
                // Prioritas: revisi_akhir dulu, baru revisi_biasa
                // Cek apakah kolom jenis_revisi ada di database
                if (Schema::hasColumn('proposal_revisi', 'jenis_revisi')) {
                    $query->orderByRaw("CASE WHEN jenis_revisi = 'revisi_akhir' THEN 0 ELSE 1 END")
                          ->orderBy('tanggal_submit', 'desc');
                } else {
                    // Fallback jika kolom belum ada (migration belum dijalankan)
                    // Gunakan filter path_file sebagai fallback
                    $query->orderByRaw("CASE WHEN path_file LIKE '%revisi_akhir%' THEN 0 ELSE 1 END")
                      ->orderBy('tanggal_submit', 'desc');
                }
            }])
                ->where(function($query) use ($pimpinanPT) {
                    $query->where('status', 'pimpinan_pt')
                          ->orWhereHas('hasilFinal', function($q) use ($pimpinanPT) {
                              $q->where('id_pimpinan_pt', $pimpinanPT->id);
                          });
                })
                ->findOrFail($id);

            // Prioritas: Ambil revisi terakhir (terbaru berdasarkan tanggal_submit) jika ada, jika tidak ambil dokumen original
            $revisiAkhir = $proposal->proposalRevisi->first();
            
            if ($revisiAkhir) {
                return StorageHelper::response($revisiAkhir->path_file, $revisiAkhir->nama_file);
            } else {
                // Gunakan dokumen original
                if (!$proposal->dokumen || !$proposal->dokumen->path_file) {
                    abort(404, 'Dokumen tidak ditemukan.');
                }

                $pathFile = $proposal->dokumen->path_file;
                $filename = basename($pathFile);

                return StorageHelper::response($pathFile, $filename);
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
        $pimpinanPT = auth()->user();

        if ($pimpinanPT->role !== 'pimpinan_pt') {
            abort(403, 'Akses ditolak. Hanya Pimpinan PT yang dapat mengakses halaman ini.');
        }

        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();

        $hasFilter = $request->filled('filter_fakultas') ||
                     $request->filled('filter_prodi') ||
                     $request->filled('filter_nim') ||
                     $request->filled('filter_nama') ||
                     $request->filled('filter_nama_dosen') ||
                     $request->filled('filter_nama_reviewer') ||
                     $request->filled('filter_nama_operator') ||
                     $request->filled('filter_nama_pimpinan_pt');

        $mahasiswaQuery = User::mahasiswa();
        if ($hasFilter) {
            if ($request->filled('filter_fakultas')) {
                $fakultasModel = Fakultas::find($request->filter_fakultas);
                if ($fakultasModel) {
                    $mahasiswaQuery->whereRaw("metadata->>'fakultas_name' = ?", [$fakultasModel->nama_fakultas]);
                }
            }
            if ($request->filled('filter_prodi')) {
                $prodiModel = Prodi::find($request->filter_prodi);
                if ($prodiModel) {
                    $mahasiswaQuery->whereRaw("metadata->>'prodi_name' = ?", [$prodiModel->nama_prodi]);
                }
            }
            if ($request->filled('filter_nim')) {
                $mahasiswaQuery->where('identifier', 'like', '%' . $request->filter_nim . '%');
            }
            if ($request->filled('filter_nama')) {
                $mahasiswaQuery->where('name', 'like', '%' . $request->filter_nama . '%');
            }
        }
        $mahasiswas = $hasFilter ? $mahasiswaQuery->orderBy('created_at', 'desc')->get() : collect();

        $dosenQuery = User::dosen();
        if ($hasFilter && $request->filled('filter_nama_dosen')) {
            $dosenQuery->where('name', 'like', '%' . $request->filter_nama_dosen . '%');
        }
        $dosens = $hasFilter ? $dosenQuery->orderBy('created_at', 'desc')->get() : collect();

        $reviewerQuery = User::reviewer();
        if ($hasFilter && $request->filled('filter_nama_reviewer')) {
            $reviewerQuery->where('name', 'like', '%' . $request->filter_nama_reviewer . '%');
        }
        $reviewers = $hasFilter ? $reviewerQuery->orderBy('created_at', 'desc')->get() : collect();

        $operatorQuery = User::operator();
        if ($hasFilter && $request->filled('filter_nama_operator')) {
            $operatorQuery->where('name', 'like', '%' . $request->filter_nama_operator . '%');
        }
        $operators = $hasFilter ? $operatorQuery->orderBy('created_at', 'desc')->get() : collect();

        $pimpinanPTQuery = User::pimpinanPT();
        if ($hasFilter && $request->filled('filter_nama_pimpinan_pt')) {
            $pimpinanPTQuery->where('name', 'like', '%' . $request->filter_nama_pimpinan_pt . '%');
        }
        $pimpinanPTs = $hasFilter ? $pimpinanPTQuery->orderBy('created_at', 'desc')->get() : collect();

        return view('pimpinan_pt.manajemen_akun', compact('mahasiswas', 'dosens', 'reviewers', 'operators', 'pimpinanPTs', 'fakultas', 'prodis', 'hasFilter'));
    }

    /**
     * Store Account - Support semua jenis user
     */
    public function storeAccount(Request $request, $type)
    {
        $pimpinanPT = auth()->user();

        if ($pimpinanPT->role !== 'pimpinan_pt') {
            return back()->with('error', 'Akses ditolak.');
        }

        $baseRules = [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'no_hp' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ];

        $metadata = [];

        switch ($type) {
            case 'mahasiswa':
                $request->validate(array_merge($baseRules, [
                    'nim' => 'required|string|max:20|unique:users,identifier',
                    'prodi' => 'required|exists:prodis,id_prodi',
                    'fakultas' => 'required|exists:fakultas,id_fakultas',
                ]));
                $prodi = Prodi::find($request->prodi);
                $fakultas = Fakultas::find($request->fakultas);
                $metadata = [
                    'prodi_name' => $prodi->nama_prodi,
                    'fakultas_name' => $fakultas->nama_fakultas,
                    'prodi_id' => $prodi->id_prodi,
                    'fakultas_id' => $fakultas->id_fakultas,
                ];
                $identifier = $request->nim;
                $role = 'mahasiswa';
                break;
            case 'dosen':
                $request->validate(array_merge($baseRules, [
                    'identifier' => 'nullable|string|max:20|unique:users,identifier',
                ]));
                $identifier = $request->identifier;
                $role = 'dosen';
                break;
            case 'reviewer':
                $request->validate(array_merge($baseRules, [
                    'identifier' => 'nullable|string|max:20|unique:users,identifier',
                ]));
                $identifier = $request->identifier;
                $role = 'reviewer';
                break;
            case 'operator':
                $request->validate($baseRules);
                $identifier = null;
                $role = 'operator';
                break;
            case 'pimpinan_pt':
                $request->validate($baseRules);
                $identifier = null;
                $role = 'pimpinan_pt';
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }

        User::create([
            'name' => $request->nama,
            'identifier' => $identifier,
            'email' => $request->email,
            'phone' => $request->no_hp,
            'password' => Hash::make($request->password),
            'role' => $role,
            'is_active' => true,
            'metadata' => $metadata,
        ]);

        return redirect()->route('pimpinan_pt.manage.accounts')->with('success', 'Akun berhasil dibuat.');
    }

    /**
     * Update Account - Support semua jenis user
     */
    public function updateAccount(Request $request, $type, $id)
    {
        $pimpinanPT = auth()->user();

        if ($pimpinanPT->role !== 'pimpinan_pt') {
            return back()->with('error', 'Akses ditolak.');
        }

        $user = User::where('role', $type)->findOrFail($id);

        $baseRules = [
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'no_hp' => 'required|string|max:15',
            'password' => 'nullable|string|min:8|confirmed',
        ];

        switch ($type) {
            case 'mahasiswa':
                $request->validate(array_merge($baseRules, [
                    'nim' => 'required|string|max:20|unique:users,identifier,' . $id,
                    'prodi' => 'required|exists:prodis,id_prodi',
                    'fakultas' => 'required|exists:fakultas,id_fakultas',
                ]));
                $prodi = Prodi::find($request->prodi);
                $fakultas = Fakultas::find($request->fakultas);
                $metadata = array_merge($user->metadata ?? [], [
                    'prodi_name' => $prodi->nama_prodi,
                    'fakultas_name' => $fakultas->nama_fakultas,
                    'prodi_id' => $prodi->id_prodi,
                    'fakultas_id' => $fakultas->id_fakultas,
                ]);
                $user->identifier = $request->nim;
                $user->metadata = $metadata;
                break;
            case 'dosen':
            case 'reviewer':
                $request->validate(array_merge($baseRules, [
                    'identifier' => 'nullable|string|max:20|unique:users,identifier,' . $id,
                ]));
                $user->identifier = $request->identifier;
                break;
            case 'operator':
            case 'pimpinan_pt':
                $request->validate($baseRules);
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }

        $user->name = $request->nama;
        $user->email = $request->email;
        $user->phone = $request->no_hp;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('pimpinan_pt.manage.accounts')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Delete Account - Support semua jenis user
     */
    public function deleteAccount($type, $id)
    {
        $pimpinanPT = auth()->user();

        if ($pimpinanPT->role !== 'pimpinan_pt') {
            return back()->with('error', 'Akses ditolak.');
        }

        if ($pimpinanPT->id == $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        try {
            User::where('id', $id)->where('role', $type)->delete();
            return redirect()->route('pimpinan_pt.manage.accounts')->with('success', 'Akun berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }

    public function bulkDeleteMahasiswa(Request $request)
    {
        $pimpinanPT = auth()->user();

        if ($pimpinanPT->role !== 'pimpinan_pt') {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        try {
            $count = User::where('role', 'mahasiswa')->whereIn('id', $request->ids)->delete();
            return redirect()->route('pimpinan_pt.manage.accounts')->with('success', "Berhasil menghapus {$count} akun mahasiswa.");
        } catch (\Exception $e) {
            return redirect()->route('pimpinan_pt.manage.accounts')->with('error', 'Gagal menghapus akun mahasiswa: ' . $e->getMessage());
        }
    }
}



