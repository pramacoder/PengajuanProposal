<?php
/**
 * Script to delete all users from Supabase REST API
 * (Requires Service Role Key to bypass RLS)
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

// Supabase REST API delete format: DELETE /rest/v1/users?id=neq.0
$endpoint = rtrim($url, '/') . '/rest/v1/users?id=not.is.null';

echo "Sending DELETE request to Supabase REST endpoint: {$endpoint}...\n";

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "apikey: {$key}",
    "Authorization: Bearer {$key}",
    "Content-Type: application/json",
    "Prefer: return=representation" // Returns deleted rows
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status Code: {$httpCode}\n";
echo "Response:\n";
$decoded = json_decode($response, true);
if (is_array($decoded)) {
    echo "Successfully deleted " . count($decoded) . " users from Supabase.\n";
    foreach ($decoded as $user) {
        printf("  - Deleted: %s (%s)\n", $user['name'] ?? 'N/A', $user['email'] ?? 'N/A');
    }
} else {
    echo $response . "\n";
}
