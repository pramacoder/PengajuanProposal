@extends('mainlayout.app')

@section('title', 'Detail Hasil Final - Operator')

@section('styles')
<style>
    .pdf-viewer-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .pdf-header {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .pdf-title {
        font-weight: 600;
        color: #333;
        margin: 0;
        margin-right: 2rem;
    }
    
    .pdf-controls {
        display: flex;
        gap: 0.5rem;
    }
    
    .pdf-viewer {
        width: 100%;
        height: 700px;
        border: none;
    }
    
    .pdf-iframe {
        width: 100%;
        height: 700px;
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        background: white;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
    }
    
    .pdf-loading {
        position: relative;
        min-height: 700px;
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
    }

    .pdf-loading .spinner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
    }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #8B0000;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        transition: opacity 0.3s ease;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .pdf-iframe {
        transition: opacity 0.3s ease;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        color: #dee2e6;
    }
    
    .empty-state h4 {
        margin-bottom: 1rem;
        color: #495057;
    }
    
    .empty-state p {
        margin-bottom: 2rem;
        font-size: 1.1rem;
    }

    /* Fullscreen styles */
    .pdf-viewer-container:fullscreen {
        background: white;
        padding: 20px;
    }
    
    .pdf-viewer-container:fullscreen .pdf-iframe {
        height: calc(100vh - 100px);
    }
    
    .pdf-viewer-container:-webkit-full-screen {
        background: white;
        padding: 20px;
    }
    
    .pdf-viewer-container:-webkit-full-screen .pdf-iframe {
        height: calc(100vh - 100px);
    }
    
    .pdf-viewer-container:-ms-fullscreen {
        background: white;
        padding: 20px;
    }
    
    .pdf-viewer-container:-ms-fullscreen .pdf-iframe {
        height: calc(100vh - 100px);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('operator.dashboard')],
        ['label' => 'Hasil Final', 'url' => route('operator.hasil.final')],
        ['label' => 'Detail Proposal', 'active' => true],
    ]" />

    <!-- Header Section -->
    <x-page-header 
        title="DETAIL HASIL FINAL" 
        subtitle="UNIVERSITAS UDAYANA" />
    
    <!-- Back Button -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('operator.hasil.final') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Hasil Final
            </a>
        </div>
    </div>

    <!-- Proposal Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>
                        Informasi Proposal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text-primary">{{ $proposal->judul_proposal }}</h4>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p><strong>Skim:</strong> <span class="badge bg-primary">{{ $proposal->skim }}</span></p>
                                    <p><strong>Dana Belmawa:</strong> <span class="text-primary fw-bold">@rupiahId($proposal->dana_diajukan_belmawa ?? 0)</span></p>
                                    <p><strong>Dana Univ:</strong> <span class="text-success fw-bold">@rupiahId($proposal->dana_diajukan_operator ?? 0)</span></p>
                                    <p><strong>Tahun Ajaran:</strong> {{ $proposal->tahun_ajaran }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-{{ $proposal->status == 'revisi' ? 'warning' : ($proposal->status == 'lolos' ? 'success' : 'danger') }}">
                                            {{ ucfirst($proposal->status) }}
                                        </span>
                                    </p>
                                    <p><strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mahasiswa Information -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Informasi Mahasiswa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Ketua Tim</h6>
                            <p><strong>Nama:</strong> {{ $proposal->ketua_nama }}</p>
                            <p><strong>NIM:</strong> {{ $proposal->ketua_nim }}</p>
                            <p><strong>Prodi:</strong> {{ $proposal->ketua_prodi }}</p>
                            <p><strong>Fakultas:</strong> {{ $proposal->ketua_fakultas }}</p>
                            <p><strong>Email:</strong> {{ $proposal->ketua_email }}</p>
                            <p><strong>No. HP:</strong> {{ $proposal->ketua_no_hp }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Dosen Pendamping</h6>
                            <p><strong>Nama:</strong> {{ $proposal->dosen_pembimbing }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Proposal Terbaru Mahasiswa
                    </h5>
                </div>
                <div class="card-body">
                    @if($latestProposals->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($latestProposals as $latest)
                                <div class="list-group-item px-0 py-2 border-bottom">
                                    <h6 class="mb-1" style="font-size: 0.9rem;">
                                        <a href="{{ route('operator.detail.hasil.final', $latest->id_proposal) }}" class="text-decoration-none">
                                            {{ Str::limit($latest->judul_proposal, 50) }}
                                        </a>
                                    </h6>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <span class="badge bg-secondary">{{ $latest->skim }}</span>
                                            <span class="badge bg-{{ $latest->status == 'lolos' ? 'success' : ($latest->status == 'revisi' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($latest->status) }}
                                            </span>
                                        </small>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($latest->tanggal_pengajuan)->format('d M Y') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Tidak ada proposal lain dari mahasiswa ini.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Proposal Viewer Section -->
    @if($proposal->dokumen && $proposal->dokumen->path_file)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-file-pdf me-2"></i>
                        Dokumen Proposal
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="pdf-viewer-container">
                        <div class="pdf-header">
                            <h5 class="pdf-title">
                                <i class="fas fa-file-pdf me-2"></i>
                                <span id="pdfProposalTitle">{{ $proposal->dokumen->nama_file ?? 'Proposal PDF' }}</span>
                            </h5>
                            <div class="pdf-controls">
                                <button id="fullscreenProposalBtn" class="btn btn-outline-secondary btn-sm me-2">
                                    <i class="fas fa-expand me-1"></i>Fullscreen
                                </button>
                                <a href="{{ route('operator.proposal.view.pdf', $proposal->id_proposal) }}" 
                                   class="btn btn-outline-primary btn-sm" 
                                   target="_blank">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                        <div id="pdfProposalViewer" class="pdf-loading">
                            <div class="spinner"></div>
                            <iframe id="proposalPdfIframe" 
                                    src="{{ route('operator.proposal.view.pdf', $proposal->id_proposal) }}" 
                                    class="pdf-iframe" 
                                    style="display: none;">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Dokumen proposal tidak tersedia.</strong>
            </div>
        </div>
    </div>
    @endif

    <!-- File Revisi Section -->
    @if($proposal->proposalRevisi->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-file-pdf me-2"></i>
                        File Revisi yang Dikumpulkan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($proposal->proposalRevisi as $revisi)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                <strong>{{ $revisi->nama_file }}</strong>
                                <br><small class="text-muted">
                                    Diupload: {{ \Carbon\Carbon::parse($revisi->tanggal_submit)->format('d M Y H:i') }}
                                </small>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-info me-2 view-pdf-btn" 
                                        data-pdf-url="{{ route('operator.revisi.view', $revisi->id_revisi) }}"
                                        data-pdf-name="{{ $revisi->nama_file }}">
                                    <i class="fas fa-eye me-1"></i>Lihat
                                </button>
                                <a href="{{ route('operator.revisi.download', $revisi->id_revisi) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   target="_blank">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Viewer Section untuk Revisi -->
    <div class="pdf-viewer-container" id="revisiPdfViewer" style="display: none;">
        <div class="pdf-header">
            <h5 class="pdf-title">
                <i class="fas fa-file-pdf me-2"></i>
                <span id="pdfViewerTitle">Pilih file untuk dilihat</span>
            </h5>
            <div class="pdf-controls">
                <button id="fullscreenBtn" class="btn btn-outline-secondary btn-sm me-2" style="display: none;">
                    <i class="fas fa-expand me-1"></i>Fullscreen
                </button>
                <button id="downloadBtn" class="btn btn-outline-primary btn-sm" style="display: none;">
                    <i class="fas fa-download me-1"></i>Download
                </button>
            </div>
        </div>
        <div id="pdfViewer" class="pdf-loading">
            <div class="spinner"></div>
            <!-- PDF iframe will be inserted here -->
        </div>
    </div>
    @endif

    <!-- Form Hasil Final -->
    <!-- Tabel Penilaian Substantif dari 2 Reviewer -->
    @if($nilaiSubstantif1 || $nilaiSubstantif2)
    <div class="row mb-4">
        <div class="col-12">
                <h5 class="mb-3">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Referensi Penilaian Substantif dari Reviewer
                </h5>
            </div>
            
            @if($nilaiSubstantif1)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-user-check me-2"></i>
                                Reviewer Substantif 1
                                @if($nilaiSubstantif1->reviewer)
                                    - {{ $nilaiSubstantif1->reviewer->nama_reviewer }}
                                @endif
                            </h6>
                        </div>
                        <div class="card-body">
                            @php
                                $skor1 = $nilaiSubstantif1->skor_per_kriteria ?? [];
                                
                                // Flatten struktur kriteria untuk menampilkan dengan benar
                                $flattenedCriteria1 = [];
                                $criteriaIndex1 = 0;
                                $mainCriteriaNumber1 = 0;
                                
                                foreach($criteria as $mainItem) {
                                    if (isset($mainItem['sub_kriteria']) && !empty($mainItem['sub_kriteria'])) {
                                        // Kriteria utama dengan sub-kriteria
                                        $mainCriteriaNumber1++;
                                        
                                        // Tambahkan header kriteria utama
                                        $flattenedCriteria1[] = [
                                            'main_number' => $mainCriteriaNumber1,
                                            'is_sub' => false,
                                            'is_header' => true,
                                            'kriteria' => $mainItem['kriteria'],
                                            'bobot' => array_sum(array_column($mainItem['sub_kriteria'], 'bobot')),
                                            'index' => -1,
                                            'has_sub' => true
                                        ];
                                        
                                        // Tambahkan sub-kriteria
                                        foreach($mainItem['sub_kriteria'] as $subItem) {
                                            $flattenedCriteria1[] = [
                                                'main_number' => $mainCriteriaNumber1,
                                                'is_sub' => true,
                                                'is_header' => false,
                                                'kriteria' => $subItem['kriteria'],
                                                'bobot' => $subItem['bobot'],
                                                'index' => $criteriaIndex1++,
                                                'has_sub' => false
                                            ];
                                        }
                                    } else {
                                        // Kriteria utama tanpa sub-kriteria
                                        $mainCriteriaNumber1++;
                                        $flattenedCriteria1[] = [
                                            'main_number' => $mainCriteriaNumber1,
                                            'is_sub' => false,
                                            'is_header' => false,
                                            'kriteria' => $mainItem['kriteria'],
                                            'bobot' => $mainItem['bobot'],
                                            'index' => $criteriaIndex1++,
                                            'has_sub' => false
                                        ];
                                    }
                                }
                            @endphp
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="50%">Kriteria</th>
                                            <th width="15%" class="text-center">Bobot</th>
                                            <th width="15%" class="text-center">Skor</th>
                                            <th width="15%" class="text-center">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($flattenedCriteria1 as $item)
                                            @php
                                                if ($item['index'] >= 0) {
                                                    $skorValue = isset($skor1[$item['index']]) ? $skor1[$item['index']] : 0;
                                                    $nilai = $item['bobot'] * $skorValue;
                                                } else {
                                                    $skorValue = '-';
                                                    $nilai = '-';
                                                }
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    @if($item['is_header'])
                                                        <strong>{{ $item['main_number'] }}</strong>
                                                    @elseif($item['is_sub'])
                                                        <span class="text-muted" style="font-size: 0.85em;">└─</span>
                                                    @else
                                                        {{ $item['main_number'] }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item['is_sub'])
                                                        <small style="padding-left: 1.5rem; color: #6c757d;">{{ $item['kriteria'] }}</small>
                                                    @else
                                                        <small><strong>{{ $item['kriteria'] }}</strong></small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($item['is_header'])
                                                        <span class="text-muted">-</span>
                                                    @else
                                                        @formatId($item['bobot'], 2)
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($item['is_header'])
                                                        <span class="text-muted">-</span>
                                                    @else
                                                        @if(is_numeric($skorValue))@formatId($skorValue, 1)@else{{ $skorValue }}@endif
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($item['is_header'])
                                                        <span class="text-muted">-</span>
                                                    @else
                                                        @if(is_numeric($nilai))@formatId($nilai, 2)@else{{ $nilai }}@endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Total</td>
                                            <td class="text-center fw-bold">-</td>
                                            <td class="text-center fw-bold">@formatId($nilaiSubstantif1->total_nilai ?? 0, 2)</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-end fw-bold">Nilai Akhir</td>
                                            <td class="text-center fw-bold text-primary">@formatId($nilaiSubstantif1->nilai_akhir ?? 0, 2)</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            @if($nilaiSubstantif1->note_substantif)
                                <div class="mt-2">
                                    <small><strong>Catatan:</strong> {{ Str::limit($nilaiSubstantif1->note_substantif, 100) }}</small>
                                </div>
                            @endif
                        </div>
        </div>
    </div>
    @endif

            @if($nilaiSubstantif2)
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-user-check me-2"></i>
                                Reviewer Substantif 2
                                @if($nilaiSubstantif2->reviewer)
                                    - {{ $nilaiSubstantif2->reviewer->nama_reviewer }}
                                @endif
                            </h6>
                        </div>
                        <div class="card-body">
                            @php
                                $skor2 = $nilaiSubstantif2->skor_per_kriteria ?? [];
                                
                                // Flatten struktur kriteria untuk menampilkan dengan benar
                                $flattenedCriteria2 = [];
                                $criteriaIndex2 = 0;
                                $mainCriteriaNumber2 = 0;
                                
                                foreach($criteria as $mainItem) {
                                    if (isset($mainItem['sub_kriteria']) && !empty($mainItem['sub_kriteria'])) {
                                        // Kriteria utama dengan sub-kriteria
                                        $mainCriteriaNumber2++;
                                        
                                        // Tambahkan header kriteria utama
                                        $flattenedCriteria2[] = [
                                            'main_number' => $mainCriteriaNumber2,
                                            'is_sub' => false,
                                            'is_header' => true,
                                            'kriteria' => $mainItem['kriteria'],
                                            'bobot' => array_sum(array_column($mainItem['sub_kriteria'], 'bobot')),
                                            'index' => -1,
                                            'has_sub' => true
                                        ];
                                        
                                        // Tambahkan sub-kriteria
                                        foreach($mainItem['sub_kriteria'] as $subItem) {
                                            $flattenedCriteria2[] = [
                                                'main_number' => $mainCriteriaNumber2,
                                                'is_sub' => true,
                                                'is_header' => false,
                                                'kriteria' => $subItem['kriteria'],
                                                'bobot' => $subItem['bobot'],
                                                'index' => $criteriaIndex2++,
                                                'has_sub' => false
                                            ];
                                        }
                                    } else {
                                        // Kriteria utama tanpa sub-kriteria
                                        $mainCriteriaNumber2++;
                                        $flattenedCriteria2[] = [
                                            'main_number' => $mainCriteriaNumber2,
                                            'is_sub' => false,
                                            'is_header' => false,
                                            'kriteria' => $mainItem['kriteria'],
                                            'bobot' => $mainItem['bobot'],
                                            'index' => $criteriaIndex2++,
                                            'has_sub' => false
                                        ];
                                    }
                                }
                            @endphp
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="5%">No</th>
                                            <th width="50%">Kriteria</th>
                                            <th width="15%" class="text-center">Bobot</th>
                                            <th width="15%" class="text-center">Skor</th>
                                            <th width="15%" class="text-center">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($flattenedCriteria2 as $item)
                                            @php
                                                if ($item['index'] >= 0) {
                                                    $skorValue = isset($skor2[$item['index']]) ? $skor2[$item['index']] : 0;
                                                    $nilai = $item['bobot'] * $skorValue;
                                                } else {
                                                    $skorValue = '-';
                                                    $nilai = '-';
                                                }
                                            @endphp
                                            <tr>
                                                <td class="text-center">
                                                    @if($item['is_header'])
                                                        <strong>{{ $item['main_number'] }}</strong>
                                                    @elseif($item['is_sub'])
                                                        <span class="text-muted" style="font-size: 0.85em;">└─</span>
                                                    @else
                                                        {{ $item['main_number'] }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item['is_sub'])
                                                        <small style="padding-left: 1.5rem; color: #6c757d;">{{ $item['kriteria'] }}</small>
                                                    @else
                                                        <small><strong>{{ $item['kriteria'] }}</strong></small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($item['is_header'])
                                                        <span class="text-muted">-</span>
                                                    @else
                                                        @formatId($item['bobot'], 2)
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($item['is_header'])
                                                        <span class="text-muted">-</span>
                                                    @else
                                                        @if(is_numeric($skorValue))@formatId($skorValue, 1)@else{{ $skorValue }}@endif
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($item['is_header'])
                                                        <span class="text-muted">-</span>
                                                    @else
                                                        @if(is_numeric($nilai))@formatId($nilai, 2)@else{{ $nilai }}@endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Total</td>
                                            <td class="text-center fw-bold">-</td>
                                            <td class="text-center fw-bold">@formatId($nilaiSubstantif2->total_nilai ?? 0, 2)</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" class="text-end fw-bold">Nilai Akhir</td>
                                            <td class="text-center fw-bold text-primary">@formatId($nilaiSubstantif2->nilai_akhir ?? 0, 2)</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            @if($nilaiSubstantif2->note_substantif)
                                <div class="mt-2">
                                    <small><strong>Catatan:</strong> {{ Str::limit($nilaiSubstantif2->note_substantif, 100) }}</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-gavel me-2"></i>
                        Penilaian Hasil Final
                    </h5>
                </div>
                <div class="card-body">
                    @if($proposal->hasilFinal)
                        <!-- Display existing result -->
                        <div class="alert alert-info mb-3">
                            <h6><i class="fas fa-info-circle me-2"></i>Hasil Final Sudah Ditentukan</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-{{ $proposal->hasilFinal->status_final == 'lolos' ? 'success' : 'danger' }}">
                                            {{ $proposal->hasilFinal->status_final == 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                                        </span>
                                    </p>
                                    <p><strong>Nilai:</strong> 
                                        <span class="badge bg-primary fs-6">@formatId($proposal->hasilFinal->nilai, 2)</span>
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    @if($proposal->hasilFinal && ($proposal->hasilFinal->dana_disetujui_belmawa || $proposal->hasilFinal->dana_disetujui_operator))
                                        <p><strong>Dana yang Disetujui (Belmawa):</strong> 
                                            <span class="badge bg-primary fs-6">@rupiahId($proposal->hasilFinal->dana_disetujui_belmawa ?? 0)</span>
                                        </p>
                                        <p><strong>Dana yang Disetujui (Universitas):</strong> 
                                            <span class="badge bg-success fs-6">@rupiahId($proposal->hasilFinal->dana_disetujui_operator ?? 0)</span>
                                        </p>
                                    @endif
                                    <p><strong>Ditentukan pada:</strong> {{ \Carbon\Carbon::parse($proposal->hasilFinal->created_at)->format('d M Y H:i') }}</p>
                                </div>
                                <div class="col-md-4">
                            @if($proposal->hasilFinal->catatan_final)
                                        <p><strong>Catatan:</strong> {{ Str::limit($proposal->hasilFinal->catatan_final, 100) }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($proposal->hasilFinal->catatan_final && strlen($proposal->hasilFinal->catatan_final) > 100)
                                <p><strong>Catatan Lengkap:</strong> {{ $proposal->hasilFinal->catatan_final }}</p>
                            @endif
                        </div>
                        
                        <!-- Edit button -->
                        <button type="button" class="btn btn-warning mb-3" onclick="toggleEditForm()">
                            <i class="fas fa-edit me-2"></i>Edit Hasil Final
                        </button>
                    @endif

                    <!-- Form for input/update -->
                    <form id="hasilFinalForm" action="{{ route('operator.update.hasil.final') }}" method="POST" 
                          @if($proposal->hasilFinal) style="display: none;" @endif>
                        @csrf
                        <input type="hidden" name="proposal_id" value="{{ $proposal->id_proposal }}">
                        <input type="hidden" name="skim" value="{{ $proposal->skim }}">
                        
                        @php
                            $criteriaItems = $criteria ?? [];
                            $existingHasilFinal = $proposal->hasilFinal;
                            $existingSkorFinalRaw = $existingHasilFinal ? ($existingHasilFinal->skor_per_kriteria ?? []) : [];
                            
                            // Normalize existing skor untuk memastikan index numerik
                            $existingSkorFinal = [];
                            if (!empty($existingSkorFinalRaw) && is_array($existingSkorFinalRaw)) {
                                foreach ($existingSkorFinalRaw as $key => $value) {
                                    $index = (int) $key;
                                    $existingSkorFinal[$index] = (float) $value;
                                }
                                ksort($existingSkorFinal);
                            }
                            
                            // Jika kriteria kosong, gunakan default
                            if (empty($criteriaItems)) {
                                $criteriaItems = \App\Helpers\ProposalHelper::getSubstantifCriteria('default');
                            }
                            
                            // Flatten struktur untuk perhitungan dan input
                            $flattenedCriteria = [];
                            $criteriaIndex = 0;
                            $mainCriteriaNumber = 0;
                            
                            foreach($criteriaItems as $mainItem) {
                                if (isset($mainItem['sub_kriteria']) && !empty($mainItem['sub_kriteria'])) {
                                    // Kriteria utama dengan sub-kriteria
                                    $mainCriteriaNumber++;
                                    
                                    // Tambahkan header kriteria utama
                                    $flattenedCriteria[] = [
                                        'main_number' => $mainCriteriaNumber,
                                        'is_sub' => false,
                                        'is_header' => true,
                                        'kriteria' => $mainItem['kriteria'],
                                        'bobot' => array_sum(array_column($mainItem['sub_kriteria'], 'bobot')),
                                        'index' => -1,
                                        'has_sub' => true
                                    ];
                                    
                                    // Tambahkan sub-kriteria
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
                                    // Kriteria utama tanpa sub-kriteria
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

                        <!-- Form Penilaian Hasil Final -->
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-3">Penilaian Hasil Final <span class="text-danger">*</span></label>
                            
                            @if(empty($criteriaItems))
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Kriteria penilaian untuk skim {{ $proposal->skim }} belum tersedia.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-primary">
                                            <tr>
                                                <th width="5%">No</th>
                                                <th width="50%">Kriteria Penilaian</th>
                                                <th width="10%" class="text-center">Bobot (%)</th>
                                                <th width="15%" class="text-center">Skor (0-10)</th>
                                                <th width="15%" class="text-center">Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($flattenedCriteria as $item)
                                                @php
                                                    // Untuk header, tidak ada skor (index = -1)
                                                    if ($item['index'] >= 0) {
                                                        $skorValue = isset($existingSkorFinal[$item['index']]) ? $existingSkorFinal[$item['index']] : '';
                                                        $nilai = $skorValue ? ($item['bobot'] * $skorValue) : 0;
                                                    } else {
                                                        $skorValue = '';
                                                        $nilai = 0;
                                                    }
                                                    
                                                    // Tampilkan nomor untuk kriteria utama
                                                    $showNumber = false;
                                                    if (!$item['is_sub']) {
                                                        $showNumber = true;
                                                    }
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        @if($showNumber)
                                                            {{ $item['main_number'] }}
                                                        @elseif($item['is_sub'])
                                                            <span class="text-muted" style="font-size: 0.85em;">└─</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item['is_sub'])
                                                            <span style="padding-left: 1.5rem; color: #6c757d; font-size: 0.95em;">{{ $item['kriteria'] }}</span>
                                                        @else
                                                            <strong>{{ $item['kriteria'] }}</strong>
                                                        @endif
                                                    </td>
                                                    <td class="text-center fw-bold">
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
                                                            <input type="number" 
                                                                   class="form-control form-control-sm skor-final-input text-center" 
                                                                   name="skor[{{ $item['index'] }}]" 
                                                                   value="{{ $skorValue }}"
                                                                   min="0" 
                                                                   max="10" 
                                                                   step="0.1"
                                                                   data-index="{{ $item['index'] }}"
                                                                   data-bobot="{{ $item['bobot'] }}"
                                                                   required>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($item['is_header'])
                                                            <span class="text-muted">-</span>
                                                        @else
                                                            <span class="nilai-final-display fw-bold" data-index="{{ $item['index'] }}">
                                                                @if($nilai > 0)@formatId($nilai, 2)@else 0,00 @endif
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-secondary">
                                            <tr>
                                                <td colspan="2" class="text-end fw-bold">Total</td>
                                                <td class="text-center fw-bold">100.00</td>
                                                <td class="text-center">
                                                    <span class="total-skor-final-display fw-bold">0.00</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="total-nilai-final-display fw-bold">0.00</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4" class="text-end fw-bold">Nilai Akhir (Total / 10)</td>
                                                <td class="text-center">
                                                    <span class="nilai-akhir-final-display fw-bold text-primary" style="font-size: 1.2em;">0.00</span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required-field">Status Final</label>
                                    <select class="form-select" name="status_final" required>
                                        <option value="">Pilih status final</option>
                                        <option value="lolos" {{ $proposal->hasilFinal && $proposal->hasilFinal->status_final == 'lolos' ? 'selected' : '' }}>Lolos</option>
                                        <option value="tidak_lolos" {{ $proposal->hasilFinal && $proposal->hasilFinal->status_final == 'tidak_lolos' ? 'selected' : '' }}>Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label required-field">Nilai Final (Otomatis dari Penilaian)</label>
                                    <input type="number" class="form-control" name="nilai" id="nilaiFinalInput"
                                           value="{{ $proposal->hasilFinal ? $proposal->hasilFinal->nilai : '' }}"
                                           min="0" max="100" step="0.01" required
                                           placeholder="Akan terisi otomatis"
                                           readonly>
                                    <div class="form-text">Nilai akan terisi otomatis berdasarkan penilaian di atas</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Dana Disetujui (Belmawa)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control js-format-id-int" name="dana_disetujui_belmawa" 
                                                   value="{{ $proposal->hasilFinal ? ($proposal->hasilFinal->dana_disetujui_belmawa > 0 ? (int)$proposal->hasilFinal->dana_disetujui_belmawa : '') : '' }}" 
                                                   placeholder="0" max="8000000">
                                        </div>
                                        <div class="form-text">Maksimal 8.000.000</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Dana Disetujui (Universitas)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control js-format-id-int" name="dana_disetujui_operator" 
                                                   value="{{ $proposal->hasilFinal ? ($proposal->hasilFinal->dana_disetujui_operator > 0 ? (int)$proposal->hasilFinal->dana_disetujui_operator : '') : '' }}" 
                                                   placeholder="0" max="2000000">
                                        </div>
                                        <div class="form-text">Maksimal 2.000.000</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Catatan Final</label>
                            <textarea class="form-control" name="catatan_final" rows="4" 
                                      placeholder="Berikan catatan untuk mahasiswa...">{{ $proposal->hasilFinal ? $proposal->hasilFinal->catatan_final : '' }}</textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ $proposal->hasilFinal ? 'Update Hasil Final' : 'Simpan Hasil Final' }}
                            </button>
                            @if($proposal->hasilFinal)
                                <button type="button" class="btn btn-secondary" onclick="toggleEditForm()">
                                    <i class="fas fa-times me-2"></i>Batal
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@php
    $hasRevisi = $proposal->proposalRevisi->count() > 0;
    $firstRevisi = $hasRevisi ? $proposal->proposalRevisi->first() : null;
    $firstRevisiUrl = $firstRevisi ? route('operator.revisi.view', $firstRevisi->id_revisi) : null;
    
    // Prepare JSON strings
    $pageDataJson = json_encode([
        'hasRevisi' => $hasRevisi,
        'firstRevisi' => $firstRevisi,
        'firstRevisiUrl' => $firstRevisiUrl
    ]);
@endphp
<script>
    // PDF viewer variables
    let currentPdfUrl = null;
    let currentFileName = null;
    
    // Blade data converted to JavaScript
    const pageData = JSON.parse('{!! addslashes($pageDataJson) !!}');

    function toggleEditForm() {
        const form = document.getElementById('hasilFinalForm');
        const editBtn = document.querySelector('button[onclick="toggleEditForm()"]');
        
        if (form.style.display === 'none') {
            form.style.display = 'block';
            editBtn.innerHTML = '<i class="fas fa-times me-2"></i>Batal';
        } else {
            form.style.display = 'none';
            editBtn.innerHTML = '<i class="fas fa-edit me-2"></i>Edit Hasil Final';
        }
    }

    // Calculate nilai final real-time
    function calculateNilaiFinal() {
        const skorInputs = document.querySelectorAll('.skor-final-input');
        let totalNilai = 0;
        let totalSkor = 0;
        let count = 0;
        
        skorInputs.forEach(input => {
            // Skip header rows (index < 0)
            const index = parseInt(input.dataset.index);
            if (index < 0) return;
            
            const skor = parseFloat(input.value) || 0;
            const bobot = parseFloat(input.dataset.bobot) || 0;
            
            // Hitung nilai per kriteria: Nilai = Bobot × Skor
            const nilai = bobot * skor;
            totalNilai += nilai;
            totalSkor += skor;
            count++;
            
            // Update nilai display per kriteria
            const nilaiDisplay = document.querySelector(`.nilai-final-display[data-index="${index}"]`);
            if (nilaiDisplay) {
                nilaiDisplay.textContent = nilai.toFixed(2);
            }
        });
        
        // Update total nilai
        const totalNilaiDisplay = document.querySelector('.total-nilai-final-display');
        if (totalNilaiDisplay) {
            totalNilaiDisplay.textContent = totalNilai.toFixed(2);
        }
        
        // Update total skor (rata-rata)
        const totalSkorDisplay = document.querySelector('.total-skor-final-display');
        if (totalSkorDisplay) {
            totalSkorDisplay.textContent = count > 0 ? (totalSkor / count).toFixed(2) : '0.00';
        }
        
        // Hitung nilai akhir: Total Nilai / 10
        const nilaiAkhir = totalNilai / 10;
        const nilaiAkhirDisplay = document.querySelector('.nilai-akhir-final-display');
        if (nilaiAkhirDisplay) {
            nilaiAkhirDisplay.textContent = nilaiAkhir.toFixed(2);
        }
        
        // Update input nilai final (readonly)
        const nilaiFinalInput = document.getElementById('nilaiFinalInput');
        if (nilaiFinalInput) {
            nilaiFinalInput.value = nilaiAkhir.toFixed(2);
        }
    }
    
    // Event listener untuk input skor final
    document.addEventListener('DOMContentLoaded', function() {
        const skorInputs = document.querySelectorAll('.skor-final-input');
        
        // Hitung nilai awal jika ada data existing
        calculateNilaiFinal();
        
        // Hitung nilai setiap kali skor berubah
        skorInputs.forEach(input => {
            input.addEventListener('input', calculateNilaiFinal);
            input.addEventListener('change', calculateNilaiFinal);
        });
    });

    // PDF Viewer Functions
    function viewPDF(pdfUrl, fileName) {
        currentPdfUrl = pdfUrl;
        currentFileName = fileName;
        
        document.getElementById('pdfViewerTitle').textContent = fileName;
        
        // Show loading
        const viewer = document.getElementById('pdfViewer');
        viewer.innerHTML = '<div class="spinner"></div>';
        
        // Show controls
        document.getElementById('fullscreenBtn').style.display = 'inline-block';
        document.getElementById('downloadBtn').style.display = 'inline-block';
        
        // Load PDF using iframe
        loadPDFDocument(pdfUrl, viewer);
    }

    function loadPDFDocument(pdfUrl, pdfViewer) {
        console.log('Loading PDF from URL:', pdfUrl);
        
        if (!pdfUrl) {
            pdfViewer.innerHTML = '<div class="empty-state"><i class="fas fa-file-pdf"></i><h4>Dokumen Tidak Tersedia</h4><p>Dokumen tidak ditemukan.</p></div>';
            return;
        }

        // Create iframe
        const iframe = document.createElement('iframe');
        iframe.src = pdfUrl;
        iframe.className = 'pdf-iframe';
        iframe.style.width = '100%';
        iframe.style.height = '700px';
        iframe.style.border = 'none';
        iframe.style.borderRadius = '8px';
        iframe.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
        iframe.style.opacity = '0';
        iframe.style.transition = 'opacity 0.3s ease';
        
        // Pre-load iframe before showing
        iframe.onload = function() {
            console.log('Iframe loaded successfully');
            setTimeout(() => {
                iframe.style.opacity = '1';
                const spinner = pdfViewer.querySelector('.spinner');
                if (spinner) {
                    spinner.style.opacity = '0';
                    setTimeout(() => {
                        if (spinner.parentNode) {
                            spinner.parentNode.removeChild(spinner);
                        }
                    }, 300);
                }
            }, 100);
        };

        // Error handler
        iframe.onerror = function() {
            console.log('Iframe failed, showing download option');
            showDownloadOption(pdfViewer);
        };

        pdfViewer.appendChild(iframe);
        
        // Set timeout for iframe
        setTimeout(() => {
            const spinner = pdfViewer.querySelector('.spinner');
            if (spinner && iframe.style.opacity === '0') {
                console.log('Iframe timeout, showing download option');
                showDownloadOption(pdfViewer);
            }
        }, 5000);
    }

    function showDownloadOption(pdfViewer) {
        pdfViewer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-file-pdf"></i>
                <h4>PDF Tidak Dapat Ditampilkan</h4>
                <p>Browser Anda tidak dapat menampilkan PDF secara langsung.</p>
                <p>Silakan download file untuk melihat dokumen:</p>
                <div style="margin-top: 1rem;">
                    <a href="${currentPdfUrl}" 
                       class="btn btn-primary" 
                       download="${currentFileName}">
                        <i class="fas fa-download me-1"></i>Download PDF
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
                if (iframe) {
                    if (iframe.requestFullscreen) {
                        iframe.requestFullscreen();
                    } else if (iframe.webkitRequestFullscreen) {
                        iframe.webkitRequestFullscreen();
                    } else if (iframe.msRequestFullscreen) {
                        iframe.msRequestFullscreen();
                    }
                } else {
                    if (pdfViewer.requestFullscreen) {
                        pdfViewer.requestFullscreen();
                    } else if (pdfViewer.webkitRequestFullscreen) {
                        pdfViewer.webkitRequestFullscreen();
                    } else if (pdfViewer.msRequestFullscreen) {
                        pdfViewer.msRequestFullscreen();
                    }
                }
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

    // Initialize PDF proposal viewer
    function initializeProposalPdfViewer() {
        const proposalIframe = document.getElementById('proposalPdfIframe');
        if (proposalIframe) {
            proposalIframe.onload = function() {
                const spinner = document.querySelector('#pdfProposalViewer .spinner');
                if (spinner) {
                    spinner.style.opacity = '0';
                    setTimeout(() => {
                        if (spinner.parentNode) {
                            spinner.parentNode.removeChild(spinner);
                        }
                        proposalIframe.style.display = 'block';
                    }, 300);
                }
            };
            
            // Show iframe after a short delay
            setTimeout(() => {
                proposalIframe.style.display = 'block';
            }, 500);
        }
        
        // Initialize fullscreen for proposal PDF
        const fullscreenProposalBtn = document.getElementById('fullscreenProposalBtn');
        if (fullscreenProposalBtn && proposalIframe) {
            fullscreenProposalBtn.addEventListener('click', function() {
                if (proposalIframe.requestFullscreen) {
                    proposalIframe.requestFullscreen();
                } else if (proposalIframe.webkitRequestFullscreen) {
                    proposalIframe.webkitRequestFullscreen();
                } else if (proposalIframe.msRequestFullscreen) {
                    proposalIframe.msRequestFullscreen();
                }
            });
        }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Detail hasil final page loaded');
        
        // Initialize PDF proposal viewer
        initializeProposalPdfViewer();
        
        // Initialize PDF view buttons for revisi
        document.querySelectorAll('.view-pdf-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const pdfUrl = this.getAttribute('data-pdf-url');
                const pdfName = this.getAttribute('data-pdf-name');
                viewPDF(pdfUrl, pdfName);
                
                // Show revisi PDF viewer
                const revisiViewer = document.getElementById('revisiPdfViewer');
                if (revisiViewer) {
                    revisiViewer.style.display = 'block';
                }
            });
        });
        
        // Initialize fullscreen functionality for revisi
        initializeFullscreen();
        
        // Initialize download functionality for revisi
        initializeDownload();
        
        // Pre-load first revision PDF if available
        if (pageData.hasRevisi && pageData.firstRevisi) {
            console.log('Pre-loading first revision PDF:', pageData.firstRevisi.nama_file);
            // Pre-load the PDF URL for faster display
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = pageData.firstRevisiUrl;
            document.head.appendChild(link);
        }
        
    });

    // Form submission
    document.getElementById('hasilFinalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        submitBtn.disabled = true;
        
        // Create FormData from form
        const formData = new FormData(this);
        
        // Validasi client-side sebelum submit
        const catatanFinal = formData.get('catatan_final');
        if (catatanFinal && catatanFinal.trim().length > 0 && catatanFinal.trim().length < 50) {
            showToast('Catatan final minimal 50 karakter. Teks yang Anda berikan di catatan kurang dari 50 karakter.', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            return;
        }
        
        // Validasi skor
        const skorInputs = document.querySelectorAll('.skor-final-input');
        let hasEmptySkor = false;
        skorInputs.forEach(input => {
            if (!input.value || input.value === '') {
                hasEmptySkor = true;
            }
        });
        
        if (hasEmptySkor) {
            showToast('Semua skor penilaian wajib diisi (0-10)', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            return;
        }
        
        // Collect skor data manually to ensure correct format
        const skorData = {};
        document.querySelectorAll('.skor-final-input').forEach(input => {
            const index = parseInt(input.dataset.index);
            if (index >= 0 && input.value !== '') {
                const skorValue = parseFloat(input.value);
                // Validate skor range (0-10)
                if (skorValue >= 0 && skorValue <= 10) {
                    skorData[index] = skorValue;
                }
            }
        });
        
        // Remove existing skor entries from formData
        formData.delete('skor[]');
        formData.delete('skor');
        
        // Append skor as array (Laravel will handle it)
        Object.keys(skorData).forEach(key => {
            formData.append(`skor[${key}]`, skorData[key]);
        });
        
        // Pastikan action URL benar
        const formAction = this.getAttribute('action');
        console.log('Form action:', formAction);
        
        fetch(formAction, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(async response => {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.includes("application/json")) {
                return response.json();
            } else {
                // Jika response bukan JSON, mungkin ada error HTML
                const text = await response.text();
                throw new Error('Server mengembalikan response yang tidak valid. Status: ' + response.status);
            }
        })
        .then(data => {
            if (data.success) {
                showToast('Hasil final berhasil diperbarui!', 'success');
                // Optionally reload the page or update the UI
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                // Tampilkan error dengan detail jika ada
                let errorMessage = data.message || 'Terjadi kesalahan';
                
                // Jika ada errors object, tambahkan detail
                if (data.errors) {
                    const errorDetails = Object.values(data.errors).flat().join(', ');
                    errorMessage += '. Detail: ' + errorDetails;
                }
                
                showToast('Gagal memperbarui hasil final: ' + errorMessage, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMessage = 'Terjadi kesalahan saat menyimpan hasil final';
            
            if (error.message) {
                errorMessage += ': ' + error.message;
            } else {
                errorMessage += '. Silakan coba lagi atau hubungi administrator.';
            }
            
            showToast(errorMessage, 'error');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    function showToast(message, type = 'info', duration = 3000) {
        window.AppUI?.showToast?.(message, type, duration);
    }
</script>
@endsection
