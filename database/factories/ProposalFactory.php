<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProposalFactory extends Factory
{
    protected $model = Proposal::class;

    public function definition(): array
    {
        $skim_options = ['RE', 'RSH', 'KC', 'PM', 'PI', 'K', 'KI', 'VGK', 'AI', 'GFT'];
        $status_validasi = ['pending', 'valid', 'tidak_valid'];
        $status_final = ['draft', 'submitted', 'review_administratif', 'review_substantif', 'revisi', 'lolos', 'tidak_lolos'];

        $judul_templates = [
            'Pengembangan Aplikasi Mobile untuk {}',
            'Sistem Informasi Manajemen {} Berbasis Web',
            'Analisis dan Implementasi {} Menggunakan Machine Learning',
            'Peningkatan Efisiensi {} Melalui Teknologi IoT',
            'Platform Digital untuk Optimalisasi {}',
            'Inovasi {} Berbasis Artificial Intelligence',
            'Smart System untuk Monitoring {}',
            'Implementasi Blockchain dalam {}',
            'Aplikasi Augmented Reality untuk {}',
            'Sistem Prediksi {} Menggunakan Deep Learning'
        ];

        $topics = [
            'Pembelajaran Online', 'Manajemen Inventori', 'Sistem Parkir', 
            'E-Commerce UMKM', 'Monitoring Kesehatan', 'Smart Farming',
            'Deteksi Penyakit', 'Sistem Keamanan', 'Pengelolaan Sampah',
            'Transport Management'
        ];

        $template = $this->faker->randomElement($judul_templates);
        $topic = $this->faker->randomElement($topics);
        $judul = str_replace('{}', $topic, $template);

        return [
            'judul_proposal' => $judul,
            'tanggal_pengajuan' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'skim' => $this->faker->randomElement($skim_options),
            'status_validasi' => $this->faker->randomElement($status_validasi),
            'status_final' => $this->faker->randomElement($status_final),
            'catatan' => $this->faker->optional()->paragraph(),
            'id_mahasiswa' => Mahasiswa::factory(),
        ];
    }

    // State methods for different proposal statuses
    public function draft()
    {
        return $this->state([
            'status_final' => 'draft',
            'status_validasi' => 'pending',
        ]);
    }

    public function submitted()
    {
        return $this->state([
            'status_final' => 'submitted',
            'status_validasi' => 'pending',
        ]);
    }

    public function validated()
    {
        return $this->state([
            'status_validasi' => 'valid',
            'status_final' => 'review_administratif',
        ]);
    }

    public function reviewAdministratif()
    {
        return $this->state([
            'status_validasi' => 'valid',
            'status_final' => 'review_administratif',
        ]);
    }

    public function reviewSubstantif()
    {
        return $this->state([
            'status_validasi' => 'valid',
            'status_final' => 'review_substantif',
        ]);
    }

    public function revisi()
    {
        return $this->state([
            'status_validasi' => 'valid',
            'status_final' => 'revisi',
        ]);
    }

    public function lolos()
    {
        return $this->state([
            'status_validasi' => 'valid',
            'status_final' => 'lolos',
        ]);
    }

    public function tidakLolos()
    {
        return $this->state([
            'status_validasi' => 'valid',
            'status_final' => 'tidak_lolos',
        ]);
    }
}