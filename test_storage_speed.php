<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Helpers\StorageHelper;

$startTime = microtime(true);

try {
    $exists = StorageHelper::exists('proposals/nonexistent_file.pdf');
    echo "Exists: " . ($exists ? "true" : "false") . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$endTime = microtime(true);
echo "Time taken for StorageHelper::exists: " . ($endTime - $startTime) . " seconds\n";
