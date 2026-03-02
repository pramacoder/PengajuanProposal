<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumens', function (Blueprint $table) {
            $table->id('id_dokumen');
            $table->string('skim', 10);
            $table->string('path_file');
            $table->string('path_file_original')->nullable();
            $table->string('file_proposal')->nullable();
            $table->string('file_lampiran')->nullable();
            $table->timestamp('tgl_upload')->nullable();
            $table->foreignId('id_proposal')->constrained('proposals', 'id_proposal')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumens');
    }
};
