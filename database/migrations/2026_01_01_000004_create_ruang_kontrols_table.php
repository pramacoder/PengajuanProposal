<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ruang_kontrols', function (Blueprint $table) {
            $table->id('id_ruang_kontrol');
            $table->string('tahun_ajaran')->nullable();
            $table->string('nama_history')->nullable();
            $table->string('status_perbaikan', 50)->default('tertutup');
            $table->string('status_pendaftaran', 50)->default('tertutup');
            $table->date('tanggal_pendaftaran_mulai')->nullable();
            $table->date('tanggal_pendaftaran_selesai')->nullable();
            $table->date('tanggal_perbaikan_mulai')->nullable();
            $table->date('tanggal_perbaikan_selesai')->nullable();
            $table->date('tanggal_review_pertama_mulai')->nullable();
            $table->date('tanggal_review_pertama_selesai')->nullable();
            $table->boolean('is_active')->default(false);
            $table->decimal('dana_min_operator', 15, 2)->nullable();
            $table->decimal('dana_max_operator', 15, 2)->nullable();
            $table->decimal('dana_min_belmawa', 15, 2)->nullable();
            $table->decimal('dana_max_belmawa', 15, 2)->nullable();
            $table->foreignId('id_pt')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['tahun_ajaran', 'is_active'], 'idx_rk_tahun_active');
            $table->index('tahun_ajaran', 'idx_rk_tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruang_kontrols');
    }
};
