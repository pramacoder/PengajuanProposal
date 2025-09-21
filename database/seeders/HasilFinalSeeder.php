<?php

namespace Database\Seeders;

use App\Models\HasilFinal;
use App\Models\Proposal;
use App\Models\PT;
use Illuminate\Database\Seeder;

class HasilFinalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get proposals from 2023 and 2024 that should have final results
        $proposals2023 = Proposal::where('tahun_ajaran', '2023/2024')->get();
        $proposals2024 = Proposal::where('tahun_ajaran', '2024/2025')->get();
        
        // Get available PT IDs
        $ptIds = PT::pluck('id_pt')->toArray();
        
        if (empty($ptIds)) {
            $this->command->error('Tidak ada PT yang tersedia untuk hasil final!');
            return;
        }
        
        $this->command->info('Membuat hasil final untuk proposal 2023...');
        foreach ($proposals2023 as $proposal) {
            $status = $proposal->status_final === 'lolos' ? 'lolos' : 'tidak_lolos';
            
            HasilFinal::factory()->create([
                'status_final' => $status,
                'id_proposal' => $proposal->id_proposal,
                'id_pt' => fake()->randomElement($ptIds),
                'catatan_final' => $this->generateFinalNote($status, $proposal),
                'nilai' => $status === 'lolos' ? fake()->numberBetween(75, 95) : fake()->numberBetween(40, 74),
            ]);
        }
        
        $this->command->info('Membuat hasil final untuk proposal 2024...');
        foreach ($proposals2024 as $proposal) {
            $status = $proposal->status_final === 'lolos' ? 'lolos' : 'tidak_lolos';
            
            HasilFinal::factory()->create([
                'status_final' => $status,
                'id_proposal' => $proposal->id_proposal,
                'id_pt' => fake()->randomElement($ptIds),
                'catatan_final' => $this->generateFinalNote($status, $proposal),
                'nilai' => $status === 'lolos' ? fake()->numberBetween(75, 95) : fake()->numberBetween(40, 74),
            ]);
        }
        
        $this->command->info('HasilFinalSeeder berhasil dijalankan!');
        $this->command->info('Total hasil final yang dibuat: ' . HasilFinal::count());
        $this->command->info('Proposal 2023 dengan hasil final: ' . $proposals2023->count());
        $this->command->info('Proposal 2024 dengan hasil final: ' . $proposals2024->count());
    }

    private function generateFinalNote(string $status, $proposal): string
    {
        if ($status === 'lolos') {
            $notes = [
                "Selamat! Proposal '{$proposal->judul_proposal}' telah lolos seleksi PKM {$proposal->skim}. Silakan lanjutkan ke tahap pelaksanaan sesuai timeline yang telah disetujui.",
                "Proposal Anda dinyatakan LOLOS dan berhak mendapatkan pendanaan. Pastikan untuk mengikuti monitoring dan evaluasi yang akan dilaksanakan.",
                "Terima kasih atas partisipasi Anda. Proposal telah memenuhi semua kriteria dan dinyatakan layak untuk didanai. Selamat melaksanakan program PKM!",
                "Proposal lolos dengan catatan untuk memperhatikan timeline dan target luaran yang telah disetujui dalam proposal.",
                "Proposal dinyatakan LOLOS dengan skor tinggi. Tim dinilai memiliki inovasi yang baik dan metodologi yang solid.",
                "Selamat! Proposal berhasil lolos seleksi dengan predikat sangat memuaskan. Lanjutkan dengan semangat!",
            ];
        } else {
            $notes = [
                "Mohon maaf, proposal '{$proposal->judul_proposal}' belum dapat lolos pada seleksi kali ini. Kami mendorong Anda untuk terus berinovasi dan mengajukan proposal di periode berikutnya.",
                "Proposal tidak lolos karena beberapa aspek masih perlu diperkuat, khususnya dalam metodologi dan kelayakan implementasi. Silakan perbaiki untuk pengajuan berikutnya.",
                "Terima kasih atas partisipasi Anda. Meskipun proposal belum lolos kali ini, kami harap Anda tidak menyerah dan terus mengembangkan ide-ide inovatif.",
                "Proposal tidak memenuhi kriteria minimum yang ditetapkan. Silakan pelajari feedback dari reviewer dan perbaiki untuk pengajuan selanjutnya.",
                "Proposal belum lolos karena perlu perbaikan pada aspek teknis dan kelayakan implementasi. Jangan menyerah, teruslah berinovasi!",
                "Meskipun proposal tidak lolos, ide yang disampaikan cukup menarik. Perbaiki metodologi dan ajukan kembali di periode berikutnya.",
            ];
        }

        return fake()->randomElement($notes);
    }
}