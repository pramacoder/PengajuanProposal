<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\Proposal;
use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        $prodi_options = ['Informatika', 'Sistem Informasi', 'Teknik Komputer', 'Teknik Elektro', 'Teknik Mesin', 'Teknik Sipil'];
        $fakultas_options = ['Fakultas Teknik', 'Fakultas Ilmu Komputer', 'Fakultas Ekonomi', 'Fakultas Hukum'];
        $role_options = ['ketua', 'anggota1', 'anggota2', 'anggota3', 'anggota4'];

        return [
            'id_proposal' => Proposal::factory(),
            'id_mahasiswa' => Mahasiswa::factory(),
            'nama' => $this->faker->name(),
            'nim' => $this->faker->unique()->numerify('2021####'),
            'prodi' => $this->faker->randomElement($prodi_options),
            'fakultas' => $this->faker->randomElement($fakultas_options),
            'email' => $this->faker->email(),
            'no_hp' => $this->faker->numerify('08##########'),
            'role' => $this->faker->randomElement($role_options),
            'status' => 'active',
        ];
    }

    // State untuk ketua tim
    public function ketua()
    {
        return $this->state([
            'role' => 'ketua',
        ]);
    }

    // State untuk anggota 1
    public function anggota1()
    {
        return $this->state([
            'role' => 'anggota1',
        ]);
    }

    // State untuk anggota 2
    public function anggota2()
    {
        return $this->state([
            'role' => 'anggota2',
        ]);
    }

    // State untuk anggota 3
    public function anggota3()
    {
        return $this->state([
            'role' => 'anggota3',
        ]);
    }

    // State untuk anggota 4
    public function anggota4()
    {
        return $this->state([
            'role' => 'anggota4',
        ]);
    }

    // State untuk anggota aktif
    public function active()
    {
        return $this->state([
            'status' => 'active',
        ]);
    }

    // State untuk anggota tidak aktif
    public function inactive()
    {
        return $this->state([
            'status' => 'inactive',
        ]);
    }

    // State untuk tim minimal (ketua + 2 anggota)
    public function minimalTeam($proposalId)
    {
        return [
            // Ketua tim
            $this->ketua()->make([
                'id_proposal' => $proposalId,
                'nama' => $this->faker->name(),
                'nim' => $this->faker->unique()->numerify('2021####'),
                'prodi' => $this->faker->randomElement(['Informatika', 'Sistem Informasi', 'Teknik Komputer']),
                'fakultas' => $this->faker->randomElement(['Fakultas Teknik', 'Fakultas Ilmu Komputer']),
                'email' => $this->faker->email(),
                'no_hp' => $this->faker->numerify('08##########'),
            ]),
            // Anggota 1
            $this->anggota1()->make([
                'id_proposal' => $proposalId,
                'nama' => $this->faker->name(),
                'nim' => $this->faker->unique()->numerify('2021####'),
                'prodi' => $this->faker->randomElement(['Informatika', 'Sistem Informasi', 'Teknik Komputer']),
                'fakultas' => $this->faker->randomElement(['Fakultas Teknik', 'Fakultas Ilmu Komputer']),
                'email' => $this->faker->email(),
                'no_hp' => $this->faker->numerify('08##########'),
            ]),
            // Anggota 2
            $this->anggota2()->make([
                'id_proposal' => $proposalId,
                'nama' => $this->faker->name(),
                'nim' => $this->faker->unique()->numerify('2021####'),
                'prodi' => $this->faker->randomElement(['Informatika', 'Sistem Informasi', 'Teknik Komputer']),
                'fakultas' => $this->faker->randomElement(['Fakultas Teknik', 'Fakultas Ilmu Komputer']),
                'email' => $this->faker->email(),
                'no_hp' => $this->faker->numerify('08##########'),
            ]),
        ];
    }

    // State untuk tim lengkap (ketua + 4 anggota)
    public function fullTeam($proposalId)
    {
        return [
            // Ketua tim
            $this->ketua()->make([
                'id_proposal' => $proposalId,
                'nama' => $this->faker->name(),
                'nim' => $this->faker->unique()->numerify('2021####'),
                'prodi' => $this->faker->randomElement(['Informatika', 'Sistem Informasi', 'Teknik Komputer']),
                'fakultas' => $this->faker->randomElement(['Fakultas Teknik', 'Fakultas Ilmu Komputer']),
                'email' => $this->faker->email(),
                'no_hp' => $this->faker->numerify('08##########'),
            ]),
            // Anggota 1
            $this->anggota1()->make([
                'id_proposal' => $proposalId,
                'nama' => $this->faker->name(),
                'nim' => $this->faker->unique()->numerify('2021####'),
                'prodi' => $this->faker->randomElement(['Informatika', 'Sistem Informasi', 'Teknik Komputer']),
                'fakultas' => $this->faker->randomElement(['Fakultas Teknik', 'Fakultas Ilmu Komputer']),
                'email' => $this->faker->email(),
                'no_hp' => $this->faker->numerify('08##########'),
            ]),
            // Anggota 2
            $this->anggota2()->make([
                'id_proposal' => $proposalId,
                'nama' => $this->faker->name(),
                'nim' => $this->faker->unique()->numerify('2021####'),
                'prodi' => $this->faker->randomElement(['Informatika', 'Sistem Informasi', 'Teknik Komputer']),
                'fakultas' => $this->faker->randomElement(['Fakultas Teknik', 'Fakultas Ilmu Komputer']),
                'email' => $this->faker->email(),
                'no_hp' => $this->faker->numerify('08##########'),
            ]),
            // Anggota 3
            $this->anggota3()->make([
                'id_proposal' => $proposalId,
                'nama' => $this->faker->name(),
                'nim' => $this->faker->unique()->numerify('2021####'),
                'prodi' => $this->faker->randomElement(['Informatika', 'Sistem Informasi', 'Teknik Komputer']),
                'fakultas' => $this->faker->randomElement(['Fakultas Teknik', 'Fakultas Ilmu Komputer']),
                'email' => $this->faker->email(),
                'no_hp' => $this->faker->numerify('08##########'),
            ]),
            // Anggota 4
            $this->anggota4()->make([
                'id_proposal' => $proposalId,
                'nama' => $this->faker->name(),
                'nim' => $this->faker->unique()->numerify('2021####'),
                'prodi' => $this->faker->randomElement(['Informatika', 'Sistem Informasi', 'Teknik Komputer']),
                'fakultas' => $this->faker->randomElement(['Fakultas Teknik', 'Fakultas Ilmu Komputer']),
                'email' => $this->faker->email(),
                'no_hp' => $this->faker->numerify('08##########'),
            ]),
        ];
    }
}
