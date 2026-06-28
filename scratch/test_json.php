<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    Illuminate\Http\Request::capture()
);

use App\Models\Proposal;

$proposal = Proposal::with([
    'mahasiswa',
    'reviewerAdministratif',
    'reviewerSubstantif1',
    'reviewerSubstantif2'
])->whereNotNull('id_reviewer_administratif')->first();

if ($proposal) {
    echo json_encode($proposal->toArray(), JSON_PRETTY_PRINT);
} else {
    echo "No assigned proposal found\n";
}
