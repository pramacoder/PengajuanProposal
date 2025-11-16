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
        Schema::table('nilai_substantifs', function (Blueprint $table) {
            // JSON field untuk menyimpan skor per kriteria
            // Format: {"kriteria_1": 8, "kriteria_2": 7, ...}
            $table->json('skor_per_kriteria')->nullable()->after('note_substantif');
            
            // Total nilai sebelum dikonversi (0-1000)
            // Nilai = sum(bobot × skor) untuk semua kriteria
            $table->decimal('total_nilai', 6, 2)->nullable()->after('skor_per_kriteria');
            
            // Nilai akhir setelah dikonversi (0-100.00)
            // Nilai akhir = total_nilai / 10
            $table->decimal('nilai_akhir', 5, 2)->nullable()->after('total_nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_substantifs', function (Blueprint $table) {
            $table->dropColumn(['skor_per_kriteria', 'total_nilai', 'nilai_akhir']);
        });
    }
};

