<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::find(2);
echo "Anggota ID: " . $user->id . "\n";
echo "Anggota TeamID: " . $user->getTeamId() . "\n";

$proposals = App\Models\Proposal::where(function($q) use ($user) {
    $q->where('id_mahasiswa', $user->id)
      ->orWhere('team_id', $user->getTeamId());
})->get();

echo "Proposals count: " . $proposals->count() . "\n";
foreach ($proposals as $p) {
    echo "- " . $p->judul . "\n";
}
