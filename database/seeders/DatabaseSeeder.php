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

        // DISABLED FOR TESTING - Database akan kosong
        // $this->call([
        //     // Cleanup database terlebih dahulu
        //     CleanupSeeder::class,
            
        //     // Seed data master
        //     FakultasSeeder::class,
        //     ProdiSeeder::class,
        //     MahasiswaSeeder::class,
        //     DosenSeeder::class,
        //     PTSeeder::class,
        //     ReviewerSeeder::class,
        //     RuangKontrolSeeder::class,
            
        //     // Seed data yang bergantung pada master
        //     ProposalSeeder::class,
        //     DokumenSeeder::class,
        //     NilaiAdministratifSeeder::class,
        //     NilaiSubstantifSeeder::class,
        //     HasilFinalSeeder::class,
        //     NotificationSeeder::class,
        // ]);
    }
}