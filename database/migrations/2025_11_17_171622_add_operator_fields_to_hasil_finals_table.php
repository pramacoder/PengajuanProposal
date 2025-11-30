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
        if (Schema::hasTable('hasil_finals')) {
        Schema::table('hasil_finals', function (Blueprint $table) {
                // Tambahkan field untuk operator (jika belum ada)
                if (!Schema::hasColumn('hasil_finals', 'status_final')) {
                    $table->enum('status_final', ['lolos', 'tidak_lolos'])->nullable()->after('status_pendanaan');
                }
                if (!Schema::hasColumn('hasil_finals', 'dana_yang_dapat_diberikan')) {
                    $table->decimal('dana_yang_dapat_diberikan', 15, 2)->nullable()->after('dana_yang_didapatkan');
                }
                if (!Schema::hasColumn('hasil_finals', 'id_pt')) {
                    // Cek apakah foreign key constraint sudah ada
                    $foreignKeys = DB::select("
                        SELECT CONSTRAINT_NAME 
                        FROM information_schema.KEY_COLUMN_USAGE 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'hasil_finals' 
                        AND COLUMN_NAME = 'id_pt'
                        AND CONSTRAINT_NAME LIKE '%foreign%'
                    ");
                    
                    if (empty($foreignKeys)) {
                        $table->foreignId('id_pt')->nullable()->constrained('pts', 'id_pt')->onDelete('cascade')->after('id_pimpinan_pt');
                    } else {
                        // Jika constraint sudah ada, tambahkan kolom tanpa foreign key dulu
                        $table->unsignedBigInteger('id_pt')->nullable()->after('id_pimpinan_pt');
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('hasil_finals')) {
        Schema::table('hasil_finals', function (Blueprint $table) {
                if (Schema::hasColumn('hasil_finals', 'id_pt')) {
                    $table->dropForeign(['id_pt']);
                    $table->dropColumn('id_pt');
                }
                if (Schema::hasColumn('hasil_finals', 'dana_yang_dapat_diberikan')) {
                    $table->dropColumn('dana_yang_dapat_diberikan');
                }
                if (Schema::hasColumn('hasil_finals', 'status_final')) {
                    $table->dropColumn('status_final');
                }
        });
        }
    }
};
