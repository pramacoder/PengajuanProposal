<?php
/**
 * Script untuk memperbaiki bug minor yang ditemukan saat blackbox testing
 */

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Perbaikan Bug Minor Pasca Blackbox Testing ===\n\n";

// Bug Fix #1: Cek kolom nilai di nilai_substantifs
echo "[1] Memeriksa kolom di tabel nilai_substantifs...\n";
$cols = DB::select("SELECT column_name, data_type, is_nullable FROM information_schema.columns WHERE table_name='nilai_substantifs' AND table_schema='public' ORDER BY ordinal_position");
echo "    Kolom yang ada:\n";
$hasNilai = false;
foreach ($cols as $col) {
    echo "      - {$col->column_name} ({$col->data_type}) nullable={$col->is_nullable}\n";
    if ($col->column_name === 'nilai') $hasNilai = true;
}

if (!$hasNilai) {
    echo "    [!] Kolom 'nilai' TIDAK ADA! Menambahkan...\n";
    DB::statement("ALTER TABLE nilai_substantifs ADD COLUMN IF NOT EXISTS nilai DECIMAL(5,2) NULL");
    echo "    [✅] Kolom 'nilai' berhasil ditambahkan!\n";
} else {
    echo "    [✅] Kolom 'nilai' sudah ada.\n";
}

// Bug Fix #2: Cek dan set default untuk status_final di hasil_semi_finals
echo "\n[2] Memeriksa kolom status_final di hasil_semi_finals...\n";
$cols2 = DB::select("SELECT column_name, data_type, is_nullable, column_default FROM information_schema.columns WHERE table_name='hasil_semi_finals' AND table_schema='public' ORDER BY ordinal_position");
echo "    Kolom yang ada:\n";
foreach ($cols2 as $col) {
    echo "      - {$col->column_name} ({$col->data_type}) nullable={$col->is_nullable} default=" . ($col->column_default ?? 'NULL') . "\n";
}

// Set default untuk status_final
try {
    DB::statement("ALTER TABLE hasil_semi_finals ALTER COLUMN status_final SET DEFAULT 'lolos'");
    echo "    [✅] Default 'lolos' berhasil diset untuk status_final!\n";
} catch (Exception $e) {
    echo "    [!] Error set default: " . $e->getMessage() . "\n";
}

// Bug Fix #3: Tambahkan kolom catatan ke hasil_semi_finals jika belum ada
echo "\n[3] Memeriksa kolom catatan di hasil_semi_finals...\n";
$hasCatatan = false;
foreach ($cols2 as $col) {
    if ($col->column_name === 'catatan') { $hasCatatan = true; break; }
}
if (!$hasCatatan) {
    // Cek nama kolom yang benar
    $hasCatatanFinal = false;
    foreach ($cols2 as $col) {
        if ($col->column_name === 'catatan_final') { $hasCatatanFinal = true; break; }
    }
    if (!$hasCatatanFinal) {
        try {
            DB::statement("ALTER TABLE hasil_semi_finals ADD COLUMN IF NOT EXISTS catatan TEXT NULL");
            echo "    [✅] Kolom 'catatan' berhasil ditambahkan!\n";
        } catch (Exception $e) {
            echo "    [!] Error: " . $e->getMessage() . "\n";
        }
    } else {
        echo "    [✅] Kolom catatan_final sudah ada (digunakan sebagai catatan).\n";
    }
} else {
    echo "    [✅] Kolom 'catatan' sudah ada.\n";
}

// Bug Fix #4: Pastikan kolom nilai di nilai_administratifs juga ada
echo "\n[4] Memeriksa kolom di nilai_administratifs...\n";
$cols3 = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name='nilai_administratifs' AND table_schema='public' ORDER BY ordinal_position");
echo "    Kolom yang ada:\n";
foreach ($cols3 as $col) {
    echo "      - {$col->column_name} ({$col->data_type})\n";
}

echo "\n=== Selesai! Semua perbaikan telah diaplikasikan. ===\n";
