<?php

$host = 'aws-1-ap-northeast-1.pooler.supabase.com';
$port = '5432';
$dbname = 'postgres';
$user = 'postgres.ykxcawwvswlqxuemchjt';
$password = '#Omong1baik1';

$dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

try {
    $pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Connected to Supabase!\n\n";
    
    // List ALL tables
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema='public' ORDER BY table_name");
    $allTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "All public tables:\n";
    foreach($allTables as $t) echo "  - $t\n";
    
    // Check ruang_kontrol related tables
    foreach(['jadwals', 'ruang_kontrols', 'settings', 'phases', 'sistem_settings'] as $t) {
        try {
            $r = $pdo->query("SELECT * FROM $t LIMIT 5");
            echo "\n[$t]:\n";
            $rows = $r->fetchAll(PDO::FETCH_ASSOC);
            if(empty($rows)) echo "  (empty)\n";
            else print_r($rows);
        } catch(Exception $e) {
            echo "\n[$t]: table not found\n";
        }
    }
    
    // Check proposals
    try {
        $r = $pdo->query("SELECT id, judul, status, user_id FROM proposals LIMIT 5");
        echo "\n[proposals]:\n";
        $rows = $r->fetchAll(PDO::FETCH_ASSOC);
        if(empty($rows)) echo "  (empty)\n";
        else print_r($rows);
    } catch(Exception $e) {
        echo "\n[proposals]: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
