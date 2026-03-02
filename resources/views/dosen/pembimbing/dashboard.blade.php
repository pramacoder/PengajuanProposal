@extends('mainlayout.app')

@section('title', 'Dashboard Dosen Pembimbing')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'active' => true],
    ]" />

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-chalkboard-teacher me-2"></i>Dashboard Dosen Pembimbing</h2>
                    <p class="text-muted">Monitor proposal mahasiswa bimbingan Anda - Tahun Ajaran 2024/2025</p>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('dosen.pembimbing.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-chalkboard-teacher me-1"></i>Pembimbing
                    </a>
                    <a href="{{ route('dosen.pendamping.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-check me-1"></i>Pendamping
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Halaman Kosong - Akan diisi dengan fungsi baru nanti -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Halaman Kosong</h5>
                    <p class="text-muted">Halaman ini akan diisi dengan fungsi baru nanti.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
