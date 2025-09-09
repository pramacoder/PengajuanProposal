<?php

namespace Database\Seeders;

use App\Models\Reviewer;
use Illuminate\Database\Seeder;

class ReviewerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Create specific reviewers for testing
        Reviewer::factory()->create([
            'nama_reviewer' => 'Prof. Dr. Maria Sari, M.Sc',
            'no_hp_reviewer' => '081234567894',
        ]);

        Reviewer::factory()->create([
            'nama_reviewer' => 'Dr. Eng. Rudi Hartono, S.T, M.T',
            'no_hp_reviewer' => '081234567895',
        ]);

        Reviewer::factory()->create([
            'nama_reviewer' => 'Dr. Indra Kusuma, S.Kom, M.Kom',
            'no_hp_reviewer' => '081234567896',
        ]);
    }
}