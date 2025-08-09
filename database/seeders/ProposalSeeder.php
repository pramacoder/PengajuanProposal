<?php

namespace Database\Seeders;

use App\Models\Proposal;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class ProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing mahasiswa IDs
        $mahasiswaIds = Mahasiswa::pluck('id_mahasiswa')->toArray();
        
        // Create proposals with different statuses to represent the workflow
        
        // Draft proposals (5)
        Proposal::factory()->count(5)->draft()->create([
            'id_mahasiswa' => function() use ($mahasiswaIds) {
                return fake()->randomElement($mahasiswaIds);
            }
        ]);
        
        // Submitted proposals waiting for validation (8)
        Proposal::factory()->count(8)->submitted()->create([
            'id_mahasiswa' => function() use ($mahasiswaIds) {
                return fake()->randomElement($mahasiswaIds);
            }
        ]);
        
        // Validated proposals ready for administrative review (10)
        Proposal::factory()->count(10)->validated()->create([
            'id_mahasiswa' => function() use ($mahasiswaIds) {
                return fake()->randomElement($mahasiswaIds);
            }
        ]);
        
        // Proposals in administrative review (7)
        Proposal::factory()->count(7)->reviewAdministratif()->create([
            'id_mahasiswa' => function() use ($mahasiswaIds) {
                return fake()->randomElement($mahasiswaIds);
            }
        ]);
        
        // Proposals in substantive review (6)
        Proposal::factory()->count(6)->reviewSubstantif()->create([
            'id_mahasiswa' => function() use ($mahasiswaIds) {
                return fake()->randomElement($mahasiswaIds);
            }
        ]);
        
        // Proposals in revision stage (4)
        Proposal::factory()->count(4)->revisi()->create([
            'id_mahasiswa' => function() use ($mahasiswaIds) {
                return fake()->randomElement($mahasiswaIds);
            }
        ]);
        
        // Passed proposals (5)
        Proposal::factory()->count(5)->lolos()->create([
            'id_mahasiswa' => function() use ($mahasiswaIds) {
                return fake()->randomElement($mahasiswaIds);
            }
        ]);
        
        // Failed proposals (5)
        Proposal::factory()->count(5)->tidakLolos()->create([
            'id_mahasiswa' => function() use ($mahasiswaIds) {
                return fake()->randomElement($mahasiswaIds);
            }
        ]);

        // Create some specific test cases
        $testMahasiswa = Mahasiswa::where('nim', '2021001001')->first();
        if ($testMahasiswa) {
            Proposal::factory()->create([
                'judul_proposal' => 'Sistem Informasi Manajemen PKM Berbasis Web',
                'skim' => 'KC',
                'status_validasi' => 'valid',
                'status_final' => 'review_substantif',
                'id_mahasiswa' => $testMahasiswa->id_mahasiswa,
            ]);
        }

        $testMahasiswa2 = Mahasiswa::where('nim', '2021001002')->first();
        if ($testMahasiswa2) {
            Proposal::factory()->create([
                'judul_proposal' => 'Aplikasi Mobile E-Commerce UMKM',
                'skim' => 'PM',
                'status_validasi' => 'pending',
                'status_final' => 'submitted',
                'id_mahasiswa' => $testMahasiswa2->id_mahasiswa,
            ]);
        }
    }
}