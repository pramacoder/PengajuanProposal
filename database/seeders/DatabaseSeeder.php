<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            // Cleanup database terlebih dahulu
            CleanupSeeder::class,
            
            // Seed data master
            FakultasSeeder::class,
            ProdiSeeder::class,
            DosenSeeder::class,
            MahasiswaSeeder::class,
            OperatorSeeder::class,
            ReviewerSeeder::class,
            RuangKontrolSeeder::class,
            
            // Seed data yang bergantung pada master
            ProposalSeeder::class,
            DokumenSeeder::class,
            NilaiAdministratifSeeder::class,
            NilaiSubstantifSeeder::class,
            HasilFinalSeeder::class,
            NotificationSeeder::class,
            
            // Cleanup proposal 2025 untuk testing pengajuan proposal tahun ini
            CleanupProposal2025Seeder::class,
        ]);
    }
}