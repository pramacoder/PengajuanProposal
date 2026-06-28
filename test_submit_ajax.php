<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Models\User;

$user = User::find(6); // Reviewer Test 1
auth()->login($user);

$request = Request::create('/reviewer/proposal/2/submit-review-administratif', 'POST', [
    'catatan' => 'Testing 1 Testing 1 Testing 1',
    'extra_fields' => [
        'field_0' => '1',
        'field_1' => '1',
    ]
]);

$request->headers->set('Accept', 'application/json');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('X-CSRF-TOKEN', 'testing');

try {
    $response = $app->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
