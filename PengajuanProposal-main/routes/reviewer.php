<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::middleware(['auth', 'role:reviewer'])->group(function () {
    Route::get('/reviewer/dashboard', [\App\Http\Controllers\Reviewer\DashboardController::class, 'dashboard'])->name('reviewer.dashboard');
    Route::get('/reviewer/profile', [AuthController::class, 'showProfile'])->name('reviewer.profile');
    Route::put('/reviewer/profile', [AuthController::class, 'updateProfile'])->name('reviewer.profile.update');
    
    // Fase 2: Review administratif & substantif
    Route::get('/reviewer/review-administratif', [\App\Http\Controllers\Reviewer\AdministratifController::class, 'reviewAdministratif'])->name('reviewer.review.administratif')->middleware('check.phase:review');
    Route::get('/reviewer/review-substantif', [\App\Http\Controllers\Reviewer\SubstantifController::class, 'reviewSubstantif'])->name('reviewer.review.substantif')->middleware('check.phase:review');
    
    // Route untuk detail proposal
    Route::get('/reviewer/proposal/{id}/detail', [\App\Http\Controllers\Reviewer\AdministratifController::class, 'detailProposal'])->name('reviewer.detail.proposal');
    
    // Route untuk detail proposal substantif
    Route::get('/reviewer/proposal/{id}/detail-substantif', [\App\Http\Controllers\Reviewer\SubstantifController::class, 'detailProposalSubstantif'])->name('reviewer.detail.proposal.substantif');
    
    // Fase 2: Submit review
    Route::post('/reviewer/proposal/{id}/submit-review-administratif', [\App\Http\Controllers\Reviewer\AdministratifController::class, 'submitReviewAdministratif'])->name('reviewer.submit.review.administratif')->middleware('check.phase:review');
    Route::post('/reviewer/proposal/{id}/submit-review-substantif', [\App\Http\Controllers\Reviewer\SubstantifController::class, 'submitReviewSubstantif'])->name('reviewer.submit.review.substantif')->middleware('check.phase:review');
    
    // Fase 3: Review substantif seleksi
    Route::get('/reviewer/review-substantif-seleksi', [\App\Http\Controllers\Reviewer\SubstantifController::class, 'reviewSubstantifSeleksi'])->name('reviewer.review.substantif.seleksi')->middleware('check.phase:perbaikan');
    Route::post('/reviewer/proposal/{id}/submit-review-substantif-seleksi', [\App\Http\Controllers\Reviewer\SubstantifController::class, 'submitReviewSubstantifSeleksi'])->name('reviewer.submit.review.substantif.seleksi')->middleware('check.phase:perbaikan');
    
    // Route untuk notifikasi reviewer
    Route::get('/reviewer/notifications', [\App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('reviewer.notifications.get');
    Route::post('/reviewer/notifications/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('reviewer.notifications.mark-read');
    Route::post('/reviewer/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('reviewer.notifications.mark-all-read');
});
