<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\ProposalRevisi;
// use App\Models\Team; // Model Team sudah dihapus
use App\Models\Dosen;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use App\Models\HasilFinal;
use App\Models\RuangKontrol;
use App\Helpers\ProposalHelper;
use App\Helpers\TahunAjaranHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProposalController extends Controller
{
    /**
     * Dashboard mahasiswa - menampilkan proposal yang sudah ada
     */
    public function dashboard()
    {
        // Cek user yang sedang login dari guard mahasiswa
        if (!auth()->guard('mahasiswa')->check()) {
            return redirect('/login')->withErrors(['email' => 'Silakan login terlebih dahulu.']);
        }

        $user = auth()->guard('mahasiswa')->user();
        
        // Ambil proposal mahasiswa
        $proposals = Proposal::where(function($query) use ($user) {
            // Proposal yang dibuat oleh mahasiswa ini
            $query->where('id_mahasiswa', $user->id_mahasiswa)
                  // ATAU proposal di mana mahasiswa ini terdaftar sebagai anggota tim
                  ->orWhere('team_id', $user->team_id);
        })
        ->with(['mahasiswa', 'dosen', 'dokumen', 'semuaAnggotaTim'])
        ->orderBy('created_at', 'desc')
        ->get();

        // Hitung statistik
        $totalProposals = $proposals->count();
        $underReview = $proposals->whereIn('status', ['review_administratif', 'review_substantif', 'review_completed'])->count();
        $waitingValidation = $proposals->where('status', 'submitted')->count();
        $approved = $proposals->where('status', 'finalized')->count();
        $revision = $proposals->where('status', 'revisi')->count();

        // Cek apakah ada proposal yang perlu direvisi
        $proposalForRevision = $proposals->where('status', 'revisi')->first();
        
        // Cek status ruang kontrol - ambil yang aktif untuk tahun ajaran terbaru
        $tahunAjaranTerbaru = \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru();
        $ruangKontrol = \App\Models\RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
            ->where('is_active', true)
            ->first();
        
        // Fallback: jika tidak ada yang aktif, ambil yang pertama untuk tahun ajaran terbaru
        if (!$ruangKontrol) {
            $ruangKontrol = \App\Models\RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        return view('mahasiswa.dashboard', compact(
            'proposals', 
            'totalProposals', 
            'underReview', 
            'waitingValidation', 
            'approved',
            'revision',
            'proposalForRevision',
            'ruangKontrol'
        ));
    }

    /**
     * Menampilkan form pengajuan proposal
     */
    public function create(Request $request)
    {
        $dosens = Dosen::all();
        $fakultas = \App\Models\Fakultas::orderBy('nama_fakultas')->get();
        
        // Cek user yang sedang login dari guard mahasiswa
        if (auth()->guard('mahasiswa')->check()) {
            $user = auth()->guard('mahasiswa')->user();
            
            // Cek apakah mahasiswa sudah memiliki proposal di tahun akademik yang sama
            $tahunAjaran = $request->input('tahun_ajaran', '2024/2025');
            $existingProposal = \App\Helpers\ProposalHelper::checkStudentInProposal($user->nim, null, $tahunAjaran);
            if ($existingProposal) {
                return redirect()->route('mahasiswa.proposal.index')
                    ->with('warning', "Anda sudah terdaftar dalam proposal tahun {$tahunAjaran}: \"{$existingProposal->judul}\". Satu mahasiswa hanya dapat terdaftar dalam satu proposal PKM per tahun akademik.");
            }
            
            // Debug: Log user data
            \Log::info('User data for auto-fill:', [
                'nim' => $user->nim ?? 'null',
                'nama_mhs' => $user->nama_mhs ?? 'null',
                'prodi_mhs' => $user->prodi_mhs ?? 'null',
                'fakultas_mhs' => $user->fakultas_mhs ?? 'null',
                'email_mhs' => $user->email_mhs ?? 'null',
                'no_hp_mhs' => $user->no_hp_mhs ?? 'null'
            ]);
        } else {
            // Jika tidak ada user yang login, redirect ke login
            return redirect('/login')->withErrors(['email' => 'Silakan login terlebih dahulu.']);
        }
        
        // Cek status ruang kontrol untuk pendaftaran
        $statusPendaftaran = \App\Helpers\RuangKontrolHelper::isPendaftaranActive() ? 'terbuka' : 'tertutup';
        
        return view('mahasiswa.ajukanproposal', compact('dosens', 'fakultas', 'user', 'statusPendaftaran'));
    }

    /**
     * Menyimpan proposal baru
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validasi data proposal menggunakan ProposalHelper
            $validator = ProposalHelper::validateProposalData($request->all());
            
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Validasi khusus untuk PKM Insentif (tidak ada pendanaan)
            $insentifSkims = ['GFT', 'AI'];
            if (in_array($request->skim, $insentifSkims)) {
                if ($request->dana_diajukan != 0) {
                    return back()
                        ->withErrors(['dana_diajukan' => 'PKM Insentif tidak memiliki pendanaan. Dana harus 0.'])
                        ->withInput();
                }
            } else {
                // Validasi untuk PKM Pendanaan
                if (!$request->dana_diajukan || $request->dana_diajukan < 1000000) {
                    return back()
                        ->withErrors(['dana_diajukan' => 'Dana yang diajukan minimal Rp 1.000.000 untuk PKM Pendanaan.'])
                        ->withInput();
                }
                if ($request->dana_diajukan > 15000000) {
                    return back()
                        ->withErrors(['dana_diajukan' => 'Dana yang diajukan maksimal Rp 15.000.000.'])
                        ->withInput();
                }
            }

            // Validasi keunikan NIM dalam tim
            $teamNimErrors = ProposalHelper::validateTeamNIMs($request->all());
            if (!empty($teamNimErrors)) {
                return back()
                    ->withErrors(['team_nim' => $teamNimErrors])
                    ->withInput();
            }

            // Validasi keunikan NIM di seluruh proposal dengan pertimbangan tahun akademik
            $tahunAjaran = $request->input('tahun_ajaran', date('Y') . '/' . (date('Y') + 1));
            $nimErrors = ProposalHelper::validateNIMsAcrossProposals($request->all(), null, $tahunAjaran);
            if (!empty($nimErrors)) {
                \Log::warning('NIM duplicate detected', [
                    'nim_errors' => $nimErrors,
                    'request_data' => [
                        'ketua_nim' => $request->ketua_nim,
                        'anggota1_nim' => $request->anggota1_nim,
                        'anggota2_nim' => $request->anggota2_nim,
                        'anggota3_nim' => $request->anggota3_nim,
                        'anggota4_nim' => $request->anggota4_nim,
                    ]
                ]);
                
                return back()
                    ->withErrors(['nim_duplicate' => $nimErrors])
                    ->withInput();
            }

            // Validasi ukuran tim
            $teamSizeErrors = ProposalHelper::validateTeamSize($request->all());
            if (!empty($teamSizeErrors)) {
                return back()
                    ->withErrors(['team_size' => $teamSizeErrors])
                    ->withInput();
            }

            // Validasi anggota opsional
            $optionalErrors = ProposalHelper::validateOptionalMembers($request->all());
            if (!empty($optionalErrors)) {
                return back()
                    ->withErrors(['optional_members' => $optionalErrors])
                    ->withInput();
            }

            // Cek apakah ketua tim sudah terdaftar dalam proposal lain di tahun akademik yang sama
            $tahunAjaran = $request->input('tahun_ajaran', date('Y') . '/' . (date('Y') + 1));
            $existingProposal = ProposalHelper::checkStudentInProposal($request->ketua_nim, null, $tahunAjaran);
                
            // Jika ada proposal lama yang ditolak, hapus file proposal lamanya
            if ($existingProposal && $existingProposal->status_validasi === 'tidak_valid') {
                // Hapus file proposal lama (file koreksi dari dosen)
                if ($existingProposal->dokumen && $existingProposal->dokumen->path_file) {
                    $oldFilePath = $existingProposal->dokumen->path_file;
                    if (Storage::disk('public')->exists($oldFilePath)) {
                        Storage::disk('public')->delete($oldFilePath);
                        \Log::info('Old proposal file deleted', [
                            'proposal_id' => $existingProposal->id_proposal,
                            'file_path' => $oldFilePath
                        ]);
                    }
                    
                    // Jika ada backup file asli, hapus juga
                    if ($existingProposal->dokumen->path_file_original) {
                        $originalFilePath = $existingProposal->dokumen->path_file_original;
                        if (Storage::disk('public')->exists($originalFilePath)) {
                            Storage::disk('public')->delete($originalFilePath);
                            \Log::info('Original proposal file deleted', [
                                'proposal_id' => $existingProposal->id_proposal,
                                'file_path' => $originalFilePath
                            ]);
                        }
                    }
                }
                
                // Hapus review PDF dosen jika ada
                if ($existingProposal->path_review_dosen && Storage::disk('public')->exists($existingProposal->path_review_dosen)) {
                    Storage::disk('public')->delete($existingProposal->path_review_dosen);
                }
            } else if ($existingProposal) {
                // Jika proposal lama masih aktif (bukan ditolak), tidak boleh membuat proposal baru
                return back()
                    ->withErrors(['ketua_nim' => "Ketua tim dengan NIM {$request->ketua_nim} sudah terdaftar dalam proposal tahun {$tahunAjaran}: {$existingProposal->judul}"])
                    ->withInput();
            }

            // Upload file proposal
            $proposalFile = null;
            if ($request->hasFile('proposal_file')) {
                $file = $request->file('proposal_file');
                
                // Validasi file
                if ($file->getSize() > 5 * 1024 * 1024) { // 5MB
                    return back()
                        ->withErrors(['proposal_file' => 'Ukuran file maksimal 5MB'])
                        ->withInput();
                }
                
                if ($file->getClientOriginalExtension() !== 'pdf') {
                    return back()
                        ->withErrors(['proposal_file' => 'File harus berformat PDF'])
                        ->withInput();
                }
                
                $proposalFile = $file->store('proposals', 'public');
            }

            // Set dana untuk PKM Insentif
            $danaDiajukan = $request->dana_diajukan;
            if (in_array($request->skim, ['GFT', 'AI'])) {
                $danaDiajukan = 0; // PKM Insentif tidak memiliki pendanaan
            }

            // Tentukan tahun ajaran dari tanggal pengajuan (jika tidak ada atau tidak sesuai)
            $tanggalPengajuan = now();
            $tahunAjaranDariTanggal = \App\Helpers\TahunAjaranHelper::getTahunAjaranByDate($tanggalPengajuan);
            $tahunAjaran = $request->tahun_ajaran;
            
            // Jika tahun ajaran dari request tidak sesuai dengan tanggal pengajuan, gunakan yang dari tanggal
            if (empty($tahunAjaran) || $tahunAjaran !== $tahunAjaranDariTanggal) {
                $tahunAjaran = $tahunAjaranDariTanggal;
            }
            
            // Buat proposal dengan data tim (untuk kompatibilitas dengan sistem lama)
            $proposal = Proposal::create([
                'judul_proposal' => $request->judul,
                'judul' => $request->judul,
                'tanggal_pengajuan' => $tanggalPengajuan,
                'skim' => $request->skim,
                'dosen_pembimbing' => $request->dosen_pembimbing,
                'dana_diajukan' => $danaDiajukan,
                'tahun_ajaran' => $tahunAjaran,
                'status_validasi' => 'pending',
                'status_final' => 'submitted',
                'status' => 'submitted',
                'id_mahasiswa' => auth()->guard('mahasiswa')->user()->id_mahasiswa,
                'id_dosen' => $this->getDosenIdByName($request->dosen_pembimbing),
                
                // Data ketua tim (wajib)
                'ketua_nama' => $request->ketua_nama,
                'ketua_nim' => $request->ketua_nim,
                'ketua_prodi' => $request->ketua_prodi,
                'ketua_fakultas' => $request->ketua_fakultas,
                'ketua_email' => $request->ketua_email,
                'ketua_no_hp' => $request->ketua_no_hp,
                
                // Data anggota 1 (wajib)
                'anggota1_nama' => $request->anggota1_nama,
                'anggota1_nim' => $request->anggota1_nim,
                'anggota1_prodi' => $request->anggota1_prodi,
                'anggota1_fakultas' => $request->anggota1_fakultas,
                'anggota1_email' => $request->anggota1_email,
                'anggota1_no_hp' => $request->anggota1_no_hp,
                
                // Data anggota 2 (wajib)
                'anggota2_nama' => $request->anggota2_nama,
                'anggota2_nim' => $request->anggota2_nim,
                'anggota2_prodi' => $request->anggota2_prodi,
                'anggota2_fakultas' => $request->anggota2_fakultas,
                'anggota2_email' => $request->anggota2_email,
                'anggota2_no_hp' => $request->anggota2_no_hp,
                
                // Data anggota 3 (opsional)
                'anggota3_nama' => $request->anggota3_nama,
                'anggota3_nim' => $request->anggota3_nim,
                'anggota3_prodi' => $request->anggota3_prodi,
                'anggota3_fakultas' => $request->anggota3_fakultas,
                'anggota3_email' => $request->anggota3_email,
                'anggota3_no_hp' => $request->anggota3_no_hp,
                
                // Data anggota 4 (opsional)
                'anggota4_nama' => $request->anggota4_nama,
                'anggota4_nim' => $request->anggota4_nim,
                'anggota4_prodi' => $request->anggota4_prodi,
                'anggota4_fakultas' => $request->anggota4_fakultas,
                'anggota4_email' => $request->anggota4_email,
                'anggota4_no_hp' => $request->anggota4_no_hp,
            ]);

            \Log::info('Proposal created successfully', [
                'proposal_id' => $proposal->id_proposal,
                'judul' => $proposal->judul,
                'ketua_nama' => $proposal->ketua_nama,
                'ketua_nim' => $proposal->ketua_nim
            ]);

            // Buat data tim di tabel teams untuk sistem yang lebih terstruktur
            $teamData = [
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
                'anggota4_nama' => $request->anggota4_nama,
                'anggota4_nim' => $request->anggota4_nim,
                'anggota4_prodi' => $request->anggota4_prodi,
                'anggota4_fakultas' => $request->anggota4_fakultas,
                'anggota4_email' => $request->anggota4_email,
                'anggota4_no_hp' => $request->anggota4_no_hp,
            ];

            // Buat tim menggunakan ProposalHelper
            ProposalHelper::createTeamData($proposal->id_proposal, $teamData);
            
            \Log::info('Team data creation completed', [
                'proposal_id' => $proposal->id_proposal,
                'team_data_count' => count($teamData)
            ]);

            // Buat dokumen jika ada file
            if ($proposalFile) {
                $proposal->dokumen()->create([
                    'skim' => $request->skim,
                    'path_file' => $proposalFile,
                    'file_proposal' => $proposalFile,
                    'tgl_upload' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('mahasiswa.proposal.index')
                ->with('success', 'Proposal berhasil diajukan! Silakan tunggu validasi dari dosen pendamping.');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Hapus file jika ada error
            if ($proposalFile && Storage::disk('public')->exists($proposalFile)) {
                Storage::disk('public')->delete($proposalFile);
            }
            
            return back()
                ->withErrors(['general' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Menampilkan daftar proposal mahasiswa
     */
    public function index()
    {
        $user = auth()->guard('mahasiswa')->user();
        
        // Ambil proposal yang dimiliki oleh mahasiswa yang login (sebagai pengaju)
        // ATAU proposal di mana mahasiswa terdaftar sebagai anggota tim
        $proposals = Proposal::with(['semuaAnggotaTim', 'dosen', 'dokumen', 'proposalRevisi', 'hasilFinal'])
            ->where(function($query) use ($user) {
                // Proposal yang dibuat oleh mahasiswa ini
                $query->where('id_mahasiswa', $user->id_mahasiswa)
                      // ATAU proposal di mana mahasiswa ini terdaftar sebagai anggota tim
                      ->orWhere('team_id', $user->team_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung statistik
        $totalProposals = $proposals->count();
        $pendingValidation = $proposals->where('status_validasi', 'pending')->count();
        $underReview = $proposals->where('status_validasi', 'valid')->where('status_final', '!=', 'approved')->count();
        $approved = $proposals->where('status_final', 'approved')->count();

        return view('mahasiswa.lihat_proposal', compact('proposals', 'totalProposals', 'pendingValidation', 'underReview', 'approved', 'user'));
    }

    /**
     * Menampilkan detail proposal dengan PDF viewer
     */
    public function show($id)
    {
        $user = auth()->guard('mahasiswa')->user();
        
        $proposal = Proposal::with([
                'semuaAnggotaTim', 
                'dosen', 
                'dosenPendampingUniversitas', 
                'dokumen', 
                'mahasiswa', 
                'nilaiAdministratif.reviewer',
                'nilaiSubstantif.reviewer',
                'hasilSemiFinal',
                'hasilFinal',
                'proposalRevisi' => function($query) {
                $query->orderBy('tanggal_submit', 'desc');
                }
            ])
            ->where('id_proposal', $id)
            ->where(function($query) use ($user) {
                // Proposal yang dibuat oleh mahasiswa ini
                $query->where('id_mahasiswa', $user->id_mahasiswa)
                      // ATAU proposal di mana mahasiswa ini terdaftar sebagai anggota tim
                      ->orWhere('team_id', $user->team_id);
            })
            ->firstOrFail();

        // Ambil data tim
        $ketua = $proposal->ketuaTim;
        $anggota = $proposal->anggotaTim;

        return view('mahasiswa.detail_proposal', compact('proposal', 'ketua', 'anggota', 'user'));
    }

    /**
     * Download dokumen proposal
     */
    public function download($id, $jenis)
    {
        $proposal = Proposal::where('id_proposal', $id)
            ->where('id_mahasiswa', auth()->user()->id_mahasiswa)
            ->with('dokumen')
            ->firstOrFail();

        if (!$proposal->dokumen || !$proposal->dokumen->path_file) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        $path = $proposal->dokumen->path_file;
        
        if (!Storage::disk('public')->exists($path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        // Get original filename or use path basename
        $filename = $proposal->dokumen->path_file_original 
            ? basename($proposal->dokumen->path_file_original)
            : basename($path);

        return Storage::disk('public')->download($path, $filename);
    }

    /**
     * Menampilkan PDF secara langsung untuk iframe
     */
    public function viewPdf($id)
    {
        try {
            $proposal = Proposal::where('id_proposal', $id)
                ->where('id_mahasiswa', auth()->user()->id_mahasiswa)
                ->with('dokumen')
                ->firstOrFail();

            if (!$proposal->dokumen) {
                abort(404, 'Dokumen tidak ditemukan.');
            }

            $path = storage_path('app/' . $proposal->dokumen->path_file);
            
            if (!file_exists($path)) {
                abort(404, 'File tidak ditemukan: ' . $path);
            }

            // Return PDF dengan content-type yang tepat untuk iframe
            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in viewPdf: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat memuat PDF: ' . $e->getMessage());
        }
    }

    public function getAdministrativeReview($id)
    {
        try {
            $proposal = Proposal::where('id_proposal', $id)
                ->where('id_mahasiswa', auth()->user()->id_mahasiswa)
                ->firstOrFail();

            // Ambil review administratif terbaru saja (hanya 1)
            $administrativeReview = NilaiAdministratif::where('id_proposal', $id)
                ->with('reviewer')
                ->latest('updated_at')
                ->first();

            if (!$administrativeReview) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'proposal_info' => [
                        'judul' => $proposal->judul_proposal,
                        'skim' => $proposal->skim,
                        'status' => $proposal->status
                    ]
                ]);
            }

            // Process review to anonymize reviewer name and format checklist
            $processedReview = $administrativeReview->toArray();
            
            // Anonymize reviewer name
            if ($administrativeReview->reviewer) {
                $processedReview['reviewer'] = [
                    'nama_reviewer' => 'Reviewer Administratif'
                ];
            }
            
            // Format checklist to show actual values (these are the selected errors)
            if ($administrativeReview->checklist && is_array($administrativeReview->checklist)) {
                $processedReview['checklist'] = $administrativeReview->checklist;
            }

            return response()->json([
                'success' => true,
                'data' => [$processedReview], // Wrap in array for consistency
                'proposal_info' => [
                    'judul' => $proposal->judul_proposal,
                    'skim' => $proposal->skim,
                    'status' => $proposal->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data review administratif: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSubstantiveReview($id)
    {
        try {
            $proposal = Proposal::where('id_proposal', $id)
                ->where('id_mahasiswa', auth()->user()->id_mahasiswa)
                ->firstOrFail();

            // Ambil review substantif terbaru dari setiap reviewer (maksimal 2)
            // Gunakan query yang lebih sederhana dan kompatibel
            $substantiveReviews = NilaiSubstantif::where('id_proposal', $id)
                ->with('reviewer')
                ->orderBy('id_reviewer')
                ->orderBy('updated_at', 'desc')
                ->get();

            // Group by reviewer dan ambil yang terbaru
            $latestReviews = collect();
            $reviewerIds = $substantiveReviews->pluck('id_reviewer')->unique();
            
            foreach ($reviewerIds as $reviewerId) {
                $latestReview = $substantiveReviews
                    ->where('id_reviewer', $reviewerId)
                    ->sortByDesc('updated_at')
                    ->first();
                
                if ($latestReview) {
                    $latestReviews->push($latestReview);
                }
            }

            // Debug: Log untuk troubleshooting
            \Log::info('Substantive Review Debug', [
                'proposal_id' => $id,
                'total_reviews_found' => $substantiveReviews->count(),
                'unique_reviewers' => $reviewerIds->count(),
                'latest_reviews_count' => $latestReviews->count(),
                'reviewer_ids' => $reviewerIds->toArray()
            ]);

            // Process reviews to anonymize reviewer names
            $processedReviews = $latestReviews->map(function($review, $index) {
                $processedReview = $review->toArray();
                
                // Anonymize reviewer name
                if ($review->reviewer) {
                    $processedReview['reviewer'] = [
                        'nama_reviewer' => 'Reviewer Substantif ' . ($index + 1)
                    ];
                }
                
                return $processedReview;
            });

            return response()->json([
                'success' => true,
                'data' => $processedReviews,
                'proposal_info' => [
                    'judul' => $proposal->judul_proposal,
                    'skim' => $proposal->skim,
                    'status' => $proposal->status
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getSubstantiveReview', [
                'proposal_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data review substantif: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFinalReview($id)
    {
        try {
            $user = auth()->guard('mahasiswa')->user();
            
            $proposal = Proposal::with(['dosenPendampingUniversitas', 'hasilSemiFinal'])
                ->where('id_proposal', $id)
                ->where(function($query) use ($user) {
                    // Proposal yang dibuat oleh mahasiswa ini
                    $query->where('id_mahasiswa', $user->id_mahasiswa)
                          // ATAU proposal di mana mahasiswa ini terdaftar sebagai anggota tim
                          ->orWhereHas('semuaAnggotaTim', function($memberQuery) use ($user) {
                              $memberQuery->where('nim', $user->nim);
                          });
                })
                ->firstOrFail();

            $finalResult = HasilFinal::where('id_proposal', $id)
                ->with('pimpinanPt')
                ->first();

            // Siapkan data dosen universitas
            $dosenUniversitas = null;
            if ($proposal->dosenPendampingUniversitas) {
                $dosenUniversitas = [
                    'nama_dosen' => $proposal->dosenPendampingUniversitas->nama_dosen,
                    'no_hp_dosen' => $proposal->dosenPendampingUniversitas->no_hp_dosen,
                    'email_dosen' => $proposal->dosenPendampingUniversitas->email_dosen
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $finalResult,
                'dosen_universitas' => $dosenUniversitas,
                'hasil_semi_final' => $proposal->hasilSemiFinal,
                'proposal_info' => [
                    'judul' => $proposal->judul_proposal,
                    'skim' => $proposal->skim,
                    'status' => $proposal->status,
                    'status_final' => $proposal->status_final
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data hasil final: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan form edit proposal
     */
    public function edit($id)
    {
        $user = auth()->guard('mahasiswa')->user();
        
        $proposal = Proposal::with(['semuaAnggotaTim'])
            ->where('id_proposal', $id)
            ->where(function($query) use ($user) {
                // Proposal yang dibuat oleh mahasiswa ini
                $query->where('id_mahasiswa', $user->id_mahasiswa)
                      // ATAU proposal di mana mahasiswa ini terdaftar sebagai anggota tim
                      ->orWhereHas('semuaAnggotaTim', function($memberQuery) use ($user) {
                          $memberQuery->where('nim', $user->nim);
                      });
            })
            ->firstOrFail();

        // Hanya bisa edit jika status masih draft atau pending
        if (!in_array($proposal->status, ['draft', 'pending'])) {
            return redirect()->route('mahasiswa.proposal.show', $id)
                ->with('error', 'Proposal tidak dapat diedit karena sudah diproses.');
        }

        $dosens = Dosen::all();
        
        return view('mahasiswa.edit_proposal', compact('proposal', 'dosens'));
    }

    /**
     * Update proposal
     */
    public function update(Request $request, $id)
    {
        $user = auth()->guard('mahasiswa')->user();
        
        $proposal = Proposal::where('id_proposal', $id)
            ->where(function($query) use ($user) {
                // Proposal yang dibuat oleh mahasiswa ini
                $query->where('id_mahasiswa', $user->id_mahasiswa)
                      // ATAU proposal di mana mahasiswa ini terdaftar sebagai anggota tim
                      ->orWhereHas('semuaAnggotaTim', function($memberQuery) use ($user) {
                          $memberQuery->where('nim', $user->nim);
                      });
            })
            ->firstOrFail();

        // Hanya bisa edit jika status masih draft atau pending
        if (!in_array($proposal->status, ['draft', 'pending'])) {
            return redirect()->route('mahasiswa.proposal.show', $id)
                ->with('error', 'Proposal tidak dapat diedit karena sudah diproses.');
        }

        try {
            DB::beginTransaction();

            // Validasi data proposal menggunakan ProposalHelper
            $validator = ProposalHelper::validateProposalData($request->all());
            
            if ($validator->fails()) {
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Validasi keunikan NIM (exclude current proposal)
            $nimErrors = ProposalHelper::validateNIMsAcrossProposals($request->all(), $proposal->id_proposal);
            if (!empty($nimErrors)) {
                return back()
                    ->withErrors(['nim_duplicate' => $nimErrors])
                    ->withInput();
            }

            // Update proposal (tanpa data tim)
            $proposal->update([
                'judul_proposal' => $request->judul,
                'judul' => $request->judul,
                'skim' => $request->skim,
                'dosen_pembimbing' => $request->dosen_pembimbing,
                'dana_diajukan' => $request->dana_diajukan,
                'tahun_ajaran' => $request->tahun_ajaran,
                'id_dosen' => $this->getDosenIdByName($request->dosen_pembimbing),
            ]);

            // Update data tim menggunakan ProposalHelper
            $teamData = [
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
                'anggota4_nama' => $request->anggota4_nama,
                'anggota4_nim' => $request->anggota4_nim,
                'anggota4_prodi' => $request->anggota4_prodi,
                'anggota4_fakultas' => $request->anggota4_fakultas,
                'anggota4_email' => $request->anggota4_email,
                'anggota4_no_hp' => $request->anggota4_no_hp,
            ];

            // Update tim menggunakan ProposalHelper
            ProposalHelper::updateTeamData($proposal->id_proposal, $teamData);

            DB::commit();

            return redirect()->route('mahasiswa.proposal.show', $id)
                ->with('success', 'Proposal berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->withErrors(['general' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Hapus proposal
     */
    public function destroy($id)
    {
        $user = auth()->guard('mahasiswa')->user();
        
        $proposal = Proposal::where('id_proposal', $id)
            ->where(function($query) use ($user) {
                // Proposal yang dibuat oleh mahasiswa ini
                $query->where('id_mahasiswa', $user->id_mahasiswa)
                      // ATAU proposal di mana mahasiswa ini terdaftar sebagai anggota tim
                      ->orWhereHas('semuaAnggotaTim', function($memberQuery) use ($user) {
                          $memberQuery->where('nim', $user->nim);
                      });
            })
            ->firstOrFail();

        // Hanya bisa hapus jika status masih draft
        if ($proposal->status !== 'draft') {
            return redirect()->route('mahasiswa.proposal.show', $id)
                ->with('error', 'Proposal tidak dapat dihapus karena sudah diproses.');
        }

        try {
            DB::beginTransaction();

            // Hapus dokumen terkait
            if ($proposal->dokumen) {
                if ($proposal->dokumen->path_file && Storage::disk('public')->exists($proposal->dokumen->path_file)) {
                    Storage::disk('public')->delete($proposal->dokumen->path_file);
                }
                $proposal->dokumen->delete();
            }

            // Hapus data tim (akan terhapus otomatis karena foreign key cascade)
            // Team::where('id_proposal', $proposal->id_proposal)->delete();

            // Hapus proposal
            $proposal->delete();

            DB::commit();

            return redirect()->route('mahasiswa.proposal.index')
                ->with('success', 'Proposal berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->withErrors(['general' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Helper method untuk mendapatkan ID dosen berdasarkan nama
     */
    private function getDosenIdByName($namaDosen)
    {
        // Extract nama dosen dari format "Nama, Gelar"
        $nama = explode(',', $namaDosen)[0];
        
        $dosen = Dosen::where('nama_dosen', 'LIKE', "%{$nama}%")->first();
        
        return $dosen ? $dosen->id_dosen : null;
    }

    /**
     * API untuk cek apakah mahasiswa sudah terdaftar dalam proposal
     */
    public function checkStudentProposal($nim, $tahunAjaran = null)
    {
        $proposal = ProposalHelper::checkStudentInProposal($nim, null, $tahunAjaran);
        
        return response()->json([
            'success' => true,
            'hasProposal' => $proposal !== null,
            'proposalTitle' => $proposal ? $proposal->judul : null,
            'proposalStatus' => $proposal ? $proposal->status : null,
            'tahunAjaran' => $proposal ? $proposal->tahun_ajaran : null,
        ]);
    }

    /**
     * API untuk mendapatkan data mahasiswa berdasarkan NIM
     */
    public function getStudentByNim($nim)
    {
        $mahasiswa = \App\Models\Mahasiswa::where('nim', $nim)->first();
        
        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nama' => $mahasiswa->nama_mhs,
                'nim' => $mahasiswa->nim,
                'prodi' => $mahasiswa->prodi_mhs,
                'fakultas' => $mahasiswa->fakultas_mhs,
                'email' => $mahasiswa->email_mhs,
                'no_hp' => $mahasiswa->no_hp_mhs,
            ]
        ]);
    }

    /**
     * Menampilkan form revisi proposal
     */
    public function showRevisiForm($id)
    {
        // Cek user yang sedang login dari guard mahasiswa
        if (!auth()->guard('mahasiswa')->check()) {
            return redirect('/login')->withErrors(['email' => 'Silakan login terlebih dahulu.']);
        }

        $user = auth()->guard('mahasiswa')->user();
        
        // Ambil proposal berdasarkan ID
        $proposal = Proposal::with([
            'mahasiswa', 
            'dosen', 
            'semuaAnggotaTim', 
            'dokumen',
            'nilaiAdministratif.reviewer',
            'nilaiSubstantif.reviewer',
            'hasilSemiFinal',
            'dosenPendampingUniversitas'
        ])
            ->where('id_proposal', $id)
            ->where(function($query) use ($user) {
                // Proposal yang dibuat oleh mahasiswa ini
                $query->where('id_mahasiswa', $user->id_mahasiswa)
                      // ATAU proposal di mana mahasiswa ini terdaftar sebagai anggota tim
                      ->orWhereHas('semuaAnggotaTim', function($memberQuery) use ($user) {
                          $memberQuery->where('nim', $user->nim);
                      });
            })
            ->firstOrFail();

        // Cek apakah status proposal sudah revisi
        if ($proposal->status !== 'revisi') {
            return redirect()->route('mahasiswa.proposal.show', $id)
                ->with('error', 'Proposal belum siap untuk direvisi. Status saat ini: ' . ucfirst(str_replace('_', ' ', $proposal->status)));
        }

        // Ambil ruang kontrol aktif untuk tahun akademik terbaru
        $tahunAjaranTerbaru = TahunAjaranHelper::getTahunAjaranTerbaru();
        $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
            ->where('is_active', true)
            ->first();
        
        // Fallback: jika tidak ada yang aktif, ambil yang pertama untuk tahun ajaran terbaru
        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        // Ambil data revisi yang sudah ada
        $revisi = ProposalRevisi::where('id_proposal', $proposal->id_proposal)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.revisi_proposal', compact('proposal', 'user', 'ruangKontrol', 'revisi'));
    }

    /**
     * Submit revisi proposal
     */
    public function submitRevisi(Request $request, $id)
    {
        try {
            // Cek user yang sedang login dari guard mahasiswa
            if (!auth()->guard('mahasiswa')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.'
                ], 401);
            }

            $user = auth()->guard('mahasiswa')->user();
            
            // Ambil proposal berdasarkan ID
            $proposal = Proposal::with(['dokumen'])
                ->where('id_proposal', $id)
                ->where(function($query) use ($user) {
                    // Proposal yang dibuat oleh mahasiswa ini
                    $query->where('id_mahasiswa', $user->id_mahasiswa)
                          // ATAU proposal di mana mahasiswa ini terdaftar sebagai anggota tim
                          ->orWhereHas('semuaAnggotaTim', function($memberQuery) use ($user) {
                              $memberQuery->where('nim', $user->nim);
                          });
                })
                ->firstOrFail();

            // Cek apakah status proposal sudah revisi
            if ($proposal->status !== 'revisi') {
                return response()->json([
                    'success' => false,
                    'message' => 'Proposal belum siap untuk direvisi.'
                ], 400);
            }

            // Validasi file revisi
            $request->validate([
                'revisi_file' => 'required|file|mimes:pdf|max:5120', // 5MB max
            ], [
                'revisi_file.required' => 'File revisi proposal wajib diupload.',
                'revisi_file.file' => 'File revisi harus berupa file.',
                'revisi_file.mimes' => 'File revisi harus berformat PDF.',
                'revisi_file.max' => 'Ukuran file revisi maksimal 5MB.',
            ]);

            DB::beginTransaction();

            // Upload file revisi
            $revisiFile = $request->file('revisi_file');
            $fileName = 'revisi_' . time() . '_' . $revisiFile->getClientOriginalName();
            $filePath = $revisiFile->storeAs('proposals/revisi', $fileName, 'public');

            // Create record di tabel proposal_revisi
            $proposalRevisi = $proposal->proposalRevisi()->create([
                'nama_file' => $fileName,
                'path_file' => $filePath,
                'tanggal_submit' => now(),
                'jenis_revisi' => 'revisi_biasa', // Revisi biasa untuk hasil semi final
            ]);

            // Update status proposal menjadi 'revisi_submitted'
            $proposal->update([
                'status' => 'revisi_submitted',
                'updated_at' => now(),
            ]);

            DB::commit();

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
            DB::rollback();
            
            \Log::error('Error in submitRevisi', [
                'proposal_id' => $id,
                'user_id' => $user->id_mahasiswa ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan form revisi akhir proposal
     */
    public function showRevisiAkhirForm($id)
    {
        $user = auth()->guard('mahasiswa')->user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil proposal
        $proposal = Proposal::with(['dokumen', 'dosenPendampingUniversitas', 'hasilSemiFinal'])
            ->where('id_proposal', $id)
            ->where(function($query) use ($user) {
                $query->where('id_mahasiswa', $user->id_mahasiswa)
                      ->orWhereHas('semuaAnggotaTim', function($memberQuery) use ($user) {
                          $memberQuery->where('nim', $user->nim);
                      });
            })
            ->firstOrFail();

        // Cek apakah status proposal sudah revisi_akhir
        if ($proposal->status !== 'revisi_akhir') {
            return redirect()->route('mahasiswa.proposal.index')
                ->with('error', 'Proposal belum siap untuk revisi akhir. Status saat ini: ' . ucfirst(str_replace('_', ' ', $proposal->status)));
        }

        // Ambil data revisi akhir yang sudah ada (berdasarkan path file yang mengandung 'revisi_akhir')
        $revisiAkhir = ProposalRevisi::where('id_proposal', $proposal->id_proposal)
            ->where('path_file', 'like', '%revisi_akhir%')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.revisi_akhir_proposal', compact('proposal', 'user', 'revisiAkhir'));
    }

    /**
     * Submit revisi akhir proposal
     */
    public function submitRevisiAkhir(Request $request, $id)
    {
        try {
            // Cek user yang sedang login dari guard mahasiswa
            if (!auth()->guard('mahasiswa')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.'
                ], 401);
            }

            $user = auth()->guard('mahasiswa')->user();
            
            // Ambil proposal berdasarkan ID
            $proposal = Proposal::with(['dokumen', 'dosenPendampingUniversitas'])
                ->where('id_proposal', $id)
                ->where(function($query) use ($user) {
                    // Proposal yang dibuat oleh mahasiswa ini
                    $query->where('id_mahasiswa', $user->id_mahasiswa)
                          // ATAU proposal di mana mahasiswa ini terdaftar sebagai anggota tim
                          ->orWhereHas('semuaAnggotaTim', function($memberQuery) use ($user) {
                              $memberQuery->where('nim', $user->nim);
                          });
                })
                ->firstOrFail();

            // Cek apakah status proposal sudah revisi_akhir
            if ($proposal->status !== 'revisi_akhir') {
                return response()->json([
                    'success' => false,
                    'message' => 'Proposal belum siap untuk revisi akhir.'
                ], 400);
            }

            // Validasi file revisi akhir
            $request->validate([
                'revisi_file' => 'required|file|mimes:pdf|max:5120', // 5MB max
            ], [
                'revisi_file.required' => 'File revisi akhir proposal wajib diupload.',
                'revisi_file.file' => 'File revisi harus berupa file.',
                'revisi_file.mimes' => 'File revisi harus berformat PDF.',
                'revisi_file.max' => 'Ukuran file revisi maksimal 5MB.',
            ]);

            DB::beginTransaction();

            // Upload file revisi akhir (disimpan di folder berbeda)
            $revisiFile = $request->file('revisi_file');
            $fileName = 'revisi_akhir_' . time() . '_' . $revisiFile->getClientOriginalName();
            $filePath = $revisiFile->storeAs('proposals/revisi_akhir', $fileName, 'public');

            // Create record di tabel proposal_revisi
            $proposalRevisi = $proposal->proposalRevisi()->create([
                'nama_file' => $fileName,
                'path_file' => $filePath,
                'tanggal_submit' => now(),
                'jenis_revisi' => 'revisi_akhir', // Revisi akhir untuk hasil final
            ]);

            // Update status proposal menjadi 'validasi_akhir_dosen_univ'
            $proposal->update([
                'status' => 'validasi_akhir_dosen_univ',
                'status_final' => 'validasi_akhir_dosen_univ',
                'updated_at' => now(),
            ]);

            DB::commit();

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
            DB::rollback();
            
            \Log::error('Error in submitRevisiAkhir', [
                'proposal_id' => $id,
                'user_id' => $user->id_mahasiswa ?? 'unknown',
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