@extends('mainlayout.app')

@section('title', 'Validasi 2 — Setelah Revisi')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('dosen.pembimbing.dashboard')],
        ['label' => 'Validasi 2', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Validasi Tahap 2" 
        subtitle="Verifikasi proposal mahasiswa yang telah direvisi berdasarkan catatan reviewer sebelum lanjut ke tahap seleksi" 
        badge="Dosen Pembimbing"
        icon="fa-check-double"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dosen.pembimbing.dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fs-5"></i>
                <div class="fw-medium">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($proposals->isEmpty())
        <div class="ui-card">
            <div class="ui-card-body p-0">
                <div class="empty-state-box py-5">
                    <div class="rounded-circle p-4 bg-success-subtle text-success d-inline-flex mb-3">
                        <i class="fas fa-clipboard-check fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Semua Proposal Sudah Tervalidasi</h5>
                    <p class="text-secondary small mb-0" style="max-width: 480px;">
                        Tidak ada proposal mahasiswa yang sedang menunggu Validasi Tahap 2. Proposal revisi baru akan otomatis tampil di sini saat mahasiswa selesai mengunggahnya.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($proposals as $proposal)
            <div class="col-md-6 col-lg-4">
                <div class="ui-card h-100 d-flex flex-column transition-hover shadow-sm border-top border-warning border-3">
                    <div class="ui-card-body p-4 flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                {{ $proposal->skim }}
                            </span>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">
                                Menunggu Validasi 2
                            </span>
                        </div>

                        <h6 class="fw-bold text-dark mb-3 line-clamp-2" title="{{ $proposal->judul_proposal }}">
                            <a href="{{ route('dosen.pembimbing.validasi.2.detail', $proposal->id_proposal) }}" class="text-decoration-none text-dark hover-primary">
                                {{ $proposal->judul_proposal }}
                            </a>
                        </h6>

                        <div class="p-3 rounded-3 bg-light border mb-3">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-user-graduate text-secondary"></i>
                                <span class="fw-semibold text-dark small">{{ $proposal->mahasiswa->nama_mhs ?? $proposal->ketua_nama }}</span>
                            </div>
                            <div class="text-muted small ps-4">
                                NIM: {{ $proposal->mahasiswa->nim ?? $proposal->ketua_nim ?? 'N/A' }}
                            </div>
                        </div>

                        @if($proposal->proposalRevisi->isNotEmpty())
                        <div class="alert alert-info py-2 px-3 mb-0 rounded-3 d-flex align-items-center gap-2 small">
                            <i class="fas fa-file-upload text-info"></i>
                            <div>
                                <span class="text-muted">Revisi diunggah:</span><br>
                                <strong class="text-dark">{{ \Carbon\Carbon::parse($proposal->proposalRevisi->first()->tanggal_submit)->format('d M Y, H:i') }}</strong>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="p-3 bg-light border-top rounded-bottom-3">
                        <a href="{{ route('dosen.pembimbing.validasi.2.detail', $proposal->id_proposal) }}"
                           class="btn btn-warning w-100 d-inline-flex align-items-center justify-content-center gap-2 rounded-3 fw-semibold text-dark shadow-sm">
                            <i class="fas fa-eye"></i>
                            <span>Periksa & Validasi</span>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
