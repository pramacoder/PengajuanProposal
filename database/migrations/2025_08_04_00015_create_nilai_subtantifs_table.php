<?php

// 2024_01_01_000009_create_nilai_substantifs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nilai_substantifs', function (Blueprint $table) {
            $table->id();
            $table->text('hasil_substantif');
            $table->text('note_substantif')->nullable(); 
                        $table->foreignId('id_proposal')->constrained('proposals', 'id_proposal')->onDelete('cascade');
            $table->foreignId('id_reviewer')->constrained('reviewers', 'id_reviewer')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nilai_substantifs');
    }
};
