@extends('mainlayout.app')

@section('title', 'Revisi Akhir Proposal PKM - Mahasiswa')

@section('styles')
<style>
    .file-upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        background-color: #f8fafc;
        cursor: pointer;
    }
    
    .file-upload-area:hover, .file-upload-area.dragover {
        border-color: var(--primary-700, #8B0000);
        background-color: rgba(139, 0, 0, 0.03);
    }
    
    .file-upload-icon {
        font-size: 2.5rem;
        color: var(--primary-700, #8B0000);
        margin-bottom: 0.75rem;
    }
    
    .file-info {
        background-color: #f1f5f9;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        display: none;
    }
    
    .file-info.show {
        display: block;
    }
    
    .progress-bar-custom {
        height: 6px;
        border-radius: 3px;
        background-color: #e2e8f0;
        overflow: hidden;
        margin-top: 0.5rem;
    }
    
    .progress-fill {
        height: 100%;
        background: var(--primary-700, #8B0000);
        width: 0%;
        transition: width 0.3s ease;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
        ['label' => 'Revisi Akhir', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Revisi Akhir Proposal PKM" 
        subtitle="Unggah berkas revisi final proposal untuk divalidasi oleh dosen pendamping universitas" 
        badge="Mahasiswa Portal"
        icon="fa-file-shield"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="row g-4 mb-4">
        <!-- Informasi Dosen Pendamping Universitas -->
        @if($proposal->dosenPendampingUniversitas)
        <div class="col-lg-5">
            <div class="ui-card h-100">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-user-graduate text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Dosen Pendamping Universitas</h6>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small">
                        Pembimbing Universitas
                    </span>
                </div>
                <div class="ui-card-body p-4">
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="fw-bold text-dark fs-6 mb-1">
                            {{ $proposal->dosenPendampingUniversitas->nama_dosen }}
                            @if($proposal->dosenPendampingUniversitas->gelar_depan), {{ $proposal->dosenPendampingUniversitas->gelar_depan }}@endif
                            @if($proposal->dosenPendampingUniversitas->gelar_belakang), {{ $proposal->dosenPendampingUniversitas->gelar_belakang }}@endif
                        </div>
                        <div class="text-secondary small">
                            NIP: {{ $proposal->dosenPendampingUniversitas->nip_dosen ?? '-' }}
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-2 mb-3">
                        @if($proposal->dosenPendampingUniversitas->email_dosen)
                        <div class="d-flex align-items-center gap-2 small">
                            <i class="fas fa-envelope text-primary" style="width: 20px;"></i>
                            <a href="mailto:{{ $proposal->dosenPendampingUniversitas->email_dosen }}" class="text-decoration-none text-dark fw-semibold">
                                {{ $proposal->dosenPendampingUniversitas->email_dosen }}
                            </a>
                        </div>
                        @endif
                        @if($proposal->dosenPendampingUniversitas->no_hp_dosen)
                        <div class="d-flex align-items-center gap-2 small">
                            <i class="fas fa-phone text-success" style="width: 20px;"></i>
                            <a href="tel:{{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}" class="text-decoration-none text-dark fw-semibold">
                                {{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}
                            </a>
                        </div>
                        @endif
                    </div>

                    <div class="alert alert-info border-0 rounded-3 p-3 mb-0 small">
                        <i class="fas fa-info-circle me-1"></i>
                        Harap berkonsultasi dan mendapatkan persetujuan dosen pendamping universitas sebelum mengunggah berkas revisi akhir ini.
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Informasi Proposal -->
        <div class="{{ $proposal->dosenPendampingUniversitas ? 'col-lg-7' : 'col-12' }}">
            <div class="ui-card h-100">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-alt text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Informasi Proposal</h6>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small">
                        Skim: {{ $proposal->skim }}
                    </span>
                </div>
                <div class="ui-card-body p-4">
                    <h5 class="fw-bold text-dark mb-3" style="line-height: 1.4;">
                        {{ $proposal->judul_proposal }}
                    </h5>
                    <div class="row g-3 pt-2 border-top">
                        <div class="col-sm-6">
                            <label class="small text-secondary fw-semibold d-block mb-1">Skim Program</label>
                            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill fw-semibold">{{ $proposal->skim }}</span>
                        </div>
                        <div class="col-sm-6">
                            <label class="small text-secondary fw-semibold d-block mb-1">Status Tahap</label>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill fw-bold">
                                {{ ucfirst(str_replace('_', ' ', $proposal->status)) }}
                            </span>
                        </div>
                    </div>

                    @if($proposal->hasilSemiFinal && $proposal->hasilSemiFinal->catatan_final)
                    <div class="mt-3 pt-3 border-top">
                        <label class="small text-secondary fw-semibold d-block mb-1">Catatan Hasil Semi Final:</label>
                        <div class="p-3 bg-light rounded-3 border small text-dark">
                            {{ $proposal->hasilSemiFinal->catatan_final }}
                        </div>
                    </div>
                    @endif

                    @php
                        $seleksiReviews = $proposal->nilaiSubstantif ? $proposal->nilaiSubstantif->where('jenis_review', 'seleksi')->filter(fn($r) => !empty($r->note_substantif ?? $r->catatan) && $r->note_substantif !== 'Review substantif dimulai') : collect();
                    @endphp
                    @if($seleksiReviews->count() > 0)
                    <div class="mt-3 pt-3 border-top">
                        <label class="small text-secondary fw-semibold d-block mb-1">Catatan Reviewer Seleksi:</label>
                        @foreach($seleksiReviews as $index => $sRev)
                            <div class="p-2.5 bg-light rounded-3 border small text-dark mb-2">
                                <strong><i class="fas fa-user-check text-primary me-1"></i>Reviewer Seleksi {{ $index + 1 }}:</strong>
                                <div class="mt-1">{{ $sRev->note_substantif ?? $sRev->catatan }}</div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upload File Revisi Akhir -->
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-upload text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Formulir Upload File Revisi Akhir</h6>
            </div>
            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                PDF Maksimal 5MB
            </span>
        </div>
        <div class="ui-card-body p-4">
            <form id="revisiAkhirForm" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="file-upload-area" id="revisiAkhirUploadArea">
                    <div class="file-upload-icon">
                        <i class="fas fa-cloud-arrow-up"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Tarik & Letakkan File PDF Revisi Akhir Di Sini</h6>
                    <p class="text-muted small mb-3">atau klik tombol di bawah untuk memilih berkas dari perangkat Anda</p>
                    <input type="file" id="revisi_file" name="revisi_file" accept=".pdf" style="display: none;" required>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4" onclick="document.getElementById('revisi_file').click()">
                        <i class="fas fa-folder-open me-2"></i>Pilih Berkas PDF
                    </button>
                </div>
                
                <div class="file-info" id="revisiAkhirFileInfo">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong id="revisiAkhirFileName" class="text-dark">Nama file</strong>
                            <br><small id="revisiAkhirFileSize" class="text-muted">Ukuran file</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 28px; height: 28px;" onclick="removeFile()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" id="revisiAkhirProgress"></div>
                    </div>
                </div>
                
                <div class="alert alert-warning mt-3 rounded-3 border-0 small">
                    <div class="fw-bold mb-1"><i class="fas fa-circle-info me-1"></i>Ketentuan Upload Berkas Revisi Akhir:</div>
                    <ul class="mb-0 ps-3">
                        <li>Format berkas wajib PDF dengan ukuran maksimal 5MB.</li>
                        <li>Pastikan proposal sudah direvisi menyeluruh sesuai rekomendasi hasil semi final.</li>
                        <li>Berkas revisi akhir ini akan diverifikasi dan divalidasi oleh dosen pendamping universitas sebelum finalisasi.</li>
                    </ul>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm" id="submitBtn">
                        <i class="fas fa-upload me-2"></i>Kirim Berkas Revisi Akhir
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar File Revisi Akhir -->
    @if($revisiAkhir->count() > 0)
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-history text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Riwayat File Revisi Akhir yang Diunggah</h6>
            </div>
            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                {{ $revisiAkhir->count() }} Berkas
            </span>
        </div>
        <div class="ui-card-body p-4">
            <div class="row g-3">
                @foreach($revisiAkhir as $revisi)
                <div class="col-md-6">
                    <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2.5 bg-danger-subtle text-danger rounded-3">
                                <i class="fas fa-file-pdf fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $revisi->nama_file }}</h6>
                                <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($revisi->tanggal_submit)->format('d M Y H:i') }}</small>
                            </div>
                        </div>
                        <a href="{{ route('mahasiswa.revisi.download', $revisi->id_revisi) }}" 
                           class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                           target="_blank">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Toast Container -->
<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
@endsection

@section('scripts')
<script>
    const proposalId = {{ $proposal->id_proposal }};
    const submitUrl = '{{ route("mahasiswa.proposal.revisi.akhir.submit", $proposal->id_proposal) }}';

    function setupFileUpload() {
        const input = document.getElementById('revisi_file');
        const area = document.getElementById('revisiAkhirUploadArea');
        const info = document.getElementById('revisiAkhirFileInfo');
        const fileName = document.getElementById('revisiAkhirFileName');
        const fileSize = document.getElementById('revisiAkhirFileSize');
        const progress = document.getElementById('revisiAkhirProgress');

        area.addEventListener('click', () => input.click());

        area.addEventListener('dragover', (e) => {
            e.preventDefault();
            area.classList.add('dragover');
        });

        area.addEventListener('dragleave', () => {
            area.classList.remove('dragover');
        });

        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                handleFileSelect();
            }
        });

        input.addEventListener('change', handleFileSelect);
    }

    function handleFileSelect() {
        const input = document.getElementById('revisi_file');
        const info = document.getElementById('revisiAkhirFileInfo');
        const fileName = document.getElementById('revisiAkhirFileName');
        const fileSize = document.getElementById('revisiAkhirFileSize');
        const progress = document.getElementById('revisiAkhirProgress');
        
        const file = input.files[0];
        if (file) {
            if (file.type !== 'application/pdf') {
                showToast('File harus berformat PDF!', 'error');
                input.value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showToast('Ukuran file maksimal 5MB!', 'error');
                input.value = '';
                return;
            }

            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            info.classList.add('show');
            simulateUpload(progress);
        }
    }

    function removeFile() {
        const input = document.getElementById('revisi_file');
        const info = document.getElementById('revisiAkhirFileInfo');
        const progress = document.getElementById('revisiAkhirProgress');
        
        input.value = '';
        info.classList.remove('show');
        progress.style.width = '0%';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function simulateUpload(progressElement) {
        let width = 0;
        const interval = setInterval(() => {
            if (width >= 100) {
                clearInterval(interval);
            } else {
                width += 5;
                progressElement.style.width = width + '%';
            }
        }, 100);
    }

    document.getElementById('revisiAkhirForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengupload...';

        const formData = new FormData(this);
        const progress = document.getElementById('revisiAkhirProgress');
        
        fetch(submitUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                progress.style.width = '100%';
                setTimeout(() => {
                    window.location.reload();
                }, 1200);
            } else {
                showToast(data.message || 'Gagal mengupload file revisi akhir', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat mengupload file', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show shadow-sm`;
        toast.style.minWidth = '300px';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.getElementById('toastContainer').appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        setupFileUpload();
    });
</script>
@endsection
