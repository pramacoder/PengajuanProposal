<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use App\Helpers\StorageHelper;
use App\Helpers\TahunAjaranHelper;

class DosenPendampingController extends Controller
{
    public function dashboard(Request $request)
    {
        $dosen = auth()->user();

        $tahunAjaranTerpilih = $request->input('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());

        $proposalsQuery = $dosen->proposalsDosen()->with([
            'mahasiswa',
            'dokumen',
            'nilaiAdministratif',
            'nilaiSubstantif',
            'hasilFinal'
        ]);

        if ($tahunAjaranTerpilih) {
            $proposalsQuery->where('tahun_ajaran', $tahunAjaranTerpilih);
        }

        $proposals = $proposalsQuery->orderBy('tanggal_pengajuan', 'desc')->get();

        $totalProposal = $proposals->count();
        $proposalPending = $proposals->where('status_validasi', 'pending')->count();
        $proposalValid = $proposals->where('status_validasi', 'valid')->count();
        $proposalTidakValid = $proposals->where('status_validasi', 'tidak_valid')->count();

        $tahunAjaranList = $dosen->proposalsDosen()
            ->whereNotNull('tahun_ajaran')
            ->whereRaw("tahun_ajaran LIKE '%/%'")
            ->select('tahun_ajaran')
            ->groupBy('tahun_ajaran')
            ->orderByRaw("CAST(SPLIT_PART(tahun_ajaran, '/', 1) AS INTEGER) DESC")
            ->pluck('tahun_ajaran')
            ->filter(function($item) {
                return strpos($item, '/') !== false &&
                       count(explode('/', $item)) === 2;
            })
            ->values();

        if ($tahunAjaranList->isEmpty()) {
            $tahunAjaranList = collect([TahunAjaranHelper::getTahunAjaranTerbaru()]);
        }

        return view('dosen.pendamping.dashboard', compact(
            'proposals',
            'totalProposal',
            'proposalPending',
            'proposalValid',
            'proposalTidakValid',
            'tahunAjaranTerpilih',
            'tahunAjaranList'
        ));
    }

    public function detailProposal($id)
    {
        $dosen = auth()->user();

        $proposal = Proposal::with([
            'mahasiswa',
            'dokumen',
            'nilaiAdministratif',
            'nilaiSubstantif',
            'hasilFinal'
        ])->findOrFail($id);

        if ($proposal->id_dosen !== $dosen->id) {
            abort(403, 'Anda tidak memiliki akses ke proposal ini.');
        }

        return view('dosen.pendamping.detail_proposal', compact('proposal'));
    }

    public function validasi(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:valid,tolak',
            'catatan' => 'nullable|string|max:1000',
            'review_pdf' => 'nullable|file|mimes:pdf|max:5120',
            'file_koreksi' => 'nullable|file|mimes:pdf|max:5120'
        ]);

        $dosen = auth()->user();
        $proposal = Proposal::with('dokumen')->findOrFail($id);

        if ($proposal->id_dosen !== $dosen->id) {
            abort(403, 'Anda tidak memiliki akses ke proposal ini.');
        }

        $statusValidasi = $request->action === 'valid' ? 'valid' : 'tidak_valid';

        if ($request->action === 'tolak' && empty($request->catatan)) {
            return back()
                ->withErrors(['catatan' => 'Alasan penolakan harus diisi.'])
                ->withInput();
        }

        $updateData = [
            'status_validasi' => $statusValidasi,
            'catatan' => $request->catatan,
            'tanggal_validasi' => now(),
            'status' => $statusValidasi === 'valid' ? 'submitted' : 'tidak_valid'
        ];

