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
            $table->decimal('dana_yang_dapat_diberikan', 15, 2)->nullable()->after('nilai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_finals', function (Blueprint $table) {
            $table->dropColumn('dana_yang_dapat_diberikan');
        });
    }
};
