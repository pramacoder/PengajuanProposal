@extends('mainlayout.app')

@section('title', 'Dashboard Dosen Pembimbing')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'active' => true],
    ]" />

    <!-- Welcome Page Header -->
    <x-page-header 
        title="Dashboard Dosen Pembimbing" 
        subtitle="Selamat datang, {{ \App\Helpers\UserHelper::getCurrentUserName() }}. Monitor dan bimbing proposal mahasiswa binaan Anda." 
        badge="Dosen Pembimbing"
        icon="fa-chalkboard-teacher"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dosen.pendamping.dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-user-check"></i>
                    <span>Mode Pendamping</span>
                </a>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Quick Navigation Hub -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="ui-card h-100 p-4 transition-hover">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1">Mahasiswa Bimbingan</h6>
                        <p class="text-secondary small mb-3">Lihat dan pantau progres proposal mahasiswa yang Anda bimbing.</p>
                        <a href="{{ route('dosen.pembimbing.mahasiswa.bimbingan') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                            Lihat Mahasiswa <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="ui-card h-100 p-4 transition-hover">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-3 p-3 bg-warning-subtle text-warning">
                        <i class="fas fa-check-double fa-2x"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1">Validasi Tahap 2</h6>
                        <p class="text-secondary small mb-3">Verifikasi proposal revisi mahasiswa sebelum lolos ke tahap seleksi.</p>
                        <a href="{{ route('dosen.pembimbing.validasi.2') }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-semibold text-dark">
                            Periksa Revisi <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="ui-card h-100 p-4 transition-hover">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-3 p-3 bg-info-subtle text-info">
                        <i class="fas fa-book-reader fa-2x"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold text-dark mb-1">Pedoman PKM</h6>
                        <p class="text-secondary small mb-3">Standar format, aturan sistematika usulan, dan petunjuk teknis Belmawa.</p>
                        <span class="badge bg-secondary-subtle text-dark border">Edisi Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Card -->
    <div class="ui-card">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-info-circle text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Pusat Informasi Pembimbing</h6>
            </div>
        </div>
        <div class="ui-card-body p-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h5 class="fw-bold text-dark mb-2">Tugas & Peran Dosen Pembimbing</h5>
                    <p class="text-secondary mb-3">
                        Sebagai Dosen Pembimbing, Anda bertugas membina mahasiswa dalam penyusunan proposal PKM, memastikan kualitas substansi ilmiah, kepatuhan metodologi, serta mengesahkan validasi tahap revisi sebelum dinilai oleh tim Reviewer Seleksi.
                    </p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('dosen.pembimbing.validasi.2') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                            <i class="fas fa-check-double"></i>
                            <span>Cek Validasi 2 Masuk</span>
                        </a>
                        <a href="{{ route('dosen.pembimbing.mahasiswa.bimbingan') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                            <i class="fas fa-users"></i>
                            <span>Daftar Mahasiswa</span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 text-center mt-4 mt-lg-0">
                    <div class="p-3 bg-light rounded-4 border d-inline-block">
                        <i class="fas fa-graduation-cap fa-4x text-primary opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
