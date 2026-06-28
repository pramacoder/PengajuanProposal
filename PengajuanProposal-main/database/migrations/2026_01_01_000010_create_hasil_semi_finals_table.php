<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_semi_finals', function (Blueprint $table) {
            $table->id();
            $table->string('status_final', 50);
            $table->text('catatan_final')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->json('skor_per_kriteria')->nullable();
            $table->decimal('dana_yang_dapat_diberikan', 15, 2)->nullable();
            $table->foreignId('id_dosen_pendamping_universitas')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('id_proposal')->constrained('proposals', 'id_proposal')->onDelete('cascade');
            $table->foreignId('id_pt')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_semi_finals');
    }
};
