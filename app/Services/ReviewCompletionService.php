<?php

namespace App\Services;

use App\Models\Proposal;
use App\Models\RuangKontrol;
use App\Helpers\TahunAjaranHelper;
use Illuminate\Support\Facades\Log;

class ReviewCompletionService
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    public function getActiveRuangKontrol()
    {
        $tahunAjaranTerbaru = TahunAjaranHelper::getTahunAjaranTerbaru();
        
        $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
            ->where('is_active', true)
            ->first();
        
        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        return $ruangKontrol;
    }

    public function openRevisionPhase()
    {
        try {
            $ruangKontrol = $this->getActiveRuangKontrol();
            
            if ($ruangKontrol && $ruangKontrol->status_perbaikan !== 'terbuka') {
                $ruangKontrol->update([
                    'status_perbaikan' => 'terbuka',
                    'tanggal_perbaikan_mulai' => now(),
                    'tanggal_perbaikan_selesai' => now()->addDays(7)
                ]);
                
                Log::info('Revision phase opened automatically', [
                    'ruang_kontrol_id' => $ruangKontrol->id_ruang_kontrol,
                    'tanggal_perbaikan_mulai' => $ruangKontrol->tanggal_perbaikan_mulai,
                    'tanggal_perbaikan_selesai' => $ruangKontrol->tanggal_perbaikan_selesai
                ]);
                
                $this->notifyRevisionPhaseOpened();
            }
        } catch (\Exception $e) {
            Log::error('Error opening revision phase', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    public function notifyRevisionPhaseOpened()
    {
        try {
            $proposalsForRevision = Proposal::where('status', 'revisi')->get();
            
            foreach ($proposalsForRevision as $proposal) {
                $this->notificationService->notifyMahasiswa(
                    $proposal,
                    'revision_opened',
                    'Fase Revisi Proposal Dibuka',
                    "Proposal '{$proposal->judul_proposal}' telah selesai direview dan siap untuk direvisi. Silakan lakukan revisi sesuai catatan reviewer.",
                    [
                        'action_url' => route('mahasiswa.revisi.index'),
                        'deadline' => $this->getActiveRuangKontrol()?->tanggal_perbaikan_selesai ?? null
                    ]
                );
                
                $this->notificationService->notifyDosen(
                    $proposal,
                    'revision_opened',
                    'Fase Revisi Proposal Dibuka',
                    "Proposal '{$proposal->judul_proposal}' telah selesai direview dan mahasiswa dapat melakukan revisi.",
                    [
                        'action_url' => route('dosen.pembimbing.dashboard'),
                        'deadline' => $this->getActiveRuangKontrol()?->tanggal_perbaikan_selesai ?? null
                    ]
                );
            }
            
            Log::info('Revision phase notifications sent', [
                'proposals_count' => $proposalsForRevision->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending revision phase notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    public function notifyProposalReadyForRevision($proposal)
    {
        try {
            $ruangKontrol = $this->getActiveRuangKontrol();
            
            $this->notificationService->notifyMahasiswa(
                $proposal,
                'proposal_ready_revision',
                'Proposal Siap Direvisi',
                "Proposal '{$proposal->judul_proposal}' telah selesai direview oleh semua reviewer. Silakan lakukan revisi sesuai catatan reviewer yang diberikan.",
                [
                    'action_url' => route('mahasiswa.revisi.index'),
                    'deadline' => $ruangKontrol->tanggal_perbaikan_selesai ?? null,
                    'proposal_id' => $proposal->id_proposal
                ]
            );
            
            $this->notificationService->notifyDosen(
                $proposal,
                'proposal_ready_revision',
                'Proposal Siap Direvisi',
                "Proposal '{$proposal->judul_proposal}' telah selesai direview dan mahasiswa dapat melakukan revisi.",
                [
                    'action_url' => route('dosen.pembimbing.dashboard'),
                    'deadline' => $ruangKontrol->tanggal_perbaikan_selesai ?? null,
                    'proposal_id' => $proposal->id_proposal
                ]
            );
            
            Log::info('Proposal ready for revision notification sent', [
                'proposal_id' => $proposal->id_proposal
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending proposal ready for revision notification', [
                'proposal_id' => $proposal->id_proposal,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function isAdminReviewCompleted($proposal)
    {
        try {
            $adminReview = $proposal->nilaiAdministratif()
                ->where('id_reviewer', $proposal->id_reviewer_administratif)
                ->first();

            if (!$adminReview) {
                return false;
            }

            return !empty($adminReview->note_administratif) && 
                   $adminReview->note_administratif !== 'Review dimulai' &&
                   !empty($adminReview->checklist);

        } catch (\Exception $e) {
            Log::error('Error checking admin review completion', [
                'proposal_id' => $proposal->id_proposal,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function checkReviewCompletion($proposal)
    {
        try {
            $adminReviewer = $proposal->id_reviewer_administratif;
            $substantifReviewer1 = $proposal->id_reviewer_substantif_1;
            $substantifReviewer2 = $proposal->id_reviewer_substantif_2;
            
            $adminCompleted = false;
            if ($adminReviewer) {
                $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', $adminReviewer)->first();
                $adminCompleted = $adminReview && $adminReview->note_administratif && 
                                $adminReview->note_administratif !== 'Review administratif dimulai' &&
                                !empty($adminReview->checklist);
                
                if ($adminCompleted && $proposal->status === 'review_administratif') {
                    $proposal->update(['status' => 'review_substantif']);
                }
            }
            
            if ($adminReviewer && $substantifReviewer1 && $substantifReviewer2) {
                $substantifReview1 = $proposal->nilaiSubstantif->where('id_reviewer', $substantifReviewer1)->first();
                $substantifReview2 = $proposal->nilaiSubstantif->where('id_reviewer', $substantifReviewer2)->first();
                
                $substantif1Completed = $substantifReview1 && 
                                      !empty($substantifReview1->note_substantif) && 
                                      $substantifReview1->note_substantif !== 'Review substantif dimulai' &&
                                      !empty($substantifReview1->skor_per_kriteria) &&
                                      !is_null($substantifReview1->total_nilai) &&
                                      !is_null($substantifReview1->nilai_akhir);
                
                $substantif2Completed = $substantifReview2 && 
                                      !empty($substantifReview2->note_substantif) && 
                                      $substantifReview2->note_substantif !== 'Review substantif dimulai' &&
                                      !empty($substantifReview2->skor_per_kriteria) &&
                                      !is_null($substantifReview2->total_nilai) &&
                                      !is_null($substantifReview2->nilai_akhir);
                
                if ($adminCompleted && $substantif1Completed && $substantif2Completed) {
                    $proposal->update(['status' => 'revisi']);
                    
                    $this->openRevisionPhase();
                    
                    try {
                        $nilaiReviewer = [
                            'reviewer1' => $substantifReview1->nilai_akhir ?? null,
                            'reviewer2' => $substantifReview2->nilai_akhir ?? null
                        ];
                        $this->notificationService->notifyReviewSubstantifSelesai(
                            $proposal->fresh(),
                            $nilaiReviewer
                        );
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim notifikasi review substantif: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in checkReviewCompletion', [
                'proposal_id' => $proposal->id_proposal,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function updateProposalStatus($proposal)
    {
        try {
            $adminCompleted = $this->isAdminReviewCompleted($proposal);
            
            if ($adminCompleted && $proposal->status === 'review_administratif') {
                $proposal->update(['status' => 'review_substantif']);
                return 'review_substantif';
            }
            
            if ($proposal->status === 'review_substantif') {
                $substantifReviewer1 = $proposal->id_reviewer_substantif_1;
                $substantifReviewer2 = $proposal->id_reviewer_substantif_2;
                
                if ($substantifReviewer1 && $substantifReviewer2) {
                    $substantifReview1 = $proposal->nilaiSubstantif->where('id_reviewer', $substantifReviewer1)->first();
                    $substantifReview2 = $proposal->nilaiSubstantif->where('id_reviewer', $substantifReviewer2)->first();
                    
                    $substantif1Completed = $substantifReview1 && $substantifReview1->note_substantif && 
                                          $substantifReview1->note_substantif !== 'Review substantif dimulai';
                    $substantif2Completed = $substantifReview2 && $substantifReview2->note_substantif && 
                                          $substantifReview2->note_substantif !== 'Review substantif dimulai';
                    
                    if ($substantif1Completed && $substantif2Completed) {
                        $proposal->update(['status' => 'revisi']);
                        $this->openRevisionPhase();
                        $this->notifyProposalReadyForRevision($proposal);
                        return 'revisi';
                    }
                }
            }
            
            return $proposal->status;
        } catch (\Exception $e) {
            Log::error('Error updating proposal status', [
                'proposal_id' => $proposal->id_proposal,
                'error' => $e->getMessage()
            ]);
            return $proposal->status;
        }
    }
}
