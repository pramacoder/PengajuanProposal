<?php

namespace Database\Factories;

use App\Models\RuangKontrol;
use App\Models\Pt;
use Illuminate\Database\Eloquent\Factories\Factory;

class RuangKontrolFactory extends Factory
{
    protected $model = RuangKontrol::class;

    public function definition(): array
    {
        return [
            'status_perbaikan' => $this->faker->randomElement(['perlu_perbaikan', 'tidak_perlu_perbaikan']),
            'status_pendaftaran' => $this->faker->randomElement(['terbuka', 'tertutup']),
            'id_pt' => Pt::factory(),
        ];
    }
}