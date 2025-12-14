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
        Schema::table('proposal_revisi', function (Blueprint $table) {
            // Tambahkan kolom jenis_revisi untuk membedakan revisi biasa dan revisi akhir
            if (!Schema::hasColumn('proposal_revisi', 'jenis_revisi')) {
                $table->enum('jenis_revisi', ['revisi_biasa', 'revisi_akhir'])->default('revisi_biasa')->after('path_file');
            }
        });

        // Update data existing berdasarkan path_file
        // Jika path_file mengandung 'revisi_akhir', set jenis_revisi = 'revisi_akhir'
        DB::statement("UPDATE proposal_revisi SET jenis_revisi = 'revisi_akhir' WHERE path_file LIKE '%revisi_akhir%'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_revisi', function (Blueprint $table) {
            if (Schema::hasColumn('proposal_revisi', 'jenis_revisi')) {
                $table->dropColumn('jenis_revisi');
            }
        });
    }
};

