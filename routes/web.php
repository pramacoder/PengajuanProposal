<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\DropdownController;

// Route untuk autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route untuk registrasi
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Route untuk dropdown
Route::get('/get-fakultas', [DropdownController::class, 'getFakultas'])->name('get.fakultas');
Route::get('/get-prodi/{id_fakultas}', [DropdownController::class, 'getProdi'])->name('get.prodi');

// Route untuk get started (pilih role)
Route::get('/getstarted', function () {
    return view('auth.getstarted');
})->name('getstarted');

// Route untuk dashboard berdasarkan user type
Route::middleware(['auth:mahasiswa'])->group(function () {
    // Dashboard mahasiswa sekarang mengarah ke halaman ajukan proposal
    Route::get('/mahasiswa/dashboard', [ProposalController::class, 'create'])->name('mahasiswa.dashboard');
    Route::get('/mahasiswa/profile', [AuthController::class, 'showProfile'])->name('mahasiswa.profile');
    Route::put('/mahasiswa/profile', [AuthController::class, 'updateProfile'])->name('mahasiswa.profile.update');
    
    // Route untuk proposal
    Route::get('/mahasiswa/proposal/create', [ProposalController::class, 'create'])->name('mahasiswa.proposal.create');
    Route::post('/mahasiswa/proposal/store', [ProposalController::class, 'store'])->name('mahasiswa.proposal.store');
    Route::get('/mahasiswa/proposal', [ProposalController::class, 'index'])->name('mahasiswa.proposal.index');
    Route::get('/mahasiswa/proposal/{id}', [ProposalController::class, 'show'])->name('mahasiswa.proposal.show');
    Route::get('/mahasiswa/proposal/{id}/edit', [ProposalController::class, 'edit'])->name('mahasiswa.proposal.edit');
    Route::put('/mahasiswa/proposal/{id}', [ProposalController::class, 'update'])->name('mahasiswa.proposal.update');
    Route::delete('/mahasiswa/proposal/{id}', [ProposalController::class, 'destroy'])->name('mahasiswa.proposal.destroy');
    Route::get('/mahasiswa/proposal/{id}/download/{jenis}', [ProposalController::class, 'download'])->name('mahasiswa.proposal.download');
});

Route::middleware(['auth:dosen'])->group(function () {
    Route::get('/dosen/dashboard', [AuthController::class, 'showDashboard'])->name('dosen.dashboard');
    Route::get('/dosen/profile', [AuthController::class, 'showProfile'])->name('dosen.profile');
    Route::put('/dosen/profile', [AuthController::class, 'updateProfile'])->name('dosen.profile.update');
});

Route::middleware(['auth:reviewer'])->group(function () {
    Route::get('/reviewer/dashboard', [AuthController::class, 'showDashboard'])->name('reviewer.dashboard');
    Route::get('/reviewer/profile', [AuthController::class, 'showProfile'])->name('reviewer.profile');
    Route::put('/reviewer/profile', [AuthController::class, 'updateProfile'])->name('reviewer.profile.update');
});

Route::middleware(['auth:operator'])->group(function () {
    Route::get('/operator/dashboard', [AuthController::class, 'showDashboard'])->name('operator.dashboard');
    Route::get('/operator/profile', [AuthController::class, 'showProfile'])->name('operator.profile');
    Route::put('/operator/profile', [AuthController::class, 'updateProfile'])->name('operator.profile.update');
});

// Route default
Route::get('/', function () {
    return view('welcome');
});
