@extends('mainlayout.app')

@section('title', 'Mahasiswa Bimbingan')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard Pembimbing', 'url' => route('dosen.pembimbing.dashboard')],
        ['label' => 'Mahasiswa Bimbingan', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Mahasiswa Bimbingan" 
        subtitle="Daftar mahasiswa binaan dalam penyusunan usulan Program Kreativitas Mahasiswa" 
        badge="Dosen Pembimbing"
        icon="fa-users"
    >
        <x-slot:actions>
            <div class="btn-group shadow-sm" role="group">
                <a href="{{ route('dosen.pembimbing.dashboard') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-start-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Pembimbing</span>
                </a>
                <a href="{{ route('dosen.pendamping.dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-end-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-user-check"></i>
                    <span>Pendamping</span>
                </a>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="ui-card">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-list text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Daftar Mahasiswa Bimbingan</h6>
            </div>
        </div>
        <div class="ui-card-body p-0">
            <div class="empty-state-box py-5">
                <div class="rounded-circle p-4 bg-primary-subtle text-primary d-inline-flex mb-3">
                    <i class="fas fa-user-graduate fa-3x"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Fitur Rekap Mahasiswa Bimbingan</h5>
                <p class="text-secondary small mb-3" style="max-width: 480px;">
                    Integrasi daftar mahasiswa bimbingan secara terpadu sedang disiapkan. Proposal bimbingan yang masuk ke tahap revisi dapat dipantau langsung pada menu Validasi Tahap 2.
                </p>
                <a href="{{ route('dosen.pembimbing.validasi.2') }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 rounded-pill px-4 py-2 fw-semibold">
                    <i class="fas fa-check-double"></i>
                    <span>Buka Validasi Tahap 2</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
