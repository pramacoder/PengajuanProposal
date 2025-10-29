<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Seed data utama terlebih dahulu
            FakultasSeeder::class,
            ProdiSeeder::class,
            ReviewerSeeder::class,
            PtSeeder::class,
            DosenSeeder::class,
            MahasiswaSeeder::class,
            
            // Seed data proposal
            ProposalSeeder::class,
            
            // Seed data penilaian
            NilaiAdministratifSeeder::class,
            NilaiSubstantifSeeder::class,
            HasilFinalSeeder::class,
            
            // Seed data ruang kontrol
            RuangKontrolSeeder::class,
        ]);
    }
}