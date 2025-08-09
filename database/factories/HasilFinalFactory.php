<?php

namespace Database\Factories;

use App\Models\HasilFinal;
use App\Models\Proposal;
use App\Models\Pt;
use Illuminate\Database\Eloquent\Factories\Factory;

class HasilFinalFactory extends Factory
{
    protected $model = HasilFinal::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['lolos', 'tidak_lolos']);
        
        $catatan_lolos = [
            'Proposal dinyatakan lolos dan dapat melanjutkan ke tahap pelaksanaan.',
            'Selamat! Proposal Anda telah memenuhi semua kriteria dan dinyatakan lolos seleksi.',
            'Proposal lolos dengan catatan untuk memperhatikan timeline pelaksanaan yang telah disetujui.',
            'Proposal diterima. Silakan mengikuti briefing teknis yang akan diadakan.',
        ];

        $catatan_tidak_lolos = [
            'Proposal tidak lolos karena metodologi yang kurang kuat.',
            'Proposal tidak memenuhi kriteria inovasi yang diharapkan.',
            'Budget yang diajukan tidak realistis dengan target yang ingin dicapai.',
            'Timeline pelaksanaan terlalu optimis dan kurang feasible.',
            'Latar belakang masalah perlu diperkuat dengan data yang lebih valid.',
        ];

        return [
            'status_final' => $status,
            'catatan_final' => $status === 'lolos' 
                ? $this->faker->randomElement($catatan_lolos)
                : $this->faker->randomElement($catatan_tidak_lolos),
            'id_proposal' => Proposal::factory(),
            'id_pt' => Pt::factory(),
        ];
    }

    public function lolos()
    {
        return $this->state([
            'status_final' => 'lolos',
            'catatan_final' => 'Proposal dinyatakan lolos dan dapat melanjutkan ke tahap pelaksanaan.',
        ]);
    }

    public function tidakLolos()
    {
        return $this->state([
            'status_final' => 'tidak_lolos',
            'catatan_final' => 'Proposal tidak lolos setelah evaluasi menyeluruh.',
        ]);
    }
}