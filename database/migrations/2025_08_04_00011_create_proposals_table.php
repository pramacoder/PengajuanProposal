<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->bigIncrements('id_proposal');
            
            // Informasi dasar proposal
            $table->string('judul_proposal');
            $table->string('judul')->nullable(); // Field baru untuk judul
            $table->date('tanggal_pengajuan');
            $table->enum('skim', ['RE', 'RSH', 'K', 'PM', 'PI', 'KC', 'KI', 'VGK', 'GFT', 'AI']);
            $table->string('dosen_pembimbing')->nullable(); // Field baru untuk nama dosen pendamping
            $table->decimal('dana_diajukan', 15, 2)->nullable(); // Field baru untuk dana
            $table->string('tahun_ajaran')->nullable(); // Field baru untuk tahun ajaran
            
            // Status proposal sesuai alur
            $table->enum('status_validasi', ['pending', 'valid', 'tidak_valid'])->default('pending');
            $table->enum('status_final', ['draft', 'submitted', 'review_administratif', 'review_substantif', 'revisi', 'lolos', 'tidak_lolos'])->default('draft');
            $table->enum('status', ['pending', 'valid', 'tidak_valid', 'submitted', 'review_administratif', 'review_substantif', 'revisi', 'lolos', 'tidak_lolos'])->default('pending');
            
            // Catatan dan informasi tambahan
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_validasi')->nullable();
            
            // Foreign keys untuk relasi
            $table->foreignId('id_mahasiswa')->constrained('mahasiswas', 'id_mahasiswa')->onDelete('cascade');
            $table->foreignId('id_dosen')->constrained('dosens', 'id_dosen')->onDelete('cascade');
            
            // Team ID untuk mengelompokkan anggota tim
            $table->integer('team_id')->nullable();
            
            // Kolom reviewer assignment untuk tracking yang lebih mudah
            $table->foreignId('id_reviewer_administratif')->nullable()->constrained('reviewers', 'id_reviewer')->onDelete('set null');
            $table->foreignId('id_reviewer_substantif_1')->nullable()->constrained('reviewers', 'id_reviewer')->onDelete('set null');
            $table->foreignId('id_reviewer_substantif_2')->nullable()->constrained('reviewers', 'id_reviewer')->onDelete('set null');
            
            // Data ketua tim (wajib)
            $table->string('ketua_nama')->nullable();
            $table->string('ketua_nim')->nullable();
            $table->string('ketua_prodi')->nullable();
            $table->string('ketua_fakultas')->nullable();
            $table->string('ketua_email')->nullable();
            $table->string('ketua_no_hp')->nullable();
            
            // Data anggota 1 (wajib)
            $table->string('anggota1_nama')->nullable();
            $table->string('anggota1_nim')->nullable();
            $table->string('anggota1_prodi')->nullable();
            $table->string('anggota1_fakultas')->nullable();
            $table->string('anggota1_email')->nullable();
            $table->string('anggota1_no_hp')->nullable();
            
            // Data anggota 2 (wajib)
            $table->string('anggota2_nama')->nullable();
            $table->string('anggota2_nim')->nullable();
            $table->string('anggota2_prodi')->nullable();
            $table->string('anggota2_fakultas')->nullable();
            $table->string('anggota2_email')->nullable();
            $table->string('anggota2_no_hp')->nullable();
            
            // Data anggota 3 (opsional)
            $table->string('anggota3_nama')->nullable();
            $table->string('anggota3_nim')->nullable();
            $table->string('anggota3_prodi')->nullable();
            $table->string('anggota3_fakultas')->nullable();
            $table->string('anggota3_email')->nullable();
            $table->string('anggota3_no_hp')->nullable();
            
            // Data anggota 4 (opsional)
            $table->string('anggota4_nama')->nullable();
            $table->string('anggota4_nim')->nullable();
            $table->string('anggota4_prodi')->nullable();
            $table->string('anggota4_fakultas')->nullable();
            $table->string('anggota4_email')->nullable();
            $table->string('anggota4_no_hp')->nullable();
            
            $table->timestamps();
            
            // Index untuk optimasi query (dengan nama yang lebih pendek)
            $table->index(['status_validasi', 'status_final'], 'idx_proposal_status');
            $table->index(['skim'], 'idx_proposal_skim');
            $table->index(['tanggal_pengajuan'], 'idx_proposal_tanggal');
            $table->index(['team_id'], 'idx_proposal_team');
            $table->index(['ketua_nim'], 'idx_ketua_nim');
            $table->index(['anggota1_nim'], 'idx_anggota1_nim');
            $table->index(['anggota2_nim'], 'idx_anggota2_nim');
            $table->index(['anggota3_nim'], 'idx_anggota3_nim');
            $table->index(['anggota4_nim'], 'idx_anggota4_nim');
        });
    }

    public function down()
    {
        Schema::dropIfExists('proposals');
    }
};
