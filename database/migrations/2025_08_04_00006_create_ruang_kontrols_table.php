<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ruang_kontrols', function (Blueprint $table) {
            $table->bigIncrements('id_ruang_kontrol'); 
            $table->enum('status_perbaikan', ['terbuka', 'tertutup', 'tidak_perlu_perbaikan', 'perlu_perbaikan'])->default('tertutup');
            $table->enum('status_pendaftaran', ['terbuka', 'tertutup'])->default('tertutup');
            $table->date('tanggal_pendaftaran_mulai')->nullable();
            $table->date('tanggal_pendaftaran_selesai')->nullable();
            $table->date('tanggal_perbaikan_mulai')->nullable();
            $table->date('tanggal_perbaikan_selesai')->nullable();
            $table->foreignId('id_pt')->constrained('pts', 'id_pt')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ruang_kontrols');
    }
};

