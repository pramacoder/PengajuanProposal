<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\NilaiAdministratif;
use App\Helpers\TahunAjaranHelper;
use App\Helpers\ProposalHelper;
use App\Services\NotificationService;
use App\Services\ReviewCompletionService;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdministratifController extends Controller
{
    public function __construct(
        private ReviewCompletionService $reviewCompletionService
    ) {}

    private function arrayFlatten($array)
    {
        $result = [];
        foreach ($array as $item) {
            if (is_array($item)) {
                $result = array_merge($result, $this->arrayFlatten($item));
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }

    public function reviewAdministratif()
    {
        $reviewer = Auth::user();
        $tahunAjaranTerpilih = request('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        $tahunAjaranList = TahunAjaranHelper::getListTahunAjaran();
        
        Log::info('Reviewer accessing administratif review page', [
            'reviewer_id' => $reviewer->id,
            'tahun_ajaran' => $tahunAjaranTerpilih
        ]);
        
        $proposals = Proposal::with(['mahasiswa', 'dosen', 'nilaiAdministratif'])
            ->where('id_reviewer_administratif', $reviewer->id)
            ->where('status_validasi', 'valid')
            ->where('tahun_ajaran', $tahunAjaranTerpilih)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('reviewer.review_administratif', compact('proposals', 'tahunAjaranTerpilih', 'tahunAjaranList'));
    }

    public function detailProposal($id)
    {
        try {
            $reviewer = Auth::user();
            
            $proposal = Proposal::with(['mahasiswa', 'dosen', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif'])->findOrFail($id);
            
            if ($proposal->status_validasi !== 'valid') {
                abort(403, 'Proposal belum divalidasi dan tidak dapat direview');
            }
            
            $isAdministratif = $proposal->id_reviewer_administratif == $reviewer->id;
            $isSubstantif = $proposal->id_reviewer_substantif_1 == $reviewer->id || 
                           $proposal->id_reviewer_substantif_2 == $reviewer->id;
            
            if (!$isAdministratif && !$isSubstantif) {
                abort(403, 'Anda tidak memiliki akses ke proposal ini');
            }
            
            if ($isAdministratif && !in_array($proposal->status, ['review_administratif', 'review_substantif', 'review_completed'])) {
                abort(403, 'Proposal belum siap untuk review administratif. Status saat ini: ' . ucfirst(str_replace('_', ' ', $proposal->status)));
            }
            
            if ($isSubstantif && !in_array($proposal->status, ['review_administratif', 'review_substantif', 'review_completed', 'revisi'])) {
                abort(403, 'Proposal belum siap untuk review substantif. Status saat ini: ' . ucfirst(str_replace('_', ' ', $proposal->status)));
            }

            if ($isAdministratif) {
                $adminReviews = $proposal->nilaiAdministratif;
                foreach ($adminReviews as $review) {
                    if ($review->checklist && !is_array($review->checklist)) {
                        if (is_string($review->checklist)) {
                            $decoded = json_decode($review->checklist, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $review->checklist = $decoded;
                            } else {
                                $review->checklist = [];
                            }
                        } else {
                            $review->checklist = [];
                        }
                    }
                }
            }

            if ($isAdministratif) {
                $dynamicForm = \App\Http\Controllers\FormPenilaianController::getActiveForm('administratif', $proposal->skim);
                $checklist = ProposalHelper::getReviewChecklist($proposal->skim);
                return view('reviewer.detail_proposal_administratif', compact('proposal', 'checklist', 'dynamicForm'));
            } else {
                $dynamicForm = \App\Http\Controllers\FormPenilaianController::getActiveForm('substantif', $proposal->skim);
                $adminReviewCompleted = $this->reviewCompletionService->isAdminReviewCompleted($proposal);
                return view('reviewer.detail_proposal_substantif', compact('proposal', 'adminReviewCompleted', 'dynamicForm'));
            }
            
        } catch (\Exception $e) {
            abort(500, 'Terjadi kesalahan saat mengakses detail proposal: ' . $e->getMessage());
        }
    }

    public function submitReviewAdministratif(Request $request, $id)
    {
        try {
            $reviewer = Auth::user();
            $proposal = Proposal::findOrFail($id);
            $dynamicForm = \App\Http\Controllers\FormPenilaianController::getActiveForm('administratif', $proposal->skim);

            if ($dynamicForm) {
                $validationRules = [
                    'catatan' => 'required|string|max:1000',
                    'extra_fields' => 'nullable|array'
                ];
                foreach ($dynamicForm->fields as $idx => $field) {
                    if (!empty($field['required'])) {
                        $validationRules['extra_fields.field_' . $idx] = 'required';
                    }
                }
                $request->validate($validationRules);
            } else {
                $request->validate([
                    'catatan' => 'required|string|max:1000',
                    'kesalahan_administratif' => 'required|array|min:1',
                    'kesalahan_administratif.*' => 'string'
                ]);
            }

            if ($proposal->status_validasi !== 'valid') {
                return response()->json(['success' => false, 'message' => 'Proposal belum divalidasi dan tidak dapat direview'], 403);
            }

            if ($proposal->id_reviewer_administratif != $reviewer->id) {
                return response()->json(['success' => false, 'message' => 'Anda tidak ditugaskan untuk review administratif proposal ini'], 403);
            }
            
            if (!in_array($proposal->status, ['review_administratif', 'review_substantif', 'review_completed'])) {
                return response()->json(['success' => false, 'message' => 'Proposal belum siap untuk review administratif. Status saat ini: ' . ucfirst(str_replace('_', ' ', $proposal->status))], 403);
            }

            $catatan = $request->input('catatan');
            $extraFields = $request->input('extra_fields', []);
            $kesalahanAdministratif = [];

            if ($dynamicForm) {
                foreach ($dynamicForm->fields as $idx => $field) {
                    $fieldKey = 'field_' . $idx;
                    // Checkbox submitted as '1' or true
                    if (($field['type'] ?? '') === 'checkbox' && !empty($extraFields[$fieldKey])) {
                        $kesalahanAdministratif[] = $field['label'];
                    }
                }
            } else {
                $kesalahanAdministratif = $request->input('kesalahan_administratif', []);
            }
            
            $nilaiAdmin = NilaiAdministratif::updateOrCreate(
                [
                    'id_proposal' => $id,
                    'id_reviewer' => $reviewer->id
                ],
                [
                    'note_administratif' => $catatan,
                    'checklist' => $kesalahanAdministratif,
                    'extra_fields' => !empty($extraFields) ? $extraFields : null,
                    'updated_at' => now()
                ]
            );


            $this->reviewCompletionService->updateProposalStatus($proposal);
            
            try {
                $adminCompleted = $this->reviewCompletionService->isAdminReviewCompleted($proposal->fresh());
                if ($adminCompleted) {
                    $notificationService = app(NotificationService::class);
                    $lolos = !empty($kesalahanAdministratif) && count($kesalahanAdministratif) > 0;
                    $notificationService->notifyReviewAdministratifSelesai(
                        $proposal->fresh(),
                        $lolos,
                        $catatan
                    );
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi review administratif: ' . $e->getMessage());
            }

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Review administratif berhasil disimpan',
                    'data' => [
                        'id' => $nilaiAdmin->id,
                        'proposal_id' => $id,
                        'reviewer_id' => $reviewer->id,
                        'saved_catatan' => $catatan,
                        'saved_kesalahan' => $kesalahanAdministratif,
                        'new_status' => $proposal->fresh()->status
                    ]
                ]);
            }
            
            return redirect()->route('reviewer.detail.proposal', $id)->with('success', 'Review administratif berhasil disimpan.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . implode(', ', $this->arrayFlatten($e->errors())),
                    'errors' => $e->errors()
                ], 422);
            }
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}
