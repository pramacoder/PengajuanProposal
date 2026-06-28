<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Helpers\StorageHelper;

$startTime = microtime(true);

try {
    $content = StorageHelper::get('proposals/nonexistent_file.pdf');
    echo "Get returned content of length: " . strlen($content) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$endTime = microtime(true);
echo "Time taken for StorageHelper::get: " . ($endTime - $startTime) . " seconds\n";
