@section('styles')
<style>
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
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clipboard-check me-2"></i>Review Administratif
            </h1>
            <p class="text-muted">Review administratif proposal: {{ $proposal->judul_proposal }}</p>
        </div>
        
        <div class="d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
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
                                src="{{ Storage::url($proposal->dokumen->path_file) }}"
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
                            <td class="py-2 px-2"><strong>ID Proposal:</strong></td>
                            <td class="py-2 px-2">{{ $proposal->id_proposal }}</td>
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
                    <form id="formReviewAdministratif" method="POST" action="{{ route('reviewer.submit.review.administratif', $proposal->id_proposal) }}" onsubmit="return false;">
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
                            <label class="form-label fw-bold">Kesalahan Administratif yang Ditemukan <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kesalahan_administratif[]" 
                                               value="Dokumen tidak lengkap" id="error1" 
                                               {{ $existingReview && in_array('Dokumen tidak lengkap', $existingReview->checklist ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="error1">
                                            Dokumen tidak lengkap
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kesalahan_administratif[]" 
                                               value="Format dokumen tidak sesuai" id="error2"
                                               {{ $existingReview && in_array('Format dokumen tidak sesuai', $existingReview->checklist ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="error2">
                                            Format dokumen tidak sesuai
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kesalahan_administratif[]" 
                                               value="Data mahasiswa tidak lengkap" id="error3"
                                               {{ $existingReview && in_array('Data mahasiswa tidak lengkap', $existingReview->checklist ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="error3">
                                            Data mahasiswa tidak lengkap
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kesalahan_administratif[]" 
                                               value="Data dosen tidak lengkap" id="error4"
                                               {{ $existingReview && in_array('Data dosen tidak lengkap', $existingReview->checklist ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="error4">
                                            Data dosen tidak lengkap
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kesalahan_administratif[]" 
                                               value="Skim tidak sesuai" id="error5"
                                               {{ $existingReview && in_array('Skim tidak sesuai', $existingReview->checklist ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="error5">
                                            Skim tidak sesuai
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kesalahan_administratif[]" 
                                               value="Tanda tangan tidak lengkap" id="error7"
                                               {{ $existingReview && in_array('Tanda tangan tidak lengkap', $existingReview->checklist ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="error7">
                                            Tanda tangan tidak lengkap
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kesalahan_administratif[]" 
                                               value="Stempel/legitimasi tidak ada" id="error8"
                                               {{ $existingReview && in_array('Stempel/legitimasi tidak ada', $existingReview->checklist ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="error8">
                                            Stempel/legitimasi tidak ada
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kesalahan_administratif[]" 
                                               value="Salah penulisan identitas (nama/NIM/NIP)" id="error9"
                                               {{ $existingReview && in_array('Salah penulisan identitas (nama/NIM/NIP)', $existingReview->checklist ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="error9">
                                            Salah penulisan identitas (nama/NIM/NIP)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kesalahan_administratif[]" 
                                               value="Tanggal dokumen tidak ada" id="error10"
                                               {{ $existingReview && in_array('Tanggal dokumen tidak ada', $existingReview->checklist ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="error10">
                                            Tanggal dokumen tidak ada
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kesalahan_administratif[]" 
                                               value="Jumlah halaman tidak sesuai ketentuan" id="error11"
                                               {{ $existingReview && in_array('Jumlah halaman tidak sesuai ketentuan', $existingReview->checklist ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="error11">
                                            Jumlah halaman tidak sesuai ketentuan
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kesalahan_administratif[]" 
                                               value="File tidak terbaca atau rusak" id="error12"
                                               {{ $existingReview && in_array('File tidak terbaca atau rusak', $existingReview->checklist ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="error12">
                                            File tidak terbaca atau rusak
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kesalahan_administratif[]" 
                                               value="Dokumen tidak sesuai template" id="error13"
                                               {{ $existingReview && in_array('Dokumen tidak sesuai template', $existingReview->checklist ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="error13">
                                            Dokumen tidak sesuai template
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="kesalahan_administratif[]" 
                                               value="Lainnya" id="error6"
                                               {{ $existingReview && in_array('Lainnya', $existingReview->checklist ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="error6">
                                            Lainnya
                                        </label>
                                    </div>
                                </div>
                            </div>
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
                'Accept': 'application/json'
            }
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
                // Tampilkan toast success
                if (typeof showToast === 'function') {
                    showToast(data.message, 'success');
                } else {
                    // Fallback jika showToast tidak tersedia
                    alert('Berhasil: ' + data.message);
                }
                
                // Redirect setelah delay
                console.log('Redirecting in 2 seconds...');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
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
