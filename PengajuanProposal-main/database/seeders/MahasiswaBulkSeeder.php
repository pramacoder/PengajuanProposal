<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Support\Facades\Hash;

class MahasiswaBulkSeeder extends Seeder
{
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

        $prodisByFakultas = $prodis->groupBy('id_fakultas');
        $fakultasArray = $fakultas->toArray();
        $fakultasCount = $fakultas->count();

        $usedNims = User::where('role', 'mahasiswa')->pluck('identifier')->toArray();

        $created = 0;
        $skipped = 0;

        for ($i = 4; $i <= 200; $i++) {
            $name = 'monk' . $i;
            $email = $name . '@unud.ac.id';

            $existingEmail = User::where('email', $email)->first();
            if ($existingEmail) {
                $this->command->warn("Email {$email} sudah ada. Melewati mahasiswa {$name}.");
                $skipped++;
                continue;
            }

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

            $fakultasIndex = ($i - 4) % $fakultasCount;
            $selectedFakultas = $fakultas->get($fakultasIndex);

            $prodisForFakultas = $prodisByFakultas->get($selectedFakultas->id_fakultas);

            $selectedProdi = null;
            if ($prodisForFakultas && $prodisForFakultas->isNotEmpty()) {
                $prodiIndex = (int)(($i - 4) / $fakultasCount) % $prodisForFakultas->count();
                $selectedProdi = $prodisForFakultas->get($prodiIndex);
            } else {
                $selectedProdi = $prodis->random();
            }

            $noHp = '08' . str_pad((string)mt_rand(100000000, 999999999), 10, '0', STR_PAD_LEFT);

            try {
                User::create([
                    'identifier' => $nim,
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'mahasiswa',
                    'phone' => $noHp,
                    'is_active' => true,
                    'email_verified_at' => now(),
                    'metadata' => [
                        'prodi_id' => $selectedProdi->id_prodi ?? null,
                        'prodi_name' => $selectedProdi->nama_prodi,
                        'fakultas_id' => $selectedFakultas->id_fakultas,
                        'fakultas_name' => $selectedFakultas->nama_fakultas,
                        'team_id' => null,
                        'is_ketua' => false,
                        'id_dosen_pembimbing' => null,
                    ],
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

        $this->command->info("Seeder selesai! Total dibuat: {$created}");
        if ($skipped > 0) {
            $this->command->warn("Total dilewati: {$skipped}");
        }
    }
}
