<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NilaiSubstantifSeeder extends Seeder
{
    public function run(): void
    {
        $reviewerIds = DB::table('users')->where('role', 'reviewer')->orderBy('id')->pluck('id')->toArray();

        if (count($reviewerIds) < 12) {
            $this->command->warn('Not enough reviewers. Need at least 12.');
            return;
        }

        $nilaiSubstantifs = [
            ['note_substantif' => 'Metodologi penelitian sangat solid dan relevan dengan tujuan penelitian. Kontribusi ilmiah yang signifikan.',         'id_proposal' => 1,  'id_reviewer' => $reviewerIds[1]],
            ['note_substantif' => 'Penelitian ini memiliki potensi besar untuk menghasilkan temuan yang inovatif. Metodologi yang digunakan sudah tepat.','id_proposal' => 2,  'id_reviewer' => $reviewerIds[3]],
            ['note_substantif' => 'Kontribusi penelitian sangat relevan dengan kebutuhan masyarakat. Metodologi yang digunakan sudah sesuai standar.',   'id_proposal' => 3,  'id_reviewer' => $reviewerIds[5]],
            ['note_substantif' => 'Penelitian ini memiliki novelty yang tinggi dan potensi komersial yang menjanjikan. Metodologi sangat baik.',         'id_proposal' => 4,  'id_reviewer' => $reviewerIds[7]],
            ['note_substantif' => 'Kontribusi ilmiah sangat signifikan dan relevan dengan isu lingkungan. Metodologi yang digunakan sudah tepat.',       'id_proposal' => 5,  'id_reviewer' => $reviewerIds[9]],
            ['note_substantif' => 'Penelitian ini sangat relevan dengan kondisi ekonomi lokal. Metodologi yang digunakan sudah sesuai standar.',         'id_proposal' => 6,  'id_reviewer' => $reviewerIds[11]],
            ['note_substantif' => 'Kontribusi penelitian sangat baik dan metodologi yang digunakan sudah tepat. Potensi publikasi yang tinggi.',         'id_proposal' => 7,  'id_reviewer' => $reviewerIds[1]],
            ['note_substantif' => 'Penelitian ini memiliki dampak yang sangat positif bagi masyarakat. Metodologi yang digunakan sudah sesuai standar.', 'id_proposal' => 8,  'id_reviewer' => $reviewerIds[3]],
            ['note_substantif' => 'Kontribusi ilmiah sangat signifikan dan relevan dengan perkembangan teknologi. Metodologi yang digunakan sudah tepat.','id_proposal' => 9,  'id_reviewer' => $reviewerIds[5]],
            ['note_substantif' => 'Penelitian ini sangat relevan dengan kebutuhan industri. Metodologi yang digunakan sudah sesuai standar.',            'id_proposal' => 10, 'id_reviewer' => $reviewerIds[7]],
            ['note_substantif' => 'Metodologi penelitian sangat solid dan relevan dengan tujuan penelitian. Kontribusi ilmiah yang signifikan.',         'id_proposal' => 11, 'id_reviewer' => $reviewerIds[11]],
            ['note_substantif' => 'Penelitian ini memiliki potensi besar untuk menghasilkan temuan yang inovatif. Metodologi yang digunakan sudah tepat.','id_proposal' => 12, 'id_reviewer' => $reviewerIds[2]],
            ['note_substantif' => 'Kontribusi penelitian sangat relevan dengan kebutuhan masyarakat. Metodologi yang digunakan sudah sesuai standar.',   'id_proposal' => 13, 'id_reviewer' => $reviewerIds[5]],
        ];

        $rows = [];
        foreach ($nilaiSubstantifs as $item) {
            $rows[] = array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('nilai_substantifs')->insert($rows);
    }
}
