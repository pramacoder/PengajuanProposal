<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_substantifs', function (Blueprint $table) {
            $table->id();
            $table->text('note_substantif')->nullable();
            $table->string('jenis_review', 50)->default('pertama');
            $table->jsonb('skor_per_kriteria')->nullable();
            $table->decimal('total_nilai', 6, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->foreignId('id_proposal')->constrained('proposals', 'id_proposal')->onDelete('cascade');
            $table->foreignId('id_reviewer')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['id_proposal', 'jenis_review']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_substantifs');
    }
};
