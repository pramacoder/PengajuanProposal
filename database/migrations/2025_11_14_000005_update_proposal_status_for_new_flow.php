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
        // Ubah enum menjadi VARCHAR sementara untuk update data
        DB::statement("ALTER TABLE proposals MODIFY COLUMN status VARCHAR(50) DEFAULT 'pending'");
        DB::statement("ALTER TABLE proposals MODIFY COLUMN status_final VARCHAR(50) DEFAULT 'draft'");
        
        // Map status lama ke status baru (jika ada yang perlu di-mapping)
        // Status 'lolos' dan 'tidak_lolos' bisa tetap digunakan atau di-mapping sesuai kebutuhan
        // Untuk sekarang, biarkan data existing tetap seperti adanya
        
        // Update enum status untuk proposal
        DB::statement("ALTER TABLE proposals MODIFY COLUMN status ENUM(
            'pending', 
            'valid', 
            'tidak_valid', 
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
            'lolos',
            'tidak_lolos',
            'revisi_submitted'
        ) DEFAULT 'pending'");
        
        // Update enum status_final untuk proposal
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan enum status
        DB::statement("ALTER TABLE proposals MODIFY COLUMN status ENUM(
            'pending', 
            'valid', 
            'tidak_valid', 
            'submitted', 
            'review_administratif', 
            'review_substantif', 
            'revisi', 
            'lolos', 
            'tidak_lolos'
        ) DEFAULT 'pending'");
        
        // Kembalikan enum status_final
        DB::statement("ALTER TABLE proposals MODIFY COLUMN status_final ENUM(
            'draft', 
            'submitted', 
            'review_administratif', 
            'review_substantif', 
            'revisi', 
            'lolos', 
            'tidak_lolos'
        ) DEFAULT 'draft'");
    }
};

