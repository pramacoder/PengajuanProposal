<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dokumens', function (Blueprint $table) {
            $table->bigIncrements('id_dokumen')->primary();
            $table->enum('skim', ['RE', 'RSH', 'KC', 'PM', 'PI', 'K','KI', 'VGK', 'AI', 'GFT']);
            $table->string('path_file', 255); 
            $table->timestamp('tgl_upload')->nullable();
            $table->foreignId('id_proposal')->constrained('proposals', 'id_proposal')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dokumens');
    }
};
