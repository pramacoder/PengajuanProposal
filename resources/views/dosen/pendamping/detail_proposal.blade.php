@extends('dosen.layout')

@section('page_title', 'Detail Proposal - Validasi')

@section('dosen_content')
<div class="row">
    <div class="col-12">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard Dosen', 'url' => route('dosen.pendamping.dashboard')],
            ['label' => 'Validasi Proposal', 'url' => route('dosen.pendamping.proposal.validasi')],
            ['label' => 'Detail Proposal', 'active' => true],
        ]" />

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
                <!-- Informasi Proposal -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0 text-white">
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
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h6 class="fw-bold mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Proposal
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                            <tr>
                                <td width="30%"><strong>Judul:</strong></td>
                                        <td><div class="text-wrap" style="max-width: 100%; word-wrap: break-word;">{{ $proposal->judul }}</div></td>
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
                                        <td><div class="text-wrap" style="max-width: 100%; word-wrap: break-word;">{{ $proposal->mahasiswa->fakultas->nama_fakultas ?? 'N/A' }}</div></td>
                            </tr>
                            <tr>
                                <td><strong>Email Ketua:</strong></td>
                                <td>{{ $proposal->mahasiswa->email_mhs ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status Validasi:</strong></td>
                                <td>
                                    @if($proposal->status_validasi === 'valid')
                                        <x-status-badge status="valid" label="Sudah Divalidasi" />
                                    @elseif($proposal->status_validasi === 'tidak_valid')
                                        <x-status-badge status="tidak_valid" label="Ditolak" />
                                    @else
                                        <x-status-badge status="pending" label="Belum Divalidasi" />
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Status Proposal:</strong></td>
                                <td>
                                    <x-status-badge :status="$proposal->status" :label="$proposal->status_label" />
                                </td>
                            </tr>
                        </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h6 class="fw-bold mb-0">
                                    <i class="fas fa-file-alt me-2"></i>Dokumen
                                </h6>
                            </div>
                            <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($proposal->dokumen && $proposal->dokumen->path_file)
                                <a href="{{ route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
                                   class="btn btn-outline-primary" target="_blank">
                                    <i class="fas fa-download me-2"></i>Download Proposal
                                </a>
                            @else
                                        <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    File proposal belum tersedia
                                </div>
                            @endif
                                </div>
                            
                                <!-- Info Alert -->
                                <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                    <small>File lampiran tidak tersedia (sistem hanya mendukung 1 file per proposal)</small>
                                </div>
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
                    <div class="col-12 text-center">
                        <h6 class="fw-bold mb-3">Aksi Validasi</h6>
                        
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
                            <form action="{{ route('dosen.pendamping.proposal.validasi.submit', $proposal->id_proposal) }}" method="POST" id="validasiForm" enctype="multipart/form-data" onsubmit="return validateForm()">
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
                                        @error('catatan')
                                            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <!-- Upload File Koreksi (Opsional) -->
                                    <div class="mb-3">
                                        <label for="file_koreksi" class="form-label">
                                            <i class="fas fa-file-pdf me-2"></i>
                                            Upload File Koreksi <small class="text-muted">(Opsional - PDF)</small>
                                        </label>
                                        <input type="file" class="form-control" id="file_koreksi" name="file_koreksi" accept=".pdf">
                                        <small class="form-text text-muted">
                                            Format: PDF saja. Maksimal: 5MB. File ini akan menggantikan file proposal asli.
                                        </small>
                                        @error('file_koreksi')
                                            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                                        @enderror
                                        <div class="alert alert-info mt-2 mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <small>File koreksi yang diupload akan menggantikan file proposal asli. Mahasiswa akan melihat file ini sebagai file proposal yang telah dikoreksi.</small>
                                        </div>
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
                                <p><strong>Nilai:</strong> @formatId((float) $nilai->nilai_administratif, 2)</p>
                                <p><strong>Komentar:</strong> {{ $nilai->komentar ?? 'Tidak ada komentar' }}</p>
                                @if(!$loop->last)<hr>@endif
                            @endforeach
                        </div>
                    @endif

                    @if($proposal->nilaiSubstantif->count() > 0)
                        <div class="mb-3">
                            <h6>Review Substantif</h6>
                            @foreach($proposal->nilaiSubstantif as $nilai)
                                <p><strong>Nilai:</strong> @formatId((float) $nilai->nilai_substantif, 2)</p>
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
                                    <x-status-badge status="diterima" />
                                @elseif($proposal->hasilFinal->status_final == 'ditolak')
                                    <x-status-badge status="ditolak" />
                                @else
                                    <x-status-badge :status="$proposal->hasilFinal->status_final" />
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
@php
    $pdfUrl = '';
    if ($proposal->dokumen && $proposal->dokumen->path_file) {
        // Gunakan route view-pdf untuk menampilkan PDF inline
        $pdfUrl = route('dosen.proposal.view-pdf', $proposal->id_proposal);
    }
@endphp
<script>
    // Set PDF URL dari server
    const PDF_URL = @json($pdfUrl);
    
    document.addEventListener('DOMContentLoaded', function() {
        if (PDF_URL) {
            loadPDFDocument();
        }

        // Initialize fullscreen functionality
        initializeFullscreen();

    });

    function loadPDFDocument() {
        const pdfViewer = document.getElementById('pdfViewer');
        const pdfUrl = PDF_URL;
        
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
    async function submitValidasi(action) {
        const confirmed = await confirmValidasi(action);
        
        if (confirmed) {
            const actionInput = document.getElementById('actionInput');
            actionInput.value = action;
            
            // Show loading state
            const submitBtn = event.target;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
            submitBtn.disabled = true;
            
            // Submit form
            const form = document.getElementById('validasiForm');
            form.submit();
        }
    }

    // Validate form before submission
    async function validateForm() {
        const form = document.getElementById('validasiForm');
        const actionInput = document.getElementById('actionInput');
        
        if (!actionInput || !actionInput.value) {
            await showCustomAlert('Pilih aksi validasi terlebih dahulu!', 'warning');
            return false;
        }
        
        if (actionInput.value === 'tolak') {
            const catatan = form.querySelector('textarea[name="catatan"]');
            if (!catatan || !catatan.value.trim()) {
                await showCustomAlert('Harap isi alasan penolakan!', 'danger');
                catatan.focus();
                return false;
            }
        }
        
        return true;
    }

    // Show custom alert
    function showCustomAlert(message, type = 'info') {
        return new Promise((resolve) => {
            const typeColors = {
                'info': 'primary',
                'success': 'success',
                'warning': 'warning',
                'danger': 'danger'
            };
            
            const typeIcons = {
                'info': 'fa-info-circle',
                'success': 'fa-check-circle',
                'warning': 'fa-exclamation-triangle',
                'danger': 'fa-exclamation-circle'
            };
            
            const color = typeColors[type] || 'primary';
            const icon = typeIcons[type] || 'fa-info-circle';
            
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="background: #343a40; color: white; border: 1px solid #495057;">
                        <div class="modal-header" style="border-bottom: 1px solid #495057;">
                            <h5 class="modal-title" style="color: white;">
                                <i class="fas ${icon} me-2 text-${color}"></i>
                                ${type === 'danger' ? 'Kesalahan' : type === 'warning' ? 'Peringatan' : type === 'success' ? 'Berhasil' : 'Informasi'}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="this.closest('.modal').remove()"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">${message}</p>
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #495057;">
                            <button type="button" class="btn btn-${color}" onclick="this.closest('.modal').remove(); this.closest('.modal').dispatchEvent(new Event('closed'))">
                                <i class="fas fa-check me-2"></i>Oke
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            
            modal.addEventListener('closed', () => resolve());
            
            modal.addEventListener('hidden.bs.modal', () => {
                modal.remove();
                resolve();
            });
        });
    }

    // Confirm validasi dengan custom modal
    function confirmValidasi(action) {
        return new Promise((resolve) => {
            const actionText = action === 'valid' ? 'Validasi' : 'Tolak';
            const actionIcon = action === 'valid' ? 'fa-check-circle' : 'fa-times-circle';
            const actionColor = action === 'valid' ? 'success' : 'danger';
            const actionMessage = action === 'valid' 
                ? 'Proposal akan divalidasi dan dapat dilanjutkan ke tahap selanjutnya.'
                : 'Proposal akan ditolak dan mahasiswa perlu memperbaiki proposal.';
            
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="background: white; color: #212529; border: 1px solid #dee2e6;">
                        <div class="modal-header" style="background: ${actionColor === 'success' ? '#198754' : '#dc3545'}; border-bottom: 1px solid ${actionColor === 'success' ? '#198754' : '#dc3545'};">
                            <h5 class="modal-title" style="color: white;">
                                <i class="fas ${actionIcon} me-2" style="color: white;"></i>
                                Konfirmasi ${actionText} Proposal
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="this.closest('.modal').remove()"></button>
                        </div>
                        <div class="modal-body" style="background: white; color: #212529;">
                            <p class="mb-3" style="color: #212529;">Apakah Anda yakin ingin ${actionText.toLowerCase()} proposal ini?</p>
                            <div class="alert alert-${actionColor === 'success' ? 'success' : 'warning'} mb-0" style="background: ${actionColor === 'success' ? 'rgba(25, 135, 84, 0.1)' : 'rgba(255, 193, 7, 0.1)'}; border-color: ${actionColor === 'success' ? '#198754' : '#ffc107'}; color: ${actionColor === 'success' ? '#0f5132' : '#664d03'};">
                                <i class="fas fa-info-circle me-2" style="color: ${actionColor === 'success' ? '#198754' : '#ffc107'};"></i>
                                <strong style="color: ${actionColor === 'success' ? '#0f5132' : '#664d03'};">Perhatian:</strong> <span style="color: ${actionColor === 'success' ? '#0f5132' : '#664d03'};">${actionMessage}</span>
                            </div>
                        </div>
                        <div class="modal-footer" style="background: white; border-top: 1px solid #dee2e6;">
                            <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove(); this.closest('.modal').dispatchEvent(new Event('canceled'))">
                                <i class="fas fa-times me-2"></i>Batal
                            </button>
                            <button type="button" class="btn btn-${actionColor}" onclick="this.closest('.modal').remove(); this.closest('.modal').dispatchEvent(new Event('confirmed'))">
                                <i class="fas fa-${actionIcon} me-2"></i>Ya, ${actionText} Proposal
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            
            modal.addEventListener('confirmed', () => resolve(true));
            modal.addEventListener('canceled', () => resolve(false));
            
            // Close on backdrop click
            modal.addEventListener('hidden.bs.modal', () => {
                modal.remove();
                resolve(false);
            });
        });
    }
</script>
@endsection
