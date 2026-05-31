<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RuangKontrol;
use App\Services\RuangKontrolService;

use App\Services\NotificationService;
use App\Helpers\TahunAjaranHelper;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RuangKontrolController extends Controller
{
    protected RuangKontrolService $ruangKontrolService;
    protected NotificationService $notificationService;

    public function __construct(
        RuangKontrolService $ruangKontrolService,
        NotificationService $notificationService
    ) {
        $this->ruangKontrolService = $ruangKontrolService;
        $this->notificationService = $notificationService;
    }

    public function ruangKontrol()
    {
        // Gunakan TahunAjaranHelper untuk format tahun akademik
        $tahunAjaranTerpilih = request('tahun', TahunAjaranHelper::getTahunAjaranTerbaru());
        
        // Ambil ruang kontrol aktif untuk tahun akademik yang dipilih
        $ruangKontrolAktif = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerpilih)
            ->where('is_active', true)
            ->first();
        
        // Jika tidak ada aktif, ambil yang pertama atau buat baru
        if (!$ruangKontrolAktif) {
            $ruangKontrolAktif = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerpilih)->first();
            
            if (!$ruangKontrolAktif) {
                $ruangKontrolAktif = RuangKontrol::create([
                    'status_pendaftaran' => 'tertutup',
                    'status_review' => 'tertutup',
                    'status_perbaikan' => 'tertutup',
                    'status_penilaian_akhir' => 'tertutup',
                    'tahun_ajaran' => $tahunAjaranTerpilih,
                    'nama_history' => 'Jadwal ' . $tahunAjaranTerpilih,
                    'is_active' => true,
                    'id_pt' => auth()->id()
                ]);
            } else {
                // Set yang pertama sebagai aktif jika belum ada yang aktif
                $ruangKontrolAktif->update(['is_active' => true]);
            }
        }
        
        // Ambil semua history untuk dropdown (hanya format tahun akademik YYYY/YYYY)
        $histories = RuangKontrol::whereNotNull('tahun_ajaran')
            ->whereRaw("tahun_ajaran LIKE '%/%'") // Hanya format YYYY/YYYY
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(function($item) {
                // Double check: pastikan format benar (mengandung slash dan memiliki 2 bagian)
                return strpos($item->tahun_ajaran, '/') !== false &&
                       count(explode('/', $item->tahun_ajaran)) === 2;
            })
            ->sortByDesc(function($item) {
                // Sort by tahun pertama (YYYY/YYYY → ambil bagian kiri), PHP-side agar SQLite-compatible
                return (int) explode('/', $item->tahun_ajaran)[0];
            })
            ->groupBy('tahun_ajaran');
        
        // Ambil semua jadwal untuk tahun akademik yang dipilih
        $jadwalTahun = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerpilih)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Auto-check dan update status berdasarkan tanggal (hanya untuk jadwal aktif)
        if ($ruangKontrolAktif && $ruangKontrolAktif->is_active) {
            $this->ruangKontrolService->checkAndUpdateAutoActivation($ruangKontrolAktif);
            // Reload untuk mendapatkan status terbaru
            $ruangKontrolAktif->refresh();
        }
        
        $phases = RuangKontrol::PHASES;
        $phaseLabels = RuangKontrol::PHASE_LABELS;
        $phaseIcons = RuangKontrol::PHASE_ICONS;
        $phaseDescriptions = RuangKontrol::PHASE_DESCRIPTIONS;

        return view('operator.ruang_kontrol', compact('ruangKontrolAktif', 'histories', 'jadwalTahun', 'tahunAjaranTerpilih', 'phases', 'phaseLabels', 'phaseIcons', 'phaseDescriptions'));
    }

    public function getActivePhase()
    {
        $phaseData = $this->ruangKontrolService->getActivePhaseData();
        return response()->json(array_merge(['success' => true], $phaseData));
    }

    public function updateRuangKontrol(Request $request)
    {
        Log::info('Update Ruang Kontrol Request', [
            'data' => $request->all(),
            'user_id' => auth()->id()
        ]);

        $request->validate([
            'id_ruang_kontrol' => 'nullable|exists:ruang_kontrols,id_ruang_kontrol',
            'active_phase' => 'nullable|in:pendaftaran,review,perbaikan,penilaian_akhir',
            'status_pendaftaran' => 'nullable|in:terbuka,tertutup',
            'status_review' => 'nullable|in:terbuka,tertutup',
            'status_perbaikan' => 'nullable|in:terbuka,tertutup',
            'status_penilaian_akhir' => 'nullable|in:terbuka,tertutup',
            'tanggal_pendaftaran_mulai' => 'nullable|date',
            'tanggal_pendaftaran_selesai' => 'nullable|date|after:tanggal_pendaftaran_mulai',
            'tanggal_review_mulai' => 'nullable|date',
            'tanggal_review_selesai' => 'nullable|date|after:tanggal_review_mulai',
            'tanggal_perbaikan_mulai' => 'nullable|date',
            'tanggal_perbaikan_selesai' => 'nullable|date|after:tanggal_perbaikan_mulai',
            'tanggal_penilaian_akhir_mulai' => 'nullable|date',
            'tanggal_penilaian_akhir_selesai' => 'nullable|date|after:tanggal_penilaian_akhir_mulai',
            'tahun_ajaran' => 'nullable|string',
            'nama_history' => 'nullable|string'
        ]);

        // Resolve which phase is open: active_phase takes precedence, else derive from status_* (mutual exclusive)
        $statusPendaftaran = 'tertutup';
        $statusReview = 'tertutup';
        $statusPerbaikan = 'tertutup';
        $statusPenilaianAkhir = 'tertutup';

        if ($request->filled('active_phase')) {
            $statusPendaftaran = $request->active_phase === 'pendaftaran' ? 'terbuka' : 'tertutup';
            $statusReview = $request->active_phase === 'review' ? 'terbuka' : 'tertutup';
            $statusPerbaikan = $request->active_phase === 'perbaikan' ? 'terbuka' : 'tertutup';
            $statusPenilaianAkhir = $request->active_phase === 'penilaian_akhir' ? 'terbuka' : 'tertutup';
        } else {
            $statusPendaftaran = $request->input('status_pendaftaran', 'tertutup');
            $statusReview = $request->input('status_review', 'tertutup');
            $statusPerbaikan = $request->input('status_perbaikan', 'tertutup');
            $statusPenilaianAkhir = $request->input('status_penilaian_akhir', 'tertutup');
            $openCount = array_filter([$statusPendaftaran, $statusReview, $statusPerbaikan, $statusPenilaianAkhir], fn($s) => $s === 'terbuka');
            if (count($openCount) > 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat membuka lebih dari satu fase secara bersamaan. Hanya satu fase yang dapat aktif pada satu waktu.'
                ], 422);
            }
        }

        // Validate chronological order if all dates provided
        $allDatesFilled = $request->tanggal_pendaftaran_selesai && $request->tanggal_review_mulai
            && $request->tanggal_review_selesai && $request->tanggal_perbaikan_mulai
            && $request->tanggal_perbaikan_selesai && $request->tanggal_penilaian_akhir_mulai;

        if ($allDatesFilled) {
            $dates = [
                'pend_selesai' => strtotime($request->tanggal_pendaftaran_selesai),
                'review_mulai' => strtotime($request->tanggal_review_mulai),
                'review_selesai' => strtotime($request->tanggal_review_selesai),
                'perbaikan_mulai' => strtotime($request->tanggal_perbaikan_mulai),
                'perbaikan_selesai' => strtotime($request->tanggal_perbaikan_selesai),
                'penilaian_mulai' => strtotime($request->tanggal_penilaian_akhir_mulai),
            ];
            if ($dates['pend_selesai'] >= $dates['review_mulai']) {
                return response()->json(['success' => false, 'message' => 'Tanggal selesai pendaftaran harus sebelum tanggal mulai review.'], 422);
            }
            if ($dates['review_selesai'] >= $dates['perbaikan_mulai']) {
                return response()->json(['success' => false, 'message' => 'Tanggal selesai review harus sebelum tanggal mulai perbaikan.'], 422);
            }
            if ($dates['perbaikan_selesai'] >= $dates['penilaian_mulai']) {
                return response()->json(['success' => false, 'message' => 'Tanggal selesai perbaikan harus sebelum tanggal mulai penilaian akhir.'], 422);
            }
        }

        try {
            $tahunAjaran = $request->input('tahun_ajaran');

            if ($request->filled('tanggal_pendaftaran_mulai')) {
                $tanggalFase1Mulai = Carbon::parse($request->tanggal_pendaftaran_mulai);
                $tahunFase1 = (int) $tanggalFase1Mulai->format('Y');
                $bulanFase1 = (int) $tanggalFase1Mulai->format('n');
                $tahunAjaran = $bulanFase1 >= 7
                    ? $tahunFase1 . '/' . ($tahunFase1 + 1)
                    : ($tahunFase1 - 1) . '/' . $tahunFase1;

                if ($this->ruangKontrolService->isTahunAkademikMasaLalu($tahunAjaran)) {
                    $tahunAkademikSekarang = TahunAjaranHelper::getTahunAjaranTerbaru();
                    return response()->json([
                        'success' => false,
                        'message' => 'Tahun akademik masa lalu tidak dapat digunakan. Hanya tahun akademik sekarang (' . $tahunAkademikSekarang . ') dan tahun akademik depan yang bisa digunakan untuk mengatur fase.'
                    ], 422);
                }
            }

            if (!$tahunAjaran) {
                $tahunAjaran = TahunAjaranHelper::getTahunAjaranTerbaru();
            }

            $payload = [
                'status_pendaftaran' => $statusPendaftaran,
                'status_review' => $statusReview,
                'status_perbaikan' => $statusPerbaikan,
                'status_penilaian_akhir' => $statusPenilaianAkhir,
                'tanggal_pendaftaran_mulai' => $request->tanggal_pendaftaran_mulai,
                'tanggal_pendaftaran_selesai' => $request->tanggal_pendaftaran_selesai,
                'tanggal_review_mulai' => $request->tanggal_review_mulai,
                'tanggal_review_selesai' => $request->tanggal_review_selesai,
                'tanggal_perbaikan_mulai' => $request->tanggal_perbaikan_mulai,
                'tanggal_perbaikan_selesai' => $request->tanggal_perbaikan_selesai,
                'tanggal_penilaian_akhir_mulai' => $request->tanggal_penilaian_akhir_mulai,
                'tanggal_penilaian_akhir_selesai' => $request->tanggal_penilaian_akhir_selesai,
                'nama_history' => $request->nama_history ?? null,
            ];

            if ($request->filled('id_ruang_kontrol')) {
                $ruangKontrol = RuangKontrol::findOrFail($request->id_ruang_kontrol);
                if ($ruangKontrol->tahun_ajaran !== $tahunAjaran) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jadwal yang dipilih tidak sesuai dengan tahun ajaran yang dipilih.'
                    ], 422);
                }
                $oldStatusPendaftaran = $ruangKontrol->status_pendaftaran;
                $payload['nama_history'] = $payload['nama_history'] ?? $ruangKontrol->nama_history;
                $ruangKontrol->update($payload);

                if ($statusPendaftaran === 'terbuka' && $oldStatusPendaftaran !== 'terbuka') {
                    try {
                        $this->notificationService->notifyRuangKontrolDibuka($ruangKontrol->fresh());
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim notifikasi ruang kontrol dibuka: ' . $e->getMessage());
                    }
                }
            } else {
                $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaran)->where('is_active', true)->first();
                if (!$ruangKontrol) {
                    $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaran)->orderBy('created_at', 'desc')->first();
                }
                if (!$ruangKontrol) {
                    $payload['tahun_ajaran'] = $tahunAjaran;
                    $payload['nama_history'] = $payload['nama_history'] ?? 'Jadwal ' . $tahunAjaran;
                    $payload['is_active'] = true;
                    $payload['id_pt'] = auth()->id();
                    $ruangKontrol = RuangKontrol::create($payload);
                    if ($statusPendaftaran === 'terbuka') {
                        try {
                            $this->notificationService->notifyRuangKontrolDibuka($ruangKontrol);
                        } catch (\Exception $e) {
                            Log::error('Gagal mengirim notifikasi ruang kontrol dibuka: ' . $e->getMessage());
                        }
                    }
                } else {
                    $oldStatusPendaftaran = $ruangKontrol->status_pendaftaran;
                    $payload['nama_history'] = $payload['nama_history'] ?? $ruangKontrol->nama_history;
                    $payload['is_active'] = true;
                    $ruangKontrol->update($payload);
                    if ($statusPendaftaran === 'terbuka' && $oldStatusPendaftaran !== 'terbuka') {
                        try {
                            $this->notificationService->notifyRuangKontrolDibuka($ruangKontrol->fresh());
                        } catch (\Exception $e) {
                            Log::error('Gagal mengirim notifikasi ruang kontrol dibuka: ' . $e->getMessage());
                        }
                    }
                }
            }

            Log::info('Ruang Kontrol Updated Successfully', [
                'ruang_kontrol_id' => $ruangKontrol->id_ruang_kontrol,
                'phases' => [
                    'pendaftaran' => $ruangKontrol->status_pendaftaran,
                    'review' => $ruangKontrol->status_review,
                    'perbaikan' => $ruangKontrol->status_perbaikan,
                    'penilaian_akhir' => $ruangKontrol->status_penilaian_akhir,
                ],
                'mutual_exclusive_applied' => true
            ]);


            $activePhaseLabel = $ruangKontrol->getActivePhase()
                ? (RuangKontrol::PHASE_LABELS[$ruangKontrol->getActivePhase()] ?? $ruangKontrol->getActivePhase())
                : 'Tidak ada fase aktif';

            return response()->json([
                'success' => true,
                'message' => "Pengaturan ruang kontrol berhasil diperbarui. Status aktif: {$activePhaseLabel}",
                'data' => [
                    'status_pendaftaran' => $statusPendaftaran,
                    'status_review' => $statusReview,
                    'status_perbaikan' => $statusPerbaikan,
                    'status_penilaian_akhir' => $statusPenilaianAkhir,
                    'active_phase' => $activePhaseLabel
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating Ruang Kontrol', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createJadwal(Request $request)
    {
        try {
            Log::info('Create Jadwal Request', [
                'data' => $request->all(),
                'user_id' => auth()->id()
            ]);

            $request->validate([
                'tahun_ajaran' => 'nullable|string',
                'nama_history' => 'required|string|max:255',
                'tanggal_pendaftaran_mulai' => 'required|date',
                'tanggal_pendaftaran_selesai' => 'required|date|after:tanggal_pendaftaran_mulai',
                'tanggal_review_mulai' => 'required|date',
                'tanggal_review_selesai' => 'required|date|after:tanggal_review_mulai',
                'tanggal_perbaikan_mulai' => 'required|date',
                'tanggal_perbaikan_selesai' => 'required|date|after:tanggal_perbaikan_mulai',
                'tanggal_penilaian_akhir_mulai' => 'required|date',
                'tanggal_penilaian_akhir_selesai' => 'required|date|after:tanggal_penilaian_akhir_mulai',
            ], [
                'nama_history.required' => 'Nama jadwal wajib diisi.',
                'nama_history.max' => 'Nama jadwal maksimal 255 karakter.',
                'tanggal_pendaftaran_mulai.required' => 'Tanggal mulai pendaftaran wajib diisi.',
                'tanggal_pendaftaran_selesai.required' => 'Tanggal selesai pendaftaran wajib diisi.',
                'tanggal_pendaftaran_selesai.after' => 'Tanggal selesai pendaftaran harus setelah tanggal mulai.',
                'tanggal_review_mulai.required' => 'Tanggal mulai review wajib diisi.',
                'tanggal_review_selesai.required' => 'Tanggal selesai review wajib diisi.',
                'tanggal_review_selesai.after' => 'Tanggal selesai review harus setelah tanggal mulai.',
                'tanggal_perbaikan_mulai.required' => 'Tanggal mulai perbaikan wajib diisi.',
                'tanggal_perbaikan_selesai.required' => 'Tanggal selesai perbaikan wajib diisi.',
                'tanggal_perbaikan_selesai.after' => 'Tanggal selesai perbaikan harus setelah tanggal mulai.',
                'tanggal_penilaian_akhir_mulai.required' => 'Tanggal mulai penilaian akhir wajib diisi.',
                'tanggal_penilaian_akhir_selesai.required' => 'Tanggal selesai penilaian akhir wajib diisi.',
                'tanggal_penilaian_akhir_selesai.after' => 'Tanggal selesai penilaian akhir harus setelah tanggal mulai.',
            ]);

            // Chronological order: fase1 end < fase2 start < fase2 end < fase3 start < fase3 end < fase4 start < fase4 end
            $pendSelesai = strtotime($request->tanggal_pendaftaran_selesai);
            $reviewMulai = strtotime($request->tanggal_review_mulai);
            $reviewSelesai = strtotime($request->tanggal_review_selesai);
            $perbaikanMulai = strtotime($request->tanggal_perbaikan_mulai);
            $perbaikanSelesai = strtotime($request->tanggal_perbaikan_selesai);
            $penilaianMulai = strtotime($request->tanggal_penilaian_akhir_mulai);

            if ($pendSelesai >= $reviewMulai) {
                return response()->json(['success' => false, 'message' => 'Tanggal selesai pendaftaran harus sebelum tanggal mulai review.'], 422);
            }
            if ($reviewSelesai >= $perbaikanMulai) {
                return response()->json(['success' => false, 'message' => 'Tanggal selesai review harus sebelum tanggal mulai perbaikan.'], 422);
            }
            if ($perbaikanSelesai >= $penilaianMulai) {
                return response()->json(['success' => false, 'message' => 'Tanggal selesai perbaikan harus sebelum tanggal mulai penilaian akhir.'], 422);
            }

            $tahunAjaran = $request->input('tahun_ajaran');
            $tanggalFase1Mulai = Carbon::parse($request->tanggal_pendaftaran_mulai);
            $tahunFase1 = (int) $tanggalFase1Mulai->format('Y');
            $bulanFase1 = (int) $tanggalFase1Mulai->format('n');
            
            $tahunAjaranKalkulasi = $bulanFase1 >= 7 
                ? $tahunFase1 . '/' . ($tahunFase1 + 1)
                : ($tahunFase1 - 1) . '/' . $tahunFase1;

            if (!$tahunAjaran) {
                $tahunAjaran = $tahunAjaranKalkulasi;
            }

            if ($this->ruangKontrolService->isTahunAkademikMasaLalu($tahunAjaran)) {
                $tahunAkademikSekarang = TahunAjaranHelper::getTahunAjaranTerbaru();
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat membuat jadwal untuk tahun akademik masa lalu. Hanya tahun akademik sekarang (' . $tahunAkademikSekarang . ') dan tahun depan yang diperbolehkan.'
                ], 422);
            }

            // Set all existing active schedules for this academic year to inactive
            RuangKontrol::where('tahun_ajaran', $tahunAjaran)->update(['is_active' => false]);

            $ruangKontrol = RuangKontrol::create([
                'tahun_ajaran' => $tahunAjaran,
                'nama_history' => $request->nama_history,
                'status_pendaftaran' => 'tertutup',
                'status_review' => 'tertutup',
                'status_perbaikan' => 'tertutup',
                'status_penilaian_akhir' => 'tertutup',
                'tanggal_pendaftaran_mulai' => $request->tanggal_pendaftaran_mulai,
                'tanggal_pendaftaran_selesai' => $request->tanggal_pendaftaran_selesai,
                'tanggal_review_mulai' => $request->tanggal_review_mulai,
                'tanggal_review_selesai' => $request->tanggal_review_selesai,
                'tanggal_perbaikan_mulai' => $request->tanggal_perbaikan_mulai,
                'tanggal_perbaikan_selesai' => $request->tanggal_perbaikan_selesai,
                'tanggal_penilaian_akhir_mulai' => $request->tanggal_penilaian_akhir_mulai,
                'tanggal_penilaian_akhir_selesai' => $request->tanggal_penilaian_akhir_selesai,
                'is_active' => true,
                'id_pt' => auth()->id()
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dibuat.',
                'data' => $ruangKontrol
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors()->all();
            return response()->json([
                'success' => false,
                'message' => implode(' ', $errors)
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating jadwal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat jadwal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getJadwal($id)
    {
        try {
            $ruangKontrol = RuangKontrol::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $ruangKontrol
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    public function updateJadwal(Request $request, $id)
    {
        $ruangKontrol = RuangKontrol::findOrFail($id);

        $tahunAjaranTerbaru = TahunAjaranHelper::getTahunAjaranTerbaru();
        $tahunAjaranJadwal = $ruangKontrol->tahun_ajaran;
        $tahunPertamaJadwal = $this->ruangKontrolService->extractYearFromTahunAjaran($tahunAjaranJadwal);
        $tahunPertamaTerbaru = $this->ruangKontrolService->extractYearFromTahunAjaran($tahunAjaranTerbaru);

        if ($tahunPertamaJadwal < $tahunPertamaTerbaru) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal dengan tahun ajaran yang sudah lewat tidak dapat diupdate. Hanya jadwal tahun ajaran sekarang (' . $tahunAjaranTerbaru . ') dan tahun ajaran depan yang bisa diupdate.'
            ], 422);
        }

        $request->validate([
            'nama_history' => 'required|string|max:255',
            'tanggal_pendaftaran_mulai' => 'required|date',
            'tanggal_pendaftaran_selesai' => 'required|date|after:tanggal_pendaftaran_mulai',
            'tanggal_review_mulai' => 'required|date',
            'tanggal_review_selesai' => 'required|date|after:tanggal_review_mulai',
            'tanggal_perbaikan_mulai' => 'required|date',
            'tanggal_perbaikan_selesai' => 'required|date|after:tanggal_perbaikan_mulai',
            'tanggal_penilaian_akhir_mulai' => 'required|date',
            'tanggal_penilaian_akhir_selesai' => 'required|date|after:tanggal_penilaian_akhir_mulai',
        ]);

        // Chronological order
        $pendSelesai = strtotime($request->tanggal_pendaftaran_selesai);
        $reviewMulai = strtotime($request->tanggal_review_mulai);
        $reviewSelesai = strtotime($request->tanggal_review_selesai);
        $perbaikanMulai = strtotime($request->tanggal_perbaikan_mulai);
        $perbaikanSelesai = strtotime($request->tanggal_perbaikan_selesai);
        $penilaianMulai = strtotime($request->tanggal_penilaian_akhir_mulai);

        if ($pendSelesai >= $reviewMulai) {
            return response()->json(['success' => false, 'message' => 'Tanggal selesai pendaftaran harus sebelum tanggal mulai review.'], 422);
        }
        if ($reviewSelesai >= $perbaikanMulai) {
            return response()->json(['success' => false, 'message' => 'Tanggal selesai review harus sebelum tanggal mulai perbaikan.'], 422);
        }
        if ($perbaikanSelesai >= $penilaianMulai) {
            return response()->json(['success' => false, 'message' => 'Tanggal selesai perbaikan harus sebelum tanggal mulai penilaian akhir.'], 422);
        }

        try {
            $ruangKontrol->update([
                'nama_history' => $request->nama_history,
                'tanggal_pendaftaran_mulai' => $request->tanggal_pendaftaran_mulai,
                'tanggal_pendaftaran_selesai' => $request->tanggal_pendaftaran_selesai,
                'tanggal_review_mulai' => $request->tanggal_review_mulai,
                'tanggal_review_selesai' => $request->tanggal_review_selesai,
                'tanggal_perbaikan_mulai' => $request->tanggal_perbaikan_mulai,
                'tanggal_perbaikan_selesai' => $request->tanggal_perbaikan_selesai,
                'tanggal_penilaian_akhir_mulai' => $request->tanggal_penilaian_akhir_mulai,
                'tanggal_penilaian_akhir_selesai' => $request->tanggal_penilaian_akhir_selesai,
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diperbarui.',
                'data' => $ruangKontrol
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteJadwal($id)
    {
        $ruangKontrol = RuangKontrol::findOrFail($id);
        
        // Cek apakah tahun ajaran adalah tahun ajaran yang sudah lewat (tidak bisa dihapus)
        $tahunAjaranTerbaru = TahunAjaranHelper::getTahunAjaranTerbaru();
        $tahunAjaranJadwal = $ruangKontrol->tahun_ajaran;
        
        // Extract tahun pertama dari tahun ajaran untuk perbandingan
        $tahunPertamaJadwal = $this->ruangKontrolService->extractYearFromTahunAjaran($tahunAjaranJadwal);
        $tahunPertamaTerbaru = $this->ruangKontrolService->extractYearFromTahunAjaran($tahunAjaranTerbaru);
        
        if ($tahunPertamaJadwal < $tahunPertamaTerbaru) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal dengan tahun ajaran yang sudah lewat tidak dapat dihapus. Jadwal ini hanya untuk melihat history.'
            ], 422);
        }
        
        try {
            $ruangKontrol->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function activateJadwal($id)
    {
        try {
            $ruangKontrol = RuangKontrol::findOrFail($id);
            
            $tahunSekarang = (int) date('Y');
            $tahunAjaran = $this->ruangKontrolService->extractYearFromTahunAjaran($ruangKontrol->tahun_ajaran);
            
            if ($tahunAjaran < $tahunSekarang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal tahun masa lalu tidak dapat diaktifkan. Hanya jadwal tahun sekarang (' . $tahunSekarang . ') dan tahun depan yang bisa diaktifkan.'
                ], 422);
            }
            
            RuangKontrol::where('tahun_ajaran', $ruangKontrol->tahun_ajaran)->update(['is_active' => false]);
            $ruangKontrol->update(['is_active' => true]);


            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diaktifkan.',
                'data' => $ruangKontrol
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
