@extends('mainlayout.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')

@php
    $now = now();
    
    // Helper function to determine phase state
    $getPhaseState = function($startDate, $endDate, $status) use ($now) {
        if (!$startDate || !$endDate) {
            return $status === 'terbuka' ? 'current' : 'upcoming';
        }
        
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();
        
        if ($now->lt($start) && $status !== 'terbuka') return 'upcoming';
        if ($now->between($start, $end) || $status === 'terbuka') return 'current';
        return 'ended';
    };

    $ruangKontrolExists = isset($ruangKontrol) && $ruangKontrol;

    $phase1State = $ruangKontrolExists ? $getPhaseState($ruangKontrol->tanggal_pendaftaran_mulai, $ruangKontrol->tanggal_pendaftaran_selesai, $ruangKontrol->status_pendaftaran) : 'upcoming';
    $phase2State = $ruangKontrolExists ? $getPhaseState($ruangKontrol->tanggal_review_mulai, $ruangKontrol->tanggal_review_selesai, $ruangKontrol->status_review) : 'upcoming';
    $phase3State = $ruangKontrolExists ? $getPhaseState($ruangKontrol->tanggal_perbaikan_mulai, $ruangKontrol->tanggal_perbaikan_selesai, $ruangKontrol->status_perbaikan) : 'upcoming';
    $phase4State = $ruangKontrolExists ? $getPhaseState($ruangKontrol->tanggal_penilaian_akhir_mulai, $ruangKontrol->tanggal_penilaian_akhir_selesai, $ruangKontrol->status_penilaian_akhir) : 'upcoming';

    // Phase Styles
    $styleEnded = 'background-color: #70151f; color: white;';
    $styleCurrent = 'background-color: #a45a61; color: white;';
    $styleUpcoming = 'background-color: #e2e8f0; color: #6c757d;';

    $getPhaseStyle = function($state) use ($styleEnded, $styleCurrent, $styleUpcoming) {
        if ($state === 'ended') return $styleEnded;
        if ($state === 'current') return $styleCurrent;
        return $styleUpcoming;
    };

    // Current phase info for text below
    $currentPhaseName = '-';
    $currentPhaseDates = '';
    $currentPhaseStatus = 'DITUTUP';
    $alertBannerText = 'Sistem PKM saat ini ditutup. Nantikan informasi selanjutnya.';
    $alertBannerSub = 'Anda dapat mempersiapkan draf proposal Anda kapan saja.';
    
    if ($ruangKontrolExists) {
        if ($phase1State === 'current') {
            $currentPhaseName = 'Fase 1: Pendaftaran';
            $currentPhaseDates = $ruangKontrol->tanggal_pendaftaran_mulai ? \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_mulai)->format('d M') . ' - ' . \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_selesai)->format('d M Y') : 'Jadwal belum ditentukan';
            $currentPhaseStatus = 'DIBUKA';
            $alertBannerText = 'Fase 1 Pengajuan Proposal PKM Sedang Berlangsung!';
            $alertBannerSub = 'Segera ajukan proposal Anda sebelum batas waktu berakhir.';
        } elseif ($phase2State === 'current') {
            $currentPhaseName = 'Fase 2: Review';
            $currentPhaseDates = $ruangKontrol->tanggal_review_mulai ? \Carbon\Carbon::parse($ruangKontrol->tanggal_review_mulai)->format('d M') . ' - ' . \Carbon\Carbon::parse($ruangKontrol->tanggal_review_selesai)->format('d M Y') : 'Jadwal belum ditentukan';
            $currentPhaseStatus = 'DIBUKA';
            $alertBannerText = 'Fase 2 Review Proposal PKM Sedang Berlangsung.';
            $alertBannerSub = 'Proposal sedang dalam tahap penilaian oleh reviewer.';
        } elseif ($phase3State === 'current') {
            $currentPhaseName = 'Fase 3: Perbaikan';
            $currentPhaseDates = $ruangKontrol->tanggal_perbaikan_mulai ? \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_mulai)->format('d M') . ' - ' . \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai)->format('d M Y') : 'Jadwal belum ditentukan';
            $currentPhaseStatus = 'DIBUKA';
            $alertBannerText = 'Fase 3 Perbaikan Proposal PKM Sedang Berlangsung!';
            $alertBannerSub = 'Bagi yang lolos revisi, segera perbaiki proposal Anda.';
        } elseif ($phase4State === 'current') {
            $currentPhaseName = 'Fase 4: Penilaian Akhir';
            $currentPhaseDates = $ruangKontrol->tanggal_penilaian_akhir_mulai ? \Carbon\Carbon::parse($ruangKontrol->tanggal_penilaian_akhir_mulai)->format('d M') . ' - ' . \Carbon\Carbon::parse($ruangKontrol->tanggal_penilaian_akhir_selesai)->format('d M Y') : 'Jadwal belum ditentukan';
            $currentPhaseStatus = 'DIBUKA';
            $alertBannerText = 'Fase 4 Penilaian Akhir Proposal PKM Sedang Berlangsung.';
            $alertBannerSub = 'Menunggu pengumuman final pendanaan PKM.';
        } else {
            if ($phase4State === 'ended') {
                $currentPhaseName = 'Siklus PKM ' . $ruangKontrol->tahun_ajaran . ' Selesai';
                $alertBannerText = 'Siklus PKM ' . $ruangKontrol->tahun_ajaran . ' Telah Selesai.';
            }
            elseif ($phase3State === 'ended') {
                $currentPhaseName = 'Menunggu Penilaian Akhir';
                $alertBannerText = 'Fase 3 Perbaikan Telah Berakhir. Menunggu Fase 4.';
            }
            elseif ($phase2State === 'ended') {
                $currentPhaseName = 'Menunggu Perbaikan';
                $alertBannerText = 'Fase 2 Review Telah Berakhir. Menunggu Pengumuman Lolos.';
            }
            elseif ($phase1State === 'ended') {
                $currentPhaseName = 'Menunggu Review';
                $alertBannerText = 'Fase 1 Pendaftaran Telah Berakhir. (Tutup: ' . ($ruangKontrol->tanggal_pendaftaran_selesai ? \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_selesai)->format('d M Y') : '-') . ').';
                $alertBannerSub = 'Mohon nantikan pengumuman Fase 2. Anda masih dapat mempersiapkan draf Anda.';
            }
            else {
                $currentPhaseName = 'Belum Dimulai';
                $alertBannerText = 'Siklus PKM Belum Dimulai.';
            }
        }
    }
