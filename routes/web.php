<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\DropdownController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\ReviewerController;
use App\Http\Controllers\ApiController;

// Route untuk autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Route untuk reset password
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.forgot');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordReset'])->name('password.reset');


// Route untuk logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route untuk registrasi
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Route untuk registrasi khusus berdasarkan role
Route::get('/register/dosen', [AuthController::class, 'showDosenRegistration'])->name('register.dosen');
Route::post('/register/dosen', [AuthController::class, 'registerDosen']);
Route::get('/register/reviewer', [AuthController::class, 'showReviewerRegistration'])->name('register.reviewer');
Route::post('/register/reviewer', [AuthController::class, 'registerReviewer']);
Route::get('/register/operator', [AuthController::class, 'showOperatorRegistration'])->name('register.operator');
Route::post('/register/operator', [AuthController::class, 'registerOperator']);

// Route untuk dropdown
Route::get('/get-fakultas', [DropdownController::class, 'getFakultas'])->name('get.fakultas');
Route::get('/get-prodi/{id_fakultas}', [DropdownController::class, 'getProdi'])->name('get.prodi');


// Route API untuk mahasiswa (moved to api.php)
// Alternative: Route API dengan pengecualian CSRF
Route::get('/api/mahasiswa/by-nim/{nim}', [ApiController::class, 'getMahasiswaByNIM'])->name('api.mahasiswa.by-nim');
Route::get('/api/mahasiswa/check-proposal/{nim}', [ApiController::class, 'checkMahasiswaInProposal'])->name('api.mahasiswa.check-proposal');
Route::get('/api/ruang-kontrol/status', [ApiController::class, 'getRuangKontrolStatus'])->name('api.ruang.kontrol.status');
Route::get('/api/proposal/{id}/revisi', [ApiController::class, 'getProposalRevisi'])->name('api.proposal.revisi');

// Route default
Route::get('/', function () {
    return redirect('/login');
});


