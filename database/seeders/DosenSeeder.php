<?php

namespace Database\Seeders;

use App\Models\Dosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific dosen for testing
        Dosen::factory()->create([
            'nuptk' => '12345678901234567890',
            'nama_dosen' => 'Dr. Budi Santoso',
            'gelar_depan' => 'Dr.',
            'gelar_belakang' => 'S.Kom, M.Kom',
            'email_dosen' => 'budi.santoso@univ.ac.id',
            'no_hp_dosen' => '081234567892',
            'password' => Hash::make('password123'),
        ]);

        Dosen::factory()->create([
            'nuptk' => '12345678901234567891',
            'nama_dosen' => 'Andi Prasetyo',
            'gelar_depan' => 'Ir.',
            'gelar_belakang' => 'S.T, M.T',
            'email_dosen' => 'andi.prasetyo@univ.ac.id',
            'no_hp_dosen' => '081234567893',
            'password' => Hash::make('password123'),
        ]);

        // Create test dosen dengan password yang mudah diingat
        Dosen::create([
            'nuptk' => 'TEST123456789012345',
            'nama_dosen' => 'Test Dosen',
            'gelar_depan' => 'Dr.',
            'gelar_belakang' => 'S.Kom, M.Kom',
            'email_dosen' => 'test.dosen@test.com',
            'no_hp_dosen' => '081234567890',
            'password' => Hash::make('12345678'),
            'role' => 'dosen',
            'is_active' => true,
        ]);
    }
}