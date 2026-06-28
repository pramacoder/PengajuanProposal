<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Proposal;

$proposal = Proposal::with(['mahasiswa', 'dosen', 'dokumen', 'nilaiAdministratif'])->find(2);

if (!$proposal) {
    echo "Proposal with ID 2 not found.\n";
    exit;
}

echo "Proposal ID: " . $proposal->id_proposal . "\n";
echo "Judul: " . $proposal->judul . "\n";
echo "Skim: " . $proposal->skim . "\n";
echo "Status: " . $proposal->status . "\n";
echo "Status Validasi: " . $proposal->status_validasi . "\n";
if ($proposal->dokumen) {
    echo "Dokumen ID: " . $proposal->dokumen->id_dokumen . "\n";
    echo "Path File: " . $proposal->dokumen->path_file . "\n";
    $disk = \App\Helpers\StorageHelper::getDisk();
    echo "Storage Disk: " . $disk . "\n";
    echo "Exists in StorageHelper::exists: " . (\App\Helpers\StorageHelper::exists($proposal->dokumen->path_file) ? 'YES' : 'NO') . "\n";
    echo "Exists in public disk: " . (\Illuminate\Support\Facades\Storage::disk('public')->exists($proposal->dokumen->path_file) ? 'YES' : 'NO') . "\n";
    echo "Absolute path on public disk: " . \Illuminate\Support\Facades\Storage::disk('public')->path($proposal->dokumen->path_file) . "\n";
} else {
    echo "No associated dokumen found.\n";
}
