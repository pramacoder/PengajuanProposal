<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat mahasiswa sesuai kebutuhan proposal
        // Untuk 20 proposal, minimal butuh 20 × 3 = 60 mahasiswa
        // Kita buat 70 mahasiswa untuk memberikan fleksibilitas
        
        $faker = \Faker\Factory::create('id_ID');
        
        // Mahasiswa testing 1 (untuk testing manual)
        Mahasiswa::create([
            'nim' => '2021001003',
            'nama_mhs' => 'Budi Santoso',
            'prodi_mhs' => 'Sistem Informasi',
            'fakultas_mhs' => 'Teknik',
            'no_hp_mhs' => '081234567892',
            'email_mhs' => 'budi.santoso@student.univ.ac.id',
            'password' => Hash::make('123456'),
            'role' => 'mahasiswa',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Mahasiswa testing 2 (untuk testing manual)
        Mahasiswa::create([
            'nim' => '2021001004',
            'nama_mhs' => 'Dewi Sartika',
            'prodi_mhs' => 'Akuntansi',
            'fakultas_mhs' => 'Ekonomi',
            'no_hp_mhs' => '081234567893',
            'email_mhs' => 'dewi.sartika@student.univ.ac.id',
            'password' => Hash::make('123456'),
            'role' => 'mahasiswa',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Mahasiswa testing 3 (untuk testing manual)
        Mahasiswa::create([
            'nim' => '2021001005',
            'nama_mhs' => 'Rudi Hermawan',
            'prodi_mhs' => 'Teknik Industri',
            'fakultas_mhs' => 'Teknik',
            'no_hp_mhs' => '081234567894',
            'email_mhs' => 'rudi.hermawan@student.univ.ac.id',
            'password' => Hash::make('123456'),
            'role' => 'mahasiswa',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        
                // Mahasiswa testing 1 (untuk testing manual)
                Mahasiswa::create([
                    'nim' => '2021001010',
                    'nama_mhs' => 'Budi Satosi',
                    'prodi_mhs' => 'Sistem Informasi',
                    'fakultas_mhs' => 'Teknik',
                    'no_hp_mhs' => '081234567934',
                    'email_mhs' => 'budi.satosi@student.univ.ac.id',
                    'password' => Hash::make('123456'),
                    'role' => 'mahasiswa',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
        
                // Mahasiswa testing 2 (untuk testing manual)
                Mahasiswa::create([
                    'nim' => '2021001011',
                    'nama_mhs' => 'Dewi Rahma',
                    'prodi_mhs' => 'Akuntansi',
                    'fakultas_mhs' => 'Ekonomi',
                    'no_hp_mhs' => '081234567832',
                    'email_mhs' => 'dewi.rahma@student.univ.ac.id',
                    'password' => Hash::make('123456'),
                    'role' => 'mahasiswa',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
        
                // Mahasiswa testing 3 (untuk testing manual)
                Mahasiswa::create([
                    'nim' => '2021001012',
                    'nama_mhs' => 'Rudi Herman',
                    'prodi_mhs' => 'Teknik Industri',
                    'fakultas_mhs' => 'Teknik',
                    'no_hp_mhs' => '081234567765',
                    'email_mhs' => 'rudi.herman@student.univ.ac.id',
                    'password' => Hash::make('123456'),
                    'role' => 'mahasiswa',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);

        echo "Seeder mahasiswa berhasil dibuat!\n";
        echo "Total mahasiswa: " . Mahasiswa::count() . "\n";
        echo "Akun untuk testing:\n";
        echo "1. budi.santoso@student.univ.ac.id / 123456\n";
        echo "2. dewi.sartika@student.univ.ac.id / 123456\n";
        echo "3. rudi.hermawan@student.univ.ac.id / 123456\n";
        echo "Maksimal proposal yang bisa dibuat: " . floor(Mahasiswa::count() / 3) . "\n";
    }
}