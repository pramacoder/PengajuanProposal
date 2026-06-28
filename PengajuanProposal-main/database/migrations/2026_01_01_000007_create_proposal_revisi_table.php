<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_revisi', function (Blueprint $table) {
            $table->id('id_revisi');
            $table->foreignId('id_proposal')->constrained('proposals', 'id_proposal')->onDelete('cascade');
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('jenis_revisi', 50)->default('revisi_biasa');
            $table->timestamp('tanggal_submit')->nullable();
            $table->timestamps();

            $table->index('id_proposal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_revisi');
    }
};
