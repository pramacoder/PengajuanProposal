@extends('mainlayout.app')

@section('title', 'Detail Hasil Review - Proposal')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard Dosen', 'url' => route('dosen.pendamping.dashboard')],
        ['label' => 'Hasil Review', 'url' => route('dosen.hasil.review')],
        ['label' => 'Detail Evaluasi', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Detail Hasil Evaluasi Reviewer" 
        subtitle="Rincian penilaian administratif, substantif, dan catatan penyempurnaan usulan proposal" 
        badge="Dosen Pendamping"
        icon="fa-clipboard-list"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dosen.hasil.review') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="row g-4 mb-4">
        <!-- Informasi Proposal -->
        <div class="col-lg-7">
            <div class="ui-card h-100 shadow-sm">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-alt text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Metadata Proposal Usulan</h6>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">{{ $proposal->skim }}</span>
                </div>
                <div class="ui-card-body p-4">
                    <div class="mb-3">
                        <small class="text-secondary fw-semibold d-block">Judul Usulan</small>
                        <h6 class="fw-bold text-dark mb-0">{{ $proposal->judul_proposal }}</h6>
                    </div>

                    <div class="row g-3 pt-3 border-top">
                        <div class="col-sm-6">
                            <small class="text-secondary fw-semibold d-block">Ketua Mahasiswa</small>
                            <span class="fw-semibold text-dark small">{{ $proposal->mahasiswa->nama_mahasiswa ?? $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</span>
                            <small class="text-muted d-block">NIM: {{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-secondary fw-semibold d-block">Program Studi & Fakultas</small>
                            <span class="text-dark small">{{ $proposal->mahasiswa->prodi->nama_prodi ?? 'N/A' }}</span>
                            <small class="text-muted d-block">{{ $proposal->mahasiswa->fakultas->nama_fakultas ?? 'N/A' }}</small>
                        </div>
                    </div>

                    <div class="pt-3 border-top mt-3">
                        <small class="text-secondary fw-semibold d-block mb-1">Status Validasi Pendamping</small>
                        <x-status-badge :status="$proposal->status_validasi" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Berkas Dokumen -->
        <div class="col-lg-5">
            <div class="ui-card h-100 shadow-sm">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-folder text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Berkas Dokumen Usulan</h6>
                    </div>
                </div>
                <div class="ui-card-body p-4">
                    <div class="d-flex flex-column gap-2">
                        @if($proposal->dokumen && $proposal->dokumen->file_proposal)
                            <a href="{{ route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
                               class="btn btn-outline-primary rounded-3 py-2 px-3 d-flex align-items-center justify-content-between fw-semibold" target="_blank">
                                <span class="d-flex align-items-center gap-2">
                                    <i class="fas fa-file-pdf text-danger fs-5"></i>
                                    <span>Unduh Naskah Proposal</span>
                                </span>
                                <i class="fas fa-download"></i>
                            </a>
                        @endif
                        @if($proposal->dokumen && $proposal->dokumen->file_lampiran)
                            <a href="{{ route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'lampiran']) }}" 
                               class="btn btn-outline-secondary rounded-3 py-2 px-3 d-flex align-items-center justify-content-between fw-semibold" target="_blank">
                                <span class="d-flex align-items-center gap-2">
                                    <i class="fas fa-paperclip text-secondary fs-5"></i>
                                    <span>Unduh Berkas Lampiran</span>
                                </span>
                                <i class="fas fa-download"></i>
                            </a>
                        @endif
                        @if(!$proposal->dokumen || (!$proposal->dokumen->file_proposal && !$proposal->dokumen->file_lampiran))
                            <div class="empty-state-box py-4">
                                <i class="fas fa-file-alt text-muted mb-2 fs-1"></i>
                                <p class="text-secondary small mb-0">Tidak ada berkas yang diunggah.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hasil Review Administratif -->
    <div class="ui-card shadow-sm mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-clipboard-check text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Hasil Review Administratif</h6>
            </div>
        </div>
        <div class="ui-card-body p-4">
            @if($proposal->nilaiAdministratif && $proposal->nilaiAdministratif->count() > 0)
                @php $adminRecord = $proposal->nilaiAdministratif->first(); @endphp
                <div class="p-3 rounded-3 bg-light border mb-3">
                    <strong class="small text-dark d-block mb-1"><i class="fas fa-comment text-primary me-1"></i>Catatan Reviewer:</strong>
                    <div class="small text-secondary">{{ $adminRecord->note_administratif ?? 'Tidak ada catatan khusus.' }}</div>
                </div>

                @if($adminRecord->checklist)
                    <div class="mb-3">
                        <strong class="small text-danger d-block mb-2">Kesalahan / Format yang Perlu Diperbaiki:</strong>
                        <div class="row g-2">
                            @foreach($adminRecord->checklist as $item)
                                <div class="col-md-6">
                                    <div class="p-2 rounded bg-danger-subtle border border-danger-subtle d-flex align-items-center gap-2 small">
                                        <i class="fas fa-times-circle text-danger"></i>
                                        <span class="text-dark">{{ $item }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="p-3 rounded-3 bg-success-subtle text-success border border-success-subtle d-flex align-items-center gap-2 small mb-3">
                        <i class="fas fa-check-circle fa-lg"></i>
                        <span>Semua kriteria format dan kelengkapan administratif telah memenuhi standar.</span>
                    </div>
                @endif
            @else
                <div class="empty-state-box py-4">
                    <i class="fas fa-clipboard-list text-muted mb-2 fs-1"></i>
                    <p class="text-secondary small mb-0">Belum ada hasil review administratif untuk proposal ini.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Hasil Review Substantif -->
    <div class="ui-card shadow-sm mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-user-check text-info"></i>
                <h6 class="mb-0 fw-bold fs-6">Hasil Review Substantif</h6>
            </div>
        </div>
        <div class="ui-card-body p-4">
            @if($proposal->nilaiSubstantif && $proposal->nilaiSubstantif->count() > 0)
                <div class="row g-3">
                    @foreach($proposal->nilaiSubstantif as $index => $review)
                        <div class="col-md-6">
                            <div class="ui-card border shadow-none h-100 mb-0">
                                <div class="ui-card-header d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-dark small"><i class="fas fa-user text-info me-2"></i>Reviewer {{ $index + 1 }}</span>
                                    <small class="text-muted">{{ $review->reviewer ? ($review->reviewer->nama_reviewer ?? $review->reviewer->name) : 'Reviewer' }}</small>
                                </div>
                                <div class="ui-card-body p-3">
                                    <div class="p-3 rounded-3 bg-light border">
                                        <strong class="small text-dark d-block mb-1">Catatan Evaluasi Substantif:</strong>
                                        <div class="small text-secondary">{{ $review->note_substantif ?? $review->catatan ?? 'Tidak ada catatan khusus.' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state-box py-4">
                    <i class="fas fa-user-check text-muted mb-2 fs-1"></i>
                    <p class="text-secondary small mb-0">Belum ada evaluasi substantif untuk proposal ini.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Status Final -->
    <div class="ui-card shadow-sm mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-award text-warning"></i>
                <h6 class="mb-0 fw-bold fs-6">Status Keputusan Final</h6>
            </div>
        </div>
        <div class="ui-card-body p-4">
            @if($proposal->status_final)
                <div class="p-4 rounded-3 {{ $proposal->status_final === 'lolos' ? 'bg-success-subtle border border-success-subtle' : 'bg-danger-subtle border border-danger-subtle' }} d-flex align-items-center gap-3">
                    <i class="fas {{ $proposal->status_final === 'lolos' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} fa-2x"></i>
                    <div>
                        <h6 class="fw-bold {{ $proposal->status_final === 'lolos' ? 'text-success' : 'text-danger' }} mb-1">
                            Status Final: {{ strtoupper($proposal->status_final) }}
                        </h6>
                        <small class="text-secondary">
                            {{ $proposal->status_final === 'lolos' ? 'Proposal dinyatakan lolos seleksi dan berhak melanjutkan ke tahapan pendanaan/nasional.' : 'Proposal belum berhasil lolos dan memerlukan penyempurnaan untuk periode pengajuan berikutnya.' }}
                        </small>
                    </div>
                </div>
            @else
                <div class="p-3 rounded-3 bg-light border d-flex align-items-center gap-3">
                    <i class="fas fa-clock text-warning fa-lg"></i>
                    <div class="small text-secondary">
                        <strong>Dalam Proses:</strong> Keputusan kelolosan akhir akan ditentukan oleh Operator / Pimpinan PT setelah seluruh proses penilaian selesai.
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
