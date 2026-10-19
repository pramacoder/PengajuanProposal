@extends('mainlayout.app')

@section('title', 'Detail Validasi Akhir - Dosen Pendamping Universitas')

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
        min-height: 600px;
        background: #f8fafc;
    }

    .pdf-loading .spinner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--bs-primary);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    .pdf-loading iframe {
        width: 100%;
        height: 600px;
        border: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard Dosen', 'url' => route('dosen.universitas.dashboard')],
        ['label' => 'Validasi Akhir', 'url' => route('dosen.universitas.validasi.akhir')],
        ['label' => 'Detail & Keputusan', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Detail & Validasi Akhir Usulan" 
        subtitle="Verifikasi naskah revisi akhir mahasiswa dan berikan pengesahan pendamping tingkat universitas" 
        badge="Pendamping Universitas"
        icon="fa-check-double"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dosen.universitas.validasi.akhir') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fs-5"></i>
                <div class="fw-medium">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-times-circle fs-5"></i>
                <div class="fw-medium">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

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
                    <div class="fw-bold text-dark fs-6 mb-1">{{ $proposal->mahasiswa->nama_mahasiswa ?? $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                    <div class="text-secondary small mb-3">NIM: {{ $proposal->mahasiswa->nim ?? 'N/A' }}</div>

                    <div class="pt-2 border-top">
                        <div class="mb-2">
                            <small class="text-secondary fw-semibold d-block">Program Studi & Fakultas</small>
                            <span class="text-dark small">{{ $proposal->mahasiswa->prodi->nama_prodi ?? '-' }} — {{ $proposal->mahasiswa->fakultas->nama_fakultas ?? '-' }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-secondary fw-semibold d-block">Email Mahasiswa</small>
                            <span class="text-dark small">{{ $proposal->mahasiswa->email_mahasiswa ?? $proposal->mahasiswa->email_mhs ?? '-' }}</span>
                        </div>
                        <div>
                            <small class="text-secondary fw-semibold d-block">Kontak Telepon</small>
                            <span class="text-dark small">{{ $proposal->mahasiswa->no_hp_mahasiswa ?? $proposal->mahasiswa->no_hp_mhs ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metadata Proposal & Semi Final Result -->
        <div class="col-lg-8">
            <div class="ui-card h-100 shadow-sm">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-alt text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Metadata Usulan & Hasil Semi Final</h6>
                    </div>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Validasi Akhir</span>
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
                            <small class="text-secondary fw-semibold d-block">Hasil Semi Final</small>
                            @if($proposal->hasilSemiFinal)
                                <span class="badge bg-{{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'success' : 'danger' }}-subtle text-{{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'success' : 'danger' }} border border-{{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'success' : 'danger' }}-subtle rounded-pill">
                                    {{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'Lolos Univ' : 'Tidak Lolos' }}
                                </span>
                                <small class="text-primary fw-bold d-block mt-1">Nilai: @formatId($proposal->hasilSemiFinal->nilai, 2)</small>
                            @else
                                <span class="text-muted small">Belum ada</span>
                            @endif
                        </div>
                        <div class="col-sm-4">
                            <small class="text-secondary fw-semibold d-block">Tanggal Pengajuan Awal</small>
                            <span class="text-dark small">{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d F Y, H:i') }}</span>
                        </div>
                    </div>

                    @if($proposal->hasilSemiFinal && $proposal->hasilSemiFinal->catatan_final)
                    <div class="p-3 bg-light rounded-3 border mt-3 small">
                        <strong class="text-dark d-block mb-1"><i class="fas fa-comment-dots text-primary me-1"></i>Catatan Semi Final:</strong>
                        <div class="text-secondary">{{ $proposal->hasilSemiFinal->catatan_final }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Previews: Asli & Revisi Akhir -->
    <div class="row g-4 mb-4">
        <!-- PDF Revisi Akhir (Prioritas Pemeriksaan) -->
        @php
            $revisiAkhir = $proposal->proposalRevisi->where('path_file', 'like', '%revisi_akhir%')->first();
        @endphp
        <div class="col-lg-6">
            <div class="pdf-viewer-section shadow-sm h-100">
                <div class="pdf-header bg-success-subtle border-success-subtle">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-pdf text-success fs-5"></i>
                        <h6 class="mb-0 fw-bold text-dark">Naskah Revisi Akhir Mahasiswa</h6>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($revisiAkhir)
                            <button id="fullscreenBtnRevisi" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                <i class="fas fa-expand me-1"></i>Fullscreen
                            </button>
                            <a href="{{ route('dosen.universitas.revisi.akhir.view.pdf', $proposal->id_proposal) }}" 
                               class="btn btn-sm btn-success rounded-pill px-3 fw-semibold text-white" target="_blank">
                                <i class="fas fa-download me-1"></i>Unduh PDF
                            </a>
                        @endif
                    </div>
                </div>
                <div class="p-0">
                    @if($revisiAkhir)
                        <div id="pdfViewerRevisi" class="pdf-loading">
                            <div class="spinner"></div>
                        </div>
                    @else
                        <div class="empty-state-box py-5">
                            <i class="fas fa-file-pdf text-muted mb-2 fs-1"></i>
                            <h6 class="fw-bold text-dark mb-1">Naskah Revisi Akhir Belum Diunggah</h6>
                            <p class="text-secondary small mb-0">Mahasiswa belum mengunggah berkas revisi akhir.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- PDF Proposal Asli (Referensi Perbandingan) -->
        <div class="col-lg-6">
            <div class="pdf-viewer-section shadow-sm h-100">
                <div class="pdf-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-pdf text-danger fs-5"></i>
                        <h6 class="mb-0 fw-bold text-dark">Naskah Proposal Asli (Awal)</h6>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        @if($proposal->dokumen && $proposal->dokumen->path_file)
                            <button id="fullscreenBtnProposal" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                <i class="fas fa-expand me-1"></i>Fullscreen
                            </button>
                            <a href="{{ route('dosen.universitas.proposal.view.pdf', $proposal->id_proposal) }}" 
                               class="btn btn-sm btn-outline-primary rounded-pill px-3" target="_blank">
                                <i class="fas fa-download me-1"></i>Unduh PDF
                            </a>
                        @endif
                    </div>
                </div>
                <div class="p-0">
                    @if($proposal->dokumen && $proposal->dokumen->path_file)
                        <div id="pdfViewerProposal" class="pdf-loading">
                            <div class="spinner"></div>
                        </div>
                    @else
                        <div class="empty-state-box py-5">
                            <i class="fas fa-file-pdf text-muted mb-2 fs-1"></i>
                            <h6 class="fw-bold text-dark mb-1">Proposal Asli Tidak Ditemukan</h6>
                            <p class="text-secondary small mb-0">File naskah awal tidak tersedia di repositori.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Keputusan Validasi Akhir -->
    <div class="ui-card shadow-sm mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-gavel text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Keputusan Validasi Akhir</h6>
            </div>
        </div>
        <div class="ui-card-body p-4">
            @if($proposal->status == 'validasi_akhir_dosen_univ')
                <form action="{{ route('dosen.universitas.validasi.akhir.submit', $proposal->id_proposal) }}" method="POST" id="validasiForm" enctype="multipart/form-data" onsubmit="return validateForm()">
                    @csrf
                    <input type="hidden" name="action" id="actionInput" value="">

                    <div class="row g-3 justify-content-center mb-3">
                        <div class="col-md-5">
                            <button type="button" class="btn btn-success w-100 py-3 rounded-3 d-flex align-items-center justify-content-center gap-2 fw-bold shadow-sm" onclick="submitValidasi('valid')">
                                <i class="fas fa-check-circle fa-lg"></i>
                                <span>Validasi Akhir — Kirim ke Pimpinan PT</span>
                            </button>
                        </div>
                        <div class="col-md-5">
                            <button type="button" class="btn btn-outline-danger w-100 py-3 rounded-3 d-flex align-items-center justify-content-center gap-2 fw-bold shadow-sm" onclick="showRejectForm()">
                                <i class="fas fa-times-circle fa-lg"></i>
                                <span>Tolak — Kembalikan ke Mahasiswa</span>
                            </button>
                        </div>
                    </div>

                    <!-- Reject Form Box -->
                    <div id="rejectForm" class="p-4 rounded-3 bg-light border border-danger-subtle mt-4" style="display: none;">
                        <h6 class="fw-bold text-danger mb-2">
                            <i class="fas fa-exclamation-triangle me-2"></i>Alasan Penolakan Validasi Akhir
                        </h6>
                        <p class="text-secondary small mb-3">Berikan catatan perbaikan yang jelas agar mahasiswa dapat melakukan revisi ulang:</p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark" for="catatan">Catatan Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="catatan" id="catatan" class="form-control rounded-3" rows="4" 
                                      placeholder="Masukkan alasan penolakan naskah revisi akhir..."></textarea>
                        </div>

                        <!-- Upload File Review (Opsional) -->
                        <div class="mb-4">
                            <label for="file_review" class="form-label fw-bold text-dark">
                                Unggah File Naskah Beranotasi <small class="text-muted fw-normal">(Opsional — Format PDF)</small>
                            </label>
                            <input type="file" class="form-control rounded-3" id="file_review" name="file_review" accept=".pdf">
                            <small class="form-text text-muted">
                                Berkas PDF ini akan diteruskan ke mahasiswa sebagai referensi perbaikan.
                            </small>
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
            @else
                @if($proposal->status_validasi == 'valid')
                    <div class="p-4 rounded-3 bg-success-subtle border border-success-subtle text-center">
                        <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                        <h5 class="fw-bold text-success mb-1">Proposal Telah Divalidasi Akhir</h5>
                        <p class="text-secondary small mb-0">
                            Usulan ini telah divalidasi dan diteruskan ke Pimpinan PT untuk penetapan kelolosan final.
                            @if($proposal->tanggal_validasi)
                                <br><span class="text-muted">Tanggal validasi: {{ \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d F Y, H:i') }}</span>
                            @endif
                        </p>
                    </div>
                @elseif($proposal->status_validasi == 'tidak_valid')
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
                @endif
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Load PDF Proposal Asli
    @if($proposal->dokumen && $proposal->dokumen->path_file)
    document.addEventListener('DOMContentLoaded', function() {
        const pdfViewerProposal = document.getElementById('pdfViewerProposal');
        const proposalUrl = '{{ route("dosen.universitas.proposal.view.pdf", $proposal->id_proposal) }}';
        
        pdfViewerProposal.innerHTML = '';
        
        const iframeProposal = document.createElement('iframe');
        iframeProposal.src = proposalUrl;
        iframeProposal.style.width = '100%';
        iframeProposal.style.height = '600px';
        iframeProposal.style.border = 'none';
        iframeProposal.style.borderRadius = '4px';
        
        iframeProposal.onerror = function() {
            pdfViewerProposal.innerHTML = '<div class="alert alert-danger p-3 small">Gagal memuat PDF. <a href="' + proposalUrl + '" target="_blank">Buka di tab baru</a></div>';
        };
        
        pdfViewerProposal.appendChild(iframeProposal);
        
        const fullscreenBtn = document.getElementById('fullscreenBtnProposal');
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', function() {
                if (iframeProposal.requestFullscreen) {
                    iframeProposal.requestFullscreen();
                } else if (iframeProposal.webkitRequestFullscreen) {
                    iframeProposal.webkitRequestFullscreen();
                } else if (iframeProposal.msRequestFullscreen) {
                    iframeProposal.msRequestFullscreen();
                }
            });
        }
    });
    @endif

    // Load PDF Revisi Akhir
    @if($revisiAkhir)
    document.addEventListener('DOMContentLoaded', function() {
        const pdfViewerRevisi = document.getElementById('pdfViewerRevisi');
        const revisiUrl = '{{ route("dosen.universitas.revisi.akhir.view.pdf", $proposal->id_proposal) }}';
        
        pdfViewerRevisi.innerHTML = '';
        
        const iframeRevisi = document.createElement('iframe');
        iframeRevisi.src = revisiUrl;
        iframeRevisi.style.width = '100%';
        iframeRevisi.style.height = '600px';
        iframeRevisi.style.border = 'none';
        iframeRevisi.style.borderRadius = '4px';
        
        iframeRevisi.onerror = function() {
            pdfViewerRevisi.innerHTML = '<div class="alert alert-danger p-3 small">Gagal memuat PDF. <a href="' + revisiUrl + '" target="_blank">Buka di tab baru</a></div>';
        };
        
        pdfViewerRevisi.appendChild(iframeRevisi);
        
        const fullscreenBtn = document.getElementById('fullscreenBtnRevisi');
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', function() {
                if (iframeRevisi.requestFullscreen) {
                    iframeRevisi.requestFullscreen();
                } else if (iframeRevisi.webkitRequestFullscreen) {
                    iframeRevisi.webkitRequestFullscreen();
                } else if (iframeRevisi.msRequestFullscreen) {
                    iframeRevisi.msRequestFullscreen();
                }
            });
        }
    });
    @endif

    function showRejectForm() {
        const rejectForm = document.getElementById('rejectForm');
        if (rejectForm) {
            rejectForm.style.display = 'block';
            const catatan = document.getElementById('catatan');
            if (catatan) catatan.focus();
        }
    }

    function hideRejectForm() {
        const rejectForm = document.getElementById('rejectForm');
        if (rejectForm) rejectForm.style.display = 'none';
        const actionInput = document.getElementById('actionInput');
        if (actionInput) actionInput.value = '';
    }

    function submitValidasi(action) {
        const actionText = action === 'valid' ? 'Validasi Akhir' : 'Tolak';
        const confirmed = confirm(`Apakah Anda yakin ingin melakukan ${actionText.toLowerCase()} pada proposal ini?`);
        
        if (confirmed) {
            const actionInput = document.getElementById('actionInput');
            actionInput.value = action;
            
            if (action === 'tolak') {
                const catatan = document.querySelector('textarea[name="catatan"]');
                if (!catatan || !catatan.value.trim()) {
                    alert('Harap isi alasan penolakan!');
                    catatan.focus();
                    return;
                }
            }
            
            const form = document.getElementById('validasiForm');
            form.submit();
        }
    }

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
</script>
@endpush
@endsection
