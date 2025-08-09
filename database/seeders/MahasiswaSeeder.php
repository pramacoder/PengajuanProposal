<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 50 mahasiswa
        Mahasiswa::factory()->count(50)->create();
        
        // Create specific mahasiswa for testing
        Mahasiswa::factory()->create([
            'nim' => '2021001001',
            'nama_mhs' => 'Ahmad Fauzi',
            'prodi_mhs' => 'Teknik Informatika',
            'fakultas_mhs' => 'Teknik',
            'no_hp_mhs' => '081234567890',
            'email_mhs' => 'ahmad.fauzi@student.univ.ac.id',
        ]);

        Mahasiswa::factory()->create([
            'nim' => '2021001002',
            'nama_mhs' => 'Siti Nurhaliza',
            'prodi_mhs' => 'Manajemen',
            'fakultas_mhs' => 'Ekonomi',
            'no_hp_mhs' => '081234567891',
            'email_mhs' => 'siti.nurhaliza@student.univ.ac.id',
        ]);
    }
}