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
            if (Schema::hasColumn('proposals', 'judul_proposal')) {
                $table->dropColumn('judul_proposal');
            }
            if (Schema::hasColumn('proposals', 'dana_diajukan')) {
                $table->dropColumn('dana_diajukan');
            }
        });

        Schema::table('hasil_semi_finals', function (Blueprint $table) {
            if (Schema::hasColumn('hasil_semi_finals', 'dana_yang_dapat_diberikan')) {
                $table->dropColumn('dana_yang_dapat_diberikan');
            }
        });

        Schema::table('hasil_finals', function (Blueprint $table) {
            if (Schema::hasColumn('hasil_finals', 'dana_yang_didapatkan')) {
                $table->dropColumn('dana_yang_didapatkan');
            }
        });

        Schema::table('form_penilaian', function (Blueprint $table) {
            if (Schema::hasColumn('form_penilaian', 'mongo_config_id')) {
                $table->dropColumn('mongo_config_id');
            }
        });

        Schema::table('simbelmawa_reports', function (Blueprint $table) {
            if (Schema::hasColumn('simbelmawa_reports', 'mongo_report_id')) {
                $table->dropColumn('mongo_report_id');
            }
        });

        Schema::table('dokumens', function (Blueprint $table) {
            if (Schema::hasColumn('dokumens', 'file_lampiran')) {
                $table->dropColumn('file_lampiran');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            if (!Schema::hasColumn('proposals', 'judul_proposal')) {
                $table->text('judul_proposal')->nullable();
            }
            if (!Schema::hasColumn('proposals', 'dana_diajukan')) {
                $table->decimal('dana_diajukan', 15, 2)->nullable();
            }
        });

        Schema::table('hasil_semi_finals', function (Blueprint $table) {
            if (!Schema::hasColumn('hasil_semi_finals', 'dana_yang_dapat_diberikan')) {
                $table->decimal('dana_yang_dapat_diberikan', 15, 2)->nullable();
            }
        });

        Schema::table('hasil_finals', function (Blueprint $table) {
            if (!Schema::hasColumn('hasil_finals', 'dana_yang_didapatkan')) {
                $table->decimal('dana_yang_didapatkan', 15, 2)->nullable();
            }
        });

        Schema::table('form_penilaian', function (Blueprint $table) {
            if (!Schema::hasColumn('form_penilaian', 'mongo_config_id')) {
                $table->string('mongo_config_id')->nullable();
            }
        });

        Schema::table('simbelmawa_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('simbelmawa_reports', 'mongo_report_id')) {
                $table->string('mongo_report_id')->nullable();
            }
        });

        Schema::table('dokumens', function (Blueprint $table) {
            if (!Schema::hasColumn('dokumens', 'file_lampiran')) {
                $table->string('file_lampiran')->nullable();
            }
        });
    }
};
