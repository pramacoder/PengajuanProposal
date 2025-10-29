<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FakultasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fakultas = [
            [
                'nama_fakultas' => 'Fakultas Ilmu Budaya',
                'kode_fakultas' => 'FIB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fakultas' => 'Fakultas Kedokteran',
                'kode_fakultas' => 'FK',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fakultas' => 'Fakultas Hukum',
                'kode_fakultas' => 'FH',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fakultas' => 'Fakultas Teknik',
                'kode_fakultas' => 'FT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fakultas' => 'Fakultas Pertanian',
                'kode_fakultas' => 'FP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fakultas' => 'Fakultas Ekonomi dan Bisnis',
                'kode_fakultas' => 'FEB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fakultas' => 'Fakultas Peternakan',
                'kode_fakultas' => 'FAPET',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fakultas' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam',
                'kode_fakultas' => 'FMIPA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fakultas' => 'Fakultas Kedokteran Hewan',
                'kode_fakultas' => 'FKH',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fakultas' => 'Fakultas Teknologi Pertanian',
                'kode_fakultas' => 'FTP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fakultas' => 'Fakultas Pariwisata',
                'kode_fakultas' => 'FAPAR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fakultas' => 'Fakultas Ilmu Sosial dan Ilmu Politik',
                'kode_fakultas' => 'FISIP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_fakultas' => 'Fakultas Kelautan dan Perikanan',
                'kode_fakultas' => 'FKP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('fakultas')->insert($fakultas);
    }
}