        if ($request->hasFile('review_pdf')) {
            $file = $request->file('review_pdf');
            $fileName = 'review_dosen_' . $id . '_' . time() . '_' . $file->getClientOriginalName();
            $path = StorageHelper::store('dosen_reviews', $file, $fileName);

            if ($proposal->path_review_dosen && StorageHelper::exists($proposal->path_review_dosen)) {
                StorageHelper::delete($proposal->path_review_dosen);
            }

            $updateData['path_review_dosen'] = $path;
            $updateData['nama_file_review_dosen'] = $file->getClientOriginalName();
            $updateData['tanggal_review_dosen'] = now();
        }

        if ($request->action === 'tolak' && $request->hasFile('file_koreksi')) {
            $file = $request->file('file_koreksi');
            $fileName = 'koreksi_dosen_' . $id . '_' . time() . '_' . $file->getClientOriginalName();
            $path = StorageHelper::store('proposals', $file, $fileName);

            if ($proposal->dokumen) {
                if (!$proposal->dokumen->path_file_original && $proposal->dokumen->path_file) {
                    $proposal->dokumen->path_file_original = $proposal->dokumen->path_file;
                    $proposal->dokumen->save();
                }

                if ($proposal->dokumen->path_file &&
                    $proposal->dokumen->path_file !== $path &&
                    StorageHelper::exists($proposal->dokumen->path_file)) {
                    if ($proposal->dokumen->path_file_original && $proposal->dokumen->path_file !== $proposal->dokumen->path_file_original) {
                        StorageHelper::delete($proposal->dokumen->path_file);
                    }
                }

                $proposal->dokumen->path_file = $path;
                $proposal->dokumen->tgl_upload = now();
                $proposal->dokumen->save();
            } else {
                $proposal->dokumen()->create([
                    'path_file' => $path,
                    'skim' => $proposal->skim,
                    'tgl_upload' => now(),
                ]);
            }
        }

        $proposal->update($updateData);

        $message = $statusValidasi === 'valid'
            ? 'Proposal berhasil divalidasi dan diteruskan ke reviewer.'
            : 'Proposal ditolak dengan alasan yang telah diberikan.';

        if ($request->hasFile('review_pdf')) {
            $message .= ' Review PDF telah diupload.';
        }

        if ($request->action === 'tolak' && $request->hasFile('file_koreksi')) {
            $message .= ' File koreksi telah diupload dan menggantikan file proposal asli.';
        }

