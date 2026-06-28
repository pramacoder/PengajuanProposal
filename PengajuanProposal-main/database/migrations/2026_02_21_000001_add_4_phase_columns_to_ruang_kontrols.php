<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ruang_kontrols', function (Blueprint $table) {
            $table->string('status_review', 50)->default('tertutup')->after('status_perbaikan');
            $table->string('status_penilaian_akhir', 50)->default('tertutup')->after('status_review');
            $table->date('tanggal_review_mulai')->nullable()->after('tanggal_perbaikan_selesai');
            $table->date('tanggal_review_selesai')->nullable()->after('tanggal_review_mulai');
            $table->date('tanggal_penilaian_akhir_mulai')->nullable()->after('tanggal_review_selesai');
            $table->date('tanggal_penilaian_akhir_selesai')->nullable()->after('tanggal_penilaian_akhir_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('ruang_kontrols', function (Blueprint $table) {
            $table->dropColumn([
                'status_review', 'status_penilaian_akhir',
                'tanggal_review_mulai', 'tanggal_review_selesai',
                'tanggal_penilaian_akhir_mulai', 'tanggal_penilaian_akhir_selesai',
            ]);
        });
    }
};