// Route untuk dashboard berdasarkan user type
Route::middleware(['auth:mahasiswa'])->group(function () {
    // Dashboard mahasiswa sekarang mengarah ke halaman ajukan proposal
    Route::get('/mahasiswa/dashboard', [ProposalController::class, 'create'])->name('mahasiswa.dashboard');
    Route::get('/mahasiswa/profile', [AuthController::class, 'showProfile'])->name('mahasiswa.profile');
    Route::put('/mahasiswa/profile', [AuthController::class, 'updateProfile'])->name('mahasiswa.profile.update');
    
    // Route untuk proposal
    Route::get('/mahasiswa/proposal/create', [ProposalController::class, 'create'])->name('mahasiswa.proposal.create')->middleware('ruang.kontrol:pendaftaran');
    Route::post('/mahasiswa/proposal/store', [ProposalController::class, 'store'])->name('mahasiswa.proposal.store')->middleware('ruang.kontrol:pendaftaran');
    Route::get('/mahasiswa/proposal', [ProposalController::class, 'index'])->name('mahasiswa.proposal.index');
    Route::get('/mahasiswa/proposal/{id}', [ProposalController::class, 'show'])->name('mahasiswa.proposal.show');
    Route::get('/mahasiswa/proposal/{id}/edit', [ProposalController::class, 'edit'])->name('mahasiswa.proposal.edit');
    Route::put('/mahasiswa/proposal/{id}', [ProposalController::class, 'update'])->name('mahasiswa.proposal.update');
    Route::delete('/mahasiswa/proposal/{id}', [ProposalController::class, 'destroy'])->name('mahasiswa.proposal.destroy');
    Route::get('/mahasiswa/proposal/{id}/download/{jenis}', [ProposalController::class, 'download'])->name('mahasiswa.proposal.download');
    Route::get('/mahasiswa/proposal/{id}/view-pdf', [ProposalController::class, 'viewPdf'])->name('mahasiswa.proposal.view-pdf');

    // Review Data Routes
    Route::get('/mahasiswa/proposal/{id}/review/administrative', [ProposalController::class, 'getAdministrativeReview'])->name('mahasiswa.proposal.review.administrative');
    Route::get('/mahasiswa/proposal/{id}/review/substantive', [ProposalController::class, 'getSubstantiveReview'])->name('mahasiswa.proposal.review.substantive');
    Route::get('/mahasiswa/proposal/{id}/review/final', [ProposalController::class, 'getFinalReview'])->name('mahasiswa.proposal.review.final');
Route::get('/mahasiswa/proposal/{id}/revisi', [ProposalController::class, 'showRevisiForm'])->name('mahasiswa.proposal.revisi');
Route::post('/mahasiswa/proposal/{id}/revisi', [ProposalController::class, 'submitRevisi'])->name('mahasiswa.proposal.revisi.submit');
    
    // Route untuk revisi proposal
    Route::get('/mahasiswa/revisi', [App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'index'])->name('mahasiswa.revisi.index');
    Route::post('/mahasiswa/revisi', [App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'store'])->name('mahasiswa.revisi.store');
    Route::get('/mahasiswa/revisi/{id}/download', [App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'download'])->name('mahasiswa.revisi.download');
    Route::delete('/mahasiswa/revisi/{id}', [App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'destroy'])->name('mahasiswa.revisi.destroy');
    
    // Route untuk notifikasi mahasiswa
    Route::get('/mahasiswa/notifications', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('mahasiswa.notifications.get');
    Route::post('/mahasiswa/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mahasiswa.notifications.mark-read');
    Route::post('/mahasiswa/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mahasiswa.notifications.mark-all-read');
});

Route::middleware(['auth:dosen'])->group(function () {
    Route::get('/dosen/dashboard', [DosenController::class, 'dashboard'])->name('dosen.dashboard');
    Route::get('/dosen/profile', [AuthController::class, 'showProfile'])->name('dosen.profile');
    Route::put('/dosen/profile', [AuthController::class, 'updateProfile'])->name('dosen.profile.update');
    
    // Route untuk validasi proposal
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
    
    // Route untuk notifikasi dosen
    Route::get('/dosen/notifications', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('dosen.notifications.get');
    Route::post('/dosen/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('dosen.notifications.mark-read');
    Route::post('/dosen/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('dosen.notifications.mark-all-read');
});

Route::middleware(['auth:reviewer'])->group(function () {
    Route::get('/reviewer/dashboard', [ReviewerController::class, 'dashboard'])->name('reviewer.dashboard');
    Route::get('/reviewer/profile', [AuthController::class, 'showProfile'])->name('reviewer.profile');
    Route::put('/reviewer/profile', [AuthController::class, 'updateProfile'])->name('reviewer.profile.update');
    
    // Route untuk review administratif
    Route::get('/reviewer/review-administratif', [ReviewerController::class, 'reviewAdministratif'])->name('reviewer.review.administratif');
    
    // Route untuk review substantif
    Route::get('/reviewer/review-substantif', [ReviewerController::class, 'reviewSubstantif'])->name('reviewer.review.substantif');
    
    // Route untuk detail proposal
    Route::get('/reviewer/proposal/{id}/detail', [ReviewerController::class, 'detailProposal'])->name('reviewer.detail.proposal');
    
    // Route untuk detail proposal substantif
    Route::get('/reviewer/proposal/{id}/detail-substantif', [ReviewerController::class, 'detailProposalSubstantif'])->name('reviewer.detail.proposal.substantif');
    
    // Route untuk submit review
    Route::post('/reviewer/proposal/{id}/submit-review-administratif', [ReviewerController::class, 'submitReviewAdministratif'])->name('reviewer.submit.review.administratif');
    Route::post('/reviewer/proposal/{id}/submit-review-substantif', [ReviewerController::class, 'submitReviewSubstantif'])->name('reviewer.submit.review.substantif');
    
    // Route untuk notifikasi reviewer
    Route::get('/reviewer/notifications', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('reviewer.notifications.get');
    Route::post('/reviewer/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('reviewer.notifications.mark-read');
    Route::post('/reviewer/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('reviewer.notifications.mark-all-read');
});

Route::middleware(['auth:operator'])->group(function () {
    Route::get('/operator/dashboard', [OperatorController::class, 'dashboard'])->name('operator.dashboard');
    Route::get('/operator/profile', [AuthController::class, 'showProfile'])->name('operator.profile');
    Route::put('/operator/profile', [AuthController::class, 'updateProfile'])->name('operator.profile.update');
    
    // Route untuk operator
    Route::get('/operator/pilih-reviewer', [OperatorController::class, 'pilihReviewer'])->name('operator.pilih.reviewer');
    Route::get('/operator/search-reviewers', [OperatorController::class, 'searchReviewers'])->name('operator.search.reviewers');
    Route::post('/operator/assign-reviewer', [OperatorController::class, 'assignReviewer'])->name('operator.assign.reviewer');
    Route::get('/operator/assigned-proposals', [OperatorController::class, 'getAssignedProposals'])->name('operator.assigned.proposals');
    Route::get('/operator/ruang-kontrol', [OperatorController::class, 'ruangKontrol'])->name('operator.ruang.kontrol');
    Route::post('/operator/update-ruang-kontrol', [OperatorController::class, 'updateRuangKontrol'])->name('operator.update.ruang.kontrol');
    Route::get('/operator/hasil-final', [OperatorController::class, 'hasilFinal'])->name('operator.hasil.final');
    Route::get('/operator/proposal/{id}/detail', [OperatorController::class, 'proposalDetail'])->name('operator.proposal.detail');
    Route::post('/operator/update-hasil-final', [OperatorController::class, 'updateHasilFinal'])->name('operator.update.hasil.final');
    Route::get('/operator/revisi/{id}/download', [OperatorController::class, 'downloadRevisi'])->name('operator.revisi.download');
    Route::get('/operator/detail-hasil-final/{id}', [OperatorController::class, 'detailHasilFinal'])->name('operator.detail.hasil.final');
    
    // Route untuk notifikasi operator
    Route::get('/operator/notifications', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('operator.notifications.get');
    Route::post('/operator/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('operator.notifications.mark-read');
    Route::post('/operator/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('operator.notifications.mark-all-read');
});


