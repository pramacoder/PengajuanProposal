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
            $table->decimal('dana_didapatkan_belmawa', 15, 2)->nullable()->after('dana_yang_didapatkan');
            $table->decimal('dana_didapatkan_operator', 15, 2)->nullable()->after('dana_didapatkan_belmawa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_finals', function (Blueprint $table) {
            $table->dropColumn(['dana_didapatkan_belmawa', 'dana_didapatkan_operator']);
        });
    }
};
