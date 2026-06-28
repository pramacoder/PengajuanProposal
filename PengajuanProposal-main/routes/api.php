<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API routes for mahasiswa data
Route::get('/mahasiswa/by-nim/{nim}', [ApiController::class, 'getMahasiswaByNIM']);
Route::get('/mahasiswa/check-proposal/{nim}', [ApiController::class, 'checkMahasiswaInProposal']);
Route::get('/ruang-kontrol/status', [ApiController::class, 'getRuangKontrolStatus']);

// API routes for fakultas and prodi
Route::get('/fakultas', [ApiController::class, 'getFakultas']);
Route::get('/prodi/fakultas/{fakultasId}', [ApiController::class, 'getProdiByFakultas']);
Route::get('/prodi/fakultas-nama/{namaFakultas}', [ApiController::class, 'getProdiByNamaFakultas']);