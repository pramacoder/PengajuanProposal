<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
try {
    view('mahasiswa.ajukanproposal')->render();
    echo "Success";
} catch (\Throwable $e) {
    echo $e->getMessage() . " at line " . $e->getLine();
}
