<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->bigIncrements('id_mahasiswa')->primary();
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
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mahasiswas');
    }
};