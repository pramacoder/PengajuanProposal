<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$startTime = microtime(true);

$user = User::where('email', 'reviewer-test1@test.com')->first();
Auth::login($user);

$controller = app()->make(\App\Http\Controllers\Reviewer\AdministratifController::class);
try {
    $response = $controller->detailProposal(2);
    if ($response instanceof \Illuminate\View\View) {
        $html = $response->render();
        echo "View rendered successfully. Length: " . strlen($html) . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$endTime = microtime(true);
echo "Time taken: " . ($endTime - $startTime) . " seconds\n";
