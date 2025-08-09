<?php

namespace Database\Seeders;

use App\Models\HasilFinal;
use App\Models\Proposal;
use App\Models\Pt;
use Illuminate\Database\Seeder;

class HasilFinalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get proposals that should have final results (lolos or tidak_lolos status)
        $finalProposals = Proposal::whereIn('status_final', ['lolos', 'tidak_lolos'])->get();
        
        // Get available PT IDs
        $ptIds = Pt::pluck('id_pt')->toArray();
        
        foreach ($finalProposals as $proposal) {
            $status = $proposal->status_final === 'lolos' ? 'lolos' : 'tidak_lolos';
            
            HasilFinal::factory()->create([
                'status_final' => $status,
                'id_proposal' => $proposal->id_proposal,
                'id_pt' => fake()->randomElement($ptIds),
                'catatan_final' => $this->generateFinalNote($status, $proposal),
            ]);
        }

        // Create some additional final results for proposals that might not have them yet
        $additionalProposals = Proposal::whereIn('status_final', ['revisi'])
            ->inRandomOrder()
            ->limit(3)
            ->get();

        foreach ($additionalProposals as $proposal) {
            $randomStatus = fake()->randomElement(['lolos', 'tidak_lolos']);
            
            HasilFinal::factory()->create([
                'status_final' => $randomStatus,
                'id_proposal' => $proposal->id_proposal,
                'id_pt' => fake()->randomElement($ptIds),
                'catatan_final' => $this->generateFinalNote($randomStatus, $proposal),
            ]);

            // Update proposal status to match final result
            $proposal->update(['status_final' => $randomStatus]);
        }
    }

    private function generateFinalNote(string $status, $proposal): string
    {
        if ($status === 'lolos') {
            $notes = [
                "Selamat! Proposal '{$proposal->judul_proposal}' telah lolos seleksi PKM {$proposal->skim}. Silakan lanjutkan ke tahap pelaksanaan sesuai timeline yang telah disetujui.",
                "Proposal Anda dinyatakan LOLOS dan berhak mendapatkan pendanaan. Pastikan untuk mengikuti monitoring dan evaluasi yang akan dilaksanakan.",
                "Terima kasih atas partisipasi Anda. Proposal telah memenuhi semua kriteria dan dinyatakan layak untuk didanai. Selamat melaksanakan program PKM!",
                "Proposal lolos dengan catatan untuk memperhatikan timeline dan target luaran yang telah disetujui dalam proposal."
            ];
        } else {
            $notes = [
                "Mohon maaf, proposal '{$proposal->judul_proposal}' belum dapat lolos pada seleksi kali ini. Kami mendorong Anda untuk terus berinovasi dan mengajukan proposal di periode berikutnya.",
                "Proposal tidak lolos karena beberapa aspek masih perlu diperkuat, khususnya dalam metodologi dan kelayakan implementasi. Silakan perbaiki untuk pengajuan berikutnya.",
                "Terima kasih atas partisipasi Anda. Meskipun proposal belum lolos kali ini, kami harap Anda tidak menyerah dan terus mengembangkan ide-ide inovatif.",
                "Proposal tidak memenuhi kriteria minimum yang ditetapkan. Silakan pelajari feedback dari reviewer dan perbaiki untuk pengajuan selanjutnya."
            ];
        }

        return fake()->randomElement($notes);
    }
}