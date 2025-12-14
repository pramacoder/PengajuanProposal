<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan nilai-nilai status_final yang diperlukan untuk hasil final
        // Cek apakah kolom status_final masih ENUM atau sudah VARCHAR
        $columnInfo = DB::select("SHOW COLUMNS FROM proposals WHERE Field = 'status_final'");
        
        if (!empty($columnInfo)) {
            $columnType = $columnInfo[0]->Type;
            
            // Jika masih ENUM, tambahkan nilai-nilai baru
            if (strpos($columnType, 'enum') !== false || strpos($columnType, 'ENUM') !== false) {
                // Ubah ke VARCHAR dulu untuk menghindari masalah dengan ENUM
                DB::statement("ALTER TABLE proposals MODIFY COLUMN status_final VARCHAR(50) DEFAULT 'draft'");
                
                // Kembalikan ke ENUM dengan semua nilai yang diperlukan
                DB::statement("ALTER TABLE proposals MODIFY COLUMN status_final ENUM(
                    'draft', 
                    'submitted', 
                    'review_administratif', 
                    'review_substantif', 
                    'revisi', 
                    'hasil_semi_final',
                    'revisi_akhir',
                    'validasi_akhir_dosen_univ',
                    'pimpinan_pt',
                    'lolos_tingkat_universitas',
                    'tidak_lolos_tingkat_universitas',
                    'lolos_pimnas',
                    'tidak_lolos_pimnas',
                    'lolos_pendanaan',
                    'tidak_lolos_pendanaan',
                    'lolos_pimnas_pendanaan',
                    'lolos_pimnas_tidak_pendanaan',
                    'tidak_lolos_pimnas_lolos_pendanaan',
                    'lolos',
                    'tidak_lolos',
                    'revisi_submitted'
                ) DEFAULT 'draft'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu rollback karena ini adalah penambahan nilai enum
        // Jika perlu, bisa dikembalikan ke enum sebelumnya
    }
};

