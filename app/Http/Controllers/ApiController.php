<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Proposal;
use App\Models\ProposalRevisi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    /**
     * Get mahasiswa data by NIM
     */
    public function getMahasiswaByNIM($nim): JsonResponse
    {
        try {
            $mahasiswa = Mahasiswa::where('nim', $nim)->first();
            
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
                    'nama' => $mahasiswa->nama_mhs,
                    'nim' => $mahasiswa->nim,
                    'prodi' => $mahasiswa->prodi_mhs,
                    'fakultas' => $mahasiswa->fakultas_mhs,
                    'email' => $mahasiswa->email_mhs,
                    'no_hp' => $mahasiswa->no_hp_mhs,
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
    
    /**
     * Check if mahasiswa already has a proposal
     */
    public function checkMahasiswaInProposal($nim): JsonResponse
    {
        try {
            // Cek di tabel proposals (sistem lama)
            $proposal = Proposal::where('ketua_nim', $nim)
                ->orWhere('anggota1_nim', $nim)
                ->orWhere('anggota2_nim', $nim)
                ->orWhere('anggota3_nim', $nim)
                ->orWhere('anggota4_nim', $nim)
                ->first();
            
            // Cek di tabel teams (sistem baru)
            if (!$proposal) {
                $teamMember = \App\Models\Team::where('nim', $nim)->first();
                if ($teamMember) {
                    $proposal = $teamMember->proposal;
                }
            }
            
            if ($proposal) {
                return response()->json([
                    'success' => true,
                    'hasProposal' => true,
                    'proposalTitle' => $proposal->judul ?? $proposal->judul_proposal,
                    'proposalStatus' => $proposal->status,
                    'message' => 'Mahasiswa sudah terdaftar dalam proposal lain'
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

    /**
     * Get ruang kontrol status
     */
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

    /**
     * Get all fakultas
     */
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

    /**
     * Get prodi by fakultas
     */
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

    /**
     * Get prodi by nama fakultas
     */
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

    /**
     * Get proposal revisi data
     */
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
                    'judul' => $proposal->judul_proposal,
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
