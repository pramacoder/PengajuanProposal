<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_finals', function (Blueprint $table) {
            $table->id();
            $table->string('status_pimnas', 50)->nullable();
            $table->string('status_pendanaan', 50)->nullable();
            $table->string('status_final', 50)->nullable();
            $table->decimal('dana_yang_didapatkan', 15, 2)->nullable();
            $table->decimal('dana_yang_dapat_diberikan', 15, 2)->nullable();
            $table->text('catatan_final')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->jsonb('skor_per_kriteria')->nullable();
            $table->foreignId('id_proposal')->constrained('proposals', 'id_proposal')->onDelete('cascade');
            $table->foreignId('id_pimpinan_pt')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('id_pt')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_finals');
    }
};
