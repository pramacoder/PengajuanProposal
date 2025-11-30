<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek apakah tabel sudah ada
        if (!Schema::hasTable('hasil_finals')) {
            Schema::create('hasil_finals', function (Blueprint $table) {
                $table->id();
                $table->enum('status_pimnas', ['lolos', 'tidak_lolos'])->nullable();
                $table->enum('status_pendanaan', ['lolos', 'tidak_lolos'])->nullable();
                $table->decimal('dana_yang_didapatkan', 15, 2)->nullable();
                $table->text('catatan_final')->nullable();
                $table->decimal('nilai', 5, 2)->nullable();
                $table->json('skor_per_kriteria')->nullable();
                $table->foreignId('id_proposal')->constrained('proposals', 'id_proposal')->onDelete('cascade');
                $table->foreignId('id_pimpinan_pt')->constrained('pts', 'id_pt')->onDelete('cascade');
                $table->timestamps();
            });
        } else {
            // Jika tabel sudah ada, tambahkan kolom yang belum ada
            Schema::table('hasil_finals', function (Blueprint $table) {
                if (!Schema::hasColumn('hasil_finals', 'status_pimnas')) {
                    $table->enum('status_pimnas', ['lolos', 'tidak_lolos'])->nullable()->after('id');
                }
                if (!Schema::hasColumn('hasil_finals', 'status_pendanaan')) {
                    $table->enum('status_pendanaan', ['lolos', 'tidak_lolos'])->nullable()->after('status_pimnas');
                }
                if (!Schema::hasColumn('hasil_finals', 'dana_yang_didapatkan')) {
                    $table->decimal('dana_yang_didapatkan', 15, 2)->nullable()->after('status_pendanaan');
                }
                if (!Schema::hasColumn('hasil_finals', 'id_pimpinan_pt')) {
                    $table->foreignId('id_pimpinan_pt')->nullable()->constrained('pts', 'id_pt')->onDelete('cascade')->after('id_proposal');
                }
                if (!Schema::hasColumn('hasil_finals', 'skor_per_kriteria')) {
                    $table->json('skor_per_kriteria')->nullable()->after('nilai');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_finals');
    }
};

