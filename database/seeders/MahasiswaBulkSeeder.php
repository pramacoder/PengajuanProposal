<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mahasiswa;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MahasiswaBulkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Menambahkan 200 mahasiswa baru (monk4 - monk200) tanpa menghapus data lama
     *
     * @return void
     */
    public function run(): void
    {
        $fakultas = Fakultas::all();
        $prodis = Prodi::all();

        if ($fakultas->isEmpty()) {
            $this->command->error('Tabel Fakultas kosong. Harap isi tabel Fakultas terlebih dahulu.');
            return;
        }
        
        if ($prodis->isEmpty()) {
            $this->command->error('Tabel Prodi kosong. Harap isi tabel Prodi terlebih dahulu.');
            return;
        }

        // Group prodi by fakultas untuk distribusi yang lebih baik
        $prodisByFakultas = $prodis->groupBy('id_fakultas');
        
        $fakultasArray = $fakultas->toArray();
        $fakultasCount = $fakultas->count();
        
        // Generate NIM yang unik
        $usedNims = Mahasiswa::pluck('nim')->toArray();
        
        $created = 0;
        $skipped = 0;
        
        // Mulai dari monk4 hingga monk200 (total 197 mahasiswa)
        for ($i = 4; $i <= 200; $i++) {
            $name = 'monk' . $i;
            $email = $name . '@unud.ac.id';
            
            // Cek apakah email sudah ada
            $existingEmail = Mahasiswa::where('email_mhs', $email)->first();
            if ($existingEmail) {
                $this->command->warn("Email {$email} sudah ada. Melewati mahasiswa {$name}.");
                $skipped++;
                continue;
            }
            
            // Generate NIM: 25 + 8 digit acak (pastikan unik)
            $attempts = 0;
            do {
                $randomDigits = str_pad((string)mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
                $nim = '25' . $randomDigits;
                $attempts++;
                
                if ($attempts > 100) {
                    $this->command->error("Tidak dapat menghasilkan NIM unik setelah 100 percobaan untuk {$name}.");
                    break 2;
                }
            } while (in_array($nim, $usedNims));
            
            $usedNims[] = $nim;
            
            // Distribusikan fakultas secara round-robin
            $fakultasIndex = ($i - 4) % $fakultasCount;
            $selectedFakultas = $fakultas->get($fakultasIndex);
            
            // Pilih prodi yang sesuai dengan fakultas yang dipilih
            $prodisForFakultas = $prodisByFakultas->get($selectedFakultas->id_fakultas);
            
            $selectedProdi = null;
            if ($prodisForFakultas && $prodisForFakultas->isNotEmpty()) {
                // Pilih prodi secara round-robin dari prodi yang dimiliki fakultas ini
                $prodiIndex = (int)(($i - 4) / $fakultasCount) % $prodisForFakultas->count();
                $selectedProdi = $prodisForFakultas->get($prodiIndex);
            } else {
                // Fallback: jika tidak ada prodi untuk fakultas ini, pilih prodi acak dari semua prodi
                $selectedProdi = $prodis->random();
                $this->command->warn("Tidak ada prodi ditemukan untuk Fakultas: {$selectedFakultas->nama_fakultas}. Menggunakan prodi: {$selectedProdi->nama_prodi}");
            }
            
            // Generate nomor HP (12 digit acak)
            $noHp = '08' . str_pad((string)mt_rand(100000000, 999999999), 10, '0', STR_PAD_LEFT);
            
            try {
                Mahasiswa::create([
                    'nim' => $nim,
                    'nama_mhs' => $name,
                    'email_mhs' => $email,
                    'password' => Hash::make('password'), // Password default: 'password'
                    'prodi_mhs' => $selectedProdi->nama_prodi,
                    'fakultas_mhs' => $selectedFakultas->nama_fakultas,
                    'no_hp_mhs' => $noHp,
                    'role' => 'mahasiswa',
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'id_dosen_pembimbing' => null,
                    'team_id' => null,
                    'is_ketua' => false,
                ]);
                
                $created++;
                
                if ($created % 50 == 0) {
                    $this->command->info("Telah membuat {$created} mahasiswa...");
                }
            } catch (\Exception $e) {
                $this->command->error("Gagal membuat mahasiswa {$name}: " . $e->getMessage());
                $skipped++;
            }
        }
        
        $this->command->info("Seeder selesai!");
        $this->command->info("Total mahasiswa yang berhasil dibuat: {$created}");
        if ($skipped > 0) {
            $this->command->warn("Total mahasiswa yang dilewati: {$skipped}");
        }
    }
}

