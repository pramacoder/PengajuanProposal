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
        // Cek apakah tabel hasil_finals ada
        if (Schema::hasTable('hasil_finals')) {
            // Update data existing terlebih dahulu sebelum rename dan ubah enum
            // Map status lama ke status baru
            DB::table('hasil_finals')
                ->where('status_final', 'lolos')
                ->update(['status_final' => 'lolos_tingkat_universitas']);
            
            DB::table('hasil_finals')
                ->where('status_final', 'tidak_lolos')
                ->update(['status_final' => 'tidak_lolos_tingkat_universitas']);
            
            // Rename table dari hasil_finals ke hasil_semi_finals
            Schema::rename('hasil_finals', 'hasil_semi_finals');
        } else {
            // Jika tabel tidak ada, cek apakah hasil_semi_finals sudah ada
            if (!Schema::hasTable('hasil_semi_finals')) {
                // Buat tabel baru jika belum ada
                Schema::create('hasil_semi_finals', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('id_proposal')->constrained('proposals', 'id_proposal')->onDelete('cascade');
                    $table->foreignId('id_pt')->constrained('pts', 'id_pt')->onDelete('cascade');
                    $table->enum('status_final', ['lolos_tingkat_universitas', 'tidak_lolos_tingkat_universitas'])->notNull();
                    $table->text('catatan_final')->nullable();
                    $table->decimal('nilai', 5, 2)->nullable();
                    $table->json('skor_per_kriteria')->nullable();
                    $table->timestamps();
                });
            }
        }
        
        // Hapus kolom dana_yang_dapat_diberikan jika ada
        if (Schema::hasTable('hasil_semi_finals')) {
            // Ubah enum menjadi VARCHAR sementara untuk update data
            DB::statement("ALTER TABLE hasil_semi_finals MODIFY COLUMN status_final VARCHAR(50) NOT NULL");
            
            // Update semua data yang tidak sesuai dengan enum baru
            // Map semua status yang tidak valid ke status default
            DB::table('hasil_semi_finals')
                ->where('status_final', 'lolos')
                ->update(['status_final' => 'lolos_tingkat_universitas']);
            
            DB::table('hasil_semi_finals')
                ->where('status_final', 'tidak_lolos')
                ->update(['status_final' => 'tidak_lolos_tingkat_universitas']);
            
            // Update status lain yang tidak valid ke default
            DB::table('hasil_semi_finals')
                ->whereNotIn('status_final', ['lolos_tingkat_universitas', 'tidak_lolos_tingkat_universitas'])
                ->update(['status_final' => 'tidak_lolos_tingkat_universitas']);
            
            Schema::table('hasil_semi_finals', function (Blueprint $table) {
                if (Schema::hasColumn('hasil_semi_finals', 'dana_yang_dapat_diberikan')) {
                    $table->dropColumn('dana_yang_dapat_diberikan');
                }
            });
            
            // Update enum status_final untuk hasil semi final
            // Status: 'lolos_tingkat_universitas', 'tidak_lolos_tingkat_universitas'
            DB::statement("ALTER TABLE hasil_semi_finals MODIFY COLUMN status_final ENUM('lolos_tingkat_universitas', 'tidak_lolos_tingkat_universitas') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan enum status_final
        DB::statement("ALTER TABLE hasil_semi_finals MODIFY COLUMN status_final ENUM('lolos', 'tidak_lolos') NOT NULL");
        
        // Tambah kembali kolom dana_yang_dapat_diberikan
        Schema::table('hasil_semi_finals', function (Blueprint $table) {
            $table->decimal('dana_yang_dapat_diberikan', 15, 2)->nullable()->after('nilai');
        });
        
        // Rename kembali ke hasil_finals
        Schema::rename('hasil_semi_finals', 'hasil_finals');
    }
};

