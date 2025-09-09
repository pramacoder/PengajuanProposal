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
        
        $this->command->info('Memulai seeding dokumen...');
        $this->command->info('Total proposal: ' . $proposals->count());
        
        foreach ($proposals as $proposal) {
            // Setiap proposal hanya memiliki 1 dokumen utama
            Dokumen::factory()->create([
                'id_proposal' => $proposal->id_proposal,
                'skim' => $proposal->skim, // Match the skim with proposal
                'path_file' => 'uploads/documents/proposal_' . $proposal->id_proposal . '.pdf',
                'file_proposal' => 'proposal_' . $proposal->id_proposal . '.pdf',
            ]);
            
            $this->command->info('Dokumen dibuat untuk proposal ID: ' . $proposal->id_proposal);
        }
        
        $this->command->info('DokumenSeeder berhasil dijalankan!');
        $this->command->info('Total dokumen yang dibuat: ' . Dokumen::count());
        $this->command->info('Rasio dokumen:proposal = ' . Dokumen::count() . ':' . $proposals->count());
    }
}