<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Helpers\StorageHelper;

$startTime = microtime(true);

try {
    $response = StorageHelper::response('proposals/8r5pbMzr9Git6T2qBYmblpQGvZJ30jl5fAQyW4CK.pdf');
    echo "Response status: " . $response->getStatusCode() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$endTime = microtime(true);
echo "Time taken for StorageHelper::response: " . ($endTime - $startTime) . " seconds\n";
