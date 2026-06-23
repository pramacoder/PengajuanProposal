<?php
/**
 * Script to create a default Pimpinan PT user on Supabase pgsql
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== CREATING DEFAULT PIMPINAN PT USER ON SUPABASE ===\n\n";

try {
    // Check if pimpinan already exists
    $pimpinan = User::where('email', 'pimpinan@test.com')->first();
    if ($pimpinan) {
        echo "Pimpinan PT user already exists.\n";
    } else {
        User::create([
            'identifier' => 'PIM-001',
            'name' => 'Prof. Dr. Budi Pimpinan PT',
            'email' => 'pimpinan@test.com',
            'phone' => '081234567891',
            'password' => Hash::make('password123'),
            'role' => 'pimpinan_pt',
            'is_active' => true,
            'metadata' => []
        ]);
        echo "Successfully created default Pimpinan PT user:\n";
        echo "  Email: pimpinan@test.com\n";
        echo "  Password: password123\n";
    }
} catch (\Throwable $e) {
    echo "Error creating pimpinan user: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";
