<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Route untuk dashboard berdasarkan user type
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/mahasiswa/dashboard', [\App\Http\Controllers\Mahasiswa\ProposalController::class, 'dashboard'])->name('mahasiswa.dashboard');
    Route::get('/mahasiswa/profile', [AuthController::class, 'showProfile'])->name('mahasiswa.profile');
    Route::put('/mahasiswa/profile', [AuthController::class, 'updateProfile'])->name('mahasiswa.profile.update');

    // Fase 1: Pendaftaran Proposal
    Route::get('/mahasiswa/proposal/create', [\App\Http\Controllers\Mahasiswa\ProposalController::class, 'create'])->name('mahasiswa.proposal.create')->middleware('check.phase:pendaftaran');
    Route::post('/mahasiswa/proposal/store', [\App\Http\Controllers\Mahasiswa\ProposalController::class, 'store'])->name('mahasiswa.proposal.store')->middleware('check.phase:pendaftaran');
    Route::get('/mahasiswa/proposal', [\App\Http\Controllers\Mahasiswa\ProposalController::class, 'index'])->name('mahasiswa.proposal.index');
    Route::get('/mahasiswa/proposal/{id}', [\App\Http\Controllers\Mahasiswa\ProposalController::class, 'show'])->name('mahasiswa.proposal.show')->whereNumber('id');
    Route::get('/mahasiswa/proposal/{id}/edit', [\App\Http\Controllers\Mahasiswa\ProposalController::class, 'edit'])->name('mahasiswa.proposal.edit')->whereNumber('id');
    Route::put('/mahasiswa/proposal/{id}', [\App\Http\Controllers\Mahasiswa\ProposalController::class, 'update'])->name('mahasiswa.proposal.update')->whereNumber('id');
    Route::delete('/mahasiswa/proposal/{id}', [\App\Http\Controllers\Mahasiswa\ProposalController::class, 'destroy'])->name('mahasiswa.proposal.destroy')->whereNumber('id');
    Route::get('/mahasiswa/proposal/{id}/download/{jenis}', [\App\Http\Controllers\Mahasiswa\ProposalController::class, 'download'])->name('mahasiswa.proposal.download')->whereNumber('id');
    Route::get('/mahasiswa/proposal/{id}/view-pdf', [\App\Http\Controllers\Mahasiswa\ProposalController::class, 'viewPdf'])->name('mahasiswa.proposal.view-pdf')->whereNumber('id');

    // Fase 2: Hasil Review
    Route::get('/mahasiswa/proposal/{id}/review/administrative', [\App\Http\Controllers\Mahasiswa\ProposalReviewController::class, 'getAdministrativeReview'])->name('mahasiswa.proposal.review.administrative')->whereNumber('id');
    Route::get('/mahasiswa/proposal/{id}/review/substantive', [\App\Http\Controllers\Mahasiswa\ProposalReviewController::class, 'getSubstantiveReview'])->name('mahasiswa.proposal.review.substantive')->whereNumber('id');
    Route::get('/mahasiswa/proposal/{id}/review/final', [\App\Http\Controllers\Mahasiswa\ProposalReviewController::class, 'getFinalReview'])->name('mahasiswa.proposal.review.final')->whereNumber('id');
    Route::get('/mahasiswa/proposal/{id}/revisi', [\App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'showRevisiForm'])->name('mahasiswa.proposal.revisi')->middleware('check.phase:perbaikan')->whereNumber('id');
    Route::post('/mahasiswa/proposal/{id}/revisi', [\App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'submitRevisi'])->name('mahasiswa.proposal.revisi.submit')->middleware('check.phase:perbaikan')->whereNumber('id');
    
    Route::get('/mahasiswa/proposal/{id}/revisi-akhir', [\App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'showRevisiAkhirForm'])->name('mahasiswa.proposal.revisi.akhir')->middleware('check.phase:penilaian_akhir')->whereNumber('id');
    Route::post('/mahasiswa/proposal/{id}/revisi-akhir', [\App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'submitRevisiAkhir'])->name('mahasiswa.proposal.revisi.akhir.submit')->middleware('check.phase:penilaian_akhir')->whereNumber('id');

    // Route untuk revisi proposal menggunakan ProposalRevisiController (Alternative Routes)
    Route::get('/mahasiswa/revisi', [\App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'index'])->name('mahasiswa.revisi.index')->middleware('check.phase:perbaikan');
    Route::post('/mahasiswa/revisi', [\App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'store'])->name('mahasiswa.revisi.store')->middleware('check.phase:perbaikan');
    Route::get('/mahasiswa/revisi/{id}/download', [\App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'download'])->name('mahasiswa.revisi.download');
    Route::delete('/mahasiswa/revisi/{id}', [\App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'destroy'])->name('mahasiswa.revisi.destroy')->middleware('check.phase:perbaikan');
    
    // Route untuk notifikasi mahasiswa
    Route::get('/mahasiswa/notifications', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('mahasiswa.notifications.get');
    Route::post('/mahasiswa/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mahasiswa.notifications.mark-read');
    Route::post('/mahasiswa/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mahasiswa.notifications.mark-all-read');
});
