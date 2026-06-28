<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::find(1);
echo 'User: ' . $user->name . ' ID: ' . $user->id . ' TeamID: ' . $user->getTeamId() . PHP_EOL;

$proposals = App\Models\Proposal::where(function($q) use ($user) {
    $q->where('id_mahasiswa', $user->id)
      ->orWhere('team_id', $user->getTeamId());
})->get();

echo 'Proposals count: ' . $proposals->count() . PHP_EOL;
foreach ($proposals as $p) {
    echo "- " . $p->judul . " (Status: " . $p->status . ")\n";
}
