<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Proposal;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * TeamSeeder - Membuat anggota tim untuk setiap proposal
 * 
 * Konsep:
 * - 1 Proposal = 1 Team
 * - 1 Team = 3-5 anggota (minimal 3, maksimal 5)
 * - Setiap record di tabel teams = 1 anggota tim
 * - id_proposal mengelompokkan anggota menjadi 1 tim
 */
class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing teams to avoid conflicts
        Team::query()->delete();
        
        // Get all proposals
        $proposals = Proposal::all();
        
        if ($proposals->isEmpty()) {
            $this->command->warn('Tidak ada proposal yang ditemukan. Jalankan ProposalSeeder terlebih dahulu.');
            return;
        }
        
        // Get all mahasiswa IDs
        $allMahasiswaIds = Mahasiswa::pluck('id_mahasiswa')->toArray();
        
        // Get mahasiswa IDs yang sudah menjadi pengaju proposal
        $pengajuIds = $proposals->pluck('id_mahasiswa')->toArray();
        
        // Mahasiswa yang tersisa untuk menjadi anggota tim
        $availableMahasiswaIds = array_values(array_diff($allMahasiswaIds, $pengajuIds));
        
        $this->command->info('Total mahasiswa: ' . count($allMahasiswaIds));
        $this->command->info('Mahasiswa pengaju proposal: ' . count($pengajuIds));
        $this->command->info('Mahasiswa tersisa untuk anggota tim: ' . count($availableMahasiswaIds));
        $this->command->info('Total proposal: ' . $proposals->count());
        
        // Track used NIMs to prevent duplicates
        $usedNims = [];
        
        // Helper function to generate unique NIM
        $generateUniqueNim = function() use (&$usedNims) {
            do {
                $nim = fake()->numerify('2021####');
            } while (in_array($nim, $usedNims));
            $usedNims[] = $nim;
            return $nim;
        };
        
        // Helper function to create team member data
        $createTeamMember = function($proposalId, $role, $mahasiswaId = null) use ($generateUniqueNim) {
            $mahasiswa = null;
            if ($mahasiswaId) {
                $mahasiswa = Mahasiswa::with(['prodi.fakultas'])->find($mahasiswaId);
            }
            
            return [
                'id_proposal' => $proposalId,
                'id_mahasiswa' => $mahasiswaId,
                'nama' => $mahasiswa ? $mahasiswa->nama_mhs : fake()->name(),
                'nim' => $mahasiswa ? $mahasiswa->nim : $generateUniqueNim(),
                'prodi' => $mahasiswa && $mahasiswa->prodi ? $mahasiswa->prodi->nama_prodi : fake()->randomElement(['Informatika', 'Sistem Informasi', 'Teknik Komputer', 'Teknik Elektro']),
                'fakultas' => $mahasiswa && $mahasiswa->prodi && $mahasiswa->prodi->fakultas ? $mahasiswa->prodi->fakultas->nama_fakultas : fake()->randomElement(['Fakultas Teknik', 'Fakultas Ilmu Komputer']),
                'email' => $mahasiswa ? $mahasiswa->email_mhs : fake()->email(),
                'no_hp' => $mahasiswa ? $mahasiswa->no_hp_mhs : fake()->numerify('08##########'),
                'role' => $role,
                'status' => 'active',
            ];
        };
        
        $availableMahasiswaIndex = 0;
        $dummyCount = 0;
        
        foreach ($proposals as $proposal) {
            $this->command->info("Membuat tim untuk proposal: {$proposal->judul}");
            
            // Ketua tim adalah pengaju proposal (wajib)
            $ketuaData = $createTeamMember($proposal->id_proposal, 'ketua', $proposal->id_mahasiswa);
            Team::create($ketuaData);
            
            // Anggota 1 (wajib) - ambil dari mahasiswa tersisa
            if ($availableMahasiswaIndex < count($availableMahasiswaIds)) {
                $anggota1Id = $availableMahasiswaIds[$availableMahasiswaIndex];
                $anggota1Data = $createTeamMember($proposal->id_proposal, 'anggota1', $anggota1Id);
                Team::create($anggota1Data);
                $availableMahasiswaIndex++;
            } else {
                // Jika mahasiswa tersisa habis, buat data dummy
                $anggota1Data = $createTeamMember($proposal->id_proposal, 'anggota1');
                Team::create($anggota1Data);
                $dummyCount++;
            }
            
            // Anggota 2 (wajib) - ambil dari mahasiswa tersisa
            if ($availableMahasiswaIndex < count($availableMahasiswaIds)) {
                $anggota2Id = $availableMahasiswaIds[$availableMahasiswaIndex];
                $anggota2Data = $createTeamMember($proposal->id_proposal, 'anggota2', $anggota2Id);
                Team::create($anggota2Data);
                $availableMahasiswaIndex++;
            } else {
                // Jika mahasiswa tersisa habis, buat data dummy
                $anggota2Data = $createTeamMember($proposal->id_proposal, 'anggota2');
                Team::create($anggota2Data);
                $dummyCount++;
            }
            
            // Anggota 3 (opsional - 50% chance) - ambil dari mahasiswa tersisa
            if (fake()->boolean(50)) {
                if ($availableMahasiswaIndex < count($availableMahasiswaIds)) {
                    $anggota3Id = $availableMahasiswaIds[$availableMahasiswaIndex];
                    $anggota3Data = $createTeamMember($proposal->id_proposal, 'anggota3', $anggota3Id);
                    Team::create($anggota3Data);
                    $availableMahasiswaIndex++;
                } else {
                    // Jika mahasiswa tersisa habis, buat data dummy
                    $anggota3Data = $createTeamMember($proposal->id_proposal, 'anggota3');
                    Team::create($anggota3Data);
                    $dummyCount++;
                }
            }
            
            // Anggota 4 (opsional - 30% chance jika anggota 3 ada) - ambil dari mahasiswa tersisa
            if (fake()->boolean(30)) {
                if ($availableMahasiswaIndex < count($availableMahasiswaIds)) {
                    $anggota4Id = $availableMahasiswaIds[$availableMahasiswaIndex];
                    $anggota4Data = $createTeamMember($proposal->id_proposal, 'anggota4', $anggota4Id);
                    Team::create($anggota4Data);
                    $availableMahasiswaIndex++;
                } else {
                    // Jika mahasiswa tersisa habis, buat data dummy
                    $anggota4Data = $createTeamMember($proposal->id_proposal, 'anggota4');
                    Team::create($anggota4Data);
                    $dummyCount++;
                }
            }
        }
        
        // Buat tim khusus untuk test cases dengan data yang lebih realistis
        $this->createTestTeams();
        
        $this->command->info('TeamSeeder berhasil dijalankan!');
        $this->command->info('Total tim yang dibuat: ' . $proposals->count());
        $this->command->info('Total anggota tim yang dibuat: ' . Team::count());
        $this->command->info('Mahasiswa yang digunakan sebagai anggota tim: ' . $availableMahasiswaIndex);
        $this->command->info('Mahasiswa yang masih tersisa: ' . (count($availableMahasiswaIds) - $availableMahasiswaIndex));
        $this->command->info('Data dummy yang dibuat: ' . $dummyCount);
    }
    
    /**
     * Buat tim khusus untuk test cases dengan mahasiswa asli
     */
    private function createTestTeams()
    {
        // Cari proposal test case
        $testProposal1 = Proposal::where('judul', 'Sistem Informasi Manajemen PKM Berbasis Web')->first();
        $testProposal2 = Proposal::where('judul', 'Aplikasi Mobile E-Commerce UMKM')->first();
        
        // Dapatkan mahasiswa yang belum digunakan
        $usedMahasiswaIds = Team::whereNotNull('id_mahasiswa')->pluck('id_mahasiswa')->toArray();
        $pengajuIds = Proposal::pluck('id_mahasiswa')->toArray();
        $allUsedIds = array_merge($usedMahasiswaIds, $pengajuIds);
        
        $availableMahasiswa = Mahasiswa::whereNotIn('id_mahasiswa', $allUsedIds)->get();
        
        if ($testProposal1) {
            // Hapus anggota tim yang sudah ada untuk test case 1
            Team::where('id_proposal', $testProposal1->id_proposal)->delete();
            
            // Tim minimal untuk test case 1 (3 anggota) - gunakan mahasiswa asli
            // Ketua tim adalah pengaju proposal
            Team::create([
                'id_proposal' => $testProposal1->id_proposal,
                'id_mahasiswa' => $testProposal1->id_mahasiswa,
                'nama' => 'John Doe',
                'nim' => 'TEST001001',
                'prodi' => 'Informatika',
                'fakultas' => 'Fakultas Teknik',
                'email' => 'john.doe@example.com',
                'no_hp' => '081234567890',
                'role' => 'ketua',
                'status' => 'active',
            ]);
            
            // Anggota 1 - gunakan mahasiswa asli
            if ($availableMahasiswa->count() > 0) {
                $mahasiswa1 = $availableMahasiswa->shift();
                Team::create([
                    'id_proposal' => $testProposal1->id_proposal,
                    'id_mahasiswa' => $mahasiswa1->id_mahasiswa,
                    'nama' => $mahasiswa1->nama_mhs,
                    'nim' => $mahasiswa1->nim,
                    'prodi' => $mahasiswa1->prodi_mhs,
                    'fakultas' => $mahasiswa1->fakultas_mhs,
                    'email' => $mahasiswa1->email_mhs,
                    'no_hp' => $mahasiswa1->no_hp_mhs,
                    'role' => 'anggota1',
                    'status' => 'active',
                ]);
            }
            
            // Anggota 2 - gunakan mahasiswa asli
            if ($availableMahasiswa->count() > 0) {
                $mahasiswa2 = $availableMahasiswa->shift();
                Team::create([
                    'id_proposal' => $testProposal1->id_proposal,
                    'id_mahasiswa' => $mahasiswa2->id_mahasiswa,
                    'nama' => $mahasiswa2->nama_mhs,
                    'nim' => $mahasiswa2->nim,
                    'prodi' => $mahasiswa2->prodi_mhs,
                    'fakultas' => $mahasiswa2->fakultas_mhs,
                    'email' => $mahasiswa2->email_mhs,
                    'no_hp' => $mahasiswa2->no_hp_mhs,
                    'role' => 'anggota2',
                    'status' => 'active',
                ]);
            }
        }
        
        if ($testProposal2) {
            // Hapus anggota tim yang sudah ada untuk test case 2
            Team::where('id_proposal', $testProposal2->id_proposal)->delete();
            
            // Tim lengkap untuk test case 2 (5 anggota) - gunakan mahasiswa asli
            // Ketua tim adalah pengaju proposal
            Team::create([
                'id_proposal' => $testProposal2->id_proposal,
                'id_mahasiswa' => $testProposal2->id_mahasiswa,
                'nama' => 'Alice Brown',
                'nim' => 'TEST002001',
                'prodi' => 'Sistem Informasi',
                'fakultas' => 'Fakultas Ilmu Komputer',
                'email' => 'alice.brown@example.com',
                'no_hp' => '081234567893',
                'role' => 'ketua',
                'status' => 'active',
            ]);
            
            // Anggota 1 - gunakan mahasiswa asli
            if ($availableMahasiswa->count() > 0) {
                $mahasiswa1 = $availableMahasiswa->shift();
                Team::create([
                    'id_proposal' => $testProposal2->id_proposal,
                    'id_mahasiswa' => $mahasiswa1->id_mahasiswa,
                    'nama' => $mahasiswa1->nama_mhs,
                    'nim' => $mahasiswa1->nim,
                    'prodi' => $mahasiswa1->prodi_mhs,
                    'fakultas' => $mahasiswa1->fakultas_mhs,
                    'email' => $mahasiswa1->email_mhs,
                    'no_hp' => $mahasiswa1->no_hp_mhs,
                    'role' => 'anggota1',
                    'status' => 'active',
                ]);
            }
            
            // Anggota 2 - gunakan mahasiswa asli
            if ($availableMahasiswa->count() > 0) {
                $mahasiswa2 = $availableMahasiswa->shift();
                Team::create([
                    'id_proposal' => $testProposal2->id_proposal,
                    'id_mahasiswa' => $mahasiswa2->id_mahasiswa,
                    'nama' => $mahasiswa2->nama_mhs,
                    'nim' => $mahasiswa2->nim,
                    'prodi' => $mahasiswa2->prodi_mhs,
                    'fakultas' => $mahasiswa2->fakultas_mhs,
                    'email' => $mahasiswa2->email_mhs,
                    'no_hp' => $mahasiswa2->no_hp_mhs,
                    'role' => 'anggota2',
                    'status' => 'active',
                ]);
            }
            
            // Anggota 3 - gunakan mahasiswa asli
            if ($availableMahasiswa->count() > 0) {
                $mahasiswa3 = $availableMahasiswa->shift();
                Team::create([
                    'id_proposal' => $testProposal2->id_proposal,
                    'id_mahasiswa' => $mahasiswa3->id_mahasiswa,
                    'nama' => $mahasiswa3->nama_mhs,
                    'nim' => $mahasiswa3->nim,
                    'prodi' => $mahasiswa3->prodi_mhs,
                    'fakultas' => $mahasiswa3->fakultas_mhs,
                    'email' => $mahasiswa3->email_mhs,
                    'no_hp' => $mahasiswa3->no_hp_mhs,
                    'role' => 'anggota3',
                    'status' => 'active',
                ]);
            }
            
            // Anggota 4 - gunakan mahasiswa asli
            if ($availableMahasiswa->count() > 0) {
                $mahasiswa4 = $availableMahasiswa->shift();
                Team::create([
                    'id_proposal' => $testProposal2->id_proposal,
                    'id_mahasiswa' => $mahasiswa4->id_mahasiswa,
                    'nama' => $mahasiswa4->nama_mhs,
                    'nim' => $mahasiswa4->nim,
                    'prodi' => $mahasiswa4->prodi_mhs,
                    'fakultas' => $mahasiswa4->fakultas_mhs,
                    'email' => $mahasiswa4->email_mhs,
                    'no_hp' => $mahasiswa4->no_hp_mhs,
                    'role' => 'anggota4',
                    'status' => 'active',
                ]);
            }
        }
    }
}
