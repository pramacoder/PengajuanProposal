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
            $table->string('path_review_dosen')->nullable()->after('catatan');
            $table->string('nama_file_review_dosen')->nullable()->after('path_review_dosen');
            $table->timestamp('tanggal_review_dosen')->nullable()->after('nama_file_review_dosen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn(['path_review_dosen', 'nama_file_review_dosen', 'tanggal_review_dosen']);
        });
    }
};

