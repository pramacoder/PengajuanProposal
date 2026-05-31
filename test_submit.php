<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Auth::loginUsingId(1);
$user = auth()->user();

echo "User ID: " . $user->id . "\n";
echo "Initial TeamID: " . $user->getTeamId() . "\n";

// Simulate proposal submission
$request = Illuminate\Http\Request::create('/mahasiswa/proposal/store', 'POST', [
    'judul' => 'Testing Submit Script ' . time(),
    'skim' => 'RE',
    'dosen_pembimbing' => 'Prof. Dr. I Made Sujana',
    'dana_diajukan' => '1000000',
    'tahun_ajaran' => '2025/2026',
    
    'ketua_nama' => 'I Made Surya Wijaya',
    'ketua_nim' => '2001234567',
    'ketua_prodi' => 'Teknologi Informasi',
    'ketua_fakultas' => 'Fakultas Teknik',
    'ketua_email' => 'surya@example.com',
    'ketua_no_hp' => '081234567890',
    
    'anggota1_nama' => 'Anggota 1',
    'anggota1_nim' => '2001234568',
    'anggota1_prodi' => 'Teknologi Informasi',
    'anggota1_fakultas' => 'Fakultas Teknik',
    'anggota1_email' => 'a1@example.com',
    'anggota1_no_hp' => '081234567891',
    
    'anggota2_nama' => 'Anggota 2',
    'anggota2_nim' => '2001234569',
    'anggota2_prodi' => 'Teknologi Informasi',
    'anggota2_fakultas' => 'Fakultas Teknik',
    'anggota2_email' => 'a2@example.com',
    'anggota2_no_hp' => '081234567892',
]);
$request->setSession(app('session.store'));

$response = app()->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";

if ($response->isRedirect()) {
    echo "Redirect URL: " . $response->headers->get('Location') . "\n";
    if (session()->has('errors')) {
        echo "Errors: ";
        print_r(session()->get('errors')->all());
    }
} else {
    echo "Output: " . substr($response->getContent(), 0, 500) . "\n";
}

$user->refresh();
echo "New TeamID: " . $user->getTeamId() . "\n";

$proposals = App\Models\Proposal::where('id_mahasiswa', 1)->get();
echo "Proposals count after submit: " . $proposals->count() . "\n";

// Test fetching with index logic
$user2 = App\Models\User::find(1);
$proposals2 = App\Models\Proposal::where(function($q) use ($user2) {
    $q->where('id_mahasiswa', $user2->id)
      ->orWhere('team_id', $user2->getTeamId());
})->get();
echo "Proposals count via index logic: " . $proposals2->count() . "\n";
