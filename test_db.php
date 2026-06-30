<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

config(['database.connections.pgsql.host' => 'db.eslvjcdxwzjboyfwchtt.supabase.co', 'database.connections.pgsql.username' => 'postgres']);
try {
    DB::reconnect('pgsql');
    DB::connection('pgsql')->getPdo();
    echo "KONEKSI DIRECT BERHASIL!\n";
} catch (\Exception $e) {
    echo "KONEKSI DIRECT GAGAL: " . $e->getMessage() . "\n";
}

config(['database.connections.pgsql.host' => 'aws-0-ap-southeast-1.pooler.supabase.com', 'database.connections.pgsql.username' => 'postgres.eslvjcdxwzjboyfwchtt', 'database.connections.pgsql.port' => '6543']);
try {
    DB::reconnect('pgsql');
    DB::connection('pgsql')->getPdo();
    echo "KONEKSI POOLER 6543 BERHASIL!\n";
} catch (\Exception $e) {
    echo "KONEKSI POOLER 6543 GAGAL: " . $e->getMessage() . "\n";
}
