<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodis = [
            // Fakultas Ilmu Budaya (id_fakultas = 1)
            ['nama_prodi' => 'Antropologi Budaya',        'kode_prodi' => 'ANT',   'id_fakultas' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Arkeologi',                 'kode_prodi' => 'ARK',   'id_fakultas' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Ilmu Sejarah',              'kode_prodi' => 'SEJ',   'id_fakultas' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Sastra Bali',               'kode_prodi' => 'SBA',   'id_fakultas' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Sastra Indonesia',          'kode_prodi' => 'SIN',   'id_fakultas' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Sastra Inggris',            'kode_prodi' => 'SING',  'id_fakultas' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Sastra Jepang',             'kode_prodi' => 'SJA',   'id_fakultas' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Sastra Jawa Kuno',          'kode_prodi' => 'SJK',   'id_fakultas' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Fakultas Kedokteran (id_fakultas = 2)
            ['nama_prodi' => 'Pendidikan Dokter',         'kode_prodi' => 'PD',    'id_fakultas' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Pendidikan Dokter Gigi',    'kode_prodi' => 'PDG',   'id_fakultas' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Ilmu Keperawatan',          'kode_prodi' => 'IK',    'id_fakultas' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Kesehatan Masyarakat',      'kode_prodi' => 'KM',    'id_fakultas' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Psikologi',                 'kode_prodi' => 'PSI',   'id_fakultas' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Fisioterapi',               'kode_prodi' => 'FTR',   'id_fakultas' => 2, 'created_at' => now(), 'updated_at' => now()],

            // Fakultas Hukum (id_fakultas = 3)
            ['nama_prodi' => 'Ilmu Hukum',                'kode_prodi' => 'IH',    'id_fakultas' => 3, 'created_at' => now(), 'updated_at' => now()],

            // Fakultas Teknik (id_fakultas = 4)
            ['nama_prodi' => 'Arsitektur',                'kode_prodi' => 'ARS',   'id_fakultas' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Teknik Elektro',            'kode_prodi' => 'TE',    'id_fakultas' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Teknik Mesin',              'kode_prodi' => 'TM',    'id_fakultas' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Teknik Sipil',              'kode_prodi' => 'TS',    'id_fakultas' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Teknik Industri',           'kode_prodi' => 'TIN',   'id_fakultas' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Teknik Lingkungan',         'kode_prodi' => 'TL',    'id_fakultas' => 4, 'created_at' => now(), 'updated_at' => now()],
            // Catatan: pakai kode_prodi unik.
            ['nama_prodi' => 'Teknologi Informasi',       'kode_prodi' => 'TIF',   'id_fakultas' => 4, 'created_at' => now(), 'updated_at' => now()],

            // Fakultas Pertanian (id_fakultas = 5)
            ['nama_prodi' => 'Agroekoteknologi',          'kode_prodi' => 'AGT',   'id_fakultas' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Agribisnis',                'kode_prodi' => 'AGB',   'id_fakultas' => 5, 'created_at' => now(), 'updated_at' => now()],

            // Fakultas Ekonomi dan Bisnis (id_fakultas = 6)
            ['nama_prodi' => 'Manajemen',                 'kode_prodi' => 'MNG',   'id_fakultas' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Ekonomi Pembangunan',       'kode_prodi' => 'EP',    'id_fakultas' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Akuntansi',                 'kode_prodi' => 'AKT',   'id_fakultas' => 6, 'created_at' => now(), 'updated_at' => now()],

            // Fakultas Peternakan (id_fakultas = 7)
            ['nama_prodi' => 'Peternakan',                'kode_prodi' => 'PET',   'id_fakultas' => 7, 'created_at' => now(), 'updated_at' => now()],

            // Fakultas Matematika dan Ilmu Pengetahuan Alam (id_fakultas = 8)
            ['nama_prodi' => 'Matematika',                'kode_prodi' => 'MAT',   'id_fakultas' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Fisika',                    'kode_prodi' => 'FIS',   'id_fakultas' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Kimia',                     'kode_prodi' => 'KIM',   'id_fakultas' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Biologi',                   'kode_prodi' => 'BIO',   'id_fakultas' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Informatika',               'kode_prodi' => 'IF',    'id_fakultas' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Farmasi',                   'kode_prodi' => 'FAR',   'id_fakultas' => 8, 'created_at' => now(), 'updated_at' => now()],

            // Fakultas Kedokteran Hewan (id_fakultas = 9)
            ['nama_prodi' => 'Kedokteran Hewan',          'kode_prodi' => 'KH',    'id_fakultas' => 9, 'created_at' => now(), 'updated_at' => now()],

            // Fakultas Teknologi Pertanian (id_fakultas = 10)
            ['nama_prodi' => 'Teknik Pertanian dan Biosistem', 'kode_prodi' => 'TPB',  'id_fakultas' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Teknologi Industri Pertanian',   'kode_prodi' => 'TIP',  'id_fakultas' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Teknologi Pangan',               'kode_prodi' => 'TPG',  'id_fakultas' => 10, 'created_at' => now(), 'updated_at' => now()],

            // Fakultas Pariwisata (id_fakultas = 11)
            ['nama_prodi' => 'Pariwisata',                'kode_prodi' => 'PAR',   'id_fakultas' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Destinasi Pariwisata',      'kode_prodi' => 'DP',    'id_fakultas' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Industri Perjalanan Wisata','kode_prodi' => 'IPW',   'id_fakultas' => 11, 'created_at' => now(), 'updated_at' => now()],

            // Fakultas Ilmu Sosial dan Ilmu Politik (id_fakultas = 12)
            ['nama_prodi' => 'Administrasi Negara',       'kode_prodi' => 'AN',    'id_fakultas' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Ilmu Politik',              'kode_prodi' => 'IP',    'id_fakultas' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Ilmu Komunikasi',           'kode_prodi' => 'IKOM',  'id_fakultas' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Hubungan Internasional',    'kode_prodi' => 'HI',    'id_fakultas' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Sosiologi',                 'kode_prodi' => 'SOS',   'id_fakultas' => 12, 'created_at' => now(), 'updated_at' => now()],

            // Fakultas Kelautan dan Perikanan (id_fakultas = 13)
            ['nama_prodi' => 'Ilmu Kelautan',             'kode_prodi' => 'IKL',   'id_fakultas' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['nama_prodi' => 'Manajemen Sumberdaya Perairan', 'kode_prodi' => 'MSP', 'id_fakultas' => 13, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('prodis')->insert($prodis);
    }
}
