<?php

namespace App\Services;

use App\Models\Proposal;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewerAssignmentService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Assign reviewer administratif dan substantif 1 & 2 ke proposal
     */
    public function assignReviewers(array $data)
    {
        try {
            DB::beginTransaction();
            
            $proposal = Proposal::findOrFail($data['proposal_id']);
            
            Log::info('Updating proposal', [
                'proposal_id' => $proposal->id_proposal,
                'current_status' => $proposal->status,
                'current_status_validasi' => $proposal->status_validasi,
                'new_status' => 'review_administratif'
            ]);
            
            // Update proposal dengan reviewer assignment
            $updateData = [
                'status' => 'review_administratif',
                'id_reviewer_administratif' => $data['reviewer_administratif'],
                'id_reviewer_substantif_1' => $data['reviewer_substantif_1'],
                'id_reviewer_substantif_2' => $data['reviewer_substantif_2']
            ];
            
            $proposal->update($updateData);
            
            Log::info('Proposal updated successfully', [
                'proposal_id' => $proposal->id_proposal,
                'new_status' => $proposal->status,
                'reviewers_assigned' => [
                    'administratif' => $proposal->id_reviewer_administratif,
                    'substantif_1' => $proposal->id_reviewer_substantif_1,
                    'substantif_2' => $proposal->id_reviewer_substantif_2
                ]
            ]);
            
            // Hapus assignment lama jika ada
            NilaiAdministratif::where('id_proposal', $data['proposal_id'])->delete();
            NilaiSubstantif::where('id_proposal', $data['proposal_id'])->delete();
            
            Log::info('Old review records deleted', [
                'proposal_id' => $data['proposal_id']
            ]);
            
            // Buat assignment baru di tabel nilai
            $nilaiAdmin = NilaiAdministratif::create([
                'id_proposal' => $data['proposal_id'],
                'id_reviewer' => $data['reviewer_administratif'],
                'note_administratif' => null, // Tidak ada note default, reviewer harus mengisi
                'checklist' => json_encode([])
            ]);
            
            $nilaiSub1 = NilaiSubstantif::create([
                'id_proposal' => $data['proposal_id'],
                'id_reviewer' => $data['reviewer_substantif_1'],
                'note_substantif' => null // Tidak ada note default, reviewer harus mengisi
            ]);
            
            $nilaiSub2 = NilaiSubstantif::create([
                'id_proposal' => $data['proposal_id'],
                'id_reviewer' => $data['reviewer_substantif_2'],
                'note_substantif' => null // Tidak ada note default, reviewer harus mengisi
            ]);
            
            Log::info('Created review records', [
                'nilai_admin_id' => $nilaiAdmin->id,
                'nilai_sub1_id' => $nilaiSub1->id,
                'nilai_sub2_id' => $nilaiSub2->id
            ]);
            
            DB::commit();
            
            // Kirim notifikasi ke mahasiswa
            try {
                $this->notificationService->notifyReviewerAssigned($proposal);
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi assign reviewer: ' . $e->getMessage());
            }
            
            Log::info('Reviewer assignment completed successfully', [
                'proposal_id' => $data['proposal_id'],
                'reviewers' => [
                    'administratif' => $data['reviewer_administratif'],
                    'substantif_1' => $data['reviewer_substantif_1'],
                    'substantif_2' => $data['reviewer_substantif_2']
                ]
            ]);
            
            return [
                'success' => true,
                'proposal_id' => $data['proposal_id'],
                'status' => 'review_administratif'
            ];
            
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Assign satu reviewer seleksi (substantif_seleksi_1 atau substantif_seleksi_2).
     * Jika kedua reviewer seleksi sudah di-assign, status proposal diupdate ke review_substantif_seleksi.
     */
    public function assignReviewerSeleksi(array $data)
    {
        try {
            DB::beginTransaction();

            $proposal = Proposal::findOrFail($data['proposal_id']);
            $reviewerId = (int) $data['reviewer_id'];
            $reviewType = $data['review_type'];

            if ($reviewType === 'substantif_seleksi_1') {
                if ($proposal->id_reviewer_substantif_seleksi_2 == $reviewerId) {
                    throw new \Exception('Reviewer yang sama tidak boleh ditugaskan untuk kedua posisi seleksi.');
                }
                $proposal->id_reviewer_substantif_seleksi_1 = $reviewerId;
            } else {
                if ($proposal->id_reviewer_substantif_seleksi_1 == $reviewerId) {
                    throw new \Exception('Reviewer yang sama tidak boleh ditugaskan untuk kedua posisi seleksi.');
                }
                $proposal->id_reviewer_substantif_seleksi_2 = $reviewerId;
            }

            $proposal->save();

            if ($proposal->id_reviewer_substantif_seleksi_1 && $proposal->id_reviewer_substantif_seleksi_2) {
                $proposal->update(['status' => 'review_substantif_seleksi']);
            }

            DB::commit();

            return [
                'success' => true,
                'proposal_id' => $proposal->id_proposal,
                'status' => $proposal->fresh()->status
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
