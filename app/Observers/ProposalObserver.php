<?php

namespace App\Observers;

use App\Models\Proposal;
use App\Services\NotificationService;

class ProposalObserver
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Proposal "created" event.
     */
    public function created(Proposal $proposal): void
    {
        // Kirim notifikasi ke mahasiswa bahwa proposal berhasil dibuat
        $this->notificationService->notifyMahasiswa(
            $proposal,
            'info',
            'Proposal Dibuat',
            "Proposal '{$proposal->judul}' telah berhasil dibuat dan tersimpan dalam sistem.",
            [
                'action' => 'edit',
                'proposal_status' => 'draft'
            ]
        );

        // Kirim notifikasi ke dosen pembimbing jika proposal sudah diajukan
        if ($proposal->status === 'submitted' && $proposal->mahasiswa->dosenPembimbing) {
            $this->notificationService->notifyDosen(
                $proposal,
                'info',
                'Proposal Baru Dikirim',
                "Mahasiswa {$proposal->mahasiswa->nama_mhs} telah mengirim proposal baru: '{$proposal->judul}' untuk divalidasi.",
                [
                    'action' => 'review',
                    'proposal_status' => 'submitted',
                    'mahasiswa_nama' => $proposal->mahasiswa->nama_mhs
                ]
            );
        }
    }

    /**
     * Handle the Proposal "updated" event.
     */
    public function updated(Proposal $proposal): void
    {
        // Cek apakah status berubah
        if ($proposal->wasChanged('status')) {
            $oldStatus = $proposal->getOriginal('status');
            $newStatus = $proposal->status;
            
            // Kirim notifikasi perubahan status
            $this->notificationService->notifyProposalStatusChange($proposal, $oldStatus, $newStatus);
        }

        // Cek apakah status validasi berubah
        if ($proposal->wasChanged('status_validasi')) {
            $oldStatusValidasi = $proposal->getOriginal('status_validasi');
            $newStatusValidasi = $proposal->status_validasi;
            
            if ($newStatusValidasi === 'valid') {
                // Notifikasi ke mahasiswa bahwa proposal divalidasi
                $this->notificationService->notifyMahasiswa(
                    $proposal,
                    'success',
                    'Proposal Divalidasi',
                    "Proposal '{$proposal->judul}' telah divalidasi oleh dosen pendamping dan siap untuk review.",
                    [
                        'action' => 'view',
                        'validation_status' => 'valid'
                    ]
                );
            } elseif ($newStatusValidasi === 'tidak_valid') {
                // Notifikasi ke mahasiswa bahwa proposal tidak valid
                $this->notificationService->notifyMahasiswa(
                    $proposal,
                    'warning',
                    'Proposal Tidak Valid',
                    "Proposal '{$proposal->judul}' tidak valid dan perlu perbaikan sebelum dapat divalidasi.",
                    [
                        'action' => 'edit',
                        'validation_status' => 'tidak_valid'
                    ]
                );
            }
        }

        // Cek apakah reviewer ditugaskan
        if ($proposal->wasChanged('id_reviewer_administratif') || 
            $proposal->wasChanged('id_reviewer_substantif_1') || 
            $proposal->wasChanged('id_reviewer_substantif_2')) {
            
            // Notifikasi ke mahasiswa bahwa reviewer telah ditugaskan
            $this->notificationService->notifyMahasiswa(
                $proposal,
                'info',
                'Reviewer Ditugaskan',
                "Reviewer telah ditugaskan untuk proposal '{$proposal->judul}'. Proses review akan segera dimulai.",
                [
                    'action' => 'view',
                    'reviewer_assigned' => true
                ]
            );
        }

        // Cek apakah status final berubah
        if ($proposal->wasChanged('status_final')) {
            $oldStatusFinal = $proposal->getOriginal('status_final');
            $newStatusFinal = $proposal->status_final;
            
            if ($newStatusFinal === 'lolos') {
                // Notifikasi ke mahasiswa bahwa proposal lolos
                $this->notificationService->notifyMahasiswa(
                    $proposal,
                    'success',
                    'Proposal Lolos!',
                    "Selamat! Proposal '{$proposal->judul}' telah lolos review dan disetujui untuk didanai.",
                    [
                        'action' => 'view',
                        'final_status' => 'lolos',
                        'funding_approved' => true
                    ]
                );
            } elseif ($newStatusFinal === 'tidak_lolos') {
                // Notifikasi ke mahasiswa bahwa proposal tidak lolos
                $this->notificationService->notifyMahasiswa(
                    $proposal,
                    'danger',
                    'Proposal Tidak Lolos',
                    "Mohon maaf, proposal '{$proposal->judul}' tidak lolos review final.",
                    [
                        'action' => 'view',
                        'final_status' => 'tidak_lolos'
                    ]
                );
            }
        }
    }

    /**
     * Handle the Proposal "deleted" event.
     */
    public function deleted(Proposal $proposal): void
    {
        // Notifikasi ke mahasiswa bahwa proposal dihapus
        $this->notificationService->notifyMahasiswa(
            $proposal,
            'warning',
            'Proposal Dihapus',
            "Proposal '{$proposal->judul}' telah dihapus dari sistem.",
            [
                'action' => 'create_new',
                'deletion_reason' => 'user_request'
            ]
        );
    }

    /**
     * Handle the Proposal "restored" event.
     */
    public function restored(Proposal $proposal): void
    {
        // Notifikasi ke mahasiswa bahwa proposal dipulihkan
        $this->notificationService->notifyMahasiswa(
            $proposal,
            'info',
            'Proposal Dipulihkan',
            "Proposal '{$proposal->judul}' telah dipulihkan dan tersedia kembali dalam sistem.",
            [
                'action' => 'view',
                'restored' => true
            ]
        );
    }

    /**
     * Handle the Proposal "force deleted" event.
     */
    public function forceDeleted(Proposal $proposal): void
    {
        // Notifikasi ke mahasiswa bahwa proposal dihapus permanen
        $this->notificationService->notifyMahasiswa(
            $proposal,
            'danger',
            'Proposal Dihapus Permanen',
            "Proposal '{$proposal->judul}' telah dihapus permanen dari sistem dan tidak dapat dipulihkan.",
            [
                'action' => 'create_new',
                'permanently_deleted' => true
            ]
        );
    }
}
