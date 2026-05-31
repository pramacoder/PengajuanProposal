<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\DosenPembimbingController;
use App\Http\Controllers\DosenPendampingController;
use App\Http\Controllers\MahasiswaRegistrationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\ReviewerController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PimpinanPTController;
use App\Http\Controllers\FormPenilaianController;
use App\Http\Controllers\SimbelmawaReportController;

// Route untuk autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Route untuk request kredensial login
Route::get('/request-credentials', [MahasiswaRegistrationController::class, 'showRegistrationForm'])->name('register');
Route::post('/request-credentials', [MahasiswaRegistrationController::class, 'register']);

// Alias untuk /register (untuk kemudahan akses)
Route::get('/register', [MahasiswaRegistrationController::class, 'showRegistrationForm']);
Route::post('/register', [MahasiswaRegistrationController::class, 'register']);

// Route untuk reset password
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.reset');

// Alias untuk /forgot-password (untuk kemudahan akses)
Route::get('/forgot-password', [PasswordResetController::class, 'showResetForm'])->name('password.forgot');
Route::post('/forgot-password', [PasswordResetController::class, 'resetPassword']);


// Route untuk logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registrasi publik dinonaktifkan (semua pembuatan akun lewat operator)
// Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
// Route::post('/register', [AuthController::class, 'register']);
// Route::get('/register/dosen', [AuthController::class, 'showDosenRegistration'])->name('register.dosen');
// Route::post('/register/dosen', [AuthController::class, 'registerDosen']);
// Route::get('/register/reviewer', [AuthController::class, 'showReviewerRegistration'])->name('register.reviewer');
// Route::post('/register/reviewer', [AuthController::class, 'registerReviewer']);
// Route::get('/register/operator', [AuthController::class, 'showOperatorRegistration'])->name('register.operator');
// Route::post('/register/operator', [AuthController::class, 'registerOperator']);

// Route untuk dropdown
Route::get('/get-fakultas', [DropdownController::class, 'getFakultas'])->name('get.fakultas');
Route::get('/get-prodi/{id_fakultas}', [DropdownController::class, 'getProdi'])->name('get.prodi');


// Route API untuk mahasiswa (moved to api.php)
// Alternative: Route API dengan pengecualian CSRF
Route::get('/api/mahasiswa/by-nim/{nim}', [ApiController::class, 'getMahasiswaByNIM'])->name('api.mahasiswa.by-nim');
Route::get('/api/mahasiswa/check-proposal/{nim}', [ApiController::class, 'checkMahasiswaInProposal'])->name('api.mahasiswa.check-proposal');
Route::get('/api/ruang-kontrol/status', [ApiController::class, 'getRuangKontrolStatus'])->name('api.ruang.kontrol.status');
Route::get('/api/proposal/{id}/revisi', [ApiController::class, 'getProposalRevisi'])->name('api.proposal.revisi');


// Authenticated file serve route (serves files from Supabase Storage or local fallback)
Route::get('/file/serve', function (\Illuminate\Http\Request $request) {
    $path = $request->query('path');
    if (!$path) abort(404);
    return \App\Helpers\StorageHelper::response($path, basename($path));
})->middleware('auth')->name('file.serve');

// Route default
Route::get('/', function () {
    return redirect('/login');
});


require __DIR__.'/mahasiswa.php';
require __DIR__.'/dosen.php';
require __DIR__.'/reviewer.php';
require __DIR__.'/operator.php';


