<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use App\Models\Proposal;
use App\Models\Reviewer;

class ReviewDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Clean up existing review data first
        $this->command->info('Cleaning up existing review data...');
        NilaiAdministratif::truncate();
        NilaiSubstantif::truncate();
        
        $this->command->info('Creating fresh review data...');
        
        // Get existing proposals and reviewers
        $proposals = Proposal::all();
        $reviewers = Reviewer::all();
        
        if ($proposals->isEmpty()) {
            $this->command->warn('No proposals found. Please run ProposalSeeder first.');
            return;
        }
        
        if ($reviewers->isEmpty()) {
            $this->command->warn('No reviewers found. Please run ReviewerSeeder first.');
            return;
        }

        foreach ($proposals as $index => $proposal) {
            // Create administrative review with mixed true/false values (more false for negative feedback)
            NilaiAdministratif::create([
                'note_administratif' => 'ini merupakan catatan review testing',
                'checklist' => [
                    'format_dokumen' => false,       // ❌ Akan ditampilkan sebagai "Format Dokumen" - Perlu Perbaikan
                    'kelengkapan_data' => false,     // ❌ Akan ditampilkan sebagai "Kelengkapan Data" - Perlu Perbaikan
                    'struktur_proposal' => true,     // ✅ Tidak ditampilkan (sudah baik)
                    'penulisan' => false,            // ❌ Akan ditampilkan sebagai "Penulisan" - Perlu Perbaikan
                    'margin_dan_spasi' => false,     // ❌ Akan ditampilkan sebagai "Margin dan Spasi" - Perlu Perbaikan
                    'font_dan_ukuran' => true,       // ✅ Tidak ditampilkan (sudah baik)
                    'nomor_halaman' => false,        // ❌ Akan ditampilkan sebagai "Nomor Halaman" - Perlu Perbaikan
                    'daftar_pustaka' => false,       // ❌ Akan ditampilkan sebagai "Daftar Pustaka" - Perlu Perbaikan
                    'cover_proposal' => true,        // ✅ Tidak ditampilkan (sudah baik)
                    'lembar_pengesahan' => false,    // ❌ Akan ditampilkan sebagai "Lembar Pengesahan" - Perlu Perbaikan
                    'abstrak' => false,              // ❌ Akan ditampilkan sebagai "Abstrak" - Perlu Perbaikan
                    'kata_pengantar' => true,        // ✅ Tidak ditampilkan (sudah baik)
                    'daftar_isi' => false,           // ❌ Akan ditampilkan sebagai "Daftar Isi" - Perlu Perbaikan
                    'daftar_gambar' => false,        // ❌ Akan ditampilkan sebagai "Daftar Gambar" - Perlu Perbaikan
                    'daftar_tabel' => true,          // ✅ Tidak ditampilkan (sudah baik)
                    'lampiran' => false              // ❌ Akan ditampilkan sebagai "Lampiran" - Perlu Perbaikan
                ],
                'id_proposal' => $proposal->id_proposal,
                'id_reviewer' => $reviewers->random()->id_reviewer
            ]);
            
            // Create another review with all true values for testing perfect case
            if ($index === 0) {
                NilaiAdministratif::create([
                    'note_administratif' => 'Test review dengan semua kriteria memenuhi standar',
                    'checklist' => [
                        'format_dokumen' => true,        // ✅ Tidak ditampilkan (sudah baik)
                        'kelengkapan_data' => true,      // ✅ Tidak ditampilkan (sudah baik)
                        'struktur_proposal' => true,     // ✅ Tidak ditampilkan (sudah baik)
                        'penulisan' => true,             // ✅ Tidak ditampilkan (sudah baik)
                        'margin_dan_spasi' => true,      // ✅ Tidak ditampilkan (sudah baik)
                        'font_dan_ukuran' => true,       // ✅ Tidak ditampilkan (sudah baik)
                        'nomor_halaman' => true,         // ✅ Tidak ditampilkan (sudah baik)
                        'daftar_pustaka' => true,        // ✅ Tidak ditampilkan (sudah baik)
                        'cover_proposal' => true,        // ✅ Tidak ditampilkan (sudah baik)
                        'lembar_pengesahan' => true,     // ✅ Tidak ditampilkan (sudah baik)
                        'abstrak' => true,               // ✅ Tidak ditampilkan (sudah baik)
                        'kata_pengantar' => true,        // ✅ Tidak ditampilkan (sudah baik)
                        'daftar_isi' => true,            // ✅ Tidak ditampilkan (sudah baik)
                        'daftar_gambar' => true,         // ✅ Tidak ditampilkan (sudah baik)
                        'daftar_tabel' => true,          // ✅ Tidak ditampilkan (sudah baik)
                        'lampiran' => true               // ✅ Tidak ditampilkan (sudah baik)
                    ],
                    'id_proposal' => $proposal->id_proposal,
                    'id_reviewer' => $reviewers->random()->id_reviewer
                ]);
            }

            // Create substantive reviews (multiple reviewers)
            for ($i = 0; $i < 2; $i++) {
                NilaiSubstantif::create([
                    'note_substantif' => 'Review substantif proposal. Kualitas konten dan metodologi sudah cukup baik, namun perlu perbaikan pada beberapa aspek.',
                    'id_proposal' => $proposal->id_proposal,
                    'id_reviewer' => $reviewers->random()->id_reviewer
                ]);
            }
        }

        $this->command->info('Review data seeded successfully!');
        $this->command->info('');
        $this->command->info('Checklist format example (Negative Feedback):');
        $this->command->info(json_encode([
            'format_dokumen' => false,       // ❌ Akan ditampilkan sebagai "Format Dokumen" - Perlu Perbaikan
            'kelengkapan_data' => false,     // ❌ Akan ditampilkan sebagai "Kelengkapan Data" - Perlu Perbaikan
            'struktur_proposal' => true,     // ✅ Tidak ditampilkan (sudah baik)
            'penulisan' => false,            // ❌ Akan ditampilkan sebagai "Penulisan" - Perlu Perbaikan
            'margin_dan_spasi' => false,     // ❌ Akan ditampilkan sebagai "Margin dan Spasi" - Perlu Perbaikan
            'font_dan_ukuran' => true,       // ✅ Tidak ditampilkan (sudah baik)
            'nomor_halaman' => false,        // ❌ Akan ditampilkan sebagai "Nomor Halaman" - Perlu Perbaikan
            'daftar_pustaka' => false,       // ❌ Akan ditampilkan sebagai "Daftar Pustaka" - Perlu Perbaikan
            'cover_proposal' => true,        // ✅ Tidak ditampilkan (sudah baik)
            'lembar_pengesahan' => false,    // ❌ Akan ditampilkan sebagai "Lembar Pengesahan" - Perlu Perbaikan
            'abstrak' => false,              // ❌ Akan ditampilkan sebagai "Abstrak" - Perlu Perbaikan
            'kata_pengantar' => true,        // ✅ Tidak ditampilkan (sudah baik)
            'daftar_isi' => false,           // ❌ Akan ditampilkan sebagai "Daftar Isi" - Perlu Perbaikan
            'daftar_gambar' => false,        // ❌ Akan ditampilkan sebagai "Daftar Gambar" - Perlu Perbaikan
            'daftar_tabel' => true,          // ✅ Tidak ditampilkan (sudah baik)
            'lampiran' => false              // ❌ Akan ditampilkan sebagai "Lampiran" - Perlu Perbaikan
        ], JSON_PRETTY_PRINT));
        
        $this->command->info('');
        $this->command->info('Expected display (Negative Feedback):');
        $this->command->info('❌ Format Dokumen - Perlu Perbaikan');
        $this->command->info('❌ Kelengkapan Data - Perlu Perbaikan');
        $this->command->info('❌ Penulisan - Perlu Perbaikan');
        $this->command->info('❌ Margin dan Spasi - Perlu Perbaikan');
        $this->command->info('❌ Nomor Halaman - Perlu Perbaikan');
        $this->command->info('❌ Daftar Pustaka - Perlu Perbaikan');
        $this->command->info('❌ Lembar Pengesahan - Perlu Perbaikan');
        $this->command->info('❌ Abstrak - Perlu Perbaikan');
        $this->command->info('❌ Daftar Isi - Perlu Perbaikan');
        $this->command->info('❌ Daftar Gambar - Perlu Perbaikan');
        $this->command->info('❌ Lampiran - Perlu Perbaikan');
    }
}


