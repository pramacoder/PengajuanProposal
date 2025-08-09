<?php

namespace Database\Factories;

use App\Models\Pt;
use Illuminate\Database\Eloquent\Factories\Factory;

class PtFactory extends Factory
{
    protected $model = Pt::class;

    public function definition(): array
    {
        $universities = [
            'Universitas Gadjah Mada',
            'Institut Teknologi Bandung',
            'Universitas Indonesia',
            'Institut Pertanian Bogor',
            'Universitas Airlangga',
            'Universitas Brawijaya',
            'Universitas Diponegoro',
            'Universitas Sebelas Maret',
            'Institut Teknologi Sepuluh Nopember',
            'Universitas Padjadjaran'
        ];

        return [
            'nama_pt' => $this->faker->randomElement($universities),
            'no_hp_pt' => '0' . $this->faker->numerify('####-######'),
            'email_pt' => $this->faker->unique()->companyEmail(),
            'password' => bcrypt('password123'),
            'role' => 'operator',
            'is_active' => true,
            'email_verified_at' => now(),
        ];
    }
}
