<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProposalSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $mahasiswas = DB::table('users')->where('role', 'mahasiswa')->orderBy('id')->get();
        $dosens = DB::table('users')->where('role', 'dosen')->pluck('id')->toArray();
        $reviewers = DB::table('users')->where('role', 'reviewer')->pluck('id')->toArray();

        $proposals = [];

        // 2022/2023 - 5 Proposals
        $proposals[] = $this->createProposal($faker, '2022/2023', '2022-09-15', 'RE',  'Pengembangan Sistem Informasi Manajemen Koperasi Berbasis Web',     15000000.00, [0, 1, 2],    $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2022/2023', '2022-10-10', 'RSH', 'Analisis Matematika dalam Optimasi Algoritma Machine Learning',     20000000.00, [3, 4, 5],    $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2022/2023', '2022-11-05', 'K',   'Pemanfaatan Energi Surya untuk Sistem Pendingin Rumah',             12000000.00, [6, 7, 8],    $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2022/2023', '2022-12-01', 'PM',  'Sintesis Nanopartikel untuk Aplikasi Biomedis',                     18000000.00, [9, 10, 11],  $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2022/2023', '2023-01-20', 'PI',  'Konservasi Keanekaragaman Hayati di Kawasan Hutan Lindung',         25000000.00, [12, 13, 14], $mahasiswas, $dosens, $reviewers);

        // 2023/2024 - 8 Proposals
        $proposals[] = $this->createProposal($faker, '2023/2024', '2023-03-15', 'KC',  'Strategi Pemasaran Digital untuk UMKM di Bali',                    10000000.00, [0, 1, 2],    $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2023/2024', '2023-04-10', 'KI',  'Analisis Sistem Akuntansi pada Perusahaan Startup',                 8000000.00, [3, 4, 5],    $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2023/2024', '2023-05-20', 'VGK', 'Desain Bangunan Tahan Gempa dengan Material Lokal',                22000000.00, [6, 7, 8],    $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2023/2024', '2023-06-15', 'GFT', 'Sistem Monitoring Energi Listrik Berbasis IoT',                    16000000.00, [9, 10, 11],  $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2023/2024', '2023-07-10', 'AI',  'Optimasi Mesin Diesel untuk Efisiensi Bahan Bakar',                14000000.00, [12, 13, 14], $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2023/2024', '2023-08-05', 'RE',  'Pengembangan Aplikasi Mobile untuk Manajemen Keuangan Pribadi',    13000000.00, [15, 16, 17], $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2023/2024', '2023-09-12', 'RSH', 'Studi Pengaruh Media Sosial terhadap Perilaku Konsumen',           11000000.00, [18, 19, 20], $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2023/2024', '2023-10-08', 'K',   'Inovasi Material Konstruksi Ramah Lingkungan',                     17000000.00, [21, 22, 23], $mahasiswas, $dosens, $reviewers);

        // 2024/2025 - 7 Proposals
        $proposals[] = $this->createProposal($faker, '2024/2025', '2024-03-20', 'PM',  'Desain Arsitektur Berkelanjutan untuk Kawasan Pesisir',             17000000.00, [0, 1, 2],    $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2024/2025', '2024-04-15', 'PI',  'Pengembangan Obat Herbal dari Tumbuhan Lokal Bali',                19000000.00, [3, 4, 5],    $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2024/2025', '2024-05-10', 'KC',  'Peningkatan Kualitas Pelayanan Kesehatan di Puskesmas',            13000000.00, [6, 7, 8],    $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2024/2025', '2024-06-25', 'KI',  'Reformasi Hukum dalam Penanganan Kasus Korupsi',                   11000000.00, [9, 10, 11],  $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2024/2025', '2024-07-18', 'VGK', 'Teknologi Pertanian Berkelanjutan untuk Lahan Kering',             21000000.00, [12, 13, 14], $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2024/2025', '2024-08-22', 'GFT', 'Peningkatan Produktivitas Ternak Sapi Bali',                       15000000.00, [15, 16, 17], $mahasiswas, $dosens, $reviewers);
        $proposals[] = $this->createProposal($faker, '2024/2025', '2024-09-14', 'AI',  'Pencegahan Penyakit Zoonosis pada Hewan Ternak',                   18000000.00, [18, 19, 20], $mahasiswas, $dosens, $reviewers);

        DB::table('proposals')->insert($proposals);
    }

    private function createProposal($faker, $tahunAjaran, $tanggalPengajuan, $skim, $judul, $dana, $mahasiswaIndexes, $allMahasiswas, $dosens, $reviewers)
    {
        $ketua    = $allMahasiswas[$mahasiswaIndexes[0]];
        $anggota1 = $allMahasiswas[$mahasiswaIndexes[1]];
        $anggota2 = $allMahasiswas[$mahasiswaIndexes[2]];

        $ketuaMeta    = json_decode($ketua->metadata, true) ?? [];
        $anggota1Meta = json_decode($anggota1->metadata, true) ?? [];
        $anggota2Meta = json_decode($anggota2->metadata, true) ?? [];

        $dosenId = $faker->randomElement($dosens);
        $dosenUser = DB::table('users')->where('id', $dosenId)->first();

        return [
            'judul_proposal' => $judul,
            'judul' => $judul,
            'tanggal_pengajuan' => $tanggalPengajuan,
            'skim' => $skim,
            'dosen_pembimbing' => $dosenUser->name ?? 'Unknown',
            'dana_diajukan' => $dana,
            'tahun_ajaran' => $tahunAjaran,
            'status_validasi' => 'valid',
            'status_final' => 'lolos',
            'status' => 'lolos',
            'catatan' => $faker->paragraph,
            'tanggal_validasi' => now(),
            'id_mahasiswa' => $ketua->id,
            'id_dosen' => $dosenId,
            'team_id' => $faker->unique()->randomNumber(5),
            'id_reviewer_administratif' => $faker->randomElement($reviewers),
            'id_reviewer_substantif_1' => $faker->randomElement($reviewers),
            'id_reviewer_substantif_2' => $faker->randomElement($reviewers),

            'ketua_nama'    => $ketua->name,
            'ketua_nim'     => $ketua->identifier,
            'ketua_prodi'   => $ketuaMeta['prodi_name'] ?? '',
            'ketua_fakultas' => $ketuaMeta['fakultas_name'] ?? '',
            'ketua_email'   => $ketua->email,
            'ketua_no_hp'   => $ketua->phone,

            'anggota1_nama'    => $anggota1->name,
            'anggota1_nim'     => $anggota1->identifier,
            'anggota1_prodi'   => $anggota1Meta['prodi_name'] ?? '',
            'anggota1_fakultas' => $anggota1Meta['fakultas_name'] ?? '',
            'anggota1_email'   => $anggota1->email,
            'anggota1_no_hp'   => $anggota1->phone,

            'anggota2_nama'    => $anggota2->name,
            'anggota2_nim'     => $anggota2->identifier,
            'anggota2_prodi'   => $anggota2Meta['prodi_name'] ?? '',
            'anggota2_fakultas' => $anggota2Meta['fakultas_name'] ?? '',
            'anggota2_email'   => $anggota2->email,
            'anggota2_no_hp'   => $anggota2->phone,

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
