<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\User;
use App\Services\ReviewerAssignmentService;
use Illuminate\Support\Facades\Log;

class ReviewerAssignmentController extends Controller
{
    protected ReviewerAssignmentService $reviewerAssignmentService;

    public function __construct(ReviewerAssignmentService $reviewerAssignmentService)
    {
        $this->reviewerAssignmentService = $reviewerAssignmentService;
    }

    public function pilihReviewer()
    {
        $tahun = request('tahun', '2025');
        $filter = request('filter', 'all');
        
        // Ambil proposal yang sudah divalidasi dosen dan BELUM memiliki reviewer
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif'])
            ->where('status_validasi', 'valid')
            ->where(function($query) {
                $query->whereNull('id_reviewer_administratif')
                      ->orWhereNull('id_reviewer_substantif_1')
                      ->orWhereNull('id_reviewer_substantif_2');
            })
            ->whereRaw("EXTRACT(YEAR FROM tanggal_pengajuan) = ?", [$tahun])
            ->when($filter !== 'all', function($query) use ($filter) {
                $query->where('skim', $filter);
            })
            ->get();
        
        $reviewers = User::reviewer()->where('is_active', true)->get();
        
        return view('operator.pilih_reviewer', compact('proposals', 'reviewers', 'tahun', 'filter'));
    }

    public function searchReviewers(Request $request)
    {
        $search = $request->get('search', '');
        
        if (strlen($search) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal 2 karakter untuk pencarian'
            ]);
        }
        
        try {
            $reviewers = User::reviewer()->where('is_active', true)
                ->where(function($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                          ->orWhere('email', 'LIKE', "%{$search}%");
                })
                ->select('id', 'name', 'email', 'phone')
                ->limit(10)
                ->get()
                ->map(function ($u) {
                    return ['id' => $u->id, 'nama_reviewer' => $u->name, 'email_reviewer' => $u->email, 'no_hp_reviewer' => $u->phone];
                });
            
            return response()->json([
                'success' => true,
                'reviewers' => $reviewers
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari reviewer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignReviewer(Request $request)
    {
        try {
            $request->validate([
                'proposal_id' => 'required|exists:proposals,id_proposal',
                'reviewer_administratif' => 'required|exists:users,id',
                'reviewer_substantif_1' => 'required|exists:users,id',
                'reviewer_substantif_2' => 'required|exists:users,id|different:reviewer_substantif_1'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', \Illuminate\Support\Arr::flatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
        }

        try {
            $result = $this->reviewerAssignmentService->assignReviewers($request->all());
            return response()->json(array_merge(['message' => 'Reviewer berhasil ditugaskan'], $result));
        } catch (\Exception $e) {
            Log::error('Error assigning reviewers', [
                'proposal_id' => $request->proposal_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'error_details' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function getAssignedProposals()
    {
        $tahun = request('tahun', '2025');
        $filter = request('filter', 'all');
        
        try {
            // Ambil proposal yang sudah ditugaskan reviewer (semua field reviewer harus terisi)
            $proposals = Proposal::with([
                'mahasiswa', 
                'dokumen', 
                'reviewerAdministratif',
                'reviewerSubstantif1',
                'reviewerSubstantif2'
            ])
            ->where('status_validasi', 'valid')
            ->whereNotNull('id_reviewer_administratif')
            ->whereNotNull('id_reviewer_substantif_1')
            ->whereNotNull('id_reviewer_substantif_2')
            ->whereRaw("EXTRACT(YEAR FROM tanggal_pengajuan) = ?", [$tahun])
            ->when($filter !== 'all', function($query) use ($filter) {
                $query->where('skim', $filter);
            })
            ->orderBy('updated_at', 'desc')
            ->get();
            
            return response()->json([
                'success' => true,
                'proposals' => $proposals,
                'count' => $proposals->count()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAssignedProposalsSeleksi()
    {
        $tahun = request('tahun', '2025');
        $filter = request('filter', 'all');

        try {
            $proposals = Proposal::with([
                'mahasiswa',
                'reviewerSubstantifSeleksi1',
                'reviewerSubstantifSeleksi2'
            ])
                ->whereNotNull('id_reviewer_substantif_seleksi_1')
                ->whereNotNull('id_reviewer_substantif_seleksi_2')
                ->whereRaw("EXTRACT(YEAR FROM tanggal_pengajuan) = ?", [$tahun])
                ->when($filter !== 'all', function ($query) use ($filter) {
                    $query->where('skim', $filter);
                })
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'proposals' => $proposals,
                'count' => $proposals->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function pilihReviewerSeleksi(Request $request)
    {
        $tahun = $request->get('tahun', '2025');
        $filter = $request->get('filter', 'all');

        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif'])
            ->whereIn('status', ['revisi', 'review_substantif_seleksi'])
            ->where(function ($query) {
                $query->whereNull('id_reviewer_substantif_seleksi_1')
                    ->orWhereNull('id_reviewer_substantif_seleksi_2');
            })
            ->whereRaw("EXTRACT(YEAR FROM tanggal_pengajuan) = ?", [$tahun])
            ->when($filter !== 'all', function ($q) use ($filter) {
                $q->where('skim', $filter);
            })
            ->get();

        $reviewers = User::reviewer()->where('is_active', true)->get();

        return view('operator.pilih_reviewer_seleksi', compact('proposals', 'reviewers', 'tahun', 'filter'));
    }

    public function assignReviewerSeleksi(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|exists:proposals,id_proposal',
            'reviewer_id' => 'required|exists:users,id',
            'review_type' => 'required|in:substantif_seleksi_1,substantif_seleksi_2'
        ]);

        try {
            $result = $this->reviewerAssignmentService->assignReviewerSeleksi($request->all());
            
            return response()->json(array_merge([
                'message' => 'Reviewer seleksi berhasil ditugaskan',
                'data' => [
                    'proposal_id' => $result['proposal_id'],
                    'review_type' => $request->review_type,
                    'reviewer_id' => $request->reviewer_id,
                    'status' => $result['status']
                ]
            ], $result));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
