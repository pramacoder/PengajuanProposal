<?php

$host = 'aws-1-ap-northeast-1.pooler.supabase.com';
$port = '5432';
$dbname = 'postgres';
$user = 'postgres.ykxcawwvswlqxuemchjt';
$password = '#Omong1baik1';

$dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
$pdo = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
echo "Connected!\n\n";

// Check proposals columns
$stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name='proposals' AND table_schema='public' ORDER BY ordinal_position");
$cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "Proposals columns: " . implode(', ', $cols) . "\n\n";

// Check proposals
$stmt = $pdo->query("SELECT * FROM proposals LIMIT 5");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Proposals count: " . count($rows) . "\n";
if(!empty($rows)) print_r($rows);

// Check mahasiswa user
$stmt = $pdo->query("SELECT id, identifier, name, email, role, is_active, metadata FROM users WHERE role='mahasiswa' LIMIT 5");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nMahasiswa users:\n";
print_r($rows);

// Check fakultas/prodi
$stmt = $pdo->query("SELECT * FROM fakultas LIMIT 3");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nFakultas:\n";
print_r($rows);

$stmt = $pdo->query("SELECT * FROM prodis LIMIT 3");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nProdis:\n";
print_r($rows);
