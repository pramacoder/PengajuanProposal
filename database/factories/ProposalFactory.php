<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Reviewer;
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
            'Sistem Prediksi {topic} Menggunakan {technology}',
            'Aplikasi {topic} Berbasis {technology}',
            'Pengembangan {topic} dengan {technology}',
            'Implementasi {topic} Menggunakan {technology}',
            'Analisis dan Implementasi {topic} dengan {technology}',
            'Sistem Informasi {topic} Berbasis {technology}',
            'Aplikasi Mobile {topic} Menggunakan {technology}',
            'Platform {topic} Berbasis {technology}',
            'Sistem Monitoring {topic} dengan {technology}',
            'Aplikasi Web {topic} Menggunakan {technology}'
        ];
        
        $topics = [
            'Sistem Parkir', 'E-Commerce UMKM', 'Manajemen Inventori', 'Sistem Pembayaran',
            'Monitoring Kesehatan', 'Sistem Akademik', 'Manajemen Proyek', 'Sistem Keamanan',
            'Monitoring Lingkungan', 'Sistem Transportasi', 'Manajemen SDM', 'Sistem Keuangan',
            'Monitoring IoT', 'Sistem Logistik', 'Manajemen Aset', 'Sistem Komunikasi'
        ];
        
        $technologies = [
            'Deep Learning', 'Machine Learning', 'Artificial Intelligence', 'Blockchain',
            'Internet of Things', 'Cloud Computing', 'Mobile Development', 'Web Development',
            'Data Analytics', 'Computer Vision', 'Natural Language Processing', 'Robotics',
            'Augmented Reality', 'Virtual Reality', 'Edge Computing', 'Microservices'
        ];
        
        $judul_template = $this->faker->randomElement($judul_templates);
        $topic = $this->faker->randomElement($topics);
        $technology = $this->faker->randomElement($technologies);
        $judul = str_replace(['{topic}', '{technology}'], [$topic, $technology], $judul_template);

        return [
            'judul_proposal' => $judul,
            'judul' => $judul,
            'tanggal_pengajuan' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'skim' => $this->faker->randomElement($skim_options),
            'dosen_pembimbing' => $this->faker->name() . ', ' . $this->faker->randomElement(['S.T., M.T.', 'S.Kom., M.Kom.', 'S.Si., M.Si.']),
            'dana_diajukan' => $this->faker->numberBetween(1000000, 15000000),
            'tahun_ajaran' => '2024/2025',
            'status_validasi' => $this->faker->randomElement($status_validasi),
            'status_final' => $this->faker->randomElement($status_final),
            'status' => $this->faker->randomElement(['pending', 'valid', 'tidak_valid', 'submitted', 'review_administratif', 'review_substantif', 'revisi', 'lolos', 'tidak_lolos']),
            'catatan' => $this->faker->optional()->paragraph(),
            'tanggal_validasi' => $this->faker->optional()->dateTimeBetween('-3 months', 'now'),
            'id_mahasiswa' => Mahasiswa::factory(),
            'id_dosen' => Dosen::factory(),
            // Kolom reviewer baru - default null (belum ditugaskan)
            'id_reviewer_administratif' => null,
            'id_reviewer_substantif_1' => null,
            'id_reviewer_substantif_2' => null,
        ];
    }

    // State untuk draft proposal
    public function draft()
    {
        return $this->state([
            'status_validasi' => 'pending',
            'status_final' => 'draft',
            'status' => 'pending',
        ]);
    }

    // State untuk submitted proposal
    public function submitted()
    {
        return $this->state([
            'status_validasi' => 'pending',
            'status_final' => 'submitted',
            'status' => 'submitted',
        ]);
    }

    // State untuk validated proposal
    public function validated()
    {
        return $this->state([
            'status_validasi' => 'valid',
            'status_final' => 'submitted',
            'status' => 'valid',
        ]);
    }

    // State untuk review administratif dengan reviewer
    public function reviewAdministratif()
    {
        return $this->state([
            'status_validasi' => 'valid',
            'status_final' => 'review_administratif',
            'status' => 'review_administratif',
            'id_reviewer_administratif' => Reviewer::factory(),
            'id_reviewer_substantif_1' => Reviewer::factory(),
            'id_reviewer_substantif_2' => Reviewer::factory(),
        ]);
    }

    // State untuk review substantif dengan reviewer
    public function reviewSubstantif()
    {
        return $this->state([
            'status_validasi' => 'valid',
            'status_final' => 'review_substantif',
            'status' => 'review_substantif',
            'id_reviewer_administratif' => Reviewer::factory(),
            'id_reviewer_substantif_1' => Reviewer::factory(),
            'id_reviewer_substantif_2' => Reviewer::factory(),
        ]);
    }

    // State untuk revisi
    public function revisi()
    {
        return $this->state([
            'status_validasi' => 'valid',
            'status_final' => 'revisi',
            'status' => 'revisi',
        ]);
    }

    // State untuk lolos
    public function lolos()
    {
        return $this->state([
            'status_validasi' => 'valid',
            'status_final' => 'lolos',
            'status' => 'lolos',
        ]);
    }

    // State untuk tidak lolos
    public function tidakLolos()
    {
        return $this->state([
            'status_validasi' => 'valid',
            'status_final' => 'tidak_lolos',
            'status' => 'tidak_lolos',
        ]);
    }

    // State untuk proposal yang sudah ditugaskan reviewer (untuk testing)
    public function assignedToReviewers()
    {
        return $this->state([
            'status_validasi' => 'valid',
            'status_final' => 'review_administratif',
            'status' => 'review_administratif',
            'id_reviewer_administratif' => Reviewer::factory(),
            'id_reviewer_substantif_1' => Reviewer::factory(),
            'id_reviewer_substantif_2' => Reviewer::factory(),
        ]);
    }
}