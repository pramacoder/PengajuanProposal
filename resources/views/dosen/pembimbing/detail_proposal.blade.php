@extends('mainlayout.app')

@section('title', 'Detail Proposal Mahasiswa')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-file-alt me-2"></i>Detail Proposal</h2>
                    <p class="text-muted">Informasi lengkap proposal mahasiswa bimbingan</p>
                </div>
                <div>
                    <a href="{{ route('dosen.pembimbing.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Mahasiswa -->
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
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i>Informasi Proposal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Judul:</strong> {{ $proposal->judul }}</p>
                            <p><strong>Skim:</strong> <span class="badge bg-info">{{ $proposal->skim }}</span></p>
                            <p><strong>Dana Diajukan:</strong> Rp {{ number_format($proposal->dana_diajukan, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d/m/Y H:i') }}</p>
                            <p><strong>Status:</strong> 
                                @switch($proposal->status_validasi)
                                    @case('pending')
                                        <span class="badge bg-warning">Pending</span>
                                        @break
                                    @case('valid')
                                        <span class="badge bg-success">Valid</span>
                                        @break
                                    @case('tidak_valid')
                                        <span class="badge bg-danger">Tidak Valid</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">Unknown</span>
                                @endswitch
                            </p>
                            <p><strong>Dosen Pendamping:</strong> {{ $proposal->dosen_pembimbing ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dokumen Proposal -->
    @if($fileProposal)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-pdf me-2"></i>Dokumen Proposal
                        @if($jenisFile === 'revisi_akhir')
                            <span class="badge bg-warning ms-2">Revisi Akhir</span>
                        @elseif($jenisFile === 'revisi')
                            <span class="badge bg-info ms-2">Revisi</span>
                        @else
                            <span class="badge bg-secondary ms-2">Proposal Awal</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Informasi Dokumen</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Nama File:</strong></td>
                                    <td>
                                        @if($jenisFile === 'revisi_akhir' || $jenisFile === 'revisi')
                                            {{ $fileProposal->nama_file }}
                                        @else
                                            {{ $fileProposal->file_proposal ?: 'proposal.pdf' }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Upload:</strong></td>
                                    <td>
                                        @if($jenisFile === 'revisi_akhir' || $jenisFile === 'revisi')
                                            {{ \Carbon\Carbon::parse($fileProposal->tanggal_submit)->format('d F Y, H:i') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($fileProposal->created_at)->format('d F Y, H:i') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis:</strong></td>
                                    <td>
                                        @if($jenisFile === 'revisi_akhir')
                                            <span class="badge bg-warning">Revisi Akhir</span>
                                        @elseif($jenisFile === 'revisi')
                                            <span class="badge bg-info">Revisi</span>
                                        @else
                                            <span class="badge bg-secondary">Proposal Awal</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($fileProposal->path_file)
                                            <span class="badge bg-success">Tersedia</span>
                                        @else
                                            <span class="badge bg-warning">Belum Upload</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3">Aksi</h6>
                            <div class="d-grid gap-2">
                                @if($fileProposal->path_file)
                                    <a href="{{ route('mahasiswa.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
                                       class="btn btn-outline-primary" target="_blank">
                                        <i class="fas fa-download me-2"></i>Download Proposal
                                    </a>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        File proposal belum tersedia
                                    </div>
                                @endif
                                
                                @if($proposal->dokumen && $proposal->dokumen->file_lampiran)
                                    <a href="{{ route('mahasiswa.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'lampiran']) }}" 
                                       class="btn btn-outline-secondary" target="_blank">
                                        <i class="fas fa-download me-2"></i>Download Lampiran
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Viewer -->
    @if($fileProposal && $fileProposal->path_file)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-eye me-2"></i>Preview Proposal
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="border rounded p-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="btn-group" role="group">
                                    <button id="fullscreenBtn" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-expand me-1"></i>Fullscreen
                                    </button>
                                    <a href="{{ route('mahasiswa.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
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
            </div>
        </div>
    @else
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>PDF tidak tersedia:</strong> File proposal belum diupload atau tidak dapat diakses.
                </div>
            </div>
        </div>
    @endif
    @endif

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

    <!-- Catatan Validasi -->
    @if($proposal->catatan_validasi)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-comment me-2"></i>Catatan Validasi
                    </h5>
                </div>
                <div class="card-body">
                    <p>{{ $proposal->catatan_validasi }}</p>
                    @if($proposal->tanggal_validasi)
                        <small class="text-muted">Divalidasi pada: {{ \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d/m/Y H:i') }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

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
</style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($fileProposal && $fileProposal->path_file)
            loadPDFDocument();
        @endif

        // Initialize fullscreen functionality
        initializeFullscreen();
    });

    function loadPDFDocument() {
        const pdfViewer = document.getElementById('pdfViewer');
        @php
            $pdfUrl = '';
            if ($fileProposal && $fileProposal->path_file) {
                $pdfUrl = asset('storage/' . $fileProposal->path_file);
            }
        @endphp
        const pdfUrl = '{{ $pdfUrl }}';
        
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
                    <a href="{{ route('mahasiswa.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
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
</script>
@endsection
