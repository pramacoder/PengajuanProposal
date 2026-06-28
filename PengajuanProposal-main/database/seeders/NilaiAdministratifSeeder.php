<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NilaiAdministratifSeeder extends Seeder
{
    public function run(): void
    {
        $reviewerIds = DB::table('users')->where('role', 'reviewer')->orderBy('id')->pluck('id')->toArray();

        if (count($reviewerIds) < 12) {
            $this->command->warn('Not enough reviewers. Need at least 12.');
            return;
        }

        $nilaiAdministratifs = [
            ['note_administratif' => 'Dokumen lengkap dan sesuai dengan persyaratan. Format penulisan sudah baik.',                    'id_proposal' => 1,  'id_reviewer' => $reviewerIds[0]],
            ['note_administratif' => 'Semua dokumen administratif sudah lengkap dan valid. Tidak ada kekurangan.',                     'id_proposal' => 2,  'id_reviewer' => $reviewerIds[1]],
            ['note_administratif' => 'Dokumen administratif sudah memenuhi standar. Ada beberapa minor yang perlu diperbaiki.',         'id_proposal' => 3,  'id_reviewer' => $reviewerIds[2]],
            ['note_administratif' => 'Kelengkapan dokumen sangat baik. Format dan struktur sudah sesuai standar.',                     'id_proposal' => 4,  'id_reviewer' => $reviewerIds[3]],
            ['note_administratif' => 'Dokumen administratif lengkap dan berkualitas. Tidak ada catatan khusus.',                        'id_proposal' => 5,  'id_reviewer' => $reviewerIds[4]],
            ['note_administratif' => 'Semua persyaratan administratif telah dipenuhi dengan baik.',                                    'id_proposal' => 6,  'id_reviewer' => $reviewerIds[5]],
            ['note_administratif' => 'Dokumen lengkap dan sesuai dengan ketentuan. Format penulisan sudah standar.',                   'id_proposal' => 7,  'id_reviewer' => $reviewerIds[6]],
            ['note_administratif' => 'Kelengkapan dokumen sangat memuaskan. Tidak ada kekurangan yang signifikan.',                    'id_proposal' => 8,  'id_reviewer' => $reviewerIds[7]],
            ['note_administratif' => 'Dokumen administratif sudah memenuhi standar kualitas yang ditetapkan.',                         'id_proposal' => 9,  'id_reviewer' => $reviewerIds[8]],
            ['note_administratif' => 'Semua dokumen telah disiapkan dengan baik dan sesuai persyaratan.',                              'id_proposal' => 10, 'id_reviewer' => $reviewerIds[9]],
            ['note_administratif' => 'Dokumen lengkap dan berkualitas tinggi. Format sudah sesuai standar internasional.',              'id_proposal' => 11, 'id_reviewer' => $reviewerIds[10]],
            ['note_administratif' => 'Kelengkapan dokumen sangat baik. Tidak ada kekurangan yang perlu diperbaiki.',                   'id_proposal' => 12, 'id_reviewer' => $reviewerIds[1]],
            ['note_administratif' => 'Dokumen administratif sudah memenuhi standar kualitas yang tinggi.',                             'id_proposal' => 13, 'id_reviewer' => $reviewerIds[4]],
        ];

        $checklist = json_encode([
            'dokumen_proposal' => true,
            'surat_pengantar' => true,
            'cv_ketua_tim' => true,
            'cv_anggota' => true,
            'surat_rekomendasi_dosen' => true,
            'surat_pernyataan_keaslian' => true,
            'dokumen_anggaran' => true,
            'dokumen_jadwal' => true,
        ]);

        $rows = [];
        foreach ($nilaiAdministratifs as $item) {
            $rows[] = array_merge($item, [
                'checklist' => $checklist,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('nilai_administratifs')->insert($rows);
    }
}
