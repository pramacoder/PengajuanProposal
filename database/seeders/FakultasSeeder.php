<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fakultas;
use Illuminate\Support\Carbon;

class FakultasSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 13 Fakultas UNUD (kode bebas asalkan konsisten & unik)
        $rows = [
            ['nama_fakultas' => 'Fakultas Ilmu Budaya',                    'kode_fakultas' => 'FIB'],
            ['nama_fakultas' => 'Fakultas Kedokteran',                    'kode_fakultas' => 'FK'],
            ['nama_fakultas' => 'Fakultas Hukum',                         'kode_fakultas' => 'FH'],
            ['nama_fakultas' => 'Fakultas Teknik',                        'kode_fakultas' => 'FT'],
            ['nama_fakultas' => 'Fakultas Pertanian',                     'kode_fakultas' => 'FP'],
            ['nama_fakultas' => 'Fakultas Ekonomi dan Bisnis',            'kode_fakultas' => 'FEB'],
            ['nama_fakultas' => 'Fakultas Peternakan',                    'kode_fakultas' => 'FAPET'],
            ['nama_fakultas' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'kode_fakultas' => 'FMIPA'],
            ['nama_fakultas' => 'Fakultas Kedokteran Hewan',              'kode_fakultas' => 'FKH'],
            ['nama_fakultas' => 'Fakultas Teknologi Pertanian',           'kode_fakultas' => 'FTP'],
            ['nama_fakultas' => 'Fakultas Pariwisata',                    'kode_fakultas' => 'FPAR'],
            ['nama_fakultas' => 'Fakultas Ilmu Sosial dan Ilmu Politik',  'kode_fakultas' => 'FISIP'],
            ['nama_fakultas' => 'Fakultas Kelautan dan Perikanan',        'kode_fakultas' => 'FKP'],
        ];

        $rows = array_map(fn($r) => $r + ['created_at' => $now, 'updated_at' => $now], $rows);

        Fakultas::upsert(
            $rows,
            ['kode_fakultas'],                 // unique key
            ['nama_fakultas', 'updated_at']    // updates
        );
    }
}
