@section('styles')
<style>
    /* Compact scale for reviewer pages to emulate 80% zoom at 100% */
    .reviewer-compact { font-size: 0.875rem; }
    .reviewer-compact h1, .reviewer-compact .h1 { font-size: 1.5rem; }
    .reviewer-compact h2, .reviewer-compact .h2 { font-size: 1.25rem; }
    .reviewer-compact h3, .reviewer-compact .h3 { font-size: 1.1rem; }
    .reviewer-compact .btn { padding: 0.35rem 0.6rem; font-size: 0.85rem; border-radius: 6px; }
    .reviewer-compact .badge { padding: 0.3rem 0.5rem; font-size: 0.7rem; }
    .reviewer-compact .form-select, .reviewer-compact .form-control { padding: 0.35rem 0.6rem; font-size: 0.875rem; }
    .reviewer-compact .card-body { padding: 0.9rem; }
    .reviewer-compact .card-header { padding: 0.7rem 0.9rem; }
    .reviewer-compact table.table th,
    .reviewer-compact table.table td { padding: 0.5rem 0.6rem; }
    .reviewer-compact .alert { padding: 0.6rem 0.8rem; font-size: 0.875rem; }
    /* PDF Viewer Section Styles */
    .pdf-viewer-section {
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border-radius: 12px;
        overflow: hidden;
    }
    
    .pdf-viewer-section .card-header {
        background: #8b3a3a;
        color: white;
        border-bottom: none;
        padding: 1rem 1.5rem;
    }
    
    .pdf-viewer-section .pdf-controls {
        display: flex;
        gap: 0.5rem;
    }
    
    .pdf-container-full {
        position: relative;
        background: #f8f9fa;
        min-height: 80vh;
    }
    
    .pdf-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        z-index: 10;
    }
    
    .pdf-loading .spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #8b3a3a;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }
    
    .pdf-loading p {
        color: #8b3a3a;
        font-weight: 500;
        margin: 0;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Fullscreen styles */
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
    
    .pdf-viewer-section.fullscreen .pdf-container-full {
        height: calc(100vh - 80px);
    }
    
    .pdf-viewer-section.fullscreen #pdfViewer {
        height: calc(100vh - 80px) !important;
    }
    
    /* Enhanced Card Styles */
    .card-custom {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    
    .card-custom:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
    
    .card-header-custom {
        background: linear-gradient(135deg, #8b3a3a 0%, #6d2d2d 100%);
        color: white;
        border-bottom: none;
        padding: 1rem 1.5rem;
        font-weight: 600;
    }
    
    .card-header-custom h6 {
        color: white;
        margin: 0;
        font-size: 1rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    /* Table Enhancement */
    .table-borderless td {
        padding: 0.75rem 0;
        border: none;
        vertical-align: middle;
    }
    
    .table-borderless td:first-child {
        font-weight: 600;
        color: #495057;
        width: 40%;
    }
    
    .table-borderless td:last-child {
        color: #6c757d;
    }
    
    /* Badge Enhancement */
    .badge {
        font-size: 0.8rem;
        padding: 0.5rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
    }
    
    /* Button Enhancement */
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #8b3a3a 0%, #6d2d2d 100%);
        border-color: #8b3a3a;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #6d2d2d 0%, #8b3a3a 100%);
        border-color: #6d2d2d;
    }
    
    /* Form Enhancement */
    .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #8b3a3a;
        box-shadow: 0 0 0 0.2rem rgba(139,58,58,0.25);
    }
    
    .form-check-input:checked {
        background-color: #8b3a3a;
        border-color: #8b3a3a;
    }
    
    /* Enhanced Error/Success Messages */
    .form-text.text-danger {
        font-weight: 500;
        padding: 0.5rem;
        border-radius: 4px;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        margin-top: 0.5rem;
    }
    
    .form-text.text-success {
        font-weight: 500;
        padding: 0.5rem;
        border-radius: 4px;
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        margin-top: 0.5rem;
    }
    
    /* Character counter animation */
    .form-control:focus + .form-text {
        transform: translateY(-2px);
    }
    
    /* Loading state for submit button */
    .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }
    
    /* Status Icons Enhancement */
    .fa-check-circle {
        color: #28a745;
    }
    
    .fa-clock {
        color: #ffc107;
    }
    
    .fa-plus-circle {
        color: #17a2b8;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .pdf-viewer-section .card-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }
        
        .pdf-viewer-section .pdf-controls {
            width: 100%;
            justify-content: center;
        }
        
        .pdf-container-full {
            min-height: 60vh;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
</style>
@endsection

@extends('mainlayout.app')

@section('title', 'Detail Proposal - Review Substantif')

@section('content')
<div class="container-fluid reviewer-compact">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('reviewer.dashboard')],
        ['label' => 'Review Substantif', 'url' => route('reviewer.review.substantif')],
        ['label' => 'Detail Proposal', 'active' => true],
    ]" />

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clipboard-check me-2"></i>Review Substantif
            </h1>
            <p class="text-muted"></p>
        </div>
        
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-secondary me-2" onclick="goBackToReviewerDashboard()">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </button>
            <span class="badge bg-success fs-6">Review Substantif</span>
        </div>
    </div>

    <!-- PDF Viewer Section - Full Width -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom pdf-viewer-section">
                <div class="card-header card-header-custom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-file-pdf me-2"></i>Dokumen Proposal - {{ $proposal->judul }}
                        </h5>
                        <div class="pdf-controls">
                            @if($proposal->dokumen && $proposal->dokumen->path_file)
                                <a href="{{ route('file.serve', ['path' => $proposal->dokumen->path_file], false) }}" 
                                   class="btn btn-sm btn-primary me-2" target="_blank">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="toggleFullscreen()">
                                    <i class="fas fa-expand me-1"></i>Fullscreen
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info" onclick="refreshPDFViewer()">
                                    <i class="fas fa-redo me-1"></i>Refresh
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($proposal->dokumen && $proposal->dokumen->path_file)
                        <div class="pdf-container-full">
                            <iframe 
                                id="pdfViewer"
                                src="{{ route('file.serve', ['path' => $proposal->dokumen->path_file], false) }}"
                                style="width: 100%; height: 80vh; border: none; border-radius: 8px;"
                                frameborder="0"
                                allowfullscreen>
                            </iframe>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-pdf fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Dokumen proposal tidak tersedia</h5>
                            <p class="text-muted">Silakan hubungi operator untuk informasi lebih lanjut</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informasi Proposal -->
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informasi Proposal
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="py-2 px-2"><strong>Judul:</strong></td>
                            <td class="py-2 px-2">{{ $proposal->judul_proposal }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2"><strong>Skim:</strong></td>
                            <td class="py-2 px-2"><span class="badge bg-primary">{{ $proposal->skim }}</span></td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2"><strong>Tahun:</strong></td>
                            <td class="py-2 px-2">{{ $proposal->tahun ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2"><strong>Status:</strong></td>
                            <td class="py-2 px-2">
                                @php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch($proposal->status) {
                                        case 'draft':
                                            $statusClass = 'bg-secondary';
                                            $statusText = 'Draft';
                                            break;
                                        case 'submitted':
                                            $statusClass = 'bg-info';
                                            $statusText = 'Submitted';
                                            break;
                                        case 'validated':
                                            $statusClass = 'bg-success';
                                            $statusText = 'Validated';
                                            break;
                                        case 'review_administratif':
                                            $statusClass = 'bg-warning';
                                            $statusText = 'Review Administratif';
                                            break;
                                        case 'review_substantif':
                                            $statusClass = 'bg-info';
                                            $statusText = 'Review Substantif';
                                            break;
                                        case 'review_completed':
                                            $statusClass = 'bg-warning';
                                            $statusText = 'Review Completed';
                                            break;
                                        case 'revisi':
                                            $statusClass = 'bg-info';
                                            $statusText = 'Revisi';
                                            break;
                                        case 'finalized':
                                            $statusClass = 'bg-primary';
                                            $statusText = 'Finalized';
                                            break;
                                        default:
                                            $statusClass = 'bg-secondary';
                                            $statusText = $proposal->status;
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Informasi Mahasiswa -->
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-user-graduate me-2"></i>Informasi Mahasiswa
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="py-2 px-2"><strong>Nama:</strong></td>
                            <td class="py-2 px-2">{{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2"><strong>NIM:</strong></td>
                            <td class="py-2 px-2">{{ $proposal->mahasiswa->nim ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2"><strong>Email:</strong></td>
                            <td class="py-2 px-2">{{ $proposal->mahasiswa->email_mhs ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2"><strong>Program Studi:</strong></td>
                            <td class="py-2 px-2">{{ $proposal->mahasiswa->prodi_mhs ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2"><strong>Fakultas:</strong></td>
                            <td class="py-2 px-2">{{ $proposal->mahasiswa->fakultas_mhs ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Informasi Dosen Pendamping -->
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Dosen Pendamping
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="py-2 px-2"><strong>Nama:</strong></td>
                            <td class="py-2 px-2">{{ $proposal->dosen->nama_dosen ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2"><strong>NUPTK:</strong></td>
                            <td class="py-2 px-2">{{ $proposal->dosen->nuptk ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-2"><strong>Email:</strong></td>
                            <td class="py-2 px-2">{{ $proposal->dosen->email_dosen ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Status Review Sebelumnya -->
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>Status Review Sebelumnya
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $existingReview = $proposal->nilaiSubstantif->where('id_reviewer', auth()->user()->id_reviewer)->first();
                        $isReviewCompleted = $existingReview && !empty($existingReview->note_substantif);
                    @endphp
                    
                    @if($isReviewCompleted)
                        <div class="text-center">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-success mb-0"><strong>Review Selesai</strong></p>
                            <small class="text-muted">Terakhir diupdate: {{ $existingReview->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    @elseif($existingReview)
                        <div class="text-center">
                            <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                            <p class="text-warning mb-0"><strong>Review Belum Selesai</strong></p>
                            <small class="text-muted">Silakan lengkapi review Anda</small>
                        </div>
                    @else
                        <div class="text-center">
                            <i class="fas fa-plus-circle fa-2x text-info mb-2"></i>
                            <p class="text-info mb-0"><strong>Review Baru</strong></p>
                            <small class="text-muted">Akan dibuat record baru</small>
                        </div>
                    @endif
                </div>
            </div>

            @if(isset($isSeleksiMode) && $isSeleksiMode)
            <!-- Riwayat Review Tahap Pertama (Khusus Seleksi) -->
            <div class="card card-custom mt-3 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Review Tahap 1</h6>
                </div>
                <div class="card-body">
                    @php
                        $review1 = $proposal->nilaiSubstantif->where('jenis_review', 'pertama')->where('id_reviewer', $proposal->id_reviewer_substantif_1)->first();
                        $review2 = $proposal->nilaiSubstantif->where('jenis_review', 'pertama')->where('id_reviewer', $proposal->id_reviewer_substantif_2)->first();
                    @endphp
                    
                    <div class="mb-3">
                        <strong class="d-block text-primary">Reviewer 1</strong>
                        @if($review1)
                            <div>Total Nilai: <span class="badge bg-primary">{{ $review1->total_nilai }}</span></div>
                            <div class="small bg-light p-2 mt-1 rounded text-muted">{{ $review1->catatan ?? 'Tidak ada catatan' }}</div>
                        @else
                            <div class="small text-muted">Belum ada nilai</div>
                        @endif
                    </div>
                    
                    <div>
                        <strong class="d-block text-success">Reviewer 2</strong>
                        @if($review2)
                            <div>Total Nilai: <span class="badge bg-success">{{ $review2->total_nilai }}</span></div>
                            <div class="small bg-light p-2 mt-1 rounded text-muted">{{ $review2->catatan ?? 'Tidak ada catatan' }}</div>
                        @else
                            <div class="small text-muted">Belum ada nilai</div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Form Review Section -->
        <div class="col-lg-8">

            <!-- Form Review Substantif -->
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Form Review Substantif
                        <span class="badge bg-success ms-2">Review Substantif</span>
                    </h6>
                </div>
                <div class="card-body">
                    <form id="formReviewSubstantif" method="POST" action="{{ request('seleksi') ? route('reviewer.submit.review.substantif.seleksi', $proposal->id_proposal) : route('reviewer.submit.review.substantif', $proposal->id_proposal) }}">
                        @csrf
                        <input type="hidden" name="skim" value="{{ $proposal->skim }}">
                        
                        <!-- Status Review -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Status Review</label>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-success me-2">Substantif</span>
                                    <small class="text-muted">Review substantif proposal</small>
                                </div>
                            </div>
                        </div>

                        @php
                            // Debug: Cek apakah $criteria ter-pass dari controller
                            $criteriaItems = $criteria ?? [];
                            $isSeleksiView = request('seleksi') == '1';
                            $existingReview = $proposal->nilaiSubstantif
                                ->where('id_reviewer', auth()->id())
                                ->where('jenis_review', $isSeleksiView ? 'seleksi' : 'pertama')
                                ->first();
                            
                            // Ambil total_nilai dan nilai_akhir dari database jika ada
                            $existingTotalNilai = $existingReview ? ($existingReview->total_nilai ?? 0) : 0;
                            $existingNilaiAkhir = $existingReview ? ($existingReview->nilai_akhir ?? 0) : 0;
                            
                            // Hitung total skor dari existing skor untuk display
                            $existingTotalSkor = 0;
                            $existingSkorCount = 0;
                            
                            // Normalize existing skor untuk memastikan index numerik
                            $existingSkorRaw = $existingReview ? ($existingReview->skor_per_kriteria ?? []) : [];
                            $existingSkor = [];
                            if (!empty($existingSkorRaw) && is_array($existingSkorRaw)) {
                                foreach ($existingSkorRaw as $key => $value) {
                                    $index = (int) $key; // Convert string key ke integer
                                    $existingSkor[$index] = (float) $value; // Convert value ke float
                                    $existingTotalSkor += (float) $value;
                                    $existingSkorCount++;
                                }
                                ksort($existingSkor); // Sort by key
                            }
                            
                            // Hitung rata-rata skor jika ada
                            $existingAvgSkor = $existingSkorCount > 0 ? ($existingTotalSkor / $existingSkorCount) : 0;
                            
                            // Jika kriteria kosong atau null, coba load ulang dari helper
                            if (empty($criteriaItems)) {
                                // Coba load langsung dari helper dengan skim proposal
                                $criteriaItems = \App\Helpers\ProposalHelper::getSubstantifCriteria($proposal->skim);
                                
                                // Jika masih kosong, gunakan default
                                if (empty($criteriaItems)) {
                                    $criteriaItems = \App\Helpers\ProposalHelper::getSubstantifCriteria('default');
                                }
                            }
                            
                            // Debug info untuk melihat data yang tersimpan
                            \Log::info('View: Loading criteria for substantif review', [
                                'proposal_id' => $proposal->id_proposal,
                                'skim' => $proposal->skim,
                                'criteria_from_controller' => !empty($criteria),
                                'criteria_count' => count($criteriaItems),
                                'existing_review_id' => $existingReview ? $existingReview->id : null,
                                'existing_skor_raw' => $existingSkorRaw,
                                'existing_skor_normalized' => $existingSkor,
                                'existing_skor_count' => count($existingSkor),
                                'existing_skor_keys' => array_keys($existingSkor)
                            ]);
                        @endphp
                        
                        @if(config('app.debug'))
                            <!-- Debug Info -->
                            <div class="alert alert-info mb-3">
                                <small>
                                    <strong>Debug Info:</strong><br>
                                    Skim: {{ $proposal->skim }}<br>
                                    Criteria dari Controller: {{ !empty($criteria) ? 'Ada (' . count($criteria) . ' items)' : 'Kosong' }}<br>
                                    Criteria Items: {{ count($criteriaItems) }} items<br>
                                    @if(!empty($criteriaItems))
                                        Kriteria pertama: {{ $criteriaItems[0]['kriteria'] ?? 'N/A' }}
                                    @endif
                                </small>
                            </div>
                        @endif

                        <!-- Form Penilaian Substantif -->
                        @if(!$dynamicForm)
                            <div class="mb-4">
                                <label class="form-label fw-bold mb-3">Penilaian Substantif <span class="text-danger">*</span></label>
                                
                                @if(empty($criteriaItems))
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Kriteria penilaian untuk skim {{ $proposal->skim }} belum tersedia.
                                    </div>
                                @else
                                    @php
                                        // Flatten struktur untuk perhitungan dan input
                                        $flattenedCriteria = [];
                                        $criteriaIndex = 0;
                                        $mainCriteriaNumber = 0;
                                        
                                        foreach($criteriaItems as $mainItem) {
                                            if (isset($mainItem['sub_kriteria']) && !empty($mainItem['sub_kriteria'])) {
                                                // Kriteria utama dengan sub-kriteria
                                                $mainCriteriaNumber++;
                                                
                                                // Tambahkan header kriteria utama (baris pertama dengan nomor)
                                                $flattenedCriteria[] = [
                                                    'main_number' => $mainCriteriaNumber,
                                                    'is_sub' => false,
                                                    'is_header' => true,
                                                    'kriteria' => $mainItem['kriteria'],
                                                    'bobot' => array_sum(array_column($mainItem['sub_kriteria'], 'bobot')), // Total bobot sub-kriteria
                                                    'index' => -1, // Header tidak punya input skor
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
                                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-success">
                                                <tr>
                                                    <th width="5%">No</th>
                                                    <th width="50%">Kriteria Penilaian</th>
                                                    <th width="10%" class="text-center">Bobot (%)</th>
                                                    <th width="15%" class="text-center">Skor (0-10)</th>
                                                    <th width="15%" class="text-center">Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $currentMainNumber = 0;
                                                @endphp
                                                @foreach($flattenedCriteria as $item)
                                                    @php
                                                        // Untuk header, tidak ada skor (index = -1)
                                                        if ($item['index'] >= 0) {
                                                            $skorValue = isset($existingSkor[$item['index']]) ? $existingSkor[$item['index']] : '';
                                                            $nilai = $skorValue ? ($item['bobot'] * $skorValue) : 0;
                                                        } else {
                                                            $skorValue = '';
                                                            $nilai = 0;
                                                        }
                                                        
                                                        // Tampilkan nomor untuk kriteria utama (header atau tanpa sub)
                                                        $showNumber = false;
                                                        if (!$item['is_sub']) {
                                                            $showNumber = true;
                                                            $currentMainNumber = $item['main_number'];
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
                                                                       class="form-control form-control-sm skor-input text-center" 
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
                                                                <span class="nilai-display fw-bold" data-index="{{ $item['index'] }}">
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
                                                        <span class="total-skor-display fw-bold">@if($existingAvgSkor > 0)@formatId($existingAvgSkor, 2)@else 0,00 @endif</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="total-nilai-display fw-bold">@if($existingTotalNilai > 0)@formatId($existingTotalNilai, 2)@else 0,00 @endif</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-end fw-bold">Nilai Akhir (Total / 10)</td>
                                                    <td class="text-center">
                                                        <span class="nilai-akhir-display fw-bold text-primary" style="font-size: 1.2em;">@if($existingNilaiAkhir > 0)@formatId($existingNilaiAkhir, 2)@else 0,00 @endif</span>
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
                        <div class="mb-3">
                            <label for="catatan" class="form-label fw-bold">Catatan Review Substantif <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="8" 
                                      placeholder="Berikan penilaian detail tentang kualitas konten, metodologi, kelayakan, dampak, dan saran perbaikan..." 
                                      required>{{ $existingReview->note_substantif ?? '' }}</textarea>
                            <div class="form-text">Jelaskan penilaian substantif dan saran perbaikan untuk mahasiswa</div>
                            <div class="form-text text-danger" id="errorCatatan" style="display: none;">
                                Catatan review substantif harus diisi minimal 50 karakter
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="fas fa-save me-1"></i>
                                <span id="submitText">Simpan Review Substantif</span>
                            </button>
                            
                            @if($existingReview)
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Review terakhir: {{ $existingReview->updated_at->format('d/m/Y H:i') }}
                                </small>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@php
    // Siapkan nilai dari database untuk JavaScript
    $initialTotalNilai = $existingReview ? ($existingReview->total_nilai ?? 0) : 0;
    $initialNilaiAkhir = $existingReview ? ($existingReview->nilai_akhir ?? 0) : 0;
    $initialAvgSkor = $existingSkorCount > 0 ? ($existingTotalSkor / $existingSkorCount) : 0;
    
    // Convert ke string untuk JavaScript
    $jsTotalNilai = number_format($initialTotalNilai, 2, '.', '');
    $jsNilaiAkhir = number_format($initialNilaiAkhir, 2, '.', '');
    $jsAvgSkor = number_format($initialAvgSkor, 2, '.', '');
@endphp
<script>
    // Nilai dari database untuk initial display
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

    // PDF Viewer Functions
    function hidePDFLoading() {
        const loading = document.getElementById('pdf-loading');
        const viewer = document.getElementById('pdfViewer');
        
        if (loading && viewer) {
            loading.style.display = 'none';
            viewer.style.display = 'block';
        }
    }

    function handlePDFError() {
        const loading = document.getElementById('pdf-loading');
        const viewer = document.getElementById('pdfViewer');
        
        if (loading) {
            loading.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5 class="text-warning">Gagal Memuat PDF</h5>
                    <p class="text-muted">Dokumen PDF tidak dapat dimuat. Silakan coba lagi atau download manual.</p>
                    <button class="btn btn-primary" onclick="refreshPDFViewer()">
                        <i class="fas fa-redo me-1"></i>Coba Lagi
                    </button>
                </div>
            `;
        }
        
        if (viewer) {
            viewer.style.display = 'none';
        }
    }

    function toggleFullscreen() {
        const pdfSection = document.querySelector('.pdf-viewer-section');
        const fullscreenBtn = document.querySelector('[onclick="toggleFullscreen()"]');
        
        if (pdfSection) {
            if (pdfSection.classList.contains('fullscreen')) {
                // Exit fullscreen
                pdfSection.classList.remove('fullscreen');
                fullscreenBtn.innerHTML = '<i class="fas fa-expand me-1"></i>Fullscreen';
                document.body.style.overflow = '';
            } else {
                // Enter fullscreen
                pdfSection.classList.add('fullscreen');
                fullscreenBtn.innerHTML = '<i class="fas fa-compress me-1"></i>Exit Fullscreen';
                document.body.style.overflow = 'hidden';
            }
        }
    }

    function refreshPDFViewer() {
        const pdfViewer = document.getElementById('pdfViewer');
        
        if (pdfViewer) {
            // Simple reload by changing src
            const currentSrc = pdfViewer.src;
            pdfViewer.src = currentSrc + '?t=' + Date.now();
        }
    }

    // Handle escape key to exit fullscreen
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const pdfSection = document.querySelector('.pdf-viewer-section.fullscreen');
            if (pdfSection) {
                toggleFullscreen();
            }
        }
    });

    // Form validation dan submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formReviewSubstantif');
        const catatanField = document.getElementById('catatan');
        const errorDiv = document.getElementById('errorCatatan');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        
        if (!form) {
            console.error('Form with ID "formReviewSubstantif" not found!');
            return;
        }
        
        // Update display dengan nilai dari database (jika ada nilai dari database)
        // Jika tidak ada nilai dari database, biarkan calculateNilai() yang menghitung
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
        
        // Hitung nilai setelah form ter-render (dengan delay untuk memastikan semua input ter-render)
        // Ini akan meng-override nilai dari database jika ada perubahan di input
        setTimeout(function() {
            console.log('Initial calculateNilai after form render');
            calculateNilai();
        }, 300);
        
        // Real-time validation untuk input field
        catatanField.addEventListener('input', function() {
            const value = this.value.trim();
            const minLength = 50;
            
            // Reset error display
            errorDiv.style.display = 'none';
            
            // Validasi real-time
            if (value.length > 0 && value.length < minLength) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = `⚠️ Teks yang Anda berikan di note kurang dari ${minLength} karakter. Saat ini: ${value.length} karakter, minimal: ${minLength} karakter.`;
                errorDiv.className = 'form-text text-danger';
                catatanField.classList.add('is-invalid');
            } else if (value.length >= minLength) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = `✓ Catatan sudah memenuhi syarat (${value.length}/${minLength} karakter)`;
                errorDiv.className = 'form-text text-success';
                catatanField.classList.remove('is-invalid');
                catatanField.classList.add('is-valid');
            } else {
                errorDiv.style.display = 'none';
                catatanField.classList.remove('is-invalid', 'is-valid');
            }
        });
        
        // Setup event listener untuk input skor
        setTimeout(function() {
            const skorInputs = document.querySelectorAll('.skor-input');
            
            console.log('Setting up skor inputs - Found:', skorInputs.length);
            
            // Hitung nilai awal jika ada data existing
            calculateNilai();
            
            // Hitung nilai setiap kali skor berubah dan validasi
            skorInputs.forEach(input => {
                // Validasi dan hitung saat input
                input.addEventListener('input', function() {
                    validateSkorInput(this);
                });
                
                // Hitung juga saat change
                input.addEventListener('change', function() {
                    validateSkorInput(this);
                });
                
                // Hitung juga saat blur untuk memastikan
                input.addEventListener('blur', function() {
                    validateSkorInput(this);
                });
            });
        }, 500);
        
        // Juga hitung saat window load untuk memastikan
        window.addEventListener('load', function() {
            setTimeout(function() {
                console.log('Window load - Recalculating nilai');
                calculateNilai();
            }, 300);
        });
        
        // Form submission handler
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Reset error messages
                errorDiv.style.display = 'none';
                
                // Get form data
                const catatan = catatanField.value.trim();
                
                // Validation
                let isValid = true;
                let errorMessage = '';
                
                // Validasi catatan
                if (!catatan) {
                    errorMessage = '⚠️ Catatan review substantif harus diisi. Silakan isi catatan review Anda.';
                    isValid = false;
                } else if (catatan.length < 50) {
                    errorMessage = `⚠️ Teks yang Anda berikan di note kurang dari 50 karakter. Saat ini: ${catatan.length} karakter, minimal: 50 karakter. Silakan lengkapi catatan review Anda.`;
                    isValid = false;
                }
                
                // Validasi skor - pastikan semua skor sudah diisi
                const skorInputs = document.querySelectorAll('.skor-input');
                let missingSkor = [];
                let validIndex = 0;
                if (skorInputs.length > 0) {
                    skorInputs.forEach((input) => {
                        // Skip header (index < 0 atau disabled)
                        if (input.disabled || input.hasAttribute('data-header') || parseInt(input.dataset.index) < 0) {
                            return;
                        }
                        
                        validIndex++;
                        const skor = parseFloat(input.value);
                        if (isNaN(skor) || skor < 0 || skor > 10) {
                            missingSkor.push(validIndex);
                        }
                    });
                    
                    if (missingSkor.length > 0) {
                        errorMessage = `Mohon isi semua skor dengan nilai 0-10. Kriteria yang belum diisi: ${missingSkor.join(', ')}`;
                        isValid = false;
                    }
                }
                
                if (!isValid) {
                    // Tampilkan error tanpa reload halaman
                    errorDiv.style.display = 'block';
                    errorDiv.textContent = errorMessage;
                    errorDiv.className = 'form-text text-danger';
                    
                    // Highlight field yang error
                    if (catatan.length < 50) {
                        catatanField.classList.add('is-invalid');
                        catatanField.focus();
                        catatanField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        // Jika error di skor, scroll ke tabel
                        const table = document.querySelector('.table-responsive');
                        if (table) {
                            table.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    }
                    
                    // Tampilkan toast error
                    if (typeof showToast === 'function') {
                        showToast(errorMessage, 'error');
                    } else {
                        alert(errorMessage);
                    }
                    
                    // Re-enable submit button agar user bisa coba lagi
                    submitBtn.disabled = false;
                    submitText.innerHTML = 'Simpan Review Substantif';
                    
                    return;
                }
                
                // Disable submit button untuk mencegah double submission
                submitBtn.disabled = true;
                submitText.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
                
                // Kumpulkan data skor secara manual untuk memastikan format benar
                const skorData = {};
                const allSkorInputs = document.querySelectorAll('.skor-input');
                allSkorInputs.forEach(input => {
                    // Skip header (index < 0 atau disabled)
                    if (input.disabled || input.hasAttribute('data-header') || parseInt(input.dataset.index) < 0) {
                        return;
                    }
                    
                    const index = parseInt(input.dataset.index);
                    const value = parseFloat(input.value);
                    
                    // Validasi skor 0-10
                    if (isNaN(value) || value < 0 || value > 10) {
                        console.error(`Invalid skor at index ${index}: ${input.value}`);
                        return;
                    }
                    
                    skorData[index] = value;
                });
                
                // Kumpulkan semua data form
                const formData = new FormData(form);
                formData.set('catatan', catatanField.value);
                formData.set('skim', '{{ $proposal->skim }}');
                formData.set('_token', '{{ csrf_token() }}');
                
                // Append skor sebagai array
                Object.keys(skorData).forEach(key => {
                    formData.append(`skor[${key}]`, skorData[key]);
                });
                
                // Debug: Log data yang akan dikirim
                console.log('Form data to be submitted:', {
                    catatan: catatanField.value,
                    skim: '{{ $proposal->skim }}',
                    skor: skorData,
                    skor_count: Object.keys(skorData).length
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
                    // Pastikan response adalah JSON
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
                        // Tampilkan success alert
                        if (typeof showToast === 'function') {
                            showToast('✅ ' + data.message, 'success', 5000);
                        } else {
                            alert('✅ Berhasil: ' + data.message);
                        }
                        
                        // Update status display
                        errorDiv.style.display = 'block';
                        errorDiv.textContent = '✅ Review berhasil disimpan!';
                        errorDiv.className = 'form-text text-success';
                        
                        // Reload halaman setelah delay untuk update status
                        // Gunakan window.location.href untuk memastikan data ter-load dengan benar
                        setTimeout(() => {
                            window.location.href = window.location.href;
                        }, 1500);
                    } else {
                        // Handle error response dari server
                        const errorMessage = data.message || 'Terjadi kesalahan saat menyimpan review';
                        
                        errorDiv.style.display = 'block';
                        errorDiv.textContent = errorMessage;
                        errorDiv.className = 'form-text text-danger';
                        
                        if (typeof showToast === 'function') {
                            showToast('❌ ' + errorMessage, 'error');
                        } else {
                            alert('❌ Error: ' + errorMessage);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error during submission:', error);
                    
                    // Handle network atau parsing errors
                    let errorMessage = 'Terjadi kesalahan saat menyimpan review';
                    
                    if (error.message.includes('HTTP')) {
                        errorMessage = 'Server error: ' + error.message;
                    } else if (error.message.includes('response yang tidak valid')) {
                        errorMessage = 'Server mengembalikan response yang tidak valid. Silakan coba lagi.';
                    } else {
                        errorMessage = 'Gagal memproses response dari server. Silakan coba lagi.';
                    }
                    
                    errorDiv.style.display = 'block';
                    errorDiv.textContent = errorMessage;
                    errorDiv.className = 'form-text text-danger';
                    
                    if (typeof showToast === 'function') {
                        showToast('❌ ' + errorMessage, 'error');
                    } else {
                        alert('❌ Error: ' + errorMessage);
                    }
                })
                .finally(() => {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitText.innerHTML = 'Simpan Review Substantif';
                });
            });
        }
    });
    
    // Validasi input skor 0-10 (dipindahkan keluar dari DOMContentLoaded agar bisa diakses global)
    function validateSkorInput(input) {
        const value = parseFloat(input.value);
        if (isNaN(value)) {
            input.setCustomValidity('Skor harus berupa angka');
            return;
        }
        
        if (value < 0) {
            input.value = 0;
            input.setCustomValidity('');
        } else if (value > 10) {
            input.value = 10;
            input.setCustomValidity('');
        } else {
            input.setCustomValidity('');
        }
        
        // Hitung ulang nilai setelah validasi
        calculateNilai();
    }
    
    // Calculate nilai real-time
    function calculateNilai() {
        const skorInputs = document.querySelectorAll('.skor-input');
        let totalNilai = 0;
        let totalSkor = 0;
        let count = 0;
        
        console.log('=== calculateNilai() called ===');
        console.log('Found skor inputs:', skorInputs.length);
        
        // Array untuk menyimpan semua nilai per kriteria untuk debugging
        const nilaiArray = [];
        
        if (skorInputs.length === 0) {
            console.warn('No skor inputs found!');
            return;
        }
        
        skorInputs.forEach((input, idx) => {
            // Skip jika input disabled atau tidak valid
            if (input.disabled || input.hasAttribute('data-header')) {
                console.log(`Skipping input ${idx}: disabled or header`);
                return;
            }
            
            const skor = parseFloat(input.value) || 0;
            const bobot = parseFloat(input.dataset.bobot) || 0;
            const index = parseInt(input.dataset.index);
            
            // Skip jika index negatif atau NaN (header)
            if (isNaN(index) || index < 0) {
                console.log(`Skipping input ${idx}: invalid index (${index})`);
                return;
            }
            
            // Hitung nilai per kriteria: Nilai = Bobot × Skor
            const nilai = bobot * skor;
            totalNilai += nilai;
            totalSkor += skor;
            count++;
            
            console.log(`Input ${idx}: Index=${index}, Bobot=${bobot}, Skor=${skor}, Nilai=${nilai.toFixed(2)}`);
            
            // Simpan nilai ke array untuk debugging
            nilaiArray.push({
                index: index,
                bobot: bobot,
                skor: skor,
                nilai: nilai
            });
            
            // Update nilai display per kriteria
            const nilaiDisplay = document.querySelector(`.nilai-display[data-index="${index}"]`);
            if (nilaiDisplay) {
                nilaiDisplay.textContent = nilai.toFixed(2);
            } else {
                console.warn(`Nilai display not found for index ${index}`);
            }
        });
        
        console.log('Total calculated:', {
            totalNilai: totalNilai.toFixed(2),
            totalSkor: totalSkor.toFixed(2),
            count: count,
            nilaiArray: nilaiArray
        });
        
        // Update total nilai (jumlahkan semua nilai di kolom nilai)
        const totalNilaiDisplay = document.querySelector('.total-nilai-display');
        if (totalNilaiDisplay) {
            // Total nilai adalah jumlah dari semua nilai per kriteria
            totalNilaiDisplay.textContent = totalNilai.toFixed(2);
            console.log('Updated total-nilai-display:', totalNilai.toFixed(2));
        } else {
            console.warn('total-nilai-display element not found!');
        }
        
        // Update total skor (rata-rata dari semua skor)
        const totalSkorDisplay = document.querySelector('.total-skor-display');
        if (totalSkorDisplay && count > 0) {
            const avgSkor = (totalSkor / count).toFixed(2);
            totalSkorDisplay.textContent = avgSkor;
            console.log('Updated total-skor-display:', avgSkor);
        } else if (totalSkorDisplay) {
            totalSkorDisplay.textContent = '0.00';
        } else {
            console.warn('total-skor-display element not found!');
        }
        
        // Hitung nilai akhir: Total Nilai / 10
        const nilaiAkhir = totalNilai / 10;
        const nilaiAkhirDisplay = document.querySelector('.nilai-akhir-display');
        if (nilaiAkhirDisplay) {
            nilaiAkhirDisplay.textContent = nilaiAkhir.toFixed(2);
            console.log('Updated nilai-akhir-display:', nilaiAkhir.toFixed(2));
        } else {
            console.warn('nilai-akhir-display element not found!');
        }
        
        // Debug log untuk verifikasi (hanya di console, tidak tampil di UI)
        if (count > 0) {
            console.log('Calculate Nilai:', {
                totalNilai: totalNilai.toFixed(2),
                totalSkor: (totalSkor / count).toFixed(2),
                count: count,
                nilaiAkhir: nilaiAkhir.toFixed(2),
                detail: nilaiArray.map(n => `Index ${n.index}: ${n.bobot} × ${n.skor} = ${n.nilai.toFixed(2)}`)
            });
        }
    }
    

    function toggleFullscreen() {
        const pdfViewer = document.getElementById('pdfViewer');
        const container = pdfViewer.parentElement;
        
        if (!document.fullscreenElement) {
            if (container.requestFullscreen) {
                container.requestFullscreen();
            } else if (container.webkitRequestFullscreen) {
                container.webkitRequestFullscreen();
            } else if (container.msRequestFullscreen) {
                container.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    }

    function refreshPDFViewer() {
        const pdfViewer = document.getElementById('pdfViewer');
        if (pdfViewer) {
            const currentSrc = pdfViewer.src;
            pdfViewer.src = '';
            setTimeout(() => {
                pdfViewer.src = currentSrc;
            }, 100);
        }
    }

    // Responsive PDF viewer
    function resizePDFViewer() {
        const pdfViewer = document.getElementById('pdfViewer');
        if (pdfViewer) {
            const container = pdfViewer.parentElement;
            const containerWidth = container.offsetWidth;
            
            if (containerWidth < 768) {
                pdfViewer.style.height = '400px';
            } else {
                pdfViewer.style.height = '600px';
            }
        }
    }

    // Event listeners
    window.addEventListener('resize', resizePDFViewer);
    document.addEventListener('DOMContentLoaded', resizePDFViewer);
</script>
@endpush
@endsection