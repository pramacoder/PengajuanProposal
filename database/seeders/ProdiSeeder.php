<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Prodi;
use App\Models\Fakultas;

class ProdiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID fakultas
        $ft = Fakultas::where('kode_fakultas', 'FT')->first();
        $feb = Fakultas::where('kode_fakultas', 'FEB')->first();
        $filkom = Fakultas::where('kode_fakultas', 'FILKOM')->first();
        $fh = Fakultas::where('kode_fakultas', 'FH')->first();
        $fk = Fakultas::where('kode_fakultas', 'FK')->first();
        $ff = Fakultas::where('kode_fakultas', 'FF')->first();
        $fp = Fakultas::where('kode_fakultas', 'FP')->first();
        $fapet = Fakultas::where('kode_fakultas', 'FAPET')->first();
        $fmipa = Fakultas::where('kode_fakultas', 'FMIPA')->first();
        $fisip = Fakultas::where('kode_fakultas', 'FISIP')->first();

        $prodis = [
            // Fakultas Teknik
            ['nama_prodi' => 'Teknik Sipil', 'kode_prodi' => 'TS', 'id_fakultas' => $ft->id_fakultas],
            ['nama_prodi' => 'Teknik Mesin', 'kode_prodi' => 'TM', 'id_fakultas' => $ft->id_fakultas],
            ['nama_prodi' => 'Teknik Elektro', 'kode_prodi' => 'TE', 'id_fakultas' => $ft->id_fakultas],
            ['nama_prodi' => 'Teknik Industri', 'kode_prodi' => 'TIN', 'id_fakultas' => $ft->id_fakultas],
            ['nama_prodi' => 'Teknik Kimia', 'kode_prodi' => 'TK', 'id_fakultas' => $ft->id_fakultas],
            
            // Fakultas Ekonomi dan Bisnis
            ['nama_prodi' => 'Manajemen', 'kode_prodi' => 'MNJ', 'id_fakultas' => $feb->id_fakultas],
            ['nama_prodi' => 'Akuntansi', 'kode_prodi' => 'AKT', 'id_fakultas' => $feb->id_fakultas],
            ['nama_prodi' => 'Ekonomi Pembangunan', 'kode_prodi' => 'EP', 'id_fakultas' => $feb->id_fakultas],
            
            // Fakultas Ilmu Komputer
            ['nama_prodi' => 'Teknik Informatika', 'kode_prodi' => 'IF', 'id_fakultas' => $filkom->id_fakultas],
            ['nama_prodi' => 'Sistem Informasi', 'kode_prodi' => 'SI', 'id_fakultas' => $filkom->id_fakultas],
            ['nama_prodi' => 'Teknologi Informasi', 'kode_prodi' => 'TIF', 'id_fakultas' => $filkom->id_fakultas],
            
            // Fakultas Hukum
            ['nama_prodi' => 'Ilmu Hukum', 'kode_prodi' => 'IH', 'id_fakultas' => $fh->id_fakultas],
            
            // Fakultas Kedokteran
            ['nama_prodi' => 'Pendidikan Dokter', 'kode_prodi' => 'PD', 'id_fakultas' => $fk->id_fakultas],
            ['nama_prodi' => 'Kedokteran Gigi', 'kode_prodi' => 'KG', 'id_fakultas' => $fk->id_fakultas],
            
            // Fakultas Farmasi
            ['nama_prodi' => 'Farmasi', 'kode_prodi' => 'FAR', 'id_fakultas' => $ff->id_fakultas],
            
            // Fakultas Pertanian
            ['nama_prodi' => 'Agroteknologi', 'kode_prodi' => 'AGT', 'id_fakultas' => $fp->id_fakultas],
            ['nama_prodi' => 'Agribisnis', 'kode_prodi' => 'AGB', 'id_fakultas' => $fp->id_fakultas],
            ['nama_prodi' => 'Teknologi Hasil Pertanian', 'kode_prodi' => 'THP', 'id_fakultas' => $fp->id_fakultas],
            
            // Fakultas Peternakan
            ['nama_prodi' => 'Peternakan', 'kode_prodi' => 'PET', 'id_fakultas' => $fapet->id_fakultas],
            ['nama_prodi' => 'Teknologi Hasil Ternak', 'kode_prodi' => 'THT', 'id_fakultas' => $fapet->id_fakultas],
            
            // Fakultas MIPA
            ['nama_prodi' => 'Matematika', 'kode_prodi' => 'MAT', 'id_fakultas' => $fmipa->id_fakultas],
            ['nama_prodi' => 'Fisika', 'kode_prodi' => 'FIS', 'id_fakultas' => $fmipa->id_fakultas],
            ['nama_prodi' => 'Kimia', 'kode_prodi' => 'KIM', 'id_fakultas' => $fmipa->id_fakultas],
            ['nama_prodi' => 'Biologi', 'kode_prodi' => 'BIO', 'id_fakultas' => $fmipa->id_fakultas],
            
            // Fakultas ISIP
            ['nama_prodi' => 'Ilmu Politik', 'kode_prodi' => 'IP', 'id_fakultas' => $fisip->id_fakultas],
            ['nama_prodi' => 'Sosiologi', 'kode_prodi' => 'SOS', 'id_fakultas' => $fisip->id_fakultas],
            ['nama_prodi' => 'Ilmu Komunikasi', 'kode_prodi' => 'IK', 'id_fakultas' => $fisip->id_fakultas],
        ];

        foreach ($prodis as $p) {
            Prodi::create($p);
        }
    }
}
