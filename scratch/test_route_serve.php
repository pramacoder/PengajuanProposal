<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;

// Find reviewer user
$user = User::where('role', 'reviewer')->first();
if (!$user) {
    echo "No reviewer user found.\n";
    exit;
}

echo "Simulating request as User ID: " . $user->id . " (" . $user->name . ")\n";

// Act as user
auth()->login($user);

// Create request
$request = \Illuminate\Http\Request::create('/file/serve', 'GET', [
    'path' => 'proposals/8r5pbMzr9Git6T2qBYmblpQGvZJ30jl5fAQyW4CK.pdf'
]);

try {
    $response = $app->handle($request);
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content-Type: " . $response->headers->get('Content-Type') . "\n";
    echo "Response Content Length: " . strlen($response->getContent()) . "\n";
    if ($response->getStatusCode() == 404) {
        echo "Response Content (first 500 chars):\n" . substr($response->getContent(), 0, 500) . "\n";
    }
} catch (\Throwable $e) {
    echo "Request threw exception: " . $e->getMessage() . "\n";
}
