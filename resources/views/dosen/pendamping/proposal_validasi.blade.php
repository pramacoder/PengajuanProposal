@extends('mainlayout.app')

@section('title', 'Validasi Proposal')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard Dosen', 'url' => route('dosen.pendamping.dashboard')],
        ['label' => 'Validasi Proposal', 'active' => true],
    ]" />

    <!-- Page Header with Year Filter -->
    <x-page-header 
        title="Validasi Usulan Proposal" 
        subtitle="Verifikasi kelayakan usulan, kelengkapan berkas, dan pengesahan proposal mahasiswa pendampingan" 
        badge="Dosen Pendamping"
        icon="fa-clipboard-check"
    >
        <x-slot:actions>
            <form method="GET" action="{{ route('dosen.pendamping.proposal.validasi') }}" class="d-flex align-items-center gap-2">
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

    <div class="ui-card shadow-sm">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-list-check text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Daftar Usulan yang Memerlukan Validasi Pendamping</h6>
            </div>
            <span class="badge bg-secondary-subtle text-dark border">{{ $proposals->count() }} Usulan</span>
        </div>
        <div class="ui-card-body p-0">
            @if($proposals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-modern">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center py-3">No</th>
                                <th style="width: 25%;" class="py-3">Mahasiswa Pengusul</th>
                                <th style="width: 35%;" class="py-3">Judul Usulan Proposal</th>
                                <th style="width: 10%;" class="py-3">Skim</th>
                                <th style="width: 12%;" class="py-3">Status</th>
                                <th style="width: 13%;" class="text-center py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $index => $proposal)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark small">{{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                                        <small class="text-muted d-block">NIM: {{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                                        <small class="text-secondary d-block">{{ $proposal->mahasiswa->prodi->nama_prodi ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark small line-clamp-2" title="{{ $proposal->judul_proposal }}">
                                            {{ $proposal->judul_proposal }}
                                        </div>
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d F Y, H:i') }}
                                        </small>
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
                                        <a href="{{ route('dosen.pendamping.proposal.detail', $proposal->id_proposal) }}" 
                                           class="btn btn-sm {{ $proposal->status_validasi === 'pending' ? 'btn-warning text-dark' : 'btn-outline-primary' }} rounded-pill px-3 fw-semibold">
                                            <i class="fas {{ $proposal->status_validasi === 'pending' ? 'fa-check-double' : 'fa-eye' }} me-1"></i>
                                            {{ $proposal->status_validasi === 'pending' ? 'Validasi' : 'Detail' }}
                                        </a>
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
                    <h5 class="fw-bold text-dark mb-1">Tidak Ada Antrean Validasi</h5>
                    <p class="text-secondary small mb-0" style="max-width: 480px;">
                        Semua proposal mahasiswa pendampingan Anda telah divalidasi atau belum ada pengajuan baru pada tahun ajaran ini.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
