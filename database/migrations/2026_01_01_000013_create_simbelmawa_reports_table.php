<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simbelmawa_reports', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran');
            $table->bigInteger('id_ruang_kontrol')->nullable();
            $table->integer('jumlah_proposal_tervalidasi_pimpinan_pt')->default(0);
            $table->integer('jumlah_proposal_dapat_pendanaan')->default(0);
            $table->decimal('total_dana_pendanaan', 15, 2)->default(0);
            $table->integer('jumlah_proposal_lolos_pimnas')->default(0);
            $table->jsonb('judul_proposal_lolos_pimnas')->nullable();
            $table->integer('jumlah_prestasi')->default(0);
            $table->jsonb('prestasi')->nullable();
            $table->string('mongo_report_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->foreign('id_ruang_kontrol')->references('id_ruang_kontrol')->on('ruang_kontrols')->onDelete('set null');
            $table->index('tahun_ajaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simbelmawa_reports');
    }
};
