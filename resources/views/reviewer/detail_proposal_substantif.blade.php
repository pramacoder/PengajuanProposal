@extends('mainlayout.app')

@section('title', 'Detail Proposal - Review Substantif')

@section('styles')
<style>
    .pdf-viewer-section {
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

    .pdf-container-full {
        position: relative;
        background: #f8fafc;
        min-height: 70vh;
    }

    .pdf-viewer-section.fullscreen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999;
        margin: 0;
        border-radius: 0;
    }
    
    .pdf-viewer-section.fullscreen .pdf-container-full,
    .pdf-viewer-section.fullscreen #pdfViewer {
        height: calc(100vh - 70px) !important;
    }

    .substantif-table th {
        font-size: 0.825rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #475569;
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }

    .substantif-table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }

    .substantif-table .table-summary-row {
        background-color: #f8fafc;
        font-weight: 600;
    }

    .substantif-table .final-score-row {
        background-color: rgba(13, 110, 253, 0.04);
        border-top: 2px solid #cbd5e1;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('reviewer.dashboard')],
        ['label' => request('seleksi') ? 'Review Substantif Seleksi' : 'Review Substantif', 'url' => request('seleksi') ? route('reviewer.review.substantif.seleksi') : route('reviewer.review.substantif')],
        ['label' => 'Detail & Penilaian', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="{{ request('seleksi') ? 'Review Substantif Seleksi' : 'Review Substantif' }}" 
        subtitle="Evaluasi substansi usulan, kelayakan metodologi, luaran target, dan kepatuhan standar Belmawa" 
        badge="{{ request('seleksi') ? 'Tahap Seleksi' : 'Tahap Reguler' }}"
        icon="fa-clipboard-check"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold" onclick="goBackToReviewerDashboard()">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </button>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- PDF Viewer Section - Full Width -->
    <div class="pdf-viewer-section mb-4 shadow-sm">
        <div class="pdf-header">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-file-pdf text-danger fs-5"></i>
                <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 600px;">
                    Dokumen Proposal: {{ $proposal->judul_proposal }}
                </h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($proposal->dokumen && $proposal->dokumen->path_file)
                    <a href="{{ route('file.serve', ['path' => $proposal->dokumen->path_file], false) }}" 
                       class="btn btn-sm btn-outline-primary rounded-pill px-3" target="_blank">
                        <i class="fas fa-download me-1"></i>Unduh PDF
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="toggleFullscreen()">
                        <i class="fas fa-expand me-1"></i>Fullscreen
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" onclick="refreshPDFViewer()">
                        <i class="fas fa-redo me-1"></i>Refresh
                    </button>
                @endif
            </div>
        </div>
        <div class="p-0">
            @if($proposal->dokumen && $proposal->dokumen->path_file)
                <div class="pdf-container-full">
                    <iframe 
                        id="pdfViewer"
                        src="{{ route('file.serve', ['path' => $proposal->dokumen->path_file], false) }}"
                        style="width: 100%; height: 75vh; border: none;"
                        allowfullscreen>
                    </iframe>
                </div>
            @else
                <div class="empty-state-box py-5">
                    <i class="fas fa-file-pdf text-muted mb-3 fs-1"></i>
                    <h5 class="fw-bold text-dark">Berkas Dokumen Tidak Tersedia</h5>
                    <p class="text-secondary small">Dokumen proposal belum diunggah atau tidak dapat diakses.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Metadata -->
        <div class="col-lg-4">
            <!-- Info Proposal -->
            <div class="ui-card mb-4">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Informasi Proposal</h6>
                    </div>
                </div>
                <div class="ui-card-body p-3">
                    <div class="mb-2">
                        <small class="text-secondary fw-semibold d-block">Judul Usulan</small>
                        <div class="fw-bold text-dark small">{{ $proposal->judul_proposal }}</div>
                    </div>
                    <div class="row g-2 pt-2 border-top">
                        <div class="col-6">
                            <small class="text-secondary fw-semibold d-block">Skim</small>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">{{ $proposal->skim }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-secondary fw-semibold d-block">Tahun</small>
                            <span class="text-dark small">{{ $proposal->tahun ?? 'N/A' }}</span>
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-secondary fw-semibold d-block">Status Proposal</small>
                            <x-status-badge :status="$proposal->status" :label="$proposal->status_label" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Mahasiswa -->
            <div class="ui-card mb-4">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-user-graduate text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Ketua Pengaju</h6>
                    </div>
                </div>
                <div class="ui-card-body p-3">
                    <div class="fw-bold text-dark">{{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                    <small class="text-muted d-block">NIM: {{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                    <small class="text-muted d-block">{{ $proposal->mahasiswa->prodi_mhs ?? 'N/A' }} - {{ $proposal->mahasiswa->fakultas_mhs ?? 'N/A' }}</small>
                    @if(isset($proposal->mahasiswa->email_mhs))
                        <small class="text-muted d-block mt-1"><i class="fas fa-envelope me-1"></i>{{ $proposal->mahasiswa->email_mhs }}</small>
                    @endif
                </div>
            </div>

            <!-- Info Dosen Pendamping -->
            <div class="ui-card mb-4">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Dosen Pendamping</h6>
                    </div>
                </div>
                <div class="ui-card-body p-3">
                    <div class="fw-bold text-dark">{{ $proposal->dosen->nama_dosen ?? 'N/A' }}</div>
                    @if(isset($proposal->dosen->nuptk))
                        <small class="text-muted d-block">NUPTK/NIDN: {{ $proposal->dosen->nuptk }}</small>
                    @endif
                    @if(isset($proposal->dosen->email_dosen))
                        <small class="text-muted d-block"><i class="fas fa-envelope me-1"></i>{{ $proposal->dosen->email_dosen }}</small>
                    @endif
                </div>
            </div>

            <!-- Status Review Sebelumnya -->
            @php
                $isSeleksiView = request('seleksi') == '1';
                $existingReview = $proposal->nilaiSubstantif
                    ->where('id_reviewer', auth()->id())
                    ->where('jenis_review', $isSeleksiView ? 'seleksi' : 'pertama')
                    ->first();
                $isReviewCompleted = $existingReview && !empty($existingReview->note_substantif);
            @endphp
            <div class="ui-card mb-4">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-history text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Status Review Anda</h6>
                    </div>
                </div>
                <div class="ui-card-body p-3 text-center">
                    @if($isReviewCompleted)
                        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-success-subtle text-success mb-2">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <div class="fw-bold text-success mb-1">Review Selesai</div>
                        <small class="text-muted d-block">Terakhir diperbarui: {{ $existingReview->updated_at->format('d/m/Y H:i') }}</small>
                        <div class="mt-2 pt-2 border-top">
                            <span class="badge bg-primary fs-6 px-3 py-1">Nilai Akhir: {{ number_format($existingReview->nilai_akhir ?? 0, 2) }}</span>
                        </div>
                    @elseif($existingReview)
                        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-warning-subtle text-warning mb-2">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <div class="fw-bold text-warning mb-1">Draf Tersimpan</div>
                        <small class="text-muted d-block">Silakan lengkapi skor & catatan review Anda</small>
                    @else
                        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-info-subtle text-info mb-2">
                            <i class="fas fa-plus-circle fa-2x"></i>
                        </div>
                        <div class="fw-bold text-info mb-1">Belum Dinilai</div>
                        <small class="text-muted d-block">Silakan lakukan penilaian pada formulir di samping</small>
                    @endif
                </div>
            </div>

            @if(isset($isSeleksiMode) && $isSeleksiMode)
            <!-- Riwayat Review Tahap Pertama (Khusus Seleksi) -->
            <div class="ui-card mb-4 border-info">
                <div class="ui-card-header bg-info-subtle text-info d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-history"></i>
                        <h6 class="mb-0 fw-bold fs-6">Riwayat Review Tahap 1</h6>
                    </div>
                </div>
                <div class="ui-card-body p-3">
                    @php
                        $review1 = $proposal->nilaiSubstantif->where('jenis_review', 'pertama')->where('id_reviewer', $proposal->id_reviewer_substantif_1)->first();
                        $review2 = $proposal->nilaiSubstantif->where('jenis_review', 'pertama')->where('id_reviewer', $proposal->id_reviewer_substantif_2)->first();
                    @endphp
                    
                    <div class="mb-3 p-2 rounded bg-light border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-primary small">Reviewer 1</strong>
                            @if($review1)
                                <span class="badge bg-primary">{{ number_format($review1->nilai_akhir ?? $review1->total_nilai, 2) }}</span>
                            @endif
                        </div>
                        @if($review1)
                            <div class="small text-secondary">{{ $review1->catatan ?? $review1->note_substantif ?? 'Tidak ada catatan' }}</div>
                        @else
                            <div class="small text-muted fst-italic">Belum ada nilai</div>
                        @endif
                    </div>
                    
                    <div class="p-2 rounded bg-light border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-success small">Reviewer 2</strong>
                            @if($review2)
                                <span class="badge bg-success">{{ number_format($review2->nilai_akhir ?? $review2->total_nilai, 2) }}</span>
                            @endif
                        </div>
                        @if($review2)
                            <div class="small text-secondary">{{ $review2->catatan ?? $review2->note_substantif ?? 'Tidak ada catatan' }}</div>
                        @else
                            <div class="small text-muted fst-italic">Belum ada nilai</div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Form Penilaian Section -->
        <div class="col-lg-8">
            <div class="ui-card shadow-sm">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-edit text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Lembar Penilaian Review Substantif</h6>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Standar Belmawa</span>
                </div>
                <div class="ui-card-body p-4">
                    <form id="formReviewSubstantif" method="POST" action="{{ request('seleksi') ? route('reviewer.submit.review.substantif.seleksi', $proposal->id_proposal) : route('reviewer.submit.review.substantif', $proposal->id_proposal) }}" data-no-loading="true">
                        @csrf
                        <input type="hidden" name="skim" value="{{ $proposal->skim }}">

                        @php
                            $criteriaItems = $criteria ?? [];
                            
                            $existingTotalNilai = $existingReview ? ($existingReview->total_nilai ?? 0) : 0;
                            $existingNilaiAkhir = $existingReview ? ($existingReview->nilai_akhir ?? 0) : 0;
                            
                            $existingTotalSkor = 0;
                            $existingSkorCount = 0;
                            
                            $existingSkorRaw = $existingReview ? ($existingReview->skor_per_kriteria ?? []) : [];
                            $existingSkor = [];
                            if (!empty($existingSkorRaw) && is_array($existingSkorRaw)) {
                                foreach ($existingSkorRaw as $key => $value) {
                                    $index = (int) $key;
                                    $existingSkor[$index] = (float) $value;
                                    $existingTotalSkor += (float) $value;
                                    $existingSkorCount++;
                                }
                                ksort($existingSkor);
                            }
                            
                            $existingAvgSkor = $existingSkorCount > 0 ? ($existingTotalSkor / $existingSkorCount) : 0;
                            
                            if (empty($criteriaItems)) {
                                $criteriaItems = \App\Helpers\ProposalHelper::getSubstantifCriteria($proposal->skim);
                                if (empty($criteriaItems)) {
                                    $criteriaItems = \App\Helpers\ProposalHelper::getSubstantifCriteria('default');
                                }
                            }
                        @endphp
                        
                        @if(config('app.debug'))
                            <div class="alert alert-info py-2 px-3 mb-3 small">
                                <strong>Debug Info:</strong> Skim: {{ $proposal->skim }} | Kriteria: {{ count($criteriaItems) }} items | Existing Review: {{ $existingReview ? 'ID #'.$existingReview->id : 'Belum Ada' }}
                            </div>
                        @endif

                        <!-- Form Penilaian Substantif -->
                        @if(!$dynamicForm)
                            <div class="mb-4">
                                @if(empty($criteriaItems))
                                    <div class="alert alert-warning d-flex align-items-center gap-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>Kriteria penilaian untuk skim {{ $proposal->skim }} belum dikonfigurasi.</span>
                                    </div>
                                @else
                                    @php
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
                                    
                                    <!-- Panduan Skoring Belmawa Card -->
                                    <div class="p-3 mb-3 rounded-3 bg-light border d-flex align-items-start gap-3">
                                        <div class="p-2 rounded bg-primary-subtle text-primary">
                                            <i class="fas fa-info-circle fa-lg"></i>
                                        </div>
                                        <div class="small text-secondary">
                                            <div class="fw-bold text-dark mb-1">Panduan Penilaian Standar Belmawa:</div>
                                            <div class="row g-1">
                                                <div class="col-md-6">• Skala Skor: <strong>1</strong> (Buruk), <strong>2</strong> (Sangat Kurang), <strong>3</strong> (Kurang), <strong>5</strong> (Cukup), <strong>6</strong> (Baik), <strong>7</strong> (Sangat Baik).</div>
                                                <div class="col-md-6">• Nilai Kriteria = <strong>Bobot (%) × Skor</strong> (Maks 700).</div>
                                                <div class="col-12 mt-1 text-primary fw-semibold">• Nilai Akhir (Skala 100) dihitung otomatis: <strong>Total Nilai / 7</strong>.</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive rounded-3 border mb-3">
                                        <table class="table substantif-table table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%;" class="text-center py-3">No</th>
                                                    <th style="width: 45%;" class="py-3">Kriteria Penilaian</th>
                                                    <th style="width: 12%;" class="text-center py-3">Bobot (%)</th>
                                                    <th style="width: 23%;" class="text-center py-3">Skor (1–7)</th>
                                                    <th style="width: 15%;" class="text-center py-3">Nilai</th>
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
                                                            $skorValue = isset($existingSkor[$item['index']]) ? $existingSkor[$item['index']] : '';
                                                            $nilai = $skorValue ? ($item['bobot'] * $skorValue) : 0;
                                                        } else {
                                                            $skorValue = '';
                                                            $nilai = 0;
                                                        }
                                                        
                                                        $showNumber = !$item['is_sub'];
                                                    @endphp
                                                    <tr class="{{ $item['is_header'] ? 'bg-light fw-semibold' : '' }}">
                                                        <td class="text-center text-muted">
                                                            @if($showNumber)
                                                                <span class="badge bg-secondary-subtle text-dark border">{{ $item['main_number'] }}</span>
                                                            @elseif($item['is_sub'])
                                                                <span class="text-muted ms-2">└─</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($item['is_sub'])
                                                                <span class="ps-3 text-secondary d-inline-block">{{ $item['kriteria'] }}</span>
                                                            @else
                                                                <span class="fw-bold text-dark">{{ $item['kriteria'] }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center fw-semibold text-secondary">
                                                            @if($item['is_header'])
                                                                <span class="text-muted">-</span>
                                                            @else
                                                                @formatId($item['bobot'], 2)%
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($item['is_header'])
                                                                <span class="text-muted text-center d-block">-</span>
                                                            @else
                                                                <select class="form-select form-select-sm rounded-3 skor-input shadow-none" 
                                                                        name="skor[{{ $item['index'] }}]" 
                                                                        data-index="{{ $item['index'] }}"
                                                                        data-bobot="{{ $item['bobot'] }}"
                                                                        onchange="calculateNilai()"
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
                                                                <span class="nilai-display fw-bold text-dark" data-index="{{ $item['index'] }}">
                                                                    @if($nilai > 0)@formatId($nilai, 2)@else 0,00 @endif
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-summary-row border-top">
                                                    <td colspan="2" class="text-end fw-bold py-2">Total Bobot & Rata-rata Skor:</td>
                                                    <td class="text-center fw-bold py-2">100.00%</td>
                                                    <td class="text-center py-2">
                                                        <span class="total-skor-display badge bg-secondary-subtle text-dark border fw-bold px-2 py-1">@if($existingAvgSkor > 0)@formatId($existingAvgSkor, 2)@else 0,00 @endif</span>
                                                    </td>
                                                    <td class="text-center fw-bold py-2">
                                                        <span class="total-nilai-display text-dark fw-bold">@if($existingTotalNilai > 0)@formatId($existingTotalNilai, 2)@else 0,00 @endif</span>
                                                    </td>
                                                </tr>
                                                <tr class="final-score-row">
                                                    <td colspan="4" class="text-end fw-bold py-3">
                                                        <span class="text-primary me-2"><i class="fas fa-award"></i> Nilai Akhir Proposal:</span>
                                                        <span class="text-muted fw-normal small">(Total Nilai / 7 — Skala 100)</span>
                                                    </td>
                                                    <td class="text-center py-3">
                                                        <span class="nilai-akhir-display fw-bold text-primary fs-5">@if($existingNilaiAkhir > 0)@formatId($existingNilaiAkhir, 2)@else 0,00 @endif</span>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Dynamic Fields from FormPenilaian (operator) --}}
                        @include('reviewer.partials.dynamic_fields', [
                            'dynamicForm'    => $dynamicForm ?? null,
                            'existingAnswers' => $existingReview->extra_fields ?? [],
                            'inputPrefix'    => 'extra_fields',
                        ])

                        <!-- Catatan Review Substantif -->
                        <div class="mb-4">
                            <label for="catatan" class="form-label fw-bold text-dark d-flex justify-content-between align-items-center">
                                <span>Catatan Review Substantif <span class="text-danger">*</span></span>
                                <small class="text-muted fw-normal">Minimal 50 karakter</small>
                            </label>
                            <textarea class="form-control rounded-3" id="catatan" name="catatan" rows="6" 
                                      placeholder="Berikan penilaian menyeluruh tentang kualitas konten, ketepatan metodologi, kelayakan anggaran, dan catatan penyempurnaan proposal..." 
                                      required>{{ $existingReview->note_substantif ?? '' }}</textarea>
                            <div class="form-text text-secondary mt-1">Uraikan secara komprehensif masukan substantif yang konstruktif bagi tim pengusul.</div>
                            <div class="form-text text-danger mt-1" id="errorCatatan" style="display: none;">
                                Catatan review substantif harus diisi minimal 50 karakter
                            </div>
                        </div>

                        <!-- Submit Action Card -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top flex-wrap gap-2">
                            <div class="text-muted small">
                                @if($existingReview)
                                    <i class="fas fa-history me-1"></i>Pembaruan terakhir: {{ $existingReview->updated_at->format('d/m/Y H:i') }}
                                @else
                                    <i class="fas fa-info-circle me-1"></i>Penilaian akan disimpan dan tercatat dalam sistem.
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-3 px-4 py-2 fw-semibold" id="submitBtn">
                                    <i class="fas fa-save"></i>
                                    <span id="submitText">Simpan Review Substantif</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@php
    $initialTotalNilai = $existingReview ? ($existingReview->total_nilai ?? 0) : 0;
    $initialNilaiAkhir = $existingReview ? ($existingReview->nilai_akhir ?? 0) : 0;
    $initialAvgSkor = $existingSkorCount > 0 ? ($existingTotalSkor / $existingSkorCount) : 0;
    
    $jsTotalNilai = number_format($initialTotalNilai, 2, '.', '');
    $jsNilaiAkhir = number_format($initialNilaiAkhir, 2, '.', '');
    $jsAvgSkor = number_format($initialAvgSkor, 2, '.', '');
@endphp
<script>
    const initialTotalNilai = parseFloat('{{ $jsTotalNilai }}') || 0;
    const initialNilaiAkhir = parseFloat('{{ $jsNilaiAkhir }}') || 0;
    const initialAvgSkor = parseFloat('{{ $jsAvgSkor }}') || 0;
    
    function goBackToReviewerDashboard() {
        if (window.history.length > 1 && document.referrer && document.referrer !== window.location.href) {
            window.history.back();
            return;
        }
        window.location.href = "{{ route('reviewer.dashboard') }}";
    }

    function toggleFullscreen() {
        const pdfSection = document.querySelector('.pdf-viewer-section');
        const fullscreenBtn = document.querySelector('[onclick="toggleFullscreen()"]');
        
        if (pdfSection) {
            if (pdfSection.classList.contains('fullscreen')) {
                pdfSection.classList.remove('fullscreen');
                if (fullscreenBtn) fullscreenBtn.innerHTML = '<i class="fas fa-expand me-1"></i>Fullscreen';
                document.body.style.overflow = '';
            } else {
                pdfSection.classList.add('fullscreen');
                if (fullscreenBtn) fullscreenBtn.innerHTML = '<i class="fas fa-compress me-1"></i>Tutup Fullscreen';
                document.body.style.overflow = 'hidden';
            }
        }
    }

    function refreshPDFViewer() {
        const pdfViewer = document.getElementById('pdfViewer');
        if (pdfViewer) {
            const currentSrc = pdfViewer.src.split('?')[0];
            pdfViewer.src = currentSrc + '?t=' + Date.now();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const pdfSection = document.querySelector('.pdf-viewer-section.fullscreen');
            if (pdfSection) {
                toggleFullscreen();
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formReviewSubstantif');
        const catatanField = document.getElementById('catatan');
        const errorDiv = document.getElementById('errorCatatan');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        
        if (!form) return;
        
        if (initialTotalNilai > 0 || initialNilaiAkhir > 0 || initialAvgSkor > 0) {
            const totalNilaiDisplay = document.querySelector('.total-nilai-display');
            if (totalNilaiDisplay && initialTotalNilai > 0) {
                totalNilaiDisplay.textContent = initialTotalNilai.toFixed(2);
            }
            
            const nilaiAkhirDisplay = document.querySelector('.nilai-akhir-display');
            if (nilaiAkhirDisplay && initialNilaiAkhir > 0) {
                nilaiAkhirDisplay.textContent = initialNilaiAkhir.toFixed(2);
            }
            
            const totalSkorDisplay = document.querySelector('.total-skor-display');
            if (totalSkorDisplay && initialAvgSkor > 0) {
                totalSkorDisplay.textContent = initialAvgSkor.toFixed(2);
            }
        }
        
        setTimeout(function() {
            calculateNilai();
        }, 300);
        
        if (catatanField && errorDiv) {
            catatanField.addEventListener('input', function() {
                const value = this.value.trim();
                const minLength = 50;
                
                errorDiv.style.display = 'none';
                
                if (value.length > 0 && value.length < minLength) {
                    errorDiv.style.display = 'block';
                    errorDiv.textContent = `Catatan masih kurang ${minLength - value.length} karakter (saat ini: ${value.length}/${minLength} karakter).`;
                    errorDiv.className = 'form-text text-danger';
                    catatanField.classList.add('is-invalid');
                } else if (value.length >= minLength) {
                    errorDiv.style.display = 'block';
                    errorDiv.textContent = `✓ Catatan sudah memenuhi batas minimal (${value.length}/${minLength} karakter)`;
                    errorDiv.className = 'form-text text-success';
                    catatanField.classList.remove('is-invalid');
                    catatanField.classList.add('is-valid');
                } else {
                    errorDiv.style.display = 'none';
                    catatanField.classList.remove('is-invalid', 'is-valid');
                }
            });
        }
        
        setTimeout(function() {
            const skorInputs = document.querySelectorAll('.skor-input');
            calculateNilai();
            
            skorInputs.forEach(input => {
                input.addEventListener('input', function() { validateSkorInput(this); });
                input.addEventListener('change', function() { validateSkorInput(this); });
                input.addEventListener('blur', function() { validateSkorInput(this); });
            });
        }, 500);
        
        window.addEventListener('load', function() {
            setTimeout(function() { calculateNilai(); }, 300);
        });
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (errorDiv) errorDiv.style.display = 'none';
            const catatan = catatanField ? catatanField.value.trim() : '';
            
            let isValid = true;
            let errorMessage = '';
            
            if (!catatan) {
                errorMessage = 'Catatan review substantif wajib diisi.';
                isValid = false;
            } else if (catatan.length < 50) {
                errorMessage = `Catatan review substantif minimal 50 karakter. Saat ini ${catatan.length} karakter.`;
                isValid = false;
            }
            
            const skorInputs = document.querySelectorAll('.skor-input');
            let missingSkor = [];
            let validIndex = 0;
            if (skorInputs.length > 0) {
                skorInputs.forEach((input) => {
                    if (input.disabled || input.hasAttribute('data-header') || parseInt(input.dataset.index) < 0) {
                        return;
                    }
                    validIndex++;
                    const skor = parseFloat(input.value);
                    if (isNaN(skor) || skor < 1 || skor > 7) {
                        missingSkor.push(validIndex);
                    }
                });
                
                if (missingSkor.length > 0) {
                    errorMessage = `Mohon pilih skor untuk semua kriteria (skala 1, 2, 3, 5, 6, 7). Kriteria nomor: ${missingSkor.join(', ')}`;
                    isValid = false;
                }
            }
            
            if (!isValid) {
                if (errorDiv) {
                    errorDiv.style.display = 'block';
                    errorDiv.textContent = errorMessage;
                    errorDiv.className = 'form-text text-danger';
                }
                
                if (catatan.length < 50 && catatanField) {
                    catatanField.classList.add('is-invalid');
                    catatanField.focus();
                    catatanField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    const table = document.querySelector('.table-responsive');
                    if (table) table.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                
                if (typeof showToast === 'function') {
                    showToast(errorMessage, 'error');
                } else {
                    alert(errorMessage);
                }
                
                submitBtn.disabled = false;
                if (submitText) submitText.innerHTML = 'Simpan Review Substantif';
                return;
            }
            
            submitBtn.disabled = true;
            if (submitText) {
                submitText.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
            }
            
            const skorData = {};
            const allSkorInputs = document.querySelectorAll('.skor-input');
            allSkorInputs.forEach(input => {
                if (input.disabled || input.hasAttribute('data-header') || parseInt(input.dataset.index) < 0) {
                    return;
                }
                const index = parseInt(input.dataset.index);
                const value = parseFloat(input.value);
                if (!isNaN(value) && value >= 1 && value <= 7) {
                    skorData[index] = value;
                }
            });
            
            const formData = new FormData(form);
            formData.set('catatan', catatanField.value);
            formData.set('skim', '{{ $proposal->skim }}');
            formData.set('_token', '{{ csrf_token() }}');
            
            Object.keys(skorData).forEach(key => {
                formData.append(`skor[${key}]`, skorData[key]);
            });
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Server mengembalikan response yang tidak valid');
                }
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (typeof showToast === 'function') {
                        showToast('Berhasil: ' + data.message, 'success', 5000);
                    } else {
                        alert('Berhasil: ' + data.message);
                    }
                    
                    if (errorDiv) {
                        errorDiv.style.display = 'block';
                        errorDiv.textContent = 'Review berhasil disimpan!';
                        errorDiv.className = 'form-text text-success';
                    }
                    
                    setTimeout(() => {
                        window.location.href = window.location.href;
                    }, 1200);
                } else {
                    const errMsg = data.message || 'Terjadi kesalahan saat menyimpan review';
                    if (errorDiv) {
                        errorDiv.style.display = 'block';
                        errorDiv.textContent = errMsg;
                        errorDiv.className = 'form-text text-danger';
                    }
                    if (typeof showToast === 'function') {
                        showToast(errMsg, 'error');
                    } else {
                        alert(errMsg);
                    }
                }
            })
            .catch(error => {
                console.error('Error during submission:', error);
                const errMsg = error.message || 'Gagal memproses response server. Silakan coba lagi.';
                if (errorDiv) {
                    errorDiv.style.display = 'block';
                    errorDiv.textContent = errMsg;
                    errorDiv.className = 'form-text text-danger';
                }
                if (typeof showToast === 'function') {
                    showToast(errMsg, 'error');
                } else {
                    alert(errMsg);
                }
            })
            .finally(() => {
                submitBtn.disabled = false;
                if (submitText) submitText.innerHTML = 'Simpan Review Substantif';
            });
        });
    });
    
    function validateSkorInput(input) {
        const value = parseFloat(input.value);
        if (isNaN(value)) {
            input.setCustomValidity('Skor harus berupa angka');
            return;
        }
        if (value < 1) {
            input.value = 1;
            input.setCustomValidity('');
        } else if (value > 7) {
            input.value = 7;
            input.setCustomValidity('');
        } else {
            input.setCustomValidity('');
        }
        calculateNilai();
    }
    
    function calculateNilai() {
        const skorInputs = document.querySelectorAll('.skor-input');
        let totalNilai = 0;
        let totalSkor = 0;
        let count = 0;
        
        if (skorInputs.length === 0) return;
        
        skorInputs.forEach((input) => {
            if (input.disabled || input.hasAttribute('data-header')) return;
            
            const skor = parseFloat(input.value) || 0;
            const bobot = parseFloat(input.dataset.bobot) || 0;
            const index = parseInt(input.dataset.index);
            
            if (isNaN(index) || index < 0) return;
            
            const nilai = bobot * skor;
            totalNilai += nilai;
            totalSkor += skor;
            count++;
            
            const nilaiDisplay = document.querySelector(`.nilai-display[data-index="${index}"]`);
            if (nilaiDisplay) {
                nilaiDisplay.textContent = nilai.toFixed(2);
            }
        });
        
        const totalNilaiDisplay = document.querySelector('.total-nilai-display');
        if (totalNilaiDisplay) {
            totalNilaiDisplay.textContent = totalNilai.toFixed(2);
        }
        
        const totalSkorDisplay = document.querySelector('.total-skor-display');
        if (totalSkorDisplay && count > 0) {
            totalSkorDisplay.textContent = (totalSkor / count).toFixed(2);
        } else if (totalSkorDisplay) {
            totalSkorDisplay.textContent = '0.00';
        }
        
        const nilaiAkhir = totalNilai > 0 ? (totalNilai / 7) : 0;
        const nilaiAkhirDisplay = document.querySelector('.nilai-akhir-display');
        if (nilaiAkhirDisplay) {
            nilaiAkhirDisplay.textContent = nilaiAkhir.toFixed(2);
        }
    }
</script>
@endpush
@endsection