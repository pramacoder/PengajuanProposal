<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Proposal;

$user = User::where('role', 'reviewer')->first();
auth()->login($user);

// Share errors ViewErrorBag for the view rendering
view()->share('errors', new \Illuminate\Support\ViewErrorBag);

// Get the controller
$controller = new \App\Http\Controllers\Reviewer\AdministratifController(
    app(\App\Services\ReviewCompletionService::class)
);

// Call detailProposal
$response = $controller->detailProposal(2);

// Check if it's a View response
if ($response instanceof \Illuminate\View\View) {
    $html = $response->render();
    // Search for iframe in rendered html
    if (preg_match('/<iframe[^>]*>/i', $html, $matches)) {
        echo "Found iframe tag:\n" . $matches[0] . "\n";
    } else {
        echo "No iframe found in rendered HTML.\n";
    }
} else {
    echo "Controller did not return a View response.\n";
}
