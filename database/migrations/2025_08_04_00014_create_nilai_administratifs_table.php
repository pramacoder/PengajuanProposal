<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nilai_administratifs', function (Blueprint $table) {
            $table->id();
            $table->text('note_administratif')->nullable();
            $table->json('checklist')->nullable(); 
                        $table->foreignId('id_proposal')->constrained('proposals', 'id_proposal')->onDelete('cascade');
            $table->foreignId('id_reviewer')->constrained('reviewers', 'id_reviewer')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nilai_administratifs');
    }
};
