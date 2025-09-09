<?php

namespace Database\Factories;

use App\Models\Pt;
use Illuminate\Database\Eloquent\Factories\Factory;

class PtFactory extends Factory
{
    protected $model = Pt::class;

    public function definition(): array
    {
        return [
            'nama_pt' => $this->faker->name(),
            'no_hp_pt' => '0' . $this->faker->numerify('####-######'),
            'email_pt' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password123'),
            'role' => 'operator',
            'is_active' => true,
        ];
    }
}
