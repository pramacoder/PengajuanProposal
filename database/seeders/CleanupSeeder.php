<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Proposal;
use App\Models\Dokumen;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Reviewer;
use App\Models\NilaiAdministratif;
use App\Models\NilaiSubstantif;
use App\Models\HasilFinal;
use App\Models\Notification;
use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\PT;

class CleanupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai cleanup database...');
        
        // Nonaktifkan foreign key checks sementara
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        
        $this->command->info('Menghapus data yang ada...');
        
        // Hapus data dalam urutan yang benar (foreign key constraints)
        // Hapus data yang bergantung pada proposal
        Notification::truncate();
        HasilFinal::truncate();
        NilaiSubstantif::truncate();
        NilaiAdministratif::truncate();
        Dokumen::truncate();
        
        // Hapus proposal
        Proposal::truncate();
        
        // Hapus data master
        Reviewer::truncate();
        Dosen::truncate();
        Mahasiswa::truncate();
        PT::truncate();
        Prodi::truncate();
        Fakultas::truncate();
        
        // Reset auto increment
        DB::statement('ALTER TABLE notifications AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE hasil_finals AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE nilai_substantifs AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE nilai_administratifs AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE dokumens AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE proposals AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE reviewers AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE dosens AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE mahasiswas AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE pts AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE prodis AUTO_INCREMENT = 1');
        DB::statement('ALTER TABLE fakultas AUTO_INCREMENT = 1');
        
        // Aktifkan kembali foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        
        $this->command->info('Cleanup database selesai!');
        $this->command->info('Semua data lama telah dihapus dan auto increment direset.');
    }
}
