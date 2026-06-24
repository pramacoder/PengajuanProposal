<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Proposal;
use App\Models\HasilFinal;
use App\Models\HasilSemiFinal;
use App\Models\ProposalRevisi;
use App\Models\User;
use App\Helpers\StorageHelper;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class HasilController extends Controller
{
    public function hasilFinal()
    {
        $tahun = request('tahun', date('Y'));
        $filter = request('filter', 'all');
        $statusRevisi = request('status_revisi', 'all');
        
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif', 'hasilFinal', 'proposalRevisi'])
            ->whereIn('status', ['revisi', 'revisi_submitted', 'lolos', 'tidak_lolos'])
            ->whereRaw("EXTRACT(YEAR FROM tanggal_pengajuan) = ?", [$tahun])
            ->when($filter !== 'all', function($query) use ($filter) {
                $query->where('skim', $filter);
            })
            ->when($statusRevisi !== 'all', function($query) use ($statusRevisi) {
                switch($statusRevisi) {
                    case 'belum':
                        $query->whereDoesntHave('proposalRevisi');
                        break;
                    case 'sudah':
                        $query->whereHas('proposalRevisi');
                        break;
                    case 'tidak_revisi':
                        $query->where('status', 'revisi')->whereDoesntHave('proposalRevisi');
                        break;
                    case 'menunggu_review':
                        $query->where('status', 'revisi_submitted');
                        break;
                    case 'selesai':
                        $query->whereIn('status', ['lolos', 'tidak_lolos']);
                        break;
                }
            })
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();
        
        return view('operator.hasil_final', compact('proposals', 'tahun', 'filter', 'statusRevisi'));
    }

    public function proposalDetail($id)
    {
        $proposal = Proposal::with([
            'mahasiswa', 
            'dosen', 
            'dokumen', 
            'nilaiAdministratif.reviewer', 
            'nilaiSubstantif.reviewer', 
            'hasilFinal',
            'proposalRevisi' => function($query) {
                $query->orderBy('tanggal_submit', 'desc');
            }
        ])->findOrFail($id);

        return view('operator.proposal_detail', compact('proposal'));
    }

    public function updateHasilFinal(Request $request)
    {
        try {
            $request->merge([
                'dana_yang_dapat_diberikan' => \App\Helpers\ProposalHelper::parseAngka($request->input('dana_yang_dapat_diberikan')),
            ]);

            $request->validate([
                'proposal_id' => 'required|exists:proposals,id_proposal',
                'status_final' => 'required|in:lolos,tidak_lolos',
                'catatan_final' => 'nullable|string',
                'nilai' => 'required|numeric|min:0|max:100',
                'skor' => 'required|array|min:1',
                'skor.*' => 'required|numeric|min:0|max:10',
                'dana_yang_dapat_diberikan' => 'nullable|numeric|min:0'
            ]);

            DB::beginTransaction();
            
            $proposal = Proposal::findOrFail($request->proposal_id);
            $criteria = \App\Helpers\ProposalHelper::getSubstantifCriteria($proposal->skim);
            $actualCriteriaCount = \App\Helpers\ProposalHelper::countActualCriteria($criteria);
            $skorPerKriteria = $request->input('skor', []);
            
            if (is_string($skorPerKriteria)) {
                $skorPerKriteria = json_decode($skorPerKriteria, true) ?? [];
            }
            
            $normalizedSkor = [];
            foreach ($skorPerKriteria as $key => $value) {
                $normalizedSkor[(int) $key] = (float) $value;
            }
            ksort($normalizedSkor);
            
            if (count($normalizedSkor) !== $actualCriteriaCount) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah skor tidak sesuai dengan jumlah kriteria penilaian.'
                ], 422);
            }
            
            if ($request->filled('catatan_final') && strlen(trim($request->catatan_final)) < 50) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Catatan final minimal 50 karakter.'
                ], 422);
            }
            
            $proposal->update(['status' => $request->status_final]);
            
            try {
                HasilFinal::updateOrCreate(
                    ['id_proposal' => $request->proposal_id],
                    [
                        'status_final' => $request->status_final,
                        'catatan_final' => $request->catatan_final,
                        'nilai' => $request->nilai,
                        'skor_per_kriteria' => $normalizedSkor,
                        'dana_yang_dapat_diberikan' => $request->input('dana_yang_dapat_diberikan', 0),
                        'id_pt' => auth()->id()
                    ]
                );
            } catch (\Exception $modelError) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan data hasil final. Error: ' . $modelError->getMessage()
                ], 500);
            }
            
            try {
                $notificationService = app(NotificationService::class);
                $notificationService->notifyHasilFinal(
                    $proposal,
                    $request->status_final,
                    $request->nilai,
                    $request->catatan_final
                );
            } catch (\Exception $notifError) {
                Log::warning('Error sending notification', ['error' => $notifError->getMessage()]);
            }
            
            DB::commit();
            
            $cacheKey = "operator_detail_hasil_final_{$request->proposal_id}";
            Cache::forget($cacheKey);
            
            return response()->json([
                'success' => true,
                'message' => 'Hasil final berhasil diperbarui'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $errors = $e->errors();
            $firstError = collect($errors)->flatten()->first();
            return response()->json([
                'success' => false,
                'message' => $firstError ?? 'Validasi gagal.',
                'errors' => $errors
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadRevisi($id)
    {
        try {
            $revisi = ProposalRevisi::findOrFail($id);
            if (!StorageHelper::exists($revisi->path_file)) {
                return redirect()->back()->with('error', 'File revisi tidak ditemukan di server.');
            }
            return StorageHelper::download($revisi->path_file);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh file revisi: ' . $e->getMessage());
        }
    }

    public function viewRevisi($id)
    {
        try {
            $revisi = ProposalRevisi::findOrFail($id);
            return StorageHelper::response($revisi->path_file, $revisi->nama_file);
        } catch (\Exception $e) {
            abort(500, 'Terjadi kesalahan saat memuat PDF revisi: ' . $e->getMessage());
        }
    }

    public function viewPdf($id)
    {
        try {
            $proposal = Proposal::with('dokumen')->findOrFail($id);
            if (!$proposal->dokumen || !$proposal->dokumen->path_file) {
                abort(404, 'Dokumen tidak ditemukan.');
            }
            return StorageHelper::response($proposal->dokumen->path_file, basename($proposal->dokumen->path_file));
        } catch (\Exception $e) {
            abort(500, 'Terjadi kesalahan saat memuat PDF: ' . $e->getMessage());
        }
    }

    public function detailHasilFinal($id)
    {
        $cacheKey = "operator_detail_hasil_final_{$id}";
        $proposal = Cache::remember($cacheKey, 300, function() use ($id) {
            return Proposal::with([
                'mahasiswa', 
                'dosen', 
                'dokumen', 
                'hasilFinal',
                'nilaiSubstantif.reviewer',
                'proposalRevisi' => function($query) {
                    $query->orderBy('tanggal_submit', 'desc');
                }
            ])->findOrFail($id);
        });

        $criteria = \App\Helpers\ProposalHelper::getSubstantifCriteria($proposal->skim);
        $nilaiSubstantif1 = $proposal->id_reviewer_substantif_1 ? $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_1)->first() : null;
        $nilaiSubstantif2 = $proposal->id_reviewer_substantif_2 ? $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_2)->first() : null;

        $latestProposals = Proposal::with(['dokumen', 'hasilFinal'])
            ->where('id_mahasiswa', $proposal->id_mahasiswa)
            ->where('id_proposal', '!=', $proposal->id_proposal)
            ->orderBy('tanggal_pengajuan', 'desc')
            ->limit(5)
            ->get();

        return view('operator.detail_hasil_final', compact('proposal', 'criteria', 'nilaiSubstantif1', 'nilaiSubstantif2', 'latestProposals'));
    }

    public function hasilSemiFinal()
    {
        $tahun = request('tahun', date('Y'));
        $filter = request('filter', 'all');
        $statusRevisi = request('status_revisi', 'all');
        
        $proposals = Proposal::with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif', 'hasilSemiFinal', 'proposalRevisi'])
            ->whereIn('status', ['revisi', 'hasil_semi_final'])
            ->whereRaw("EXTRACT(YEAR FROM tanggal_pengajuan) = ?", [$tahun])
            ->when($filter !== 'all', function($query) use ($filter) {
                $query->where('skim', $filter);
            })
            ->when($statusRevisi !== 'all', function($query) use ($statusRevisi) {
                switch($statusRevisi) {
                    case 'belum':
                        $query->whereDoesntHave('proposalRevisi');
                        break;
                    case 'sudah':
                        $query->whereHas('proposalRevisi');
                        break;
                }
            })
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();
        
        return view('operator.hasil_semi_final', compact('proposals', 'tahun', 'filter', 'statusRevisi'));
    }

    public function detailHasilSemiFinal($id)
    {
        $cacheKey = "operator_detail_hasil_semi_final_{$id}";
        $proposal = Cache::remember($cacheKey, 300, function() use ($id) {
            return Proposal::with([
                'mahasiswa', 
                'dosen', 
                'dokumen', 
                'hasilSemiFinal',
                'nilaiSubstantif.reviewer',
                'proposalRevisi' => function($query) {
                    $query->orderByRaw("CASE WHEN jenis_revisi = 'revisi_biasa' THEN 0 ELSE 1 END")
                          ->orderBy('tanggal_submit', 'desc');
                }
            ])->findOrFail($id);
        });

        $criteria = \App\Helpers\ProposalHelper::getSubstantifCriteria($proposal->skim);
        $nilaiSubstantif1 = $proposal->id_reviewer_substantif_1 ? $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_1)->first() : null;
        $nilaiSubstantif2 = $proposal->id_reviewer_substantif_2 ? $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_2)->first() : null;

        $dosens = User::dosen()->where('is_active', true)->orderBy('name')->get();

        $latestProposals = Proposal::with(['dokumen', 'hasilSemiFinal'])
            ->where('id_mahasiswa', $proposal->id_mahasiswa)
            ->where('id_proposal', '!=', $proposal->id_proposal)
            ->orderBy('tanggal_pengajuan', 'desc')
            ->limit(5)
            ->get();

        return view('operator.detail_hasil_semi_final', compact('proposal', 'criteria', 'nilaiSubstantif1', 'nilaiSubstantif2', 'dosens', 'latestProposals'));
    }

    public function updateHasilSemiFinal(Request $request)
    {
        $request->validate([
            'proposal_id' => 'required|exists:proposals,id_proposal',
            'status_final' => 'required|in:lolos_tingkat_universitas,tidak_lolos_tingkat_universitas',
            'catatan_final' => 'nullable|string',
            'nilai' => 'required|numeric|min:0|max:100',
            'skor' => 'required|array',
            'skor.*' => 'required|numeric|min:0|max:10',
        ]);

        if ($request->status_final === 'lolos_tingkat_universitas') {
            $request->validate([
                'id_dosen_pendamping_universitas' => 'required|exists:users,id'
            ]);
        } else {
            if ($request->filled('id_dosen_pendamping_universitas')) {
                $request->validate([
                    'id_dosen_pendamping_universitas' => 'nullable|exists:users,id'
                ]);
            }
        }

        try {
            DB::beginTransaction();
            
            $proposal = Proposal::findOrFail($request->proposal_id);
            $criteria = \App\Helpers\ProposalHelper::getSubstantifCriteria($proposal->skim);
            $actualCriteriaCount = \App\Helpers\ProposalHelper::countActualCriteria($criteria);
            $skorPerKriteria = $request->input('skor', []);
            
            $normalizedSkor = [];
            foreach ($skorPerKriteria as $key => $value) {
                $normalizedSkor[(int) $key] = (float) $value;
            }
            ksort($normalizedSkor);
            
            if (count($normalizedSkor) !== $actualCriteriaCount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah skor tidak sesuai dengan jumlah kriteria penilaian.'
                ], 422);
            }
            
            if ($request->status_final === 'lolos_tingkat_universitas') {
                $updateData = [
                    'status' => 'revisi_akhir',
                    'status_final' => 'revisi_akhir',
                    'id_dosen_pendamping_universitas' => $request->id_dosen_pendamping_universitas
                ];
            } else {
                $updateData = [
                    'status' => 'tidak_lolos',
                    'status_final' => 'tidak_lolos_tingkat_universitas',
                    'id_dosen_pendamping_universitas' => null
                ];
            }
            
            $proposal->update($updateData);
            
            HasilSemiFinal::updateOrCreate(
                ['id_proposal' => $request->proposal_id],
                [
                    'status_final' => $request->status_final,
                    'catatan_final' => $request->catatan_final,
                    'nilai' => $request->nilai,
                    'skor_per_kriteria' => $normalizedSkor,
                    'dana_yang_dapat_diberikan' => $request->dana_yang_dapat_diberikan ?? null,
                    'id_pt' => auth()->id()
                ]
            );
            
            try {
                $notificationService = app(NotificationService::class);
                $notificationService->notifyHasilSemiFinal(
                    $proposal,
                    $request->status_final,
                    $request->nilai,
                    $request->catatan_final ?? null,
                    $request->dana_yang_dapat_diberikan ?? null
                );
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi hasil semi final: ' . $e->getMessage());
            }
            
            DB::commit();
            
            $cacheKey = "operator_detail_hasil_semi_final_{$request->proposal_id}";
            Cache::forget($cacheKey);
            
            return response()->json([
                'success' => true,
                'message' => 'Hasil semi final berhasil diperbarui'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
