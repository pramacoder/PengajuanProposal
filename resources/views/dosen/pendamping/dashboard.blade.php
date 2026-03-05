@extends('mainlayout.app')

@section('title', 'Dashboard Dosen Pendamping')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'active' => true],
    ]" />

    {{-- Welcome Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h4 fw-bold mb-1" style="color: var(--text-900);">
                Selamat datang, <span style="color: var(--primary-700);">{{ \App\Helpers\UserHelper::getCurrentUserName() }}</span> 👋
            </h1>
            <p class="mb-0" style="color: var(--text-600); font-size: 0.9rem;">Dashboard Dosen Pendamping — Validasi proposal yang ditugaskan kepada Anda</p>
        </div>
        {{-- Year Filter --}}
        <form method="GET" action="{{ route('dosen.pendamping.dashboard') }}">
            <div class="d-flex align-items-center gap-2">
                <label class="form-label mb-0 fw-semibold" style="font-size:0.8rem;color:var(--text-600);white-space:nowrap;">Tahun Ajaran</label>
                <select name="tahun_ajaran" id="tahun_ajaran" class="form-select form-select-sm" onchange="this.form.submit()"
                        style="border-color:var(--border);border-radius:8px;min-width:130px;font-size:0.85rem;">
                    @foreach($tahunAjaranList as $tahunAjaran)
                        <option value="{{ $tahunAjaran }}" {{ $tahunAjaranTerpilih == $tahunAjaran ? 'selected' : '' }}>
                            {{ $tahunAjaran }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon maroon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">Total Proposal</div>
                    <div class="stat-value">{{ $totalProposal }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">Pending Validasi</div>
                    <div class="stat-value">{{ $proposalPending }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">Valid</div>
                    <div class="stat-value">{{ $proposalValid }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon red">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">Tidak Valid</div>
                    <div class="stat-value">{{ $proposalTidakValid }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Proposal Table --}}
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <i class="fas fa-list"></i>
            <span>Daftar Proposal — Tahun Ajaran {{ $tahunAjaranTerpilih }}</span>
        </div>
        <div class="card-body p-0">
            @if($proposals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                        <thead style="background:var(--surface-2);border-bottom:1px solid var(--border);">
                            <tr>
                                <th class="px-4 py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">No</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Mahasiswa</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">NIM</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Judul Proposal</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Skim</th>
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
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="width:32px;height:32px;border-radius:50%;background:var(--primary-100);color:var(--primary-700);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.8rem;flex-shrink:0;">
                                                {{ strtoupper(substr($proposal->mahasiswa->nama_mhs, 0, 1)) }}
                                            </div>
                                            <span class="fw-medium" style="color:var(--text-900);">{{ $proposal->mahasiswa->nama_mhs }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3" style="color:var(--text-600);">{{ $proposal->mahasiswa->nim }}</td>
                                    <td class="py-3">
                                        <div class="text-truncate fw-medium" style="max-width:240px;color:var(--text-900);" title="{{ $proposal->judul }}">
                                            {{ $proposal->judul }}
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge rounded-pill" style="background:rgba(8,145,178,0.1);color:#0891B2;font-size:0.72rem;padding:0.3rem 0.65rem;">
                                            {{ $proposal->skim }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <x-status-badge :status="$proposal->status_validasi" />
                                    </td>
                                    <td class="py-3" style="color:var(--text-600);font-size:0.82rem;">
                                        {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('dosen.pendamping.proposal.detail', $proposal->id_proposal) }}"
                                               class="btn btn-sm" title="Lihat Detail"
                                               style="background:var(--primary-100);color:var(--primary-700);border-radius:7px;padding:0.3rem 0.6rem;">
                                                <i class="fas fa-eye" style="font-size:0.8rem;"></i>
                                            </a>
                                            @if($proposal->status_validasi == 'pending')
                                                <a href="{{ route('dosen.pendamping.proposal.detail', $proposal->id_proposal) }}#validasi"
                                                   class="btn btn-sm" title="Validasi"
                                                   style="background:rgba(245,158,11,0.12);color:#D97706;border-radius:7px;padding:0.3rem 0.6rem;">
                                                    <i class="fas fa-check" style="font-size:0.8rem;"></i>
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
                <div class="text-center py-5">
                    <div style="width:64px;height:64px;border-radius:16px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <i class="fas fa-file-alt" style="font-size:1.5rem;color:var(--text-400);"></i>
                    </div>
                    <h6 class="fw-semibold mb-1" style="color:var(--text-900);">Belum Ada Proposal</h6>
                    <p class="mb-1" style="color:var(--text-600);font-size:0.875rem;">Proposal yang ditugaskan kepada Anda untuk divalidasi akan muncul di sini.</p>
                    <small style="color:var(--text-400);">Tahun Ajaran: {{ $tahunAjaranTerpilih }}</small>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.stat-card-icon.orange { background: rgba(245,158,11,0.1); color: #D97706; }
.stat-card-icon.red    { background: rgba(220,38,38,0.1);  color: #DC2626; }
</style>
@endsection
