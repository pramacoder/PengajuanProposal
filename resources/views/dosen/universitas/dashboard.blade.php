@extends('mainlayout.app')

@section('title', 'Dashboard Dosen Pendamping Universitas')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Dashboard Pendamping Universitas" 
        subtitle="Selamat datang, {{ \App\Helpers\UserHelper::getCurrentUserName() }}. Validasi akhir proposal mahasiswa binaan tingkat universitas sebelum penetapan kelolosan." 
        badge="Pendamping Universitas"
        icon="fa-university"
    >
        <x-slot:actions>
            <a href="{{ route('dosen.universitas.validasi.akhir') }}" class="btn btn-warning d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold text-dark shadow-sm">
                <i class="fas fa-check-double"></i>
                <span>Antrean Validasi Akhir</span>
            </a>
        </x-slot:actions>
    </x-page-header>

    <!-- Stat Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-sm-6">
            <div class="metric-card p-3 rounded-3 bg-white border shadow-sm h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning-subtle text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">Perlu Validasi Akhir</div>
                        <div class="fs-4 fw-bold text-dark">{{ $proposalsValidasi->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-6">
            <div class="metric-card p-3 rounded-3 bg-white border shadow-sm h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success-subtle text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">Sudah Divalidasi</div>
                        <div class="fs-4 fw-bold text-dark">{{ $proposalsValid->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-sm-6">
            <div class="metric-card p-3 rounded-3 bg-white border shadow-sm h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">Total Usulan Didampingi</div>
                        <div class="fs-4 fw-bold text-dark">{{ $proposals->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Proposal Table Card -->
    <div class="ui-card shadow-sm">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-list text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Daftar Usulan Tingkat Universitas</h6>
            </div>
        </div>
        <div class="ui-card-body p-0">
            @if($proposals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-modern">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center py-3">No</th>
                                <th style="width: 35%;" class="py-3">Judul Usulan Proposal</th>
                                <th style="width: 10%;" class="py-3">Skim</th>
                                <th style="width: 23%;" class="py-3">Mahasiswa Pengusul</th>
                                <th style="width: 14%;" class="py-3">Status</th>
                                <th style="width: 13%;" class="text-center py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $index => $proposal)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark small line-clamp-2" title="{{ $proposal->judul_proposal }}">
                                            {{ $proposal->judul_proposal }}
                                        </div>
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d M Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                            {{ $proposal->skim }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark small">{{ $proposal->mahasiswa->nama_mahasiswa ?? $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                                        <small class="text-muted">NIM: {{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @if($proposal->status == 'validasi_akhir_dosen_univ')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">
                                                Perlu Validasi
                                            </span>
                                        @elseif($proposal->status_validasi == 'valid')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                Valid
                                            </span>
                                        @elseif($proposal->status_validasi == 'tidak_valid')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">
                                                Tidak Valid
                                            </span>
                                        @else
                                            <x-status-badge :status="$proposal->status" />
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($proposal->status == 'validasi_akhir_dosen_univ')
                                            <a href="{{ route('dosen.universitas.validasi.akhir.detail', $proposal->id_proposal) }}"
                                               class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold text-dark shadow-sm">
                                                <i class="fas fa-check-double me-1"></i>Validasi
                                            </a>
                                        @else
                                            <a href="{{ route('dosen.universitas.validasi.akhir.detail', $proposal->id_proposal) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state-box py-5">
                    <div class="rounded-circle p-4 bg-light text-secondary d-inline-flex mb-3">
                        <i class="fas fa-file-alt fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada Proposal Masuk</h5>
                    <p class="text-secondary small mb-0" style="max-width: 480px;">
                        Proposal mahasiswa yang masuk ke tahap pendampingan universitas akan muncul di sini.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
