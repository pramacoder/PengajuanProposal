@extends('dosen.layout')

@section('page_title', 'Detail Proposal - Validasi')

@section('dosen_content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>
                    Detail Proposal
                </h5>
                <a href="{{ route('dosen.pendamping.dashboard') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
            <div class="card-body">
                <!-- Success/Error Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Informasi Proposal -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Informasi Mahasiswa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nama:</strong> {{ $proposal->mahasiswa->nama_mhs }}</p>
                            <p><strong>NIM:</strong> {{ $proposal->mahasiswa->nim }}</p>
                            <p><strong>Prodi:</strong> {{ $proposal->mahasiswa->prodi_mhs }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fakultas:</strong> {{ $proposal->mahasiswa->fakultas_mhs }}</p>
                            <p><strong>Email:</strong> {{ $proposal->mahasiswa->email_mhs }}</p>
                            <p><strong>No. HP:</strong> {{ $proposal->mahasiswa->no_hp_mhs }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

                <!-- Informasi Proposal -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3">Informasi Proposal</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Judul:</strong></td>
                                <td>{{ $proposal->judul_proposal }}</td>
                            </tr>
                            <tr>
                                <td><strong>Skim:</strong></td>
                                <td>{{ $proposal->skim }}</td>
                            </tr>
                            <tr>
                                <td><strong>Ketua:</strong></td>
                                <td>{{ $proposal->mahasiswa->nama_mhs }}</td>
                            </tr>
                            <tr>
                                <td><strong>Prodi:</strong></td>
                                <td>{{ $proposal->mahasiswa->prodi->nama_prodi ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fakultas:</strong></td>
                                <td>{{ $proposal->mahasiswa->fakultas->nama_fakultas ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email Ketua:</strong></td>
                                <td>{{ $proposal->mahasiswa->email_mhs ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status Validasi:</strong></td>
                                <td>
                                    @if($proposal->status_validasi === 'valid')
                                        <span class="badge bg-success">Sudah Divalidasi</span>
                                    @elseif($proposal->status_validasi === 'tidak_valid')
                                        <span class="badge bg-danger">Ditolak</span>
                                    @else
                                        <span class="badge bg-warning">Belum Divalidasi</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status Proposal:</strong></td>
                                <td>
                                    @if($proposal->status === 'valid')
                                        <span class="badge bg-success">Valid</span>
                                    @elseif($proposal->status === 'tidak_valid')
                                        <span class="badge bg-danger">Tidak Valid</span>
                                    @elseif($proposal->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($proposal->status === 'submitted')
                                        <span class="badge bg-info">Submitted</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($proposal->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3">Dokumen</h6>
                        <div class="d-grid gap-2">
                            @if($proposal->dokumen && $proposal->dokumen->path_file)
                                <a href="{{ route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
                                   class="btn btn-outline-primary" target="_blank">
                                    <i class="fas fa-download me-2"></i>Download Proposal
                                </a>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    File proposal belum tersedia
                                </div>
                            @endif
                            
                            <!-- Lampiran tidak tersedia karena hanya ada 1 file per proposal -->
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                File lampiran tidak tersedia (sistem hanya mendukung 1 file per proposal)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PDF Viewer -->
                @if($proposal->dokumen && $proposal->dokumen->path_file)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-3">Preview Proposal</h6>
                            <div class="border rounded p-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="btn-group" role="group">
                                        <button id="fullscreenBtn" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-expand me-1"></i>Fullscreen
                                        </button>
                                        <a href="{{ route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
                                           class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </div>
                                    <span class="text-muted">PDF akan dimuat secara otomatis</span>
                                </div>
                                
                                <div id="pdfViewer" class="pdf-loading">
                                    <div class="spinner"></div>
                                    <!-- PDF iframe will be inserted here -->
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>PDF tidak tersedia:</strong> File proposal belum diupload atau tidak dapat diakses.
                    </div>
                @endif

                <!-- Form Validasi -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-3">Aksi Validasi</h6>
                        
                        @if($proposal->status_validasi === 'valid')
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Proposal sudah divalidasi!</strong> Status: {{ $proposal->status_validasi }}
                                @if($proposal->tanggal_validasi)
                                    <br><small>Tanggal validasi: {{ \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d F Y, H:i') }}</small>
                                @endif
                            </div>
                        @elseif($proposal->status_validasi === 'tidak_valid')
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                <strong>Proposal ditolak!</strong>
                                @if($proposal->catatan)
                                    <br><strong>Alasan:</strong> {{ $proposal->catatan }}
                                @endif
                                @if($proposal->tanggal_validasi)
                                    <br><small>Tanggal validasi: {{ \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d F Y, H:i') }}</small>
                                @endif
                            </div>
                        @else
                            <form action="{{ route('dosen.pendamping.proposal.validasi.submit', $proposal->id_proposal) }}" method="POST" id="validasiForm" onsubmit="return validateForm()">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-success btn-lg" onclick="submitValidasi('valid')">
                                                <i class="fas fa-check me-2"></i>Validasi
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-danger btn-lg" onclick="showRejectForm()">
                                                <i class="fas fa-times me-2"></i>Tolak
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Hidden input untuk action -->
                                <input type="hidden" name="action" id="actionInput" value="">
                                
                                <!-- Form Penolakan (Hidden by default) -->
                                <div id="rejectForm" class="mt-4" style="display: none;">
                                    <div class="alert alert-warning">
                                        <h6 class="alert-heading">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Alasan Penolakan
                                        </h6>
                                        <p class="mb-2">Berikan alasan penolakan yang jelas agar mahasiswa dapat melakukan perbaikan:</p>
                                        <textarea name="catatan" class="form-control" rows="4" 
                                                  placeholder="Masukkan alasan penolakan proposal..." required></textarea>
                                    </div>
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="button" class="btn btn-secondary me-md-2" onclick="hideRejectForm()">
                                            <i class="fas fa-times me-2"></i>Batal
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="submitValidasi('tolak')">
                                            <i class="fas fa-times me-2"></i>Tolak Proposal
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

    <!-- Hasil Review -->
    @if($proposal->nilaiAdministratif->count() > 0 || $proposal->nilaiSubstantif->count() > 0 || $proposal->hasilFinal)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star me-2"></i>Hasil Review
                    </h5>
                </div>
                <div class="card-body">
                    @if($proposal->nilaiAdministratif->count() > 0)
                        <div class="mb-3">
                            <h6>Review Administratif</h6>
                            @foreach($proposal->nilaiAdministratif as $nilai)
                                <p><strong>Nilai:</strong> {{ $nilai->nilai_administratif }}</p>
                                <p><strong>Komentar:</strong> {{ $nilai->komentar ?? 'Tidak ada komentar' }}</p>
                                @if(!$loop->last)<hr>@endif
                            @endforeach
                        </div>
                    @endif

                    @if($proposal->nilaiSubstantif->count() > 0)
                        <div class="mb-3">
                            <h6>Review Substantif</h6>
                            @foreach($proposal->nilaiSubstantif as $nilai)
                                <p><strong>Nilai:</strong> {{ $nilai->nilai_substantif }}</p>
                                <p><strong>Komentar:</strong> {{ $nilai->komentar ?? 'Tidak ada komentar' }}</p>
                                @if(!$loop->last)<hr>@endif
                            @endforeach
                        </div>
                    @endif

                    @if($proposal->hasilFinal)
                        <div class="mb-3">
                            <h6>Hasil Final</h6>
                            <p><strong>Status:</strong> 
                                @if($proposal->hasilFinal->status_final == 'diterima')
                                    <span class="badge bg-success">Diterima</span>
                                @elseif($proposal->hasilFinal->status_final == 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($proposal->hasilFinal->status_final) }}</span>
                                @endif
                            </p>
                            <p><strong>Komentar Final:</strong> {{ $proposal->hasilFinal->catatan_final ?? 'Tidak ada komentar' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="validasiToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fas fa-info-circle me-2"></i>
            <strong class="me-auto">Notifikasi</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage">
        </div>
    </div>
</div>
@endsection

@section('dosen_styles')
<style>
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
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--bs-primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        transition: opacity 0.3s ease;
    }
    
    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
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

    .required-field::after {
        content: " *";
        color: #dc3545;
    }
</style>
@endsection

@section('dosen_scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($proposal->dokumen && $proposal->dokumen->path_file)
            loadPDFDocument();
        @endif

        // Initialize fullscreen functionality
        initializeFullscreen();

        // Show success/error messages
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
            
            // Redirect setelah 2 detik jika ada redirect_to
            @if(session('redirect_to'))
                setTimeout(() => {
                    window.location.href = '{{ session('redirect_to') }}';
                }, 2000);
            @endif
        @endif

        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
    });

    function loadPDFDocument() {
        const pdfViewer = document.getElementById('pdfViewer');
        const pdfUrl = '{{ $proposal->dokumen ? Storage::url($proposal->dokumen->path_file) : "" }}';
        
        if (!pdfUrl) {
            pdfViewer.innerHTML = '<div class="empty-state"><i class="fas fa-file-pdf"></i><h4>Dokumen Tidak Tersedia</h4><p>Dokumen proposal tidak ditemukan.</p></div>';
            return;
        }

        // Create iframe first (before clearing content)
        const iframe = document.createElement('iframe');
        iframe.src = pdfUrl;
        iframe.className = 'pdf-iframe';
        iframe.style.width = '100%';
        iframe.style.height = '700px';
        iframe.style.border = 'none';
        iframe.style.borderRadius = '8px';
        iframe.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
        iframe.style.opacity = '0'; // Start invisible
        iframe.style.transition = 'opacity 0.3s ease'; // Smooth transition
        
        // Pre-load iframe before showing
        iframe.onload = function() {
            // Smooth fade in
            setTimeout(() => {
                iframe.style.opacity = '1';
                // Remove spinner after iframe is visible
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
            showDownloadOption(pdfViewer);
        };

        // Add iframe to container (but keep it invisible initially)
        pdfViewer.appendChild(iframe);
        
        // Set timeout for iframe
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
                <h4>PDF Tidak Dapat Ditampilkan</h4>
                <p>Browser Anda tidak dapat menampilkan PDF secara langsung.</p>
                <p>Silakan download file untuk melihat dokumen:</p>
                <div style="margin-top: 1rem;">
                    <a href="{{ route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
                       class="btn btn-primary">
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
                    // If using PDF.js canvas, make the container fullscreen
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

    // Show reject form
    function showRejectForm() {
        const rejectForm = document.getElementById('rejectForm');
        if (rejectForm) {
            rejectForm.style.display = 'block';
        }
    }
    
    // Hide reject form
    function hideRejectForm() {
        const rejectForm = document.getElementById('rejectForm');
        const textarea = document.querySelector('textarea[name="catatan"]');
        if (rejectForm) {
            rejectForm.style.display = 'none';
        }
        if (textarea) {
            textarea.value = '';
        }
    }
    
    // Submit validasi
    function submitValidasi(action) {
        if (confirmValidasi(action)) {
            const actionInput = document.getElementById('actionInput');
            actionInput.value = action;
            
            // Submit form
            const form = document.getElementById('validasiForm');
            form.submit();
        }
    }

    // Validate form before submission
    function validateForm() {
        const form = document.getElementById('validasiForm');
        const actionInput = document.getElementById('actionInput');
        
        if (!actionInput || !actionInput.value) {
            alert('Pilih aksi validasi terlebih dahulu!');
            return false;
        }
        
        if (actionInput.value === 'tolak') {
            const catatan = form.querySelector('textarea[name="catatan"]');
            if (!catatan || !catatan.value.trim()) {
                alert('Harap isi alasan penolakan!');
                catatan.focus();
                return false;
            }
        }
        
        return true;
    }

    // Confirm validasi
    function confirmValidasi(action) {
        const actionText = action === 'valid' ? 'validasi' : 'tolak';
        const result = confirm(`Apakah Anda yakin ingin ${actionText} proposal ini?`);
        
        if (result) {
            // Show loading state
            const submitBtn = event.target;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
            submitBtn.disabled = true;
            
            // Re-enable button after 3 seconds if form doesn't submit
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        }
        
        return result;
    }
</script>
@endsection
