@extends('mainlayout.app')

@section('title', 'Detail Proposal Mahasiswa')

@section('styles')
<style>
    .pdf-viewer-section {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
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

    .pdf-loading {
        position: relative;
        min-height: 650px;
        background: #f8fafc;
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
        height: 650px;
        border: none;
        background: white;
        transition: opacity 0.3s ease;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard Pembimbing', 'url' => route('dosen.pembimbing.dashboard')],
        ['label' => 'Detail Proposal', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Detail Proposal Bimbingan" 
        subtitle="Informasi komprehensif, berkas naskah, dan riwayat evaluasi proposal mahasiswa" 
        badge="Dosen Pembimbing"
        icon="fa-file-alt"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dosen.pembimbing.dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="row g-4 mb-4">
        <!-- Informasi Mahasiswa -->
        <div class="col-lg-4">
            <div class="ui-card h-100 shadow-sm">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-user-graduate text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Data Mahasiswa Pengusul</h6>
                    </div>
                </div>
                <div class="ui-card-body p-3">
                    <div class="fw-bold text-dark fs-6 mb-1">{{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                    <div class="text-secondary small mb-3">NIM: {{ $proposal->mahasiswa->nim ?? 'N/A' }}</div>
                    
                    <div class="pt-2 border-top">
                        <div class="mb-2">
                            <small class="text-secondary fw-semibold d-block">Program Studi & Fakultas</small>
                            <span class="text-dark small">{{ $proposal->mahasiswa->prodi_mhs ?? '-' }} — {{ $proposal->mahasiswa->fakultas_mhs ?? '-' }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-secondary fw-semibold d-block">Kontak Email</small>
                            <span class="text-dark small">{{ $proposal->mahasiswa->email_mhs ?? '-' }}</span>
                        </div>
                        <div>
                            <small class="text-secondary fw-semibold d-block">Nomor Telepon / WhatsApp</small>
                            <span class="text-dark small">{{ $proposal->mahasiswa->no_hp_mhs ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Proposal -->
        <div class="col-lg-8">
            <div class="ui-card h-100 shadow-sm">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Metadata Proposal Usulan</h6>
                    </div>
                    <x-status-badge :status="$proposal->status_validasi" />
                </div>
                <div class="ui-card-body p-4">
                    <div class="mb-3">
                        <small class="text-secondary fw-semibold d-block">Judul Usulan</small>
                        <h6 class="fw-bold text-dark mb-0">{{ $proposal->judul ?? $proposal->judul_proposal }}</h6>
                    </div>

                    <div class="row g-3 pt-3 border-top">
                        <div class="col-sm-4">
                            <small class="text-secondary fw-semibold d-block">Skim PKM</small>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">{{ $proposal->skim }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-secondary fw-semibold d-block">Dana Diajukan</small>
                            <span class="fw-bold text-success">@rupiahId($proposal->dana_diajukan)</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-secondary fw-semibold d-block">Tanggal Pengajuan</small>
                            <span class="text-dark small">{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d F Y, H:i') }}</span>
                        </div>
                    </div>

                    @if($fileProposal && $fileProposal->path_file)
                    <div class="mt-4 pt-3 border-top d-flex gap-2 flex-wrap">
                        <a href="{{ route('mahasiswa.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
                           class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" target="_blank">
                            <i class="fas fa-download me-1"></i>Unduh Naskah Proposal
                        </a>
                        @if($proposal->dokumen && $proposal->dokumen->file_lampiran)
                            <a href="{{ route('mahasiswa.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'lampiran']) }}" 
                               class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold" target="_blank">
                                <i class="fas fa-paperclip me-1"></i>Unduh Berkas Lampiran
                            </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Viewer Section -->
    @if($fileProposal && $fileProposal->path_file)
        <div class="pdf-viewer-section shadow-sm mb-4">
            <div class="pdf-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-file-pdf text-danger fs-5"></i>
                    <h6 class="mb-0 fw-bold text-dark">
                        Pratinjau Dokumen Usulan
                        @if($jenisFile === 'revisi_akhir')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill ms-2">Revisi Akhir</span>
                        @elseif($jenisFile === 'revisi')
                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill ms-2">Revisi</span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border rounded-pill ms-2">Proposal Awal</span>
                        @endif
                    </h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button id="fullscreenBtn" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="fas fa-expand me-1"></i>Fullscreen
                    </button>
                    <a href="{{ route('mahasiswa.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
                       class="btn btn-sm btn-outline-primary rounded-pill px-3" target="_blank">
                        <i class="fas fa-download me-1"></i>Unduh PDF
                    </a>
                </div>
            </div>
            <div class="p-0">
                <div id="pdfViewer" class="pdf-loading">
                    <div class="spinner"></div>
                </div>
            </div>
        </div>
    @else
        <div class="ui-card mb-4">
            <div class="ui-card-body p-0">
                <div class="empty-state-box py-5">
                    <i class="fas fa-file-pdf text-muted mb-3 fs-1"></i>
                    <h5 class="fw-bold text-dark">Berkas Proposal Belum Tersedia</h5>
                    <p class="text-secondary small mb-0">File naskah usulan belum diunggah atau tidak ditemukan di repositori sistem.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Hasil Review Section -->
    @if($proposal->nilaiAdministratif->count() > 0 || $proposal->nilaiSubstantif->count() > 0 || $proposal->hasilFinal)
    <div class="ui-card shadow-sm mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-star text-warning"></i>
                <h6 class="mb-0 fw-bold fs-6">Hasil Penilaian & Komentar Reviewer</h6>
            </div>
        </div>
        <div class="ui-card-body p-4">
            <div class="row g-4">
                @if($proposal->nilaiAdministratif->count() > 0)
                <div class="col-md-6">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-clipboard-check text-primary me-2"></i>Review Administratif</h6>
                    @foreach($proposal->nilaiAdministratif as $nilai)
                        <div class="p-3 rounded-3 bg-light border mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-secondary-subtle text-dark border">Penilaian Administratif</span>
                                <strong class="text-primary fs-6">@formatId((float) $nilai->nilai_administratif, 2)</strong>
                            </div>
                            <div class="small text-secondary mt-2">{{ $nilai->komentar ?? $nilai->note_administratif ?? 'Tidak ada catatan tertulis.' }}</div>
                        </div>
                    @endforeach
                </div>
                @endif

                @if($proposal->nilaiSubstantif->count() > 0)
                <div class="col-md-6">
                    <h6 class="fw-bold text-dark mb-3"><i class="fas fa-check-circle text-success me-2"></i>Review Substantif</h6>
                    @foreach($proposal->nilaiSubstantif as $index => $nilai)
                        <div class="p-3 rounded-3 bg-light border mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-primary-subtle text-primary border">Reviewer {{ $index + 1 }}</span>
                                <strong class="text-success fs-6">@formatId((float) ($nilai->nilai_akhir ?? $nilai->nilai_substantif ?? $nilai->total_nilai), 2)</strong>
                            </div>
                            <div class="small text-secondary mt-2">{{ $nilai->komentar ?? $nilai->catatan ?? $nilai->note_substantif ?? 'Tidak ada komentar khusus.' }}</div>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            @if($proposal->hasilFinal)
            <div class="mt-4 pt-3 border-top">
                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-award text-warning me-2"></i>Hasil Akhir / Final</h6>
                <div class="p-3 rounded-3 bg-light border d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="text-muted small d-block">Status Kelolosan Final</span>
                        <x-status-badge :status="$proposal->hasilFinal->status_final" />
                    </div>
                    @if($proposal->hasilFinal->catatan_final)
                    <div class="text-secondary small mt-2 w-100">
                        <strong>Catatan:</strong> {{ $proposal->hasilFinal->catatan_final }}
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Catatan Validasi -->
    @if($proposal->catatan_validasi)
    <div class="ui-card shadow-sm mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-comment text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Catatan Riwayat Validasi</h6>
            </div>
            @if($proposal->tanggal_validasi)
                <small class="text-muted">{{ \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d/m/Y H:i') }}</small>
            @endif
        </div>
        <div class="ui-card-body p-4">
            <p class="mb-0 text-dark">{{ $proposal->catatan_validasi }}</p>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($fileProposal && $fileProposal->path_file)
        loadPDFDocument();
    @endif
    initializeFullscreen();
});

function loadPDFDocument() {
    const pdfViewer = document.getElementById('pdfViewer');
    @php
        $pdfUrl = '';
        if ($fileProposal && $fileProposal->path_file) {
            $pdfUrl = route('file.serve', ['path' => $fileProposal->path_file]);
        }
    @endphp
    const pdfUrl = '{{ $pdfUrl }}';
    
    if (!pdfUrl) {
        pdfViewer.innerHTML = '<div class="empty-state-box py-5"><i class="fas fa-file-pdf text-muted mb-2 fs-1"></i><p class="text-muted">Dokumen proposal tidak ditemukan.</p></div>';
        return;
    }

    const iframe = document.createElement('iframe');
    iframe.src = pdfUrl;
    iframe.className = 'pdf-iframe';
    iframe.style.opacity = '0';
    
    iframe.onload = function() {
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

    iframe.onerror = function() {
        showDownloadOption(pdfViewer);
    };

    pdfViewer.appendChild(iframe);
    
    setTimeout(() => {
        const spinner = pdfViewer.querySelector('.spinner');
        if (spinner && iframe.style.opacity === '0') {
            showDownloadOption(pdfViewer);
        }
    }, 6000);
}

function showDownloadOption(pdfViewer) {
    pdfViewer.innerHTML = `
        <div class="empty-state-box py-5">
            <i class="fas fa-file-pdf text-muted mb-3 fs-1"></i>
            <h5 class="fw-bold text-dark">Pratinjau PDF Tidak Dapat Dimuat</h5>
            <p class="text-secondary small mb-3">Silakan unduh dokumen untuk melihat isi proposal:</p>
            <a href="{{ route('mahasiswa.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
               class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-download me-1"></i>Unduh PDF
            </a>
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
            if (target.requestFullscreen) {
                target.requestFullscreen();
            } else if (target.webkitRequestFullscreen) {
                target.webkitRequestFullscreen();
            } else if (target.msRequestFullscreen) {
                target.msRequestFullscreen();
            }
        });
    }
}
</script>
@endpush
@endsection
