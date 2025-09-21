<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Proposal;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Kirim notifikasi ke mahasiswa terkait proposal
     */
    public function notifyMahasiswa(Proposal $proposal, string $type, string $title, string $message, array $data = []): void
    {
        try {
            // Dapatkan semua anggota tim proposal
            $teamMembers = $proposal->allMembers;
            
            foreach ($teamMembers as $member) {
                Notification::create([
                    'user_identifier' => $member->nim,
                    'user_type' => 'mahasiswa',
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'data' => array_merge($data, [
                        'proposal_id' => $proposal->id_proposal,
                        'nim' => $member->nim,
                        'role' => $member->role
                    ]),
                    'proposal_id' => $proposal->id_proposal,
                ]);
            }
            
            Log::info("Notifikasi berhasil dikirim ke mahasiswa untuk proposal {$proposal->id_proposal}");
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi ke mahasiswa: " . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi ke dosen pendamping
     */
    public function notifyDosen(Proposal $proposal, string $type, string $title, string $message, array $data = []): void
    {
        try {
            if ($proposal->id_dosen) {
                $dosen = $proposal->dosen;
                if ($dosen) {
                    Notification::create([
                        'user_identifier' => $dosen->nidn,
                        'user_type' => 'dosen',
                        'title' => $title,
                        'message' => $message,
                        'type' => $type,
                        'data' => array_merge($data, [
                            'proposal_id' => $proposal->id_proposal,
                            'mahasiswa_nama' => $proposal->mahasiswa->nama ?? 'N/A'
                        ]),
                        'proposal_id' => $proposal->id_proposal,
                    ]);
                }
            }
            
            Log::info("Notifikasi berhasil dikirim ke dosen untuk proposal {$proposal->id_proposal}");
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi ke dosen: " . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi ke reviewer
     */
    public function notifyReviewer(Proposal $proposal, string $type, string $title, string $message, array $data = []): void
    {
        try {
            $reviewers = [];
            
            // Reviewer administratif
            if ($proposal->id_reviewer_administratif) {
                $reviewers[] = $proposal->reviewerAdministratif;
            }
            
            // Reviewer substantif 1
            if ($proposal->id_reviewer_substantif_1) {
                $reviewers[] = $proposal->reviewerSubstantif1;
            }
            
            // Reviewer substantif 2
            if ($proposal->id_reviewer_substantif_2) {
                $reviewers[] = $proposal->reviewerSubstantif2;
            }
            
            foreach ($reviewers as $reviewer) {
                if ($reviewer) {
                    Notification::create([
                        'user_identifier' => $reviewer->id_reviewer,
                        'user_type' => 'reviewer',
                        'title' => $title,
                        'message' => $message,
                        'type' => $type,
                        'data' => array_merge($data, [
                            'proposal_id' => $proposal->id_proposal,
                            'review_type' => $this->getReviewType($proposal, $reviewer->id_reviewer)
                        ]),
                        'proposal_id' => $proposal->id_proposal,
                    ]);
                }
            }
            
            Log::info("Notifikasi berhasil dikirim ke reviewer untuk proposal {$proposal->id_proposal}");
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi ke reviewer: " . $e->getMessage());
        }
    }

    /**
     * Kirim notifikasi ke operator
     */
    public function notifyOperator(string $type, string $title, string $message, array $data = []): void
    {
        try {
            // Dapatkan semua operator (menggunakan model PT)
            $operators = \App\Models\PT::all();
            
            foreach ($operators as $operator) {
                Notification::create([
                    'user_identifier' => $operator->id_pt,
                    'user_type' => 'operator',
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'data' => $data,
                ]);
            }
            
            Log::info("Notifikasi berhasil dikirim ke operator");
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi ke operator: " . $e->getMessage());
        }
    }

    /**
     * Notifikasi perubahan status proposal
     */
    public function notifyProposalStatusChange(Proposal $proposal, string $oldStatus, string $newStatus): void
    {
        $statusMessages = [
            'submitted' => [
                'title' => 'Proposal Dikirim',
                'message' => "Proposal '{$proposal->judul}' telah berhasil dikirim dan sedang dalam proses review.",
                'type' => 'info'
            ],
            'valid' => [
                'title' => 'Proposal Divalidasi',
                'message' => "Proposal '{$proposal->judul}' telah divalidasi oleh dosen pendamping.",
                'type' => 'success'
            ],
            'tidak_valid' => [
                'title' => 'Proposal Tidak Valid',
                'message' => "Proposal '{$proposal->judul}' tidak valid dan perlu perbaikan.",
                'type' => 'warning'
            ],
            'review_administratif' => [
                'title' => 'Review Administratif Dimulai',
                'message' => "Proposal '{$proposal->judul}' sedang dalam proses review administratif.",
                'type' => 'info'
            ],
            'review_substantif' => [
                'title' => 'Review Substantif Dimulai',
                'message' => "Proposal '{$proposal->judul}' sedang dalam proses review substantif.",
                'type' => 'info'
            ],
            'revisi' => [
                'title' => 'Proposal Perlu Revisi',
                'message' => "Proposal '{$proposal->judul}' memerlukan revisi. Silakan periksa detail revisi.",
                'type' => 'warning'
            ],
            'lolos' => [
                'title' => 'Proposal Lolos',
                'message' => "Selamat! Proposal '{$proposal->judul}' telah lolos review dan disetujui.",
                'type' => 'success'
            ],
            'tidak_lolos' => [
                'title' => 'Proposal Tidak Lolos',
                'message' => "Mohon maaf, proposal '{$proposal->judul}' tidak lolos review.",
                'type' => 'danger'
            ]
        ];

        if (isset($statusMessages[$newStatus])) {
            $statusInfo = $statusMessages[$newStatus];
            
            // Notifikasi ke mahasiswa
            $this->notifyMahasiswa(
                $proposal,
                $statusInfo['type'],
                $statusInfo['title'],
                $statusInfo['message'],
                [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'status_change_time' => now()->toISOString()
                ]
            );

            // Notifikasi ke dosen jika status berubah menjadi valid/tidak valid
            if (in_array($newStatus, ['valid', 'tidak_valid'])) {
                $this->notifyDosen(
                    $proposal,
                    $statusInfo['type'],
                    $statusInfo['title'],
                    $statusInfo['message'],
                    [
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'status_change_time' => now()->toISOString()
                    ]
                );
            }

            // Notifikasi ke operator untuk status tertentu
            if (in_array($newStatus, ['submitted', 'review_administratif', 'review_substantif'])) {
                $this->notifyOperator(
                    $statusInfo['type'],
                    $statusInfo['title'],
                    $statusInfo['message'],
                    [
                        'proposal_id' => $proposal->id_proposal,
                        'old_status' => $oldStatus,
                        'new_status' => $newStatus,
                        'status_change_time' => now()->toISOString()
                    ]
                );
            }
        }
    }

    /**
     * Notifikasi review selesai
     */
    public function notifyReviewComplete(Proposal $proposal, string $reviewType, string $result): void
    {
        $type = $result === 'lolos' ? 'success' : 'warning';
        $title = "Review {$reviewType} Selesai";
        $message = "Review {$reviewType} untuk proposal '{$proposal->judul}' telah selesai dengan hasil: {$result}.";

        // Notifikasi ke mahasiswa
        $this->notifyMahasiswa(
            $proposal,
            $type,
            $title,
            $message,
            [
                'review_type' => $reviewType,
                'review_result' => $result,
                'review_completion_time' => now()->toISOString()
            ]
        );

        // Notifikasi ke operator
        $this->notifyOperator(
            $type,
            $title,
            $message,
            [
                'proposal_id' => $proposal->id_proposal,
                'review_type' => $reviewType,
                'review_result' => $result,
                'review_completion_time' => now()->toISOString()
            ]
        );
    }

    /**
     * Dapatkan tipe review berdasarkan reviewer
     */
    private function getReviewType(Proposal $proposal, int $reviewerId): string
    {
        if ($proposal->id_reviewer_administratif === $reviewerId) {
            return 'administratif';
        } elseif ($proposal->id_reviewer_substantif_1 === $reviewerId) {
            return 'substantif_1';
        } elseif ($proposal->id_reviewer_substantif_2 === $reviewerId) {
            return 'substantif_2';
        }
        
        return 'unknown';
    }

    /**
     * Hapus notifikasi lama (lebih dari 30 hari)
     */
    public function cleanupOldNotifications(): int
    {
        $deletedCount = Notification::where('created_at', '<', now()->subDays(30))->delete();
        Log::info("Berhasil menghapus {$deletedCount} notifikasi lama");
        return $deletedCount;
    }

    /**
     * Notifikasi hasil final proposal
     */
    public function notifyHasilFinal(Proposal $proposal, string $statusFinal, float $nilai, string $catatanFinal = null): void
    {
        $type = $statusFinal === 'lolos' ? 'success' : 'danger';
        $title = $statusFinal === 'lolos' ? 'Proposal Lolos Final' : 'Proposal Tidak Lolos Final';
        $message = $statusFinal === 'lolos' 
            ? "Selamat! Proposal '{$proposal->judul_proposal}' telah lolos penilaian final dengan nilai {$nilai}."
            : "Mohon maaf, proposal '{$proposal->judul_proposal}' tidak lolos penilaian final dengan nilai {$nilai}.";

        if ($catatanFinal) {
            $message .= " Catatan: {$catatanFinal}";
        }

        // Notifikasi ke mahasiswa
        $this->notifyMahasiswa(
            $proposal,
            $type,
            $title,
            $message,
            [
                'status_final' => $statusFinal,
                'nilai' => $nilai,
                'catatan_final' => $catatanFinal,
                'hasil_final_time' => now()->toISOString()
            ]
        );

        // Notifikasi ke dosen pendamping
        $this->notifyDosen(
            $proposal,
            $type,
            $title,
            $message,
            [
                'status_final' => $statusFinal,
                'nilai' => $nilai,
                'catatan_final' => $catatanFinal,
                'hasil_final_time' => now()->toISOString()
            ]
        );

        Log::info("Notifikasi hasil final berhasil dikirim untuk proposal {$proposal->id_proposal}");
    }

    /**
     * Dapatkan jumlah notifikasi yang belum dibaca untuk user tertentu
     */
    public function getUnreadCount(string $userIdentifier, string $userType): int
    {
        return Notification::where('user_identifier', $userIdentifier)
                          ->where('user_type', $userType)
                          ->whereNull('read_at')
                          ->count();
    }
}
