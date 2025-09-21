<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat 60+ mahasiswa untuk 20 proposal (minimal 3x jumlah proposal)
        $mahasiswaData = [
            // Batch 1 - 2021 (20 mahasiswa)
            ['nim' => '2021001001', 'nama' => 'Ahmad Rizki', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2021001002', 'nama' => 'Siti Nurhaliza', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2021001003', 'nama' => 'Budi Santoso', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2021001004', 'nama' => 'Dewi Sartika', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2021001005', 'nama' => 'Rudi Hermawan', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2021001006', 'nama' => 'Indra Kusuma', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2021001007', 'nama' => 'Maria Sari', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2021001008', 'nama' => 'Agus Setiawan', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2021001009', 'nama' => 'Susi Susanti', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2021001010', 'nama' => 'Budi Satosi', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2021001011', 'nama' => 'Dewi Rahma', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2021001012', 'nama' => 'Rudi Herman', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2021001013', 'nama' => 'Endang Rahayu', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2021001014', 'nama' => 'Bambang Sutrisno', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2021001015', 'nama' => 'Rina Sari', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2021001016', 'nama' => 'Ahmad Wijaya', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2021001017', 'nama' => 'Siti Nurhaliza', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2021001018', 'nama' => 'Budi Santoso', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2021001019', 'nama' => 'Dewi Sartika', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2021001020', 'nama' => 'Rudi Hermawan', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            
            // Batch 2 - 2022 (20 mahasiswa)
            ['nim' => '2022002001', 'nama' => 'Ahmad Rizki', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2022002002', 'nama' => 'Siti Nurhaliza', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2022002003', 'nama' => 'Budi Santoso', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2022002004', 'nama' => 'Dewi Sartika', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2022002005', 'nama' => 'Rudi Hermawan', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2022002006', 'nama' => 'Indra Kusuma', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2022002007', 'nama' => 'Maria Sari', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2022002008', 'nama' => 'Agus Setiawan', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2022002009', 'nama' => 'Susi Susanti', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2022002010', 'nama' => 'Budi Satosi', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2022002011', 'nama' => 'Dewi Rahma', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2022002012', 'nama' => 'Rudi Herman', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2022002013', 'nama' => 'Endang Rahayu', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2022002014', 'nama' => 'Bambang Sutrisno', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2022002015', 'nama' => 'Rina Sari', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2022002016', 'nama' => 'Ahmad Wijaya', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2022002017', 'nama' => 'Siti Nurhaliza', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2022002018', 'nama' => 'Budi Santoso', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2022002019', 'nama' => 'Dewi Sartika', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2022002020', 'nama' => 'Rudi Hermawan', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            
            // Batch 3 - 2023 (20 mahasiswa)
            ['nim' => '2023003001', 'nama' => 'Ahmad Rizki', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2023003002', 'nama' => 'Siti Nurhaliza', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2023003003', 'nama' => 'Budi Santoso', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2023003004', 'nama' => 'Dewi Sartika', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2023003005', 'nama' => 'Rudi Hermawan', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2023003006', 'nama' => 'Indra Kusuma', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2023003007', 'nama' => 'Maria Sari', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2023003008', 'nama' => 'Agus Setiawan', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2023003009', 'nama' => 'Susi Susanti', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2023003010', 'nama' => 'Budi Satosi', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2023003011', 'nama' => 'Dewi Rahma', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2023003012', 'nama' => 'Rudi Herman', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2023003013', 'nama' => 'Endang Rahayu', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2023003014', 'nama' => 'Bambang Sutrisno', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2023003015', 'nama' => 'Rina Sari', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2023003016', 'nama' => 'Ahmad Wijaya', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 3],
            ['nim' => '2023003017', 'nama' => 'Siti Nurhaliza', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2023003018', 'nama' => 'Budi Santoso', 'prodi' => 'Teknik Komputer', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 1],
            ['nim' => '2023003019', 'nama' => 'Dewi Sartika', 'prodi' => 'Teknik Informatika', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
            ['nim' => '2023003020', 'nama' => 'Rudi Hermawan', 'prodi' => 'Sistem Informasi', 'fakultas' => 'Fakultas Teknik', 'dosen_pembimbing' => 2],
        ];

        foreach ($mahasiswaData as $index => $data) {
            Mahasiswa::create([
                'nim' => $data['nim'],
                'nama_mhs' => $data['nama'],
                'prodi_mhs' => $data['prodi'],
                'fakultas_mhs' => $data['fakultas'],
                'no_hp_mhs' => '081234567' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'email_mhs' => strtolower(str_replace(' ', '.', $data['nama'])) . '.' . $data['nim'] . '@student.univ.ac.id',
                'password' => Hash::make('123456'),
                'role' => 'mahasiswa',
                'is_active' => true,
                'email_verified_at' => now(),
                'id_dosen_pembimbing' => $data['dosen_pembimbing'],
            ]);
        }

        echo "Seeder mahasiswa berhasil dibuat!\n";
        echo "Total mahasiswa: " . Mahasiswa::count() . "\n";
        echo "Akun untuk testing:\n";
        echo "1. ahmad.rizki@student.univ.ac.id / 123456 (Dosen Pembimbing: Dr. Budi Santoso)\n";
        echo "2. siti.nurhaliza@student.univ.ac.id / 123456 (Dosen Pembimbing: Dr. Budi Santoso)\n";
        echo "3. budi.santoso@student.univ.ac.id / 123456 (Dosen Pembimbing: Dr. Budi Santoso)\n";
        echo "4. dewi.sartika@student.univ.ac.id / 123456 (Dosen Pembimbing: Andi Prasetyo)\n";
        echo "5. rudi.hermawan@student.univ.ac.id / 123456 (Dosen Pembimbing: Andi Prasetyo)\n";
        echo "Maksimal proposal yang bisa dibuat: " . floor(Mahasiswa::count() / 3) . "\n";
    }
}