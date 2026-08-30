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
        subtitle="UNIVERSITAS UDAYANA" />

    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
        <div>
            <h5 class="fw-bold mb-1" style="color: var(--text-900); font-family: 'Inter', sans-serif;">
                Selamat Datang, {{ auth()->user()->name }} 👋
            </h5>
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

    {{-- PKM Cycle Timeline Component --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-5" style="color: var(--text-900);">Alur Waktu Siklus PKM</h5>
            
            <div class="timeline-container d-flex position-relative align-items-center justify-content-between mb-3 mt-3">
                {{-- Phase 1 --}}
                <div class="timeline-step flex-fill d-flex align-items-center position-relative">
                    @if($phase1State === 'current') <div class="text-center position-absolute w-100 text-danger fw-bold d-flex flex-column align-items-center" style="bottom: 100%; font-size: 0.8rem; z-index: 10; margin-bottom: 2px;"><span>saat ini</span><i class="fa-solid fa-caret-down" style="font-size: 1.2rem; line-height: 1; margin-top: -2px;"></i></div> @endif
                    <div class="timeline-bar text-center fw-semibold d-flex align-items-center justify-content-center" style="{{ $getPhaseStyle($phase1State) }} height: 36px; width: 100%; position: relative; clip-path: polygon(0 0, calc(100% - 15px) 0, 100% 50%, calc(100% - 15px) 100%, 0 100%);">
                        Fase 1 
                        @if($phase1State === 'ended') <span class="ms-2" style="font-size: 0.75rem; letter-spacing: 1px; color: rgba(255,255,255,0.7);">SELESAI</span> @endif
                    </div>
                </div>
                
                {{-- Phase 2 --}}
                <div class="timeline-step flex-fill d-flex align-items-center position-relative ms-n3">
                    @if($phase2State === 'current') <div class="text-center position-absolute w-100 text-danger fw-bold d-flex flex-column align-items-center" style="bottom: 100%; font-size: 0.8rem; z-index: 10; margin-bottom: 2px;"><span>saat ini</span><i class="fa-solid fa-caret-down" style="font-size: 1.2rem; line-height: 1; margin-top: -2px;"></i></div> @endif
                    <div class="timeline-bar text-center fw-semibold d-flex align-items-center justify-content-center" style="{{ $getPhaseStyle($phase2State) }} height: 36px; width: 100%; position: relative; clip-path: polygon(0 0, calc(100% - 15px) 0, 100% 50%, calc(100% - 15px) 100%, 0 100%, 15px 50%); padding-left: 15px;">
                        Fase 2
                        @if($phase2State === 'ended') <span class="ms-2" style="font-size: 0.75rem; letter-spacing: 1px; color: rgba(255,255,255,0.7);">SELESAI</span> @endif
                    </div>
                </div>

                {{-- Phase 3 --}}
                <div class="timeline-step flex-fill d-flex align-items-center position-relative ms-n3">
                    @if($phase3State === 'current') <div class="text-center position-absolute w-100 text-danger fw-bold d-flex flex-column align-items-center" style="bottom: 100%; font-size: 0.8rem; z-index: 10; margin-bottom: 2px;"><span>saat ini</span><i class="fa-solid fa-caret-down" style="font-size: 1.2rem; line-height: 1; margin-top: -2px;"></i></div> @endif
                    <div class="timeline-bar text-center fw-semibold d-flex align-items-center justify-content-center" style="{{ $getPhaseStyle($phase3State) }} height: 36px; width: 100%; position: relative; clip-path: polygon(0 0, calc(100% - 15px) 0, 100% 50%, calc(100% - 15px) 100%, 0 100%, 15px 50%); padding-left: 15px;">
                        Fase 3
                        @if($phase3State === 'ended') <span class="ms-2" style="font-size: 0.75rem; letter-spacing: 1px; color: rgba(255,255,255,0.7);">SELESAI</span> @endif
                    </div>
                </div>

                {{-- Phase 4 --}}
                <div class="timeline-step flex-fill d-flex align-items-center position-relative ms-n3">
                    @if($phase4State === 'current') <div class="text-center position-absolute w-100 text-danger fw-bold d-flex flex-column align-items-center" style="bottom: 100%; font-size: 0.8rem; z-index: 10; margin-bottom: 2px;"><span>saat ini</span><i class="fa-solid fa-caret-down" style="font-size: 1.2rem; line-height: 1; margin-top: -2px;"></i></div> @endif
                    <div class="timeline-bar text-center fw-semibold d-flex align-items-center justify-content-center" style="{{ $getPhaseStyle($phase4State) }} height: 36px; width: 100%; border-radius: 0 6px 6px 0; position: relative; clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%, 15px 50%); padding-left: 15px;">
                        Fase 4
                        @if($phase4State === 'ended') <span class="ms-2" style="font-size: 0.75rem; letter-spacing: 1px; color: rgba(255,255,255,0.7);">SELESAI</span> @endif
                    </div>
                </div>
            </div>

            <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                Fase Saat Ini ({{ $currentPhaseDates }}): <span class="fw-bold {{ $currentPhaseStatus === 'DIBUKA' ? 'text-success' : 'text-danger' }}">{{ $currentPhaseStatus }}</span> - {{ $currentPhaseName }}
            </p>
        </div>
    </div>

    {{-- Info Banner Alert --}}
    @if(isset($ruangKontrol) && $ruangKontrol)
        <div class="alert mb-4 border" role="alert" style="background-color: #f7e6e8; border-color: #9e2a2b !important; color: #4a0002; border-radius: 8px;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white" style="width: 32px; height: 32px; background-color: #70151f;">
                    <i class="fas fa-info fa-sm"></i>
                </div>
                <div>
                    <strong style="font-size: 0.95rem;">{{ $alertBannerText }}</strong><br>
                    <span style="font-size: 0.9rem;">
                        {{ $alertBannerSub }}
                    </span>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Content Grid --}}
    <div class="row g-4">
        {{-- Left Column: Submissions Overview --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0" style="color: var(--text-900);">Ringkasan Proposal Saya</h5>
                    <i class="far fa-question-circle text-muted"></i>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="stat-card-modern p-3 border rounded-3 d-flex align-items-center gap-3" style="background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                <div class="rounded p-2" style="background-color: #fee2e2; color: #b91c1c;">
                                    <i class="fas fa-file-alt fa-lg"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.8rem;">Total Proposal</div>
                                    <div class="fw-bold fs-4 text-dark">{{ $totalProposals }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card-modern p-3 border rounded-3 d-flex align-items-center gap-3" style="background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                <div class="rounded p-2" style="background-color: #fce7f3; color: #be185d;">
                                    <i class="fas fa-eye fa-lg"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.8rem;">Sedang Direview</div>
                                    <div class="fw-bold fs-4 text-dark">{{ $underReview }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card-modern p-3 border rounded-3 d-flex align-items-center gap-3" style="background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                <div class="rounded p-2" style="background-color: #f3e8ff; color: #7e22ce;">
                                    <i class="fas fa-clock fa-lg"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.8rem;">Menunggu Validasi</div>
                                    <div class="fw-bold fs-4 text-dark">{{ $waitingValidation }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card-modern p-3 border rounded-3 d-flex align-items-center gap-3" style="background-color: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                                <div class="rounded p-2" style="background-color: #dcfce7; color: #15803d;">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </div>
                                <div>
                                    <div class="text-muted" style="font-size: 0.8rem;">Disetujui</div>
                                    <div class="fw-bold fs-4 text-dark">{{ $approved }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Submissions Saya --}}
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="fw-bold mb-0" style="color: var(--text-900);">Proposal Saya</h5>
                </div>
                <div class="card-body p-4">
                    @if($proposals->count() > 0)
                        <div class="table-responsive">
                            <table class="table mb-0" id="dataTable">
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
                                            <div class="fw-500 text-truncate" style="max-width: 200px; font-weight: 500; color: var(--text-900);" title="{{ $proposal->judul_proposal }}">
                                                {{ $proposal->judul_proposal }}
                                            </div>
                                        </td>
                                        <td><span class="badge" style="background: var(--primary-100); color: var(--primary-700);">{{ $proposal->skim }}</span></td>
                                        <td><x-status-badge :status="$proposal->status" /></td>
                                        <td style="white-space: nowrap; color: var(--text-600); font-size: 0.875rem;">{{ \Carbon\Carbon::parse($proposal->created_at)->format('d M Y') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('mahasiswa.proposal.show', $proposal->id_proposal) }}" class="btn btn-sm btn-info" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        {{-- Empty State Design --}}
                        <div class="d-flex align-items-center gap-4 py-3">
                            <div class="flex-shrink-0 text-center ms-4">
                                {{-- Placeholder for illustration, using an icon if image not available --}}
                                <i class="fas fa-folder-open text-muted opacity-25" style="font-size: 8rem;"></i>
                            </div>
                            <div>
                                <p class="text-dark mb-1" style="font-size: 0.95rem;">
                                    Anda belum mengajukan proposal apapun. Meskipun Fase 1 ditutup, Anda tetap dapat menyiapkan draf proposal.
                                </p>
                                <p class="fw-bold mb-4" style="color: var(--text-900);">
                                    Belum Ada Proposal Diajukan. Mulai siapkan draf Anda!
                                </p>
                                <div class="d-flex gap-2">
                                    <div data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $phase1State === 'current' ? 'Ajukan Sekarang' : 'Akan Aktif Saat Fase Pendaftaran Dibuka' }}">
                                        @if($phase1State === 'current')
                                            <a href="{{ route('mahasiswa.proposal.create') }}" class="btn text-white fw-medium px-4" style="background-color: #70151f; border-radius: 6px;">
                                                Buat Draf Proposal Baru
                                            </a>
                                        @else
                                            <button class="btn text-white fw-medium px-4" style="background-color: #70151f; border-radius: 6px;" disabled>
                                                Buat Draf Proposal Baru
                                            </button>
                                        @endif
                                    </div>
                                    <button class="btn btn-outline-dark fw-medium px-4" style="border-radius: 6px; border-color: #cbd5e1;">
                                        Lihat Panduan PKM
                                    </button>
                                </div>
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
