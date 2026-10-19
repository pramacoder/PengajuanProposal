@extends('mainlayout.app')

@section('title', 'Dashboard Dosen Pendamping')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'active' => true],
    ]" />

    <!-- Page Header with Year Filter -->
    <x-page-header 
        title="Dashboard Dosen Pendamping" 
        subtitle="Selamat datang, {{ \App\Helpers\UserHelper::getCurrentUserName() }}. Pantau dan lakukan validasi terhadap proposal yang ditugaskan kepada Anda." 
        badge="Dosen Pendamping"
        icon="fa-user-check"
    >
        <x-slot:actions>
            <form method="GET" action="{{ route('dosen.pendamping.dashboard') }}" class="d-flex align-items-center gap-2">
                <label for="tahun_ajaran" class="form-label mb-0 fw-semibold text-secondary small text-nowrap">Tahun Ajaran:</label>
                <select name="tahun_ajaran" id="tahun_ajaran" class="form-select form-select-sm rounded-3 shadow-none border-secondary-subtle" onchange="this.form.submit()" style="min-width: 140px;">
                    @foreach($tahunAjaranList as $tahunAjaran)
                        <option value="{{ $tahunAjaran }}" {{ $tahunAjaranTerpilih == $tahunAjaran ? 'selected' : '' }}>
                            {{ $tahunAjaran }}
                        </option>
                    @endforeach
                </select>
            </form>
        </x-slot:actions>
    </x-page-header>

    <!-- Stat Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card p-3 rounded-3 bg-white border shadow-sm h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary-subtle text-primary">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">Total Proposal</div>
                        <div class="fs-4 fw-bold text-dark">{{ $totalProposal }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="metric-card p-3 rounded-3 bg-white border shadow-sm h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning-subtle text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">Menunggu Validasi</div>
                        <div class="fs-4 fw-bold text-dark">{{ $proposalPending }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="metric-card p-3 rounded-3 bg-white border shadow-sm h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success-subtle text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">Proposal Valid</div>
                        <div class="fs-4 fw-bold text-dark">{{ $proposalValid }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="metric-card p-3 rounded-3 bg-white border shadow-sm h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-danger-subtle text-danger">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                    <div>
                        <div class="text-secondary small fw-semibold">Tidak Valid / Ditolak</div>
                        <div class="fs-4 fw-bold text-dark">{{ $proposalTidakValid }}</div>
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
                <h6 class="mb-0 fw-bold fs-6">Daftar Usulan Mahasiswa — Tahun {{ $tahunAjaranTerpilih }}</h6>
            </div>
            <a href="{{ route('dosen.pendamping.proposal.validasi') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                Buka Menu Validasi <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="ui-card-body p-0">
            @if($proposals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-modern">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center py-3">No</th>
                                <th style="width: 25%;" class="py-3">Mahasiswa Pengusul</th>
                                <th style="width: 35%;" class="py-3">Judul Usulan</th>
                                <th style="width: 10%;" class="py-3">Skim</th>
                                <th style="width: 12%;" class="py-3">Status Validasi</th>
                                <th style="width: 13%;" class="text-center py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $index => $proposal)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold fs-6" style="width: 36px; height: 36px; flex-shrink: 0;">
                                                {{ strtoupper(substr($proposal->mahasiswa->nama_mhs ?? 'M', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark small">{{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                                                <small class="text-muted">NIM: {{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark small line-clamp-2" title="{{ $proposal->judul }}">
                                            {{ $proposal->judul }}
                                        </div>
                                        <small class="text-muted">Diajukan: {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                            {{ $proposal->skim }}
                                        </span>
                                    </td>
                                    <td>
                                        <x-status-badge :status="$proposal->status_validasi" />
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('dosen.pendamping.proposal.detail', $proposal->id_proposal) }}"
                                               class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" title="Lihat Detail">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </a>
                                            @if($proposal->status_validasi == 'pending')
                                                <a href="{{ route('dosen.pendamping.proposal.detail', $proposal->id_proposal) }}#validasi"
                                                   class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold text-dark" title="Lakukan Validasi">
                                                    <i class="fas fa-check me-1"></i>Validasi
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state-box py-5">
                    <div class="rounded-circle p-4 bg-light text-secondary d-inline-flex mb-3">
                        <i class="fas fa-inbox fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada Proposal Ditugaskan</h5>
                    <p class="text-secondary small mb-0" style="max-width: 480px;">
                        Proposal mahasiswa yang menunjuk Anda sebagai Dosen Pendamping pada tahun ajaran {{ $tahunAjaranTerpilih }} akan ditampilkan di sini.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
