<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('hasil_finals', function (Blueprint $table) {
            $table->id();
            $table->enum('status_final', ['lolos', 'tidak_lolos']);
            $table->text('catatan_final')->nullable();
            $table->foreignId('id_proposal')->constrained('proposals', 'id_proposal')->onDelete('cascade');
            $table->foreignId('id_pt')->constrained('pts', 'id_pt')->onDelete('cascade');  // Relasi ke PT
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hasil_finals');
    }
};

