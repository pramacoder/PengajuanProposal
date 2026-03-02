<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswas = [
            ['identifier' => '2001234567', 'name' => 'I Made Surya Wijaya',                'email' => 'made.surya@student.unud.ac.id',      'phone' => '081234567200', 'metadata' => ['prodi_name' => 'Teknologi Informasi',  'fakultas_name' => 'Fakultas Teknik', 'is_ketua' => false]],
            ['identifier' => '2001234568', 'name' => 'I Gusti Ayu Made Sari Dewi',         'email' => 'sari.dewi@student.unud.ac.id',        'phone' => '081234567201', 'metadata' => ['prodi_name' => 'Teknologi Informasi',  'fakultas_name' => 'Fakultas Teknik', 'is_ketua' => false]],
            ['identifier' => '2001234569', 'name' => 'I Wayan Gede Suardana',              'email' => 'suardana@student.unud.ac.id',         'phone' => '081234567202', 'metadata' => ['prodi_name' => 'Teknologi Informasi',  'fakultas_name' => 'Fakultas Teknik', 'is_ketua' => false]],
            ['identifier' => '2001234570', 'name' => 'I Made Sudarma Putra',               'email' => 'sudarma.putra@student.unud.ac.id',    'phone' => '081234567203', 'metadata' => ['prodi_name' => 'Matematika',           'fakultas_name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'is_ketua' => false]],
            ['identifier' => '2001234571', 'name' => 'I Gusti Agung Ayu Ratna Dewi',       'email' => 'ratna.dewi@student.unud.ac.id',       'phone' => '081234567204', 'metadata' => ['prodi_name' => 'Matematika',           'fakultas_name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'is_ketua' => false]],
            ['identifier' => '2001234572', 'name' => 'I Wayan Gede Artawan Eka Putra',     'email' => 'artawan.eka@student.unud.ac.id',      'phone' => '081234567205', 'metadata' => ['prodi_name' => 'Matematika',           'fakultas_name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'is_ketua' => false]],
            ['identifier' => '2001234573', 'name' => 'I Made Rai Pramana',                 'email' => 'rai.pramana@student.unud.ac.id',      'phone' => '081234567206', 'metadata' => ['prodi_name' => 'Fisika',               'fakultas_name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'is_ketua' => false]],
            ['identifier' => '2001234574', 'name' => 'I Wayan Gede Suharta',               'email' => 'suharta@student.unud.ac.id',          'phone' => '081234567207', 'metadata' => ['prodi_name' => 'Fisika',               'fakultas_name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'is_ketua' => false]],
            ['identifier' => '2001234575', 'name' => 'I Made Sudiana',                     'email' => 'made.sudiana@student.unud.ac.id',     'phone' => '081234567208', 'metadata' => ['prodi_name' => 'Fisika',               'fakultas_name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'is_ketua' => false]],
            ['identifier' => '2001234576', 'name' => 'I Gusti Ayu Made Sari',              'email' => 'made.sari@student.unud.ac.id',        'phone' => '081234567209', 'metadata' => ['prodi_name' => 'Kimia',                'fakultas_name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'is_ketua' => false]],
            ['identifier' => '2001234577', 'name' => 'I Wayan Gede Artawan',               'email' => 'artawan@student.unud.ac.id',          'phone' => '081234567210', 'metadata' => ['prodi_name' => 'Kimia',                'fakultas_name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'is_ketua' => false]],
            ['identifier' => '2001234578', 'name' => 'I Made Sudarsana',                   'email' => 'sudarsana@student.unud.ac.id',        'phone' => '081234567211', 'metadata' => ['prodi_name' => 'Kimia',                'fakultas_name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'is_ketua' => false]],
            ['identifier' => '2001234579', 'name' => 'I Made Surya Putra',                 'email' => 'surya.putra@student.unud.ac.id',      'phone' => '081234567212', 'metadata' => ['prodi_name' => 'Biologi',              'fakultas_name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'is_ketua' => false]],
            ['identifier' => '2001234580', 'name' => 'I Gusti Ayu Sari Lestari',           'email' => 'sari.lestari@student.unud.ac.id',     'phone' => '081234567213', 'metadata' => ['prodi_name' => 'Biologi',              'fakultas_name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'is_ketua' => false]],
            ['identifier' => '2001234581', 'name' => 'I Wayan Gede Suardika',              'email' => 'suardika@student.unud.ac.id',         'phone' => '081234567214', 'metadata' => ['prodi_name' => 'Biologi',              'fakultas_name' => 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'is_ketua' => false]],
            ['identifier' => '2001234582', 'name' => 'I Made Sudarma',                     'email' => 'made.sudarma@student.unud.ac.id',     'phone' => '081234567215', 'metadata' => ['prodi_name' => 'Manajemen',            'fakultas_name' => 'Fakultas Ekonomi dan Bisnis', 'is_ketua' => false]],
            ['identifier' => '2001234583', 'name' => 'I Gusti Agung Ratna Sari',           'email' => 'ratna.sari@student.unud.ac.id',       'phone' => '081234567216', 'metadata' => ['prodi_name' => 'Manajemen',            'fakultas_name' => 'Fakultas Ekonomi dan Bisnis', 'is_ketua' => false]],
            ['identifier' => '2001234584', 'name' => 'I Wayan Gede Eka Putra',             'email' => 'eka.putra@student.unud.ac.id',        'phone' => '081234567217', 'metadata' => ['prodi_name' => 'Manajemen',            'fakultas_name' => 'Fakultas Ekonomi dan Bisnis', 'is_ketua' => false]],
            ['identifier' => '2001234585', 'name' => 'I Made Rai Wijaya',                  'email' => 'rai.wijaya@student.unud.ac.id',       'phone' => '081234567218', 'metadata' => ['prodi_name' => 'Akuntansi',            'fakultas_name' => 'Fakultas Ekonomi dan Bisnis', 'is_ketua' => false]],
            ['identifier' => '2001234586', 'name' => 'I Wayan Suharta Putra',              'email' => 'suharta.putra@student.unud.ac.id',    'phone' => '081234567219', 'metadata' => ['prodi_name' => 'Akuntansi',            'fakultas_name' => 'Fakultas Ekonomi dan Bisnis', 'is_ketua' => false]],
            ['identifier' => '2001234587', 'name' => 'I Made Sudiana Eka',                 'email' => 'sudiana.eka@student.unud.ac.id',      'phone' => '081234567220', 'metadata' => ['prodi_name' => 'Akuntansi',            'fakultas_name' => 'Fakultas Ekonomi dan Bisnis', 'is_ketua' => false]],
            ['identifier' => '2001234588', 'name' => 'I Gusti Ayu Sari Purnama',           'email' => 'sari.purnama@student.unud.ac.id',     'phone' => '081234567221', 'metadata' => ['prodi_name' => 'Teknik Sipil',         'fakultas_name' => 'Fakultas Teknik', 'is_ketua' => false]],
            ['identifier' => '2001234589', 'name' => 'I Wayan Artawan Putra',              'email' => 'artawan.putra@student.unud.ac.id',    'phone' => '081234567222', 'metadata' => ['prodi_name' => 'Teknik Sipil',         'fakultas_name' => 'Fakultas Teknik', 'is_ketua' => false]],
            ['identifier' => '2001234590', 'name' => 'I Made Sudarsana Wijaya',            'email' => 'sudarsana.wijaya@student.unud.ac.id', 'phone' => '081234567223', 'metadata' => ['prodi_name' => 'Teknik Sipil',         'fakultas_name' => 'Fakultas Teknik', 'is_ketua' => false]],
            ['identifier' => '2001234591', 'name' => 'I Made Surya Dharma',                'email' => 'surya.dharma@student.unud.ac.id',     'phone' => '081234567224', 'metadata' => ['prodi_name' => 'Teknik Elektro',       'fakultas_name' => 'Fakultas Teknik', 'is_ketua' => false]],
        ];

        foreach ($mahasiswas as $mhs) {
            DB::table('users')->insert([
                'identifier' => $mhs['identifier'],
                'name' => $mhs['name'],
                'email' => $mhs['email'],
                'phone' => $mhs['phone'],
                'password' => Hash::make('password123'),
                'role' => 'mahasiswa',
                'is_active' => true,
                'metadata' => json_encode($mhs['metadata']),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
