<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->bigIncrements('id_proposal')->primary();
            $table->string('judul_proposal');
            $table->date('tanggal_pengajuan');
            $table->enum('skim', ['RE', 'RSH', 'KC', 'PM', 'PI', 'K','KI', 'VGK', 'AI', 'GFT']);
            $table->enum('status_validasi', ['pending', 'valid', 'tidak_valid'])->default('pending');
            $table->enum('status_final', ['draft', 'submitted', 'review_administratif', 'review_substantif', 'revisi', 'lolos', 'tidak_lolos'])->default('draft');
            $table->text('catatan')->nullable();
            $table->foreignId('id_mahasiswa')->constrained('mahasiswas', 'id_mahasiswa')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proposals');
    }
};
