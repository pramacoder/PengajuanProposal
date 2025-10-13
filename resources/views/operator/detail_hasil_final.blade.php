@extends('operator.layout')

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
                                    <p><strong>Dana Diajukan:</strong> Rp {{ number_format($proposal->dana_diajukan, 0, ',', '.') }}</p>
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
        <div class="col-12">
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
    </div>

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
                                <button class="btn btn-sm btn-outline-info me-2" 
                                        onclick="viewPDF('{{ route('operator.revisi.download', $revisi->id_revisi) }}', '{{ $revisi->nama_file }}')">
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

    <!-- PDF Viewer Section -->
    <div class="pdf-viewer-container">
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
    @else
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Belum ada file revisi yang dikumpulkan.</strong> Mahasiswa belum mengumpulkan file revisi proposal.
            </div>
        </div>
    </div>
    @endif

    <!-- Form Hasil Final -->
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
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Hasil Final Sudah Ditentukan</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-{{ $proposal->hasilFinal->status_final == 'lolos' ? 'success' : 'danger' }}">
                                            {{ $proposal->hasilFinal->status_final == 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                                        </span>
                                    </p>
                                    <p><strong>Nilai:</strong> 
                                        <span class="badge bg-primary fs-6">{{ number_format($proposal->hasilFinal->nilai, 2) }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Ditentukan pada:</strong> {{ \Carbon\Carbon::parse($proposal->hasilFinal->created_at)->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                            @if($proposal->hasilFinal->catatan_final)
                                <p><strong>Catatan:</strong> {{ $proposal->hasilFinal->catatan_final }}</p>
                            @endif
                        </div>
                        
                        <!-- Edit button -->
                        <button type="button" class="btn btn-warning" onclick="toggleEditForm()">
                            <i class="fas fa-edit me-2"></i>Edit Hasil Final
                        </button>
                    @endif

                    <!-- Form for input/update -->
                    <form id="hasilFinalForm" action="{{ route('operator.update.hasil.final') }}" method="POST" 
                          style="{{ $proposal->hasilFinal ? 'display: none;' : '' }}">
                        @csrf
                        <input type="hidden" name="proposal_id" value="{{ $proposal->id_proposal }}">
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required-field">Status Final</label>
                                    <select class="form-select" name="status_final" required>
                                        <option value="">Pilih status final</option>
                                        <option value="lolos" {{ $proposal->hasilFinal && $proposal->hasilFinal->status_final == 'lolos' ? 'selected' : '' }}>Lolos</option>
                                        <option value="tidak_lolos" {{ $proposal->hasilFinal && $proposal->hasilFinal->status_final == 'tidak_lolos' ? 'selected' : '' }}>Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required-field">Nilai (0-100)</label>
                                    <input type="number" class="form-control" name="nilai" 
                                           value="{{ $proposal->hasilFinal ? $proposal->hasilFinal->nilai : '' }}"
                                           min="0" max="100" step="0.01" required
                                           placeholder="Masukkan nilai 0-100">
                                    <div class="form-text">Nilai untuk perangkingan proposal (0.00 - 100.00)</div>
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
<script>
    // PDF viewer variables
    let currentPdfUrl = null;
    let currentFileName = null;

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

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Detail hasil final page loaded');
        
        // Initialize fullscreen functionality
        initializeFullscreen();
        
        // Initialize download functionality
        initializeDownload();
        
        // Pre-load first revision PDF if available
        @if($proposal->proposalRevisi->count() > 0)
            const firstRevisi = @json($proposal->proposalRevisi->first());
            if (firstRevisi) {
                console.log('Pre-loading first revision PDF:', firstRevisi.nama_file);
                // Pre-load the PDF URL for faster display
                const link = document.createElement('link');
                link.rel = 'prefetch';
                link.href = '{{ route('operator.revisi.download', $proposal->proposalRevisi->first()->id_revisi) }}';
                document.head.appendChild(link);
            }
        @endif
        
        // Show success/error messages
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
    });

    // Form submission
    document.getElementById('hasilFinalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        submitBtn.disabled = true;
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Hasil final berhasil diperbarui!', 'success');
                // Optionally reload the page or update the UI
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast('Gagal memperbarui hasil final: ' + (data.message || 'Terjadi kesalahan'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat menyimpan hasil final: ' + error.message, 'error');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
</script>
@endsection
