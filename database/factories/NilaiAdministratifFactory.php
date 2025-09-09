<?php

namespace Database\Factories;

use App\Models\NilaiAdministratif;
use App\Models\Proposal;
use App\Models\Reviewer;
use Illuminate\Database\Eloquent\Factories\Factory;

class NilaiAdministratifFactory extends Factory
{
    protected $model = NilaiAdministratif::class;

    public function definition(): array
    {
        // Checklist items for administrative review
        $checklist_items = [
            'format_penulisan' => $this->faker->boolean(80),
            'kelengkapan_identitas' => $this->faker->boolean(85),
            'struktur_proposal' => $this->faker->boolean(75),
            'lampiran_lengkap' => $this->faker->boolean(70),
            'bibliography_format' => $this->faker->boolean(80),
            'ukuran_file_sesuai' => $this->faker->boolean(90),
            'format_file_pdf' => $this->faker->boolean(95),
        ];

        $notes = [
            'Format penulisan sudah sesuai dengan template yang diberikan.',
            'Beberapa bagian perlu diperbaiki dari segi struktur penulisan.',
            'Lampiran kurang lengkap, harap dilengkapi.',
            'Secara keseluruhan proposal sudah memenuhi syarat administratif.',
            'Format bibliography perlu diperbaiki sesuai dengan standar IEEE.',
            'Ukuran font dan spacing perlu disesuaikan dengan ketentuan.',
        ];

        return [
            'note_administratif' => $this->faker->randomElement($notes),
            'checklist' => $checklist_items,
            'id_proposal' => Proposal::factory(),
            'id_reviewer' => Reviewer::factory(),
        ];
    }

    // State untuk review yang sudah selesai
    public function completed()
    {
        return $this->state([
            'note_administratif' => $this->faker->randomElement([
                'Proposal sudah memenuhi semua syarat administratif dengan baik.',
                'Format dan struktur proposal sudah sesuai standar yang ditetapkan.',
                'Kelengkapan dokumen dan lampiran sudah memadai.',
                'Proposal siap untuk tahap review substantif.',
                'Tidak ada kesalahan administratif yang signifikan.'
            ]),
        ]);
    }

    // State untuk review yang masih pending
    public function pending()
    {
        return $this->state([
            'note_administratif' => 'Review administratif dimulai',
        ]);
    }
}