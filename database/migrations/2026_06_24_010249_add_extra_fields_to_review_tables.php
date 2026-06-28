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
        Schema::table('nilai_administratifs', function (Blueprint $table) {
            $table->json('extra_fields')->nullable()->after('checklist');
        });

        Schema::table('nilai_substantifs', function (Blueprint $table) {
            $table->json('extra_fields')->nullable()->after('skor_per_kriteria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_administratifs', function (Blueprint $table) {
            $table->dropColumn('extra_fields');
        });

        Schema::table('nilai_substantifs', function (Blueprint $table) {
            $table->dropColumn('extra_fields');
        });
    }
};
