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
        Schema::table('hasil_finals', function (Blueprint $table) {
            // JSON field untuk menyimpan skor per kriteria
            // Format: {"kriteria_1": 8, "kriteria_2": 7, ...}
            $table->json('skor_per_kriteria')->nullable()->after('nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_finals', function (Blueprint $table) {
            $table->dropColumn('skor_per_kriteria');
        });
    }
};

