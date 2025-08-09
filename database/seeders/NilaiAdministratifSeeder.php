<?php

namespace Database\Seeders;

use App\Models\NilaiAdministratif;
use App\Models\Proposal;
use App\Models\Reviewer;
use Illuminate\Database\Seeder;

class NilaiAdministratifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get proposals that should have administrative reviews
        // (proposals with status: review_administratif, review_substantif, revisi, lolos, tidak_lolos)
        $reviewableProposals = Proposal::whereIn('status_final', [
            'review_administratif',
            'review_substantif', 
            'revisi',
            'lolos',
            'tidak_lolos'
        ])->get();
        
        // Get available reviewers
        $reviewerIds = Reviewer::pluck('id_reviewer')->toArray();
        
        foreach ($reviewableProposals as $proposal) {
            // Each proposal gets one administrative review
            NilaiAdministratif::factory()->create([
                'id_proposal' => $proposal->id_proposal,
                'id_reviewer' => fake()->randomElement($reviewerIds),
                'checklist' => json_encode([
                    'format_penulisan' => fake()->boolean(85),
                    'kelengkapan_identitas' => fake()->boolean(90),
                    'struktur_proposal' => fake()->boolean(80),
                    'lampiran_lengkap' => fake()->boolean(75),
                    'bibliography_format' => fake()->boolean(70),
                    'ukuran_file_sesuai' => fake()->boolean(95),
                    'format_file_pdf' => fake()->boolean(98),
                    'jumlah_halaman_sesuai' => fake()->boolean(85),
                    'margin_sesuai' => fake()->boolean(90),
                    'font_sesuai' => fake()->boolean(88),
                ])
            ]);
        }

        // Create some additional administrative reviews for testing purposes
        NilaiAdministratif::factory()->count(5)->create();
    }
}