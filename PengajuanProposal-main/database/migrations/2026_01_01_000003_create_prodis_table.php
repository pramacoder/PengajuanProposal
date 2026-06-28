<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prodis', function (Blueprint $table) {
            $table->id('id_prodi');
            $table->string('nama_prodi');
            $table->string('kode_prodi')->unique();
            $table->bigInteger('id_fakultas');
            $table->timestamps();

            $table->foreign('id_fakultas')->references('id_fakultas')->on('fakultas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prodis');
    }
};
