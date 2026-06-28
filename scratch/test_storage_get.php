<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Proposal;
use App\Helpers\StorageHelper;

$proposal = Proposal::find(2);
$path = $proposal->dokumen->path_file;

echo "Path: " . $path . "\n";

try {
    echo "Running StorageHelper::get...\n";
    $content = StorageHelper::get($path);
    if ($content === null) {
        echo "StorageHelper::get returned NULL.\n";
    } else {
        echo "StorageHelper::get returned content. Length: " . strlen($content) . " bytes\n";
    }
} catch (\Throwable $e) {
    echo "StorageHelper::get threw exception: " . $e->getMessage() . "\n";
}

try {
    echo "Running Storage::disk('supabase')->get...\n";
    $content = \Illuminate\Support\Facades\Storage::disk('supabase')->get($path);
    echo "Success. Length: " . strlen($content) . "\n";
} catch (\Throwable $e) {
    echo "Storage::disk('supabase')->get threw: " . $e->getMessage() . "\n";
}

try {
    echo "Running Storage::disk('public')->get...\n";
    $content = \Illuminate\Support\Facades\Storage::disk('public')->get($path);
    echo "Success. Length: " . strlen($content) . "\n";
} catch (\Throwable $e) {
    echo "Storage::disk('public')->get threw: " . $e->getMessage() . "\n";
}
