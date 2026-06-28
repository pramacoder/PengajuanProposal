@extends('mainlayout.app')

@section('title', 'Dashboard Dosen Pendamping Universitas')

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
        <p class="mb-0" style="color: var(--text-600); font-size: 0.9rem;">Dashboard Dosen Pendamping Universitas — Validasi akhir proposal yang ditugaskan</p>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">Perlu Validasi Akhir</div>
                    <div class="stat-value">{{ $proposalsValidasi->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">Sudah Valid</div>
                    <div class="stat-value">{{ $proposalsValid->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon maroon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">Total Proposal</div>
                    <div class="stat-value">{{ $proposals->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Proposal Table --}}
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <i class="fas fa-list"></i>
            <span>Daftar Proposal yang Didampingi</span>
        </div>
        <div class="card-body p-0">
            @if($proposals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                        <thead style="background:var(--surface-2);border-bottom:1px solid var(--border);">
                            <tr>
                                <th class="px-4 py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">No</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Judul Proposal</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Skim</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Mahasiswa</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Status</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Tanggal</th>
                                <th class="py-3 fw-semibold text-center" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $index => $proposal)
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td class="px-4 py-3" style="color:var(--text-400);">{{ $index + 1 }}</td>
                                    <td class="py-3">
                                        <div class="fw-medium text-truncate" style="max-width:240px;color:var(--text-900);">
                                            {{ Str::limit($proposal->judul_proposal, 50) }}
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge rounded-pill" style="background:rgba(8,145,178,0.1);color:#0891B2;font-size:0.72rem;padding:0.3rem 0.65rem;">
                                            {{ $proposal->skim }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-medium" style="color:var(--text-900);">{{ $proposal->mahasiswa->nama_mahasiswa ?? 'N/A' }}</div>
                                        <div style="font-size:0.78rem;color:var(--text-400);">{{ $proposal->mahasiswa->nim ?? 'N/A' }}</div>
                                    </td>
                                    <td class="py-3">
                                        @if($proposal->status == 'validasi_akhir_dosen_univ')
                                            <span class="badge rounded-pill" style="background:rgba(245,158,11,0.12);color:#D97706;font-size:0.72rem;padding:0.3rem 0.65rem;">Perlu Validasi</span>
                                        @elseif($proposal->status_validasi == 'valid')
                                            <span class="badge rounded-pill" style="background:rgba(5,150,105,0.1);color:#059669;font-size:0.72rem;padding:0.3rem 0.65rem;">Valid</span>
                                        @elseif($proposal->status_validasi == 'tidak_valid')
                                            <span class="badge rounded-pill" style="background:rgba(220,38,38,0.1);color:#DC2626;font-size:0.72rem;padding:0.3rem 0.65rem;">Tidak Valid</span>
                                        @else
                                            <span class="badge rounded-pill" style="background:var(--surface-2);color:var(--text-600);font-size:0.72rem;padding:0.3rem 0.65rem;">{{ ucfirst(str_replace('_', ' ', $proposal->status)) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3" style="color:var(--text-600);font-size:0.82rem;">
                                        {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d M Y') }}
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($proposal->status == 'validasi_akhir_dosen_univ')
                                            <a href="{{ route('dosen.universitas.validasi.akhir.detail', $proposal->id_proposal) }}"
                                               class="btn btn-sm"
                                               style="background:rgba(245,158,11,0.12);color:#D97706;border-radius:7px;padding:0.3rem 0.75rem;font-size:0.8rem;font-weight:600;">
                                                <i class="fas fa-check-double me-1"></i>Validasi
                                            </a>
                                        @else
                                            <a href="{{ route('dosen.universitas.validasi.akhir.detail', $proposal->id_proposal) }}"
                                               class="btn btn-sm"
                                               style="background:var(--primary-100);color:var(--primary-700);border-radius:7px;padding:0.3rem 0.75rem;font-size:0.8rem;font-weight:600;">
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
                <div class="text-center py-5">
                    <div style="width:64px;height:64px;border-radius:16px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <i class="fas fa-file-alt" style="font-size:1.5rem;color:var(--text-400);"></i>
                    </div>
                    <h6 class="fw-semibold mb-1" style="color:var(--text-900);">Belum Ada Proposal</h6>
                    <p style="color:var(--text-600);font-size:0.875rem;">Proposal yang Anda dampingi sebagai Dosen Pendamping Universitas akan muncul di sini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.stat-card-icon.orange { background: rgba(245,158,11,0.1); color: #D97706; }
</style>
@endsection
