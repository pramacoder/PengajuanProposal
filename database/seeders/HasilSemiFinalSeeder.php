<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HasilSemiFinalSeeder extends Seeder
{
    public function run(): void
    {
        $operatorIds = DB::table('users')
            ->whereIn('role', ['operator', 'pimpinan_pt'])
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        $dosenIds = DB::table('users')
            ->where('role', 'dosen')
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        if (count($operatorIds) < 2) {
            $this->command->warn('Not enough operators/pimpinan. Need at least 2.');
            return;
        }

        if (count($dosenIds) < 2) {
            $this->command->warn('Not enough dosen. Need at least 2.');
            return;
        }

        $hasilSemiFinals = [
            [
                'status_final' => 'lolos_tingkat_universitas',
                'catatan_final' => 'Proposal memiliki kualitas sangat baik dan layak untuk maju ke tingkat universitas.',
                'nilai' => 87.50,
                'skor_per_kriteria' => json_encode([9, 8, 9, 8, 9]),
                'dana_yang_dapat_diberikan' => 10000000.00,
                'id_dosen_pendamping_universitas' => $dosenIds[0],
                'id_proposal' => 1,
                'id_pt' => $operatorIds[0],
            ],
            [
                'status_final' => 'lolos_tingkat_universitas',
                'catatan_final' => 'Metodologi penelitian solid dan kontribusi signifikan terhadap bidang ilmu.',
                'nilai' => 89.25,
                'skor_per_kriteria' => json_encode([9, 9, 8, 9, 9]),
                'dana_yang_dapat_diberikan' => 12000000.00,
                'id_dosen_pendamping_universitas' => $dosenIds[1],
                'id_proposal' => 2,
                'id_pt' => $operatorIds[1],
            ],
            [
                'status_final' => 'lolos_tingkat_universitas',
                'catatan_final' => 'Proposal inovatif dengan potensi dampak positif bagi masyarakat.',
                'nilai' => 85.00,
                'skor_per_kriteria' => json_encode([8, 9, 8, 8, 9]),
                'dana_yang_dapat_diberikan' => 9500000.00,
                'id_dosen_pendamping_universitas' => $dosenIds[0],
                'id_proposal' => 3,
                'id_pt' => $operatorIds[0],
            ],
            [
                'status_final' => 'lolos_tingkat_universitas',
                'catatan_final' => 'Potensi komersial yang menjanjikan dengan perencanaan yang matang.',
                'nilai' => 91.00,
                'skor_per_kriteria' => json_encode([9, 9, 9, 9, 10]),
                'dana_yang_dapat_diberikan' => 15000000.00,
                'id_dosen_pendamping_universitas' => $dosenIds[1],
                'id_proposal' => 4,
                'id_pt' => $operatorIds[1],
            ],
            [
                'status_final' => 'tidak_lolos_tingkat_universitas',
                'catatan_final' => 'Metodologi penelitian perlu diperkuat dan analisis data kurang mendalam.',
                'nilai' => 65.00,
                'skor_per_kriteria' => json_encode([6, 7, 6, 7, 6]),
                'dana_yang_dapat_diberikan' => null,
                'id_dosen_pendamping_universitas' => null,
                'id_proposal' => 5,
                'id_pt' => $operatorIds[0],
            ],
            [
                'status_final' => 'lolos_tingkat_universitas',
                'catatan_final' => 'Relevansi tinggi dengan kebutuhan industri dan masyarakat lokal.',
                'nilai' => 86.75,
                'skor_per_kriteria' => json_encode([8, 9, 9, 8, 9]),
                'dana_yang_dapat_diberikan' => 11000000.00,
                'id_dosen_pendamping_universitas' => $dosenIds[0],
                'id_proposal' => 6,
                'id_pt' => $operatorIds[1],
            ],
            [
                'status_final' => 'tidak_lolos_tingkat_universitas',
                'catatan_final' => 'Proposal kurang orisinal dan belum menunjukkan kebaruan yang signifikan.',
                'nilai' => 62.50,
                'skor_per_kriteria' => json_encode([6, 6, 7, 6, 6]),
                'dana_yang_dapat_diberikan' => null,
                'id_dosen_pendamping_universitas' => null,
                'id_proposal' => 7,
                'id_pt' => $operatorIds[0],
            ],
            [
                'status_final' => 'lolos_tingkat_universitas',
                'catatan_final' => 'Dampak positif langsung bagi masyarakat sekitar kampus.',
                'nilai' => 84.50,
                'skor_per_kriteria' => json_encode([8, 8, 9, 8, 9]),
                'dana_yang_dapat_diberikan' => 9000000.00,
                'id_dosen_pendamping_universitas' => $dosenIds[1],
                'id_proposal' => 8,
                'id_pt' => $operatorIds[1],
            ],
        ];

        $rows = [];
        foreach ($hasilSemiFinals as $item) {
            $rows[] = array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('hasil_semi_finals')->insert($rows);
    }
}
