<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NilaiAdministratifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nilaiAdministratifs = [
            // Proposal 1 - 2023
            [
                'note_administratif' => 'Dokumen lengkap dan sesuai dengan persyaratan. Format penulisan sudah baik.',
                'checklist' => json_encode([
                    'dokumen_proposal' => true,
                    'surat_pengantar' => true,
                    'cv_ketua_tim' => true,
                    'cv_anggota' => true,
                    'surat_rekomendasi_dosen' => true,
                    'surat_pernyataan_keaslian' => true,
                    'dokumen_anggaran' => true,
                    'dokumen_jadwal' => true,
                ]),
                'id_proposal' => 1,
                'id_reviewer' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 2 - 2023
            [
                'note_administratif' => 'Semua dokumen administratif sudah lengkap dan valid. Tidak ada kekurangan.',
                'checklist' => json_encode([
                    'dokumen_proposal' => true,
                    'surat_pengantar' => true,
                    'cv_ketua_tim' => true,
                    'cv_anggota' => true,
                    'surat_rekomendasi_dosen' => true,
                    'surat_pernyataan_keaslian' => true,
                    'dokumen_anggaran' => true,
                    'dokumen_jadwal' => true,
                ]),
                'id_proposal' => 2,
                'id_reviewer' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 3 - 2023
            [
                'note_administratif' => 'Dokumen administratif sudah memenuhi standar. Ada beberapa minor yang perlu diperbaiki.',
                'checklist' => json_encode([
                    'dokumen_proposal' => true,
                    'surat_pengantar' => true,
                    'cv_ketua_tim' => true,
                    'cv_anggota' => true,
                    'surat_rekomendasi_dosen' => true,
                    'surat_pernyataan_keaslian' => true,
                    'dokumen_anggaran' => true,
                    'dokumen_jadwal' => true,
                ]),
                'id_proposal' => 3,
                'id_reviewer' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 4 - 2023
            [
                'note_administratif' => 'Kelengkapan dokumen sangat baik. Format dan struktur sudah sesuai standar.',
                'checklist' => json_encode([
                    'dokumen_proposal' => true,
                    'surat_pengantar' => true,
                    'cv_ketua_tim' => true,
                    'cv_anggota' => true,
                    'surat_rekomendasi_dosen' => true,
                    'surat_pernyataan_keaslian' => true,
                    'dokumen_anggaran' => true,
                    'dokumen_jadwal' => true,
                ]),
                'id_proposal' => 4,
                'id_reviewer' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 5 - 2023
            [
                'note_administratif' => 'Dokumen administratif lengkap dan berkualitas. Tidak ada catatan khusus.',
                'checklist' => json_encode([
                    'dokumen_proposal' => true,
                    'surat_pengantar' => true,
                    'cv_ketua_tim' => true,
                    'cv_anggota' => true,
                    'surat_rekomendasi_dosen' => true,
                    'surat_pernyataan_keaslian' => true,
                    'dokumen_anggaran' => true,
                    'dokumen_jadwal' => true,
                ]),
                'id_proposal' => 5,
                'id_reviewer' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 6 - 2023
            [
                'note_administratif' => 'Semua persyaratan administratif telah dipenuhi dengan baik.',
                'checklist' => json_encode([
                    'dokumen_proposal' => true,
                    'surat_pengantar' => true,
                    'cv_ketua_tim' => true,
                    'cv_anggota' => true,
                    'surat_rekomendasi_dosen' => true,
                    'surat_pernyataan_keaslian' => true,
                    'dokumen_anggaran' => true,
                    'dokumen_jadwal' => true,
                ]),
                'id_proposal' => 6,
                'id_reviewer' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 7 - 2023
            [
                'note_administratif' => 'Dokumen lengkap dan sesuai dengan ketentuan. Format penulisan sudah standar.',
                'checklist' => json_encode([
                    'dokumen_proposal' => true,
                    'surat_pengantar' => true,
                    'cv_ketua_tim' => true,
                    'cv_anggota' => true,
                    'surat_rekomendasi_dosen' => true,
                    'surat_pernyataan_keaslian' => true,
                    'dokumen_anggaran' => true,
                    'dokumen_jadwal' => true,
                ]),
                'id_proposal' => 7,
                'id_reviewer' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 8 - 2023
            [
                'note_administratif' => 'Kelengkapan dokumen sangat memuaskan. Tidak ada kekurangan yang signifikan.',
                'checklist' => json_encode([
                    'dokumen_proposal' => true,
                    'surat_pengantar' => true,
                    'cv_ketua_tim' => true,
                    'cv_anggota' => true,
                    'surat_rekomendasi_dosen' => true,
                    'surat_pernyataan_keaslian' => true,
                    'dokumen_anggaran' => true,
                    'dokumen_jadwal' => true,
                ]),
                'id_proposal' => 8,
                'id_reviewer' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 9 - 2023
            [
                'note_administratif' => 'Dokumen administratif sudah memenuhi standar kualitas yang ditetapkan.',
                'checklist' => json_encode([
                    'dokumen_proposal' => true,
                    'surat_pengantar' => true,
                    'cv_ketua_tim' => true,
                    'cv_anggota' => true,
                    'surat_rekomendasi_dosen' => true,
                    'surat_pernyataan_keaslian' => true,
                    'dokumen_anggaran' => true,
                    'dokumen_jadwal' => true,
                ]),
                'id_proposal' => 9,
                'id_reviewer' => 9,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 10 - 2023
            [
                'note_administratif' => 'Semua dokumen telah disiapkan dengan baik dan sesuai persyaratan.',
                'checklist' => json_encode([
                    'dokumen_proposal' => true,
                    'surat_pengantar' => true,
                    'cv_ketua_tim' => true,
                    'cv_anggota' => true,
                    'surat_rekomendasi_dosen' => true,
                    'surat_pernyataan_keaslian' => true,
                    'dokumen_anggaran' => true,
                    'dokumen_jadwal' => true,
                ]),
                'id_proposal' => 10,
                'id_reviewer' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 11 - 2024
            [
                'note_administratif' => 'Dokumen lengkap dan berkualitas tinggi. Format sudah sesuai standar internasional.',
                'checklist' => json_encode([
                    'dokumen_proposal' => true,
                    'surat_pengantar' => true,
                    'cv_ketua_tim' => true,
                    'cv_anggota' => true,
                    'surat_rekomendasi_dosen' => true,
                    'surat_pernyataan_keaslian' => true,
                    'dokumen_anggaran' => true,
                    'dokumen_jadwal' => true,
                ]),
                'id_proposal' => 11,
                'id_reviewer' => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 12 - 2024
            [
                'note_administratif' => 'Kelengkapan dokumen sangat baik. Tidak ada kekurangan yang perlu diperbaiki.',
                'checklist' => json_encode([
                    'dokumen_proposal' => true,
                    'surat_pengantar' => true,
                    'cv_ketua_tim' => true,
                    'cv_anggota' => true,
                    'surat_rekomendasi_dosen' => true,
                    'surat_pernyataan_keaslian' => true,
                    'dokumen_anggaran' => true,
                    'dokumen_jadwal' => true,
                ]),
                'id_proposal' => 12,
                'id_reviewer' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 13 - 2024
            [
                'note_administratif' => 'Dokumen administratif sudah memenuhi standar kualitas yang tinggi.',
                'checklist' => json_encode([
                    'dokumen_proposal' => true,
                    'surat_pengantar' => true,
                    'cv_ketua_tim' => true,
                    'cv_anggota' => true,
                    'surat_rekomendasi_dosen' => true,
                    'surat_pernyataan_keaslian' => true,
                    'dokumen_anggaran' => true,
                    'dokumen_jadwal' => true,
                ]),
                'id_proposal' => 13,
                'id_reviewer' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 14-20 (2024/2025) tidak memiliki nilai administratif karena masih dalam proses
        ];

        DB::table('nilai_administratifs')->insert($nilaiAdministratifs);
    }
}

