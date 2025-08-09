<?php

namespace Database\Seeders;

use App\Models\Dokumen;
use App\Models\Proposal;
use Illuminate\Database\Seeder;

class DokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all proposals
        $proposals = Proposal::all();
        
        foreach ($proposals as $proposal) {
            // Each proposal should have at least one document
            Dokumen::factory()->create([
                'id_proposal' => $proposal->id_proposal,
                'skim' => $proposal->skim, // Match the skim with proposal
            ]);
            
            // Some proposals might have additional documents (30% chance)
            if (fake()->boolean(30)) {
                Dokumen::factory()->create([
                    'id_proposal' => $proposal->id_proposal,
                    'skim' => $proposal->skim,
                    'path_file' => 'uploads/documents/' . fake()->slug() . '_lampiran.pdf',
                ]);
            }
            
            // Few proposals might have supplementary documents (10% chance)
            if (fake()->boolean(10)) {
                Dokumen::factory()->create([
                    'id_proposal' => $proposal->id_proposal,
                    'skim' => $proposal->skim,
                    'path_file' => 'uploads/documents/' . fake()->slug() . '_budget.xlsx',
                ]);
            }
        }
    }
}