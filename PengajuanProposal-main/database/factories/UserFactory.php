<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'identifier'  => $this->faker->unique()->numerify('OP####'),
            'name'        => $this->faker->name(),
            'email'       => $this->faker->unique()->safeEmail(),
            'password'    => static::$password ??= Hash::make('password'),
            'role'        => 'operator',
            'phone'       => $this->faker->phoneNumber(),
            'is_active'   => true,
            'metadata'    => null,
        ];
    }

    /** State: operator */
    public function operator(): static
    {
        return $this->state(fn () => [
            'role'       => 'operator',
            'identifier' => $this->faker->unique()->numerify('OP####'),
        ]);
    }

    /** State: mahasiswa */
    public function mahasiswa(): static
    {
        return $this->state(fn () => [
            'role'       => 'mahasiswa',
            'identifier' => $this->faker->unique()->numerify('##########'),
            'metadata'   => ['is_ketua' => true, 'prodi_id' => 1, 'fakultas_id' => 1],
        ]);
    }

    /** State: dosen */
    public function dosen(): static
    {
        return $this->state(fn () => [
            'role'       => 'dosen',
            'identifier' => $this->faker->unique()->numerify('NIDN####'),
        ]);
    }

    /** State: reviewer */
    public function reviewer(): static
    {
        return $this->state(fn () => [
            'role'       => 'reviewer',
            'identifier' => $this->faker->unique()->numerify('RV####'),
        ]);
    }

    /** State: pimpinan_pt */
    public function pimpinanPT(): static
    {
        return $this->state(fn () => [
            'role'       => 'pimpinan_pt',
            'identifier' => $this->faker->unique()->numerify('PT####'),
        ]);
    }

    /** State: inactive */
    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
