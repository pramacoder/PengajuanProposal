<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Auth::loginUsingId(1);
$response = app()->handle(Illuminate\Http\Request::create('/mahasiswa/proposal', 'GET'));

$content = $response->getContent();
if (strpos($content, 'Testing Judul Porposal 1') !== false) {
    echo "=== PROPOSAL FOUND IN HTML ===\n";
} else {
    echo "=== PROPOSAL NOT FOUND IN HTML ===\n";
}

if (strpos($content, 'Belum Ada Proposal') !== false) {
    echo "=== HTML SHOWS EMPTY STATE ===\n";
}

file_put_contents('test_html_output.html', $content);
