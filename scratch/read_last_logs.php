<?php
$logFile = 'storage/logs/laravel.log';
if (!file_exists($logFile)) {
    echo "Log file does not exist.\n";
    exit;
}

$lines = file($logFile);
$lastLines = array_slice($lines, -100);
echo implode("", $lastLines);
