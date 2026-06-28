<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct() {
    }

    /**
     * Kirim notifikasi ke mahasiswa terkait proposal
     */
    public function notifyMahasiswa(Proposal $proposal, string $type, string $title, string $message, array $data = []): void
    {
        defer(function () use ($proposal, $type, $title, $message, $data) {
            try {
                // Dapatkan semua anggota tim proposal (termasuk ketua)
                $teamMembers = $proposal->semuaAnggotaTim;
                
                // Jika tidak ada anggota tim, gunakan mahasiswa pengaju
                if ($teamMembers->isEmpty() && $proposal->mahasiswa) {
                    $teamMembers = collect([$proposal->mahasiswa]);
                }
                
                foreach ($teamMembers as $member) {
                    $payload = [
                        'user_identifier' => $member->identifier,
                        'user_type' => 'mahasiswa',
                        'title' => $title,
                        'message' => $message,
                        'type' => $type,
                        'data' => array_merge($data, [
                            'proposal_id' => $proposal->id_proposal,
                            'nim' => $member->identifier,
                            'judul_proposal' => $proposal->judul_proposal ?? $proposal->judul
                        ]),
                        'proposal_id' => $proposal->id_proposal,
                    ];
                    Notification::create($payload);
                }
                
                Log::info("Notifikasi berhasil dikirim ke mahasiswa untuk proposal {$proposal->id_proposal}");
            } catch (\Exception $e) {
                Log::error("Gagal mengirim notifikasi ke mahasiswa: " . $e->getMessage());
            }
        });
    }

    /**
     * Kirim notifikasi ke dosen pendamping
     */
    public function notifyDosen(Proposal $proposal, string $type, string $title, string $message, array $data = []): void
    {
        defer(function () use ($proposal, $type, $title, $message, $data) {
            try {
                if ($proposal->id_dosen) {
                    $dosen = $proposal->dosen;
                    if ($dosen) {
                        $payload = [
                            'user_identifier' => $dosen->identifier,
                            'user_type' => 'dosen',
                            'title' => $title,
                            'message' => $message,
                            'type' => $type,
                            'data' => array_merge($data, [
                                'proposal_id' => $proposal->id_proposal,
                                'mahasiswa_nama' => $proposal->mahasiswa->name ?? 'N/A'
                            ]),
                            'proposal_id' => $proposal->id_proposal,
                        ];
                        Notification::create($payload);
                    }
                }
                
                Log::info("Notifikasi berhasil dikirim ke dosen untuk proposal {$proposal->id_proposal}");
            } catch (\Exception $e) {
                Log::error("Gagal mengirim notifikasi ke dosen: " . $e->getMessage());
            }
        });
    }

    /**
     * Kirim notifikasi ke reviewer
     */
    public function notifyReviewer(Proposal $proposal, string $type, string $title, string $message, array $data = []): void
    {
        defer(function () use ($proposal, $type, $title, $message, $data) {
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
                        $payload = [
                            'user_identifier' => $reviewer->identifier,
                            'user_type' => 'reviewer',
                            'title' => $title,
                            'message' => $message,
                            'type' => $type,
                            'data' => array_merge($data, [
                                'proposal_id' => $proposal->id_proposal,
                                'review_type' => $this->getReviewType($proposal, $reviewer->id)
                            ]),
                            'proposal_id' => $proposal->id_proposal,
                        ];
                        Notification::create($payload);
                    }
                }
                
                Log::info("Notifikasi berhasil dikirim ke reviewer untuk proposal {$proposal->id_proposal}");
            } catch (\Exception $e) {
                Log::error("Gagal mengirim notifikasi ke reviewer: " . $e->getMessage());
            }
        });
    }

    /**
     * Kirim notifikasi ke operator
     */
    public function notifyOperator(string $type, string $title, string $message, array $data = []): void
    {
        defer(function () use ($type, $title, $message, $data) {
            try {
                $operators = User::whereIn('role', ['operator', 'pimpinan_pt'])
                    ->where('is_active', true)
                    ->get();

                foreach ($operators as $operator) {
                    $payload = [
                        'user_identifier' => $operator->identifier,
                        'user_type' => 'operator',
                        'title' => $title,
                        'message' => $message,
                        'type' => $type,
                        'data' => $data,
                    ];
                    Notification::create($payload);
                }
                
                Log::info("Notifikasi berhasil dikirim ke operator");
            } catch (\Exception $e) {
                Log::error("Gagal mengirim notifikasi ke operator: " . $e->getMessage());
            }
        });
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

    /**
     * Notifikasi ruang kontrol dibuka (pendaftaran proposal dibuka)
     */
    public function notifyRuangKontrolDibuka(\App\Models\RuangKontrol $ruangKontrol): void
    {
        try {
            $tahunAjaran = $ruangKontrol->tahun_ajaran;
            $tanggalMulai = \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_mulai)->format('d M Y');
            $tanggalSelesai = \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_selesai)->format('d M Y');
            
            $mahasiswas = User::where('role', 'mahasiswa')->where('is_active', true)->get();

            foreach ($mahasiswas as $mahasiswa) {
                $payload = [
                    'user_identifier' => $mahasiswa->identifier,
                    'user_type' => 'mahasiswa',
                    'title' => 'Pendaftaran Proposal PKM Dibuka',
                    'message' => "Pendaftaran proposal PKM untuk tahun ajaran {$tahunAjaran} telah dibuka. Periode: {$tanggalMulai} - {$tanggalSelesai}. Segera ajukan proposal Anda!",
                    'type' => 'success',
                    'data' => [
                        'ruang_kontrol_id' => $ruangKontrol->id_ruang_kontrol,
                        'tahun_ajaran' => $tahunAjaran,
                        'tanggal_mulai' => $ruangKontrol->tanggal_pendaftaran_mulai,
                        'tanggal_selesai' => $ruangKontrol->tanggal_pendaftaran_selesai,
                    ],
                ];
                Notification::create($payload);
            }
            
            Log::info("Notifikasi ruang kontrol dibuka berhasil dikirim ke semua mahasiswa");
        } catch (\Exception $e) {
            Log::error("Gagal mengirim notifikasi ruang kontrol dibuka: " . $e->getMessage());
        }
    }

    /**
     * Notifikasi proposal berhasil diupload
     */
    public function notifyProposalUploaded(Proposal $proposal): void
    {
        $judul = $proposal->judul_proposal ?? $proposal->judul;
        $skim = $proposal->skim;
        
        $this->notifyMahasiswa(
            $proposal,
            'success',
            'Proposal Berhasil Dikirim',
            "Proposal Anda '{$judul}' (Skim: {$skim}) telah berhasil dikirim dan sedang menunggu validasi dari dosen pendamping.",
            [
                'action' => 'view_proposal',
                'upload_time' => now()->toISOString()
            ]
        );
    }

    /**
     * Notifikasi validasi dosen pendamping
     */
    public function notifyValidasiDosen(Proposal $proposal, string $status, string $catatan = null): void
    {
        $judul = $proposal->judul_proposal ?? $proposal->judul;
        
        if ($status === 'valid') {
            $this->notifyMahasiswa(
                $proposal,
                'success',
                'Proposal Divalidasi - Siap Review',
                "Proposal Anda '{$judul}' telah divalidasi oleh dosen pendamping dan siap untuk proses review.",
                [
                    'status_validasi' => 'valid',
                    'catatan' => $catatan,
                    'action' => 'view_proposal'
                ]
            );
        } else {
            // Status tidak valid - notifikasi negatif dengan pesan yang jelas
            $message = "Proposal Anda '{$judul}' tidak dapat divalidasi oleh dosen pendamping.";
            if ($catatan) {
                $message .= " Catatan: {$catatan}";
            }
            $message .= " Silakan perbaiki proposal Anda dan kirim ulang.";
            
            $this->notifyMahasiswa(
                $proposal,
                'danger',
                'Proposal Tidak Valid - Perlu Perbaikan',
                $message,
                [
                    'status_validasi' => 'tidak_valid',
                    'catatan' => $catatan,
                    'action' => 'revisi_proposal',
                    'is_negative' => true
                ]
            );
        }
    }

    /**
     * Notifikasi reviewer di-assign
     */
    public function notifyReviewerAssigned(Proposal $proposal): void
    {
        $judul = $proposal->judul_proposal ?? $proposal->judul;
        
        $this->notifyMahasiswa(
            $proposal,
            'info',
            'Review Proposal Dimulai',
            "Proposal Anda '{$judul}' telah ditugaskan kepada reviewer dan proses review administratif telah dimulai.",
            [
                'action' => 'view_proposal',
                'review_stage' => 'administratif'
            ]
        );
    }

    /**
     * Notifikasi review administratif selesai
     */
    public function notifyReviewAdministratifSelesai(Proposal $proposal, bool $lolos = true, string $catatan = null): void
    {
        $judul = $proposal->judul_proposal ?? $proposal->judul;
        
        if ($lolos) {
            $this->notifyMahasiswa(
                $proposal,
                'success',
                'Review Administratif Selesai - Lolos',
                "Proposal Anda '{$judul}' telah lolos review administratif dan akan dilanjutkan ke review substantif.",
                [
                    'review_type' => 'administratif',
                    'result' => 'lolos',
                    'action' => 'view_proposal'
                ]
            );
        } else {
            $message = "Proposal Anda '{$judul}' tidak lolos review administratif.";
            if ($catatan) {
                $message .= " Catatan reviewer: {$catatan}";
            }
            $message .= " Silakan perbaiki proposal Anda sesuai catatan reviewer.";
            
            $this->notifyMahasiswa(
                $proposal,
                'danger',
                'Review Administratif - Tidak Lolos',
                $message,
                [
                    'review_type' => 'administratif',
                    'result' => 'tidak_lolos',
                    'catatan' => $catatan,
                    'action' => 'revisi_proposal',
                    'is_negative' => true
                ]
            );
        }
    }

    /**
     * Notifikasi review substantif selesai - perlu revisi
     */
    public function notifyReviewSubstantifSelesai(Proposal $proposal, array $nilaiReviewer = []): void
    {
        $judul = $proposal->judul_proposal ?? $proposal->judul;
        $nilai1 = $nilaiReviewer['reviewer1'] ?? null;
        $nilai2 = $nilaiReviewer['reviewer2'] ?? null;
        
        $message = "Review substantif untuk proposal Anda '{$judul}' telah selesai.";
        if ($nilai1 !== null) {
            $message .= " Nilai Reviewer 1: " . number_format($nilai1, 2, ',', '.');
        }
        if ($nilai2 !== null) {
            $message .= " Nilai Reviewer 2: " . number_format($nilai2, 2, ',', '.');
        }
        $message .= " Proposal Anda memerlukan revisi. Silakan periksa catatan reviewer dan upload file revisi.";
        
        $this->notifyMahasiswa(
            $proposal,
            'warning',
            'Review Substantif Selesai - Perlu Revisi',
            $message,
            [
                'review_type' => 'substantif',
                'nilai_reviewer1' => $nilai1,
                'nilai_reviewer2' => $nilai2,
                'action' => 'upload_revisi'
            ]
        );
    }

    /**
     * Notifikasi hasil semi final
     */
    public function notifyHasilSemiFinal(Proposal $proposal, string $status, float $nilai = null, string $catatan = null, float $dana = null): void
    {
        $judul = $proposal->judul_proposal ?? $proposal->judul;
        
        if ($status === 'lolos_tingkat_universitas') {
            $message = "Selamat! Proposal Anda '{$judul}' telah lolos penilaian semi final tingkat universitas.";
            if ($nilai !== null) {
                $message .= " Nilai: " . number_format($nilai, 2, ',', '.');
            }
            if ($dana !== null && $dana > 0) {
                $message .= " Dana yang dapat diberikan: Rp " . number_format($dana, 0, ',', '.');
            }
            $message .= " Silakan upload revisi akhir proposal Anda.";
            
            $this->notifyMahasiswa(
                $proposal,
                'success',
                'Lolos Semi Final - Upload Revisi Akhir',
                $message,
                [
                    'status_semi_final' => 'lolos_tingkat_universitas',
                    'nilai' => $nilai,
                    'dana_yang_dapat_diberikan' => $dana,
                    'catatan' => $catatan,
                    'action' => 'upload_revisi_akhir'
                ]
            );
        } else {
            // Tidak lolos - notifikasi negatif dengan pesan yang jelas
            $message = "Mohon maaf, proposal Anda '{$judul}' tidak lolos penilaian semi final tingkat universitas.";
            if ($nilai !== null) {
                $message .= " Nilai: " . number_format($nilai, 2, ',', '.');
            }
            if ($catatan) {
                $message .= " Catatan: {$catatan}";
            }
            $message .= " Terima kasih atas partisipasi Anda.";
            
            $this->notifyMahasiswa(
                $proposal,
                'danger',
                'Tidak Lolos Semi Final',
                $message,
                [
                    'status_semi_final' => 'tidak_lolos_tingkat_universitas',
                    'nilai' => $nilai,
                    'catatan' => $catatan,
                    'action' => 'view_proposal',
                    'is_negative' => true
                ]
            );
        }
    }

    /**
     * Notifikasi revisi akhir divalidasi dosen universitas
     */
    public function notifyValidasiAkhirDosen(Proposal $proposal, string $status, string $catatan = null): void
    {
        $judul = $proposal->judul_proposal ?? $proposal->judul;
        
        if ($status === 'valid') {
            $this->notifyMahasiswa(
                $proposal,
                'success',
                'Revisi Akhir Divalidasi',
                "Revisi akhir proposal Anda '{$judul}' telah divalidasi oleh dosen pendamping universitas dan siap untuk penilaian final oleh Pimpinan PT.",
                [
                    'status_validasi_akhir' => 'valid',
                    'action' => 'view_proposal'
                ]
            );
        } else {
            $message = "Revisi akhir proposal Anda '{$judul}' tidak dapat divalidasi oleh dosen pendamping universitas.";
            if ($catatan) {
                $message .= " Catatan: {$catatan}";
            }
            $message .= " Silakan perbaiki dan upload ulang revisi akhir.";
            
            $this->notifyMahasiswa(
                $proposal,
                'danger',
                'Revisi Akhir Tidak Valid',
                $message,
                [
                    'status_validasi_akhir' => 'tidak_valid',
                    'catatan' => $catatan,
                    'action' => 'upload_revisi_akhir',
                    'is_negative' => true
                ]
            );
        }
    }

    /**
     * Notifikasi hasil final lengkap dengan status PIMNAS dan pendanaan
     */
    public function notifyHasilFinalLengkap(Proposal $proposal, string $statusPimnas, string $statusPendanaan, float $nilai, float $danaYangDidapatkan = 0, string $catatan = null): void
    {
        $judul = $proposal->judul_proposal ?? $proposal->judul;
        $isLolosPimnas = $statusPimnas === 'lolos';
        $isLolosPendanaan = $statusPendanaan === 'lolos';
        
        // Tentukan tipe notifikasi
        if ($isLolosPimnas && $isLolosPendanaan) {
            // Lolos keduanya - sukses besar
            $type = 'success';
            $title = 'Selamat! Proposal Lolos PIMNAS dan Mendapat Pendanaan';
            $message = "Selamat! Proposal Anda '{$judul}' telah lolos PIMNAS dan mendapatkan pendanaan.";
            $message .= " Nilai: " . number_format($nilai, 2, ',', '.');
            if ($danaYangDidapatkan > 0) {
                $message .= " Dana yang didapatkan: Rp " . number_format($danaYangDidapatkan, 0, ',', '.');
            }
        } elseif ($isLolosPimnas && !$isLolosPendanaan) {
            // Lolos PIMNAS tapi tidak pendanaan
            $type = 'success';
            $title = 'Selamat! Proposal Lolos PIMNAS';
            $message = "Selamat! Proposal Anda '{$judul}' telah lolos PIMNAS, namun tidak mendapatkan pendanaan.";
            $message .= " Nilai: " . number_format($nilai, 2, ',', '.');
        } elseif (!$isLolosPimnas && $isLolosPendanaan) {
            // Tidak lolos PIMNAS tapi dapat pendanaan
            $type = 'warning';
            $title = 'Proposal Mendapat Pendanaan';
            $message = "Proposal Anda '{$judul}' tidak lolos PIMNAS, namun mendapatkan pendanaan.";
            $message .= " Nilai: " . number_format($nilai, 2, ',', '.');
            if ($danaYangDidapatkan > 0) {
                $message .= " Dana yang didapatkan: Rp " . number_format($danaYangDidapatkan, 0, ',', '.');
            }
        } else {
            // Tidak lolos keduanya - notifikasi negatif
            $type = 'danger';
            $title = 'Proposal Tidak Lolos Final';
            $message = "Mohon maaf, proposal Anda '{$judul}' tidak lolos PIMNAS dan tidak mendapatkan pendanaan.";
            $message .= " Nilai: " . number_format($nilai, 2, ',', '.');
        }
        
        if ($catatan) {
            $message .= " Catatan: {$catatan}";
        }
        
        $this->notifyMahasiswa(
            $proposal,
            $type,
            $title,
            $message,
            [
                'status_pimnas' => $statusPimnas,
                'status_pendanaan' => $statusPendanaan,
                'nilai' => $nilai,
                'dana_yang_didapatkan' => $danaYangDidapatkan,
                'catatan' => $catatan,
                'action' => 'view_hasil_final',
                'is_negative' => (!$isLolosPimnas && !$isLolosPendanaan)
            ]
        );
    }

    /**
     * Notifikasi deadline reminder
     */
    public function notifyDeadlineReminder(Proposal $proposal, string $deadlineType, \Carbon\Carbon $deadline): void
    {
        $judul = $proposal->judul_proposal ?? $proposal->judul;
        $daysLeft = now()->diffInDays($deadline, false);
        
        if ($daysLeft < 0) {
            return; // Deadline sudah lewat
        }
        
        $message = "Pengingat: ";
        if ($deadlineType === 'upload_revisi') {
            $message .= "Deadline upload revisi proposal '{$judul}' adalah " . $deadline->format('d M Y H:i');
        } elseif ($deadlineType === 'upload_revisi_akhir') {
            $message .= "Deadline upload revisi akhir proposal '{$judul}' adalah " . $deadline->format('d M Y H:i');
        } else {
            $message .= "Deadline untuk proposal '{$judul}' adalah " . $deadline->format('d M Y H:i');
        }
        
        if ($daysLeft === 0) {
            $message .= " (Hari ini!)";
        } elseif ($daysLeft === 1) {
            $message .= " (Besok!)";
        } else {
            $message .= " ({$daysLeft} hari lagi)";
        }
        
        $type = $daysLeft <= 1 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'info');
        
        $this->notifyMahasiswa(
            $proposal,
            $type,
            'Pengingat Deadline',
            $message,
            [
                'deadline_type' => $deadlineType,
                'deadline' => $deadline->toISOString(),
                'days_left' => $daysLeft,
                'action' => $deadlineType === 'upload_revisi' ? 'upload_revisi' : 'upload_revisi_akhir'
            ]
        );
    }

    /**
     * Notifikasi untuk semua mahasiswa (broadcast)
     */
    public function notifyAllMahasiswa(string $type, string $title, string $message, array $data = []): void
    {
        defer(function () use ($type, $title, $message, $data) {
            try {
                $mahasiswas = User::where('role', 'mahasiswa')->where('is_active', true)->get();

                foreach ($mahasiswas as $mahasiswa) {
                    $payload = [
                        'user_identifier' => $mahasiswa->identifier,
                        'user_type' => 'mahasiswa',
                        'title' => $title,
                        'message' => $message,
                        'type' => $type,
                        'data' => $data,
                    ];
                    Notification::create($payload);
                    $this->writeToFirestore($payload);
                }
                
                Log::info("Notifikasi broadcast berhasil dikirim ke semua mahasiswa");
            } catch (\Exception $e) {
                Log::error("Gagal mengirim notifikasi broadcast: " . $e->getMessage());
            }
        });
    }

    /**
     * Tulis notifikasi ke Firestore jika terkonfigurasi (opsional)
     */
    protected function writeToFirestore(array $payload): void
    {
        // Fitur Firestore bersifat opsional, saat ini dinonaktifkan
    }
}