        return redirect()->route('dosen.pendamping.dashboard')
            ->with('success', $message);
    }

    public function proposalValidasi(Request $request)
    {
        $dosen = auth()->user();

        $tahunAjaranTerpilih = $request->input('tahun_ajaran', TahunAjaranHelper::getTahunAjaranTerbaru());

        $proposalsQuery = $dosen->proposalsDosen()->with([
            'mahasiswa',
        ]);

        if ($tahunAjaranTerpilih) {
            $proposalsQuery->where(function($query) use ($tahunAjaranTerpilih) {
                $query->where('tahun_ajaran', $tahunAjaranTerpilih)
                      ->orWhere(function($q) use ($tahunAjaranTerpilih) {
                          $q->where(function($subQ) {
                              $subQ->whereNull('tahun_ajaran')
                                   ->orWhere('tahun_ajaran', '');
                          })
                          ->whereRaw("CONCAT(
                              CASE WHEN EXTRACT(MONTH FROM tanggal_pengajuan) >= 7
                                  THEN EXTRACT(YEAR FROM tanggal_pengajuan)::text
                                  ELSE (EXTRACT(YEAR FROM tanggal_pengajuan) - 1)::text
                              END,
                              '/',
                              CASE WHEN EXTRACT(MONTH FROM tanggal_pengajuan) >= 7
                                  THEN (EXTRACT(YEAR FROM tanggal_pengajuan) + 1)::text
                                  ELSE EXTRACT(YEAR FROM tanggal_pengajuan)::text
                              END
                          ) = ?", [$tahunAjaranTerpilih]);
                      });
            });
        }

        $proposals = $proposalsQuery->orderBy('tanggal_pengajuan', 'desc')->get();

        foreach ($proposals as $proposal) {
            if (empty($proposal->tahun_ajaran) ||
                $proposal->tahun_ajaran !== TahunAjaranHelper::getTahunAjaranByDate($proposal->tanggal_pengajuan)) {
                $tahunAjaranDariTanggal = TahunAjaranHelper::getTahunAjaranByDate($proposal->tanggal_pengajuan);

                if ($proposal->tahun_ajaran !== $tahunAjaranDariTanggal) {
                    $proposal->tahun_ajaran = $tahunAjaranDariTanggal;
                    $proposal->save();
                }
            }
        }

        $tahunAjaranList = $dosen->proposalsDosen()
            ->get()
            ->map(function($proposal) {
                if (empty($proposal->tahun_ajaran)) {
                    return TahunAjaranHelper::getTahunAjaranByDate($proposal->tanggal_pengajuan);
                }
                return $proposal->tahun_ajaran;
            })
            ->filter(function($item) {
                return !empty($item) &&
                       strpos($item, '/') !== false &&
                       count(explode('/', $item)) === 2;
            })
            ->unique()
            ->sort(function($a, $b) {
                $yearA = (int) explode('/', $a)[0];
                $yearB = (int) explode('/', $b)[0];
                return $yearB - $yearA;
            })
            ->values();

        if ($tahunAjaranList->isEmpty()) {
            $tahunAjaranList = collect([TahunAjaranHelper::getTahunAjaranTerbaru()]);
        }

        return view('dosen.pendamping.proposal_validasi', compact('proposals', 'tahunAjaranTerpilih', 'tahunAjaranList'));
    }

    public function hasilReview()
    {
        $dosen = auth()->user();

        $proposals = $dosen->proposalsDosen()
            ->whereIn('status', ['review_administratif', 'review_substantif', 'revisi', 'lolos', 'tidak_lolos'])
            ->with(['mahasiswa', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif'])
            ->get();

        return view('dosen.pendamping.hasil_review', compact('proposals'));
    }

    public function hasilFinal()
    {
        $dosen = auth()->user();

        $proposals = $dosen->proposalsDosen()
            ->whereIn('status', ['lolos', 'tidak_lolos'])
            ->with(['mahasiswa', 'dokumen', 'hasilFinal'])
            ->get();

        return view('dosen.pendamping.hasil_final', compact('proposals'));
    }

    public function getReviewData($proposalId)
    {
        $dosen = auth()->user();

        $proposal = Proposal::with([
            'nilaiAdministratif',
            'nilaiSubstantif.reviewer',
            'mahasiswa',
            'hasilFinal'
        ])->findOrFail($proposalId);

        if ($proposal->id_dosen !== $dosen->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke proposal ini.'
            ], 403);
        }

        $administratif = $proposal->nilaiAdministratif->first();

        $substantif = $proposal->nilaiSubstantif->map(function($item) {
            return [
                'reviewer' => $item->reviewer ? $item->reviewer->name : 'Unknown',
                'catatan' => $item->komentar,
                'created_at' => $item->created_at ? $item->created_at->format('d F Y, H:i') : 'N/A'
            ];
        });

        return response()->json([
            'success' => true,
            'administratif' => $administratif ? [
                'reviewer' => $administratif->reviewer ? $administratif->reviewer->name : 'Unknown',
                'catatan' => $administratif->komentar,
                'checklist' => $administratif->checklist ? json_decode($administratif->checklist, true) : [],
                'created_at' => $administratif->created_at ? $administratif->created_at->format('d F Y, H:i') : 'N/A'
            ] : null,
            'substantif' => $substantif,
            'proposal_info' => [
                'judul' => $proposal->judul_proposal,
                'skim' => $proposal->skim,
                'mahasiswa' => $proposal->mahasiswa->name,
                'status' => $proposal->status
            ],
            'hasil_final' => $proposal->hasilFinal
        ]);
    }
}
