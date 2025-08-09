<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fakultas;

class FakultasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fakultas = [
            ['nama_fakultas' => 'Fakultas Teknik', 'kode_fakultas' => 'FT'],
            ['nama_fakultas' => 'Fakultas Ekonomi dan Bisnis', 'kode_fakultas' => 'FEB'],
            ['nama_fakultas' => 'Fakultas Ilmu Komputer', 'kode_fakultas' => 'FILKOM'],
            ['nama_fakultas' => 'Fakultas Hukum', 'kode_fakultas' => 'FH'],
            ['nama_fakultas' => 'Fakultas Kedokteran', 'kode_fakultas' => 'FK'],
            ['nama_fakultas' => 'Fakultas Farmasi', 'kode_fakultas' => 'FF'],
            ['nama_fakultas' => 'Fakultas Pertanian', 'kode_fakultas' => 'FP'],
            ['nama_fakultas' => 'Fakultas Peternakan', 'kode_fakultas' => 'FAPET'],
            ['nama_fakultas' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'kode_fakultas' => 'FMIPA'],
            ['nama_fakultas' => 'Fakultas Ilmu Sosial dan Ilmu Politik', 'kode_fakultas' => 'FISIP'],
        ];

        foreach ($fakultas as $f) {
            Fakultas::create($f);
        }
    }
}
