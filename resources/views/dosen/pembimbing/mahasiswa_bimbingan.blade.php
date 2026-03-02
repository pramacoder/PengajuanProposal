@extends('mainlayout.app')

@section('title', 'Mahasiswa Bimbingan')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard Pembimbing', 'url' => route('dosen.pembimbing.dashboard')],
        ['label' => 'Mahasiswa Bimbingan', 'active' => true],
    ]" />

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-users me-2"></i>Mahasiswa Bimbingan</h2>
                    <p class="text-muted">Daftar mahasiswa yang Anda bimbing</p>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('dosen.pembimbing.dashboard') }}" class="btn btn-outline-primary">
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
</div>

<style>
.avatar-sm {
    width: 35px;
    height: 35px;
    font-size: 14px;
    font-weight: 600;
}
</style>
@endsection
