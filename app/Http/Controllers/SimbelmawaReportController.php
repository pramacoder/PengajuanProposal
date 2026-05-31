<?php

namespace App\Http\Controllers;

use App\Models\SimbelmawaReport;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SimbelmawaReportController extends Controller
{
    public function __construct() {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = SimbelmawaReport::query()->with(['ruangKontrol', 'creator']);

        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        $reports = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        return view('operator.laporan_simbelmawa.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('operator.laporan_simbelmawa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'judul_proposal_lolos_pimnas' => $request->input('judul_proposal_lolos_pimnas') === '' ? null : $request->input('judul_proposal_lolos_pimnas'),
            'prestasi' => $request->input('prestasi') === '' ? null : $request->input('prestasi'),
            'jumlah_proposal_tervalidasi_pimpinan_pt' => (int) \App\Helpers\ProposalHelper::parseAngka($request->input('jumlah_proposal_tervalidasi_pimpinan_pt')),
            'jumlah_proposal_dapat_pendanaan' => (int) \App\Helpers\ProposalHelper::parseAngka($request->input('jumlah_proposal_dapat_pendanaan')),
            'total_dana_pendanaan' => \App\Helpers\ProposalHelper::parseAngka($request->input('total_dana_pendanaan')),
            'jumlah_proposal_lolos_pimnas' => (int) \App\Helpers\ProposalHelper::parseAngka($request->input('jumlah_proposal_lolos_pimnas')),
            'jumlah_prestasi' => (int) \App\Helpers\ProposalHelper::parseAngka($request->input('jumlah_prestasi')),
        ]);

        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'jumlah_proposal_tervalidasi_pimpinan_pt' => 'required|integer|min:0',
            'jumlah_proposal_dapat_pendanaan' => 'required|integer|min:0',
            'total_dana_pendanaan' => 'required|numeric|min:0',
            'jumlah_proposal_lolos_pimnas' => 'required|integer|min:0',
            'judul_proposal_lolos_pimnas' => 'nullable|json',
            'jumlah_prestasi' => 'required|integer|min:0',
            'prestasi' => 'nullable|json',
        ]);

        $judulArray = $validated['judul_proposal_lolos_pimnas']
            ? json_decode($validated['judul_proposal_lolos_pimnas'], true) ?? []
            : [];
        $prestasiArray = $validated['prestasi']
            ? json_decode($validated['prestasi'], true) ?? []
            : [];

        $report = new SimbelmawaReport();
        $report->tahun_ajaran = $validated['tahun_ajaran'];
        $report->jumlah_proposal_tervalidasi_pimpinan_pt = $validated['jumlah_proposal_tervalidasi_pimpinan_pt'];
        $report->jumlah_proposal_dapat_pendanaan = $validated['jumlah_proposal_dapat_pendanaan'];
        $report->total_dana_pendanaan = $validated['total_dana_pendanaan'];
        $report->jumlah_proposal_lolos_pimnas = $validated['jumlah_proposal_lolos_pimnas'];
        $report->judul_proposal_lolos_pimnas = $judulArray;
        $report->jumlah_prestasi = $validated['jumlah_prestasi'];
        $report->prestasi = $prestasiArray;
        $report->created_by = $request->user()->id;
        $report->save();

        return redirect()
            ->route('operator.laporan.simbelmawa.index')
            ->with('success', 'Laporan SIMBELMAWA berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $report = SimbelmawaReport::findOrFail($id);
        return view('operator.laporan_simbelmawa.edit', compact('report'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $report = SimbelmawaReport::findOrFail($id);

        $request->merge([
            'judul_proposal_lolos_pimnas' => $request->input('judul_proposal_lolos_pimnas') === '' ? null : $request->input('judul_proposal_lolos_pimnas'),
            'prestasi' => $request->input('prestasi') === '' ? null : $request->input('prestasi'),
            'jumlah_proposal_tervalidasi_pimpinan_pt' => (int) \App\Helpers\ProposalHelper::parseAngka($request->input('jumlah_proposal_tervalidasi_pimpinan_pt')),
            'jumlah_proposal_dapat_pendanaan' => (int) \App\Helpers\ProposalHelper::parseAngka($request->input('jumlah_proposal_dapat_pendanaan')),
            'total_dana_pendanaan' => \App\Helpers\ProposalHelper::parseAngka($request->input('total_dana_pendanaan')),
            'jumlah_proposal_lolos_pimnas' => (int) \App\Helpers\ProposalHelper::parseAngka($request->input('jumlah_proposal_lolos_pimnas')),
            'jumlah_prestasi' => (int) \App\Helpers\ProposalHelper::parseAngka($request->input('jumlah_prestasi')),
        ]);

        $validated = $request->validate([
            'tahun_ajaran' => 'required|string|max:20',
            'jumlah_proposal_tervalidasi_pimpinan_pt' => 'required|integer|min:0',
            'jumlah_proposal_dapat_pendanaan' => 'required|integer|min:0',
            'total_dana_pendanaan' => 'required|numeric|min:0',
            'jumlah_proposal_lolos_pimnas' => 'required|integer|min:0',
            'judul_proposal_lolos_pimnas' => 'nullable|json',
            'jumlah_prestasi' => 'required|integer|min:0',
            'prestasi' => 'nullable|json',
        ]);

        $judulArray = $validated['judul_proposal_lolos_pimnas']
            ? json_decode($validated['judul_proposal_lolos_pimnas'], true) ?? []
            : [];
        $prestasiArray = $validated['prestasi']
            ? json_decode($validated['prestasi'], true) ?? []
            : [];

        $report->tahun_ajaran = $validated['tahun_ajaran'];
        $report->jumlah_proposal_tervalidasi_pimpinan_pt = $validated['jumlah_proposal_tervalidasi_pimpinan_pt'];
        $report->jumlah_proposal_dapat_pendanaan = $validated['jumlah_proposal_dapat_pendanaan'];
        $report->total_dana_pendanaan = $validated['total_dana_pendanaan'];
        $report->jumlah_proposal_lolos_pimnas = $validated['jumlah_proposal_lolos_pimnas'];
        $report->judul_proposal_lolos_pimnas = $judulArray;
        $report->jumlah_prestasi = $validated['jumlah_prestasi'];
        $report->prestasi = $prestasiArray;
        $report->save();

        return redirect()
            ->route('operator.laporan.simbelmawa.index')
            ->with('success', 'Laporan SIMBELMAWA berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $report = SimbelmawaReport::findOrFail($id);
        $report->delete();

        return redirect()
            ->route('operator.laporan.simbelmawa.index')
            ->with('success', 'Laporan SIMBELMAWA berhasil dihapus.');
    }
}
