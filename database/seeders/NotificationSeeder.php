<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use App\Models\Proposal;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some sample users and proposals for seeding
        $mahasiswas = \App\Models\Mahasiswa::take(5)->get();
        $dosens = \App\Models\Dosen::take(3)->get();
        $reviewers = \App\Models\Reviewer::take(2)->get();
        $proposals = Proposal::take(5)->get();

        if ($proposals->isEmpty()) {
            $this->command->info('Skipping notification seeding - no proposals found');
            return;
        }

        // Create sample notifications for mahasiswa
        foreach ($mahasiswas as $mahasiswa) {
            $this->createNotificationsForUser($mahasiswa, 'mahasiswa', $proposals);
        }

        // Create sample notifications for dosen
        foreach ($dosens as $dosen) {
            $this->createNotificationsForUser($dosen, 'dosen', $proposals);
        }

        // Create sample notifications for reviewer
        foreach ($reviewers as $reviewer) {
            $this->createNotificationsForUser($reviewer, 'reviewer', $proposals);
        }

        $this->command->info('Notification seeding completed successfully!');
    }

    /**
     * Get user type based on user model
     */
    private function getUserType(User $user): string
    {
        if ($user->mahasiswa) {
            return 'mahasiswa';
        } elseif ($user->dosen) {
            return 'dosen';
        } elseif ($user->reviewer) {
            return 'reviewer';
        } elseif ($user->operator) {
            return 'operator';
        }
        
        return 'user';
    }

    /**
     * Get random notification type
     */
    private function getRandomNotificationType(): string
    {
        $types = ['info', 'success', 'warning', 'danger'];
        return $types[array_rand($types)];
    }

    /**
     * Get notification title based on type and user type
     */
    private function getNotificationTitle(string $type, string $userType): string
    {
        $titles = [
            'mahasiswa' => [
                'info' => 'Update Status Proposal',
                'success' => 'Proposal Disetujui',
                'warning' => 'Perlu Revisi',
                'danger' => 'Proposal Ditolak'
            ],
            'dosen' => [
                'info' => 'Proposal Baru Perlu Validasi',
                'success' => 'Validasi Berhasil',
                'warning' => 'Proposal Perlu Perbaikan',
                'danger' => 'Validasi Gagal'
            ],
            'reviewer' => [
                'info' => 'Proposal Perlu Review',
                'success' => 'Review Selesai',
                'warning' => 'Review Perlu Perbaikan',
                'danger' => 'Review Ditolak'
            ],
            'operator' => [
                'info' => 'Proposal Pending',
                'success' => 'Proposal Diproses',
                'warning' => 'Perlu Tindakan',
                'danger' => 'Error Sistem'
            ]
        ];

        return $titles[$userType][$type] ?? 'Notifikasi Sistem';
    }

    /**
     * Get notification message based on type, user type, and proposal
     */
    private function getNotificationMessage(string $type, string $userType, Proposal $proposal): string
    {
        $judul = $proposal->judul ?? 'Proposal PKM';
        
        $messages = [
            'mahasiswa' => [
                'info' => "Status proposal '{$judul}' telah diperbarui. Silakan periksa detail perubahan.",
                'success' => "Selamat! Proposal '{$judul}' telah disetujui dan lolos ke tahap selanjutnya.",
                'warning' => "Proposal '{$judul}' memerlukan revisi. Silakan periksa detail revisi yang diperlukan.",
                'danger' => "Mohon maaf, proposal '{$judul}' tidak dapat diproses. Silakan periksa detail penolakan."
            ],
            'dosen' => [
                'info' => "Proposal '{$judul}' dari mahasiswa memerlukan validasi Anda.",
                'success' => "Validasi proposal '{$judul}' berhasil diselesaikan.",
                'warning' => "Proposal '{$judul}' perlu perbaikan sebelum dapat divalidasi.",
                'danger' => "Validasi proposal '{$judul}' gagal. Silakan periksa kembali."
            ],
            'reviewer' => [
                'info' => "Proposal '{$judul}' memerlukan review substantif dari Anda.",
                'success' => "Review proposal '{$judul}' telah berhasil diselesaikan.",
                'warning' => "Review proposal '{$judul}' perlu perbaikan.",
                'danger' => "Review proposal '{$judul}' ditolak. Silakan periksa kembali."
            ],
            'operator' => [
                'info' => "Ada proposal baru yang menunggu untuk diproses dan didistribusikan.",
                'success' => "Proposal '{$judul}' berhasil diproses dan didistribusikan ke reviewer.",
                'warning' => "Proposal '{$judul}' memerlukan tindakan segera dari operator.",
                'danger' => "Terjadi error saat memproses proposal '{$judul}'."
            ]
        ];

        return $messages[$userType][$type] ?? 'Notifikasi sistem untuk pengguna.';
    }

    /**
     * Get action URL based on user type and proposal
     */
    private function getActionUrl(string $userType, Proposal $proposal): string
    {
        $proposalId = $proposal->id_proposal;
        
        $urls = [
            'mahasiswa' => "/mahasiswa/proposal/{$proposalId}",
            'dosen' => "/dosen/proposal/{$proposalId}/detail",
            'reviewer' => "/reviewer/proposal/{$proposalId}/detail",
            'operator' => "/operator/dashboard"
        ];

        return $urls[$userType] ?? '/dashboard';
    }

    /**
     * Get action text based on user type
     */
    private function getActionText(string $userType): string
    {
        $texts = [
            'mahasiswa' => 'Lihat Detail',
            'dosen' => 'Validasi',
            'reviewer' => 'Review',
            'operator' => 'Proses'
        ];

        return $texts[$userType] ?? 'Lihat';
    }

    /**
     * Create notifications for a specific user
     */
    private function createNotificationsForUser($user, string $userType, $proposals): void
    {
        // Create 2-5 notifications per user
        $notificationCount = rand(2, 5);
        
        for ($i = 0; $i < $notificationCount; $i++) {
            $proposal = $proposals->random();
            $type = $this->getRandomNotificationType();
            
            Notification::create([
                'user_identifier' => $this->getUserIdentifier($user, $userType),
                'user_type' => $userType,
                'title' => $this->getNotificationTitle($type, $userType),
                'message' => $this->getNotificationMessage($type, $userType, $proposal),
                'type' => $type,
                'data' => [
                    'proposal_id' => $proposal->id_proposal,
                    'action_url' => $this->getActionUrl($userType, $proposal),
                    'action_text' => $this->getActionText($userType),
                ],
                'proposal_id' => $proposal->id_proposal,
                'read_at' => rand(0, 1) ? now()->subDays(rand(1, 7)) : null,
            ]);
        }
    }

    /**
     * Get user identifier based on user type
     */
    private function getUserIdentifier($user, string $userType): string
    {
        switch ($userType) {
            case 'mahasiswa':
                return $user->nim ?? '12345678';
            case 'dosen':
                return $user->nidn ?? '0012345678';
            case 'reviewer':
                return $user->id_reviewer ?? '1';
            case 'operator':
                return $user->id_operator ?? '1';
            default:
                return 'unknown';
        }
    }
}
