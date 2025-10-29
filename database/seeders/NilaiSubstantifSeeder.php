<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NilaiSubstantifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nilaiSubstantifs = [
            // Proposal 1 - 2023
            [
                'note_substantif' => 'Metodologi penelitian sangat solid dan relevan dengan tujuan penelitian. Kontribusi ilmiah yang signifikan.',
                'id_proposal' => 1,
                'id_reviewer' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 2 - 2023
            [
                'note_substantif' => 'Penelitian ini memiliki potensi besar untuk menghasilkan temuan yang inovatif. Metodologi yang digunakan sudah tepat.',
                'id_proposal' => 2,
                'id_reviewer' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 3 - 2023
            [
                'note_substantif' => 'Kontribusi penelitian sangat relevan dengan kebutuhan masyarakat. Metodologi yang digunakan sudah sesuai standar.',
                'id_proposal' => 3,
                'id_reviewer' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 4 - 2023
            [
                'note_substantif' => 'Penelitian ini memiliki novelty yang tinggi dan potensi komersial yang menjanjikan. Metodologi sangat baik.',
                'id_proposal' => 4,
                'id_reviewer' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 5 - 2023
            [
                'note_substantif' => 'Kontribusi ilmiah sangat signifikan dan relevan dengan isu lingkungan. Metodologi yang digunakan sudah tepat.',
                'id_proposal' => 5,
                'id_reviewer' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 6 - 2023
            [
                'note_substantif' => 'Penelitian ini sangat relevan dengan kondisi ekonomi lokal. Metodologi yang digunakan sudah sesuai standar.',
                'id_proposal' => 6,
                'id_reviewer' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 7 - 2023
            [
                'note_substantif' => 'Kontribusi penelitian sangat baik dan metodologi yang digunakan sudah tepat. Potensi publikasi yang tinggi.',
                'id_proposal' => 7,
                'id_reviewer' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 8 - 2023
            [
                'note_substantif' => 'Penelitian ini memiliki dampak yang sangat positif bagi masyarakat. Metodologi yang digunakan sudah sesuai standar.',
                'id_proposal' => 8,
                'id_reviewer' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 9 - 2023
            [
                'note_substantif' => 'Kontribusi ilmiah sangat signifikan dan relevan dengan perkembangan teknologi. Metodologi yang digunakan sudah tepat.',
                'id_proposal' => 9,
                'id_reviewer' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 10 - 2023
            [
                'note_substantif' => 'Penelitian ini sangat relevan dengan kebutuhan industri. Metodologi yang digunakan sudah sesuai standar.',
                'id_proposal' => 10,
                'id_reviewer' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 11 - 2024
            [
                'note_substantif' => 'Metodologi penelitian sangat solid dan relevan dengan tujuan penelitian. Kontribusi ilmiah yang signifikan.',
                'id_proposal' => 11,
                'id_reviewer' => 12,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 12 - 2024
            [
                'note_substantif' => 'Penelitian ini memiliki potensi besar untuk menghasilkan temuan yang inovatif. Metodologi yang digunakan sudah tepat.',
                'id_proposal' => 12,
                'id_reviewer' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 13 - 2024
            [
                'note_substantif' => 'Kontribusi penelitian sangat relevan dengan kebutuhan masyarakat. Metodologi yang digunakan sudah sesuai standar.',
                'id_proposal' => 13,
                'id_reviewer' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Proposal 14-20 (2024/2025) tidak memiliki nilai substantif karena masih dalam proses
        ];

        DB::table('nilai_substantifs')->insert($nilaiSubstantifs);
    }
}

