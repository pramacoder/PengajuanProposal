@extends('mainlayout.app')

@section('title', 'Ruang Kontrol & Jadwal - Operator')

@section('content')
    @php
        if (!isset($ruangKontrolAktif) || !$ruangKontrolAktif) {
            $ruangKontrolAktif = (object) [
                'status_pendaftaran' => 'tertutup',
                'status_review' => 'tertutup',
                'status_perbaikan' => 'tertutup',
                'status_penilaian_akhir' => 'tertutup',
                'tanggal_pendaftaran_mulai' => null,
                'tanggal_pendaftaran_selesai' => null,
                'tanggal_review_mulai' => null,
                'tanggal_review_selesai' => null,
                'tanggal_perbaikan_mulai' => null,
                'tanggal_perbaikan_selesai' => null,
                'tanggal_penilaian_akhir_mulai' => null,
                'tanggal_penilaian_akhir_selesai' => null,
                'tahun_ajaran' => \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru(),
                'nama_history' => 'Jadwal Baru',
                'is_active' => false,
            ];
        }

        $phaseConfig = [
            'pendaftaran' => [
                'label' => 'Fase 1 | Pengajuan Proposal',
                'icon' => 'fa-file-upload',
                'color' => 'primary',
                'desc' => 'Mahasiswa submit proposal, dosen pendamping validasi',
                'status_field' => 'status_pendaftaran',
                'start_field' => 'tanggal_pendaftaran_mulai',
                'end_field' => 'tanggal_pendaftaran_selesai',
            ],
            'review' => [
                'label' => 'Fase 2 | Review',
                'icon' => 'fa-search',
                'color' => 'info',
                'desc' => 'Operator assign reviewer, reviewer review administratif & substantif',
                'status_field' => 'status_review',
                'start_field' => 'tanggal_review_mulai',
                'end_field' => 'tanggal_review_selesai',
            ],
            'perbaikan' => [
                'label' => 'Fase 3 | Revisi & Seleksi',
                'icon' => 'fa-edit',
                'color' => 'warning',
                'desc' => 'Mahasiswa revisi, reviewer seleksi, hasil semi final, validasi dosen universitas',
                'status_field' => 'status_perbaikan',
                'start_field' => 'tanggal_perbaikan_mulai',
                'end_field' => 'tanggal_perbaikan_selesai',
            ],
            'penilaian_akhir' => [
                'label' => 'Fase 4 | Penilaian Akhir',
                'icon' => 'fa-trophy',
                'color' => 'success',
                'desc' => 'Revisi akhir, penilaian Pimpinan PT, pengumuman hasil final',
                'status_field' => 'status_penilaian_akhir',
                'start_field' => 'tanggal_penilaian_akhir_mulai',
                'end_field' => 'tanggal_penilaian_akhir_selesai',
            ],
        ];

        $activePhase = null;
        foreach ($phaseConfig as $key => $cfg) {
            if (data_get($ruangKontrolAktif, $cfg['status_field']) === 'terbuka') {
                $activePhase = $key;
                break;
            }
        }
    @endphp

    <div class="container-fluid px-4 py-3">
        <x-breadcrumb :items="[
            ['label' => 'Beranda', 'url' => route('operator.dashboard')],
            ['label' => 'Ruang Kontrol & Jadwal', 'active' => true],
        ]" />

        <x-page-header 
            title="Ruang Kontrol & Jadwal" 
            subtitle="Kelola linimasa tahapan seleksi PKM, batas waktu pengunggahan, dan jadwal aktif universitas"
            badge="Operator Workspace"
            icon="fa-sliders-h"
        >
            <x-slot:actions>
                <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 shadow-sm rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalBuatJadwal">
                    <i class="fas fa-plus"></i>
                    <span>Buat Jadwal Baru</span>
                </button>
            </x-slot:actions>
        </x-page-header>

        {{-- Filter & Academic Year Selector --}}
        <div class="filter-card mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-4 col-lg-3">
                    <label class="form-label fw-bold mb-1 small text-secondary">
                        <i class="fas fa-calendar-alt text-primary me-1"></i> Tahun Akademik
                    </label>
                    <select class="form-select form-select-sm" id="tahunSelector" onchange="window.location.href='?tahun=' + this.value">
                        @php
                            $tahunAjaranTerpilih = $tahunAjaranTerpilih ?? \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru();
                            $tahunArray = [];
                            if (isset($histories) && $histories->count() > 0) {
                                foreach ($histories as $tahunAk => $items) {
                                    if (strpos($tahunAk, '/') !== false && count(explode('/', $tahunAk)) === 2) {
                                        $tahunArray[] = $tahunAk;
                                    }
                                }
                            }
                            $currentYear = (int) date('Y');
                            $currentMonth = (int) date('n');
                            $tahunAkademikSekarang = $currentMonth >= 7
                                ? $currentYear . '/' . ($currentYear + 1)
                                : ($currentYear - 1) . '/' . $currentYear;
                            $tahunAkademikDepan = $currentMonth >= 7
                                ? ($currentYear + 1) . '/' . ($currentYear + 2)
                                : $currentYear . '/' . ($currentYear + 1);
                            if (!in_array($tahunAkademikSekarang, $tahunArray))
                                $tahunArray[] = $tahunAkademikSekarang;
                            if (!in_array($tahunAkademikDepan, $tahunArray))
                                $tahunArray[] = $tahunAkademikDepan;
                            usort($tahunArray, fn($a, $b) => (int) explode('/', $b)[0] - (int) explode('/', $a)[0]);
                        @endphp
                        @foreach($tahunArray as $ta)
                            <option value="{{ $ta }}" @selected($ta === $tahunAjaranTerpilih)>
                                {{ $ta }} @if($ta === $tahunAkademikSekarang) (Tahun Berjalan) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8 col-lg-9 d-flex flex-wrap justify-content-md-end align-items-center gap-3">
                    <div class="d-inline-flex align-items-center gap-2 bg-light px-3 py-2 rounded-3 border">
                        <span class="small text-muted">Jadwal Aktif Terpasang:</span>
                        <strong class="text-dark small">{{ $ruangKontrolAktif->nama_history ?? 'Belum ada' }}</strong>
                        @if($ruangKontrolAktif && is_object($ruangKontrolAktif) && isset($ruangKontrolAktif->is_active) && $ruangKontrolAktif->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
                                <i class="fas fa-check-circle me-1"></i>Aktif
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill small">
                                Non-aktif
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Phase Timeline --}}
        <div class="ui-card mb-4">
            <div class="ui-card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-stream text-primary"></i>
                    <h5 class="mb-0 fw-bold fs-6">Timeline Linimasa Fase — {{ $tahunAjaranTerpilih }}</h5>
                </div>
                <span class="small text-muted">Tahapan Berurutan</span>
            </div>
            <div class="ui-card-body">
                <div class="row g-3">
                    @foreach($phaseConfig as $phaseKey => $cfg)
                        @php
                            $status = data_get($ruangKontrolAktif, $cfg['status_field'], 'tertutup');
                            $startDate = data_get($ruangKontrolAktif, $cfg['start_field']);
                            $endDate = data_get($ruangKontrolAktif, $cfg['end_field']);
                            $endDatePassed = $endDate && \Carbon\Carbon::parse($endDate)->endOfDay()->isPast();
                            $isOpen = $status === 'terbuka' && !$endDatePassed;
                            $isPast = $endDate && ($endDatePassed || (\Carbon\Carbon::parse($endDate)->endOfDay()->isPast() && !$isOpen));
                            $isFuture = $startDate && \Carbon\Carbon::parse($startDate)->startOfDay()->isFuture() && !$isOpen;
                        @endphp
                        <div class="col-md-6 col-xl-3">
                            <div class="card h-100 border transition-all rounded-3 {{ $isOpen ? 'border-primary shadow-sm ring-1 ring-primary' : 'border-light-subtle bg-light-subtle' }}" style="{{ $isOpen ? 'border-color: var(--primary-500) !important;' : '' }}">
                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge rounded-pill px-2.5 py-1 small fw-semibold {{ $isOpen ? 'bg-success text-white' : ($isPast ? 'bg-secondary text-white' : 'bg-light text-muted border') }}">
                                                <i class="fas {{ $isOpen ? 'fa-circle-play' : ($isPast ? 'fa-check' : 'fa-clock') }} me-1"></i>
                                                {{ $isOpen ? 'AKTIF' : ($isPast ? 'SELESAI' : 'BELUM DIMULAI') }}
                                            </span>
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: {{ $isOpen ? 'rgba(143,11,19,0.1)' : 'rgba(0,0,0,0.05)' }}; color: {{ $isOpen ? 'var(--primary-700)' : 'var(--text-400)' }};">
                                                <i class="fas {{ $cfg['icon'] }} small"></i>
                                            </div>
                                        </div>
                                        <h6 class="fw-bold mb-1 fs-6 text-dark">{{ $cfg['label'] }}</h6>
                                        <p class="text-muted small mb-3" style="font-size: 0.78rem; line-height: 1.4;">{{ $cfg['desc'] }}</p>
                                    </div>
                                    <div class="pt-2 border-top">
                                        @if($startDate && $endDate)
                                            <div class="small text-secondary mb-1" style="font-size: 0.78rem;">
                                                <i class="far fa-calendar-alt text-muted me-1"></i>
                                                <span>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</span>
                                                <span class="mx-1 text-muted">—</span>
                                                <span>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</span>
                                            </div>
                                            @if($isOpen && $endDate)
                                                @php
                                                    $targetDate = \Carbon\Carbon::parse($endDate)->endOfDay();
                                                    $diffInHours = (int) now()->diffInHours($targetDate, false);
                                                    $diffInDays = (int) now()->diffInDays($targetDate, false);
                                                    
                                                    if ($diffInHours >= 24) {
                                                        $timeLeftText = $diffInDays . ' hari tersisa';
                                                        $badgeClass = $diffInDays <= 3 ? 'bg-danger text-white' : ($diffInDays <= 7 ? 'bg-warning text-dark' : 'bg-primary-subtle text-primary border border-primary-subtle');
                                                    } elseif ($diffInHours >= 0 && $diffInHours < 24) {
                                                        $timeLeftText = $diffInHours . ' jam tersisa';
                                                        $badgeClass = 'bg-danger text-white';
                                                    } else {
                                                        $timeLeftText = 'Melewati batas waktu';
                                                        $badgeClass = 'bg-secondary text-white';
                                                    }
                                                @endphp
                                                <div>
                                                    <span class="badge {{ $badgeClass }} small px-2 py-0.5">
                                                        <i class="fas fa-stopwatch me-1"></i>{{ $timeLeftText }}
                                                    </span>
                                                </div>
                                            @endif
                                        @else
                                            <span class="small text-muted fst-italic"><i class="fas fa-info-circle me-1"></i>Tanggal belum diatur</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Active Schedule Editor --}}
        <div class="ui-card mb-4">
            <div class="ui-card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-sliders-h text-primary"></i>
                    <h5 class="mb-0 fw-bold fs-6">Konfigurasi Jadwal Aktif</h5>
                </div>
                @if($ruangKontrolAktif && is_object($ruangKontrolAktif) && isset($ruangKontrolAktif->is_active) && $ruangKontrolAktif->is_active)
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small">
                        <i class="fas fa-circle me-1" style="font-size:0.5rem;vertical-align:middle;"></i>Status: Aktif Berjalan
                    </span>
                @endif
            </div>
            <div class="ui-card-body p-0">
                <form id="formUpdateRuangKontrol">
                    @csrf
                    @if(isset($ruangKontrolAktif->id_ruang_kontrol))
                        <input type="hidden" name="id_ruang_kontrol" value="{{ $ruangKontrolAktif->id_ruang_kontrol }}">
                    @endif

                    {{-- Top meta row: fase aktif & nama jadwal --}}
                    <div class="p-4 border-bottom bg-light">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary mb-1">
                                    <i class="fas fa-bolt text-warning me-1"></i> FASE AKTIF SAAT INI
                                </label>
                                <select name="active_phase" id="activePhasePicker" class="form-select form-select-sm rounded-3">
                                    <option value="" @selected(!$activePhase)>— Tidak ada fase aktif (Tertutup) —</option>
                                    @foreach($phaseConfig as $phaseKey => $cfg)
                                        <option value="{{ $phaseKey }}" @selected($activePhase === $phaseKey)>
                                            {{ $cfg['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text small text-muted">Hanya satu tahapan yang dapat dibuka secara bersamaan untuk alur mahasiswa/reviewer.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary mb-1">
                                    <i class="fas fa-heading text-primary me-1"></i> NAMA JADWAL / SESI
                                </label>
                                <input type="text" name="nama_history" class="form-control form-control-sm rounded-3"
                                    value="{{ $ruangKontrolAktif->nama_history ?? '' }}"
                                    placeholder="Contoh: Jadwal PKM Udayana 2025/2026">
                                <div class="form-text small text-muted">Penamaan identifikasi sesi yang muncul pada rekaman laporan.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Phase rows --}}
                    <div class="phase-list p-0">
                        @foreach($phaseConfig as $phaseKey => $cfg)
                            @php
                                $phaseNum = $loop->iteration;
                                $accentColors = [
                                    ['bg' => 'rgba(143,11,19,0.08)', 'text' => '#8F0B13', 'border' => 'rgba(143,11,19,0.2)'],
                                    ['bg' => 'rgba(8,145,178,0.08)', 'text' => '#0891B2', 'border' => 'rgba(8,145,178,0.2)'],
                                    ['bg' => 'rgba(217,119,6,0.08)', 'text' => '#D97706', 'border' => 'rgba(217,119,6,0.2)'],
                                    ['bg' => 'rgba(5,150,105,0.08)', 'text' => '#059669', 'border' => 'rgba(5,150,105,0.2)'],
                                ];
                                $pc = $accentColors[$loop->index];
                            @endphp
                            <div class="p-3 px-4 {{ !$loop->last ? 'border-bottom' : '' }} transition-all" style="background: white;">
                                <div class="row align-items-center g-3">
                                    {{-- Step number + label --}}
                                    <div class="col-lg-5 d-flex align-items-center gap-3">
                                        <div style="width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0; background: {{ $pc['bg'] }}; border: 1px solid {{ $pc['border'] }}; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem; color: {{ $pc['text'] }};">
                                            {{ $phaseNum }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-1.5">
                                                <i class="fas {{ $cfg['icon'] }} small" style="color: {{ $pc['text'] }};"></i>
                                                <span>{{ $cfg['label'] }}</span>
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.78rem;">{{ $cfg['desc'] }}</div>
                                        </div>
                                    </div>
                                    {{-- Date fields --}}
                                    <div class="col-sm-6 col-lg-3">
                                        <label class="form-label mb-1 small fw-semibold text-secondary">Tanggal Mulai</label>
                                        <input type="date" name="{{ $cfg['start_field'] }}" class="form-control form-control-sm rounded-3"
                                            value="{{ data_get($ruangKontrolAktif, $cfg['start_field']) ? \Carbon\Carbon::parse(data_get($ruangKontrolAktif, $cfg['start_field']))->format('Y-m-d') : '' }}">
                                    </div>
                                    <div class="col-sm-6 col-lg-4">
                                        <label class="form-label mb-1 small fw-semibold text-secondary">Tanggal Selesai (Batas Akhir)</label>
                                        <input type="date" name="{{ $cfg['end_field'] }}" class="form-control form-control-sm rounded-3"
                                            value="{{ data_get($ruangKontrolAktif, $cfg['end_field']) ? \Carbon\Carbon::parse(data_get($ruangKontrolAktif, $cfg['end_field']))->format('Y-m-d') : '' }}">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Footer Action --}}
                    <div class="p-3 px-4 bg-light border-top d-flex justify-content-end align-items-center">
                        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 px-4 py-2 shadow-sm rounded-3 fw-semibold" id="btnSimpan">
                            <i class="fas fa-save"></i>
                            <span>Simpan Pengaturan Jadwal</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Schedule History Table --}}
        <div class="ui-card mb-4">
            <div class="ui-card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-list text-primary"></i>
                    <h5 class="mb-0 fw-bold fs-6">Daftar Arsip Jadwal — {{ $tahunAjaranTerpilih }}</h5>
                </div>
                <span class="badge bg-light text-secondary border px-2.5 py-1 small">
                    Total: {{ isset($jadwalTahun) ? $jadwalTahun->count() : 0 }} Jadwal
                </span>
            </div>
            <div class="ui-card-body p-0">
                @if(isset($jadwalTahun) && $jadwalTahun->count() > 0)
                    <div class="table-responsive">
                        <table class="table-modern">
                            <thead>
                                <tr>
                                    <th style="min-width: 180px;">Nama Jadwal</th>
                                    <th>Fase 1 (Pendaftaran)</th>
                                    <th>Fase 2 (Review)</th>
                                    <th>Fase 3 (Revisi/Seleksi)</th>
                                    <th>Fase 4 (Penilaian Akhir)</th>
                                    <th class="text-center" style="width: 110px;">Status</th>
                                    <th class="text-center" style="width: 130px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalTahun as $jadwal)
                                    @php
                                        $isActive = $jadwal->is_active;
                                        $fmtDate = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d/m/Y') : '-';
                                    @endphp
                                    <tr class="{{ $isActive ? 'bg-success-subtle bg-opacity-10' : '' }}">
                                        <td>
                                            <div class="fw-bold text-dark">{{ $jadwal->nama_history ?? '-' }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">Dibuat: {{ $jadwal->created_at ? $jadwal->created_at->format('d M Y') : '-' }}</small>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-secondary">{{ $fmtDate($jadwal->tanggal_pendaftaran_mulai) }}</div>
                                            <div class="small text-muted">s/d {{ $fmtDate($jadwal->tanggal_pendaftaran_selesai) }}</div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-secondary">{{ $fmtDate($jadwal->tanggal_review_mulai) }}</div>
                                            <div class="small text-muted">s/d {{ $fmtDate($jadwal->tanggal_review_selesai) }}</div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-secondary">{{ $fmtDate($jadwal->tanggal_perbaikan_mulai) }}</div>
                                            <div class="small text-muted">s/d {{ $fmtDate($jadwal->tanggal_perbaikan_selesai) }}</div>
                                        </td>
                                        <td>
                                            <div class="small fw-semibold text-secondary">{{ $fmtDate($jadwal->tanggal_penilaian_akhir_mulai) }}</div>
                                            <div class="small text-muted">s/d {{ $fmtDate($jadwal->tanggal_penilaian_akhir_selesai) }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if($isActive)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small">
                                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill small">
                                                    Non-aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                @if(!$isActive)
                                                    <button class="btn btn-outline-success btn-activate-jadwal"
                                                        data-id="{{ $jadwal->id_ruang_kontrol }}" title="Jadikan Jadwal Aktif">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                <button class="btn btn-outline-primary btn-edit-jadwal"
                                                    data-id="{{ $jadwal->id_ruang_kontrol }}" title="Edit Detail Jadwal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-delete-jadwal"
                                                    data-id="{{ $jadwal->id_ruang_kontrol }}" title="Hapus Jadwal">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state-box text-center py-5">
                        <div class="empty-state-icon mb-3">
                            <i class="fas fa-calendar-times fa-3x text-muted opacity-50"></i>
                        </div>
                        <h6 class="fw-bold text-dark">Belum Ada Jadwal</h6>
                        <p class="text-muted small mb-3">Belum ada jadwal yang dikonfigurasi untuk tahun ajaran {{ $tahunAjaranTerpilih }}.</p>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatJadwal">
                            <i class="fas fa-plus me-1"></i>Buat Jadwal Pertama
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal: Buat Jadwal Baru --}}
    <div class="modal fade" id="modalBuatJadwal" tabindex="-1" aria-labelledby="modalBuatJadwalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fs-6 fw-bold" id="modalBuatJadwalLabel">
                        <i class="fas fa-plus-circle me-2"></i>Buat Jadwal Sesi Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formBuatJadwal">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary">Nama Jadwal / Sesi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_history" class="form-control rounded-3" required
                                placeholder="Contoh: Jadwal PKM Utama 2025/2026">
                        </div>

                        <div class="vstack gap-3">
                            @foreach($phaseConfig as $phaseKey => $cfg)
                                <div class="card border rounded-3 p-3 bg-light-subtle">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas {{ $cfg['icon'] }} text-primary"></i>
                                        <h6 class="fw-bold mb-0 text-dark small">{{ $cfg['label'] }}</h6>
                                    </div>
                                    <p class="text-muted small mb-3" style="font-size:0.75rem;">{{ $cfg['desc'] }}</p>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-secondary">Tanggal Mulai <span class="text-danger">*</span></label>
                                            <input type="date" name="{{ $cfg['start_field'] }}" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-secondary">Tanggal Selesai <span class="text-danger">*</span></label>
                                            <input type="date" name="{{ $cfg['end_field'] }}" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-sm btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3" id="btnBuatJadwal">
                            <i class="fas fa-save me-1"></i>Buat Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Edit Jadwal --}}
    <div class="modal fade" id="modalEditJadwal" tabindex="-1" aria-labelledby="modalEditJadwalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fs-6 fw-bold" id="modalEditJadwalLabel">
                        <i class="fas fa-edit me-2"></i>Perbarui Jadwal Sesi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditJadwal">
                    @csrf
                    <input type="hidden" name="jadwal_id" id="editJadwalId">
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary">Nama Jadwal / Sesi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_history" id="editNamaHistory" class="form-control rounded-3" required>
                        </div>
                        <div class="vstack gap-3">
                            @foreach($phaseConfig as $phaseKey => $cfg)
                                <div class="card border rounded-3 p-3 bg-light-subtle">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas {{ $cfg['icon'] }} text-primary"></i>
                                        <h6 class="fw-bold mb-0 text-dark small">{{ $cfg['label'] }}</h6>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-secondary">Tanggal Mulai</label>
                                            <input type="date" name="{{ $cfg['start_field'] }}"
                                                id="edit_{{ $cfg['start_field'] }}" class="form-control form-control-sm rounded-3">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold text-secondary">Tanggal Selesai</label>
                                            <input type="date" name="{{ $cfg['end_field'] }}" id="edit_{{ $cfg['end_field'] }}"
                                                class="form-control form-control-sm rounded-3">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-sm btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3" id="btnEditJadwal">
                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || document.querySelector('input[name="_token"]')?.value;

            function showAlert(type, message) {
                if (window.AppUI && typeof window.AppUI.showToast === 'function') {
                    window.AppUI.showToast(message, type === 'danger' ? 'danger' : (type === 'success' ? 'success' : 'info'));
                    return;
                }
                const container = document.querySelector('.container-fluid');
                const existing = container.querySelector('.alert-dynamic');
                if (existing) existing.remove();
                const alert = document.createElement('div');
                alert.className = `alert alert-${type} alert-dismissible fade show alert-dynamic shadow-sm mt-3`;
                alert.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
                container.insertBefore(alert, container.children[1]);
                setTimeout(() => alert.remove(), 5000);
            }

            function validateDateRanges(formEl) {
                const phasePairs = [
                    ['tanggal_pendaftaran_mulai', 'tanggal_pendaftaran_selesai', 'Fase 1 (Pendaftaran)'],
                    ['tanggal_review_mulai', 'tanggal_review_selesai', 'Fase 2 (Review)'],
                    ['tanggal_perbaikan_mulai', 'tanggal_perbaikan_selesai', 'Fase 3 (Revisi/Seleksi)'],
                    ['tanggal_penilaian_akhir_mulai', 'tanggal_penilaian_akhir_selesai', 'Fase 4 (Penilaian Akhir)'],
                ];

                for (const [startName, endName, phaseLabel] of phasePairs) {
                    const startInput = formEl.querySelector(`[name="${startName}"]`);
                    const endInput = formEl.querySelector(`[name="${endName}"]`);
                    if (!startInput || !endInput || !startInput.value || !endInput.value) continue;

                    if (startInput.value > endInput.value) {
                        showAlert('danger', `${phaseLabel}: tanggal mulai tidak boleh lebih besar dari tanggal selesai.`);
                        startInput.focus();
                        return false;
                    }
                }
                return true;
            }

            // Update ruang kontrol (active schedule)
            document.getElementById('formUpdateRuangKontrol')?.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!validateDateRanges(this)) return;
                const btn = document.getElementById('btnSimpan');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';

                const formData = new FormData(this);
                fetch('{{ route("operator.update.ruang.kontrol") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', data.message);
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert('danger', data.message || 'Gagal menyimpan pengaturan.');
                        }
                    })
                    .catch(() => showAlert('danger', 'Terjadi kesalahan jaringan.'))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-save me-2"></i>Simpan Pengaturan Jadwal';
                    });
            });

            // Create jadwal
            document.getElementById('formBuatJadwal')?.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!validateDateRanges(this)) return;
                const btn = document.getElementById('btnBuatJadwal');
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Membuat...';

                fetch('{{ route("operator.create.jadwal") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: new FormData(this)
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', data.message);
                            bootstrap.Modal.getInstance(document.getElementById('modalBuatJadwal')).hide();
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert('danger', data.message || 'Gagal membuat jadwal.');
                        }
                    })
                    .catch(() => showAlert('danger', 'Terjadi kesalahan jaringan.'))
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-save me-2"></i>Buat Jadwal';
                    });
            });

            // Edit jadwal — load data
            document.querySelectorAll('.btn-edit-jadwal').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.dataset.id;
                    fetch(`/operator/jadwal/${id}`, { headers: { 'Accept': 'application/json' } })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                const j = data.data;
                                document.getElementById('editJadwalId').value = j.id_ruang_kontrol;
                                document.getElementById('editNamaHistory').value = j.nama_history || '';
                                const dateFields = [
                                    'tanggal_pendaftaran_mulai', 'tanggal_pendaftaran_selesai',
                                    'tanggal_review_mulai', 'tanggal_review_selesai',
                                    'tanggal_perbaikan_mulai', 'tanggal_perbaikan_selesai',
                                    'tanggal_penilaian_akhir_mulai', 'tanggal_penilaian_akhir_selesai',
                                ];
                                dateFields.forEach(f => {
                                    const el = document.getElementById('edit_' + f);
                                    if (el && j[f]) el.value = j[f].substring(0, 10);
                                    else if (el) el.value = '';
                                });
                                new bootstrap.Modal(document.getElementById('modalEditJadwal')).show();
                            }
                        });
                });
            });

            // Edit jadwal — submit
            document.getElementById('formEditJadwal')?.addEventListener('submit', function (e) {
                e.preventDefault();
                if (!validateDateRanges(this)) return;
                const id = document.getElementById('editJadwalId').value;
                const btn = document.getElementById('btnEditJadwal');
                btn.disabled = true;

                fetch(`/operator/update-jadwal/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: new FormData(this)
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', data.message);
                            bootstrap.Modal.getInstance(document.getElementById('modalEditJadwal')).hide();
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showAlert('danger', data.message || 'Gagal mengupdate jadwal.');
                        }
                    })
                    .catch(() => showAlert('danger', 'Terjadi kesalahan jaringan.'))
                    .finally(() => { btn.disabled = false; });
            });

            // Activate jadwal
            document.querySelectorAll('.btn-activate-jadwal').forEach(btn => {
                btn.addEventListener('click', function () {
                    if (!confirm('Aktifkan jadwal ini? Jadwal lain untuk tahun ajaran yang sama akan dinonaktifkan.')) return;
                    const id = this.dataset.id;
                    fetch(`/operator/activate-jadwal/${id}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                showAlert('success', data.message);
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                showAlert('danger', data.message);
                            }
                        })
                        .catch(() => showAlert('danger', 'Terjadi kesalahan.'));
                });
            });

            // Delete jadwal
            document.querySelectorAll('.btn-delete-jadwal').forEach(btn => {
                btn.addEventListener('click', function () {
                    if (!confirm('Hapus jadwal ini? Tindakan ini tidak dapat dibatalkan.')) return;
                    const id = this.dataset.id;
                    fetch(`/operator/delete-jadwal/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                showAlert('success', data.message);
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                showAlert('danger', data.message);
                            }
                        })
                        .catch(() => showAlert('danger', 'Terjadi kesalahan.'));
                });
            });
        });
    </script>
@endpush