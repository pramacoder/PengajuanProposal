@extends('mainlayout.app')

@section('title', 'Dashboard Dosen Pembimbing')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'active' => true],
    ]" />

    {{-- Welcome Header --}}
    <div class="mb-4">
        <h1 class="h4 fw-bold mb-1" style="color: var(--text-900);">
            Selamat datang, <span style="color: var(--primary-700);">{{ \App\Helpers\UserHelper::getCurrentUserName() }}</span> 👋
        </h1>
        <p class="mb-0" style="color: var(--text-600); font-size: 0.9rem;">Dashboard Dosen Pembimbing — Monitor proposal mahasiswa bimbingan Anda</p>
    </div>

    <div class="card card-custom">
        <div class="card-body text-center py-5">
            <div style="width:64px;height:64px;border-radius:16px;background:var(--primary-100);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <i class="fas fa-chalkboard-teacher" style="font-size:1.5rem;color:var(--primary-700);"></i>
            </div>
            <h6 class="fw-semibold mb-1" style="color:var(--text-900);">Halaman Dalam Pengembangan</h6>
            <p style="color:var(--text-600);font-size:0.875rem;">Halaman ini akan diisi dengan fungsi baru.</p>
        </div>
    </div>
</div>
@endsection
