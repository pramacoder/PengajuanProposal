<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$file = Illuminate\Http\UploadedFile::fake()->create('test.pdf', 100);
$result = App\Helpers\StorageHelper::store('proposals', $file);
var_dump($result);
