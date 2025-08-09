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
        $hasil_options = [
            'Sangat Baik - Proposal memiliki inovasi tinggi dan metodologi yang kuat',
            'Baik - Proposal sudah cukup baik namun perlu beberapa perbaikan minor',
            'Cukup - Proposal memiliki potensi namun perlu perbaikan yang cukup signifikan',
            'Kurang - Proposal perlu perbaikan besar-besaran dalam metodologi dan konsep',
        ];

        $notes = [
            'Metodologi penelitian sudah cukup baik, namun perlu penambahan referensi terkini.',
            'Inovasi yang diajukan menarik, tetapi implementasi perlu diperjelas.',
            'Latar belakang masalah sudah dijelaskan dengan baik.',
            'Target luaran perlu disesuaikan dengan skala penelitian.',
            'Timeline pelaksanaan perlu direview untuk memastikan feasibility.',
            'Budget yang diajukan sudah sesuai dengan kegiatan yang direncanakan.',
            'Perlu penambahan analisis risiko dalam metodologi.',
        ];

        return [
            'hasil_substantif' => $this->faker->randomElement($hasil_options),
            'note_substantif' => $this->faker->randomElement($notes),
            'id_proposal' => Proposal::factory(),
            'id_reviewer' => Reviewer::factory(),
        ];
    }
}