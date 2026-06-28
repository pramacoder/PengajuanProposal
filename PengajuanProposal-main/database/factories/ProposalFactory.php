<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $skims = ['RE', 'RSH', 'K', 'PM', 'PI', 'KC', 'KI', 'VGK', 'GFT', 'AI'];
        $statuses = ['pending', 'valid', 'tidak_valid', 'submitted', 'review_administratif', 'review_substantif', 'revisi', 'lolos', 'tidak_lolos'];
        $years = ['2023/2024', '2024/2025'];
        
        return [
            'judul_proposal' => $this->faker->sentence(8),
            'judul' => $this->faker->sentence(8),
            'tanggal_pengajuan' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'skim' => $this->faker->randomElement($skims),
            'dosen_pembimbing' => $this->faker->name(),
            'dana_diajukan' => $this->faker->randomFloat(2, 5000000, 30000000),
            'tahun_ajaran' => $this->faker->randomElement($years),
            'status_validasi' => $this->faker->randomElement(['pending', 'valid', 'tidak_valid']),
            'status_final' => $this->faker->randomElement(['draft', 'submitted', 'review_administratif', 'review_substantif', 'revisi', 'lolos', 'tidak_lolos']),
            'status' => $this->faker->randomElement($statuses),
            'catatan' => $this->faker->paragraph(),
            'tanggal_validasi' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'id_mahasiswa' => $this->faker->numberBetween(1, 60),
            'id_dosen' => $this->faker->numberBetween(1, 10),
            'team_id' => $this->faker->numberBetween(1, 20),
            'id_reviewer_administratif' => $this->faker->numberBetween(1, 12),
            'id_reviewer_substantif_1' => $this->faker->numberBetween(1, 12),
            'id_reviewer_substantif_2' => $this->faker->numberBetween(1, 12),
            'ketua_nama' => $this->faker->name(),
            'ketua_nim' => $this->faker->numerify('##########'),
            'ketua_prodi' => $this->faker->randomElement(['Teknik Informatika', 'Matematika', 'Fisika', 'Kimia', 'Biologi', 'Manajemen', 'Akuntansi']),
            'ketua_fakultas' => $this->faker->randomElement(['Fakultas Teknik', 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'Fakultas Ekonomi dan Bisnis']),
            'ketua_email' => $this->faker->unique()->safeEmail(),
            'ketua_no_hp' => $this->faker->numerify('08##########'),
            'anggota1_nama' => $this->faker->name(),
            'anggota1_nim' => $this->faker->numerify('##########'),
            'anggota1_prodi' => $this->faker->randomElement(['Teknik Informatika', 'Matematika', 'Fisika', 'Kimia', 'Biologi', 'Manajemen', 'Akuntansi']),
            'anggota1_fakultas' => $this->faker->randomElement(['Fakultas Teknik', 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'Fakultas Ekonomi dan Bisnis']),
            'anggota1_email' => $this->faker->unique()->safeEmail(),
            'anggota1_no_hp' => $this->faker->numerify('08##########'),
            'anggota2_nama' => $this->faker->name(),
            'anggota2_nim' => $this->faker->numerify('##########'),
            'anggota2_prodi' => $this->faker->randomElement(['Teknik Informatika', 'Matematika', 'Fisika', 'Kimia', 'Biologi', 'Manajemen', 'Akuntansi']),
            'anggota2_fakultas' => $this->faker->randomElement(['Fakultas Teknik', 'Fakultas Matematika dan Ilmu Pengetahuan Alam', 'Fakultas Ekonomi dan Bisnis']),
            'anggota2_email' => $this->faker->unique()->safeEmail(),
            'anggota2_no_hp' => $this->faker->numerify('08##########'),
        ];
    }
}

