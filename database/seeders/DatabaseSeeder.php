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
            FakultasSeeder::class,
            ProdiSeeder::class,
            MahasiswaSeeder::class,
            DosenSeeder::class,
            PtSeeder::class,
            ReviewerSeeder::class,
            RuangKontrolSeeder::class,
            ProposalSeeder::class,
            DokumenSeeder::class,
            NilaiAdministratifSeeder::class,
            NilaiSubstantifSeeder::class,
            HasilFinalSeeder::class,
        ]);
    }
}