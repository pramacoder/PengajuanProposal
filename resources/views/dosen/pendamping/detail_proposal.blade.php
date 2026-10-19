@extends('mainlayout.app')

@section('title', 'Detail Proposal - Validasi Pendamping')

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
        min-height: 700px;
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
        height: 700px;
        border: none;
        background: white;
        transition: opacity 0.3s ease;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard Dosen', 'url' => route('dosen.pendamping.dashboard')],
        ['label' => 'Validasi Proposal', 'url' => route('dosen.pendamping.proposal.validasi')],
        ['label' => 'Detail Usulan', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Detail & Validasi Usulan Proposal" 
        subtitle="Evaluasi berkas usulan mahasiswa, periksa naskah digital, dan tentukan persetujuan pendamping" 
        badge="Dosen Pendamping"
        icon="fa-clipboard-check"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dosen.pendamping.dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
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
                        <h6 class="mb-0 fw-bold fs-6">Ketua Mahasiswa Pengusul</h6>
                    </div>
                </div>
                <div class="ui-card-body p-3">
                    <div class="fw-bold text-dark fs-6 mb-1">{{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                    <div class="text-secondary small mb-3">NIM: {{ $proposal->mahasiswa->nim ?? 'N/A' }}</div>

                    <div class="pt-2 border-top">
                        <div class="mb-2">
                            <small class="text-secondary fw-semibold d-block">Program Studi</small>
                            <span class="text-dark small">{{ $proposal->mahasiswa->prodi->nama_prodi ?? $proposal->mahasiswa->prodi_mhs ?? '-' }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-secondary fw-semibold d-block">Fakultas</small>
                            <span class="text-dark small">{{ $proposal->mahasiswa->fakultas->nama_fakultas ?? $proposal->mahasiswa->fakultas_mhs ?? '-' }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-secondary fw-semibold d-block">Email</small>
                            <span class="text-dark small">{{ $proposal->mahasiswa->email_mhs ?? '-' }}</span>
                        </div>
                        <div>
                            <small class="text-secondary fw-semibold d-block">Nomor Telepon</small>
                            <span class="text-dark small">{{ $proposal->mahasiswa->no_hp_mhs ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Proposal & Status -->
        <div class="col-lg-8">
            <div class="ui-card h-100 shadow-sm">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Metadata Proposal & Status Validasi</h6>
                    </div>
                    <x-status-badge :status="$proposal->status_validasi" />
                </div>
                <div class="ui-card-body p-4">
                    <div class="mb-3">
                        <small class="text-secondary fw-semibold d-block">Judul Usulan</small>
                        <h6 class="fw-bold text-dark mb-0">{{ $proposal->judul_proposal }}</h6>
                    </div>

                    <div class="row g-3 pt-3 border-top">
                        <div class="col-sm-4">
                            <small class="text-secondary fw-semibold d-block">Skim PKM</small>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">{{ $proposal->skim }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-secondary fw-semibold d-block">Status Sistem</small>
                            <x-status-badge :status="$proposal->status" :label="$proposal->status_label" />
                        </div>
                        <div class="col-sm-4">
                            <small class="text-secondary fw-semibold d-block">Tanggal Pengajuan</small>
                            <span class="text-dark small">{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d F Y, H:i') }}</span>
                        </div>
                    </div>

                    @if($proposal->dokumen && $proposal->dokumen->path_file)
                    <div class="mt-4 pt-3 border-top d-flex gap-2 flex-wrap">
                        <a href="{{ route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
                           class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" target="_blank">
                            <i class="fas fa-download me-1"></i>Unduh Berkas Usulan
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Viewer Section -->
    @if($proposal->dokumen && $proposal->dokumen->path_file)
        <div class="pdf-viewer-section shadow-sm mb-4">
            <div class="pdf-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-file-pdf text-danger fs-5"></i>
                    <h6 class="mb-0 fw-bold text-dark">Pratinjau Naskah Digital Usulan</h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button id="fullscreenBtn" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="fas fa-expand me-1"></i>Fullscreen
                    </button>
                    <a href="{{ route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
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
                    <h5 class="fw-bold text-dark">Dokumen Naskah Tidak Ditemukan</h5>
                    <p class="text-secondary small mb-0">Berkas digital belum diunggah atau tidak ditemukan di repositori.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Aksi Validasi Section -->
    <div class="ui-card shadow-sm mb-4" id="validasi">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-gavel text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Aksi Validasi Proposal</h6>
            </div>
        </div>
        <div class="ui-card-body p-4">
            @if($proposal->status_validasi === 'valid')
                <div class="p-4 rounded-3 bg-success-subtle border border-success-subtle text-center">
                    <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                    <h5 class="fw-bold text-success mb-1">Proposal Telah Divalidasi</h5>
                    <p class="text-secondary small mb-0">
                        Anda telah menyetujui usulan ini.
                        @if($proposal->tanggal_validasi)
                            <br><span class="text-muted">Tanggal validasi: {{ \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d F Y, H:i') }}</span>
                        @endif
                    </p>
                </div>
            @elseif($proposal->status_validasi === 'tidak_valid')
                <div class="p-4 rounded-3 bg-danger-subtle border border-danger-subtle text-center">
                    <i class="fas fa-times-circle fa-3x text-danger mb-2"></i>
                    <h5 class="fw-bold text-danger mb-1">Proposal Ditolak</h5>
                    @if($proposal->catatan)
                        <p class="text-dark small mb-1"><strong>Alasan:</strong> {{ $proposal->catatan }}</p>
                    @endif
                    @if($proposal->tanggal_validasi)
                        <small class="text-muted">Tanggal penolakan: {{ \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d F Y, H:i') }}</small>
                    @endif
                </div>
            @else
                <form action="{{ route('dosen.pendamping.proposal.validasi.submit', $proposal->id_proposal) }}" method="POST" id="validasiForm" enctype="multipart/form-data" onsubmit="return validateForm()">
                    @csrf
                    <input type="hidden" name="action" id="actionInput" value="">

                    <div class="row g-3 justify-content-center mb-3">
                        <div class="col-md-5">
                            <button type="button" class="btn btn-success w-100 py-3 rounded-3 d-flex align-items-center justify-content-center gap-2 fw-bold shadow-sm" onclick="submitValidasi('valid')">
                                <i class="fas fa-check-circle fa-lg"></i>
                                <span>Setujui & Validasi Proposal</span>
                            </button>
                        </div>
                        <div class="col-md-5">
                            <button type="button" class="btn btn-outline-danger w-100 py-3 rounded-3 d-flex align-items-center justify-content-center gap-2 fw-bold shadow-sm" onclick="showRejectForm()">
                                <i class="fas fa-times-circle fa-lg"></i>
                                <span>Tolak / Kembalikan Usulan</span>
                            </button>
                        </div>
                    </div>

                    <!-- Form Penolakan (Hidden by default) -->
                    <div id="rejectForm" class="p-4 rounded-3 bg-light border border-danger-subtle mt-4" style="display: none;">
                        <h6 class="fw-bold text-danger mb-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>Keterangan Penolakan & Koreksi
                        </h6>
                        <p class="text-secondary small mb-3">Berikan catatan perbaikan yang jelas agar mahasiswa dapat menyempurnakan kembali proposalnya:</p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark" for="catatan">Catatan Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="catatan" id="catatan" class="form-control rounded-3" rows="4" 
                                      placeholder="Masukkan alasan penolakan dan instruksi perbaikan untuk mahasiswa..."></textarea>
                            @error('catatan')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Upload File Koreksi (Opsional) -->
                        <div class="mb-4">
                            <label for="file_koreksi" class="form-label fw-bold text-dark">
                                Unggah File Naskah Koreksi <small class="text-muted fw-normal">(Opsional — Format PDF)</small>
                            </label>
                            <input type="file" class="form-control rounded-3" id="file_koreksi" name="file_koreksi" accept=".pdf">
                            <small class="form-text text-muted">
                                File PDF yang diunggah akan menggantikan file usulan sebagai berkas dengan koreksi dosen.
                            </small>
                            @error('file_koreksi')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary rounded-3 px-3 fw-semibold" onclick="hideRejectForm()">
                                <i class="fas fa-times me-1"></i>Batal
                            </button>
                            <button type="button" class="btn btn-danger rounded-3 px-4 fw-semibold" onclick="submitValidasi('tolak')">
                                <i class="fas fa-paper-plane me-1"></i>Kirim Penolakan
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Hasil Review Section -->
    @if($proposal->nilaiAdministratif->count() > 0 || $proposal->nilaiSubstantif->count() > 0 || $proposal->hasilFinal)
    <div class="ui-card shadow-sm mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-star text-warning"></i>
                <h6 class="mb-0 fw-bold fs-6">Hasil Penilaian Evaluator / Reviewer</h6>
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
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="validasiToast" class="toast rounded-3 shadow" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="fas fa-info-circle me-2 text-primary"></i>
            <strong class="me-auto text-dark">Notifikasi Sistem</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($proposal->dokumen && $proposal->dokumen->path_file)
        loadPDFDocument();
    @endif
    initializeFullscreen();
});

function loadPDFDocument() {
    const pdfViewer = document.getElementById('pdfViewer');
    @php
        $pdfUrl = '';
        if ($proposal->dokumen && $proposal->dokumen->path_file) {
            $pdfUrl = route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']);
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
            <h5 class="fw-bold text-dark">PDF Tidak Dapat Dimuat Langsung</h5>
            <p class="text-secondary small mb-3">Silakan unduh file untuk melihat isi dokumen:</p>
            <a href="{{ route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
               class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-download me-1"></i>Unduh Berkas Usulan
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

function showRejectForm() {
    const rejectForm = document.getElementById('rejectForm');
    if (rejectForm) {
        rejectForm.style.display = 'block';
        const catatanField = rejectForm.querySelector('textarea[name="catatan"]');
        if (catatanField) catatanField.focus();
    }
}

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

async function submitValidasi(action) {
    const confirmed = await confirmValidasi(action);
    
    if (confirmed) {
        const actionInput = document.getElementById('actionInput');
        actionInput.value = action;
        
        const form = document.getElementById('validasiForm');
        form.submit();
    }
}

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
            await showCustomAlert('Harap isi alasan penolakan proposal!', 'danger');
            catatan.focus();
            return false;
        }
    }
    
    return true;
}

function showCustomAlert(message, type = 'info') {
    return new Promise((resolve) => {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold text-dark">
                            <i class="fas fa-info-circle me-2 text-${type}"></i>Perhatian
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0 text-secondary">${message}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary rounded-3 px-3 fw-semibold" data-bs-dismiss="modal">
                            Mengerti
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
            resolve();
        });
    });
}

function confirmValidasi(action) {
    return new Promise((resolve) => {
        const actionText = action === 'valid' ? 'Validasi' : 'Tolak';
        const actionColor = action === 'valid' ? 'success' : 'danger';
        const actionMessage = action === 'valid' 
            ? 'Proposal akan disetujui dan dilanjutkan ke proses penilaian reviewer.'
            : 'Proposal akan dikembalikan dan mahasiswa harus memperbaiki sesuai catatan Anda.';
        
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold text-dark">
                            <i class="fas fa-question-circle me-2 text-${actionColor}"></i>Konfirmasi ${actionText} Usulan
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2 text-dark">Apakah Anda yakin ingin melakukan <strong>${actionText.toLowerCase()}</strong> pada proposal ini?</p>
                        <div class="p-3 rounded-3 bg-light border small text-secondary">
                            ${actionMessage}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary rounded-3 px-3 fw-semibold" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="button" class="btn btn-${actionColor} rounded-3 px-3 fw-semibold" id="confirmActionBtn">
                            Ya, ${actionText} Usulan
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        modal.querySelector('#confirmActionBtn').addEventListener('click', () => {
            bsModal.hide();
            resolve(true);
        });
        
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
            resolve(false);
        });
    });
}
</script>
@endpush
@endsection
