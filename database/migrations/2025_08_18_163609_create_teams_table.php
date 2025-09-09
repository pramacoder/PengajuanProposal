<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->bigIncrements('id_team')->primary();
            $table->foreignId('id_proposal')->constrained('proposals', 'id_proposal')->onDelete('cascade');
            $table->foreignId('id_mahasiswa')->nullable()->constrained('mahasiswas', 'id_mahasiswa')->onDelete('set null');
            
            // Data anggota tim
            $table->string('nama');
            $table->string('nim')->unique();
            $table->string('prodi');
            $table->string('fakultas');
            $table->string('email');
            $table->string('no_hp');
            
            // Role dalam tim (ketua, anggota1, anggota2, dll)
            $table->enum('role', ['ketua', 'anggota1', 'anggota2', 'anggota3', 'anggota4']);
            
            // Status keanggotaan
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            $table->timestamps();
            
            // Index untuk optimasi query
            $table->index(['id_proposal', 'role'], 'idx_team_proposal_role');
            $table->index(['nim'], 'idx_team_nim');
            $table->index(['id_mahasiswa'], 'idx_team_mahasiswa');
            
            // Unique constraint untuk mencegah duplikasi NIM dalam satu proposal
            $table->unique(['id_proposal', 'nim'], 'unique_proposal_nim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
