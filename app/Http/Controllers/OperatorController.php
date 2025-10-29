<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Proposal;
use App\Models\Dosen;
use App\Models\Reviewer;
use App\Models\RuangKontrol;
use App\Models\HasilFinal;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use App\Models\Dokumen;
use App\Models\PT;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\ProposalRevisi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Services\NotificationService;

class OperatorController extends Controller
{
    public function dashboard()
    {
        // Ambil data untuk dashboard
        $tahun = request('tahun', '2025');
        
        // Data untuk PKM-8 Bidang
        $pkm8Bidang = $this->getPKM8BidangData($tahun);
        
        // Data untuk PKM Insentif
        $pkmInsentif = $this->getPKMInsentifData($tahun);
        
        // Total keseluruhan
        $totalKeseluruhan = $pkm8Bidang->sum('jumlah');
        $totalInsentif = $pkmInsentif->sum('jumlah');
        
        // Data untuk grafik
        $chartData = $this->getChartData($tahun);
        
        // Data perangkingan proposal terbaik
        $topProposals = $this->getTopProposals($tahun);
        
        return view('operator.dashboard', compact('pkm8Bidang', 'pkmInsentif', 'totalKeseluruhan', 'totalInsentif', 'tahun', 'chartData', 'topProposals'));
    }

    public function pilihReviewer()
    {
        $tahun = request('tahun', '2025');
        $filter = request('filter', 'all');
        
        // Ambil proposal yang sudah divalidasi dosen dan BELUM memiliki reviewer
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif'])
            ->where('status_validasi', 'valid')
            ->where(function($query) {
                $query->whereNull('id_reviewer_administratif')
                      ->orWhereNull('id_reviewer_substantif_1')
                      ->orWhereNull('id_reviewer_substantif_2');
            })
            ->whereYear('tanggal_pengajuan', $tahun)
            ->when($filter !== 'all', function($query) use ($filter) {
                $query->where('skim', $filter);
            })
            ->get();
        
        // Debug: Log jumlah proposal yang ditemukan
        \Log::info('Proposals found for pilih reviewer:', [
            'count' => $proposals->count(),
            'tahun' => $tahun,
            'filter' => $filter
        ]);
        
        // Ambil semua reviewer yang tersedia
        $reviewers = Reviewer::where('is_active', true)->get();
        
        return view('operator.pilih_reviewer', compact('proposals', 'reviewers', 'tahun', 'filter'));
    }

