<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RuangKontrol;
use App\Models\PT;

class RuangKontrolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first PT (operator) to assign to ruang kontrol
        $pt = PT::first();
        
        if (!$pt) {
            $this->command->warn('No PT found. Please run PtSeeder first.');
            return;
        }

        // Check if ruang kontrol already exists
        $existingRuangKontrol = RuangKontrol::first();
        
        if ($existingRuangKontrol) {
            $this->command->info('Ruang Kontrol already exists. Updating...');
            $existingRuangKontrol->update([
                'status_pendaftaran' => 'tertutup',
                'status_perbaikan' => 'tertutup',
                'tanggal_pendaftaran_mulai' => null,
                'tanggal_pendaftaran_selesai' => null,
                'tanggal_perbaikan_mulai' => null,
                'tanggal_perbaikan_selesai' => null,
                'id_pt' => $pt->id_pt
            ]);
        } else {
            $this->command->info('Creating new Ruang Kontrol...');
            RuangKontrol::create([
                'status_pendaftaran' => 'tertutup',
                'status_perbaikan' => 'tertutup',
                'tanggal_pendaftaran_mulai' => null,
                'tanggal_pendaftaran_selesai' => null,
                'tanggal_perbaikan_mulai' => null,
                'tanggal_perbaikan_selesai' => null,
                'id_pt' => $pt->id_pt
            ]);
        }
        
        $this->command->info('Ruang Kontrol seeded successfully.');
    }
}


