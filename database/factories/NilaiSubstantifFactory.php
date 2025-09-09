<?php

namespace Database\Factories;

use App\Models\NilaiSubstantif;
use App\Models\Proposal;
use App\Models\Reviewer;
use Illuminate\Database\Eloquent\Factories\Factory;

class NilaiSubstantifFactory extends Factory
{
    protected $model = NilaiSubstantif::class;

    public function definition(): array
    {
        $notes = [
            'Metodologi penelitian sudah cukup baik, namun perlu penambahan referensi terkini.',
            'Inovasi yang diajukan menarik, tetapi implementasi perlu diperjelas.',
            'Latar belakang masalah sudah dijelaskan dengan baik.',
            'Target luaran perlu disesuaikan dengan skala penelitian.',
            'Timeline pelaksanaan perlu direview untuk memastikan feasibility.',
            'Budget yang diajukan sudah sesuai dengan kegiatan yang direncanakan.',
            'Perlu penambahan analisis risiko dalam metodologi.',
            'Konsep inovasi perlu diperkuat dengan data pendukung yang lebih konkret.',
            'Metodologi penelitian perlu dijelaskan lebih detail untuk memastikan reproducibility.',
            'Analisis dampak dan manfaat perlu diperkuat dengan studi literatur yang lebih komprehensif.',
        ];

        return [
            'note_substantif' => $this->faker->randomElement($notes),
            'id_proposal' => Proposal::factory(),
            'id_reviewer' => Reviewer::factory(),
        ];
    }

    // State untuk review yang sudah selesai
    public function completed()
    {
        return $this->state([
            'note_substantif' => $this->faker->randomElement([
                'Kualitas penelitian sangat baik dengan metodologi yang solid.',
                'Inovasi yang diajukan memiliki potensi dampak yang signifikan.',
                'Metodologi penelitian sudah sesuai dengan standar akademik.',
                'Proposal siap untuk tahap implementasi.',
                'Kontribusi penelitian terhadap bidang keilmuan sudah jelas.'
            ]),
        ]);
    }

    // State untuk review yang masih pending
    public function pending()
    {
        return $this->state([
            'note_substantif' => 'Review substantif dimulai',
        ]);
    }
}