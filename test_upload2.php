<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$file = Illuminate\Http\UploadedFile::fake()->create('test.pdf', 100);

try {
    var_dump(\Illuminate\Support\Facades\Storage::disk('supabase')->putFile('proposals', $file));
} catch (\Throwable $e) {
    var_dump("Supabase error: " . $e->getMessage());
}

try {
    var_dump(\Illuminate\Support\Facades\Storage::disk('public')->putFile('proposals', $file));
} catch (\Throwable $e) {
    var_dump("Public error: " . $e->getMessage());
}
