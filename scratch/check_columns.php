<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

foreach (['nilai_administratifs', 'nilai_substantifs'] as $table) {
    echo "Columns for table '$table':\n";
    $columns = Schema::getColumnListing($table);
    foreach ($columns as $column) {
        echo " - $column\n";
    }
}
