<?php

namespace Database\Factories;

use App\Models\Dosen;
use Illuminate\Database\Eloquent\Factories\Factory;

class DosenFactory extends Factory
{
    protected $model = Dosen::class;

    public function definition(): array
    {
        $gelar_depan = ['Dr.', 'Prof.', 'Ir.', 'Drs.', 'Dra.', null];
        $gelar_belakang = ['S.Kom, M.Kom', 'S.T, M.T', 'S.E, M.E', 'S.H, M.H', 'S.Si, M.Si', 'S.Pd, M.Pd', 'Ph.D'];

        return [
            'nuptk' => $this->faker->unique()->numerify('####################'),
            'nama_dosen' => $this->faker->name(),
            'gelar_depan' => $this->faker->randomElement($gelar_depan),
            'gelar_belakang' => $this->faker->randomElement($gelar_belakang),
            'email_dosen' => $this->faker->unique()->safeEmail(),
            'no_hp_dosen' => '08' . $this->faker->numerify('########'),
            'password' => bcrypt('password123'),
            'role' => 'dosen',
            'is_active' => true,
            'email_verified_at' => now(),
        ];
    }
}
