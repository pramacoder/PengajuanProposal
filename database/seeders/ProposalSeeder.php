<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proposal;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Reviewer;
use App\Models\Dokumen;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use Faker\Factory as Faker;

class ProposalSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        $this->command->info('Memulai seeding proposal...');
        
        // Ambil data yang sudah ada dari seeder lain
        $reviewers = Reviewer::all();
        $mahasiswas = Mahasiswa::all();
        $dosens = Dosen::all();
        
        // Validasi jumlah data yang tersedia
        if ($mahasiswas->count() < 60) {
            $this->command->error('Jumlah mahasiswa tidak cukup! Minimal butuh 60 mahasiswa untuk 20 proposal.');
            return;
        }
        
        if ($dosens->count() < 20) {
            $this->command->error('Jumlah dosen tidak cukup! Minimal butuh 20 dosen untuk 20 proposal.');
            return;
        }
        
        $this->command->info("Total mahasiswa tersedia: " . $mahasiswas->count());
        $this->command->info("Total dosen tersedia: " . $dosens->count());
        $this->command->info("Maksimal proposal yang bisa dibuat: " . floor($mahasiswas->count() / 3));
        
        $proposals = [];
        $usedMahasiswas = collect(); // Track mahasiswa yang sudah digunakan
        
        // 1. Proposal 2023 (7 proposal) - Sudah selesai, memiliki hasil final
        $this->command->info('Membuat proposal 2023 (sudah selesai)...');
        for ($i = 0; $i < 7; $i++) {
            $availableMahasiswas = $mahasiswas->whereNotIn('id_mahasiswa', $usedMahasiswas->pluck('id_mahasiswa'));
            if ($availableMahasiswas->count() < 3) {
                $this->command->error('Mahasiswa tidak cukup untuk membuat proposal ke-' . ($i + 1));
                break;
            }
            
            $ketua = $availableMahasiswas->random();
            $anggota1 = $availableMahasiswas->where('id_mahasiswa', '!=', $ketua->id_mahasiswa)->random();
            $anggota2 = $availableMahasiswas->whereNotIn('id_mahasiswa', [$ketua->id_mahasiswa, $anggota1->id_mahasiswa])->random();
            
            // Anggota 3 dan 4 opsional (random 0-2 anggota tambahan)
            $remainingMahasiswas = $availableMahasiswas->whereNotIn('id_mahasiswa', [$ketua->id_mahasiswa, $anggota1->id_mahasiswa, $anggota2->id_mahasiswa]);
            $additionalMembers = $remainingMahasiswas->take(rand(0, min(2, $remainingMahasiswas->count())));
            $anggota3 = $additionalMembers->shift();
            $anggota4 = $additionalMembers->shift();
            
            $proposal = Proposal::factory()->create([
                'id_mahasiswa' => $ketua->id_mahasiswa,
                'id_dosen' => $dosens->random()->id_dosen,
                'status_validasi' => 'valid',
                'status_final' => $faker->randomElement(['lolos', 'tidak_lolos']),
                'status' => 'lolos',
                'judul_proposal' => 'Proposal PKM 2023 - ' . ($i + 1),
                'judul' => 'Proposal PKM 2023 - ' . ($i + 1),
                'tanggal_pengajuan' => $faker->dateTimeBetween('2023-01-01', '2023-12-31'),
                'tahun_ajaran' => '2023/2024',
                'dana_diajukan' => $faker->numberBetween(1000000, 15000000),
                'dosen_pembimbing' => $dosens->random()->nama_dosen,
                // Kolom wajib - ketua
                'ketua_nama' => $ketua->nama_mhs,
                'ketua_nim' => $ketua->nim,
                'ketua_prodi' => $ketua->prodi_mhs,
                'ketua_fakultas' => $ketua->fakultas_mhs,
                'ketua_email' => $ketua->email_mhs,
                'ketua_no_hp' => $ketua->no_hp_mhs,
                // Kolom wajib - anggota 1
                'anggota1_nama' => $anggota1->nama_mhs,
                'anggota1_nim' => $anggota1->nim,
                'anggota1_prodi' => $anggota1->prodi_mhs,
                'anggota1_fakultas' => $anggota1->fakultas_mhs,
                'anggota1_email' => $anggota1->email_mhs,
                'anggota1_no_hp' => $anggota1->no_hp_mhs,
                // Kolom wajib - anggota 2
                'anggota2_nama' => $anggota2->nama_mhs,
                'anggota2_nim' => $anggota2->nim,
                'anggota2_prodi' => $anggota2->prodi_mhs,
                'anggota2_fakultas' => $anggota2->fakultas_mhs,
                'anggota2_email' => $anggota2->email_mhs,
                'anggota2_no_hp' => $anggota2->no_hp_mhs,
                // Kolom opsional - anggota 3
                'anggota3_nama' => $anggota3 ? $anggota3->nama_mhs : null,
                'anggota3_nim' => $anggota3 ? $anggota3->nim : null,
                'anggota3_prodi' => $anggota3 ? $anggota3->prodi_mhs : null,
                'anggota3_fakultas' => $anggota3 ? $anggota3->fakultas_mhs : null,
                'anggota3_email' => $anggota3 ? $anggota3->email_mhs : null,
                'anggota3_no_hp' => $anggota3 ? $anggota3->no_hp_mhs : null,
                // Kolom opsional - anggota 4
                'anggota4_nama' => $anggota4 ? $anggota4->nama_mhs : null,
                'anggota4_nim' => $anggota4 ? $anggota4->nim : null,
                'anggota4_prodi' => $anggota4 ? $anggota4->prodi_mhs : null,
                'anggota4_fakultas' => $anggota4 ? $anggota4->fakultas_mhs : null,
                'anggota4_email' => $anggota4 ? $anggota4->email_mhs : null,
                'anggota4_no_hp' => $anggota4 ? $anggota4->no_hp_mhs : null,
            ]);
            
            $proposals[] = $proposal;
            $usedMahasiswas->push($ketua, $anggota1, $anggota2);
            if ($anggota3) $usedMahasiswas->push($anggota3);
            if ($anggota4) $usedMahasiswas->push($anggota4);
        }
        
        // 2. Proposal 2024 (8 proposal) - Sudah selesai, memiliki hasil final
        $this->command->info('Membuat proposal 2024 (sudah selesai)...');
        for ($i = 0; $i < 8; $i++) {
            $availableMahasiswas = $mahasiswas->whereNotIn('id_mahasiswa', $usedMahasiswas->pluck('id_mahasiswa'));
            if ($availableMahasiswas->count() < 3) {
                $this->command->error('Mahasiswa tidak cukup untuk membuat proposal ke-' . ($i + 8));
                break;
            }
            
            $ketua = $availableMahasiswas->random();
            $anggota1 = $availableMahasiswas->where('id_mahasiswa', '!=', $ketua->id_mahasiswa)->random();
            $anggota2 = $availableMahasiswas->whereNotIn('id_mahasiswa', [$ketua->id_mahasiswa, $anggota1->id_mahasiswa])->random();
            
            $remainingMahasiswas = $availableMahasiswas->whereNotIn('id_mahasiswa', [$ketua->id_mahasiswa, $anggota1->id_mahasiswa, $anggota2->id_mahasiswa]);
            $additionalMembers = $remainingMahasiswas->take(rand(0, min(2, $remainingMahasiswas->count())));
            $anggota3 = $additionalMembers->shift();
            $anggota4 = $additionalMembers->shift();
            
            $proposal = Proposal::factory()->create([
                'id_mahasiswa' => $ketua->id_mahasiswa,
                'id_dosen' => $dosens->random()->id_dosen,
                'status_validasi' => 'valid',
                'status_final' => $faker->randomElement(['lolos', 'tidak_lolos']),
                'status' => 'lolos',
                'judul_proposal' => 'Proposal PKM 2024 - ' . ($i + 1),
                'judul' => 'Proposal PKM 2024 - ' . ($i + 1),
                'tanggal_pengajuan' => $faker->dateTimeBetween('2024-01-01', '2024-12-31'),
                'tahun_ajaran' => '2024/2025',
                'dana_diajukan' => $faker->numberBetween(1000000, 15000000),
                'dosen_pembimbing' => $dosens->random()->nama_dosen,
                // Kolom wajib - ketua
                'ketua_nama' => $ketua->nama_mhs,
                'ketua_nim' => $ketua->nim,
                'ketua_prodi' => $ketua->prodi_mhs,
                'ketua_fakultas' => $ketua->fakultas_mhs,
                'ketua_email' => $ketua->email_mhs,
                'ketua_no_hp' => $ketua->no_hp_mhs,
                // Kolom wajib - anggota 1
                'anggota1_nama' => $anggota1->nama_mhs,
                'anggota1_nim' => $anggota1->nim,
                'anggota1_prodi' => $anggota1->prodi_mhs,
                'anggota1_fakultas' => $anggota1->fakultas_mhs,
                'anggota1_email' => $anggota1->email_mhs,
                'anggota1_no_hp' => $anggota1->no_hp_mhs,
                // Kolom wajib - anggota 2
                'anggota2_nama' => $anggota2->nama_mhs,
                'anggota2_nim' => $anggota2->nim,
                'anggota2_prodi' => $anggota2->prodi_mhs,
                'anggota2_fakultas' => $anggota2->fakultas_mhs,
                'anggota2_email' => $anggota2->email_mhs,
                'anggota2_no_hp' => $anggota2->no_hp_mhs,
                // Kolom opsional - anggota 3
                'anggota3_nama' => $anggota3 ? $anggota3->nama_mhs : null,
                'anggota3_nim' => $anggota3 ? $anggota3->nim : null,
                'anggota3_prodi' => $anggota3 ? $anggota3->prodi_mhs : null,
                'anggota3_fakultas' => $anggota3 ? $anggota3->fakultas_mhs : null,
                'anggota3_email' => $anggota3 ? $anggota3->email_mhs : null,
                'anggota3_no_hp' => $anggota3 ? $anggota3->no_hp_mhs : null,
                // Kolom opsional - anggota 4
                'anggota4_nama' => $anggota4 ? $anggota4->nama_mhs : null,
                'anggota4_nim' => $anggota4 ? $anggota4->nim : null,
                'anggota4_prodi' => $anggota4 ? $anggota4->prodi_mhs : null,
                'anggota4_fakultas' => $anggota4 ? $anggota4->fakultas_mhs : null,
                'anggota4_email' => $anggota4 ? $anggota4->email_mhs : null,
                'anggota4_no_hp' => $anggota4 ? $anggota4->no_hp_mhs : null,
            ]);
            
            $proposals[] = $proposal;
            $usedMahasiswas->push($ketua, $anggota1, $anggota2);
            if ($anggota3) $usedMahasiswas->push($anggota3);
            if ($anggota4) $usedMahasiswas->push($anggota4);
        }
        
        // 3. Proposal 2025 (5 proposal) - Masih dalam proses
        $this->command->info('Membuat proposal 2025 (masih dalam proses)...');
        $proposals2025 = [];
        for ($i = 0; $i < 5; $i++) {
            $availableMahasiswas = $mahasiswas->whereNotIn('id_mahasiswa', $usedMahasiswas->pluck('id_mahasiswa'));
            if ($availableMahasiswas->count() < 3) {
                $this->command->error('Mahasiswa tidak cukup untuk membuat proposal ke-' . ($i + 16));
                break;
            }
            
            $ketua = $availableMahasiswas->random();
            $anggota1 = $availableMahasiswas->where('id_mahasiswa', '!=', $ketua->id_mahasiswa)->random();
            $anggota2 = $availableMahasiswas->whereNotIn('id_mahasiswa', [$ketua->id_mahasiswa, $anggota1->id_mahasiswa])->random();
            
            $remainingMahasiswas = $availableMahasiswas->whereNotIn('id_mahasiswa', [$ketua->id_mahasiswa, $anggota1->id_mahasiswa, $anggota2->id_mahasiswa]);
            $additionalMembers = $remainingMahasiswas->take(rand(0, min(2, $remainingMahasiswas->count())));
            $anggota3 = $additionalMembers->shift();
            $anggota4 = $additionalMembers->shift();
            
            $statusOptions = ['draft', 'submitted', 'review_administratif', 'review_substantif', 'revisi'];
            $status = $faker->randomElement($statusOptions);
            
            $proposal = Proposal::factory()->create([
                'id_mahasiswa' => $ketua->id_mahasiswa,
                'id_dosen' => $dosens->random()->id_dosen,
                'status_validasi' => $status === 'draft' ? 'pending' : 'valid',
                'status_final' => $status,
                'status' => $status,
                'judul_proposal' => 'Proposal PKM 2025 - ' . ($i + 1),
                'judul' => 'Proposal PKM 2025 - ' . ($i + 1),
                'tanggal_pengajuan' => $faker->dateTimeBetween('2025-01-01', 'now'),
                'tahun_ajaran' => '2025/2026',
                'dana_diajukan' => $faker->numberBetween(1000000, 15000000),
                'dosen_pembimbing' => $dosens->random()->nama_dosen,
                // Kolom wajib - ketua
                'ketua_nama' => $ketua->nama_mhs,
                'ketua_nim' => $ketua->nim,
                'ketua_prodi' => $ketua->prodi_mhs,
                'ketua_fakultas' => $ketua->fakultas_mhs,
                'ketua_email' => $ketua->email_mhs,
                'ketua_no_hp' => $ketua->no_hp_mhs,
                // Kolom wajib - anggota 1
                'anggota1_nama' => $anggota1->nama_mhs,
                'anggota1_nim' => $anggota1->nim,
                'anggota1_prodi' => $anggota1->prodi_mhs,
                'anggota1_fakultas' => $anggota1->fakultas_mhs,
                'anggota1_email' => $anggota1->email_mhs,
                'anggota1_no_hp' => $anggota1->no_hp_mhs,
                // Kolom wajib - anggota 2
                'anggota2_nama' => $anggota2->nama_mhs,
                'anggota2_nim' => $anggota2->nim,
                'anggota2_prodi' => $anggota2->prodi_mhs,
                'anggota2_fakultas' => $anggota2->fakultas_mhs,
                'anggota2_email' => $anggota2->email_mhs,
                'anggota2_no_hp' => $anggota2->no_hp_mhs,
                // Kolom opsional - anggota 3
                'anggota3_nama' => $anggota3 ? $anggota3->nama_mhs : null,
                'anggota3_nim' => $anggota3 ? $anggota3->nim : null,
                'anggota3_prodi' => $anggota3 ? $anggota3->prodi_mhs : null,
                'anggota3_fakultas' => $anggota3 ? $anggota3->fakultas_mhs : null,
                'anggota3_email' => $anggota3 ? $anggota3->email_mhs : null,
                'anggota3_no_hp' => $anggota3 ? $anggota3->no_hp_mhs : null,
                // Kolom opsional - anggota 4
                'anggota4_nama' => $anggota4 ? $anggota4->nama_mhs : null,
                'anggota4_nim' => $anggota4 ? $anggota4->nim : null,
                'anggota4_prodi' => $anggota4 ? $anggota4->prodi_mhs : null,
                'anggota4_fakultas' => $anggota4 ? $anggota4->fakultas_mhs : null,
                'anggota4_email' => $anggota4 ? $anggota4->email_mhs : null,
                'anggota4_no_hp' => $anggota4 ? $anggota4->no_hp_mhs : null,
            ]);
            
            $proposals2025[] = $proposal;
            $proposals[] = $proposal;
            $usedMahasiswas->push($ketua, $anggota1, $anggota2);
            if ($anggota3) $usedMahasiswas->push($anggota3);
            if ($anggota4) $usedMahasiswas->push($anggota4);
        }
        
        $this->command->info('ProposalSeeder berhasil dijalankan!');
        $this->command->info('Total proposal yang dibuat: ' . count($proposals));
        $this->command->info('Proposal 2023 (selesai): 7 proposal');
        $this->command->info('Proposal 2024 (selesai): 8 proposal');
        $this->command->info('Proposal 2025 (proses): 5 proposal');
        $this->command->info('Mahasiswa yang digunakan: ' . $usedMahasiswas->count());
        $this->command->info('Mahasiswa yang tersisa: ' . ($mahasiswas->count() - $usedMahasiswas->count()));
        
        $this->command->info('');
        $this->command->info('=== LOGIKA YANG BENAR ===');
        $this->command->info('✓ 1 proposal = 1 team');
        $this->command->info('✓ 1 team = minimal 3 mahasiswa, maksimal 5 mahasiswa');
        $this->command->info('✓ 1 mahasiswa hanya bisa terikat pada 1 proposal');
        $this->command->info('✓ Setiap proposal wajib memiliki dosen pendamping');
        $this->command->info('✓ Total 20 proposal membutuhkan minimal 60 mahasiswa (20 × 3)');
        $this->command->info('✓ Total 20 proposal membutuhkan minimal 20 dosen (1 dosen per proposal)');
        $this->command->info('✓ Proposal 2023-2024 sudah selesai dan memiliki hasil final');
        $this->command->info('✓ Proposal 2025 masih dalam proses');
    }
}