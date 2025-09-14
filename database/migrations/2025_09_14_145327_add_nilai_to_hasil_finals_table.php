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
            $table->decimal('nilai', 5, 2)->nullable()->after('catatan_final');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_finals', function (Blueprint $table) {
            $table->dropColumn('nilai');
        });
    }
};
