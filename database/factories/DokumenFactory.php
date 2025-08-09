<?php

namespace Database\Factories;

use App\Models\Dokumen;
use App\Models\Proposal;
use Illuminate\Database\Eloquent\Factories\Factory;

class DokumenFactory extends Factory
{
    protected $model = Dokumen::class;

    public function definition(): array
    {
        $skim_options = ['RE', 'RSH', 'KC', 'PM', 'PI', 'K', 'KI', 'VGK', 'AI', 'GFT'];
        
        $file_types = ['pdf', 'doc', 'docx'];
        $file_type = $this->faker->randomElement($file_types);
        $filename = $this->faker->slug() . '_proposal.' . $file_type;

        return [
            'skim' => $this->faker->randomElement($skim_options),
            'path_file' => 'uploads/documents/' . $filename,
            'tgl_upload' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'id_proposal' => Proposal::factory(),
        ];
    }
}