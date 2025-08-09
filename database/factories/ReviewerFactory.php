<?php

namespace Database\Factories;

use App\Models\Reviewer;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewerFactory extends Factory
{
    protected $model = Reviewer::class;

    public function definition(): array
    {
        return [
            'nama_reviewer' => $this->faker->name(),
            'no_hp_reviewer' => '08' . $this->faker->numerify('########'),
            'email_reviewer' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password123'),
            'role' => 'reviewer',
            'is_active' => true,
            'email_verified_at' => now(),
        ];
    }
}