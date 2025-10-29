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
     * Check if mahasiswa already has a proposal (with year consideration)
     */
    public function checkMahasiswaInProposal(Request $request, $nim): JsonResponse
    {
        try {
            // Ambil tahun akademik dari query parameter atau request
            $tahunAjaran = $request->query('tahun_ajaran') ?? $request->input('tahun_ajaran');
            
            // Cek di tabel proposals (sistem lama) - hanya jika tidak ada tahun akademik
            $proposal = null;
            if (!$tahunAjaran) {
                $proposal = Proposal::where('ketua_nim', $nim)
                    ->orWhere('anggota1_nim', $nim)
                    ->orWhere('anggota2_nim', $nim)
                    ->orWhere('anggota3_nim', $nim)
                    ->orWhere('anggota4_nim', $nim)
                    ->first();
            }
            
            // Cek di tabel mahasiswa (sistem baru) - selalu cek dengan filter tahun jika ada
            if (!$proposal) {
                $mahasiswa = \App\Models\Mahasiswa::where('nim', $nim)
                    ->whereNotNull('team_id')
                    ->first();
                
                if ($mahasiswa) {
                    // Cek proposal berdasarkan team_id dan tahun ajaran
                    $proposalQuery = Proposal::where('team_id', $mahasiswa->team_id);
                    
                    if ($tahunAjaran) {
                        $proposalQuery->where('tahun_ajaran', $tahunAjaran);
                    }
                    
                    // Logika validasi berdasarkan skenario:
                    // 1. Proposal ditolak validasi → Mahasiswa bisa ajukan proposal baru saat pendaftaran terbuka
                    // 2. Proposal setelah review → Mahasiswa tidak bisa ajukan proposal baru, hanya revisi
                    // 3. Proposal masih dalam proses → Mahasiswa tidak bisa ajukan proposal baru
                    // 4. Proposal sudah lolos → Mahasiswa tidak bisa ajukan proposal baru
                    
                    // Hanya proposal yang menghalangi pengajuan baru:
                    $proposalQuery->where(function($query) {
                        $query->where(function($subQuery) {
                            // Case 2: Proposal setelah review - menghalangi pengajuan baru
                            $subQuery->whereIn('status_final', ['review_administratif', 'review_substantif', 'revisi', 'lolos', 'tidak_lolos']);
                        })->orWhere(function($subQuery) {
                            // Case 3: Proposal masih dalam proses - menghalangi pengajuan baru
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
                    'proposalTitle' => $proposal->judul ?? $proposal->judul_proposal,
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
