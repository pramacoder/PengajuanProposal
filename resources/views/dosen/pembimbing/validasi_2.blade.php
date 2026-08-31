@extends('mainlayout.app')

@section('title', 'Validasi 2 — Setelah Revisi')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('dosen.pembimbing.dashboard')],
        ['label' => 'Validasi 2', 'active' => true],
    ]" />

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-check-double me-2 text-warning"></i>Validasi Tahap 2</h2>
                    <p class="text-muted">Proposal yang sudah direvisi mahasiswa dan menunggu validasi Anda</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($proposals->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada proposal yang menunggu Validasi 2</h5>
                <p class="text-muted">Proposal akan muncul di sini setelah mahasiswa mengupload revisi.</p>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($proposals as $proposal)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-warning">
                    <div class="card-header bg-warning bg-opacity-10">
                        <h6 class="card-title mb-0 fw-bold text-warning">
                            <i class="fas fa-file-alt me-2"></i>{{ Str::limit($proposal->judul, 55) }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Mahasiswa</small>
                            <div class="fw-semibold">{{ $proposal->mahasiswa->nama_mhs ?? $proposal->ketua_nama }}</div>
                        </div>
                        <div class="d-flex gap-2 mb-3">
                            <span class="badge bg-secondary">{{ $proposal->skim }}</span>
                            <span class="badge bg-warning text-dark">Menunggu Validasi 2</span>
                        </div>
                        @if($proposal->proposalRevisi->isNotEmpty())
                        <div class="alert alert-info py-2 mb-2" style="font-size: 0.82rem;">
                            <i class="fas fa-file-upload me-1"></i>
                            Revisi diterima: {{ \Carbon\Carbon::parse($proposal->proposalRevisi->first()->tanggal_submit)->format('d M Y H:i') }}
                        </div>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('dosen.pembimbing.validasi.2.detail', $proposal->id_proposal) }}"
                           class="btn btn-warning w-100">
                            <i class="fas fa-eye me-2"></i>Lihat & Validasi
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
