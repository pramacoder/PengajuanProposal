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
        Schema::table('proposals', function (Blueprint $table) {
            $table->foreignId('id_dosen_pendamping_universitas')
                  ->nullable()
                  ->after('id_dosen')
                  ->constrained('dosens', 'id_dosen')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropForeign(['id_dosen_pendamping_universitas']);
            $table->dropColumn('id_dosen_pendamping_universitas');
        });
    }
};



