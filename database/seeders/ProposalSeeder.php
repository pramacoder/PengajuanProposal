<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProposalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        // Ambil semua mahasiswa (25 mahasiswa)
        $mahasiswas = DB::table('mahasiswas')->get();
        $dosens = DB::table('dosens')->pluck('id_dosen')->toArray();
        $reviewers = DB::table('reviewers')->pluck('id_reviewer')->toArray();
        
        $proposals = [];
        
        // ===== TAHUN 2022/2023 - 5 Proposal (15 mahasiswa) =====
        // Proposal 1 (Mahasiswa 1, 2, 3)
        $proposals[] = $this->createProposal(
            $faker,
            '2022/2023',
            '2022-09-15',
            'RE',
            'Pengembangan Sistem Informasi Manajemen Koperasi Berbasis Web',
            15000000.00,
            [1, 2, 3],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 2 (Mahasiswa 4, 5, 6)
        $proposals[] = $this->createProposal(
            $faker,
            '2022/2023',
            '2022-10-10',
            'RSH',
            'Analisis Matematika dalam Optimasi Algoritma Machine Learning',
            20000000.00,
            [4, 5, 6],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 3 (Mahasiswa 7, 8, 9)
        $proposals[] = $this->createProposal(
            $faker,
            '2022/2023',
            '2022-11-05',
            'K',
            'Pemanfaatan Energi Surya untuk Sistem Pendingin Rumah',
            12000000.00,
            [7, 8, 9],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 4 (Mahasiswa 10, 11, 12)
        $proposals[] = $this->createProposal(
            $faker,
            '2022/2023',
            '2022-12-01',
            'PM',
            'Sintesis Nanopartikel untuk Aplikasi Biomedis',
            18000000.00,
            [10, 11, 12],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 5 (Mahasiswa 13, 14, 15)
        $proposals[] = $this->createProposal(
            $faker,
            '2022/2023',
            '2023-01-20',
            'PI',
            'Konservasi Keanekaragaman Hayati di Kawasan Hutan Lindung',
            25000000.00,
            [13, 14, 15],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // ===== TAHUN 2023/2024 - 8 Proposal (24 mahasiswa) =====
        // Proposal 6 (Mahasiswa 1, 2, 3) - Mahasiswa yang sama bisa mengajukan di tahun berbeda
        $proposals[] = $this->createProposal(
            $faker,
            '2023/2024',
            '2023-03-15',
            'KC',
            'Strategi Pemasaran Digital untuk UMKM di Bali',
            10000000.00,
            [1, 2, 3],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 7 (Mahasiswa 4, 5, 6)
        $proposals[] = $this->createProposal(
            $faker,
            '2023/2024',
            '2023-04-10',
            'KI',
            'Analisis Sistem Akuntansi pada Perusahaan Startup',
            8000000.00,
            [4, 5, 6],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 8 (Mahasiswa 7, 8, 9)
        $proposals[] = $this->createProposal(
            $faker,
            '2023/2024',
            '2023-05-20',
            'VGK',
            'Desain Bangunan Tahan Gempa dengan Material Lokal',
            22000000.00,
            [7, 8, 9],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 9 (Mahasiswa 10, 11, 12)
        $proposals[] = $this->createProposal(
            $faker,
            '2023/2024',
            '2023-06-15',
            'GFT',
            'Sistem Monitoring Energi Listrik Berbasis IoT',
            16000000.00,
            [10, 11, 12],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 10 (Mahasiswa 13, 14, 15)
        $proposals[] = $this->createProposal(
            $faker,
            '2023/2024',
            '2023-07-10',
            'AI',
            'Optimasi Mesin Diesel untuk Efisiensi Bahan Bakar',
            14000000.00,
            [13, 14, 15],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 11 (Mahasiswa 16, 17, 18)
        $proposals[] = $this->createProposal(
            $faker,
            '2023/2024',
            '2023-08-05',
            'RE',
            'Pengembangan Aplikasi Mobile untuk Manajemen Keuangan Pribadi',
            13000000.00,
            [16, 17, 18],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 12 (Mahasiswa 19, 20, 21)
        $proposals[] = $this->createProposal(
            $faker,
            '2023/2024',
            '2023-09-12',
            'RSH',
            'Studi Pengaruh Media Sosial terhadap Perilaku Konsumen',
            11000000.00,
            [19, 20, 21],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 13 (Mahasiswa 22, 23, 24)
        $proposals[] = $this->createProposal(
            $faker,
            '2023/2024',
            '2023-10-08',
            'K',
            'Inovasi Material Konstruksi Ramah Lingkungan',
            17000000.00,
            [22, 23, 24],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // ===== TAHUN 2024/2025 - 7 Proposal (21 mahasiswa) =====
        // Proposal 14 (Mahasiswa 1, 2, 3)
        $proposals[] = $this->createProposal(
            $faker,
            '2024/2025',
            '2024-03-20',
            'PM',
            'Desain Arsitektur Berkelanjutan untuk Kawasan Pesisir',
            17000000.00,
            [1, 2, 3],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 15 (Mahasiswa 4, 5, 6)
        $proposals[] = $this->createProposal(
            $faker,
            '2024/2025',
            '2024-04-15',
            'PI',
            'Pengembangan Obat Herbal dari Tumbuhan Lokal Bali',
            19000000.00,
            [4, 5, 6],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 16 (Mahasiswa 7, 8, 9)
        $proposals[] = $this->createProposal(
            $faker,
            '2024/2025',
            '2024-05-10',
            'KC',
            'Peningkatan Kualitas Pelayanan Kesehatan di Puskesmas',
            13000000.00,
            [7, 8, 9],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 17 (Mahasiswa 10, 11, 12)
        $proposals[] = $this->createProposal(
            $faker,
            '2024/2025',
            '2024-06-25',
            'KI',
            'Reformasi Hukum dalam Penanganan Kasus Korupsi',
            11000000.00,
            [10, 11, 12],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 18 (Mahasiswa 13, 14, 15)
        $proposals[] = $this->createProposal(
            $faker,
            '2024/2025',
            '2024-07-18',
            'VGK',
            'Teknologi Pertanian Berkelanjutan untuk Lahan Kering',
            21000000.00,
            [13, 14, 15],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 19 (Mahasiswa 16, 17, 18)
        $proposals[] = $this->createProposal(
            $faker,
            '2024/2025',
            '2024-08-22',
            'GFT',
            'Peningkatan Produktivitas Ternak Sapi Bali',
            15000000.00,
            [16, 17, 18],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        // Proposal 20 (Mahasiswa 19, 20, 21)
        $proposals[] = $this->createProposal(
            $faker,
            '2024/2025',
            '2024-09-14',
            'AI',
            'Pencegahan Penyakit Zoonosis pada Hewan Ternak',
            18000000.00,
            [19, 20, 21],
            $mahasiswas,
            $dosens,
            $reviewers
        );
        
        DB::table('proposals')->insert($proposals);
    }
    
    /**
     * Helper function untuk membuat proposal
     */
    private function createProposal($faker, $tahunAjaran, $tanggalPengajuan, $skim, $judul, $dana, $mahasiswaIds, $allMahasiswas, $dosens, $reviewers)
    {
        // Ambil data mahasiswa berdasarkan ID
        $ketua = $allMahasiswas->where('id_mahasiswa', $mahasiswaIds[0])->first();
        $anggota1 = $allMahasiswas->where('id_mahasiswa', $mahasiswaIds[1])->first();
        $anggota2 = $allMahasiswas->where('id_mahasiswa', $mahasiswaIds[2])->first();
        
        // Ambil dosen pembimbing dari ketua
        $dosenPembimbing = DB::table('dosens')->where('id_dosen', $ketua->id_dosen_pembimbing)->first();
        
        $proposal = [
            'judul_proposal' => $judul,
            'judul' => $judul,
            'tanggal_pengajuan' => $tanggalPengajuan,
            'skim' => $skim,
            'dosen_pembimbing' => $dosenPembimbing->nama_dosen ?? 'Unknown',
            'dana_diajukan' => $dana,
            'tahun_ajaran' => $tahunAjaran,
            'status_validasi' => 'valid',
            'status_final' => 'lolos',
            'status' => 'lolos',
            'catatan' => $faker->paragraph,
            'tanggal_validasi' => now(),
            'id_mahasiswa' => $ketua->id_mahasiswa,
            'id_dosen' => $ketua->id_dosen_pembimbing,
            'team_id' => $faker->unique()->randomNumber(5),
            'id_reviewer_administratif' => $faker->randomElement($reviewers),
            'id_reviewer_substantif_1' => $faker->randomElement($reviewers),
            'id_reviewer_substantif_2' => $faker->randomElement($reviewers),
            
            // Data Ketua
            'ketua_nama' => $ketua->nama_mhs,
            'ketua_nim' => $ketua->nim,
            'ketua_prodi' => $ketua->prodi_mhs,
            'ketua_fakultas' => $ketua->fakultas_mhs,
            'ketua_email' => $ketua->email_mhs,
            'ketua_no_hp' => $ketua->no_hp_mhs,
            
            // Data Anggota 1
            'anggota1_nama' => $anggota1->nama_mhs,
            'anggota1_nim' => $anggota1->nim,
            'anggota1_prodi' => $anggota1->prodi_mhs,
            'anggota1_fakultas' => $anggota1->fakultas_mhs,
            'anggota1_email' => $anggota1->email_mhs,
            'anggota1_no_hp' => $anggota1->no_hp_mhs,
            
            // Data Anggota 2
            'anggota2_nama' => $anggota2->nama_mhs,
            'anggota2_nim' => $anggota2->nim,
            'anggota2_prodi' => $anggota2->prodi_mhs,
            'anggota2_fakultas' => $anggota2->fakultas_mhs,
            'anggota2_email' => $anggota2->email_mhs,
            'anggota2_no_hp' => $anggota2->no_hp_mhs,
            
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        return $proposal;
    }
}
