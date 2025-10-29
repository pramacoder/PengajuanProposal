<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->bigIncrements('id_mahasiswa');
            $table->string('nim', 20)->unique();
            $table->string('nama_mhs');
            $table->string('prodi_mhs');
            $table->string('fakultas_mhs');
            $table->string('no_hp_mhs', 15);
            $table->string('email_mhs')->unique();
            $table->string('password');
            $table->enum('role', ['mahasiswa'])->default('mahasiswa');
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            
            // Dosen pembimbing
            $table->unsignedBigInteger('id_dosen_pembimbing')->nullable();
            $table->foreign('id_dosen_pembimbing')->references('id_dosen')->on('dosens')->onDelete('set null');
            
            // Team attributes
            $table->integer('team_id')->nullable();
            $table->boolean('is_ketua')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index('team_id');
            $table->index(['team_id', 'is_ketua']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('mahasiswas');
    }
};