    public function searchReviewers(Request $request)
    {
        $search = $request->get('search', '');
        
        if (strlen($search) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal 2 karakter untuk pencarian'
            ]);
        }
        
        try {
            $reviewers = Reviewer::where('is_active', true)
                ->where(function($query) use ($search) {
                    $query->where('nama_reviewer', 'LIKE', "%{$search}%")
                          ->orWhere('email_reviewer', 'LIKE', "%{$search}%");
                })
                ->select('id_reviewer', 'nama_reviewer', 'email_reviewer', 'no_hp_reviewer')
                ->limit(10)
                ->get();
            
            return response()->json([
                'success' => true,
                'reviewers' => $reviewers
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari reviewer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignReviewer(Request $request)
    {
        \Log::info('Assigning reviewers to proposal', [
            'proposal_id' => $request->proposal_id,
            'reviewer_administratif' => $request->reviewer_administratif,
            'reviewer_substantif_1' => $request->reviewer_substantif_1,
            'reviewer_substantif_2' => $request->reviewer_substantif_2,
            'request_data' => $request->all()
        ]);

        try {
            $request->validate([
                'proposal_id' => 'required|exists:proposals,id_proposal',
                'reviewer_administratif' => 'required|exists:reviewers,id_reviewer',
                'reviewer_substantif_1' => 'required|exists:reviewers,id_reviewer',
                'reviewer_substantif_2' => 'required|exists:reviewers,id_reviewer|different:reviewer_substantif_1'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed for reviewer assignment', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', array_flatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            // Update proposal status dan assignment reviewer
            $proposal = Proposal::findOrFail($request->proposal_id);
            
            \Log::info('Updating proposal', [
                'proposal_id' => $proposal->id_proposal,
                'current_status' => $proposal->status,
                'current_status_validasi' => $proposal->status_validasi,
                'new_status' => 'review_administratif'
            ]);
            
            // Update proposal dengan reviewer assignment
            $updateData = [
                'status' => 'review_administratif',
                'id_reviewer_administratif' => $request->reviewer_administratif,
                'id_reviewer_substantif_1' => $request->reviewer_substantif_1,
                'id_reviewer_substantif_2' => $request->reviewer_substantif_2
            ];
            
            $proposal->update($updateData);
            
            \Log::info('Proposal updated successfully', [
                'proposal_id' => $proposal->id_proposal,
                'new_status' => $proposal->status,
                'reviewers_assigned' => [
                    'administratif' => $proposal->id_reviewer_administratif,
                    'substantif_1' => $proposal->id_reviewer_substantif_1,
                    'substantif_2' => $proposal->id_reviewer_substantif_2
                ]
            ]);
            
            // Hapus assignment lama jika ada
            NilaiAdministratif::where('id_proposal', $request->proposal_id)->delete();
            NilaiSubstantif::where('id_proposal', $request->proposal_id)->delete();
            
            \Log::info('Old review records deleted', [
                'proposal_id' => $request->proposal_id
            ]);
            
            // Buat assignment baru di tabel nilai
            $nilaiAdmin = NilaiAdministratif::create([
                'id_proposal' => $request->proposal_id,
                'id_reviewer' => $request->reviewer_administratif,
                'note_administratif' => null, // Tidak ada note default, reviewer harus mengisi
                'checklist' => json_encode([])
            ]);
            
            $nilaiSub1 = NilaiSubstantif::create([
                'id_proposal' => $request->proposal_id,
                'id_reviewer' => $request->reviewer_substantif_1,
                'note_substantif' => null // Tidak ada note default, reviewer harus mengisi
            ]);
            
            $nilaiSub2 = NilaiSubstantif::create([
                'id_proposal' => $request->proposal_id,
                'id_reviewer' => $request->reviewer_substantif_2,
                'note_substantif' => null // Tidak ada note default, reviewer harus mengisi
            ]);
            
            \Log::info('Created review records', [
                'nilai_admin_id' => $nilaiAdmin->id,
                'nilai_sub1_id' => $nilaiSub1->id,
                'nilai_sub2_id' => $nilaiSub2->id
            ]);
            
            DB::commit();
            
            \Log::info('Reviewer assignment completed successfully', [
                'proposal_id' => $request->proposal_id,
                'reviewers' => [
                    'administratif' => $request->reviewer_administratif,
                    'substantif_1' => $request->reviewer_substantif_1,
                    'substantif_2' => $request->reviewer_substantif_2
                ]
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Reviewer berhasil ditugaskan',
                'data' => [
                    'proposal_id' => $request->proposal_id,
                    'status' => 'review_administratif',
                    'reviewers' => [
                        'administratif' => $request->reviewer_administratif,
                        'substantif_1' => $request->reviewer_substantif_1,
                        'substantif_2' => $request->reviewer_substantif_2
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error assigning reviewers', [
                'proposal_id' => $request->proposal_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function getAssignedProposals()
    {
        $tahun = request('tahun', '2025');
        $filter = request('filter', 'all');
        
        \Log::info('Getting assigned proposals', [
            'tahun' => $tahun,
            'filter' => $filter
        ]);
        
        try {
            // Ambil proposal yang sudah ditugaskan reviewer (semua field reviewer harus terisi)
            $proposals = Proposal::with([
                'mahasiswa', 
                'dokumen', 
                'reviewerAdministratif',
                'reviewerSubstantif1',
                'reviewerSubstantif2'
            ])
            ->where('status_validasi', 'valid')
            ->whereNotNull('id_reviewer_administratif')
            ->whereNotNull('id_reviewer_substantif_1')
            ->whereNotNull('id_reviewer_substantif_2')
            ->whereYear('tanggal_pengajuan', $tahun)
            ->when($filter !== 'all', function($query) use ($filter) {
                $query->where('skim', $filter);
            })
            ->orderBy('updated_at', 'desc')
            ->get();
            
            \Log::info('Found assigned proposals', [
                'count' => $proposals->count(),
                'proposals' => $proposals->map(function($p) {
                    return [
                        'id' => $p->id_proposal,
                        'status' => $p->status,
                        'skim' => $p->skim,
                        'has_reviewers' => [
                            'administratif' => !is_null($p->id_reviewer_administratif),
                            'substantif_1' => !is_null($p->id_reviewer_substantif_1),
                            'substantif_2' => !is_null($p->id_reviewer_substantif_2)
                        ]
                    ];
                })
            ]);
            
            return response()->json([
                'success' => true,
                'proposals' => $proposals,
                'count' => $proposals->count()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting assigned proposals', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ruangKontrol()
    {
        $ruangKontrol = RuangKontrol::first();
        
        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::create([
                'status_pendaftaran' => 'tertutup',
                'status_perbaikan' => 'tertutup',
                'id_pt' => auth('operator')->id()
            ]);
        }
        
        return view('operator.ruang_kontrol', compact('ruangKontrol'));
    }

    /**
     * Get current active phase status
     */
    public function getActivePhase()
    {
        $ruangKontrol = RuangKontrol::first();
        
        if (!$ruangKontrol) {
            return response()->json([
                'success' => true,
                'active_phase' => null,
                'status_pendaftaran' => 'tertutup',
                'status_perbaikan' => 'tertutup'
            ]);
        }
        
        $activePhase = null;
        if ($ruangKontrol->status_pendaftaran === 'terbuka') {
            $activePhase = 'pendaftaran';
        } elseif ($ruangKontrol->status_perbaikan === 'terbuka') {
            $activePhase = 'perbaikan';
        }
        
        return response()->json([
            'success' => true,
            'active_phase' => $activePhase,
            'status_pendaftaran' => $ruangKontrol->status_pendaftaran,
            'status_perbaikan' => $ruangKontrol->status_perbaikan
        ]);
    }

    public function updateRuangKontrol(Request $request)
    {
        // Log request untuk debugging
        \Log::info('Update Ruang Kontrol Request', [
            'data' => $request->all(),
            'user_id' => auth('operator')->id()
        ]);

        $request->validate([
            'status_pendaftaran' => 'required|in:terbuka,tertutup',
            'status_perbaikan' => 'required|in:terbuka,tertutup',
            'tanggal_pendaftaran_mulai' => 'required|date',
            'tanggal_pendaftaran_selesai' => 'required|date|after:tanggal_pendaftaran_mulai',
            'tanggal_perbaikan_mulai' => 'required|date',
            'tanggal_perbaikan_selesai' => 'required|date|after:tanggal_perbaikan_mulai'
        ]);

        // Implement mutual exclusive logic
        $statusPendaftaran = $request->status_pendaftaran;
        $statusPerbaikan = $request->status_perbaikan;
        
        // If both phases are set to 'terbuka', this is invalid
        if ($statusPendaftaran === 'terbuka' && $statusPerbaikan === 'terbuka') {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat membuka kedua fase secara bersamaan. Hanya satu fase yang dapat aktif pada satu waktu.'
            ], 422);
        }

        try {
            $ruangKontrol = RuangKontrol::first();
            
            if (!$ruangKontrol) {
                // Buat record baru jika belum ada
                $ruangKontrol = RuangKontrol::create([
                    'status_pendaftaran' => $statusPendaftaran,
                    'status_perbaikan' => $statusPerbaikan,
                    'tanggal_pendaftaran_mulai' => $request->tanggal_pendaftaran_mulai,
                    'tanggal_pendaftaran_selesai' => $request->tanggal_pendaftaran_selesai,
                    'tanggal_perbaikan_mulai' => $request->tanggal_perbaikan_mulai,
                    'tanggal_perbaikan_selesai' => $request->tanggal_perbaikan_selesai,
                    'id_pt' => auth('operator')->id()
                ]);
            } else {
                // Update record yang sudah ada
                $ruangKontrol->update([
                    'status_pendaftaran' => $statusPendaftaran,
                    'status_perbaikan' => $statusPerbaikan,
                    'tanggal_pendaftaran_mulai' => $request->tanggal_pendaftaran_mulai,
                    'tanggal_pendaftaran_selesai' => $request->tanggal_pendaftaran_selesai,
                    'tanggal_perbaikan_mulai' => $request->tanggal_perbaikan_mulai,
                    'tanggal_perbaikan_selesai' => $request->tanggal_perbaikan_selesai
                ]);
            }
            
            // Log success
            \Log::info('Ruang Kontrol Updated Successfully', [
                'ruang_kontrol_id' => $ruangKontrol->id_ruang_kontrol,
                'status_pendaftaran' => $ruangKontrol->status_pendaftaran,
                'status_perbaikan' => $ruangKontrol->status_perbaikan,
                'mutual_exclusive_applied' => true
            ]);
            
            // Determine which phase is active for response message
            $activePhase = '';
            if ($statusPendaftaran === 'terbuka') {
                $activePhase = 'Fase 1 (Pengajuan Proposal)';
            } elseif ($statusPerbaikan === 'terbuka') {
                $activePhase = 'Fase 2 (Perbaikan Proposal)';
            } else {
                $activePhase = 'Tidak ada fase aktif';
            }
            
            return response()->json([
                'success' => true,
                'message' => "Pengaturan ruang kontrol berhasil diperbarui. Status aktif: {$activePhase}",
                'data' => [
                    'status_pendaftaran' => $statusPendaftaran,
                    'status_perbaikan' => $statusPerbaikan,
                    'active_phase' => $activePhase
                ]
            ]);
            
        } catch (\Exception $e) {
            // Log error
            \Log::error('Error updating Ruang Kontrol', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function hasilFinal()
    {
        $tahun = request('tahun', '2025');
        $filter = request('filter', 'all');
        $statusRevisi = request('status_revisi', 'all');
        
        // Ambil proposal yang sudah selesai review (status minimal "revisi")
        // Hanya proposal yang sudah masuk tahap revisi atau final yang ditampilkan
        // Status yang diizinkan: revisi, revisi_submitted, lolos, tidak_lolos
        // EXCLUDE: submitted, review_administratif, review_substantif, review_completed, draft, pending, tidak_valid
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif', 'hasilFinal', 'proposalRevisi'])
            ->whereIn('status', ['revisi', 'revisi_submitted', 'lolos', 'tidak_lolos'])
            ->whereYear('tanggal_pengajuan', $tahun)
            ->when($filter !== 'all', function($query) use ($filter) {
                $query->where('skim', $filter);
            })
            ->when($statusRevisi !== 'all', function($query) use ($statusRevisi) {
                switch($statusRevisi) {
                    case 'belum':
                        $query->whereDoesntHave('proposalRevisi');
                        break;
                    case 'sudah':
                        $query->whereHas('proposalRevisi');
                        break;
                    case 'tidak_revisi':
                        // Proposal dengan status "revisi" tapi tidak memiliki file revisi
                        $query->where('status', 'revisi')->whereDoesntHave('proposalRevisi');
                        break;
                    case 'menunggu_review':
                        $query->where('status', 'revisi_submitted');
                        break;
                    case 'selesai':
                        $query->whereIn('status', ['lolos', 'tidak_lolos']);
                        break;
                }
            })
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();
        
        // Debug: Log proposal yang ditampilkan untuk memastikan status yang benar
        \Log::info('Hasil Final - Proposals loaded', [
            'count' => $proposals->count(),
            'statuses' => $proposals->pluck('status')->unique()->toArray(),
            'tahun' => $tahun,
            'filter' => $filter,
            'status_revisi' => $statusRevisi
        ]);
        
        return view('operator.hasil_final', compact('proposals', 'tahun', 'filter', 'statusRevisi'));
    }

    public function proposalDetail($id)
    {
        $proposal = Proposal::with([
            'mahasiswa', 
            'dosen', 
            'semuaAnggotaTim', 
            'dokumen', 
            'nilaiAdministratif.reviewer', 
            'nilaiSubstantif.reviewer', 
            'hasilFinal',
            'proposalRevisi' => function($query) {
                $query->orderBy('tanggal_submit', 'desc');
            }
        ])->findOrFail($id);

        return view('operator.proposal_detail', compact('proposal'));
    }

    public function updateHasilFinal(Request $request)
    {
        \Log::info('UpdateHasilFinal called', [
            'request_data' => $request->all(),
            'user_id' => auth('operator')->id()
        ]);

        $request->validate([
            'proposal_id' => 'required|exists:proposals,id_proposal',
            'status_final' => 'required|in:lolos,tidak_lolos',
            'catatan_final' => 'nullable|string',
            'nilai' => 'required|numeric|min:0|max:100'
        ]);

        try {
            DB::beginTransaction();
            
            $proposal = Proposal::findOrFail($request->proposal_id);
            
            // Update status proposal
            $proposal->update(['status' => $request->status_final]);
            
            // Update atau buat hasil final
            HasilFinal::updateOrCreate(
                ['id_proposal' => $request->proposal_id],
                [
                    'status_final' => $request->status_final,
                    'catatan_final' => $request->catatan_final,
                    'nilai' => $request->nilai,
                    'id_pt' => auth('operator')->id()
                ]
            );
            
            // Kirim notifikasi ke mahasiswa dan dosen
            $notificationService = new NotificationService();
            $notificationService->notifyHasilFinal(
                $proposal,
                $request->status_final,
                $request->nilai,
                $request->catatan_final
            );
            
            DB::commit();
            
            // Clear cache for this proposal
            $cacheKey = "operator_detail_hasil_final_{$request->proposal_id}";
            Cache::forget($cacheKey);
            
            \Log::info('Hasil final updated successfully', [
                'proposal_id' => $request->proposal_id,
                'status_final' => $request->status_final
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Hasil final berhasil diperbarui'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Error updating hasil final', [
                'error' => $e->getMessage(),
                'proposal_id' => $request->proposal_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadRevisi($id)
    {
        try {
            $revisi = ProposalRevisi::findOrFail($id);
            
            if (!Storage::disk('public')->exists($revisi->path_file)) {
                return redirect()->back()->with('error', 'File revisi tidak ditemukan di server.');
            }

            return Storage::disk('public')->download($revisi->path_file, $revisi->nama_file);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh file revisi: ' . $e->getMessage());
        }
    }

    public function detailHasilFinal($id)
    {
        // Use caching to improve performance
        $cacheKey = "operator_detail_hasil_final_{$id}";
        
        $proposal = Cache::remember($cacheKey, 300, function() use ($id) { // Cache for 5 minutes
            return Proposal::with([
                'mahasiswa', 
                'dosen', 
                'teams', 
                'dokumen', 
                'hasilFinal',
                'proposalRevisi' => function($query) {
                    $query->orderBy('tanggal_submit', 'desc');
                }
            ])->findOrFail($id);
        });

        return view('operator.detail_hasil_final', compact('proposal'));
    }

    // Manajemen Akun
    public function manageAccounts()
    {
        $mahasiswas = Mahasiswa::orderBy('created_at', 'desc')->limit(100)->get();
        $dosens = Dosen::orderBy('created_at', 'desc')->limit(100)->get();
        $reviewers = Reviewer::orderBy('created_at', 'desc')->limit(100)->get();
        $operators = PT::orderBy('created_at', 'desc')->limit(100)->get();
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        return view('operator.manajemen_akun', compact('mahasiswas', 'dosens', 'reviewers', 'operators', 'fakultas', 'prodis'));
    }

    public function storeAccount(Request $request, $type)
    {
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
                    'nuptk' => 'required|string|max:20|unique:dosens,nuptk',
                    'email' => 'required|email|max:255|unique:dosens,email_dosen',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                Dosen::create([
                    'nama_dosen' => $request->nama,
                    'nuptk' => $request->nuptk,
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
                    'role' => 'reviewer',
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
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }
        return redirect()->route('operator.manage.accounts')->with('success', 'Akun berhasil dibuat.');
    }

    public function updateAccount(Request $request, $type, $id)
    {
        switch ($type) {
            case 'mahasiswa':
                $mahasiswa = Mahasiswa::findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nim' => 'required|string|max:20|unique:mahasiswas,nim,' . $mahasiswa->id_mahasiswa . ',id_mahasiswa',
                    'email' => 'required|email|max:255|unique:mahasiswas,email_mhs,' . $mahasiswa->id_mahasiswa . ',id_mahasiswa',
                    'no_hp' => 'required|string|max:15',
                    'prodi' => 'nullable|exists:prodis,id_prodi',
                    'fakultas' => 'nullable|exists:fakultas,id_fakultas',
                    'password' => 'nullable|string|min:8|confirmed',
                    'is_active' => 'nullable|boolean',
                ]);
                $mahasiswa->nama_mhs = $request->nama;
                $mahasiswa->nim = $request->nim;
                $mahasiswa->email_mhs = $request->email;
                $mahasiswa->no_hp_mhs = $request->no_hp;
                if ($request->filled('prodi')) { $mahasiswa->prodi_mhs = Prodi::find($request->prodi)->nama_prodi; }
                if ($request->filled('fakultas')) { $mahasiswa->fakultas_mhs = Fakultas::find($request->fakultas)->nama_fakultas; }
                if ($request->filled('password')) { $mahasiswa->password = Hash::make($request->password); }
                if ($request->has('is_active')) { $mahasiswa->is_active = (bool)$request->is_active; }
                $mahasiswa->save();
                break;
            case 'dosen':
                $dosen = Dosen::findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nuptk' => 'required|string|max:20|unique:dosens,nuptk,' . $dosen->id_dosen . ',id_dosen',
                    'email' => 'required|email|max:255|unique:dosens,email_dosen,' . $dosen->id_dosen . ',id_dosen',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'nullable|string|min:8|confirmed',
                    'is_active' => 'nullable|boolean',
                ]);
                $dosen->nama_dosen = $request->nama;
                $dosen->nuptk = $request->nuptk;
                $dosen->email_dosen = $request->email;
                $dosen->no_hp_dosen = $request->no_hp;
                if ($request->filled('password')) { $dosen->password = Hash::make($request->password); }
                if ($request->has('is_active')) { $dosen->is_active = (bool)$request->is_active; }
                $dosen->save();
                break;
            case 'reviewer':
                $reviewer = Reviewer::findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:reviewers,email_reviewer,' . $reviewer->id_reviewer . ',id_reviewer',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'nullable|string|min:8|confirmed',
                    'is_active' => 'nullable|boolean',
                ]);
                $reviewer->nama_reviewer = $request->nama;
                $reviewer->email_reviewer = $request->email;
                $reviewer->no_hp_reviewer = $request->no_hp;
                if ($request->filled('password')) { $reviewer->password = Hash::make($request->password); }
                if ($request->has('is_active')) { $reviewer->is_active = (bool)$request->is_active; }
                $reviewer->save();
                break;
            case 'operator':
                $operator = PT::findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:pts,email_pt,' . $operator->id_pt . ',id_pt',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'nullable|string|min:8|confirmed',
                    'is_active' => 'nullable|boolean',
                ]);
                $operator->nama_pt = $request->nama;
                $operator->email_pt = $request->email;
                $operator->no_hp_pt = $request->no_hp;
                if ($request->filled('password')) { $operator->password = Hash::make($request->password); }
                if ($request->has('is_active')) { $operator->is_active = (bool)$request->is_active; }
                $operator->save();
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }
        return redirect()->route('operator.manage.accounts')->with('success', 'Akun berhasil diperbarui.');
    }

    public function deleteAccount($type, $id)
    {
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
                PT::where('id_pt', $id)->delete();
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }
        return redirect()->route('operator.manage.accounts')->with('success', 'Akun berhasil dihapus.');
    }

    private function getPKM8BidangData($tahun)
    {
        $skims = ['RE', 'RSH', 'KC', 'PM', 'PI', 'K', 'KI', 'VGK'];
        
        return collect($skims)->map(function($skim) use ($tahun) {
            // Total proposal berdasarkan skim
            $total = Proposal::where('skim', $skim)
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang sudah divalidasi (status_validasi = 'valid')
            $sudahValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'valid')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang belum divalidasi (status_validasi = 'pending')
            $belumValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'pending')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang ditolak validasi (status_validasi = 'tidak_valid')
            $tolakValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'tidak_valid')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang sedang dalam proses review
            $sedangReview = Proposal::where('skim', $skim)
                ->whereIn('status', ['submitted', 'review_administratif', 'review_substantif', 'revisi'])
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang sudah selesai review (lolos/tidak_lolos)
            $selesaiReview = Proposal::where('skim', $skim)
                ->whereIn('status', ['lolos', 'tidak_lolos'])
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            return [
                'skim' => $skim,
                'jumlah' => $total,
                'sudah_valid' => $sudahValid,
                'belum_valid' => $belumValid,
                'tolak_valid' => $tolakValid,
                'sedang_review' => $sedangReview,
                'selesai_review' => $selesaiReview
            ];
        });
    }

    private function getPKMInsentifData($tahun)
    {
        $skims = ['AI', 'GFT'];
        
        return collect($skims)->map(function($skim) use ($tahun) {
            // Total proposal berdasarkan skim
            $total = Proposal::where('skim', $skim)
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang sudah divalidasi (status_validasi = 'valid')
            $sudahValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'valid')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang belum divalidasi (status_validasi = 'pending')
            $belumValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'pending')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang ditolak validasi (status_validasi = 'tidak_valid')
            $tolakValid = Proposal::where('skim', $skim)
                ->where('status_validasi', 'tidak_valid')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang sedang dalam proses review
            $sedangReview = Proposal::where('skim', $skim)
                ->whereIn('status', ['submitted', 'review_administratif', 'review_substantif', 'revisi'])
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            // Proposal yang sudah selesai review (lolos/tidak_lolos)
            $selesaiReview = Proposal::where('skim', $skim)
                ->whereIn('status', ['lolos', 'tidak_lolos'])
                ->whereYear('tanggal_pengajuan', $tahun)
                ->count();
            
            return [
                'skim' => $skim,
                'jumlah' => $total,
                'sudah_valid' => $sudahValid,
                'belum_valid' => $belumValid,
                'tolak_valid' => $tolakValid,
                'sedang_review' => $sedangReview,
                'selesai_review' => $selesaiReview
            ];
        });
    }

    /**
     * Get chart data for dashboard
     */
    private function getChartData($tahun)
    {
        // Data proposal per tahun (3 tahun terakhir)
        $years = [$tahun - 2, $tahun - 1, $tahun];
        $proposalPerTahun = [];
        
        foreach ($years as $year) {
            $count = Proposal::whereYear('tanggal_pengajuan', $year)->count();
            $proposalPerTahun[] = [
                'tahun' => $year,
                'jumlah' => $count
            ];
        }
        
        // Data proposal per skim di tahun terbaru
        $proposalPerSkim = Proposal::whereYear('tanggal_pengajuan', $tahun)
            ->selectRaw('skim, COUNT(*) as jumlah')
            ->groupBy('skim')
            ->orderBy('jumlah', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'skim' => $item->skim,
                    'jumlah' => $item->jumlah
                ];
            });
        
        // Data proposal per fakultas di tahun terbaru
        $proposalPerFakultas = Proposal::whereYear('tanggal_pengajuan', $tahun)
            ->join('mahasiswas', 'proposals.id_mahasiswa', '=', 'mahasiswas.id_mahasiswa')
            ->selectRaw('mahasiswas.fakultas_mhs as nama_fakultas, COUNT(*) as jumlah')
            ->groupBy('mahasiswas.fakultas_mhs')
            ->orderBy('jumlah', 'desc')
            ->get()
            ->map(function($item) {
                return [
                    'fakultas' => $item->nama_fakultas,
                    'jumlah' => $item->jumlah
                ];
            });
        
        return [
            'proposal_per_tahun' => $proposalPerTahun,
            'proposal_per_skim' => $proposalPerSkim,
            'proposal_per_fakultas' => $proposalPerFakultas
        ];
    }

    /**
     * Get top 10 proposals by nilai
     */
    private function getTopProposals($tahun)
    {
        return Proposal::with(['mahasiswa', 'hasilFinal'])
            ->whereYear('tanggal_pengajuan', $tahun)
            ->whereHas('hasilFinal')
            ->join('hasil_finals', 'proposals.id_proposal', '=', 'hasil_finals.id_proposal')
            ->orderBy('hasil_finals.nilai', 'desc')
            ->limit(10)
            ->get()
            ->map(function($proposal, $index) {
                return [
                    'ranking' => $index + 1,
                    'judul' => $proposal->judul_proposal,
                    'skim' => $proposal->skim,
                    'mahasiswa' => $proposal->mahasiswa->nama_mahasiswa ?? 'N/A',
                    'nilai' => $proposal->hasilFinal->nilai ?? 0,
                    'status' => $proposal->hasilFinal->status_final ?? 'N/A'
                ];
            });
    }
}

