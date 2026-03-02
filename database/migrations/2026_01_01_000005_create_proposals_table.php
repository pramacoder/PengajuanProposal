<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id('id_proposal');

            $table->string('judul_proposal');
            $table->string('judul')->nullable();
            $table->date('tanggal_pengajuan');
            $table->string('skim', 10);
            $table->string('dosen_pembimbing')->nullable();
            $table->decimal('dana_diajukan', 15, 2)->nullable();
            $table->decimal('dana_diajukan_operator', 15, 2)->nullable();
            $table->decimal('dana_diajukan_belmawa', 15, 2)->nullable();
            $table->string('tahun_ajaran')->nullable();

            $table->string('status', 50)->default('pending');
            $table->string('status_validasi', 50)->default('pending');
            $table->string('status_validasi_2', 50)->default('pending');
            $table->string('status_final', 50)->default('draft');

            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_validasi')->nullable();
            $table->string('path_review_dosen')->nullable();
            $table->string('nama_file_review_dosen')->nullable();
            $table->timestamp('tanggal_review_dosen')->nullable();

            $table->foreignId('id_mahasiswa')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_dosen')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_dosen_pendamping_universitas')->nullable()->constrained('users')->onDelete('set null');

            $table->integer('team_id')->nullable();

            $table->foreignId('id_reviewer_administratif')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('id_reviewer_substantif_1')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('id_reviewer_substantif_2')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('id_reviewer_substantif_seleksi_1')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('id_reviewer_substantif_seleksi_2')->nullable()->constrained('users')->onDelete('set null');

            $table->string('ketua_nama')->nullable();
            $table->string('ketua_nim')->nullable();
            $table->string('ketua_prodi')->nullable();
            $table->string('ketua_fakultas')->nullable();
            $table->string('ketua_email')->nullable();
            $table->string('ketua_no_hp')->nullable();

            $table->string('anggota1_nama')->nullable();
            $table->string('anggota1_nim')->nullable();
            $table->string('anggota1_prodi')->nullable();
            $table->string('anggota1_fakultas')->nullable();
            $table->string('anggota1_email')->nullable();
            $table->string('anggota1_no_hp')->nullable();

            $table->string('anggota2_nama')->nullable();
            $table->string('anggota2_nim')->nullable();
            $table->string('anggota2_prodi')->nullable();
            $table->string('anggota2_fakultas')->nullable();
            $table->string('anggota2_email')->nullable();
            $table->string('anggota2_no_hp')->nullable();

            $table->string('anggota3_nama')->nullable();
            $table->string('anggota3_nim')->nullable();
            $table->string('anggota3_prodi')->nullable();
            $table->string('anggota3_fakultas')->nullable();
            $table->string('anggota3_email')->nullable();
            $table->string('anggota3_no_hp')->nullable();

            $table->string('anggota4_nama')->nullable();
            $table->string('anggota4_nim')->nullable();
            $table->string('anggota4_prodi')->nullable();
            $table->string('anggota4_fakultas')->nullable();
            $table->string('anggota4_email')->nullable();
            $table->string('anggota4_no_hp')->nullable();

            $table->timestamps();

            $table->index(['status_validasi', 'status_final'], 'idx_proposal_status');
            $table->index('skim', 'idx_proposal_skim');
            $table->index('tanggal_pengajuan', 'idx_proposal_tanggal');
            $table->index('team_id', 'idx_proposal_team');
            $table->index('ketua_nim', 'idx_ketua_nim');
            $table->index('tahun_ajaran', 'idx_proposal_tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
