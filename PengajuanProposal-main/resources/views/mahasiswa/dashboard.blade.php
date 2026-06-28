@extends('mainlayout.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'active' => true],
    ]" />

    {{-- Welcome Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1" style="color: var(--text-900);">
                Selamat datang, <span style="color: var(--primary-700);">{{ auth()->user()->name }}</span> 👋
            </h1>
            <p class="mb-0" style="color: var(--text-600); font-size: 0.9rem;">Dashboard PKM — Lihat dan kelola proposal Anda</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            @if($proposalForRevision && $ruangKontrol && $ruangKontrol->status_perbaikan === 'terbuka')
                <a href="{{ route('mahasiswa.revisi.index') }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Revisi Proposal
                </a>
            @endif
            @if($ruangKontrol && $ruangKontrol->status_pendaftaran === 'terbuka')
                <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajukan Proposal
                </a>
            @endif
        </div>
    </div>

    {{-- Alert blocking proposal --}}
    @if(isset($blockingProposal) && $blockingProposal)
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-info-circle fa-lg flex-shrink-0"></i>
                <div>
                    <strong>Anda sudah terdaftar dalam proposal "{{ $blockingProposal->judul_proposal }}"</strong><br>
                    <span>Satu mahasiswa hanya dapat terdaftar dalam satu proposal PKM per tahun akademik.</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon maroon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">Total Proposal</div>
                    <div class="stat-value">{{ $totalProposals }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon purple">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">Sedang Direview</div>
                    <div class="stat-value">{{ $underReview }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon teal">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">Menunggu Validasi</div>
                    <div class="stat-value">{{ $waitingValidation }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">Disetujui</div>
                    <div class="stat-value">{{ $approved }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Proposal List --}}
    @if($proposals->count() > 0)
    <div class="card">
        <div class="card-header">
            <i class="fas fa-list"></i>
            <span>Daftar Proposal Saya</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="dataTable">
                    <thead>
                        <tr>
                            <th>Judul Proposal</th>
                            <th>Skim</th>
                            <th>Status</th>
                            <th>Tanggal Pengajuan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proposals as $proposal)
                        <tr>
                            <td>
                                <div class="fw-500" style="max-width: 320px; font-weight: 500; color: var(--text-900);">
                                    {{ $proposal->judul_proposal }}
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background: var(--primary-100); color: var(--primary-700); font-weight: 600;">
                                    {{ $proposal->skim }}
                                </span>
                            </td>
                            <td>
                                <x-status-badge :status="$proposal->status" />
                            </td>
                            <td style="white-space: nowrap; color: var(--text-600); font-size: 0.875rem;">
                                {{ \Carbon\Carbon::parse($proposal->created_at)->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('mahasiswa.proposal.show', $proposal->id_proposal) }}"
                                       class="btn btn-sm btn-info" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($proposal->status === 'revisi' && $ruangKontrol && $ruangKontrol->status_perbaikan === 'terbuka')
                                        <a href="{{ route('mahasiswa.revisi.index') }}"
                                           class="btn btn-sm btn-warning" title="Revisi Proposal">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-body text-center py-5">
            <div class="mb-3" style="font-size: 3rem; color: var(--text-400);">
                <i class="fas fa-file-alt"></i>
            </div>
            <h5 class="fw-bold" style="color: var(--text-900);">Belum Ada Proposal</h5>
            <p style="color: var(--text-600);">Anda belum mengajukan proposal PKM. Mulai sekarang!</p>
            @if($ruangKontrol && $ruangKontrol->status_pendaftaran === 'terbuka')
                <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus"></i> Ajukan Proposal Pertama
                </a>
            @else
                <div class="alert alert-info mt-3 d-inline-block text-start">
                    <i class="fas fa-info-circle me-2"></i>
                    Pendaftaran proposal sedang ditutup. Silakan tunggu hingga periode pendaftaran dibuka.
                </div>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            },
            "pageLength": 10,
            "order": [[ 3, "desc" ]],
            "dom": '<"d-flex justify-content-between align-items-center mb-3"lf>rtip'
        });
    });
</script>
@endpush
