<?php
/**
 * Script to clear all transactions and users on Supabase via REST API
 * (Requires Service Role Key)
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

$tables = [
    'hasil_finals',
    'hasil_semi_finals',
    'nilai_substantifs',
    'nilai_administratifs',
    'proposal_revisi',
    'dokumens',
    'proposals',
    'users' // users last due to foreign keys
];

echo "=== MEMULAI PEMBERSIHAN TABEL DI SUPABASE ===\n\n";

foreach ($tables as $table) {
    // We send a DELETE request. We try filtering by id not being null or id_proposal not being null.
    // For REST API, to delete all, we can filter for something that is not null (e.g. id is not null)
    $idCol = ($table === 'proposals') ? 'id_proposal' : 'id';
    $endpoint = rtrim($url, '/') . "/rest/v1/{$table}?{$idCol}=not.is.null";

    echo "Deleting table [{$table}] via endpoint: {$endpoint}...\n";

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "apikey: {$key}",
        "Authorization: Bearer {$key}",
        "Content-Type: application/json",
        "Prefer: return=representation"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "  HTTP Status Code: {$httpCode}\n";
    $decoded = json_decode($response, true);
    if (is_array($decoded)) {
        echo "  Successfully deleted " . count($decoded) . " rows from {$table}.\n\n";
    } else {
        echo "  Response: " . $response . "\n\n";
    }
}

echo "=== PEMBERSIHAN SELESAI ===\n";
