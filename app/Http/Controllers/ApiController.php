<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Proposal;
use App\Models\ProposalRevisi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    public function getMahasiswaByNIM($nim): JsonResponse
    {
        try {
            $mahasiswa = User::where('role', 'mahasiswa')
                ->where('identifier', $nim)
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa tidak ditemukan',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data mahasiswa berhasil ditemukan',
                'data' => [
                    'nama' => $mahasiswa->name,
                    'nim' => $mahasiswa->identifier,
                    'prodi' => $mahasiswa->getProdiName(),
                    'fakultas' => $mahasiswa->getFakultasName(),
                    'email' => $mahasiswa->email,
                    'no_hp' => $mahasiswa->phone,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    public function checkMahasiswaInProposal(Request $request, $nim): JsonResponse
    {
        try {
            $tahunAjaran = $request->query('tahun_ajaran') ?? $request->input('tahun_ajaran');

            $proposal = null;
            if (!$tahunAjaran) {
                $proposal = Proposal::where('ketua_nim', $nim)
                    ->orWhere('anggota1_nim', $nim)
                    ->orWhere('anggota2_nim', $nim)
                    ->orWhere('anggota3_nim', $nim)
                    ->orWhere('anggota4_nim', $nim)
                    ->first();
            }

            if (!$proposal) {
                $user = User::where('role', 'mahasiswa')
                    ->where('identifier', $nim)
                    ->whereRaw("(metadata->>'team_id') IS NOT NULL")
                    ->first();

                if ($user) {
                    $proposalQuery = Proposal::where('team_id', $user->getTeamId());

                    if ($tahunAjaran) {
                        $proposalQuery->where('tahun_ajaran', $tahunAjaran);
                    }

                    $proposalQuery->where(function($query) {
                        $query->where(function($subQuery) {
                            $subQuery->whereIn('status_final', ['review_administratif', 'review_substantif', 'revisi', 'lolos', 'tidak_lolos']);
                        })->orWhere(function($subQuery) {
                            $subQuery->whereIn('status_validasi', ['pending', 'valid'])
                                     ->whereIn('status_final', ['draft', 'submitted']);
                        });
                    });

                    $proposal = $proposalQuery->first();
                }
            }

            if ($proposal) {
                $tahunInfo = $tahunAjaran ? " tahun {$tahunAjaran}" : "";
                return response()->json([
                    'success' => true,
                    'hasProposal' => true,
                    'proposalTitle' => $proposal->judul ?? $proposal->judul,
                    'proposalStatus' => $proposal->status,
                    'tahunAjaran' => $proposal->tahun_ajaran,
                    'message' => "Mahasiswa sudah terdaftar dalam proposal{$tahunInfo}"
                ]);
            }

            return response()->json([
                'success' => true,
                'hasProposal' => false,
                'message' => 'Mahasiswa belum terdaftar dalam proposal lain'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRuangKontrolStatus()
    {
        try {
            $status = \App\Helpers\RuangKontrolHelper::getRuangKontrolStatus();

            return response()->json([
                'success' => true,
                'data' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFakultas()
    {
        try {
            $fakultas = \App\Models\Fakultas::orderBy('nama_fakultas')->get();

            return response()->json([
                'success' => true,
                'data' => $fakultas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProdiByFakultas($fakultasId)
    {
        try {
            $prodis = \App\Models\Prodi::where('id_fakultas', $fakultasId)
                ->orderBy('nama_prodi')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $prodis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProdiByNamaFakultas($namaFakultas)
    {
        try {
            $fakultas = \App\Models\Fakultas::where('nama_fakultas', $namaFakultas)->first();

            if (!$fakultas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fakultas tidak ditemukan'
                ], 404);
            }

            $prodis = \App\Models\Prodi::where('id_fakultas', $fakultas->id_fakultas)
                ->orderBy('nama_prodi')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $prodis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProposalRevisi($proposalId)
    {
        try {
            $proposal = Proposal::findOrFail($proposalId);

            $revisi = ProposalRevisi::where('id_proposal', $proposalId)
                ->orderBy('tanggal_submit', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'revisi' => $revisi,
                'proposal' => [
                    'id_proposal' => $proposal->id_proposal,
                    'judul' => $proposal->judul,
                    'skim' => $proposal->skim
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
