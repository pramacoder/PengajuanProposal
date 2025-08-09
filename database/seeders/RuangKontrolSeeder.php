<?php

namespace Database\Seeders;

use App\Models\RuangKontrol;
use App\Models\Pt;
use Illuminate\Database\Seeder;

class RuangKontrolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing PT IDs
        $ptIds = Pt::pluck('id_pt')->toArray();
        
        // Create ruang kontrol for each PT
        foreach ($ptIds as $ptId) {
            RuangKontrol::factory()->create([
                'id_pt' => $ptId,
                'status_pendaftaran' => 'terbuka', // Most should be open for registration
                'status_perbaikan' => 'tidak_perlu_perbaikan',
            ]);
        }

        // Create some additional ruang kontrol with different statuses
        RuangKontrol::factory()->count(3)->create([
            'status_pendaftaran' => 'tertutup',
            'status_perbaikan' => 'perlu_perbaikan',
        ]);
    }
}