<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Helpers\TahunAjaranHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $reviewer = Auth::user();
        $tahunAjaranTerpilih = request('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        $tahunAjaranList = TahunAjaranHelper::getListTahunAjaran();
        
        Log::info('Reviewer accessing dashboard', [
            'reviewer_id' => $reviewer->id,
            'tahun_ajaran' => $tahunAjaranTerpilih
        ]);
        
        $proposals = Proposal::with(['mahasiswa', 'dosen', 'nilaiAdministratif', 'nilaiSubstantif'])
            ->where(function($query) use ($reviewer) {
                $query->where('id_reviewer_administratif', $reviewer->id)
                      ->orWhere('id_reviewer_substantif_1', $reviewer->id)
                      ->orWhere('id_reviewer_substantif_2', $reviewer->id);
            })
            ->where('status_validasi', 'valid')
            ->where('tahun_ajaran', $tahunAjaranTerpilih)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        Log::info('Proposals found for dashboard', [
            'reviewer_id' => $reviewer->id,
            'proposal_count' => $proposals->count(),
            'proposals' => $proposals->map(function($p) use ($reviewer) {
                $isAdminReviewer = $p->id_reviewer_administratif == $reviewer->id;
                $isSubstantifReviewer = $p->id_reviewer_substantif_1 == $reviewer->id || 
                                       $p->id_reviewer_substantif_2 == $reviewer->id;
                
                return [
                    'id' => $p->id_proposal,
                    'status' => $p->status,
                    'is_admin_reviewer' => $isAdminReviewer,
                    'is_substantif_reviewer' => $isSubstantifReviewer
                ];
            })
        ]);

        $totalAssigned = $proposals->count();
        $completedReview = $proposals->filter(function($proposal) use ($reviewer) {
            if ($proposal->id_reviewer_administratif == $reviewer->id) {
                $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', $reviewer->id)->first();
                return $adminReview && $adminReview->note_administratif && !empty($adminReview->checklist);
            }
            
            if ($proposal->id_reviewer_substantif_1 == $reviewer->id || 
                $proposal->id_reviewer_substantif_2 == $reviewer->id) {
                $substantifReview = $proposal->nilaiSubstantif->where('id_reviewer', $reviewer->id)->first();
                return $substantifReview && $substantifReview->note_substantif && 
                       $substantifReview->note_substantif !== 'Review substantif dimulai';
            }
            
            return false;
        })->count();
        
        $pendingReview = $totalAssigned - $completedReview;
        
        Log::info('Dashboard statistics calculated', [
            'reviewer_id' => $reviewer->id,
            'total_assigned' => $totalAssigned,
            'completed_review' => $completedReview,
            'pending_review' => $pendingReview
        ]);

        return view('reviewer.dashboard', compact('proposals', 'totalAssigned', 'completedReview', 'pendingReview', 'tahunAjaranTerpilih', 'tahunAjaranList'));
    }
}
