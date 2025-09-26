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
        // Cek apakah ada constraint unique pada nim saja (bukan composite)
        $indexes = \Illuminate\Support\Facades\DB::select("SHOW INDEX FROM teams WHERE Column_name = 'nim' AND Non_unique = 0");
        
        foreach ($indexes as $index) {
            // Jika ini adalah constraint unique pada nim saja (bukan composite)
            if ($index->Key_name !== 'unique_proposal_nim') {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE teams DROP INDEX {$index->Key_name}");
                echo "Dropped index: {$index->Key_name}\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan constraint unique pada nim
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE teams ADD UNIQUE KEY teams_nim_unique (nim)");
    }
};
