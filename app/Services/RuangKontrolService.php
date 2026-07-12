<?php

namespace App\Services;

use App\Models\RuangKontrol;
use App\Helpers\TahunAjaranHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RuangKontrolService
{
    /**
     * Extract year from tahun ajaran format (support "2025/2026")
     * Untuk tahun akademik, ambil tahun pertama sebagai referensi
     */
    public function extractYearFromTahunAjaran($tahunAjaran)
    {
        if (strpos($tahunAjaran, '/') !== false) {
            // Format "2025/2026" - ambil tahun pertama
            $parts = explode('/', $tahunAjaran);
            return (int) $parts[0];
        }
        // Jika format lama (tanpa slash), kembalikan sebagai integer
        return (int) $tahunAjaran;
    }

    /**
     * Check apakah tahun akademik adalah tahun masa lalu
     */
    public function isTahunAkademikMasaLalu($tahunAjaran)
    {
        $tahunAkademikSekarang = TahunAjaranHelper::getTahunAjaranTerbaru();
        $tahunPertamaSekarang = (int) explode('/', $tahunAkademikSekarang)[0];
        $tahunPertama = $this->extractYearFromTahunAjaran($tahunAjaran);
        
        return $tahunPertama < $tahunPertamaSekarang;
    }

    /**
     * Check and auto-activate phases based on dates.
     * Only one phase can be open at a time. Processes phases in order: pendaftaran -> review -> perbaikan -> penilaian_akhir.
     */
    public function checkAndUpdateAutoActivation($ruangKontrol)
    {
        $now = now();
        $updated = false;
        $phaseConfig = [
            'pendaftaran'     => ['mulai' => 'tanggal_pendaftaran_mulai',      'selesai' => 'tanggal_pendaftaran_selesai',      'status' => 'status_pendaftaran'],
            'review'          => ['mulai' => 'tanggal_review_mulai',           'selesai' => 'tanggal_review_selesai',           'status' => 'status_review'],
            'perbaikan'       => ['mulai' => 'tanggal_perbaikan_mulai',        'selesai' => 'tanggal_perbaikan_selesai',        'status' => 'status_perbaikan'],
            'penilaian_akhir' => ['mulai' => 'tanggal_penilaian_akhir_mulai', 'selesai' => 'tanggal_penilaian_akhir_selesai', 'status' => 'status_penilaian_akhir'],
        ];

        // -------------------------------------------------------
        // Pass 1: Tutup setiap fase yang sudah melewati tanggal selesai
        // Gunakan endOfDay() agar fase dianggap aktif hingga akhir hari tersebut
        // -------------------------------------------------------
        foreach (RuangKontrol::PHASES as $phase) {
            $cfg        = $phaseConfig[$phase];
            $selesaiRaw = $ruangKontrol->{$cfg['selesai']};
            $statusAttr = $cfg['status'];

            if (!$selesaiRaw) {
                continue;
            }

            // Tanggal selesai dianggap berakhir pada 23:59:59 hari tersebut
            $selesai = Carbon::parse($selesaiRaw)->endOfDay();

            if ($now->gt($selesai) && $ruangKontrol->{$statusAttr} === 'terbuka') {
                $ruangKontrol->{$statusAttr} = 'tertutup';
                $updated = true;
            }
        }

        // -------------------------------------------------------
        // Pass 2: Buka fase yang sedang dalam rentang tanggalnya (jika belum ada yang terbuka)
        // Hanya satu fase yang bisa terbuka pada satu waktu
        // -------------------------------------------------------
        $anyOpen = false;
        foreach (RuangKontrol::PHASES as $phase) {
            $cfg        = $phaseConfig[$phase];
            $mulaiRaw   = $ruangKontrol->{$cfg['mulai']};
            $selesaiRaw = $ruangKontrol->{$cfg['selesai']};
            $statusAttr = $cfg['status'];

            if (!$mulaiRaw || !$selesaiRaw) {
                continue;
            }

            $mulai   = Carbon::parse($mulaiRaw)->startOfDay();
            $selesai = Carbon::parse($selesaiRaw)->endOfDay();

            if ($anyOpen) {
                // Sudah ada fase yang terbuka, tutup yang lain
                if ($ruangKontrol->{$statusAttr} === 'terbuka') {
                    $ruangKontrol->{$statusAttr} = 'tertutup';
                    $updated = true;
                }
                continue;
            }

            if ($now->between($mulai, $selesai)) {
                // Fase ini seharusnya terbuka
                if ($ruangKontrol->{$statusAttr} !== 'terbuka') {
                    foreach (RuangKontrol::PHASES as $p) {
                        $ruangKontrol->{$phaseConfig[$p]['status']} = ($p === $phase) ? 'terbuka' : 'tertutup';
                    }
                    $updated = true;
                }
                $anyOpen = true;
            }
        }

        if ($updated) {
            $ruangKontrol->save();
            Log::info('Auto-activation updated', [
                'ruang_kontrol_id' => $ruangKontrol->id_ruang_kontrol,
                'phases' => [
                    'pendaftaran'     => $ruangKontrol->status_pendaftaran,
                    'review'          => $ruangKontrol->status_review,
                    'perbaikan'       => $ruangKontrol->status_perbaikan,
                    'penilaian_akhir' => $ruangKontrol->status_penilaian_akhir,
                ],
            ]);
        }
    }

    /**
     * Get current active phase (all 4 phases)
     */
    public function getActivePhaseData()
    {
        $tahunAjaranTerbaru = TahunAjaranHelper::getTahunAjaranTerbaru();
        $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
            ->where('is_active', true)
            ->first();

        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if (!$ruangKontrol) {
            return [
                'active_phase' => null,
                'phases' => [
                    'pendaftaran' => 'tertutup',
                    'review' => 'tertutup',
                    'perbaikan' => 'tertutup',
                    'penilaian_akhir' => 'tertutup',
                ],
            ];
        }

        // Jalankan auto-check untuk memastikan status up-to-date
        if ($ruangKontrol->is_active) {
            $this->checkAndUpdateAutoActivation($ruangKontrol);
            $ruangKontrol->refresh();
        }

        return [
            'active_phase' => $ruangKontrol->getActivePhase(),
            'phases' => [
                'pendaftaran' => $ruangKontrol->status_pendaftaran,
                'review' => $ruangKontrol->status_review,
                'perbaikan' => $ruangKontrol->status_perbaikan,
                'penilaian_akhir' => $ruangKontrol->status_penilaian_akhir,
            ],
        ];
    }
}