@endphp

<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
        ['label' => 'Dashboard', 'active' => true],
    ]" />

    <x-page-header 
        title="Dashboard Mahasiswa" 
        subtitle="Selamat datang kembali, {{ auth()->user()->name }}! Pantau tahapan dan kemajuan proposal PKM Anda."
        icon="fa-graduation-cap">
        <x-slot name="actions">
            @if($phase1State === 'current')
                <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Ajukan Proposal
                </a>
            @endif
            <a href="{{ route('mahasiswa.proposal.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-list me-1"></i>Proposal Saya
            </a>
        </x-slot>
    </x-page-header>

    {{-- Alert blocking proposal --}}
    @if(isset($blockingProposal) && $blockingProposal)
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center gap-3">
                <i class="fas fa-exclamation-triangle fa-lg flex-shrink-0 text-warning"></i>
                <div>
                    <strong>Anda sudah terdaftar dalam proposal "{{ $blockingProposal->judul_proposal }}"</strong><br>
                    <span class="text-muted" style="font-size: 0.875rem;">Satu mahasiswa hanya dapat terdaftar dalam satu proposal PKM per tahun akademik.</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Info Banner Alert --}}
    @if(isset($ruangKontrol) && $ruangKontrol)
        <div class="alert alert-info d-flex align-items-center gap-3 mb-4" role="alert">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; background: rgba(59, 130, 246, 0.15); color: #1D4ED8;">
                <i class="fas fa-bell"></i>
            </div>
            <div class="flex-grow-1">
                <strong style="font-size: 0.95rem;">{{ $alertBannerText }}</strong>
                <div class="text-muted" style="font-size: 0.875rem;">{{ $alertBannerSub }}</div>
            </div>
            <div class="text-end d-none d-md-block">
                <span class="badge {{ $currentPhaseStatus === 'DIBUKA' ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
                    {{ $currentPhaseStatus }}
                </span>
            </div>
        </div>
    @endif

    {{-- PKM Cycle Timeline Stepper Component --}}
    <div class="card card-custom mb-4">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-stream text-primary me-1"></i>
                <h6 class="mb-0 fw-bold">Alur Waktu Siklus PKM {{ $ruangKontrolExists ? $ruangKontrol->tahun_ajaran : '' }}</h6>
            </div>
            <span class="badge bg-light text-dark border">
                Fase Aktif: <strong class="{{ $currentPhaseStatus === 'DIBUKA' ? 'text-success' : 'text-danger' }}">{{ $currentPhaseName }}</strong>
                @if($currentPhaseDates) ({{ $currentPhaseDates }}) @endif
            </span>
        </div>
        <div class="card-body p-4">
            <div class="stepper-nav">
                {{-- Fase 1 --}}
                <div class="stepper-item {{ $phase1State === 'current' ? 'active' : ($phase1State === 'ended' ? 'completed' : '') }}">
                    <div class="stepper-badge">
                        @if($phase1State === 'ended') <i class="fas fa-check"></i> @else 1 @endif
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 0.875rem;">Fase 1: Pendaftaran</div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                            @if($phase1State === 'ended') Selesai @elseif($phase1State === 'current') Berlangsung @else Mendatang @endif
                        </small>
                    </div>
                </div>

                {{-- Fase 2 --}}
                <div class="stepper-item {{ $phase2State === 'current' ? 'active' : ($phase2State === 'ended' ? 'completed' : '') }}">
                    <div class="stepper-badge">
                        @if($phase2State === 'ended') <i class="fas fa-check"></i> @else 2 @endif
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 0.875rem;">Fase 2: Review</div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                            @if($phase2State === 'ended') Selesai @elseif($phase2State === 'current') Berlangsung @else Mendatang @endif
                        </small>
                    </div>
                </div>

                {{-- Fase 3 --}}
                <div class="stepper-item {{ $phase3State === 'current' ? 'active' : ($phase3State === 'ended' ? 'completed' : '') }}">
                    <div class="stepper-badge">
                        @if($phase3State === 'ended') <i class="fas fa-check"></i> @else 3 @endif
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 0.875rem;">Fase 3: Perbaikan</div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                            @if($phase3State === 'ended') Selesai @elseif($phase3State === 'current') Berlangsung @else Mendatang @endif
                        </small>
                    </div>
                </div>

                {{-- Fase 4 --}}
                <div class="stepper-item {{ $phase4State === 'current' ? 'active' : ($phase4State === 'ended' ? 'completed' : '') }}">
                    <div class="stepper-badge">
                        @if($phase4State === 'ended') <i class="fas fa-check"></i> @else 4 @endif
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size: 0.875rem;">Fase 4: Penilaian Akhir</div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">
                            @if($phase4State === 'ended') Selesai @elseif($phase4State === 'current') Berlangsung @else Mendatang @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="row g-4">
        {{-- Left Column: Submissions Overview --}}
        <div class="col-lg-5">
            <div class="card card-custom h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-chart-pie text-primary me-1"></i>
                        <h6 class="mb-0 fw-bold">Ringkasan Proposal Saya</h6>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-card p-3">
                                <div class="stat-card-icon maroon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-label">Total Proposal</div>
                                    <div class="stat-value">{{ $totalProposals }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card p-3">
                                <div class="stat-card-icon purple">
                                    <i class="fas fa-search"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-label">Sedang Review</div>
                                    <div class="stat-value">{{ $underReview }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card p-3">
                                <div class="stat-card-icon amber">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-card-info">
                                    <div class="stat-label">Menunggu Validasi</div>
                                    <div class="stat-value">{{ $waitingValidation }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card p-3">
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
                </div>
            </div>
        </div>

        {{-- Right Column: Submissions Saya --}}
        <div class="col-lg-7">
            <div class="card card-custom h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-folder-open text-primary me-1"></i>
                        <h6 class="mb-0 fw-bold">Daftar Proposal Terbaru</h6>
                    </div>
                    @if($proposals->count() > 0)
                        <a href="{{ route('mahasiswa.proposal.index') }}" class="btn btn-sm btn-outline-secondary" style="font-size: 0.8rem;">
                            Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if($proposals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern align-middle mb-0" id="dataTable">
                                <thead>
                                    <tr>
                                        <th>Judul Proposal</th>
                                        <th>Skim</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proposals as $proposal)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-truncate" style="max-width: 240px; color: var(--text-900);" title="{{ $proposal->judul_proposal }}">
                                                {{ $proposal->judul_proposal }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary-emphasis fw-bold">
                                                {{ $proposal->skim }}
                                            </span>
                                        </td>
                                        <td><x-status-badge :status="$proposal->status" /></td>
                                        <td class="text-muted" style="white-space: nowrap; font-size: 0.85rem;">
                                            {{ \Carbon\Carbon::parse($proposal->created_at)->format('d M Y') }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('mahasiswa.proposal.show', $proposal->id_proposal) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        {{-- Empty State Design --}}
                        <div class="empty-state-box">
                            <div class="empty-state-icon">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <div class="empty-state-title">Belum Ada Proposal Diajukan</div>
                            <p class="empty-state-text">
                                Anda belum memiliki proposal aktif. @if($phase1State === 'current') Fase pendaftaran saat ini sedang dibuka, silakan ajukan proposal tim Anda. @else Anda tetap dapat menyiapkan berkas draf sebelum jadwal dibuka. @endif
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                                @if($phase1State === 'current')
                                    <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Buat Draf Proposal Baru
                                    </a>
                                @else
                                    <button class="btn btn-secondary btn-sm" disabled title="Fase pendaftaran belum dibuka">
                                        <i class="fas fa-lock me-1"></i>Pendaftaran Ditutup
                                    </button>
                                @endif
                                <a href="https://simbelmawa.kemdikbud.go.id" target="_blank" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-book me-1"></i>Panduan PKM
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('#dataTable').length) {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                },
                "pageLength": 5,
                "order": [[ 3, "desc" ]],
                "dom": '<"d-flex justify-content-between align-items-center mb-3"lf>rtip'
            });
        }
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
