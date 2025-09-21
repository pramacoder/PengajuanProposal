<?php

namespace Database\Seeders;

use App\Models\Dosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20+ dosen untuk testing (minimal 1 dosen per proposal)
        $dosenData = [
            ['nuptk' => '12345678901234567890', 'nama' => 'Dr. Budi Santoso', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.Kom, M.Kom', 'email' => 'budi.santoso@univ.ac.id'],
            ['nuptk' => '12345678901234567891', 'nama' => 'Andi Prasetyo', 'gelar_depan' => 'Ir.', 'gelar_belakang' => 'S.T, M.T', 'email' => 'andi.prasetyo@univ.ac.id'],
            ['nuptk' => 'TEST123456789012345', 'nama' => 'Test Dosen', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.Kom, M.Kom', 'email' => 'test.dosen@test.com'],
            ['nuptk' => '12345678901234567892', 'nama' => 'Prof. Dr. Maria Sari', 'gelar_depan' => 'Prof. Dr.', 'gelar_belakang' => 'M.Sc', 'email' => 'maria.sari@univ.ac.id'],
            ['nuptk' => '12345678901234567893', 'nama' => 'Dr. Eng. Rudi Hartono', 'gelar_depan' => 'Dr. Eng.', 'gelar_belakang' => 'S.T, M.T', 'email' => 'rudi.hartono@univ.ac.id'],
            ['nuptk' => '12345678901234567894', 'nama' => 'Dr. Indra Kusuma', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.Kom, M.Kom', 'email' => 'indra.kusuma@univ.ac.id'],
            ['nuptk' => '12345678901234567895', 'nama' => 'Prof. Dr. Susi Susanti', 'gelar_depan' => 'Prof. Dr.', 'gelar_belakang' => 'M.Pd', 'email' => 'susi.susanti@univ.ac.id'],
            ['nuptk' => '12345678901234567896', 'nama' => 'Dr. Agus Setiawan', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.T, M.Eng', 'email' => 'agus.setiawan@univ.ac.id'],
            ['nuptk' => '12345678901234567897', 'nama' => 'Dr. Siti Nurhaliza', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'M.Kom', 'email' => 'siti.nurhaliza@univ.ac.id'],
            ['nuptk' => '12345678901234567898', 'nama' => 'Prof. Dr. Ahmad Wijaya', 'gelar_depan' => 'Prof. Dr.', 'gelar_belakang' => 'S.T, M.T', 'email' => 'ahmad.wijaya@univ.ac.id'],
            ['nuptk' => '12345678901234567899', 'nama' => 'Dr. Rina Sari', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.Kom, M.Kom', 'email' => 'rina.sari@univ.ac.id'],
            ['nuptk' => '12345678901234567900', 'nama' => 'Ir. Bambang Sutrisno', 'gelar_depan' => 'Ir.', 'gelar_belakang' => 'M.T', 'email' => 'bambang.sutrisno@univ.ac.id'],
            ['nuptk' => '12345678901234567901', 'nama' => 'Dr. Endang Rahayu', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.Pd, M.Pd', 'email' => 'endang.rahayu@univ.ac.id'],
            ['nuptk' => '12345678901234567902', 'nama' => 'Dr. Dewi Sartika', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.T, M.T', 'email' => 'dewi.sartika@univ.ac.id'],
            ['nuptk' => '12345678901234567903', 'nama' => 'Prof. Dr. Rudi Hermawan', 'gelar_depan' => 'Prof. Dr.', 'gelar_belakang' => 'S.Kom, M.Kom', 'email' => 'rudi.hermawan@univ.ac.id'],
            ['nuptk' => '12345678901234567904', 'nama' => 'Dr. Indra Wijaya', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.T, M.Eng', 'email' => 'indra.wijaya@univ.ac.id'],
            ['nuptk' => '12345678901234567905', 'nama' => 'Dr. Maria Susanti', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.Pd, M.Pd', 'email' => 'maria.susanti@univ.ac.id'],
            ['nuptk' => '12345678901234567906', 'nama' => 'Prof. Dr. Agus Santoso', 'gelar_depan' => 'Prof. Dr.', 'gelar_belakang' => 'S.T, M.T', 'email' => 'agus.santoso@univ.ac.id'],
            ['nuptk' => '12345678901234567907', 'nama' => 'Dr. Siti Rahayu', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.Kom, M.Kom', 'email' => 'siti.rahayu@univ.ac.id'],
            ['nuptk' => '12345678901234567908', 'nama' => 'Dr. Ahmad Kusuma', 'gelar_depan' => 'Dr.', 'gelar_belakang' => 'S.T, M.Eng', 'email' => 'ahmad.kusuma@univ.ac.id'],
        ];

        foreach ($dosenData as $index => $data) {
            Dosen::create([
                'nuptk' => $data['nuptk'],
                'nama_dosen' => $data['nama'],
                'gelar_depan' => $data['gelar_depan'],
                'gelar_belakang' => $data['gelar_belakang'],
                'email_dosen' => $data['email'],
                'no_hp_dosen' => '081234567' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        echo "Seeder dosen berhasil dibuat!\n";
        echo "Total dosen: " . Dosen::count() . "\n";
        echo "Akun dosen untuk testing:\n";
        echo "1. budi.santoso@univ.ac.id / password123 (NUPTK: 12345678901234567890)\n";
        echo "2. andi.prasetyo@univ.ac.id / password123 (NUPTK: 12345678901234567891)\n";
        echo "3. test.dosen@test.com / 12345678 (NUPTK: TEST123456789012345)\n";
        echo "4. maria.sari@univ.ac.id / password123 (NUPTK: 12345678901234567892)\n";
        echo "5. rudi.hartono@univ.ac.id / password123 (NUPTK: 12345678901234567893)\n";
    }
}