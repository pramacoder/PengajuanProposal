<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Proposal;
use App\Helpers\ProposalHelper;
use Illuminate\Support\Facades\Auth;

// Mock session and errors for view rendering
$session = app('session')->driver('array');
$session->put('_token', 'token');
$session->put('errors', new \Illuminate\Support\MessageBag());
app('request')->setLaravelSession($session);

$t0 = microtime(true);

$user = User::where('email', 'reviewer-test1@test.com')->first();
Auth::login($user);
$reviewer = Auth::user();

$t1 = microtime(true);

$id = 2;
$proposal = Proposal::with(['mahasiswa', 'dosen', 'dokumen', 'nilaiAdministratif', 'nilaiSubstantif'])->findOrFail($id);

$t2 = microtime(true);

$isAdministratif = $proposal->id_reviewer_administratif == $reviewer->id;
$isSubstantif = $proposal->id_reviewer_substantif_1 == $reviewer->id || 
               $proposal->id_reviewer_substantif_2 == $reviewer->id;

$t3 = microtime(true);

$dynamicForm = \App\Http\Controllers\FormPenilaianController::getActiveForm('administratif', $proposal->skim);

$t4 = microtime(true);

$checklist = ProposalHelper::getReviewChecklist($proposal->skim);

$t5 = microtime(true);

$view = view('reviewer.detail_proposal_administratif', compact('proposal', 'checklist', 'dynamicForm'))->withErrors(new \Illuminate\Support\MessageBag());
$html = $view->render();

$t6 = microtime(true);

echo "Auth login: " . ($t1 - $t0) . "s\n";
echo "Proposal fetch: " . ($t2 - $t1) . "s\n";
echo "Access checks: " . ($t3 - $t2) . "s\n";
echo "getActiveForm: " . ($t4 - $t3) . "s\n";
echo "getReviewChecklist: " . ($t5 - $t4) . "s\n";
echo "View render: " . ($t6 - $t5) . "s\n";
echo "Total: " . ($t6 - $t0) . "s\n";
