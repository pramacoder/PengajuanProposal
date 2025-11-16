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

@section('title', 'Detail Proposal - Review Administratif')

@section('content')
<div class="container-fluid reviewer-compact">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clipboard-check me-2"></i>Review Administratif
            </h1>
            <p class="text-muted"></p>
        </div>
        
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-secondary me-2" onclick="goBackToReviewerDashboard()">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </button>
            <span class="badge bg-warning fs-6">Review Administratif</span>
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
                                <a href="{{ asset('storage/' . $proposal->dokumen->path_file) }}" 
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
                                src="{{ asset('storage/' . $proposal->dokumen->path_file) }}"
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
                        </tr>
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
                        $existingReview = $proposal->nilaiAdministratif->where('id_reviewer', auth()->user()->id_reviewer)->first();
                        $isReviewCompleted = $existingReview && $existingReview->note_administratif;
                    @endphp
                    
                    @if($isReviewCompleted)
                        <div class="text-center">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-success mb-0"><strong>Review Sudah Selesai</strong></p>
                            <small class="text-muted">Data akan diupdate</small>
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
        </div>

        <!-- Form Review Section -->
        <div class="col-lg-8">

            <!-- Form Review Administratif -->
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Form Review Administratif
                        <span class="badge bg-warning ms-2">Review Administratif</span>
                    </h6>
                </div>
                <div class="card-body">
                    <form id="formReviewAdministratif" method="POST" action="{{ route('reviewer.submit.review.administratif', $proposal->id_proposal) }}">
                        @csrf
                        
                        <!-- Status Review -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Status Review</label>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-warning me-2">Administratif</span>
                                    <small class="text-muted">Review administratif proposal</small>
                                </div>
                            </div>
                        </div>

                        <!-- Kesalahan Administratif -->
                        <div class="mb-3">
                            <label class="form-label fw-bold d-block">Kesalahan Administratif yang Ditemukan <span class="text-danger">*</span></label>
                            <label class="form-label fw-bold text-danger d-block small" style="font-size: 0.85rem; margin-top: 0.5rem;">Pastikan anda mencentang hanya kesalahan yang sesuai dengan proposal<span class="text-danger">*</span></label>
                            @php
                                // Pastikan checklist tersedia, jika tidak gunakan default
                                $checklistItems = $checklist ?? [];
                                $existingChecklist = $existingReview ? ($existingReview->checklist ?? []) : [];
                                
                                // Jika checklist kosong atau tidak ada, gunakan default
                                if (empty($checklistItems)) {
                                    $checklistItems = \App\Helpers\ProposalHelper::getReviewChecklist('default');
                                }
                                
                                // Collect all items from checklist untuk validasi
                                $allChecklistItems = [];
                                foreach ($checklistItems as $kategori => $items) {
                                    if (is_array($items)) {
                                        $allChecklistItems = array_merge($allChecklistItems, $items);
                                    }
                                }
                                
                                // Find items in existing review that are not in new checklist (backward compatibility)
                                $orphanedItems = [];
                                if (!empty($existingChecklist) && is_array($existingChecklist)) {
                                    foreach ($existingChecklist as $item) {
                                        if (!in_array($item, $allChecklistItems)) {
                                            $orphanedItems[] = $item;
                                        }
                                    }
                                }
                            @endphp
                            
                            @if(empty($checklistItems))
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Checklist untuk skim {{ $proposal->skim }} belum tersedia. Menggunakan checklist umum.
                                </div>
                            @endif
                            
                            @foreach($checklistItems as $kategori => $items)
                                @if(is_array($items) && count($items) > 0)
                                    <div class="checklist-category mb-4">
                                        <div class="card border-primary mb-3">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-list-check me-2"></i>{{ $kategori }}
                                                    <span class="badge bg-light text-primary ms-2" id="counter-{{ md5($kategori) }}">
                                                        0 / {{ count($items) }}
                                                    </span>
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @php
                                                        $itemsPerColumn = ceil(count($items) / 2);
                                                        $itemsArray = array_values($items);
                                                    @endphp
                                                    
                                                    @for($i = 0; $i < 2; $i++)
                                                        <div class="col-md-6">
                                                            @for($j = $i * $itemsPerColumn; $j < min(($i + 1) * $itemsPerColumn, count($itemsArray)); $j++)
                                                                @php
                                                                    $item = $itemsArray[$j];
                                                                    $itemId = 'error_' . md5($kategori . '_' . $item);
                                                                    $isChecked = in_array($item, $existingChecklist);
                                                                @endphp
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input checklist-item" 
                                                                           type="checkbox" 
                                                                           name="kesalahan_administratif[]" 
                                                                           value="{{ $item }}" 
                                                                           id="{{ $itemId }}"
                                                                           data-category="{{ md5($kategori) }}"
                                                                           {{ $isChecked ? 'checked' : '' }}>
                                                                    <label class="form-check-label" for="{{ $itemId }}">
                                                                        {{ $item }}
                                                                    </label>
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            
                            {{-- Tampilkan item dari review lama yang tidak ada di checklist baru (backward compatibility) --}}
                            @if(!empty($orphanedItems))
                                <div class="checklist-category mb-4">
                                    <div class="card border-warning mb-3">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0">
                                                <i class="fas fa-exclamation-triangle me-2"></i>Item dari Review Sebelumnya
                                                <span class="badge bg-light text-warning ms-2">
                                                    {{ count($orphanedItems) }} item
                                                </span>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info mb-3">
                                                <small>
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Item berikut berasal dari review sebelumnya dan tidak ada di checklist baru untuk skim ini.
                                                </small>
                                            </div>
                                            <div class="row">
                                                @php
                                                    $orphanedPerColumn = ceil(count($orphanedItems) / 2);
                                                @endphp
                                                @for($i = 0; $i < 2; $i++)
                                                    <div class="col-md-6">
                                                        @for($j = $i * $orphanedPerColumn; $j < min(($i + 1) * $orphanedPerColumn, count($orphanedItems)); $j++)
                                                            @php
                                                                $item = $orphanedItems[$j];
                                                                $itemId = 'error_orphaned_' . md5($item);
                                                            @endphp
                                                            <div class="form-check mb-2">
                                                                <input class="form-check-input checklist-item" 
                                                                       type="checkbox" 
                                                                       name="kesalahan_administratif[]" 
                                                                       value="{{ $item }}" 
                                                                       id="{{ $itemId }}"
                                                                       checked>
                                                                <label class="form-check-label" for="{{ $itemId }}">
                                                                    {{ $item }}
                                                                </label>
                                                            </div>
                                                        @endfor
                                                    </div>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="form-text text-danger" id="errorKesalahan" style="display: none;">
                                Pilih minimal satu kesalahan administratif
                            </div>
                        </div>

                        <!-- Catatan Review -->
                        <div class="mb-3">
                            <label for="catatan" class="form-label fw-bold">Catatan Review <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="6" 
                                      placeholder="Berikan catatan detail tentang review administratif, temuan kesalahan, dan saran perbaikan untuk mahasiswa..." 
                                      required>{{ $existingReview->note_administratif ?? '' }}</textarea>
                            <div class="form-text">Jelaskan temuan dan saran perbaikan untuk mahasiswa</div>
                            <div class="form-text text-danger" id="errorCatatan" style="display: none;">
                                Catatan review harus diisi
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-warning" id="submitBtn">
                                <i class="fas fa-save me-1"></i>
                                <span id="submitText">Simpan Review Administratif</span>
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
<script>
    function goBackToReviewerDashboard() {
        // Jika ada history sebelumnya dan bukan halaman ini sendiri, gunakan back
        if (window.history.length > 1 && document.referrer && document.referrer !== window.location.href) {
            window.history.back();
            return;
        }
        // Fallback ke dashboard reviewer
        window.location.href = "{{ route('reviewer.dashboard') }}";
    }

    // Success Modal for AJAX submissions
    function showSuccessModal(message, backUrl) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-check-circle me-2"></i>Berhasil
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">${message || 'Review berhasil disimpan.'}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <a href="${backUrl || window.location.href}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali ke Detail
                        </a>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
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

    // Update counter per kategori
    function updateCategoryCounters() {
        const checkboxes = document.querySelectorAll('.checklist-item');
        const categoryCounts = {};
        
        // Initialize counters
        checkboxes.forEach(checkbox => {
            const category = checkbox.getAttribute('data-category');
            if (!categoryCounts[category]) {
                categoryCounts[category] = { total: 0, checked: 0 };
            }
            categoryCounts[category].total++;
            if (checkbox.checked) {
                categoryCounts[category].checked++;
            }
        });
        
        // Update counter badges
        Object.keys(categoryCounts).forEach(category => {
            const counter = document.getElementById('counter-' + category);
            if (counter) {
                const { checked, total } = categoryCounts[category];
                counter.textContent = `${checked} / ${total}`;
                
                // Update badge color based on progress
                if (checked === 0) {
                    counter.className = 'badge bg-light text-primary ms-2';
                } else if (checked === total) {
                    counter.className = 'badge bg-success ms-2';
                } else {
                    counter.className = 'badge bg-warning text-dark ms-2';
                }
            }
        });
    }
    
    // Initialize counters on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateCategoryCounters();
        
        // Update counters when checkboxes change
        document.querySelectorAll('.checklist-item').forEach(checkbox => {
            checkbox.addEventListener('change', updateCategoryCounters);
        });
    });

    // Form validation dan submission
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formReviewAdministratif');
        
        if (!form) {
            console.error('Form with ID "formReviewAdministratif" not found!');
            return;
        }
        
        console.log('Form found, adding event listener...');
        
        form.addEventListener('submit', function(e) {
            console.log('Form submit event triggered');
            e.preventDefault();
            e.stopPropagation();
            
            console.log('Form submission started for administratif review');
        
        // Reset error messages
        document.getElementById('errorKesalahan').style.display = 'none';
        document.getElementById('errorCatatan').style.display = 'none';
        
        // Get form data
        const catatan = document.getElementById('catatan').value.trim();
        const kesalahanCheckboxes = document.querySelectorAll('input[name="kesalahan_administratif[]"]:checked');
        
        console.log('Form data:', {
            catatan: catatan,
            kesalahan_count: kesalahanCheckboxes.length,
            kesalahan_values: Array.from(kesalahanCheckboxes).map(cb => cb.value)
        });
        
        // Validation
        let isValid = true;
        
        if (!catatan) {
            document.getElementById('errorCatatan').style.display = 'block';
            isValid = false;
        }
        
        if (kesalahanCheckboxes.length === 0) {
            document.getElementById('errorKesalahan').style.display = 'block';
            isValid = false;
        }
        
        if (!isValid) {
            if (typeof showToast === 'function') {
                showToast('Mohon lengkapi semua field yang diperlukan', 'error');
            } else {
                alert('Mohon lengkapi semua field yang diperlukan');
            }
            return;
        }
        
        console.log('Form validation passed, submitting...');
        
        // Disable submit button
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        submitBtn.disabled = true;
        submitText.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
        
        // Submit form
        const formData = new FormData(this);
        
        console.log('Submitting to:', this.action);
        console.log('FormData contents:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            // Log response untuk debugging
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            // Cek content type untuk memastikan response adalah JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Response is not JSON');
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                // Tampilkan modal sukses dengan tombol kembali
                showSuccessModal(data.message, window.location.href);

                // Fallback auto-reload jika modal tidak tersedia
                setTimeout(() => {
                    if (document.querySelector('.modal.show') === null) {
                    window.location.reload();
                    }
                }, 2500);
            } else {
                // Handle error response
                const errorMessage = data.message || 'Terjadi kesalahan';
                if (typeof showToast === 'function') {
                    showToast(errorMessage, 'error');
                } else {
                    alert('Error: ' + errorMessage);
                }
            }
        })
        .catch(error => {
            console.error('Error during submission:', error);
            
            // Handle network atau parsing errors
            let errorMessage = 'Terjadi kesalahan saat menyimpan review';
            
            if (error.message.includes('HTTP error')) {
                errorMessage = 'Server error: ' + error.message;
            } else if (error.message.includes('Response is not JSON')) {
                errorMessage = 'Server mengembalikan response yang tidak valid. Silakan coba lagi.';
            } else if (error.message.includes('JSON')) {
                errorMessage = 'Gagal memproses response dari server. Silakan coba lagi.';
            }
            
            // Jangan tampilkan JSON raw di console untuk user
            console.log('User-friendly error message:', errorMessage);
            
            if (typeof showToast === 'function') {
                showToast(errorMessage, 'error');
            } else {
                alert('Error: ' + errorMessage);
            }
        })
        .finally(() => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitText.innerHTML = 'Simpan Review Administratif';
        });
        });
    });


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
