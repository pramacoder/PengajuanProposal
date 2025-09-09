<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prodi;
use App\Models\Fakultas;
use Illuminate\Support\Carbon;

class ProdiSeeder extends Seeder
{
    public function run(): void
    {
        // Peta kode_fakultas -> id_fakultas (sekali tarik)
        $fmap = Fakultas::query()->pluck('id_fakultas', 'kode_fakultas')->toArray();
        $fid  = fn(string $k) => $fmap[$k] ?? null;

        // ===== Prodi S1 (SNBT) & penempatan ke fakultas =====
        // Kode prodi (singkat) boleh kamu ganti sesuai standar internal
        $prodis = [
            // FK
            ['nama_prodi'=>'Kedokteran',                    'kode_prodi'=>'S1-KED',   'kode_fakultas'=>'FK'],
            ['nama_prodi'=>'Kedokteran Gigi',               'kode_prodi'=>'S1-KG',    'kode_fakultas'=>'FK'],
            ['nama_prodi'=>'Ilmu Kesehatan Masyarakat',     'kode_prodi'=>'S1-IKM',   'kode_fakultas'=>'FK'],
            ['nama_prodi'=>'Keperawatan',                   'kode_prodi'=>'S1-Kep',   'kode_fakultas'=>'FK'],
            ['nama_prodi'=>'Fisioterapi',                   'kode_prodi'=>'S1-Fis',   'kode_fakultas'=>'FK'],
            ['nama_prodi'=>'Psikologi',                     'kode_prodi'=>'S1-Psi',   'kode_fakultas'=>'FK'],

            // FKH
            ['nama_prodi'=>'Kedokteran Hewan',              'kode_prodi'=>'S1-KH',    'kode_fakultas'=>'FKH'],

            // FT
            ['nama_prodi'=>'Arsitektur',                    'kode_prodi'=>'S1-Ars',   'kode_fakultas'=>'FT'],
            ['nama_prodi'=>'Teknik Sipil',                  'kode_prodi'=>'S1-TS',    'kode_fakultas'=>'FT'],
            ['nama_prodi'=>'Teknik Mesin',                  'kode_prodi'=>'S1-TM',    'kode_fakultas'=>'FT'],
            ['nama_prodi'=>'Teknik Elektro',                'kode_prodi'=>'S1-TE',    'kode_fakultas'=>'FT'],
            ['nama_prodi'=>'Teknik Industri',               'kode_prodi'=>'S1-TInd',  'kode_fakultas'=>'FT'],
            ['nama_prodi'=>'Teknik Lingkungan',             'kode_prodi'=>'S1-TL',    'kode_fakultas'=>'FT'],
            // TI UNUD ada di Fakultas Teknik (resmi)
            ['nama_prodi'=>'Teknologi Informasi',           'kode_prodi'=>'S1-TI',    'kode_fakultas'=>'FT'],

            // FMIPA
            ['nama_prodi'=>'Biologi',                       'kode_prodi'=>'S1-Bio',   'kode_fakultas'=>'FMIPA'],
            ['nama_prodi'=>'Kimia',                         'kode_prodi'=>'S1-Kim',   'kode_fakultas'=>'FMIPA'],
            ['nama_prodi'=>'Fisika',                        'kode_prodi'=>'S1-Fis',   'kode_fakultas'=>'FMIPA'],
            ['nama_prodi'=>'Matematika',                    'kode_prodi'=>'S1-Mat',   'kode_fakultas'=>'FMIPA'],
            ['nama_prodi'=>'Informatika',                   'kode_prodi'=>'S1-IF',    'kode_fakultas'=>'FMIPA'],
            // Farmasi berada di FMIPA (resmi)
            ['nama_prodi'=>'Farmasi',                       'kode_prodi'=>'S1-Far',   'kode_fakultas'=>'FMIPA'],

            // FP
            ['nama_prodi'=>'Agribisnis',                    'kode_prodi'=>'S1-AGB',   'kode_fakultas'=>'FP'],
            ['nama_prodi'=>'Agroekoteknologi',              'kode_prodi'=>'S1-AGT',   'kode_fakultas'=>'FP'],
            // Arsitektur Lanskap di F. Pertanian (resmi)
            ['nama_prodi'=>'Arsitektur Lanskap',            'kode_prodi'=>'S1-ARL',   'kode_fakultas'=>'FP'],

            // FAPET
            ['nama_prodi'=>'Peternakan',                    'kode_prodi'=>'S1-Pet',   'kode_fakultas'=>'FAPET'],

            // FTP
            ['nama_prodi'=>'Teknologi Pangan',              'kode_prodi'=>'S1-TP',    'kode_fakultas'=>'FTP'],
            ['nama_prodi'=>'Teknologi Industri Pertanian',  'kode_prodi'=>'S1-TIP',   'kode_fakultas'=>'FTP'],
            ['nama_prodi'=>'Teknik Pertanian dan Biosistem','kode_prodi'=>'S1-TPB',   'kode_fakultas'=>'FTP'],

            // FISIP
            ['nama_prodi'=>'Hubungan Internasional',        'kode_prodi'=>'S1-HI',    'kode_fakultas'=>'FISIP'],
            ['nama_prodi'=>'Sosiologi',                     'kode_prodi'=>'S1-Sos',   'kode_fakultas'=>'FISIP'],
            ['nama_prodi'=>'Administrasi Negara',           'kode_prodi'=>'S1-AN',    'kode_fakultas'=>'FISIP'],
            ['nama_prodi'=>'Ilmu Komunikasi',               'kode_prodi'=>'S1-IKom',  'kode_fakultas'=>'FISIP'],
            ['nama_prodi'=>'Ilmu Politik',                  'kode_prodi'=>'S1-IPol',  'kode_fakultas'=>'FISIP'],

            // FEB
            ['nama_prodi'=>'Ekonomi',                       'kode_prodi'=>'S1-Eko',   'kode_fakultas'=>'FEB'],
            ['nama_prodi'=>'Akuntansi',                     'kode_prodi'=>'S1-Akt',   'kode_fakultas'=>'FEB'],
            ['nama_prodi'=>'Manajemen',                     'kode_prodi'=>'S1-Mnj',   'kode_fakultas'=>'FEB'],

            // FH
            ['nama_prodi'=>'Ilmu Hukum',                    'kode_prodi'=>'S1-IH',    'kode_fakultas'=>'FH'],

            // FIB
            ['nama_prodi'=>'Arkeologi',                     'kode_prodi'=>'S1-Ark',   'kode_fakultas'=>'FIB'],
            ['nama_prodi'=>'Antropologi Budaya',            'kode_prodi'=>'S1-AB',    'kode_fakultas'=>'FIB'],
            ['nama_prodi'=>'Ilmu Sejarah',                  'kode_prodi'=>'S1-Sej',   'kode_fakultas'=>'FIB'],
            ['nama_prodi'=>'Sastra Indonesia',              'kode_prodi'=>'S1-SInd',  'kode_fakultas'=>'FIB'],
            ['nama_prodi'=>'Sastra Inggris',                'kode_prodi'=>'S1-SIng',  'kode_fakultas'=>'FIB'],
            ['nama_prodi'=>'Sastra Jawa Kuno',              'kode_prodi'=>'S1-SJK',   'kode_fakultas'=>'FIB'],
            ['nama_prodi'=>'Sastra Bali',                   'kode_prodi'=>'S1-SBal',  'kode_fakultas'=>'FIB'],
            ['nama_prodi'=>'Sastra Jepang',                 'kode_prodi'=>'S1-SJep',  'kode_fakultas'=>'FIB'],

            // FPAR
            ['nama_prodi'=>'Pariwisata',                    'kode_prodi'=>'S1-Par',   'kode_fakultas'=>'FPAR'],
            ['nama_prodi'=>'Industri Perjalanan Wisata',    'kode_prodi'=>'S1-IPW',   'kode_fakultas'=>'FPAR'],

            // FKP
            ['nama_prodi'=>'Ilmu Kelautan',                 'kode_prodi'=>'S1-IKL',   'kode_fakultas'=>'FKP'],
            ['nama_prodi'=>'Manajemen Sumber Daya Perairan','kode_prodi'=>'S1-MSDP',  'kode_fakultas'=>'FKP'],
            ['nama_prodi'=>'Akuakultur',                    'kode_prodi'=>'S1-Aku',   'kode_fakultas'=>'FKP'],
        ];

        $now = Carbon::now();
        $rows = [];
        foreach ($prodis as $p) {
            $id_fak = $fid($p['kode_fakultas']);
            if (!$id_fak) {
                // Lewati jika fakultas belum ada (menghindari FK error)
                continue;
            }
            $rows[] = [
                'nama_prodi'   => $p['nama_prodi'],
                'kode_prodi'   => $p['kode_prodi'],
                'id_fakultas'  => $id_fak,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        // Wajib ada unique index di 'kode_prodi' pada migrasi Prodi
        Prodi::upsert(
            $rows,
            ['kode_prodi'],                         // kunci unik
            ['nama_prodi','id_fakultas','updated_at'] // kolom diupdate bila bentrok
        );
    }
}
    