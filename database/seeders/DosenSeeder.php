<?php

namespace Database\Seeders;

use App\Models\Dosen;
use Illuminate\Database\Seeder;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20 dosen
        Dosen::factory()->count(20)->create();
        
        // Create specific dosen for testing
        Dosen::factory()->create([
            'nuptk' => '12345678901234567890',
            'nama_dosen' => 'Dr. Budi Santoso',
            'gelar_depan' => 'Dr.',
            'gelar_belakang' => 'S.Kom, M.Kom',
            'email_dosen' => 'budi.santoso@univ.ac.id',
            'no_hp_dosen' => '081234567892',
        ]);

        Dosen::factory()->create([
            'nuptk' => '12345678901234567891',
            'nama_dosen' => 'Andi Prasetyo',
            'gelar_depan' => 'Ir.',
            'gelar_belakang' => 'S.T, M.T',
            'email_dosen' => 'andi.prasetyo@univ.ac.id',
            'no_hp_dosen' => '081234567893',
        ]);
    }
}