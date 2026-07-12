<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FormPenilaianController;
use App\Http\Controllers\SimbelmawaReportController;
use App\Http\Controllers\PimpinanPTController;

Route::middleware(['auth', 'role:operator,pimpinan_pt'])->group(function () {
    Route::get('/operator/dashboard', [\App\Http\Controllers\Operator\DashboardController::class, 'dashboard'])->name('operator.dashboard');
    Route::get('/operator/profile', [AuthController::class, 'showProfile'])->name('operator.profile');
    Route::put('/operator/profile', [AuthController::class, 'updateProfile'])->name('operator.profile.update');
    
    // Route untuk operator
    // Fase 2: Assign reviewer
    Route::get('/operator/pilih-reviewer', [\App\Http\Controllers\Operator\ReviewerAssignmentController::class, 'pilihReviewer'])->name('operator.pilih.reviewer')->middleware('check.phase:review');
    Route::get('/operator/search-reviewers', [\App\Http\Controllers\Operator\ReviewerAssignmentController::class, 'searchReviewers'])->name('operator.search.reviewers');
    Route::post('/operator/assign-reviewer', [\App\Http\Controllers\Operator\ReviewerAssignmentController::class, 'assignReviewer'])->name('operator.assign.reviewer')->middleware('check.phase:review');
    Route::get('/operator/assigned-proposals', [\App\Http\Controllers\Operator\ReviewerAssignmentController::class, 'getAssignedProposals'])->name('operator.assigned.proposals');
    Route::get('/operator/assigned-proposals-seleksi', [\App\Http\Controllers\Operator\ReviewerAssignmentController::class, 'getAssignedProposalsSeleksi'])->name('operator.assigned.proposals.seleksi');
    Route::get('/operator/ruang-kontrol', [\App\Http\Controllers\Operator\RuangKontrolController::class, 'ruangKontrol'])->name('operator.ruang.kontrol');
    Route::post('/operator/update-ruang-kontrol', [\App\Http\Controllers\Operator\RuangKontrolController::class, 'updateRuangKontrol'])->name('operator.update.ruang.kontrol');
    Route::post('/operator/create-jadwal', [\App\Http\Controllers\Operator\RuangKontrolController::class, 'createJadwal'])->name('operator.create.jadwal');
    Route::get('/operator/jadwal/{id}', [\App\Http\Controllers\Operator\RuangKontrolController::class, 'getJadwal'])->name('operator.get.jadwal');
    Route::post('/operator/update-jadwal/{id}', [\App\Http\Controllers\Operator\RuangKontrolController::class, 'updateJadwal'])->name('operator.update.jadwal');
    Route::delete('/operator/delete-jadwal/{id}', [\App\Http\Controllers\Operator\RuangKontrolController::class, 'deleteJadwal'])->name('operator.delete.jadwal');
    Route::post('/operator/activate-jadwal/{id}', [\App\Http\Controllers\Operator\RuangKontrolController::class, 'activateJadwal'])->name('operator.activate.jadwal');
    Route::get('/operator/active-phase', [\App\Http\Controllers\Operator\RuangKontrolController::class, 'getActivePhase'])->name('operator.active.phase');
    // Fase 4: Hasil final
    Route::get('/operator/hasil-final', [\App\Http\Controllers\Operator\HasilController::class, 'hasilFinal'])->name('operator.hasil.final')->middleware('check.phase:penilaian_akhir');
    Route::get('/operator/proposal/{id}/detail', [\App\Http\Controllers\Operator\HasilController::class, 'proposalDetail'])->name('operator.proposal.detail');
    Route::post('/operator/update-hasil-final', [\App\Http\Controllers\Operator\HasilController::class, 'updateHasilFinal'])->name('operator.update.hasil.final')->middleware('check.phase:penilaian_akhir');
    Route::get('/operator/revisi/{id}/download', [\App\Http\Controllers\Operator\HasilController::class, 'downloadRevisi'])->name('operator.revisi.download');
    Route::get('/operator/revisi/{id}/view', [\App\Http\Controllers\Operator\HasilController::class, 'viewRevisi'])->name('operator.revisi.view');
    Route::get('/operator/detail-hasil-final/{id}', [\App\Http\Controllers\Operator\HasilController::class, 'detailHasilFinal'])->name('operator.detail.hasil.final');
    Route::get('/operator/proposal/{id}/view-pdf', [\App\Http\Controllers\Operator\HasilController::class, 'viewPdf'])->name('operator.proposal.view.pdf');
    
    // Fase 3: Hasil semi final + pilih reviewer seleksi
    Route::get('/operator/hasil-semi-final', [\App\Http\Controllers\Operator\HasilController::class, 'hasilSemiFinal'])->name('operator.hasil.semi.final')->middleware('check.phase:perbaikan');
    Route::get('/operator/detail-hasil-semi-final/{id}', [\App\Http\Controllers\Operator\HasilController::class, 'detailHasilSemiFinal'])->name('operator.detail.hasil.semi.final');
    Route::post('/operator/update-hasil-semi-final', [\App\Http\Controllers\Operator\HasilController::class, 'updateHasilSemiFinal'])->name('operator.update.hasil.semi.final')->middleware('check.phase:perbaikan');
    Route::get('/operator/pilih-reviewer-seleksi', [\App\Http\Controllers\Operator\ReviewerAssignmentController::class, 'pilihReviewerSeleksi'])->name('operator.pilih.reviewer.seleksi')->middleware('check.phase:perbaikan');
    Route::post('/operator/assign-reviewer-seleksi', [\App\Http\Controllers\Operator\ReviewerAssignmentController::class, 'assignReviewerSeleksi'])->name('operator.assign.reviewer.seleksi')->middleware('check.phase:perbaikan');
    
    // CRUD Form Penilaian
    Route::get('/operator/form-penilaian', [FormPenilaianController::class, 'index'])->name('operator.form.penilaian.index');
    Route::get('/operator/form-penilaian/create', [FormPenilaianController::class, 'create'])->name('operator.form.penilaian.create');
    Route::post('/operator/form-penilaian', [FormPenilaianController::class, 'store'])->name('operator.form.penilaian.store');
    Route::get('/operator/form-penilaian/{id}/edit', [FormPenilaianController::class, 'edit'])->name('operator.form.penilaian.edit');
    Route::put('/operator/form-penilaian/{id}', [FormPenilaianController::class, 'update'])->name('operator.form.penilaian.update');
    Route::delete('/operator/form-penilaian/{id}', [FormPenilaianController::class, 'destroy'])->name('operator.form.penilaian.destroy');
    
    // CRUD Laporan SIMBELMAWA
    Route::get('/operator/laporan-simbelmawa', [SimbelmawaReportController::class, 'index'])->name('operator.laporan.simbelmawa.index');
    Route::get('/operator/laporan-simbelmawa/create', [SimbelmawaReportController::class, 'create'])->name('operator.laporan.simbelmawa.create');
    Route::post('/operator/laporan-simbelmawa', [SimbelmawaReportController::class, 'store'])->name('operator.laporan.simbelmawa.store');
    Route::get('/operator/laporan-simbelmawa/{id}/edit', [SimbelmawaReportController::class, 'edit'])->name('operator.laporan.simbelmawa.edit');
    Route::put('/operator/laporan-simbelmawa/{id}', [SimbelmawaReportController::class, 'update'])->name('operator.laporan.simbelmawa.update');
    Route::delete('/operator/laporan-simbelmawa/{id}', [SimbelmawaReportController::class, 'destroy'])->name('operator.laporan.simbelmawa.destroy');
    
    // Manajemen Akun (Mahasiswa, Dosen, Reviewer, Operator)
    Route::get('/operator/akun', [\App\Http\Controllers\Operator\AccountManagementController::class, 'manageAccounts'])->name('operator.manage.accounts');
    Route::post('/operator/akun/{type}', [\App\Http\Controllers\Operator\AccountManagementController::class, 'storeAccount'])->name('operator.accounts.store');
    Route::put('/operator/akun/{type}/{id}', [\App\Http\Controllers\Operator\AccountManagementController::class, 'updateAccount'])->name('operator.accounts.update');
    Route::delete('/operator/akun/{type}/{id}', [\App\Http\Controllers\Operator\AccountManagementController::class, 'deleteAccount'])->name('operator.accounts.delete');
    Route::post('/operator/akun/mahasiswa/bulk-delete', [\App\Http\Controllers\Operator\AccountManagementController::class, 'bulkDeleteMahasiswa'])->name('operator.accounts.mahasiswa.bulk-delete');
    
    // Route untuk notifikasi operator
    Route::get('/operator/notifications', [\App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('operator.notifications.get');
    Route::post('/operator/notifications/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('operator.notifications.mark-read');
    Route::post('/operator/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('operator.notifications.mark-all-read');
    
    // Route untuk Pimpinan PT (menggunakan guard operator dengan role pimpinan_pt)
    Route::prefix('pimpinan-pt')->name('pimpinan_pt.')->group(function () {
        // Dashboard Pimpinan PT (dengan statistik lengkap seperti operator)
        Route::get('/dashboard', [PimpinanPTController::class, 'dashboard'])->name('dashboard');
        
        // Fitur Penilaian Final (khusus Pimpinan PT)
        Route::get('/detail-hasil-final/{id}', [PimpinanPTController::class, 'detailHasilFinal'])->name('detail.hasil.final');
        Route::post('/update-hasil-final', [PimpinanPTController::class, 'updateHasilFinal'])->name('update.hasil.final');
        Route::get('/proposal/{id}/view-pdf', [PimpinanPTController::class, 'viewPdf'])->name('proposal.view.pdf');
        
        // Manajemen Akun Pimpinan PT (dapat mengelola semua jenis user)
        Route::get('/akun', [PimpinanPTController::class, 'manageAccounts'])->name('manage.accounts');
        Route::post('/akun/{type}', [PimpinanPTController::class, 'storeAccount'])->name('accounts.store');
        Route::put('/akun/{type}/{id}', [PimpinanPTController::class, 'updateAccount'])->name('accounts.update');
        Route::delete('/akun/{type}/{id}', [PimpinanPTController::class, 'deleteAccount'])->name('accounts.delete');
        Route::post('/akun/mahasiswa/bulk-delete', [PimpinanPTController::class, 'bulkDeleteMahasiswa'])->name('accounts.mahasiswa.bulk-delete');

        // ===================================================
        // Fitur Operator yang juga tersedia untuk Pimpinan PT
        // ===================================================
        
        // Pilih Reviewer (Read-only - monitoring)
        Route::get('/pilih-reviewer', [PimpinanPTController::class, 'pilihReviewer'])->name('pilih.reviewer');
        Route::get('/pilih-reviewer-seleksi', [PimpinanPTController::class, 'pilihReviewerSeleksi'])->name('pilih.reviewer.seleksi');
        Route::get('/assigned-proposals', [PimpinanPTController::class, 'getAssignedProposals'])->name('assigned.proposals');
        Route::get('/assigned-proposals-seleksi', [PimpinanPTController::class, 'getAssignedProposalsSeleksi'])->name('assigned.proposals.seleksi');
        
        // Ruang Kontrol (Read-only - monitoring)
        Route::get('/ruang-kontrol', [PimpinanPTController::class, 'ruangKontrol'])->name('ruang.kontrol');
        Route::get('/active-phase', [PimpinanPTController::class, 'getActivePhase'])->name('active.phase');
        
        // Hasil Semi Final (monitoring)
        Route::get('/hasil-semi-final', [PimpinanPTController::class, 'hasilSemiFinal'])->name('hasil.semi.final');
        Route::get('/detail-hasil-semi-final/{id}', [PimpinanPTController::class, 'detailHasilSemiFinal'])->name('detail.hasil.semi.final');

        // Detail Proposal
        Route::get('/proposal/{id}/detail', [PimpinanPTController::class, 'proposalDetail'])->name('proposal.detail');
        
        // Form Penilaian (Read-only)
        Route::get('/form-penilaian', [PimpinanPTController::class, 'formPenilaian'])->name('form.penilaian');
        
        // Laporan SIMBELMAWA (Read-only)
        Route::get('/laporan-simbelmawa', [PimpinanPTController::class, 'laporanSimbelmawa'])->name('laporan.simbelmawa');
        
        // Notifikasi Pimpinan PT
        Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'getNotifications'])->name('notifications.get');
        Route::post('/notifications/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    });
});
