@extends('mainlayout.app')

@section('title', 'Revisi Proposal PKM')

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
        color: var(--primary-color);
    }
    
    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .btn-download {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
    }
    
    .btn-download:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        color: white;
    }
    
    .btn-delete {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        color: white;
    }
    
    .btn-delete:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        color: white;
    }
    
    .status-badge {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
    }
    
    .alert-info {
        border-left: 4px solid var(--primary-color);
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <x-page-header 
        title="Revisi Proposal PKM" 
    />
    <!-- Status Perbaikan -->
    <div class="alert alert-info">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle me-3 fa-2x"></i>
            <div>
                <h5 class="mb-1">Status Perbaikan: 
                    <span class="badge bg-success status-badge">
                        {{ ucfirst($ruangKontrol ? $ruangKontrol->status_perbaikan : 'tertutup') }}
                    </span>
                </h5>
                @if(!$ruangKontrol)
                    <p class="mb-0 text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Ruang kontrol tidak ditemukan untuk tahun ajaran ini.</strong>
                    </p>
                @endif
                @if($ruangKontrol && $ruangKontrol->tanggal_perbaikan_mulai && $ruangKontrol->tanggal_perbaikan_selesai)
                    <p class="mb-0">
                        Periode: {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_mulai)->format('d M Y') }} - 
                        {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai)->format('d M Y') }}
                    </p>
                    @php
                        $deadline = \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai);
                        $daysLeft = (int) now()->diffInDays($deadline, false);
                    @endphp
                    @if($daysLeft > 0)
                        <p class="mb-0 text-warning">
                            <i class="fas fa-clock me-1"></i>
                            <strong>Sisa waktu: {{ $daysLeft }} hari</strong>
                        </p>
                    @elseif($daysLeft == 0)
                        <p class="mb-0 text-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Hari terakhir!</strong>
                        </p>
                    @else
                        <p class="mb-0 text-danger">
                            <i class="fas fa-times-circle me-1"></i>
                            <strong>Batas waktu telah terlampaui {{ abs($daysLeft) }} hari</strong>
                        </p>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Informasi Proposal -->
    <div class="revisi-section">
        <h4 class="section-title">
            <i class="fas fa-clipboard-list me-2"></i>Informasi Proposal
        </h4>
        
        <div class="row">
            <div class="col-md-8">
                <h5 class="text-primary">{{ $proposal->judul_proposal }}</h5>
                <p class="text-muted mb-2">
                    <strong>Skim:</strong> {{ $proposal->skim }} | 
                    <strong>Dana:</strong> Rp {{ number_format($proposal->dana_diajukan, 0, ',', '.') }}
                </p>
                <p class="text-muted mb-0">
                    <strong>Status:</strong> 
                    <span class="badge bg-warning">{{ ucfirst($proposal->status) }}</span>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>
                    Diajukan: {{ \Carbon\Carbon::parse($proposal->created_at)->format('d M Y H:i') }}
                </small>
            </div>
        </div>
    </div>

    <!-- Catatan Review -->
    <div class="revisi-section">
        <h4 class="section-title">
            <i class="fas fa-comments me-2"></i>Catatan Review dari Reviewer
        </h4>
        
        @php
            $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', $proposal->id_reviewer_administratif)->first();
            $substantifReviews = $proposal->nilaiSubstantif;
        @endphp
        
        @if($adminReview && $adminReview->note_administratif)
        <div class="mb-4">
            <h6 class="text-primary">
                <i class="fas fa-user-tie me-2"></i>Review Administratif
            </h6>
            <div class="alert alert-light border-start border-primary border-4">
                <p class="mb-0">{{ $adminReview->note_administratif }}</p>
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    {{ \Carbon\Carbon::parse($adminReview->updated_at)->format('d M Y H:i') }}
                </small>
            </div>
        </div>
        @endif
        
        @if($substantifReviews->count() > 0)
        <div class="mb-4">
            <h6 class="text-primary">
                <i class="fas fa-user-graduate me-2"></i>Review Substantif
            </h6>
            @foreach($substantifReviews as $review)
                @if($review->note_substantif)
                <div class="alert alert-light border-start border-info border-4 mb-3">
                    <p class="mb-0">{{ $review->note_substantif }}</p>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        {{ \Carbon\Carbon::parse($review->updated_at)->format('d M Y H:i') }}
                    </small>
                </div>
                @endif
            @endforeach
        </div>
        @endif
        
        @if((!$adminReview || !$adminReview->note_administratif) && $substantifReviews->where('note_substantif')->count() == 0)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Belum ada catatan review yang tersedia. Silakan tunggu hingga reviewer menyelesaikan review mereka.
        </div>
        @endif
    </div>

    <!-- Upload File Revisi -->
    <div class="revisi-section">
        <h4 class="section-title">
            <i class="fas fa-upload me-2"></i>Upload File Revisi
        </h4>
        
        <form action="{{ route('mahasiswa.revisi.store') }}" method="POST" enctype="multipart/form-data" id="revisiForm">
            @csrf
            
            <div class="file-upload-area" id="revisiUploadArea">
                <div class="file-upload-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <h5>Upload File Revisi Proposal</h5>
                <p class="text-muted">Drag & drop file PDF di sini atau klik untuk memilih file</p>
                <input type="file" id="file_revisi" name="file_revisi" accept=".pdf" style="display: none;" required>
                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('file_revisi').click()">
                    <i class="fas fa-folder-open me-2"></i>Pilih File
                </button>
            </div>
            
            <div class="file-info" id="revisiFileInfo">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong id="revisiFileName">Nama file</strong>
                        <br><small id="revisiFileSize">Ukuran file</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="progress-bar-custom">
                    <div class="progress-fill" id="revisiProgress"></div>
                </div>
            </div>
            
            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Ketentuan Upload:</strong>
                <ul class="mb-0 mt-2">
                    <li>Format file harus PDF</li>
                    <li>Ukuran maksimal 5MB</li>
                    <li>File harus berisi proposal yang sudah direvisi sesuai catatan reviewer</li>
                </ul>
            </div>
            
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                    <i class="fas fa-upload me-2"></i>Upload File Revisi
                </button>
            </div>
        </form>
    </div>

    <!-- Daftar File Revisi -->
    @if($revisi->count() > 0)
    <div class="revisi-section">
        <h4 class="section-title">
            <i class="fas fa-history me-2"></i>Riwayat File Revisi
        </h4>
        
        <div class="row">
            @foreach($revisi as $item)
            <div class="col-md-6 mb-3">
                <div class="revisi-item">
                    <div class="d-flex align-items-start">
                        <div class="file-icon me-3">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $item->nama_file }}</h6>
                            <p class="text-muted mb-2">
                                <small>
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ \Carbon\Carbon::parse($item->tanggal_submit)->format('d M Y H:i') }}
                                </small>
                            </p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('mahasiswa.revisi.download', $item->id_revisi) }}" 
                                   class="btn btn-download btn-action">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                                <button type="button" class="btn btn-delete btn-action" 
                                        onclick="deleteRevisi({{ $item->id_revisi }})">
                                    <i class="fas fa-trash me-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tombol Kembali -->
    <div class="text-center mt-4">
        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus file revisi ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let deleteId = null;

    // File upload handling
    function setupFileUpload() {
        const input = document.getElementById('file_revisi');
        const area = document.getElementById('revisiUploadArea');
        const info = document.getElementById('revisiFileInfo');
        const fileName = document.getElementById('revisiFileName');
        const fileSize = document.getElementById('revisiFileSize');
        const progress = document.getElementById('revisiProgress');

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
        const input = document.getElementById('file_revisi');
        const info = document.getElementById('revisiFileInfo');
        const fileName = document.getElementById('revisiFileName');
        const fileSize = document.getElementById('revisiFileSize');
        const progress = document.getElementById('revisiProgress');
        
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
        const input = document.getElementById('file_revisi');
        const info = document.getElementById('revisiFileInfo');
        const progress = document.getElementById('revisiProgress');
        
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
                width++;
                progressElement.style.width = width + '%';
            }
        }, 20);
    }

    function deleteRevisi(id) {
        deleteId = id;
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (deleteId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/mahasiswa/revisi/${deleteId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });

    // Form submission
    document.getElementById('revisiForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengupload...';
        submitBtn.disabled = true;
    });

    // Show toast notification
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info'} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            <strong>${type === 'error' ? 'Error' : type === 'success' ? 'Sukses' : type === 'warning' ? 'Peringatan' : 'Info'}:</strong> ${message}
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        setupFileUpload();
    });
</script>
@endsection