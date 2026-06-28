<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RuangKontrol>
 */
class RuangKontrolFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tahun_ajaran'                      => '2025/2026',
            'is_active'                         => true,
            'status_pendaftaran'                => 'tertutup',
            'status_review'                     => 'tertutup',
            'status_perbaikan'                  => 'tertutup',
            'status_penilaian_akhir'            => 'tertutup',
            'tanggal_pendaftaran_mulai'         => now()->subMonth(),
            'tanggal_pendaftaran_selesai'       => now()->addMonth(),
            'tanggal_review_mulai'              => now()->addMonth(),
            'tanggal_review_selesai'            => now()->addMonths(2),
            'tanggal_perbaikan_mulai'           => now()->addMonths(2),
            'tanggal_perbaikan_selesai'         => now()->addMonths(3),
            'tanggal_penilaian_akhir_mulai'     => now()->addMonths(3),
            'tanggal_penilaian_akhir_selesai'   => now()->addMonths(4),
            'tanggal_review_pertama_mulai'      => null,
            'tanggal_review_pertama_selesai'    => null,
            'nama_history'                      => 'Test Jadwal',
            'dana_min_operator'                 => 5000000,
            'dana_max_operator'                 => 30000000,
            'dana_min_belmawa'                  => 5000000,
            'dana_max_belmawa'                  => 70000000,
            'id_pt'                             => \App\Models\User::factory()->operator(),
        ];
    }

    /** State: fase pendaftaran terbuka */
    public function pendaftaranTerbuka(): static
    {
        return $this->state(fn () => ['status_pendaftaran' => 'terbuka']);
    }

    /** State: fase review terbuka */
    public function reviewTerbuka(): static
    {
        return $this->state(fn () => ['status_review' => 'terbuka']);
    }

    /** State: fase perbaikan terbuka */
    public function perbaikanTerbuka(): static
    {
        return $this->state(fn () => ['status_perbaikan' => 'terbuka']);
    }

    /** State: fase penilaian akhir terbuka */
    public function penilaianAkhirTerbuka(): static
    {
        return $this->state(fn () => ['status_penilaian_akhir' => 'terbuka']);
    }

    /** State: semua fase tertutup */
    public function semuaTertutup(): static
    {
        return $this->state(fn () => [
            'status_pendaftaran'     => 'tertutup',
            'status_review'          => 'tertutup',
            'status_perbaikan'       => 'tertutup',
            'status_penilaian_akhir' => 'tertutup',
        ]);
    }
}
