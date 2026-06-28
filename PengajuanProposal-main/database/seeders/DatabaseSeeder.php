<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            FakultasSeeder::class,
            ProdiSeeder::class,

            // All user roles → unified 'users' table
            MahasiswaSeeder::class,
            DosenSeeder::class,
            ReviewerSeeder::class,
            PtSeeder::class,
            PimpinanPTSeeder::class,

            ProposalSeeder::class,

            NilaiAdministratifSeeder::class,
            NilaiSubstantifSeeder::class,
            HasilSemiFinalSeeder::class,
            HasilFinalSeeder::class,

            RuangKontrolSeeder::class,
        ]);
    }
}
