<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProposalController;
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

// Route default
Route::get('/', function () {
    return redirect('/login');
});


// Route untuk dashboard berdasarkan user type
Route::middleware(['auth:mahasiswa'])->group(function () {
    // Dashboard mahasiswa sekarang mengarah ke dashboard yang proper
    Route::get('/mahasiswa/dashboard', [ProposalController::class, 'dashboard'])->name('mahasiswa.dashboard');
    Route::get('/mahasiswa/profile', [AuthController::class, 'showProfile'])->name('mahasiswa.profile');
    Route::put('/mahasiswa/profile', [AuthController::class, 'updateProfile'])->name('mahasiswa.profile.update');
    
    // Route untuk proposal
    Route::get('/mahasiswa/proposal/create', [ProposalController::class, 'create'])->name('mahasiswa.proposal.create')->middleware('check.phase:pendaftaran');
    Route::post('/mahasiswa/proposal/store', [ProposalController::class, 'store'])->name('mahasiswa.proposal.store')->middleware('check.phase:pendaftaran');
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
Route::get('/mahasiswa/proposal/{id}/revisi', [ProposalController::class, 'showRevisiForm'])->name('mahasiswa.proposal.revisi')->middleware('check.phase:perbaikan');
Route::post('/mahasiswa/proposal/{id}/revisi', [ProposalController::class, 'submitRevisi'])->name('mahasiswa.proposal.revisi.submit')->middleware('check.phase:perbaikan');
Route::get('/mahasiswa/proposal/{id}/revisi-akhir', [ProposalController::class, 'showRevisiAkhirForm'])->name('mahasiswa.proposal.revisi.akhir');
Route::post('/mahasiswa/proposal/{id}/revisi-akhir', [ProposalController::class, 'submitRevisiAkhir'])->name('mahasiswa.proposal.revisi.akhir.submit');
    
    // Route untuk revisi proposal
    Route::get('/mahasiswa/revisi', [App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'index'])->name('mahasiswa.revisi.index')->middleware('check.phase:perbaikan');
    Route::post('/mahasiswa/revisi', [App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'store'])->name('mahasiswa.revisi.store')->middleware('check.phase:perbaikan');
    Route::get('/mahasiswa/revisi/{id}/download', [App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'download'])->name('mahasiswa.revisi.download');
    Route::delete('/mahasiswa/revisi/{id}', [App\Http\Controllers\Mahasiswa\ProposalRevisiController::class, 'destroy'])->name('mahasiswa.revisi.destroy')->middleware('check.phase:perbaikan');
    
    // Route untuk notifikasi mahasiswa
    Route::get('/mahasiswa/notifications', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('mahasiswa.notifications.get');
    Route::post('/mahasiswa/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mahasiswa.notifications.mark-read');
    Route::post('/mahasiswa/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mahasiswa.notifications.mark-all-read');
});

Route::middleware(['auth:dosen'])->group(function () {
    // Dashboard utama dosen (redirect ke pembimbing atau pendamping)
    Route::get('/dosen/dashboard', [DosenController::class, 'dashboard'])->name('dosen.dashboard');
    Route::get('/dosen/profile', [AuthController::class, 'showProfile'])->name('dosen.profile');
    Route::put('/dosen/profile', [AuthController::class, 'updateProfile'])->name('dosen.profile.update');
    
    // Route untuk Dosen Pembimbing
    Route::prefix('dosen/pembimbing')->name('dosen.pembimbing.')->group(function () {
        Route::get('/dashboard', [DosenPembimbingController::class, 'dashboard'])->name('dashboard');
        Route::get('/mahasiswa-bimbingan', [DosenPembimbingController::class, 'mahasiswaBimbingan'])->name('mahasiswa.bimbingan');
        Route::get('/proposal/{id}/detail', [DosenPembimbingController::class, 'detailProposal'])->name('proposal.detail');
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
    Route::get('/dosen/notifications', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('dosen.notifications.get');
    Route::post('/dosen/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('dosen.notifications.mark-read');
    Route::post('/dosen/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('dosen.notifications.mark-all-read');
});

Route::middleware(['auth:reviewer'])->group(function () {
    Route::get('/reviewer/dashboard', [ReviewerController::class, 'dashboard'])->name('reviewer.dashboard');
    Route::get('/reviewer/profile', [AuthController::class, 'showProfile'])->name('reviewer.profile');
    Route::put('/reviewer/profile', [AuthController::class, 'updateProfile'])->name('reviewer.profile.update');
    
    // Route untuk review administratif (memerlukan fase perbaikan)
    Route::get('/reviewer/review-administratif', [ReviewerController::class, 'reviewAdministratif'])->name('reviewer.review.administratif')->middleware('check.phase:perbaikan');
    
    // Route untuk review substantif (memerlukan fase perbaikan)
    Route::get('/reviewer/review-substantif', [ReviewerController::class, 'reviewSubstantif'])->name('reviewer.review.substantif')->middleware('check.phase:perbaikan');
    
    // Route untuk detail proposal
    Route::get('/reviewer/proposal/{id}/detail', [ReviewerController::class, 'detailProposal'])->name('reviewer.detail.proposal');
    
    // Route untuk detail proposal substantif
    Route::get('/reviewer/proposal/{id}/detail-substantif', [ReviewerController::class, 'detailProposalSubstantif'])->name('reviewer.detail.proposal.substantif');
    
    // Route untuk submit review (memerlukan fase perbaikan)
    Route::post('/reviewer/proposal/{id}/submit-review-administratif', [ReviewerController::class, 'submitReviewAdministratif'])->name('reviewer.submit.review.administratif')->middleware('check.phase:perbaikan');
    Route::post('/reviewer/proposal/{id}/submit-review-substantif', [ReviewerController::class, 'submitReviewSubstantif'])->name('reviewer.submit.review.substantif')->middleware('check.phase:perbaikan');
    
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
    Route::get('/operator/pilih-reviewer', [OperatorController::class, 'pilihReviewer'])->name('operator.pilih.reviewer')->middleware('check.phase:perbaikan');
    Route::get('/operator/search-reviewers', [OperatorController::class, 'searchReviewers'])->name('operator.search.reviewers');
    Route::post('/operator/assign-reviewer', [OperatorController::class, 'assignReviewer'])->name('operator.assign.reviewer')->middleware('check.phase:perbaikan');
    Route::get('/operator/assigned-proposals', [OperatorController::class, 'getAssignedProposals'])->name('operator.assigned.proposals');
    Route::get('/operator/ruang-kontrol', [OperatorController::class, 'ruangKontrol'])->name('operator.ruang.kontrol');
    Route::post('/operator/update-ruang-kontrol', [OperatorController::class, 'updateRuangKontrol'])->name('operator.update.ruang.kontrol');
    Route::post('/operator/create-jadwal', [OperatorController::class, 'createJadwal'])->name('operator.create.jadwal');
    Route::get('/operator/jadwal/{id}', [OperatorController::class, 'getJadwal'])->name('operator.get.jadwal');
    Route::post('/operator/update-jadwal/{id}', [OperatorController::class, 'updateJadwal'])->name('operator.update.jadwal');
    Route::delete('/operator/delete-jadwal/{id}', [OperatorController::class, 'deleteJadwal'])->name('operator.delete.jadwal');
    Route::post('/operator/activate-jadwal/{id}', [OperatorController::class, 'activateJadwal'])->name('operator.activate.jadwal');
    Route::get('/operator/active-phase', [OperatorController::class, 'getActivePhase'])->name('operator.active.phase');
    Route::get('/operator/hasil-final', [OperatorController::class, 'hasilFinal'])->name('operator.hasil.final')->middleware('check.phase:perbaikan');
    Route::get('/operator/proposal/{id}/detail', [OperatorController::class, 'proposalDetail'])->name('operator.proposal.detail');
    Route::post('/operator/update-hasil-final', [OperatorController::class, 'updateHasilFinal'])->name('operator.update.hasil.final')->middleware('check.phase:perbaikan');
    Route::get('/operator/revisi/{id}/download', [OperatorController::class, 'downloadRevisi'])->name('operator.revisi.download');
    Route::get('/operator/revisi/{id}/view', [OperatorController::class, 'viewRevisi'])->name('operator.revisi.view');
    Route::get('/operator/detail-hasil-final/{id}', [OperatorController::class, 'detailHasilFinal'])->name('operator.detail.hasil.final');
    Route::get('/operator/proposal/{id}/view-pdf', [OperatorController::class, 'viewPdf'])->name('operator.proposal.view.pdf');
    
    // Route untuk hasil semi final
    Route::get('/operator/hasil-semi-final', [OperatorController::class, 'hasilSemiFinal'])->name('operator.hasil.semi.final')->middleware('check.phase:perbaikan');
    Route::get('/operator/detail-hasil-semi-final/{id}', [OperatorController::class, 'detailHasilSemiFinal'])->name('operator.detail.hasil.semi.final');
    Route::post('/operator/update-hasil-semi-final', [OperatorController::class, 'updateHasilSemiFinal'])->name('operator.update.hasil.semi.final')->middleware('check.phase:perbaikan');
    
    // Manajemen Akun (Mahasiswa, Dosen, Reviewer, Operator)
    Route::get('/operator/akun', [OperatorController::class, 'manageAccounts'])->name('operator.manage.accounts');
    Route::post('/operator/akun/{type}', [OperatorController::class, 'storeAccount'])->name('operator.accounts.store');
    Route::put('/operator/akun/{type}/{id}', [OperatorController::class, 'updateAccount'])->name('operator.accounts.update');
    Route::delete('/operator/akun/{type}/{id}', [OperatorController::class, 'deleteAccount'])->name('operator.accounts.delete');
    Route::post('/operator/akun/mahasiswa/bulk-delete', [OperatorController::class, 'bulkDeleteMahasiswa'])->name('operator.accounts.mahasiswa.bulk-delete');
    
    // Route untuk notifikasi operator
    Route::get('/operator/notifications', [App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('operator.notifications.get');
    Route::post('/operator/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('operator.notifications.mark-read');
    Route::post('/operator/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('operator.notifications.mark-all-read');
    
    // Route untuk Pimpinan PT (menggunakan guard operator dengan role pimpinan_pt)
    Route::prefix('pimpinan-pt')->name('pimpinan_pt.')->group(function () {
        Route::get('/dashboard', [PimpinanPTController::class, 'dashboard'])->name('dashboard');
        Route::get('/detail-hasil-final/{id}', [PimpinanPTController::class, 'detailHasilFinal'])->name('detail.hasil.final');
        Route::post('/update-hasil-final', [PimpinanPTController::class, 'updateHasilFinal'])->name('update.hasil.final');
        Route::get('/proposal/{id}/view-pdf', [PimpinanPTController::class, 'viewPdf'])->name('proposal.view.pdf');
        
        // Manajemen Akun Pimpinan PT (dapat mengelola semua jenis user)
        Route::get('/akun', [PimpinanPTController::class, 'manageAccounts'])->name('manage.accounts');
        Route::post('/akun/{type}', [PimpinanPTController::class, 'storeAccount'])->name('accounts.store');
        Route::put('/akun/{type}/{id}', [PimpinanPTController::class, 'updateAccount'])->name('accounts.update');
        Route::delete('/akun/{type}/{id}', [PimpinanPTController::class, 'deleteAccount'])->name('accounts.delete');
        Route::post('/akun/mahasiswa/bulk-delete', [PimpinanPTController::class, 'bulkDeleteMahasiswa'])->name('accounts.mahasiswa.bulk-delete');
    });
});


