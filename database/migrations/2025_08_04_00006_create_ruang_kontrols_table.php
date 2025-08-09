<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ruang_kontrols', function (Blueprint $table) {
            $table->bigIncrements('id_ruang_kontrol')->primary(); 
            $table->enum('status_perbaikan', ['perlu_perbaikan', 'tidak_perlu_perbaikan']);
            $table->enum('status_pendaftaran', ['terbuka', 'tertutup']);
            $table->foreignId('id_pt')->constrained('pts', 'id_pt')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ruang_kontrols');
    }
};

