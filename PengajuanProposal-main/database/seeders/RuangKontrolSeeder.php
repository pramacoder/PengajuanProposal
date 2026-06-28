<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RuangKontrol;
use App\Models\User;
use App\Helpers\TahunAjaranHelper;

class RuangKontrolSeeder extends Seeder
{
    public function run(): void
    {
        $operator = User::where('role', 'operator')->first();

        if (!$operator) {
            $this->command->warn('No operator found. Please run PtSeeder first.');
            return;
        }

        $tahunAjaran = TahunAjaranHelper::getTahunAjaranTerbaru();

        RuangKontrol::updateOrCreate(
            ['tahun_ajaran' => $tahunAjaran],
            [
                'nama_history' => 'Jadwal Utama ' . $tahunAjaran,
                'status_pendaftaran' => 'terbuka',
                'status_review' => 'tertutup',
                'status_perbaikan' => 'tertutup',
                'status_penilaian_akhir' => 'tertutup',
                'tanggal_pendaftaran_mulai' => now()->startOfMonth(),
                'tanggal_pendaftaran_selesai' => now()->addMonths(2)->endOfMonth(),
                'tanggal_review_mulai' => now()->addMonths(3)->startOfMonth(),
                'tanggal_review_selesai' => now()->addMonths(4)->endOfMonth(),
                'tanggal_perbaikan_mulai' => now()->addMonths(5)->startOfMonth(),
                'tanggal_perbaikan_selesai' => now()->addMonths(6)->endOfMonth(),
                'tanggal_penilaian_akhir_mulai' => now()->addMonths(7)->startOfMonth(),
                'tanggal_penilaian_akhir_selesai' => now()->addMonths(8)->endOfMonth(),
                'is_active' => true,
                'id_pt' => $operator->id,
            ]
        );

        $this->command->info("Ruang Kontrol seeded for {$tahunAjaran} with 4 phases.");
    }
}
