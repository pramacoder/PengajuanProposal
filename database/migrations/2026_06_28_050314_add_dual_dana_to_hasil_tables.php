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
        Schema::table('hasil_semi_finals', function (Blueprint $table) {
            $table->decimal('dana_disetujui_belmawa', 15, 2)->nullable()->after('dana_yang_dapat_diberikan');
            $table->decimal('dana_disetujui_operator', 15, 2)->nullable()->after('dana_disetujui_belmawa');
        });

        Schema::table('hasil_finals', function (Blueprint $table) {
            $table->decimal('dana_disetujui_belmawa', 15, 2)->nullable()->after('dana_yang_dapat_diberikan');
            $table->decimal('dana_disetujui_operator', 15, 2)->nullable()->after('dana_disetujui_belmawa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_semi_finals', function (Blueprint $table) {
            $table->dropColumn(['dana_disetujui_belmawa', 'dana_disetujui_operator']);
        });

        Schema::table('hasil_finals', function (Blueprint $table) {
            $table->dropColumn(['dana_disetujui_belmawa', 'dana_disetujui_operator']);
        });
    }
};
