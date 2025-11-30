<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pts', function (Blueprint $table) {
            // Update enum role untuk include 'pimpinan_pt'
            DB::statement("ALTER TABLE pts MODIFY COLUMN role ENUM('operator', 'pimpinan_pt') DEFAULT 'operator'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pts', function (Blueprint $table) {
            // Kembalikan ke enum role sebelumnya
            DB::statement("ALTER TABLE pts MODIFY COLUMN role ENUM('operator') DEFAULT 'operator'");
        });
    }
};

