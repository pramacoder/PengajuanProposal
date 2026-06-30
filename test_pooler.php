<?php
$host = "aws-0-ap-southeast-1.pooler.supabase.com";
$user = "postgres.eslvjcdxwzjboyfwchtt";
$pass = "#Omong1baik1";
$db = "postgres";

echo "Testing port 6543...\n";
try {
    $dsn = "pgsql:host=$host;port=6543;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Port 6543 OK!\n";
} catch (Exception $e) {
    echo "Port 6543 Error: " . $e->getMessage() . "\n";
}

echo "Testing port 5432...\n";
try {
    $dsn = "pgsql:host=$host;port=5432;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Port 5432 OK!\n";
} catch (Exception $e) {
    echo "Port 5432 Error: " . $e->getMessage() . "\n";
}
