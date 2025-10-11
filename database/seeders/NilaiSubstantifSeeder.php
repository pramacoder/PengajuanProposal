<?php

namespace Database\Seeders;

use App\Models\NilaiSubstantif;
use App\Models\Proposal;
use App\Models\Reviewer;
use Illuminate\Database\Seeder;

class NilaiSubstantifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get proposals that should have substantive reviews
        // (proposals with status: review_substantif, revisi, lolos, tidak_lolos)
        $reviewableProposals = Proposal::whereIn('status_final', [
            'review_substantif',
            'revisi',
            'lolos',
            'tidak_lolos'
        ])->get();
        
        // Get available reviewers
        $reviewerIds = Reviewer::pluck('id_reviewer')->toArray();
        
        foreach ($reviewableProposals as $proposal) {
            // Each proposal gets TWO substantive reviews (as per your specification)
            
            // First reviewer
            NilaiSubstantif::factory()->create([
                'id_proposal' => $proposal->id_proposal,
                'id_reviewer' => fake()->randomElement($reviewerIds),
                'note_substantif' => $this->generateDetailedResult(),
            ]);
            
            // Second reviewer (different from first)
            $availableReviewers = array_diff($reviewerIds, [NilaiSubstantif::where('id_proposal', $proposal->id_proposal)->first()->id_reviewer ?? null]);
            
            NilaiSubstantif::factory()->create([
                'id_proposal' => $proposal->id_proposal,
                'id_reviewer' => fake()->randomElement($availableReviewers),
                'note_substantif' => $this->generateDetailedResult(),
            ]);
        }

        // Create some additional substantive reviews for testing
        // DIHAPUS: NilaiSubstantif::factory()->count(8)->create();
    }

    private function generateDetailedResult(): string
    {
        $aspects = [
            'Kualitas Inovasi' => fake()->numberBetween(60, 95),
            'Metodologi Penelitian' => fake()->numberBetween(65, 90),
            'Kelayakan Implementasi' => fake()->numberBetween(70, 95),
            'Dampak dan Manfaat' => fake()->numberBetween(60, 88),
            'Kesesuaian Budget' => fake()->numberBetween(75, 95),
            'Timeline Pelaksanaan' => fake()->numberBetween(70, 90),
        ];

        $result = "HASIL PENILAIAN SUBSTANTIF:\n\n";
        $totalScore = 0;
        $count = count($aspects);

        foreach ($aspects as $aspect => $score) {
            $result .= "• {$aspect}: {$score}/100\n";
            $totalScore += $score;
        }

        $averageScore = round($totalScore / $count, 1);
        $result .= "\nSkor Rata-rata: {$averageScore}/100\n\n";

        if ($averageScore >= 85) {
            $result .= "KESIMPULAN: Sangat Baik - Proposal sangat layak untuk didanai dengan sedikit revisi minor.";
        } elseif ($averageScore >= 75) {
            $result .= "KESIMPULAN: Baik - Proposal layak untuk didanai dengan beberapa perbaikan.";
        } elseif ($averageScore >= 65) {
            $result .= "KESIMPULAN: Cukup - Proposal memiliki potensi namun memerlukan revisi signifikan.";
        } else {
            $result .= "KESIMPULAN: Kurang - Proposal memerlukan perbaikan mendasar atau tidak direkomendasikan.";
        }

        return $result;
    }
}