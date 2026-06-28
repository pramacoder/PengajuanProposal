<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\DosenPembimbingController;
use App\Http\Controllers\DosenPendampingController;

Route::middleware(['auth', 'role:dosen'])->group(function () {
    // Dashboard utama dosen (redirect ke pembimbing atau pendamping)
    Route::get('/dosen/dashboard', [DosenController::class, 'dashboard'])->name('dosen.dashboard');
    Route::get('/dosen/profile', [AuthController::class, 'showProfile'])->name('dosen.profile');
    Route::put('/dosen/profile', [AuthController::class, 'updateProfile'])->name('dosen.profile.update');
    
    // Route untuk Dosen Pembimbing
    Route::prefix('dosen/pembimbing')->name('dosen.pembimbing.')->group(function () {
        Route::get('/dashboard', [DosenPembimbingController::class, 'dashboard'])->name('dashboard');
        Route::get('/mahasiswa-bimbingan', [DosenPembimbingController::class, 'mahasiswaBimbingan'])->name('mahasiswa.bimbingan');
        Route::get('/proposal/{id}/detail', [DosenPembimbingController::class, 'detailProposal'])->name('proposal.detail');

        // Validasi 2 (setelah mahasiswa revisi)
        Route::get('/validasi-2', [DosenController::class, 'validasiProposal2'])->name('validasi.2');
        Route::get('/proposal/{id}/validasi-2-detail', [DosenController::class, 'detailValidasiProposal2'])->name('validasi.2.detail');
        Route::post('/proposal/{id}/validasi-2', [DosenController::class, 'validasiProposal2Action'])->name('validasi.2.submit');
    });
    
    // Route untuk Dosen Pendamping
    Route::prefix('dosen/pendamping')->name('dosen.pendamping.')->group(function () {
        Route::get('/dashboard', [DosenPendampingController::class, 'dashboard'])->name('dashboard');
        Route::get('/proposal-validasi', [DosenPendampingController::class, 'proposalValidasi'])->name('proposal.validasi');
        Route::get('/proposal/{id}/detail', [DosenPendampingController::class, 'detailProposal'])->name('proposal.detail');
        Route::post('/proposal/{id}/validasi', [DosenPendampingController::class, 'validasi'])->name('proposal.validasi.submit');
        Route::get('/hasil-review', [DosenPendampingController::class, 'hasilReview'])->name('hasil.review');
        Route::get('/hasil-final', [DosenPendampingController::class, 'hasilFinal'])->name('hasil.final');
        Route::get('/review-data/{id}', [DosenPendampingController::class, 'getReviewData'])->name('review.data');
    });
    
    // Route untuk view PDF dosen
    Route::get('/dosen/proposal/{id}/view-pdf', [DosenController::class, 'viewPdf'])->name('dosen.proposal.view-pdf');
    
    // Route untuk validasi proposal (legacy - bisa dilakukan di kedua fase)
    Route::get('/dosen/validasi-proposal', [DosenController::class, 'validasiProposal'])->name('dosen.validasi.proposal');
    Route::get('/dosen/proposal/{id}/detail', [DosenController::class, 'detailProposal'])->name('dosen.proposal.detail');
    Route::post('/dosen/proposal/{id}/validasi', [DosenController::class, 'validasiProposalAction'])->name('dosen.proposal.validasi');
    
    // Route untuk hasil review
    Route::get('/dosen/hasil-review', [DosenController::class, 'hasilReview'])->name('dosen.hasil.review');
    Route::get('/dosen/hasil-review/{id}/detail', [DosenController::class, 'detailHasilReview'])->name('dosen.hasil.review.detail');
    
    // Route untuk hasil final
    Route::get('/dosen/hasil-final', [DosenController::class, 'hasilFinal'])->name('dosen.hasil.final');
    Route::get('/dosen/hasil-final/{id}/detail', [DosenController::class, 'detailHasilFinal'])->name('dosen.hasil.final.detail');
    
    // Route untuk download dokumen
    Route::get('/dosen/proposal/{id}/download/{jenis}', [DosenController::class, 'downloadDokumen'])->name('dosen.proposal.download');
    
    // Route untuk mendapatkan data review
    Route::get('/dosen/review-data/{id}', [DosenController::class, 'getReviewData'])->name('dosen.review.data');
    
    // Route untuk Dosen Universitas (Menu Khusus)
    Route::prefix('dosen/universitas')->name('dosen.universitas.')->group(function () {
        Route::get('/dashboard', [DosenController::class, 'dashboardUniversitas'])->name('dashboard');
        Route::get('/validasi-akhir', [DosenController::class, 'validasiAkhirProposal'])->name('validasi.akhir');
        Route::get('/validasi-akhir/{id}/detail', [DosenController::class, 'detailValidasiAkhir'])->name('validasi.akhir.detail');
        Route::post('/validasi-akhir/{id}/submit', [DosenController::class, 'submitValidasiAkhir'])->name('validasi.akhir.submit');
        Route::get('/proposal/{id}/view-pdf', [DosenController::class, 'viewPdfUniversitas'])->name('proposal.view.pdf');
        Route::get('/revisi-akhir/{id}/view-pdf', [DosenController::class, 'viewPdfRevisiAkhir'])->name('revisi.akhir.view.pdf');
        Route::get('/revisi-akhir/{id}/download', [DosenController::class, 'downloadRevisiAkhir'])->name('revisi.akhir.download');
    });
    
    // Route untuk notifikasi dosen
    Route::get('/dosen/notifications', [\App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('dosen.notifications.get');
    Route::post('/dosen/notifications/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('dosen.notifications.mark-read');
    Route::post('/dosen/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('dosen.notifications.mark-all-read');
});
