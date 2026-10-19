@extends('mainlayout.app')

@section('title', 'Detail Hasil Semi Final - Operator')

@section('styles')
<style>
    .pdf-viewer-container {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    
    .pdf-header {
        background: #f8fafc;
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .pdf-title {
        font-weight: 600;
        font-size: 0.95rem;
        color: #1e293b;
        margin: 0;
    }
    
    .pdf-controls {
        display: flex;
        gap: 0.5rem;
    }
    
    .pdf-viewer {
        width: 100%;
        height: 650px;
        border: none;
    }
    
    .pdf-iframe {
        width: 100%;
        height: 650px;
        border: none;
        background: white;
    }
    
    .pdf-loading {
        position: relative;
        min-height: 650px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e2e8f0;
        border-top: 4px solid var(--primary-700, #8B0000);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        transition: opacity 0.3s ease;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #64748b;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #cbd5e1;
    }

    .review-score-hero {
        background: linear-gradient(135deg, rgba(248, 250, 252, 0.8) 0%, rgba(241, 245, 249, 0.9) 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.25rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('operator.dashboard')],
        ['label' => 'Hasil Semi Final', 'url' => route('operator.hasil.semi.final')],
        ['label' => 'Detail Proposal', 'active' => true],
    ]" />

    <!-- Header Section -->
    <x-page-header 
        title="Detail Hasil Semi Final" 
        subtitle="Evaluasi substantif seleksi dan penetapan status kelulusan tingkat universitas" 
        badge="Operator Workspace"
        icon="fa-award"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('operator.hasil.semi.final') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Hasil Semi Final</span>
                </a>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Proposal Information -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="ui-card h-100">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-alt text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Informasi Proposal</h6>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small">
                        Skim: {{ $proposal->skim }}
                    </span>
                </div>
                <div class="ui-card-body p-4">
                    <h5 class="fw-bold text-dark mb-3" style="line-height: 1.4;">
                        {{ $proposal->judul_proposal }}
                    </h5>
                    <div class="row g-3 pt-2 border-top">
                        <div class="col-sm-6 col-md-3">
                            <label class="small text-secondary fw-semibold d-block mb-1">Skim Program</label>
                            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill fw-semibold">{{ $proposal->skim }}</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="small text-secondary fw-semibold d-block mb-1">Dana Belmawa</label>
                            <span class="text-primary fw-bold">@rupiahId($proposal->dana_diajukan_belmawa ?? 0)</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="small text-secondary fw-semibold d-block mb-1">Dana Universitas</label>
                            <span class="text-success fw-bold">@rupiahId($proposal->dana_diajukan_operator ?? 0)</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="small text-secondary fw-semibold d-block mb-1">Tahun Ajaran</label>
                            <span class="fw-semibold text-dark">{{ $proposal->tahun_ajaran ?? 'N/A' }}</span>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="small text-secondary fw-semibold d-block mb-1">Status Verifikasi</label>
                            <x-status-badge :status="$proposal->status" :label="ucfirst($proposal->status)" />
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <label class="small text-secondary fw-semibold d-block mb-1">Tanggal Pengajuan</label>
                            <span class="text-secondary small"><i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tim & Pembimbing -->
        <div class="col-lg-4">
            <div class="ui-card h-100">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-users text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Ketua & Pembimbing</h6>
                    </div>
                </div>
                <div class="ui-card-body p-4">
                    <div class="mb-3 pb-3 border-bottom">
                        <span class="badge bg-primary-subtle text-primary mb-1 small fw-bold">Ketua Tim</span>
                        <div class="fw-bold text-dark">{{ $proposal->ketua_nama }}</div>
                        <div class="text-muted small">NIM: {{ $proposal->ketua_nim }} | {{ $proposal->ketua_prodi }}</div>
                        <div class="text-muted small">{{ $proposal->ketua_fakultas }}</div>
                        <div class="text-muted small mt-1">
                            <i class="fas fa-envelope me-1"></i>{{ $proposal->ketua_email }} 
                            @if($proposal->ketua_no_hp)| <i class="fas fa-phone me-1"></i>{{ $proposal->ketua_no_hp }}@endif
                        </div>
                    </div>
                    <div>
                        <span class="badge bg-secondary-subtle text-secondary mb-1 small fw-bold">Dosen Pendamping</span>
                        <div class="fw-bold text-dark">{{ $proposal->dosen_pembimbing ?? '-' }}</div>
                        @if($proposal->dosen)
                            <div class="text-muted small">{{ $proposal->dosen->email_dosen }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Proposal Viewer Section -->
    @php
        $latestRevisi = $proposal->proposalRevisi->where('jenis_revisi', 'revisi_biasa')->first();
        $hasRevisi = $latestRevisi !== null;
        $displayDocument = $hasRevisi ? $latestRevisi : ($proposal->dokumen ?? null);
    @endphp
    
    @if($displayDocument)
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-file-pdf text-danger"></i>
                <h6 class="mb-0 fw-bold fs-6">
                    @if($hasRevisi)
                        Dokumen Berkas Revisi Terakhir
                    @else
                        Dokumen Berkas Proposal
                    @endif
                </h6>
            </div>
            @if($hasRevisi)
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill small">
                    <i class="fas fa-history me-1"></i>Revisi
                </span>
            @endif
        </div>
        <div class="ui-card-body p-0">
            <div class="pdf-viewer-container border-0 rounded-0 mb-0">
                <div class="pdf-header">
                    <h5 class="pdf-title">
                        <i class="fas fa-file-pdf text-danger me-2"></i>
                        <span id="pdfProposalTitle">
                            @if($hasRevisi)
                                {{ $latestRevisi->nama_file }}
                            @else
                                {{ $proposal->dokumen->nama_file ?? 'Proposal PDF' }}
                            @endif
                        </span>
                        @if($hasRevisi)
                            <span class="badge bg-light text-secondary border ms-2 fw-normal small">
                                <i class="fas fa-clock me-1"></i>
                                Diupload: {{ \Carbon\Carbon::parse($latestRevisi->tanggal_submit)->format('d M Y H:i') }}
                            </span>
                        @endif
                    </h5>
                    <div class="pdf-controls">
                        <button id="fullscreenProposalBtn" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="fas fa-expand me-1"></i>Fullscreen
                        </button>
                        @if($hasRevisi)
                            <a href="{{ route('operator.revisi.download', $latestRevisi->id_revisi) }}" 
                               class="btn btn-outline-primary btn-sm rounded-pill px-3" 
                               target="_blank">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                        @else
                            <a href="{{ route('operator.proposal.view.pdf', $proposal->id_proposal) }}" 
                               class="btn btn-outline-primary btn-sm rounded-pill px-3" 
                               target="_blank">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                        @endif
                    </div>
                </div>
                <div id="pdfProposalViewer" class="pdf-loading">
                    <div class="spinner"></div>
                    <iframe id="proposalPdfIframe" 
                            src="{{ $hasRevisi ? route('operator.revisi.view', $latestRevisi->id_revisi) : route('operator.proposal.view.pdf', $proposal->id_proposal) }}" 
                            class="pdf-iframe" 
                            style="display: none;">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-warning d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm">
        <i class="fas fa-exclamation-triangle fs-5"></i>
        <div><strong>Dokumen proposal tidak tersedia atau belum diupload.</strong></div>
    </div>
    @endif

    <!-- File Revisi Section (Jika Ada Riwayat Lainnya) -->
    @if($proposal->proposalRevisi->count() > 0)
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-layer-group text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Riwayat File Revisi yang Dikumpulkan</h6>
            </div>
            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                {{ $proposal->proposalRevisi->count() }} Berkas
            </span>
        </div>
        <div class="ui-card-body p-3">
            <div class="list-group list-group-flush">
                @foreach($proposal->proposalRevisi as $revisi)
                <div class="list-group-item d-flex justify-content-between align-items-center py-3 border-bottom px-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2.5 rounded-3 bg-danger-subtle text-danger">
                            <i class="fas fa-file-pdf fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $revisi->nama_file }}</div>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>Diupload: {{ \Carbon\Carbon::parse($revisi->tanggal_submit)->format('d M Y H:i') }}
                            </small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-info rounded-pill px-3 view-pdf-btn" 
                                data-pdf-url="{{ route('operator.revisi.download', $revisi->id_revisi) }}"
                                data-pdf-name="{{ $revisi->nama_file }}">
                            <i class="fas fa-eye me-1"></i>Lihat
                        </button>
                        <a href="{{ route('operator.revisi.download', $revisi->id_revisi) }}" 
                           class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                           target="_blank">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- PDF Viewer Section untuk Revisi Popup/Inline -->
    <div class="pdf-viewer-container" id="revisiPdfViewer" style="display: none;">
        <div class="pdf-header">
            <h5 class="pdf-title">
                <i class="fas fa-file-pdf text-danger me-2"></i>
                <span id="pdfViewerTitle">Pilih file untuk dilihat</span>
            </h5>
            <div class="pdf-controls">
                <button id="fullscreenBtn" class="btn btn-outline-secondary btn-sm rounded-pill px-3" style="display: none;">
                    <i class="fas fa-expand me-1"></i>Fullscreen
                </button>
                <button id="downloadBtn" class="btn btn-outline-primary btn-sm rounded-pill px-3" style="display: none;">
                    <i class="fas fa-download me-1"></i>Download
                </button>
            </div>
        </div>
        <div id="pdfViewer" class="pdf-loading">
            <div class="spinner"></div>
        </div>
    </div>
    @endif

    <!-- Penilaian Substantif Seleksi dari 2 Reviewer -->
    @if($nilaiSubstantifSeleksi1 || $nilaiSubstantifSeleksi2)
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-award text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Referensi Penilaian Substantif Seleksi dari Reviewer</h6>
            </div>
            <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill small">
                Tahap Seleksi
            </span>
        </div>
        <div class="ui-card-body p-4">
            <div class="row g-4">
                @if($nilaiSubstantifSeleksi1)
                <div class="col-md-6">
                    <div class="review-score-hero h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-primary mb-0">
                                <i class="fas fa-user-check me-2"></i>Reviewer Seleksi 1
                            </h6>
                            @if($nilaiSubstantifSeleksi1->reviewer)
                                <span class="badge bg-light text-secondary border small">{{ $nilaiSubstantifSeleksi1->reviewer->nama_reviewer }}</span>
                            @endif
                        </div>
                        <div class="row align-items-center g-3">
                            <div class="col-auto text-center border-end pe-4">
                                <div class="display-6 fw-bold text-primary">@formatId($nilaiSubstantifSeleksi1->nilai_akhir ?? ($nilaiSubstantifSeleksi1->total_nilai / 7), 2)</div>
                                <div class="text-secondary small fw-semibold">Nilai Akhir (Skala 100)</div>
                                <span class="badge bg-white text-secondary border mt-1">Total: @formatId($nilaiSubstantifSeleksi1->total_nilai, 2)</span>
                            </div>
                            <div class="col">
                                <label class="small text-secondary fw-semibold d-block mb-1"><i class="fas fa-comment-alt me-1"></i>Catatan Reviewer:</label>
                                <div class="p-3 bg-white rounded-3 border small text-dark" style="min-height: 70px;">
                                    {{ $nilaiSubstantifSeleksi1->note_substantif ?? $nilaiSubstantifSeleksi1->catatan ?? 'Tidak ada catatan khusus.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if($nilaiSubstantifSeleksi2)
                <div class="col-md-6">
                    <div class="review-score-hero h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-success mb-0">
                                <i class="fas fa-user-check me-2"></i>Reviewer Seleksi 2
                            </h6>
                            @if($nilaiSubstantifSeleksi2->reviewer)
                                <span class="badge bg-light text-secondary border small">{{ $nilaiSubstantifSeleksi2->reviewer->nama_reviewer }}</span>
                            @endif
                        </div>
                        <div class="row align-items-center g-3">
                            <div class="col-auto text-center border-end pe-4">
                                <div class="display-6 fw-bold text-success">@formatId($nilaiSubstantifSeleksi2->nilai_akhir ?? ($nilaiSubstantifSeleksi2->total_nilai / 7), 2)</div>
                                <div class="text-secondary small fw-semibold">Nilai Akhir (Skala 100)</div>
                                <span class="badge bg-white text-secondary border mt-1">Total: @formatId($nilaiSubstantifSeleksi2->total_nilai, 2)</span>
                            </div>
                            <div class="col">
                                <label class="small text-secondary fw-semibold d-block mb-1"><i class="fas fa-comment-alt me-1"></i>Catatan Reviewer:</label>
                                <div class="p-3 bg-white rounded-3 border small text-dark" style="min-height: 70px;">
                                    {{ $nilaiSubstantifSeleksi2->note_substantif ?? $nilaiSubstantifSeleksi2->catatan ?? 'Tidak ada catatan khusus.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Formulir Penilaian Hasil Semi Final -->
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-gavel text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Penetapan Nilai & Hasil Semi Final</h6>
            </div>
            @if($proposal->hasilSemiFinal)
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small">
                    <i class="fas fa-check-circle me-1"></i>Hasil Sudah Ditentukan
                </span>
            @endif
        </div>
        <div class="ui-card-body p-4">
            @if($proposal->hasilSemiFinal)
                <!-- Existing Result Banner -->
                <div class="alert {{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'alert-success border-success-subtle' : 'alert-danger border-danger-subtle' }} p-4 rounded-3 mb-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="fas {{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} fs-4"></i>
                                <h5 class="mb-0 fw-bold">
                                    {{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'Lolos Tingkat Universitas' : 'Tidak Lolos Tingkat Universitas' }}
                                </h5>
                            </div>
                            <div class="d-flex flex-wrap gap-4 mt-3">
                                <div>
                                    <div class="small text-secondary fw-semibold">Nilai Semi Final</div>
                                    <div class="fs-4 fw-bold text-primary">@formatId($proposal->hasilSemiFinal->nilai, 2)</div>
                                </div>
                                @if($proposal->dosenPendampingUniversitas)
                                <div>
                                    <div class="small text-secondary fw-semibold">Dosen Pendamping Universitas</div>
                                    <div class="fw-bold text-dark">{{ $proposal->dosenPendampingUniversitas->nama_dosen }}</div>
                                    <small class="text-muted">{{ $proposal->dosenPendampingUniversitas->email_dosen }}</small>
                                </div>
                                @endif
                                <div>
                                    <div class="small text-secondary fw-semibold">Waktu Penetapan</div>
                                    <div class="text-dark">{{ \Carbon\Carbon::parse($proposal->hasilSemiFinal->created_at)->format('d M Y H:i') }}</div>
                                </div>
                            </div>
                            @if($proposal->hasilSemiFinal->catatan_final)
                            <div class="mt-3 pt-3 border-top">
                                <div class="small text-secondary fw-semibold mb-1">Catatan Keputusan:</div>
                                <div class="text-dark bg-white p-2.5 rounded-2 border small">{{ $proposal->hasilSemiFinal->catatan_final }}</div>
                            </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-warning rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center gap-2" onclick="toggleEditForm()">
                            <i class="fas fa-edit"></i>
                            <span>Edit Hasil Semi Final</span>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Form for input/update -->
            <form id="hasilSemiFinalForm" action="{{ route('operator.update.hasil.semi.final') }}" method="POST" 
                  @if($proposal->hasilSemiFinal) style="display: none;" @endif>
                @csrf
                <input type="hidden" name="proposal_id" value="{{ $proposal->id_proposal }}">
                <input type="hidden" name="skim" value="{{ $proposal->skim }}">
                
                @php
                    $criteriaItems = $criteria ?? [];
                    $existingHasilSemiFinal = $proposal->hasilSemiFinal;
                    $existingSkorFinalRaw = $existingHasilSemiFinal ? ($existingHasilSemiFinal->skor_per_kriteria ?? []) : [];
                    
                    $existingSkorFinal = [];
                    if (!empty($existingSkorFinalRaw) && is_array($existingSkorFinalRaw)) {
                        foreach ($existingSkorFinalRaw as $key => $value) {
                            $index = (int) $key;
                            $existingSkorFinal[$index] = (float) $value;
                        }
                        ksort($existingSkorFinal);
                    }
                    
                    if (empty($criteriaItems)) {
                        $criteriaItems = \App\Helpers\ProposalHelper::getSubstantifCriteria('default');
                    }
                    
                    $flattenedCriteria = [];
                    $criteriaIndex = 0;
                    $mainCriteriaNumber = 0;
                    
                    foreach($criteriaItems as $mainItem) {
                        if (isset($mainItem['sub_kriteria']) && !empty($mainItem['sub_kriteria'])) {
                            $mainCriteriaNumber++;
                            $flattenedCriteria[] = [
                                'main_number' => $mainCriteriaNumber,
                                'is_sub' => false,
                                'is_header' => true,
                                'kriteria' => $mainItem['kriteria'],
                                'bobot' => array_sum(array_column($mainItem['sub_kriteria'], 'bobot')),
                                'index' => -1,
                                'has_sub' => true
                            ];
                            foreach($mainItem['sub_kriteria'] as $subItem) {
                                $flattenedCriteria[] = [
                                    'main_number' => $mainCriteriaNumber,
                                    'is_sub' => true,
                                    'is_header' => false,
                                    'kriteria' => $subItem['kriteria'],
                                    'bobot' => $subItem['bobot'],
                                    'index' => $criteriaIndex++,
                                    'has_sub' => false
                                ];
                            }
                        } else {
                            $mainCriteriaNumber++;
                            $flattenedCriteria[] = [
                                'main_number' => $mainCriteriaNumber,
                                'is_sub' => false,
                                'is_header' => false,
                                'kriteria' => $mainItem['kriteria'],
                                'bobot' => $mainItem['bobot'],
                                'index' => $criteriaIndex++,
                                'has_sub' => false
                            ];
                        }
                    }
                @endphp

                <!-- Form Penilaian Rubrik -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <label class="form-label fw-bold mb-0 fs-6">
                            <i class="fas fa-list-check text-primary me-2"></i>Rubrik Penilaian Substantif (Standar Belmawa) <span class="text-danger">*</span>
                        </label>
                        <span class="badge bg-light text-secondary border small">Skala 1, 2, 3, 5, 6, 7</span>
                    </div>
                    
                    @if(empty($criteriaItems))
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Kriteria penilaian untuk skim {{ $proposal->skim }} belum tersedia.
                        </div>
                    @else
                        <div class="table-responsive rounded-3 border">
                            <table class="table table-modern table-bordered align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th width="45%">Kriteria Penilaian</th>
                                        <th width="12%" class="text-center">Bobot (%)</th>
                                        <th width="23%" class="text-center">Skor (1–7)</th>
                                        <th width="15%" class="text-center">Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $belmawaOptions = [
                                            1 => '1 - Buruk',
                                            2 => '2 - Sangat Kurang',
                                            3 => '3 - Kurang',
                                            5 => '5 - Cukup',
                                            6 => '6 - Baik',
                                            7 => '7 - Sangat Baik',
                                        ];
                                    @endphp
                                    @foreach($flattenedCriteria as $item)
                                        @php
                                            if ($item['index'] >= 0) {
                                                $skorValue = isset($existingSkorFinal[$item['index']]) ? $existingSkorFinal[$item['index']] : '';
                                                $nilai = $skorValue ? ($item['bobot'] * $skorValue) : 0;
                                            } else {
                                                $skorValue = '';
                                                $nilai = 0;
                                            }
                                            $showNumber = !$item['is_sub'];
                                        @endphp
                                        <tr class="{{ $item['is_header'] ? 'bg-light fw-bold' : '' }}">
                                            <td class="text-center text-secondary">
                                                @if($showNumber)
                                                    {{ $item['main_number'] }}
                                                @elseif($item['is_sub'])
                                                    <span class="text-muted small">└─</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['is_sub'])
                                                    <span class="ps-3 text-secondary small">{{ $item['kriteria'] }}</span>
                                                @else
                                                    <span class="fw-semibold text-dark">{{ $item['kriteria'] }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center fw-semibold text-secondary">
                                                @if($item['is_header'])
                                                    <span class="text-muted">-</span>
                                                @else
                                                    @formatId($item['bobot'], 2)
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['is_header'])
                                                    <span class="text-muted">-</span>
                                                @else
                                                    <select class="form-select form-select-sm skor-final-input rounded-2" 
                                                            name="skor[{{ $item['index'] }}]" 
                                                            data-index="{{ $item['index'] }}"
                                                            data-bobot="{{ $item['bobot'] }}"
                                                            required>
                                                        <option value="">-- Pilih Skor --</option>
                                                        @foreach($belmawaOptions as $bVal => $bLabel)
                                                            <option value="{{ $bVal }}" {{ (string)(int)$skorValue === (string)$bVal && $skorValue !== '' ? 'selected' : '' }}>
                                                                {{ $bLabel }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($item['is_header'])
                                                    <span class="text-muted">-</span>
                                                @else
                                                    <span class="nilai-final-display fw-bold text-dark" data-index="{{ $item['index'] }}">
                                                        @if($nilai > 0)@formatId($nilai, 2)@else 0,00 @endif
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="2" class="text-end fw-bold">Total Bobot & Nilai</td>
                                        <td class="text-center fw-bold">100.00</td>
                                        <td class="text-center">
                                            <small class="text-muted me-1">Rata Skor:</small>
                                            <span class="total-skor-final-display fw-bold text-dark">0.00</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="total-nilai-final-display fw-bold text-dark">0.00</span>
                                        </td>
                                    </tr>
                                    <tr class="bg-primary-subtle">
                                        <td colspan="4" class="text-end fw-bold text-primary">Nilai Akhir (Total Nilai / 7) — Skala 100</td>
                                        <td class="text-center">
                                            <span class="nilai-akhir-final-display fw-bold text-primary fs-5">0.00</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
                
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status Semi Final <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3" name="status_final" id="statusSemiFinalSelect" required>
                                <option value="">Pilih status semi final</option>
                                <option value="lolos_tingkat_universitas" {{ $proposal->hasilSemiFinal && $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'selected' : '' }}>Lolos Tingkat Universitas</option>
                                <option value="tidak_lolos_tingkat_universitas" {{ $proposal->hasilSemiFinal && $proposal->hasilSemiFinal->status_final == 'tidak_lolos_tingkat_universitas' ? 'selected' : '' }}>Tidak Lolos Tingkat Universitas</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nilai Semi Final (Terhitung Otomatis)</label>
                            <input type="number" class="form-control rounded-3 bg-light" name="nilai" id="nilaiSemiFinalInput"
                                   value="{{ $proposal->hasilSemiFinal ? $proposal->hasilSemiFinal->nilai : '' }}"
                                   min="0" max="100" step="0.01" required
                                   placeholder="Akan terisi otomatis"
                                   readonly>
                            <div class="form-text small">Dihitung otomatis dari rubrik di atas (Total Nilai / 7)</div>
                        </div>
                    </div>
                    <div class="col-md-4" id="dosenUniversitasField" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Dosen Pendamping Universitas <span class="text-danger">*</span></label>
                            <select class="form-select rounded-3" name="id_dosen_pendamping_universitas" id="dosenUniversitasSelect">
                                <option value="">Pilih Dosen Pendamping Universitas</option>
                                @foreach($dosens as $dosen)
                                    <option value="{{ $dosen->id_dosen }}" 
                                            {{ $proposal->id_dosen_pendamping_universitas == $dosen->id_dosen ? 'selected' : '' }}>
                                        {{ $dosen->nama_dosen }} ({{ $dosen->email_dosen }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small text-danger">Wajib dipilih jika status "Lolos Tingkat Universitas"</div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Catatan Semi Final</label>
                    <textarea class="form-control rounded-3" name="catatan_final" rows="3" 
                              placeholder="Berikan catatan perbaikan atau rekomendasi untuk mahasiswa...">{{ $proposal->hasilSemiFinal ? $proposal->hasilSemiFinal->catatan_final : '' }}</textarea>
                </div>
                
                <div class="d-flex align-items-center gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm">
                        <i class="fas fa-save"></i>
                        <span>{{ $proposal->hasilSemiFinal ? 'Update Hasil Semi Final' : 'Simpan Hasil Semi Final' }}</span>
                    </button>
                    @if($proposal->hasilSemiFinal)
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold" onclick="toggleEditForm()">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@php
    $hasRevisi = $proposal->proposalRevisi->count() > 0;
    $firstRevisi = $hasRevisi ? $proposal->proposalRevisi->first() : null;
    $firstRevisiUrl = $firstRevisi ? route('operator.revisi.download', $firstRevisi->id_revisi) : null;
    
    $pageDataJson = json_encode([
        'hasRevisi' => $hasRevisi,
        'firstRevisi' => $firstRevisi,
        'firstRevisiUrl' => $firstRevisiUrl
    ]);
@endphp
<script>
    let currentPdfUrl = null;
    let currentFileName = null;
    const pageData = JSON.parse('{!! addslashes($pageDataJson) !!}');

    function toggleEditForm() {
        const form = document.getElementById('hasilSemiFinalForm');
        const editBtn = document.querySelector('button[onclick="toggleEditForm()"]');
        
        if (form.style.display === 'none') {
            form.style.display = 'block';
            if (editBtn) editBtn.innerHTML = '<i class="fas fa-times me-2"></i>Batal';
        } else {
            form.style.display = 'none';
            if (editBtn) editBtn.innerHTML = '<i class="fas fa-edit me-2"></i>Edit Hasil Semi Final';
        }
    }
    
    function toggleDosenUniversitasField() {
        const statusSelect = document.getElementById('statusSemiFinalSelect');
        const dosenField = document.getElementById('dosenUniversitasField');
        const dosenSelect = document.getElementById('dosenUniversitasSelect');
        
        if (statusSelect && dosenField && dosenSelect) {
            if (statusSelect.value === 'lolos_tingkat_universitas') {
                dosenField.style.display = 'block';
                dosenSelect.setAttribute('required', 'required');
            } else {
                dosenField.style.display = 'none';
                dosenSelect.removeAttribute('required');
                dosenSelect.value = '';
            }
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        toggleDosenUniversitasField();
        const statusSelect = document.getElementById('statusSemiFinalSelect');
        if (statusSelect) {
            statusSelect.addEventListener('change', toggleDosenUniversitasField);
        }
    });

    function calculateNilaiFinal() {
        const skorInputs = document.querySelectorAll('.skor-final-input');
        let totalNilai = 0;
        let totalSkor = 0;
        let count = 0;
        
        skorInputs.forEach(input => {
            const index = parseInt(input.dataset.index);
            if (index < 0) return;
            
            const skor = parseFloat(input.value) || 0;
            const bobot = parseFloat(input.dataset.bobot) || 0;
            
            const nilai = bobot * skor;
            totalNilai += nilai;
            totalSkor += skor;
            count++;
            
            const nilaiDisplay = document.querySelector(`.nilai-final-display[data-index="${index}"]`);
            if (nilaiDisplay) {
                nilaiDisplay.textContent = nilai.toFixed(2);
            }
        });
        
        const totalNilaiDisplay = document.querySelector('.total-nilai-final-display');
        if (totalNilaiDisplay) {
            totalNilaiDisplay.textContent = totalNilai.toFixed(2);
        }
        
        const totalSkorDisplay = document.querySelector('.total-skor-final-display');
        if (totalSkorDisplay) {
            totalSkorDisplay.textContent = count > 0 ? (totalSkor / count).toFixed(2) : '0.00';
        }
        
        const nilaiAkhir = totalNilai > 0 ? (totalNilai / 7) : 0;
        const nilaiAkhirDisplay = document.querySelector('.nilai-akhir-final-display');
        if (nilaiAkhirDisplay) {
            nilaiAkhirDisplay.textContent = nilaiAkhir.toFixed(2);
        }
        
        const nilaiSemiFinalInput = document.getElementById('nilaiSemiFinalInput');
        if (nilaiSemiFinalInput) {
            nilaiSemiFinalInput.value = nilaiAkhir.toFixed(2);
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const skorInputs = document.querySelectorAll('.skor-final-input');
        calculateNilaiFinal();
        skorInputs.forEach(input => {
            input.addEventListener('input', calculateNilaiFinal);
            input.addEventListener('change', calculateNilaiFinal);
        });
    });

    function viewPDF(pdfUrl, fileName) {
        currentPdfUrl = pdfUrl;
        currentFileName = fileName;
        
        const titleEl = document.getElementById('pdfViewerTitle');
        if (titleEl) titleEl.textContent = fileName;
        
        const viewer = document.getElementById('pdfViewer');
        if (viewer) viewer.innerHTML = '<div class="spinner"></div>';
        
        const fsBtn = document.getElementById('fullscreenBtn');
        const dlBtn = document.getElementById('downloadBtn');
        if (fsBtn) fsBtn.style.display = 'inline-block';
        if (dlBtn) dlBtn.style.display = 'inline-block';
        
        if (viewer) loadPDFDocument(pdfUrl, viewer);
    }

    function loadPDFDocument(pdfUrl, pdfViewer) {
        if (!pdfUrl) {
            pdfViewer.innerHTML = '<div class="empty-state"><i class="fas fa-file-pdf"></i><h4>Dokumen Tidak Tersedia</h4><p>Dokumen tidak ditemukan.</p></div>';
            return;
        }

        const iframe = document.createElement('iframe');
        iframe.src = pdfUrl;
        iframe.className = 'pdf-iframe';
        iframe.style.width = '100%';
        iframe.style.height = '650px';
        iframe.style.border = 'none';
        iframe.style.opacity = '0';
        iframe.style.transition = 'opacity 0.3s ease';
        
        iframe.onload = function() {
            setTimeout(() => {
                iframe.style.opacity = '1';
                const spinner = pdfViewer.querySelector('.spinner');
                if (spinner && spinner.parentNode) {
                    spinner.parentNode.removeChild(spinner);
                }
            }, 100);
        };

        iframe.onerror = function() {
            showDownloadOption(pdfViewer);
        };

        pdfViewer.appendChild(iframe);
        
        setTimeout(() => {
            const spinner = pdfViewer.querySelector('.spinner');
            if (spinner && iframe.style.opacity === '0') {
                showDownloadOption(pdfViewer);
            }
        }, 5000);
    }

    function showDownloadOption(pdfViewer) {
        pdfViewer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-file-pdf"></i>
                <h5 class="fw-bold text-dark">PDF Tidak Dapat Ditampilkan Langsung</h5>
                <p class="text-secondary small">Silakan unduh berkas untuk membaca isi dokumen secara lengkap.</p>
                <div class="mt-3">
                    <a href="${currentPdfUrl}" class="btn btn-primary rounded-pill px-4" download="${currentFileName}">
                        <i class="fas fa-download me-2"></i>Download PDF
                    </a>
                </div>
            </div>
        `;
    }

    function initializeFullscreen() {
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const pdfViewer = document.getElementById('pdfViewer');
        
        if (fullscreenBtn && pdfViewer) {
            fullscreenBtn.addEventListener('click', function() {
                const iframe = pdfViewer.querySelector('.pdf-iframe');
                const target = iframe || pdfViewer;
                if (target.requestFullscreen) target.requestFullscreen();
                else if (target.webkitRequestFullscreen) target.webkitRequestFullscreen();
                else if (target.msRequestFullscreen) target.msRequestFullscreen();
            });
        }
    }

    function initializeDownload() {
        const downloadBtn = document.getElementById('downloadBtn');
        if (downloadBtn) {
            downloadBtn.addEventListener('click', function() {
                if (currentPdfUrl) {
                    const link = document.createElement('a');
                    link.href = currentPdfUrl;
                    link.download = currentFileName || 'document.pdf';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            });
        }
    }

    function initializeProposalPdfViewer() {
        const proposalIframe = document.getElementById('proposalPdfIframe');
        if (proposalIframe) {
            proposalIframe.onload = function() {
                const spinner = document.querySelector('#pdfProposalViewer .spinner');
                if (spinner) {
                    spinner.style.opacity = '0';
                    setTimeout(() => {
                        if (spinner.parentNode) spinner.parentNode.removeChild(spinner);
                        proposalIframe.style.display = 'block';
                    }, 300);
                }
            };
            
            setTimeout(() => {
                proposalIframe.style.display = 'block';
            }, 500);
        }
        
        const fullscreenProposalBtn = document.getElementById('fullscreenProposalBtn');
        if (fullscreenProposalBtn && proposalIframe) {
            fullscreenProposalBtn.addEventListener('click', function() {
                if (proposalIframe.requestFullscreen) proposalIframe.requestFullscreen();
                else if (proposalIframe.webkitRequestFullscreen) proposalIframe.webkitRequestFullscreen();
                else if (proposalIframe.msRequestFullscreen) proposalIframe.msRequestFullscreen();
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        initializeProposalPdfViewer();
        
        document.querySelectorAll('.view-pdf-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const pdfUrl = this.getAttribute('data-pdf-url');
                const pdfName = this.getAttribute('data-pdf-name');
                viewPDF(pdfUrl, pdfName);
                
                const revisiViewer = document.getElementById('revisiPdfViewer');
                if (revisiViewer) {
                    revisiViewer.style.display = 'block';
                    revisiViewer.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
        
        initializeFullscreen();
        initializeDownload();
    });

    document.getElementById('hasilSemiFinalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        submitBtn.disabled = true;
        
        const formData = new FormData(this);
        const statusFinal = formData.get('status_final');
        if (statusFinal === 'tidak_lolos_tingkat_universitas') {
            formData.delete('id_dosen_pendamping_universitas');
        }
        
        if (statusFinal === 'lolos_tingkat_universitas') {
            const dosenId = formData.get('id_dosen_pendamping_universitas');
            if (!dosenId) {
                showToast('Dosen Pendamping Universitas wajib dipilih jika status Lolos Tingkat Universitas', 'error');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return;
            }
        }
        
        const skorInputs = document.querySelectorAll('.skor-final-input');
        let allSkorFilled = true;
        skorInputs.forEach(input => {
            const index = parseInt(input.dataset.index);
            if (index >= 0 && (!input.value || input.value === '')) {
                allSkorFilled = false;
            }
        });
        
        if (!allSkorFilled) {
            showToast('Semua skor rubrik penilaian harus diisi', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            return;
        }
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                if (response.status === 422 && data.errors) {
                    const errorMessages = [];
                    Object.keys(data.errors).forEach(key => {
                        errorMessages.push(...data.errors[key]);
                    });
                    showToast('Validasi gagal: ' + errorMessages.join(', '), 'error');
                } else {
                    showToast('Gagal memperbarui hasil semi final: ' + (data.message || 'Terjadi kesalahan'), 'error');
                }
                throw new Error(data.message || 'Validation failed');
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                showToast('Hasil semi final berhasil disimpan!', 'success');
                setTimeout(() => {
                    location.reload();
                }, 1200);
            } else {
                showToast('Gagal memperbarui hasil semi final: ' + (data.message || 'Terjadi kesalahan'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (!error.message.includes('Validation failed')) {
                showToast('Terjadi kesalahan saat menyimpan: ' + error.message, 'error');
            }
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    function showToast(message, type = 'info', duration = 3000) {
        if (window.AppUI?.showToast) {
            window.AppUI.showToast(message, type, duration);
        } else if (type === 'error') {
            alert(message);
        } else {
            console.log(`[Toast ${type}] ${message}`);
        }
    }
</script>
@endsection
