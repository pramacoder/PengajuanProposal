<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Proposal;
use App\Helpers\TahunAjaranHelper;
use Illuminate\Support\Facades\Auth;

class DosenPembimbingController extends Controller
{
    public function dashboard()
    {
        return view('dosen.pembimbing.dashboard');
    }

    public function detailProposal($id)
    {
        $dosen = auth()->user();

        $proposal = Proposal::with([
            'mahasiswa',
            'dokumen',
            'nilaiAdministratif',
            'nilaiSubstantif',
            'hasilFinal',
            'dosen',
            'proposalRevisi' => function($query) {
                $query->orderBy('tanggal_submit', 'desc');
            }
        ])->findOrFail($id);

        if ($proposal->id_dosen !== $dosen->id) {
            abort(403, 'Anda tidak memiliki akses ke proposal ini.');
        }

        $fileProposal = null;
        $jenisFile = 'proposal_awal';

        $revisiAkhir = $proposal->proposalRevisi->filter(function($revisi) {
            return strpos($revisi->path_file, 'revisi_akhir') !== false;
        })->first();

        if ($revisiAkhir) {
            $fileProposal = $revisiAkhir;
            $jenisFile = 'revisi_akhir';
        } else {
            $revisiBiasa = $proposal->proposalRevisi->filter(function($revisi) {
                return strpos($revisi->path_file, 'revisi') !== false &&
                       strpos($revisi->path_file, 'revisi_akhir') === false;
            })->first();

            if ($revisiBiasa) {
                $fileProposal = $revisiBiasa;
                $jenisFile = 'revisi';
            } else if ($proposal->dokumen && $proposal->dokumen->path_file) {
                $fileProposal = $proposal->dokumen;
                $jenisFile = 'proposal_awal';
            }
        }

        return view('dosen.pembimbing.detail_proposal', compact('proposal', 'fileProposal', 'jenisFile'));
    }

    public function mahasiswaBimbingan()
    {
        $dosen = auth()->user();

        $tahunAjaranTerbaru = TahunAjaranHelper::getTahunAjaranTerbaru();

        $mahasiswaIds = Proposal::where('id_dosen', $dosen->id)
            ->where('tahun_ajaran', $tahunAjaranTerbaru)
            ->pluck('id_mahasiswa')
            ->unique();

        $mahasiswaBimbingan = User::whereIn('id', $mahasiswaIds)
            ->where('role', 'mahasiswa')
            ->get();

        return view('dosen.pembimbing.mahasiswa_bimbingan', compact('mahasiswaBimbingan'));
    }

    public function testingChild1()
    {
        return view('dosen.pembimbing.testing.child1');
    }

    public function testingChild2()
    {
        return view('dosen.pembimbing.testing.child2');
    }
}
