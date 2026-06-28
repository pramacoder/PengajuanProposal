<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proposal;
use App\Models\NilaiSubstantif;
use App\Helpers\TahunAjaranHelper;
use App\Helpers\ProposalHelper;
use App\Services\ReviewCompletionService;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubstantifController extends Controller
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

    public function reviewSubstantif()
    {
        $reviewer = Auth::user();
        $tahunAjaranTerpilih = request('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        $tahunAjaranList = TahunAjaranHelper::getListTahunAjaran();
        
        Log::info('Reviewer accessing substantif review page', [
            'reviewer_id' => $reviewer->id,
            'tahun_ajaran' => $tahunAjaranTerpilih
        ]);
        
        $proposals = Proposal::with(['mahasiswa', 'dosen', 'nilaiSubstantif'])
            ->where(function($query) use ($reviewer) {
                $query->where('id_reviewer_substantif_1', $reviewer->id)
                      ->orWhere('id_reviewer_substantif_2', $reviewer->id);
            })
            ->where('status_validasi', 'valid')
            ->where('tahun_ajaran', $tahunAjaranTerpilih)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('reviewer.review_substantif', compact('proposals', 'tahunAjaranTerpilih', 'tahunAjaranList'));
    }

    public function detailProposalSubstantif($id)
    {
        try {
            $reviewer = Auth::user();
            
            $proposal = Proposal::with(['mahasiswa', 'dosen', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif'])->findOrFail($id);
            
            if ($proposal->status_validasi !== 'valid') {
                abort(403, 'Proposal belum divalidasi dan tidak dapat direview');
            }
            
            $isSeleksiMode = request()->get('seleksi') == '1';
            if ($isSeleksiMode) {
                $isSubstantif = $proposal->id_reviewer_substantif_seleksi_1 == $reviewer->id ||
                               $proposal->id_reviewer_substantif_seleksi_2 == $reviewer->id;
            } else {
                $isSubstantif = $proposal->id_reviewer_substantif_1 == $reviewer->id ||
                               $proposal->id_reviewer_substantif_2 == $reviewer->id;
            }
            if (!$isSubstantif) {
                abort(403, 'Anda tidak ditugaskan untuk review substantif proposal ini');
            }
            
            $adminReviewCompleted = $this->reviewCompletionService->isAdminReviewCompleted($proposal);

            $criteria = ProposalHelper::getSubstantifCriteria($proposal->skim);
            
            $jenisForm = $isSeleksiMode ? 'substantif_seleksi' : 'substantif';
            $dynamicForm = \App\Http\Controllers\FormPenilaianController::getActiveForm($jenisForm, $proposal->skim);

            return view('reviewer.detail_proposal_substantif', compact('proposal', 'adminReviewCompleted', 'criteria', 'dynamicForm', 'isSeleksiMode'));
            
        } catch (\Exception $e) {
            abort(500, 'Terjadi kesalahan saat mengakses detail proposal substantif: ' . $e->getMessage());
        }
    }

    public function submitReviewSubstantif(Request $request, $id)
    {
        try {
            $reviewer = Auth::user();
            $proposal = Proposal::findOrFail($id);
            $dynamicForm = \App\Http\Controllers\FormPenilaianController::getActiveForm('substantif', $proposal->skim);

            if ($dynamicForm) {
                $validationRules = [
                    'catatan' => 'required|string|min:50|max:1000',
                    'extra_fields' => 'nullable|array'
                ];
                foreach ($dynamicForm->fields as $idx => $field) {
                    if (!empty($field['required'])) {
                        $validationRules['extra_fields.field_' . $idx] = 'required';
                    }
                }
                $request->validate($validationRules);
            } else {
                $criteria = ProposalHelper::getSubstantifCriteria($request->skim ?? 'default');
                
                $validationRules = [
                    'catatan' => 'required|string|min:50|max:1000',
                    'skor' => 'required|array',
                    'skor.*' => 'required|numeric|min:0|max:10'
                ];

                $request->validate($validationRules);
                
                if (empty($criteria)) {
                    $criteria = ProposalHelper::getSubstantifCriteria($proposal->skim);
                }
            }

            if ($proposal->status_validasi !== 'valid') {
                return response()->json(['success' => false, 'message' => 'Proposal belum divalidasi dan tidak dapat direview'], 403);
            }

            $isSubstantifReviewer = in_array($reviewer->id, [
                $proposal->id_reviewer_substantif_1,
                $proposal->id_reviewer_substantif_2
            ]);

            if (!$isSubstantifReviewer) {
                return response()->json(['success' => false, 'message' => 'Anda tidak ditugaskan untuk review substantif proposal ini'], 403);
            }

            if (!in_array($proposal->status, ['review_administratif', 'review_substantif', 'review_completed'])) {
                return response()->json(['success' => false, 'message' => 'Proposal belum siap untuk review substantif. Status saat ini: ' . ucfirst(str_replace('_', ' ', $proposal->status))], 403);
            }

            $catatan = $request->input('catatan');
            $extraFields = $request->input('extra_fields', []);
            
            $skorPerKriteria = [];
            $totalNilai = null;
            $nilaiAkhir = null;
            $scoreCalculation = ['total_nilai' => null, 'nilai_akhir' => null];

            if ($dynamicForm) {
                $totalBobot = 0;
                $criteriaIndex = 0;
                foreach ($dynamicForm->fields as $idx => $field) {
                    $fieldKey = 'field_' . $idx;
                    if (($field['type'] ?? '') === 'integer_scale') {
                        $skor = (float) ($extraFields[$fieldKey] ?? 0);
                        $bobot = (float) ($field['weight'] ?? 0);
                        $totalNilai += $bobot * $skor;
                        $totalBobot += $bobot;
                        $skorPerKriteria[$criteriaIndex++] = $skor;
                    }
                }
                $nilaiAkhir = ($totalBobot > 0) ? (($totalNilai / ($totalBobot * 7)) * 100) : 0;
                $scoreCalculation = ['total_nilai' => $totalNilai, 'nilai_akhir' => $nilaiAkhir];
            } else {
                $skorPerKriteriaRaw = $request->input('skor', []);
                
                if (!is_array($skorPerKriteriaRaw)) {
                    return response()->json(['success' => false, 'message' => 'Data skor tidak valid. Silakan refresh halaman dan coba lagi.'], 422);
                }
                
                foreach ($skorPerKriteriaRaw as $key => $value) {
                    if ($value === null || $value === '' || $value === false) {
                        continue;
                    }
                    
                    $index = (int) $key;
                    $skorValue = (float) $value;
                    
                    if ($skorValue < 0) {
                        $skorValue = 0;
                    } elseif ($skorValue > 10) {
                        $skorValue = 10;
                    }
                    
                    $skorPerKriteria[$index] = $skorValue;
                }
                ksort($skorPerKriteria);
                
                $expectedCount = ProposalHelper::countActualCriteria($criteria);
                $actualCount = count($skorPerKriteria);
                
                if ($actualCount !== $expectedCount) {
                    return response()->json(['success' => false, 'message' => "Jumlah skor tidak sesuai. Diharapkan: {$expectedCount}, Diterima: {$actualCount}. Silakan pastikan semua skor sudah diisi."], 422);
                }
                
                $scoreCalculation = ProposalHelper::calculateSubstantifScore($criteria, $skorPerKriteria);
                $totalNilai = $scoreCalculation['total_nilai'] ?? null;
                $nilaiAkhir = $scoreCalculation['nilai_akhir'] ?? null;
            }
            
            $dataToSave = [
                'note_substantif' => $catatan,
                'skor_per_kriteria' => !empty($skorPerKriteria) ? $skorPerKriteria : null,
                'total_nilai' => $totalNilai,
                'nilai_akhir' => $nilaiAkhir,
                'extra_fields' => !empty($extraFields) ? $extraFields : null,
                'updated_at' => now()
            ];
            
            // Skip this check for dynamic forms — they may have skor from integer_scale fields
            if (!$dynamicForm && (empty($dataToSave['skor_per_kriteria']) || !is_array($dataToSave['skor_per_kriteria']))) {
                return response()->json(['success' => false, 'message' => 'Data skor tidak valid. Silakan coba lagi.'], 422);
            }
            
            try {
                if (empty($dataToSave['skor_per_kriteria']) || empty($dataToSave['note_substantif'])) {
                    return response()->json(['success' => false, 'message' => 'Data tidak lengkap. Pastikan semua field terisi.'], 422);
                }
                
                DB::beginTransaction();
                
                $nilaiSubstantif = NilaiSubstantif::updateOrCreate(
                    [
                        'id_proposal' => $id,
                        'id_reviewer' => $reviewer->id
                    ],
                    $dataToSave
                );
                
                $nilaiSubstantif->refresh();
                $nilaiSubstantif = $nilaiSubstantif->fresh();
                
                $verificationPassed = true;
                $verificationErrors = [];
                
                if (empty($nilaiSubstantif->note_substantif)) {
                    $verificationPassed = false;
                    $verificationErrors[] = 'note_substantif is empty';
                }
                
                if (empty($nilaiSubstantif->skor_per_kriteria) || !is_array($nilaiSubstantif->skor_per_kriteria)) {
                    $verificationPassed = false;
                    $verificationErrors[] = 'skor_per_kriteria is empty or not array';
                }
                
                if (is_null($nilaiSubstantif->total_nilai) && !$dynamicForm) {
                    $verificationPassed = false;
                    $verificationErrors[] = 'total_nilai is null';
                }
                
                if (is_null($nilaiSubstantif->nilai_akhir) && !$dynamicForm) {
                    $verificationPassed = false;
                    $verificationErrors[] = 'nilai_akhir is null';
                }
                
                if (!$verificationPassed) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Data tidak tersimpan dengan benar. Silakan coba lagi. Error: ' . implode(', ', $verificationErrors)], 500);
                }
                
                DB::commit();
                
            } catch (\Exception $saveException) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan data penilaian: ' . $saveException->getMessage()], 500);
            }


            $this->reviewCompletionService->checkReviewCompletion($proposal);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Review substantif berhasil disimpan',
                    'data' => [
                        'id' => $nilaiSubstantif->id,
                        'proposal_id' => $id,
                        'reviewer_id' => $reviewer->id,
                        'saved_catatan' => $catatan,
                        'total_nilai' => $scoreCalculation['total_nilai'],
                        'nilai_akhir' => $scoreCalculation['nilai_akhir']
                    ]
                ]);
            }
            
            return redirect()->route('reviewer.detail.proposal.substantif', $id)->with('success', 'Review substantif berhasil disimpan.');
            
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

    public function reviewSubstantifSeleksi(Request $request)
    {
        $reviewer = Auth::user();
        $tahunAjaranTerpilih = request('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());
        $tahunAjaranList = TahunAjaranHelper::getListTahunAjaran();

        $proposals = Proposal::with(['mahasiswa', 'dosen', 'nilaiSubstantif'])
            ->where(function ($query) use ($reviewer) {
                $query->where('id_reviewer_substantif_seleksi_1', $reviewer->id)
                    ->orWhere('id_reviewer_substantif_seleksi_2', $reviewer->id);
            })
            ->where('tahun_ajaran', $tahunAjaranTerpilih)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reviewer.review_substantif_seleksi', compact('proposals', 'tahunAjaranTerpilih', 'tahunAjaranList'));
    }

    public function submitReviewSubstantifSeleksi(Request $request, $id)
    {
        $reviewer = Auth::user();
        $proposal = Proposal::findOrFail($id);
        $dynamicForm = \App\Http\Controllers\FormPenilaianController::getActiveForm('substantif_seleksi', $proposal->skim);

        try {
            if ($dynamicForm) {
                $validationRules = [
                    'catatan' => 'required|string|min:50|max:1000',
                    'extra_fields' => 'nullable|array'
                ];
                foreach ($dynamicForm->fields as $idx => $field) {
                    if (!empty($field['required'])) {
                        $validationRules['extra_fields.field_' . $idx] = 'required';
                    }
                }
                $request->validate($validationRules);
            } else {
                $criteria = ProposalHelper::getSubstantifCriteria($request->skim ?? 'default');
                $validationRules = [
                    'catatan' => 'required|string|min:50|max:1000',
                    'skor' => 'required|array',
                    'skor.*' => 'required|numeric|min:0|max:10'
                ];
                $request->validate($validationRules);
                if (empty($criteria)) {
                    $criteria = ProposalHelper::getSubstantifCriteria($proposal->skim);
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $this->arrayFlatten($e->errors())),
                'errors' => $e->errors()
            ], 422);
        }

        if ($proposal->status_validasi !== 'valid') {
            return response()->json(['success' => false, 'message' => 'Proposal belum divalidasi dan tidak dapat direview'], 403);
        }

        $isSeleksiReviewer = in_array($reviewer->id, [
            $proposal->id_reviewer_substantif_seleksi_1,
            $proposal->id_reviewer_substantif_seleksi_2
        ]);
        if (!$isSeleksiReviewer) {
            return response()->json(['success' => false, 'message' => 'Anda tidak ditugaskan untuk review substantif seleksi proposal ini'], 403);
        }

        if (!in_array($proposal->status, ['review_substantif_seleksi', 'revisi'])) {
            return response()->json(['success' => false, 'message' => 'Proposal belum siap untuk review substantif seleksi. Status: ' . $proposal->status], 403);
        }

        $catatan = $request->input('catatan');
        $extraFields = $request->input('extra_fields', []);
        
        $skorPerKriteria = [];
        $totalNilai = null;
        $nilaiAkhir = null;

        if ($dynamicForm) {
            $totalBobot = 0;
            $criteriaIndex = 0;
            foreach ($dynamicForm->fields as $idx => $field) {
                $fieldKey = 'field_' . $idx;
                if (($field['type'] ?? '') === 'integer_scale') {
                    $skor = (float) ($extraFields[$fieldKey] ?? 0);
                    $bobot = (float) ($field['weight'] ?? 0);
                    $totalNilai += $bobot * $skor;
                    $totalBobot += $bobot;
                    $skorPerKriteria[$criteriaIndex++] = $skor;
                }
            }
            $nilaiAkhir = ($totalBobot > 0) ? (($totalNilai / ($totalBobot * 7)) * 100) : 0;
        } else {
            $skorPerKriteriaRaw = $request->input('skor', []);
            if (!is_array($skorPerKriteriaRaw)) {
                return response()->json(['success' => false, 'message' => 'Data skor tidak valid.'], 422);
            }

            foreach ($skorPerKriteriaRaw as $key => $value) {
                if ($value === null || $value === '' || $value === false) {
                    continue;
                }
                $index = (int) $key;
                $skorValue = (float) $value;
                if ($skorValue < 0) {
                    $skorValue = 0;
                } elseif ($skorValue > 10) {
                    $skorValue = 10;
                }
                $skorPerKriteria[$index] = $skorValue;
            }
            ksort($skorPerKriteria);

            $expectedCount = ProposalHelper::countActualCriteria($criteria);
            if (count($skorPerKriteria) !== $expectedCount) {
                return response()->json(['success' => false, 'message' => "Jumlah skor tidak sesuai. Diharapkan: {$expectedCount}, Diterima: " . count($skorPerKriteria)], 422);
            }

            $scoreCalculation = ProposalHelper::calculateSubstantifScore($criteria, $skorPerKriteria);
            $totalNilai = $scoreCalculation['total_nilai'] ?? null;
            $nilaiAkhir = $scoreCalculation['nilai_akhir'] ?? null;
        }

        $dataToSave = [
            'note_substantif' => $catatan,
            'skor_per_kriteria' => $skorPerKriteria,
            'total_nilai' => $totalNilai,
            'nilai_akhir' => $nilaiAkhir,
            'jenis_review' => 'seleksi',
            'extra_fields' => !empty($extraFields) ? $extraFields : null,
            'updated_at' => now()
        ];

        try {
            DB::beginTransaction();

            $nilaiSubstantif = NilaiSubstantif::updateOrCreate(
                [
                    'id_proposal' => $id,
                    'id_reviewer' => $reviewer->id,
                    'jenis_review' => 'seleksi'
                ],
                $dataToSave
            );

            $proposal->refresh();
            $sid1 = $proposal->id_reviewer_substantif_seleksi_1;
            $sid2 = $proposal->id_reviewer_substantif_seleksi_2;

            $reviewsSeleksi = NilaiSubstantif::where('id_proposal', $id)
                ->where('jenis_review', 'seleksi')
                ->whereIn('id_reviewer', array_filter([$sid1, $sid2]))
                ->get();

            $r1 = $reviewsSeleksi->where('id_reviewer', $sid1)->first();
            $r2 = $reviewsSeleksi->where('id_reviewer', $sid2)->first();

            $completed1 = $r1 && !empty($r1->note_substantif) && !empty($r1->skor_per_kriteria) &&
                !is_null($r1->total_nilai) && !is_null($r1->nilai_akhir);
            $completed2 = $r2 && !empty($r2->note_substantif) && !empty($r2->skor_per_kriteria) &&
                !is_null($r2->total_nilai) && !is_null($r2->nilai_akhir);

            if ($sid1 && $sid2 && $completed1 && $completed2) {
                $proposal->update(['status' => 'hasil_semi_final']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review substantif seleksi berhasil disimpan',
                'data' => [
                    'id' => $nilaiSubstantif->id,
                    'proposal_id' => (int) $id,
                    'reviewer_id' => $reviewer->id,
                    'total_nilai' => $scoreCalculation['total_nilai'] ?? null,
                    'nilai_akhir' => $scoreCalculation['nilai_akhir'] ?? null
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data penilaian: ' . $e->getMessage()], 500);
        }
    }
}
