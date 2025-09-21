<?php

namespace Database\Seeders;

use App\Models\Reviewer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReviewerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 reviewers for testing
        Reviewer::create([
            'nama_reviewer' => 'Prof. Dr. Maria Sari, M.Sc',
            'email_reviewer' => 'maria.sari@univ.ac.id',
            'no_hp_reviewer' => '081234567894',
            'password' => Hash::make('reviewer123'),
            'role' => 'reviewer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Reviewer::create([
            'nama_reviewer' => 'Dr. Eng. Rudi Hartono, S.T, M.T',
            'email_reviewer' => 'rudi.hartono@univ.ac.id',
            'no_hp_reviewer' => '081234567895',
            'password' => Hash::make('reviewer123'),
            'role' => 'reviewer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Reviewer::create([
            'nama_reviewer' => 'Dr. Indra Kusuma, S.Kom, M.Kom',
            'email_reviewer' => 'indra.kusuma@univ.ac.id',
            'no_hp_reviewer' => '081234567896',
            'password' => Hash::make('reviewer123'),
            'role' => 'reviewer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Reviewer::create([
            'nama_reviewer' => 'Prof. Dr. Susi Susanti, M.Pd',
            'email_reviewer' => 'susi.susanti@univ.ac.id',
            'no_hp_reviewer' => '081234567897',
            'password' => Hash::make('reviewer123'),
            'role' => 'reviewer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Reviewer::create([
            'nama_reviewer' => 'Dr. Agus Setiawan, S.T, M.Eng',
            'email_reviewer' => 'agus.setiawan@univ.ac.id',
            'no_hp_reviewer' => '081234567898',
            'password' => Hash::make('reviewer123'),
            'role' => 'reviewer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        echo "Seeder reviewer berhasil dibuat!\n";
        echo "Total reviewer: " . Reviewer::count() . "\n";
        echo "Akun reviewer untuk testing:\n";
        echo "1. maria.sari@univ.ac.id / reviewer123\n";
        echo "2. rudi.hartono@univ.ac.id / reviewer123\n";
        echo "3. indra.kusuma@univ.ac.id / reviewer123\n";
        echo "4. susi.susanti@univ.ac.id / reviewer123\n";
        echo "5. agus.setiawan@univ.ac.id / reviewer123\n";
    }
}