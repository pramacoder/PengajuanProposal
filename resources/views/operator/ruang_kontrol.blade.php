@extends('operator.layout')

@section('title', 'Ruang Kontrol - Operator')

@section('content')
@php
    if (!isset($ruangKontrolAktif) || !$ruangKontrolAktif) {
        $ruangKontrolAktif = (object)[
            'status_pendaftaran' => 'tertutup',
            'status_review' => 'tertutup',
            'status_perbaikan' => 'tertutup',
            'status_penilaian_akhir' => 'tertutup',
            'tanggal_pendaftaran_mulai' => null, 'tanggal_pendaftaran_selesai' => null,
            'tanggal_review_mulai' => null, 'tanggal_review_selesai' => null,
            'tanggal_perbaikan_mulai' => null, 'tanggal_perbaikan_selesai' => null,
            'tanggal_penilaian_akhir_mulai' => null, 'tanggal_penilaian_akhir_selesai' => null,
            'tahun_ajaran' => \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru(),
            'nama_history' => 'Jadwal Baru',
            'is_active' => false,
        ];
    }

    $phaseConfig = [
        'pendaftaran' => [
            'label' => 'Fase 1 — Pengajuan Proposal',
            'icon' => 'fa-file-upload',
            'color' => 'primary',
            'desc' => 'Mahasiswa submit proposal, dosen pendamping validasi',
            'status_field' => 'status_pendaftaran',
            'start_field' => 'tanggal_pendaftaran_mulai',
            'end_field' => 'tanggal_pendaftaran_selesai',
        ],
        'review' => [
            'label' => 'Fase 2 — Review',
            'icon' => 'fa-search',
            'color' => 'info',
            'desc' => 'Operator assign reviewer, reviewer review administratif & substantif',
            'status_field' => 'status_review',
            'start_field' => 'tanggal_review_mulai',
            'end_field' => 'tanggal_review_selesai',
        ],
        'perbaikan' => [
            'label' => 'Fase 3 — Revisi & Seleksi',
            'icon' => 'fa-edit',
            'color' => 'warning',
            'desc' => 'Mahasiswa revisi, reviewer seleksi, hasil semi final, validasi dosen universitas',
            'status_field' => 'status_perbaikan',
            'start_field' => 'tanggal_perbaikan_mulai',
            'end_field' => 'tanggal_perbaikan_selesai',
        ],
        'penilaian_akhir' => [
            'label' => 'Fase 4 — Penilaian Akhir',
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

<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('operator.dashboard')],
        ['label' => 'Ruang Kontrol', 'active' => true],
    ]" />

    <x-page-header title="RUANG KONTROL" subtitle="UNIVERSITAS UDAYANA" />

    {{-- Year Selector --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <label class="form-label fw-bold mb-2">
                <i class="fas fa-calendar-alt me-2"></i>Pilih Tahun Ajaran
            </label>
            <select class="form-select" id="tahunSelector" onchange="window.location.href='?tahun=' + this.value">
                @php
                    $tahunAjaranTerpilih = $tahunAjaranTerpilih ?? \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru();
                    $tahunArray = [];
                    if(isset($histories) && $histories->count() > 0) {
                        foreach($histories as $tahunAk => $items) {
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
                    if (!in_array($tahunAkademikSekarang, $tahunArray)) $tahunArray[] = $tahunAkademikSekarang;
                    if (!in_array($tahunAkademikDepan, $tahunArray)) $tahunArray[] = $tahunAkademikDepan;
                    usort($tahunArray, fn($a, $b) => (int)explode('/', $b)[0] - (int)explode('/', $a)[0]);
                @endphp
                @foreach($tahunArray as $ta)
                    <option value="{{ $ta }}" @selected($ta === $tahunAjaranTerpilih)>
                        {{ $ta }} @if($ta === $tahunAkademikSekarang) (Sekarang) @endif
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-9 d-flex align-items-end justify-content-end gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBuatJadwal">
                <i class="fas fa-plus me-1"></i>Buat Jadwal Baru
            </button>
        </div>
    </div>

    {{-- Phase Timeline --}}
    <div class="card card-custom mb-4">
        <div class="card-header card-header-custom">
            <h5 class="mb-0"><i class="fas fa-stream me-2"></i>Timeline Fase — {{ $tahunAjaranTerpilih }}</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($phaseConfig as $phaseKey => $cfg)
                    @php
                        $status = data_get($ruangKontrolAktif, $cfg['status_field'], 'tertutup');
                        $isOpen = $status === 'terbuka';
                        $startDate = data_get($ruangKontrolAktif, $cfg['start_field']);
                        $endDate = data_get($ruangKontrolAktif, $cfg['end_field']);
                        $isPast = $endDate && \Carbon\Carbon::parse($endDate)->isPast() && !$isOpen;
                        $isFuture = $startDate && \Carbon\Carbon::parse($startDate)->isFuture() && !$isOpen;
                    @endphp
                    <div class="col-md-3">
                        <div class="card h-100 border-{{ $isOpen ? $cfg['color'] : ($isPast ? 'secondary' : 'light') }} {{ $isOpen ? 'shadow' : '' }}">
                            <div class="card-body text-center p-3">
                                <div class="mb-2">
                                    <span class="badge bg-{{ $isOpen ? $cfg['color'] : ($isPast ? 'secondary' : 'light text-dark') }} rounded-pill px-3 py-2">
                                        <i class="fas {{ $cfg['icon'] }} me-1"></i>
                                        {{ $isOpen ? 'AKTIF' : ($isPast ? 'SELESAI' : 'BELUM DIMULAI') }}
                                    </span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ $cfg['label'] }}</h6>
                                <small class="text-muted d-block mb-2">{{ $cfg['desc'] }}</small>
                                @if($startDate && $endDate)
                                    <div class="small">
                                        <i class="far fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} —
                                        {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                                    </div>
                                    @if($isOpen && $endDate)
                                        @php $daysLeft = now()->diffInDays(\Carbon\Carbon::parse($endDate), false); @endphp
                                        <div class="mt-1">
                                            <span class="badge bg-{{ $daysLeft <= 3 ? 'danger' : ($daysLeft <= 7 ? 'warning' : $cfg['color']) }}">
                                                {{ $daysLeft >= 0 ? $daysLeft . ' hari tersisa' : 'Melewati deadline' }}
                                            </span>
                                        </div>
                                    @endif
                                @else
                                    <small class="text-muted"><i>Tanggal belum diatur</i></small>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Active Schedule Editor --}}
    <div class="card card-custom mb-4">
        <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-sliders-h" style="color: var(--primary-700);"></i>
                <span class="fw-bold" style="font-size:0.95rem;">Pengaturan Jadwal Aktif</span>
            </div>
            @if($ruangKontrolAktif && is_object($ruangKontrolAktif) && isset($ruangKontrolAktif->is_active) && $ruangKontrolAktif->is_active)
                <span class="badge" style="background:rgba(5,150,105,0.12);color:#059669;border:1px solid rgba(5,150,105,0.25);font-size:0.75rem;padding:0.35rem 0.75rem;border-radius:20px;">
                    <i class="fas fa-circle" style="font-size:0.5rem;vertical-align:middle;margin-right:4px;"></i>Aktif
                </span>
            @endif
        </div>
        <div class="card-body p-0">
            <form id="formUpdateRuangKontrol">
                @csrf
                @if(isset($ruangKontrolAktif->id_ruang_kontrol))
                    <input type="hidden" name="id_ruang_kontrol" value="{{ $ruangKontrolAktif->id_ruang_kontrol }}">
                @endif

                {{-- Top meta row: fase aktif & nama jadwal --}}
                <div class="px-4 py-3" style="border-bottom: 1px solid var(--border); background: var(--surface-2);">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="font-size:0.8rem;color:var(--text-600);">FASE AKTIF SAAT INI</label>
                            <select name="active_phase" id="activePhasePicker" class="form-select form-select-sm" style="border-color:var(--border);border-radius:8px;">
                                <option value="" @selected(!$activePhase)>— Tidak ada fase aktif —</option>
                                @foreach($phaseConfig as $phaseKey => $cfg)
                                    <option value="{{ $phaseKey }}" @selected($activePhase === $phaseKey)>
                                        {{ $cfg['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-1" style="font-size:0.8rem;color:var(--text-600);">NAMA JADWAL</label>
                            <input type="text" name="nama_history" class="form-control form-control-sm"
                                   value="{{ $ruangKontrolAktif->nama_history ?? '' }}"
                                   placeholder="Contoh: Jadwal PKM 2025/2026"
                                   style="border-color:var(--border);border-radius:8px;">
                        </div>
                    </div>
                </div>

                {{-- Phase rows --}}
                <div class="phase-list">
                    @foreach($phaseConfig as $phaseKey => $cfg)
                        @php
                            $phaseNum = $loop->iteration;
                            $phaseColors = ['maroon','teal','purple','green'];
                            $dotColors = [
                                'maroon' => ['bg' => 'rgba(143,11,19,0.1)', 'text' => '#8F0B13', 'border' => 'rgba(143,11,19,0.2)'],
                                'teal'   => ['bg' => 'rgba(8,145,178,0.1)', 'text' => '#0891B2', 'border' => 'rgba(8,145,178,0.2)'],
                                'purple' => ['bg' => 'rgba(124,58,237,0.1)', 'text' => '#7C3AED', 'border' => 'rgba(124,58,237,0.2)'],
                                'green'  => ['bg' => 'rgba(5,150,105,0.1)', 'text' => '#059669', 'border' => 'rgba(5,150,105,0.2)'],
                            ];
                            $pc = $dotColors[$phaseColors[$loop->index]];
                        @endphp
                        <div class="phase-row px-4 py-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color: var(--border) !important;">
                            <div class="row align-items-center g-3">
                                {{-- Step number + label --}}
                                <div class="col-md-4 d-flex align-items-center gap-3">
                                    <div class="phase-step-num" style="
                                        width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
                                        background: {{ $pc['bg'] }}; border: 1px solid {{ $pc['border'] }};
                                        display: flex; align-items: center; justify-content: center;
                                        font-weight: 800; font-size: 0.85rem; color: {{ $pc['text'] }};
                                    ">{{ $phaseNum }}</div>
                                    <div>
                                        <div class="fw-semibold" style="font-size:0.875rem;color:var(--text-900);line-height:1.3;">
                                            <i class="fas {{ $cfg['icon'] }} me-1" style="color:{{ $pc['text'] }};font-size:0.8rem;"></i>
                                            {{ $cfg['label'] }}
                                        </div>
                                        <div style="font-size:0.72rem;color:var(--text-400);margin-top:1px;">{{ $cfg['desc'] }}</div>
                                    </div>
                                </div>
                                {{-- Date fields --}}
                                <div class="col-md-4">
                                    <label class="form-label mb-1" style="font-size:0.72rem;color:var(--text-600);font-weight:600;text-transform:uppercase;letter-spacing:0.03em;">Tanggal Mulai</label>
                                    <input type="date" name="{{ $cfg['start_field'] }}" class="form-control form-control-sm"
                                           value="{{ data_get($ruangKontrolAktif, $cfg['start_field']) ? \Carbon\Carbon::parse(data_get($ruangKontrolAktif, $cfg['start_field']))->format('Y-m-d') : '' }}"
                                           style="border-color:var(--border);border-radius:8px;">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label mb-1" style="font-size:0.72rem;color:var(--text-600);font-weight:600;text-transform:uppercase;letter-spacing:0.03em;">Tanggal Selesai</label>
                                    <input type="date" name="{{ $cfg['end_field'] }}" class="form-control form-control-sm"
                                           value="{{ data_get($ruangKontrolAktif, $cfg['end_field']) ? \Carbon\Carbon::parse(data_get($ruangKontrolAktif, $cfg['end_field']))->format('Y-m-d') : '' }}"
                                           style="border-color:var(--border);border-radius:8px;">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Footer / Action --}}
                <div class="px-4 py-3 d-flex justify-content-end" style="border-top: 1px solid var(--border); background: var(--surface-2);">
                    <button type="submit" class="btn btn-sm fw-semibold d-flex align-items-center gap-2" id="btnSimpan"
                            style="background:var(--primary-700);color:white;border-radius:8px;padding:0.5rem 1.25rem;border:none;box-shadow:0 2px 8px rgba(143,11,19,0.25);transition:all 0.2s ease;"
                            onmouseover="this.style.background='var(--primary-900)'" onmouseout="this.style.background='var(--primary-700)'">
                        <i class="fas fa-save" style="font-size:0.85rem;"></i>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- Schedule List --}}
    <div class="card card-custom mb-4">
        <div class="card-header card-header-custom">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Daftar Jadwal — {{ $tahunAjaranTerpilih }}</h5>
        </div>
        <div class="card-body">
            @if(isset($jadwalTahun) && $jadwalTahun->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Nama Jadwal</th>
                                <th>Fase 1</th>
                                <th>Fase 2</th>
                                <th>Fase 3</th>
                                <th>Fase 4</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwalTahun as $jadwal)
                                @php
                                    $isActive = $jadwal->is_active;
                                    $fmtDate = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('d/m/y') : '-';
                                @endphp
                                <tr class="{{ $isActive ? 'table-success' : '' }}">
                                    <td class="fw-bold">{{ $jadwal->nama_history ?? '-' }}</td>
                                    <td class="small">{{ $fmtDate($jadwal->tanggal_pendaftaran_mulai) }} — {{ $fmtDate($jadwal->tanggal_pendaftaran_selesai) }}</td>
                                    <td class="small">{{ $fmtDate($jadwal->tanggal_review_mulai) }} — {{ $fmtDate($jadwal->tanggal_review_selesai) }}</td>
                                    <td class="small">{{ $fmtDate($jadwal->tanggal_perbaikan_mulai) }} — {{ $fmtDate($jadwal->tanggal_perbaikan_selesai) }}</td>
                                    <td class="small">{{ $fmtDate($jadwal->tanggal_penilaian_akhir_mulai) }} — {{ $fmtDate($jadwal->tanggal_penilaian_akhir_selesai) }}</td>
                                    <td>
                                        @if($isActive)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Non-aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            @if(!$isActive)
                                                <button class="btn btn-outline-success btn-activate-jadwal" data-id="{{ $jadwal->id_ruang_kontrol }}" title="Aktifkan">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-outline-primary btn-edit-jadwal" data-id="{{ $jadwal->id_ruang_kontrol }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-delete-jadwal" data-id="{{ $jadwal->id_ruang_kontrol }}" title="Hapus">
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
                <div class="text-center text-muted py-4">
                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                    <p>Belum ada jadwal untuk tahun ajaran {{ $tahunAjaranTerpilih }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal: Buat Jadwal Baru --}}
