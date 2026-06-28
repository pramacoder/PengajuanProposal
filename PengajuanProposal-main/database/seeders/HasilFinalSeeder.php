<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HasilFinalSeeder extends Seeder
{
    public function run(): void
    {
        $operatorIds = DB::table('users')
            ->whereIn('role', ['operator', 'pimpinan_pt'])
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        if (count($operatorIds) < 6) {
            $this->command->warn('Not enough operators/pimpinan. Need at least 6.');
            return;
        }

        $hasilFinals = [
            ['status_final' => 'lolos', 'catatan_final' => 'Proposal dinyatakan lolos dengan nilai yang sangat memuaskan. Kontribusi penelitian sangat signifikan.', 'nilai' => 85.50, 'id_proposal' => 1,  'id_pt' => $operatorIds[0]],
            ['status_final' => 'lolos', 'catatan_final' => 'Proposal dinyatakan lolos dengan nilai yang sangat baik. Metodologi penelitian sangat solid.',           'nilai' => 88.75, 'id_proposal' => 2,  'id_pt' => $operatorIds[1]],
            ['status_final' => 'lolos', 'catatan_final' => 'Proposal dinyatakan lolos dengan nilai yang memuaskan. Kontribusi penelitian sangat relevan.',           'nilai' => 82.25, 'id_proposal' => 3,  'id_pt' => $operatorIds[2]],
            ['status_final' => 'lolos', 'catatan_final' => 'Proposal dinyatakan lolos dengan nilai yang sangat baik. Potensi komersial yang menjanjikan.',           'nilai' => 90.00, 'id_proposal' => 4,  'id_pt' => $operatorIds[3]],
            ['status_final' => 'lolos', 'catatan_final' => 'Proposal dinyatakan lolos dengan nilai yang sangat memuaskan. Kontribusi lingkungan yang signifikan.',   'nilai' => 87.50, 'id_proposal' => 5,  'id_pt' => $operatorIds[4]],
            ['status_final' => 'lolos', 'catatan_final' => 'Proposal dinyatakan lolos dengan nilai yang baik. Relevansi dengan kondisi lokal sangat tinggi.',        'nilai' => 83.75, 'id_proposal' => 6,  'id_pt' => $operatorIds[5]],
            ['status_final' => 'lolos', 'catatan_final' => 'Proposal dinyatakan lolos dengan nilai yang sangat baik. Potensi publikasi yang tinggi.',                'nilai' => 86.25, 'id_proposal' => 7,  'id_pt' => $operatorIds[0]],
            ['status_final' => 'lolos', 'catatan_final' => 'Proposal dinyatakan lolos dengan nilai yang memuaskan. Dampak positif bagi masyarakat.',                 'nilai' => 84.50, 'id_proposal' => 8,  'id_pt' => $operatorIds[1]],
            ['status_final' => 'lolos', 'catatan_final' => 'Proposal dinyatakan lolos dengan nilai yang sangat baik. Kontribusi teknologi yang signifikan.',         'nilai' => 89.25, 'id_proposal' => 9,  'id_pt' => $operatorIds[2]],
            ['status_final' => 'lolos', 'catatan_final' => 'Proposal dinyatakan lolos dengan nilai yang baik. Relevansi dengan kebutuhan industri tinggi.',          'nilai' => 85.00, 'id_proposal' => 10, 'id_pt' => $operatorIds[3]],
            ['status_final' => 'lolos', 'catatan_final' => 'Proposal dinyatakan lolos dengan nilai yang sangat memuaskan. Desain arsitektur yang inovatif.',         'nilai' => 88.75, 'id_proposal' => 11, 'id_pt' => $operatorIds[4]],
            ['status_final' => 'lolos', 'catatan_final' => 'Proposal dinyatakan lolos dengan nilai yang sangat baik. Pemanfaatan kekayaan lokal yang optimal.',      'nilai' => 87.50, 'id_proposal' => 12, 'id_pt' => $operatorIds[5]],
            ['status_final' => 'lolos', 'catatan_final' => 'Proposal dinyatakan lolos dengan nilai yang memuaskan. Dampak langsung pada masyarakat.',                'nilai' => 84.25, 'id_proposal' => 13, 'id_pt' => $operatorIds[0]],
        ];

        $rows = [];
        foreach ($hasilFinals as $item) {
            $rows[] = array_merge($item, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('hasil_finals')->insert($rows);
    }
}
