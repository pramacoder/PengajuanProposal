<?php
/**
 * Script to query users from Supabase REST API
 */

define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$url = env('SUPABASE_URL');
$key = env('SUPABASE_KEY');

if (!$url || !$key) {
    echo "Error: SUPABASE_URL or SUPABASE_KEY not configured in .env\n";
    exit(1);
}

$endpoint = rtrim($url, '/') . '/rest/v1/users?select=*';

echo "Querying Supabase REST endpoint: {$endpoint}...\n";

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "apikey: {$key}",
    "Authorization: Bearer {$key}",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: {$httpCode}\n";
echo "Response:\n";
$decoded = json_decode($response, true);
if (is_array($decoded)) {
    echo "Total users found: " . count($decoded) . "\n";
    foreach ($decoded as $user) {
        printf("  - ID: %s | Role: %s | Email: %s | Name: %s\n", 
            $user['id'] ?? 'N/A', 
            $user['role'] ?? 'N/A', 
            $user['email'] ?? 'N/A', 
            $user['name'] ?? 'N/A'
        );
    }
} else {
    echo $response . "\n";
}
