<?php

namespace App\Helpers;

use App\Models\Proposal;
use App\Models\Team;
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
     * Validasi keunikan NIM di seluruh proposal menggunakan tabel teams
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
            $query = Team::where('nim', $nim);
            
            if ($excludeProposalId) {
                $query->where('id_proposal', '!=', $excludeProposalId);
            }

            // Jika tahun akademik diberikan, hanya cek proposal dari tahun yang sama
            if ($tahunAjaran) {
                $query->whereHas('proposal', function($q) use ($tahunAjaran) {
                    $q->where('tahun_ajaran', $tahunAjaran);
                });
            }

            $existingTeam = $query->first();
            if ($existingTeam) {
                $proposal = $existingTeam->proposal;
                $tahunInfo = $tahunAjaran ? " tahun {$tahunAjaran}" : "";
                $errors[] = "NIM {$nim} sudah terdaftar dalam proposal{$tahunInfo}: {$proposal->judul}";
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
     * Cek apakah mahasiswa sudah terdaftar dalam proposal lain menggunakan tabel teams
     * dengan pertimbangan tahun akademik
     */
    public static function checkStudentInProposal($nim, $excludeProposalId = null, $tahunAjaran = null)
    {
        $query = Team::where('nim', $nim);
        
        if ($excludeProposalId) {
            $query->where('id_proposal', '!=', $excludeProposalId);
        }

        // Jika tahun akademik diberikan, hanya cek proposal dari tahun yang sama
        if ($tahunAjaran) {
            $query->whereHas('proposal', function($q) use ($tahunAjaran) {
                $q->where('tahun_ajaran', $tahunAjaran);
            });
        }

        $existingTeam = $query->first();
        return $existingTeam ? $existingTeam->proposal : null;
    }

    /**
     * Dapatkan jumlah anggota tim menggunakan tabel teams
     */
    public static function getTeamSize($proposalId)
    {
        return Team::where('id_proposal', $proposalId)
                   ->where('status', 'active')
                   ->count();
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
     * Setiap anggota tim disimpan sebagai record terpisah di tabel teams
     */
    public static function createTeamData($proposalId, $data)
    {
        $teamData = [];

        // Ketua tim (wajib)
        if (!empty($data['ketua_nim'])) {
            $teamData[] = [
                'id_proposal' => $proposalId,
                'nama' => $data['ketua_nama'],
                'nim' => $data['ketua_nim'],
                'prodi' => $data['ketua_prodi'],
                'fakultas' => $data['ketua_fakultas'],
                'email' => $data['ketua_email'],
                'no_hp' => $data['ketua_no_hp'],
                'role' => 'ketua',
                'status' => 'active',
            ];
        }

        // Anggota 1 (wajib)
        if (!empty($data['anggota1_nim'])) {
            $teamData[] = [
                'id_proposal' => $proposalId,
                'nama' => $data['anggota1_nama'],
                'nim' => $data['anggota1_nim'],
                'prodi' => $data['anggota1_prodi'],
                'fakultas' => $data['anggota1_fakultas'],
                'email' => $data['anggota1_email'],
                'no_hp' => $data['anggota1_no_hp'],
                'role' => 'anggota1',
                'status' => 'active',
            ];
        }

        // Anggota 2 (wajib)
        if (!empty($data['anggota2_nim'])) {
            $teamData[] = [
                'id_proposal' => $proposalId,
                'nama' => $data['anggota2_nama'],
                'nim' => $data['anggota2_nim'],
                'prodi' => $data['anggota2_prodi'],
                'fakultas' => $data['anggota2_fakultas'],
                'email' => $data['anggota2_email'],
                'no_hp' => $data['anggota2_no_hp'],
                'role' => 'anggota2',
                'status' => 'active',
            ];
        }

        // Anggota 3 (opsional)
        if (!empty($data['anggota3_nim'])) {
            $teamData[] = [
                'id_proposal' => $proposalId,
                'nama' => $data['anggota3_nama'],
                'nim' => $data['anggota3_nim'],
                'prodi' => $data['anggota3_prodi'],
                'fakultas' => $data['anggota3_fakultas'],
                'email' => $data['anggota3_email'],
                'no_hp' => $data['anggota3_no_hp'],
                'role' => 'anggota3',
                'status' => 'active',
            ];
        }

        // Anggota 4 (opsional)
        if (!empty($data['anggota4_nim'])) {
            $teamData[] = [
                'id_proposal' => $proposalId,
                'nama' => $data['anggota4_nama'],
                'nim' => $data['anggota4_nim'],
                'prodi' => $data['anggota4_prodi'],
                'fakultas' => $data['anggota4_fakultas'],
                'email' => $data['anggota4_email'],
                'no_hp' => $data['anggota4_no_hp'],
                'role' => 'anggota4',
                'status' => 'active',
            ];
        }

        // Buat anggota tim di database
        foreach ($teamData as $member) {
            try {
                Team::create($member);
                \Log::info('Team member created successfully', [
                    'proposal_id' => $proposalId,
                    'role' => $member['role'],
                    'nim' => $member['nim'],
                    'nama' => $member['nama']
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create team member', [
                    'proposal_id' => $proposalId,
                    'role' => $member['role'],
                    'nim' => $member['nim'],
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        }

        \Log::info('Team data created successfully', [
            'proposal_id' => $proposalId,
            'team_count' => count($teamData)
        ]);

        return $teamData;
    }

    /**
     * Update anggota tim dari form data
     * 
     * Konsep: Hapus semua anggota tim lama, lalu buat yang baru
     */
    public static function updateTeamData($proposalId, $data)
    {
        // Hapus anggota tim yang ada
        Team::where('id_proposal', $proposalId)->delete();
        
        // Buat anggota tim baru
        $teamData = self::createTeamData($proposalId, $data);
        
        foreach ($teamData as $member) {
            Team::create($member);
        }
    }
}
