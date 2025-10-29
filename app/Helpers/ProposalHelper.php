<?php

namespace App\Helpers;

use App\Models\Proposal;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProposalHelper
{
    /**
     * Validasi data proposal sesuai aturan sistem PKM
     */
    public static function validateProposalData($data)
    {
        // Tentukan validasi dana berdasarkan skim
        $skim = $data['skim'] ?? '';
        $insentifSkims = ['GFT', 'AI'];
        $isInsentif = in_array($skim, $insentifSkims);
        
        $danaRules = $isInsentif 
            ? 'required|numeric|min:0|max:0' 
            : 'required|numeric|min:1000000|max:15000000';

        $rules = [
            // Informasi dasar proposal
            'judul' => 'required|string|min:10|max:200',
            'skim' => 'required|in:RE,RSH,KC,PM,PI,K,KI,VGK,AI,GFT',
            'dosen_pembimbing' => 'required|string',
            'dana_diajukan' => $danaRules,
            'tahun_ajaran' => 'required|string',
            
            // Data ketua tim (wajib)
            'ketua_nama' => 'required|string|max:255',
            'ketua_nim' => 'required|string|min:8|max:20',
            'ketua_prodi' => 'required|string|max:255',
            'ketua_fakultas' => 'required|string|max:255',
            'ketua_email' => 'required|email|max:255',
            'ketua_no_hp' => 'required|string|min:10|max:15',
            
            // Data anggota 1 (wajib)
            'anggota1_nama' => 'required|string|max:255',
            'anggota1_nim' => 'required|string|min:8|max:20',
            'anggota1_prodi' => 'required|string|max:255',
            'anggota1_fakultas' => 'required|string|max:255',
            'anggota1_email' => 'required|email|max:255',
            'anggota1_no_hp' => 'required|string|min:10|max:15',
            
            // Data anggota 2 (wajib)
            'anggota2_nama' => 'required|string|max:255',
            'anggota2_nim' => 'required|string|min:8|max:20',
            'anggota2_prodi' => 'required|string|max:255',
            'anggota2_fakultas' => 'required|string|max:255',
            'anggota2_email' => 'required|email|max:255',
            'anggota2_no_hp' => 'required|string|min:10|max:15',
            
            // Data anggota 3 (opsional)
            'anggota3_nama' => 'nullable|string|max:255',
            'anggota3_nim' => 'nullable|string|min:8|max:20',
            'anggota3_prodi' => 'nullable|string|max:255',
            'anggota3_fakultas' => 'nullable|string|max:255',
            'anggota3_email' => 'nullable|email|max:255',
            'anggota3_no_hp' => 'nullable|string|min:10|max:15',
            
            // Data anggota 4 (opsional)
            'anggota4_nama' => 'nullable|string|max:255',
            'anggota4_nim' => 'nullable|string|min:8|max:20',
            'anggota4_prodi' => 'nullable|string|max:255',
            'anggota4_fakultas' => 'nullable|string|max:255',
            'anggota4_email' => 'nullable|email|max:255',
            'anggota4_no_hp' => 'nullable|string|min:10|max:15',
        ];

        $messages = [
            'judul.required' => 'Judul proposal wajib diisi',
            'judul.min' => 'Judul proposal minimal 10 karakter',
            'judul.max' => 'Judul proposal maksimal 200 karakter',
            'skim.required' => 'Skim PKM wajib dipilih',
            'dosen_pembimbing.required' => 'Dosen pendamping wajib dipilih',
            'dana_diajukan.required' => 'Dana yang diajukan wajib diisi',
            'dana_diajukan.min' => $isInsentif ? 'PKM Insentif tidak memiliki pendanaan. Dana harus 0.' : 'Dana minimal Rp 1.000.000',
            'dana_diajukan.max' => $isInsentif ? 'PKM Insentif tidak memiliki pendanaan. Dana harus 0.' : 'Dana maksimal Rp 15.000.000',
            'ketua_nama.required' => 'Nama ketua tim wajib diisi',
            'ketua_nim.required' => 'NIM ketua tim wajib diisi',
            'ketua_nim.min' => 'NIM ketua tim minimal 8 digit',
            'ketua_prodi.required' => 'Program studi ketua tim wajib diisi',
            'ketua_fakultas.required' => 'Fakultas ketua tim wajib diisi',
            'ketua_email.required' => 'Email ketua tim wajib diisi',
            'ketua_email.email' => 'Format email ketua tim tidak valid',
            'ketua_no_hp.required' => 'No. HP ketua tim wajib diisi',
            'ketua_no_hp.min' => 'No. HP ketua tim minimal 10 digit',
            'anggota1_nama.required' => 'Nama anggota 1 wajib diisi',
            'anggota1_nim.required' => 'NIM anggota 1 wajib diisi',
            'anggota1_nim.min' => 'NIM anggota 1 minimal 8 digit',
            'anggota1_prodi.required' => 'Program studi anggota 1 wajib diisi',
            'anggota1_fakultas.required' => 'Fakultas anggota 1 wajib diisi',
            'anggota1_email.required' => 'Email anggota 1 wajib diisi',
            'anggota1_email.email' => 'Format email anggota 1 tidak valid',
            'anggota1_no_hp.required' => 'No. HP anggota 1 wajib diisi',
            'anggota1_no_hp.min' => 'No. HP anggota 1 minimal 10 digit',
            'anggota2_nama.required' => 'Nama anggota 2 wajib diisi',
            'anggota2_nim.required' => 'NIM anggota 2 wajib diisi',
            'anggota2_nim.min' => 'NIM anggota 2 minimal 8 digit',
            'anggota2_prodi.required' => 'Program studi anggota 2 wajib diisi',
            'anggota2_fakultas.required' => 'Fakultas anggota 2 wajib diisi',
            'anggota2_email.required' => 'Email anggota 2 wajib diisi',
            'anggota2_email.email' => 'Format email anggota 2 tidak valid',
            'anggota2_no_hp.required' => 'No. HP anggota 2 wajib diisi',
            'anggota2_no_hp.min' => 'No. HP anggota 2 minimal 10 digit',
        ];

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Validasi keunikan NIM dalam tim
     */
    public static function validateTeamNIMs($data)
    {
        $errors = [];
        $nims = [];

        // Collect all NIMs
        if (!empty($data['ketua_nim'])) $nims[] = $data['ketua_nim'];
        if (!empty($data['anggota1_nim'])) $nims[] = $data['anggota1_nim'];
        if (!empty($data['anggota2_nim'])) $nims[] = $data['anggota2_nim'];
        if (!empty($data['anggota3_nim'])) $nims[] = $data['anggota3_nim'];
        if (!empty($data['anggota4_nim'])) $nims[] = $data['anggota4_nim'];

        // Check for duplicates within the team
        $duplicates = array_diff_assoc($nims, array_unique($nims));
        if (!empty($duplicates)) {
            $errors[] = 'NIM tidak boleh duplikat dalam satu tim: ' . implode(', ', array_unique($duplicates));
        }

        return $errors;
    }

    /**
     * Validasi keunikan NIM di seluruh proposal menggunakan tabel mahasiswa
     * dengan pertimbangan tahun akademik
     */
    public static function validateNIMsAcrossProposals($data, $excludeProposalId = null, $tahunAjaran = null)
    {
        $errors = [];
        $nims = [];

        // Collect all NIMs
        if (!empty($data['ketua_nim'])) $nims[] = $data['ketua_nim'];
        if (!empty($data['anggota1_nim'])) $nims[] = $data['anggota1_nim'];
        if (!empty($data['anggota2_nim'])) $nims[] = $data['anggota2_nim'];
        if (!empty($data['anggota3_nim'])) $nims[] = $data['anggota3_nim'];
        if (!empty($data['anggota4_nim'])) $nims[] = $data['anggota4_nim'];

        foreach ($nims as $nim) {
            // Cari mahasiswa dengan NIM tersebut
            $mahasiswa = \App\Models\Mahasiswa::where('nim', $nim)
                ->whereNotNull('team_id')
                ->first();
            
            if ($mahasiswa && $mahasiswa->team_id) {
                // Cari proposal yang terkait dengan team_id
                $query = \App\Models\Proposal::where('team_id', $mahasiswa->team_id);
                
                if ($excludeProposalId) {
                    $query->where('id_proposal', '!=', $excludeProposalId);
                }
                
                if ($tahunAjaran) {
                    $query->where('tahun_ajaran', $tahunAjaran);
                }
                
                // Hanya cek proposal yang MASIH AKTIF (belum ditolak atau tidak lolos)
                // Proposal yang ditolak atau tidak lolos TIDAK menghalangi pengajuan baru
                $query->where(function($q) {
                    $q->where(function($subQ) {
                        // Case 1: Proposal sudah masuk ke tahap review
                        $subQ->whereIn('status_final', ['review_administratif', 'review_substantif', 'revisi', 'lolos']);
                    })->orWhere(function($subQ) {
                        // Case 2: Proposal masih dalam proses validasi
                        $subQ->whereIn('status_validasi', ['pending', 'valid'])
                             ->whereIn('status_final', ['draft', 'submitted']);
                    });
                });
                
                $proposal = $query->first();
                
                if ($proposal) {
                    $tahunInfo = $tahunAjaran ? " tahun {$tahunAjaran}" : "";
                    $memberType = $mahasiswa->is_ketua ? "Ketua" : "Anggota";
                    $errors[] = "{$memberType} dengan NIM {$nim} sudah terdaftar dalam proposal{$tahunInfo}: \"{$proposal->judul}\"";
                }
            }
        }

        return $errors;
    }

    /**
     * Validasi kelengkapan data anggota opsional
     */
    public static function validateOptionalMembers($data)
    {
        $errors = [];

        // Validasi anggota 3
        $hasAnggota3Data = !empty($data['anggota3_nama']) || !empty($data['anggota3_nim']) || 
                          !empty($data['anggota3_prodi']) || !empty($data['anggota3_fakultas']) || 
                          !empty($data['anggota3_email']) || !empty($data['anggota3_no_hp']);

        if ($hasAnggota3Data) {
            if (empty($data['anggota3_nama'])) $errors[] = 'Nama anggota 3 wajib diisi jika ada data anggota';
            if (empty($data['anggota3_nim'])) $errors[] = 'NIM anggota 3 wajib diisi jika ada data anggota';
            if (empty($data['anggota3_prodi'])) $errors[] = 'Program studi anggota 3 wajib diisi jika ada data anggota';
            if (empty($data['anggota3_fakultas'])) $errors[] = 'Fakultas anggota 3 wajib diisi jika ada data anggota';
            if (empty($data['anggota3_email'])) $errors[] = 'Email anggota 3 wajib diisi jika ada data anggota';
            if (empty($data['anggota3_no_hp'])) $errors[] = 'No. HP anggota 3 wajib diisi jika ada data anggota';
        }

        // Validasi anggota 4
        $hasAnggota4Data = !empty($data['anggota4_nama']) || !empty($data['anggota4_nim']) || 
                          !empty($data['anggota4_prodi']) || !empty($data['anggota4_fakultas']) || 
                          !empty($data['anggota4_email']) || !empty($data['anggota4_no_hp']);

        if ($hasAnggota4Data) {
            if (empty($data['anggota4_nama'])) $errors[] = 'Nama anggota 4 wajib diisi jika ada data anggota';
            if (empty($data['anggota4_nim'])) $errors[] = 'NIM anggota 4 wajib diisi jika ada data anggota';
            if (empty($data['anggota4_prodi'])) $errors[] = 'Program studi anggota 4 wajib diisi jika ada data anggota';
            if (empty($data['anggota4_fakultas'])) $errors[] = 'Fakultas anggota 4 wajib diisi jika ada data anggota';
            if (empty($data['anggota4_email'])) $errors[] = 'Email anggota 4 wajib diisi jika ada data anggota';
            if (empty($data['anggota4_no_hp'])) $errors[] = 'No. HP anggota 4 wajib diisi jika ada data anggota';
        }

        return $errors;
    }

    /**
     * Cek apakah mahasiswa sudah terdaftar dalam proposal lain menggunakan tabel mahasiswa
     * dengan pertimbangan tahun akademik
     */
    public static function checkStudentInProposal($nim, $excludeProposalId = null, $tahunAjaran = null)
    {
        $mahasiswa = \App\Models\Mahasiswa::where('nim', $nim)
            ->whereNotNull('team_id')
            ->first();
        
        if (!$mahasiswa) {
            return null;
        }
        
        // Cek proposal berdasarkan team_id
        $proposalQuery = Proposal::where('team_id', $mahasiswa->team_id);
        
        if ($excludeProposalId) {
            $proposalQuery->where('id_proposal', '!=', $excludeProposalId);
        }
        
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
        
        return $proposalQuery->first();
    }

    /**
     * Dapatkan jumlah anggota tim menggunakan tabel mahasiswa
     */
    public static function getTeamSize($proposalId)
    {
        $proposal = Proposal::find($proposalId);
        if (!$proposal || !$proposal->team_id) {
            return 0;
        }
        
        return \App\Models\Mahasiswa::where('team_id', $proposal->team_id)->count();
    }

    /**
     * Validasi ukuran tim
     */
    public static function validateTeamSize($data)
    {
        $count = 1; // Ketua tim

        if (!empty($data['anggota1_nim'])) $count++;
        if (!empty($data['anggota2_nim'])) $count++;
        if (!empty($data['anggota3_nim'])) $count++;
        if (!empty($data['anggota4_nim'])) $count++;

        if ($count < 3) {
            return ['Tim minimal harus terdiri dari 3 orang (1 ketua + 2 anggota)'];
        }

        if ($count > 5) {
            return ['Tim maksimal terdiri dari 5 orang'];
        }

        return [];
    }

    /**
     * Dapatkan status proposal dalam bahasa Indonesia
     */
    public static function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Menunggu',
            'valid' => 'Valid',
            'tidak_valid' => 'Tidak Valid',
            'submitted' => 'Telah Diajukan',
            'review_administratif' => 'Review Administratif',
            'review_substantif' => 'Review Substantif',
            'revisi' => 'Revisi',
            'lolos' => 'Lolos',
            'tidak_lolos' => 'Tidak Lolos'
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Dapatkan label skim PKM
     */
    public static function getSkimLabel($skim)
    {
        $labels = [
            'RE' => 'PKM-RE (Riset Eksak)',
            'RSH' => 'PKM-RSH (Riset Sosial Humaniora)',
            'KC' => 'PKM-KC (Karsa Cipta)',
            'PM' => 'PKM-PM (Pengabdian Masyarakat)',
            'PI' => 'PKM-PI (Penerapan Iptek)',
            'K' => 'PKM-K (Kewirausahaan)',
            'KI' => 'PKM-KI (Karsa Cipta)',
            'VGK' => 'PKM-VGK (Video Gagasan Konstruktif)',
            'AI' => 'PKM-AI (Artikel Ilmiah)',
            'GFT' => 'PKM-GFT (Gagasan Futuristik Tertulis)'
        ];

        return $labels[$skim] ?? $skim;
    }

    /**
     * Buat anggota tim dari form data
     * 
     * Konsep: 1 proposal = 1 tim dengan 3-5 anggota
     * Setiap anggota tim disimpan sebagai mahasiswa dengan team_id yang sama
     */
    public static function createTeamData($proposalId, $data)
    {
        $proposal = Proposal::find($proposalId);
        if (!$proposal) {
            throw new \Exception('Proposal tidak ditemukan');
        }

        // Generate team_id unik (bisa menggunakan proposal_id)
        $teamId = $proposalId;
        
        // Update proposal dengan team_id
        $proposal->update(['team_id' => $teamId]);

        $teamMembers = [];

        // Ketua tim (wajib)
        if (!empty($data['ketua_nim'])) {
            $ketua = self::createOrUpdateMahasiswa([
                'nim' => $data['ketua_nim'],
                'nama_mhs' => $data['ketua_nama'],
                'prodi_mhs' => $data['ketua_prodi'],
                'fakultas_mhs' => $data['ketua_fakultas'],
                'email_mhs' => $data['ketua_email'],
                'no_hp_mhs' => $data['ketua_no_hp'],
                'team_id' => $teamId,
                'is_ketua' => true,
            ]);
            $teamMembers[] = $ketua;
        }

        // Anggota 1 (wajib)
        if (!empty($data['anggota1_nim'])) {
            $anggota1 = self::createOrUpdateMahasiswa([
                'nim' => $data['anggota1_nim'],
                'nama_mhs' => $data['anggota1_nama'],
                'prodi_mhs' => $data['anggota1_prodi'],
                'fakultas_mhs' => $data['anggota1_fakultas'],
                'email_mhs' => $data['anggota1_email'],
                'no_hp_mhs' => $data['anggota1_no_hp'],
                'team_id' => $teamId,
                'is_ketua' => false,
            ]);
            $teamMembers[] = $anggota1;
        }

        // Anggota 2 (wajib)
        if (!empty($data['anggota2_nim'])) {
            $anggota2 = self::createOrUpdateMahasiswa([
                'nim' => $data['anggota2_nim'],
                'nama_mhs' => $data['anggota2_nama'],
                'prodi_mhs' => $data['anggota2_prodi'],
                'fakultas_mhs' => $data['anggota2_fakultas'],
                'email_mhs' => $data['anggota2_email'],
                'no_hp_mhs' => $data['anggota2_no_hp'],
                'team_id' => $teamId,
                'is_ketua' => false,
            ]);
            $teamMembers[] = $anggota2;
        }

        // Anggota 3 (opsional)
        if (!empty($data['anggota3_nim'])) {
            $anggota3 = self::createOrUpdateMahasiswa([
                'nim' => $data['anggota3_nim'],
                'nama_mhs' => $data['anggota3_nama'],
                'prodi_mhs' => $data['anggota3_prodi'],
                'fakultas_mhs' => $data['anggota3_fakultas'],
                'email_mhs' => $data['anggota3_email'],
                'no_hp_mhs' => $data['anggota3_no_hp'],
                'team_id' => $teamId,
                'is_ketua' => false,
            ]);
            $teamMembers[] = $anggota3;
        }

        // Anggota 4 (opsional)
        if (!empty($data['anggota4_nim'])) {
            $anggota4 = self::createOrUpdateMahasiswa([
                'nim' => $data['anggota4_nim'],
                'nama_mhs' => $data['anggota4_nama'],
                'prodi_mhs' => $data['anggota4_prodi'],
                'fakultas_mhs' => $data['anggota4_fakultas'],
                'email_mhs' => $data['anggota4_email'],
                'no_hp_mhs' => $data['anggota4_no_hp'],
                'team_id' => $teamId,
                'is_ketua' => false,
            ]);
            $teamMembers[] = $anggota4;
        }

        \Log::info('Team data created successfully', [
            'proposal_id' => $proposalId,
            'team_id' => $teamId,
            'team_count' => count($teamMembers)
        ]);

        return $teamMembers;
    }

    /**
     * Update anggota tim dari form data
     * 
     * Konsep: Hapus semua anggota tim lama, lalu buat yang baru
     */
    public static function updateTeamData($proposalId, $data)
    {
        $proposal = Proposal::find($proposalId);
        if (!$proposal || !$proposal->team_id) {
            return self::createTeamData($proposalId, $data);
        }

        // Hapus anggota tim yang ada (set team_id = null)
        \App\Models\Mahasiswa::where('team_id', $proposal->team_id)->update(['team_id' => null, 'is_ketua' => false]);
        
        // Buat anggota tim baru
        return self::createTeamData($proposalId, $data);
    }

    /**
     * Helper method untuk membuat atau update mahasiswa
     */
    private static function createOrUpdateMahasiswa($data)
    {
        try {
            $mahasiswa = \App\Models\Mahasiswa::where('nim', $data['nim'])->first();
            
            if ($mahasiswa) {
                // Update mahasiswa yang sudah ada
                $mahasiswa->update([
                    'nama_mhs' => $data['nama_mhs'],
                    'prodi_mhs' => $data['prodi_mhs'],
                    'fakultas_mhs' => $data['fakultas_mhs'],
                    'email_mhs' => $data['email_mhs'],
                    'no_hp_mhs' => $data['no_hp_mhs'],
                    'team_id' => $data['team_id'],
                    'is_ketua' => $data['is_ketua'],
                ]);
                
                \Log::info('Mahasiswa updated', [
                    'nim' => $data['nim'],
                    'team_id' => $data['team_id'],
                    'is_ketua' => $data['is_ketua']
                ]);
            } else {
                // Buat mahasiswa baru
                $mahasiswa = \App\Models\Mahasiswa::create([
                    'nim' => $data['nim'],
                    'nama_mhs' => $data['nama_mhs'],
                    'prodi_mhs' => $data['prodi_mhs'],
                    'fakultas_mhs' => $data['fakultas_mhs'],
                    'email_mhs' => $data['email_mhs'],
                    'no_hp_mhs' => $data['no_hp_mhs'],
                    'password' => bcrypt('default123'), // Password default
                    'role' => 'mahasiswa',
                    'is_active' => true,
                    'team_id' => $data['team_id'],
                    'is_ketua' => $data['is_ketua'],
                ]);
                
                \Log::info('Mahasiswa created', [
                    'nim' => $data['nim'],
                    'team_id' => $data['team_id'],
                    'is_ketua' => $data['is_ketua']
                ]);
            }
            
            return $mahasiswa;
            
        } catch (\Exception $e) {
            \Log::error('Error in createOrUpdateMahasiswa', [
                'nim' => $data['nim'],
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }
}
