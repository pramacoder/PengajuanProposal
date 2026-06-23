<?php
/**
 * Script to create a single default Operator user on Supabase pgsql
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== CREATING DEFAULT OPERATOR USER ON SUPABASE ===\n\n";

try {
    // Check if operator already exists
    $operator = User::where('email', 'operator@test.com')->first();
    if ($operator) {
        echo "Operator user already exists.\n";
    } else {
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
        echo "Successfully created default Operator user:\n";
        echo "  Email: operator@test.com\n";
        echo "  Password: password123\n";
    }
} catch (\Throwable $e) {
    echo "Error creating operator user: " . $e->getMessage() . "\n";
}

echo "\n=== DONE ===\n";
