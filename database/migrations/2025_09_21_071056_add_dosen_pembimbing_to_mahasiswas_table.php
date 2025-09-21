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
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_dosen_pembimbing')->nullable()->after('id_mahasiswa');
            $table->foreign('id_dosen_pembimbing')->references('id_dosen')->on('dosens')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mahasiswas', function (Blueprint $table) {
            $table->dropForeign(['id_dosen_pembimbing']);
            $table->dropColumn('id_dosen_pembimbing');
        });
    }
};
