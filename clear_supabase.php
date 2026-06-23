<?php
/**
 * Script untuk membersihkan data testing:
 * 1. Menghapus semua file di Local Storage (public disk fallback)
 * 2. Mengosongkan tabel proposals, dokumens, nilai_administratifs, nilai_substantifs, hasil_semi_finals, hasil_finals, proposal_revisi
 * 3. Menyisakan satu akun Operator default agar Anda bisa login dan membuat akun Pimpinan/Operator baru.
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

echo "=== MEMULAI PEMBERSIHAN DATA TESTING ===\n\n";

// 1. Bersihkan File di Local Storage (Public Disk Fallback)
try {
    echo "Membersihkan local public storage...\n";
    $disk = Storage::disk('public');
    $files = $disk->allFiles();
    $count = 0;
    foreach ($files as $file) {
        if (strpos($file, '.gitignore') === false) {
            $disk->delete($file);
            echo "  Deleted local file: {$file}\n";
            $count++;
        }
    }
    echo "Berhasil menghapus {$count} file dari local storage.\n\n";
} catch (\Throwable $e) {
    echo "Gagal membersihkan local storage: " . $e->getMessage() . "\n\n";
}

// 2. Bersihkan Tabel Transaksi Proposal
echo "Mengosongkan tabel proposal dan penilaian...\n";
try {
    DB::statement('PRAGMA foreign_keys = OFF;'); // Untuk SQLite
    
    $tables = ['proposals', 'dokumens', 'nilai_administratifs', 'nilai_substantifs', 'hasil_semi_finals', 'hasil_finals', 'proposal_revisi'];
    foreach ($tables as $table) {
        try {
            DB::table($table)->truncate();
            echo "  Table {$table} truncated.\n";
        } catch (\Throwable $ex) {
            echo "  Warning: could not truncate {$table} - " . $ex->getMessage() . "\n";
        }
    }
    echo "Tabel proposal dan penilaian selesai diproses.\n\n";
} catch (\Throwable $e) {
    echo "Gagal mengosongkan tabel: " . $e->getMessage() . "\n";
}

// 3. Bersihkan User & Sisakan Operator Utama
echo "Mengatur ulang tabel users...\n";
try {
    DB::table('users')->truncate();
    
    // Tambah 1 akun Operator Utama agar bisa login
    User::create([
        'identifier' => 'OPR-001',
        'name' => 'Budi Operator Utama',
        'email' => 'operator@test.com',
        'phone' => '081234567890',
        'password' => Hash::make('password123'),
        'role' => 'operator',
        'is_active' => true,
        'metadata' => []
    ]);
    
    echo "Tabel users dikosongkan. Menyisakan 1 akun Operator Utama:\n";
    echo "  Email: operator@test.com\n";
    echo "  Password: password123\n\n";
} catch (\Throwable $e) {
    echo "Gagal mengatur ulang tabel users: " . $e->getMessage() . "\n";
} finally {
    DB::statement('PRAGMA foreign_keys = ON;');
}

echo "=== PEMBERSIHAN SELESAI ===\n";
echo "Silakan jalankan sistem dan buat akun Pimpinan / Operator baru melalui dashboard Operator.\n";
