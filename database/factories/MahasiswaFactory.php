<?php

namespace Database\Factories;

use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class MahasiswaFactory extends Factory
{
    protected $model = Mahasiswa::class;

    public function definition(): array
    {
        $faculties = ['Teknik', 'Ekonomi', 'Hukum', 'Kedokteran', 'MIPA', 'Pertanian', 'Sosial Politik', 'Sastra'];
        $programs = [
            'Teknik' => ['Teknik Informatika', 'Teknik Sipil', 'Teknik Mesin', 'Teknik Elektro'],
            'Ekonomi' => ['Manajemen', 'Akuntansi', 'Ekonomi Pembangunan'],
            'Hukum' => ['Ilmu Hukum'],
            'Kedokteran' => ['Pendidikan Dokter', 'Keperawatan', 'Farmasi'],
            'MIPA' => ['Matematika', 'Fisika', 'Kimia', 'Biologi'],
            'Pertanian' => ['Agroteknologi', 'Peternakan', 'Kehutanan'],
            'Sosial Politik' => ['Ilmu Politik', 'Sosiologi', 'Ilmu Komunikasi'],
            'Sastra' => ['Sastra Indonesia', 'Sastra Inggris', 'Bahasa dan Sastra Daerah']
        ];

        $faculty = $this->faker->randomElement($faculties);
        $prodi = $this->faker->randomElement($programs[$faculty]);

        return [
            'nim' => $this->faker->unique()->numerify('##########'),
            'nama_mhs' => $this->faker->name(),
            'prodi_mhs' => $prodi,
            'fakultas_mhs' => $faculty,
            'no_hp_mhs' => '08' . $this->faker->numerify('########'),
            'email_mhs' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
            'is_active' => true,
            'email_verified_at' => now(),
        ];
    }
}