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
        Schema::table('ruang_kontrols', function (Blueprint $table) {
            $table->string('tahun_ajaran')->nullable()->after('id_ruang_kontrol');
            $table->string('nama_history')->nullable()->after('tahun_ajaran');
            $table->boolean('is_active')->default(false)->after('tanggal_perbaikan_selesai');
            $table->index(['tahun_ajaran', 'is_active'], 'idx_ruang_kontrol_tahun_active');
            $table->index('tahun_ajaran', 'idx_ruang_kontrol_tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ruang_kontrols', function (Blueprint $table) {
            $table->dropIndex('idx_ruang_kontrol_tahun_active');
            $table->dropIndex('idx_ruang_kontrol_tahun');
            $table->dropColumn(['tahun_ajaran', 'nama_history', 'is_active']);
        });
    }
};