<div class="modal fade" id="modalBuatJadwal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Buat Jadwal Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formBuatJadwal">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Jadwal <span class="text-danger">*</span></label>
                        <input type="text" name="nama_history" class="form-control" required placeholder="Contoh: Jadwal Utama 2025/2026">
                    </div>

                    @foreach($phaseConfig as $phaseKey => $cfg)
                        <div class="card mb-3 border-start border-4 border-{{ $cfg['color'] }}">
                            <div class="card-body py-3">
                                <h6 class="fw-bold text-{{ $cfg['color'] }}">
                                    <i class="fas {{ $cfg['icon'] }} me-2"></i>{{ $cfg['label'] }}
                                </h6>
                                <small class="text-muted d-block mb-2">{{ $cfg['desc'] }}</small>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">Tanggal Mulai <span class="text-danger">*</span></label>
                                        <input type="date" name="{{ $cfg['start_field'] }}" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Tanggal Selesai <span class="text-danger">*</span></label>
                                        <input type="date" name="{{ $cfg['end_field'] }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnBuatJadwal">
                        <i class="fas fa-save me-2"></i>Buat Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Edit Jadwal --}}
<div class="modal fade" id="modalEditJadwal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditJadwal">
                @csrf
                <input type="hidden" name="jadwal_id" id="editJadwalId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Jadwal <span class="text-danger">*</span></label>
                        <input type="text" name="nama_history" id="editNamaHistory" class="form-control" required>
                    </div>
                    @foreach($phaseConfig as $phaseKey => $cfg)
                        <div class="card mb-3 border-start border-4 border-{{ $cfg['color'] }}">
                            <div class="card-body py-3">
                                <h6 class="fw-bold text-{{ $cfg['color'] }}">
                                    <i class="fas {{ $cfg['icon'] }} me-2"></i>{{ $cfg['label'] }}
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">Tanggal Mulai</label>
                                        <input type="date" name="{{ $cfg['start_field'] }}" id="edit_{{ $cfg['start_field'] }}" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Tanggal Selesai</label>
                                        <input type="date" name="{{ $cfg['end_field'] }}" id="edit_{{ $cfg['end_field'] }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info text-white" id="btnEditJadwal">
                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                   || document.querySelector('input[name="_token"]')?.value;

    function showAlert(type, message) {
        const container = document.querySelector('.container-fluid');
        const existing = container.querySelector('.alert-dynamic');
        if (existing) existing.remove();
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show alert-dynamic`;
        alert.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        container.insertBefore(alert, container.children[1]);
        setTimeout(() => alert.remove(), 5000);
    }

    function validateDateRanges(formEl) {
        const phasePairs = [
            ['tanggal_pendaftaran_mulai', 'tanggal_pendaftaran_selesai', 'Fase 1'],
            ['tanggal_review_mulai', 'tanggal_review_selesai', 'Fase 2'],
            ['tanggal_perbaikan_mulai', 'tanggal_perbaikan_selesai', 'Fase 3'],
            ['tanggal_penilaian_akhir_mulai', 'tanggal_penilaian_akhir_selesai', 'Fase 4'],
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
    document.getElementById('formUpdateRuangKontrol')?.addEventListener('submit', function(e) {
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
                showAlert('danger', data.message || 'Gagal menyimpan.');
            }
        })
        .catch(() => showAlert('danger', 'Terjadi kesalahan jaringan.'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-2"></i>Simpan Pengaturan';
        });
    });

    // Create jadwal
    document.getElementById('formBuatJadwal')?.addEventListener('submit', function(e) {
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
        btn.addEventListener('click', function() {
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
    document.getElementById('formEditJadwal')?.addEventListener('submit', function(e) {
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
        btn.addEventListener('click', function() {
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
        btn.addEventListener('click', function() {
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
