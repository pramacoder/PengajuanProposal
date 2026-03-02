@extends('mainlayout.app')

@section('title', 'Revisi Akhir Proposal PKM')

@section('styles')
<style>
    .revisi-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .revisi-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .section-title {
        color: var(--primary-color);
        font-weight: bold;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color);
        display: flex;
        align-items: center;
    }
    
    .dosen-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .dosen-info-card h5 {
        color: white;
        margin-bottom: 1rem;
    }
    
    .dosen-info-card .info-item {
        margin-bottom: 0.75rem;
    }
    
    .dosen-info-card .info-item i {
        width: 30px;
        margin-right: 10px;
    }
    
    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
        cursor: pointer;
    }
    
    .file-upload-area:hover {
        border-color: var(--primary-color);
        background-color: rgba(139, 58, 58, 0.05);
    }
    
    .file-upload-area.dragover {
        border-color: var(--primary-color);
        background-color: rgba(139, 58, 58, 0.1);
        transform: scale(1.02);
    }
    
    .file-upload-icon {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }
    
    .file-info {
        background-color: #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        display: none;
    }
    
    .file-info.show {
        display: block;
    }
    
    .progress-bar-custom {
        height: 8px;
        border-radius: 4px;
        background-color: #e9ecef;
        overflow: hidden;
        margin-top: 0.5rem;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        width: 0%;
        transition: width 0.3s ease;
    }
    
    .revisi-item {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .revisi-item:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .file-icon {
        font-size: 2rem;
        color: #dc3545;
        margin-right: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
        ['label' => 'Revisi Akhir', 'active' => true],
    ]" />

    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="mb-4">
                <h2 class="mb-2">
                    <i class="fas fa-edit me-2"></i>
                    Revisi Akhir Proposal PKM
                </h2>
                <p class="text-muted">
                    Upload file revisi akhir proposal untuk divalidasi oleh dosen pendamping universitas
                </p>
            </div>

            <!-- Informasi Dosen Pendamping Universitas -->
            @if($proposal->dosenPendampingUniversitas)
            <div class="dosen-info-card">
                <h5>
                    <i class="fas fa-user-tie me-2"></i>
                    Dosen Pendamping Universitas
                </h5>
                <div class="info-item">
                    <i class="fas fa-user"></i>
                    <strong>Nama:</strong> {{ $proposal->dosenPendampingUniversitas->nama_dosen }}
                    @if($proposal->dosenPendampingUniversitas->gelar_depan)
                        , {{ $proposal->dosenPendampingUniversitas->gelar_depan }}
                    @endif
                    @if($proposal->dosenPendampingUniversitas->gelar_belakang)
                        , {{ $proposal->dosenPendampingUniversitas->gelar_belakang }}
                    @endif
                </div>
                @if($proposal->dosenPendampingUniversitas->no_hp_dosen)
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <strong>No. HP:</strong> 
                    <a href="tel:{{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}" class="text-white" style="text-decoration: underline;">
                        {{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}
                    </a>
                </div>
                @endif
                @if($proposal->dosenPendampingUniversitas->email_dosen)
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <strong>Email:</strong> 
                    <a href="mailto:{{ $proposal->dosenPendampingUniversitas->email_dosen }}" class="text-white" style="text-decoration: underline;">
                        {{ $proposal->dosenPendampingUniversitas->email_dosen }}
                    </a>
                </div>
                @endif
                <div class="mt-3">
                    <small class="text-white-50">
                        <i class="fas fa-info-circle me-1"></i>
                        Anda dapat menghubungi dosen pendamping universitas untuk konsultasi sebelum mengupload revisi akhir.
                    </small>
                </div>
            </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Peringatan:</strong> Dosen pendamping universitas belum ditetapkan. Silakan hubungi operator.
            </div>
            @endif

            <!-- Informasi Proposal -->
            <div class="revisi-section">
                <h4 class="section-title">
                    <i class="fas fa-file-alt me-2"></i>Informasi Proposal
                </h4>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Judul Proposal:</strong></p>
                        <p class="text-primary">{{ $proposal->judul_proposal }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Skim:</strong> <span class="badge bg-primary">{{ $proposal->skim }}</span></p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $proposal->status)) }}</span>
                        </p>
                    </div>
                </div>
                @if($proposal->hasilSemiFinal && $proposal->hasilSemiFinal->catatan_final)
                <div class="mt-3">
                    <p><strong>Catatan Hasil Semi Final:</strong></p>
                    <div class="alert alert-info">
                        {{ $proposal->hasilSemiFinal->catatan_final }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Upload File Revisi Akhir -->
            <div class="revisi-section">
                <h4 class="section-title">
                    <i class="fas fa-upload me-2"></i>Upload File Revisi Akhir
                </h4>
                
                <form id="revisiAkhirForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="file-upload-area" id="revisiAkhirUploadArea">
                        <div class="file-upload-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h5>Upload File Revisi Akhir Proposal</h5>
                        <p class="text-muted">Drag & drop file PDF di sini atau klik untuk memilih file</p>
                        <input type="file" id="revisi_file" name="revisi_file" accept=".pdf" style="display: none;" required>
                        <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('revisi_file').click()">
                            <i class="fas fa-folder-open me-2"></i>Pilih File
                        </button>
                    </div>
                    
                    <div class="file-info" id="revisiAkhirFileInfo">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong id="revisiAkhirFileName">Nama file</strong>
                                <br><small id="revisiAkhirFileSize">Ukuran file</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="progress-bar-custom">
                            <div class="progress-fill" id="revisiAkhirProgress"></div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Ketentuan Upload:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Format file harus PDF</li>
                            <li>Ukuran maksimal 5MB</li>
                            <li>File harus berisi proposal yang sudah direvisi sesuai catatan hasil semi final</li>
                            <li>File revisi akhir akan divalidasi oleh dosen pendamping universitas</li>
                        </ul>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="fas fa-upload me-2"></i>Upload File Revisi Akhir
                        </button>
                    </div>
                </form>
            </div>

            <!-- Daftar File Revisi Akhir -->
            @if($revisiAkhir->count() > 0)
            <div class="revisi-section">
                <h4 class="section-title">
                    <i class="fas fa-history me-2"></i>Daftar File Revisi Akhir yang Sudah Diupload
                </h4>
                
                @foreach($revisiAkhir as $revisi)
                <div class="revisi-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="file-icon fas fa-file-pdf"></i>
                            <div>
                                <h6 class="mb-1">{{ $revisi->nama_file }}</h6>
                                <small class="text-muted">
                                    Diupload: {{ \Carbon\Carbon::parse($revisi->tanggal_submit)->format('d M Y H:i') }}
                                </small>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('mahasiswa.revisi.download', $revisi->id_revisi) }}" 
                               class="btn btn-sm btn-outline-primary" 
                               target="_blank">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toastContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

@endsection

@section('scripts')
<script>
    const proposalId = {{ $proposal->id_proposal }};
    const submitUrl = '{{ route("mahasiswa.proposal.revisi.akhir.submit", $proposal->id_proposal) }}';

    // File upload handling
    function setupFileUpload() {
        const input = document.getElementById('revisi_file');
        const area = document.getElementById('revisiAkhirUploadArea');
        const info = document.getElementById('revisiAkhirFileInfo');
        const fileName = document.getElementById('revisiAkhirFileName');
        const fileSize = document.getElementById('revisiAkhirFileSize');
        const progress = document.getElementById('revisiAkhirProgress');

        // Click to upload
        area.addEventListener('click', () => input.click());

        // Drag and drop
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

        // File selection
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
            // Validate file type
            if (file.type !== 'application/pdf') {
                showToast('File harus berformat PDF!', 'error');
                input.value = '';
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showToast('Ukuran file maksimal 5MB!', 'error');
                input.value = '';
                return;
            }

            // Display file info
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            info.classList.add('show');

            // Simulate upload progress
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

    // Form submission
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
                }, 1500);
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
        toast.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show`;
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

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        setupFileUpload();
    });
</script>
@endsection



