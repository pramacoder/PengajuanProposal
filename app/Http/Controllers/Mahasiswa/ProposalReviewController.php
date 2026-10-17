<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Proposal;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use App\Models\HasilFinal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProposalReviewController extends Controller
{
    public function getAdministrativeReview($id)
    {
        $user = auth()->user();
        try {
            $proposal = Proposal::where('id_proposal', $id)
                ->where(function($query) use ($user) {
                    $query->where('id_mahasiswa', $user->id)
                          ->orWhere('team_id', $user->getTeamId());
                })
                ->firstOrFail();

            $administrativeReview = NilaiAdministratif::where('id_proposal', $id)
                ->with('reviewer')
                ->latest('updated_at')
                ->first();

            if (!$administrativeReview) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'proposal_info' => [
                        'judul' => $proposal->judul_proposal,
                        'skim' => $proposal->skim,
                        'status' => $proposal->status
                    ]
                ]);
            }

            $processedReview = $administrativeReview->toArray();
            
            if ($administrativeReview->reviewer) {
                $processedReview['reviewer'] = [
                    'nama_reviewer' => 'Reviewer Administratif'
                ];
            }
            
            if ($administrativeReview->checklist && is_array($administrativeReview->checklist)) {
                $processedReview['checklist'] = $administrativeReview->checklist;
            }

            return response()->json([
                'success' => true,
                'data' => [$processedReview], 
                'proposal_info' => [
                    'judul' => $proposal->judul_proposal,
                    'skim' => $proposal->skim,
                    'status' => $proposal->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data review administratif: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSubstantiveReview($id)
    {
        $user = auth()->user();
        try {
            $proposal = Proposal::where('id_proposal', $id)
                ->where(function($query) use ($user) {
                    $query->where('id_mahasiswa', $user->id)
                          ->orWhere('team_id', $user->getTeamId());
                })
                ->firstOrFail();

            $substantiveReviews = NilaiSubstantif::where('id_proposal', $id)
                ->with('reviewer')
                ->whereNotNull('note_substantif')
                ->where('note_substantif', '!=', 'Review substantif dimulai')
                ->where('note_substantif', '!=', '')
                ->orderBy('jenis_review', 'asc')
                ->orderBy('updated_at', 'desc')
                ->get();

            $uniqueReviews = $substantiveReviews->unique(function ($item) {
                return $item->id_reviewer . '_' . ($item->jenis_review ?? 'pertama');
            });

            $processedReviews = $uniqueReviews->values()->map(function($review, $index) {
                $processedReview = $review->toArray();
                $jenisLabel = ($review->jenis_review === 'seleksi') ? 'Seleksi' : 'Tahap Awal';
                $processedReview['jenis_label'] = $jenisLabel;
                $processedReview['catatan'] = $review->note_substantif;
                $processedReview['reviewer'] = [
                    'nama_reviewer' => 'Reviewer Substantif (' . $jenisLabel . ') ' . ($index + 1)
                ];
                
                return $processedReview;
            });

            return response()->json([
                'success' => true,
                'data' => $processedReviews,
                'proposal_info' => [
                    'judul' => $proposal->judul_proposal,
                    'skim' => $proposal->skim,
                    'status' => $proposal->status
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getSubstantiveReview', [
                'proposal_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data review substantif: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFinalReview($id)
    {
        try {
            $user = auth()->user();
            
            $proposal = Proposal::with(['dosenPendampingUniversitas', 'hasilSemiFinal'])
                ->where('id_proposal', $id)
                ->where(function($query) use ($user) {
                    $query->where('id_mahasiswa', $user->id)
                          ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE users.role = 'mahasiswa' AND users.metadata->>'$.team_id' = proposals.team_id AND users.identifier = ?)", [$user->identifier]);
                })
                ->firstOrFail();

            $finalResult = HasilFinal::where('id_proposal', $id)
                ->with('pimpinanPt')
                ->first();

            $dosenUniversitas = null;
            if ($proposal->dosenPendampingUniversitas) {
                $dosenUniversitas = [
                    'nama_dosen' => $proposal->dosenPendampingUniversitas->nama_dosen,
                    'no_hp_dosen' => $proposal->dosenPendampingUniversitas->no_hp_dosen,
                    'email_dosen' => $proposal->dosenPendampingUniversitas->email_dosen
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $finalResult,
                'dosen_universitas' => $dosenUniversitas,
                'hasil_semi_final' => $proposal->hasilSemiFinal,
                'proposal_info' => [
                    'judul' => $proposal->judul_proposal,
                    'skim' => $proposal->skim,
                    'status' => $proposal->status,
                    'status_final' => $proposal->status_final
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data hasil final: ' . $e->getMessage()
            ], 500);
        }
    }
}
