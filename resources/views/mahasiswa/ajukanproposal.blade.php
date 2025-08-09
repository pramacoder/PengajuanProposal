@extends('mainlayout.app')

@section('title', 'Pengajuan Proposal PKM')

@section('styles')
<style>
    .form-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .form-section:hover {
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
    
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(139, 58, 58, 0.25);
    }
    
    .btn-submit {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        border: none;
        padding: 1rem 3rem;
        font-weight: bold;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 58, 58, 0.4);
    }
    
    .member-group {
        border: 1px solid #dee2e6;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        transition: all 0.3s ease;
    }
    
    .member-group:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .member-title {
        color: var(--primary-color);
        font-weight: bold;
        margin-bottom: 1rem;
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
    
    .required-field::after {
        content: " *";
        color: #dc3545;
    }
    
    .form-floating {
        margin-bottom: 1rem;
    }
    
    .form-floating > .form-control {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }
    
    .form-floating > label {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        padding: 1rem 0.75rem;
        pointer-events: none;
        border: 1px solid transparent;
        transform-origin: 0 0;
        transition: opacity .1s ease-in-out,transform .1s ease-in-out;
    }
    
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        opacity: .65;
        transform: scale(.85) translateY(-0.5rem) translateX(0.15rem);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">
                <i class="fas fa-file-alt me-2"></i>
                PENGAJUAN PROPOSAL PKM
            </h2>
            <p class="text-muted mb-0">Silakan lengkapi form berikut untuk mengajukan proposal PKM. Pastikan semua data yang diperlukan telah diisi dengan benar.</p>
        </div>
        <div class="text-end">
            <img src="https://via.placeholder.com/80x80?text=Logo" alt="Logo" class="rounded">
            <div class="mt-2">
                <small class="text-muted">Tahun Ajaran: 2024/2025</small>
            </div>
        </div>
    </div>

    <form action="{{ route('mahasiswa.proposal.store') }}" method="POST" enctype="multipart/form-data" id="proposalForm">
        @csrf
        
        <!-- Informasi Proposal -->
        <div class="form-section">
            <h4 class="section-title">
                <i class="fas fa-clipboard-list me-2"></i>Informasi Proposal PKM
            </h4>
            
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label for="judul" class="form-label required-field">Judul Proposal</label>
                    <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" placeholder="Masukkan judul proposal PKM" value="{{ old('judul') }}" required>
                    <div class="form-text">Judul harus jelas, spesifik, dan mencerminkan isi proposal</div>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="skim" class="form-label required-field">Skim/Jenis PKM</label>
                    <select class="form-select" id="skim" name="skim" required>
                        <option value="">Pilih Skim</option>
                        <option value="KC">PKM-KC (Karsa Cipta)</option>
                        <option value="RE">PKM-RE (Riset Eksak)</option>
                        <option value="RSH">PKM-RSH (Riset Sosial Humaniora)</option>
                        <option value="PI">PKM-PI (Penerapan Iptek)</option>
                        <option value="PM">PKM-PM (Pengabdian Masyarakat)</option>
                        <option value="K">PKM-K (Kewirausahaan)</option>
                        <option value="VGK">PKM-VGK (Video Gagasan Konstruktif)</option>
                        <option value="GFT">PKM-GFT (Gagasan Futuristik Tertulis)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                    <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" value="2024/2025" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tanggal_pengajuan" class="form-label">Tanggal Pengajuan</label>
                    <input type="date" class="form-control" id="tanggal_pengajuan" name="tanggal_pengajuan" value="{{ date('Y-m-d') }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="dana_diajukan" class="form-label required-field">Dana yang Diajukan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="dana_diajukan" name="dana_diajukan" placeholder="0" min="0" max="15000000" required>
                    </div>
                    <div class="form-text">Maksimal Rp 15.000.000</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="dosen_pembimbing" class="form-label required-field">Dosen Pembimbing</label>
                    <input type="text" class="form-control" id="dosen_pembimbing" name="dosen_pembimbing" placeholder="Masukkan nama dosen pembimbing" required>
                </div>
            </div>
        </div>

        <!-- Identitas Ketua Tim -->
        <div class="form-section">
            <h4 class="section-title">
                <i class="fas fa-user-tie me-2"></i>Identitas Ketua Tim
            </h4>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="ketua_nama" class="form-label required-field">Nama Lengkap</label>
                    <input type="text" class="form-control" id="ketua_nama" name="ketua_nama" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ketua_nim" class="form-label required-field">NIM</label>
                    <input type="text" class="form-control" id="ketua_nim" name="ketua_nim" placeholder="Masukkan NIM" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ketua_prodi" class="form-label required-field">Program Studi</label>
                    <input type="text" class="form-control" id="ketua_prodi" name="ketua_prodi" placeholder="Masukkan program studi" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ketua_fakultas" class="form-label required-field">Fakultas</label>
                    <input type="text" class="form-control" id="ketua_fakultas" name="ketua_fakultas" placeholder="Masukkan fakultas" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ketua_email" class="form-label required-field">Email</label>
                    <input type="email" class="form-control" id="ketua_email" name="ketua_email" placeholder="Masukkan email" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ketua_no_hp" class="form-label required-field">No. HP</label>
                    <input type="tel" class="form-control" id="ketua_no_hp" name="ketua_no_hp" placeholder="Masukkan nomor HP" required>
                </div>
            </div>
        </div>

        <!-- Anggota Tim -->
        <div class="form-section">
            <h4 class="section-title">
                <i class="fas fa-users me-2"></i>Anggota Tim
            </h4>
            <p class="text-muted mb-3">Anggota tim bersifat opsional. Jika tidak ada anggota, biarkan kosong.</p>
            
            <!-- Anggota 1 -->
            <div class="member-group">
                <div class="member-title">
                    <i class="fas fa-user me-2"></i>Anggota 1
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="anggota1_nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="anggota1_nama" name="anggota1_nama" placeholder="Masukkan nama">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota1_nim" class="form-label">NIM</label>
                        <input type="text" class="form-control" id="anggota1_nim" name="anggota1_nim" placeholder="Masukkan NIM">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota1_prodi" class="form-label">Program Studi</label>
                        <input type="text" class="form-control" id="anggota1_prodi" name="anggota1_prodi" placeholder="Masukkan program studi">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota1_fakultas" class="form-label">Fakultas</label>
                        <input type="text" class="form-control" id="anggota1_fakultas" name="anggota1_fakultas" placeholder="Masukkan fakultas">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota1_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="anggota1_email" name="anggota1_email" placeholder="Masukkan email">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota1_no_hp" class="form-label">No. HP</label>
                        <input type="tel" class="form-control" id="anggota1_no_hp" name="anggota1_no_hp" placeholder="Masukkan nomor HP">
                    </div>
                </div>
            </div>

            <!-- Anggota 2 -->
            <div class="member-group">
                <div class="member-title">
                    <i class="fas fa-user me-2"></i>Anggota 2
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="anggota2_nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="anggota2_nama" name="anggota2_nama" placeholder="Masukkan nama">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota2_nim" class="form-label">NIM</label>
                        <input type="text" class="form-control" id="anggota2_nim" name="anggota2_nim" placeholder="Masukkan NIM">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota2_prodi" class="form-label">Program Studi</label>
                        <input type="text" class="form-control" id="anggota2_prodi" name="anggota2_prodi" placeholder="Masukkan program studi">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota2_fakultas" class="form-label">Fakultas</label>
                        <input type="text" class="form-control" id="anggota2_fakultas" name="anggota2_fakultas" placeholder="Masukkan fakultas">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota2_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="anggota2_email" name="anggota2_email" placeholder="Masukkan email">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota2_no_hp" class="form-label">No. HP</label>
                        <input type="tel" class="form-control" id="anggota2_no_hp" name="anggota2_no_hp" placeholder="Masukkan nomor HP">
                    </div>
                </div>
            </div>

            <!-- Anggota 3 -->
            <div class="member-group">
                <div class="member-title">
                    <i class="fas fa-user me-2"></i>Anggota 3
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="anggota3_nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="anggota3_nama" name="anggota3_nama" placeholder="Masukkan nama">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota3_nim" class="form-label">NIM</label>
                        <input type="text" class="form-control" id="anggota3_nim" name="anggota3_nim" placeholder="Masukkan NIM">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota3_prodi" class="form-label">Program Studi</label>
                        <input type="text" class="form-control" id="anggota3_prodi" name="anggota3_prodi" placeholder="Masukkan program studi">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota3_fakultas" class="form-label">Fakultas</label>
                        <input type="text" class="form-control" id="anggota3_fakultas" name="anggota3_fakultas" placeholder="Masukkan fakultas">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota3_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="anggota3_email" name="anggota3_email" placeholder="Masukkan email">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota3_no_hp" class="form-label">No. HP</label>
                        <input type="tel" class="form-control" id="anggota3_no_hp" name="anggota3_no_hp" placeholder="Masukkan nomor HP">
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Dokumen -->
        <div class="form-section">
            <h4 class="section-title">
                <i class="fas fa-upload me-2"></i>Upload Dokumen
            </h4>
            
            <!-- Upload Proposal -->
            <div class="mb-4">
                <label class="form-label required-field">File Proposal (PDF)</label>
                <div class="file-upload-area" id="proposalUploadArea">
                    <div class="file-upload-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <h5>Upload File Proposal</h5>
                    <p class="text-muted">Drag & drop file PDF di sini atau klik untuk memilih file</p>
                    <input type="file" id="proposal_file" name="proposal_file" accept=".pdf" style="display: none;" required>
                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('proposal_file').click()">
                        <i class="fas fa-folder-open me-2"></i>Pilih File
                    </button>
                </div>
                <div class="file-info" id="proposalFileInfo">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong id="proposalFileName">Nama file</strong>
                            <br><small id="proposalFileSize">Ukuran file</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile('proposal')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" id="proposalProgress"></div>
                    </div>
                </div>
            </div>

            <!-- Upload Persetujuan Dosen -->
            <div class="mb-4">
                <label class="form-label required-field">File Persetujuan Dosen (PDF)</label>
                <div class="file-upload-area" id="persetujuanUploadArea">
                    <div class="file-upload-icon">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h5>Upload File Persetujuan</h5>
                    <p class="text-muted">Drag & drop file PDF di sini atau klik untuk memilih file</p>
                    <input type="file" id="persetujuan_file" name="persetujuan_file" accept=".pdf" style="display: none;" required>
                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('persetujuan_file').click()">
                        <i class="fas fa-folder-open me-2"></i>Pilih File
                    </button>
                </div>
                <div class="file-info" id="persetujuanFileInfo">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong id="persetujuanFileName">Nama file</strong>
                            <br><small id="persetujuanFileSize">Ukuran file</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile('persetujuan')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" id="persetujuanProgress"></div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Ketentuan Upload:</strong>
                <ul class="mb-0 mt-2">
                    <li>Format file harus PDF</li>
                    <li>Ukuran maksimal 5MB per file</li>
                    <li>File proposal harus lengkap sesuai template</li>
                    <li>File persetujuan harus ditandatangani oleh dosen pembimbing</li>
                </ul>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-submit" id="submitBtn">
                <i class="fas fa-paper-plane me-2"></i>Ajukan Proposal
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // File upload handling
    function setupFileUpload(inputId, areaId, infoId, fileNameId, fileSizeId, progressId) {
        const input = document.getElementById(inputId);
        const area = document.getElementById(areaId);
        const info = document.getElementById(infoId);
        const fileName = document.getElementById(fileNameId);
        const fileSize = document.getElementById(fileSizeId);
        const progress = document.getElementById(progressId);

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
                handleFileSelect(input, info, fileName, fileSize, progress);
            }
        });

        // File selection
        input.addEventListener('change', () => {
            handleFileSelect(input, info, fileName, fileSize, progress);
        });
    }

    function handleFileSelect(input, info, fileName, fileSize, progress) {
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

    function removeFile(type) {
        const input = document.getElementById(type + '_file');
        const info = document.getElementById(type + 'FileInfo');
        const progress = document.getElementById(type + 'Progress');
        
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

    // Form validation
    document.getElementById('proposalForm').addEventListener('submit', function(e) {
        const requiredFields = [
            'judul', 'skim', 'dana_diajukan', 'dosen_pembimbing',
            'ketua_nama', 'ketua_nim', 'ketua_prodi', 'ketua_fakultas', 
            'ketua_email', 'ketua_no_hp'
        ];

        // Check required fields
        for (let field of requiredFields) {
            const element = document.getElementById(field);
            if (!element.value.trim()) {
                e.preventDefault();
                showToast(`Mohon lengkapi field ${element.placeholder || field}!`, 'error');
                element.focus();
                return false;
            }
        }

        // Check file uploads
        const proposalFile = document.getElementById('proposal_file');
        const persetujuanFile = document.getElementById('persetujuan_file');
        
        if (!proposalFile.files[0]) {
            e.preventDefault();
            showToast('Mohon upload file proposal!', 'error');
            return false;
        }

        if (!persetujuanFile.files[0]) {
            e.preventDefault();
            showToast('Mohon upload file persetujuan dosen!', 'error');
            return false;
        }

        // Validate NIM format
        const ketuaNim = document.getElementById('ketua_nim').value.trim();
        if (ketuaNim.length < 8) {
            e.preventDefault();
            showToast('NIM harus minimal 8 digit!', 'error');
            return false;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const ketuaEmail = document.getElementById('ketua_email').value.trim();
        if (!emailRegex.test(ketuaEmail)) {
            e.preventDefault();
            showToast('Format email tidak valid!', 'error');
            return false;
        }

        // Validate phone number
        const ketuaNoHp = document.getElementById('ketua_no_hp').value.trim();
        if (ketuaNoHp.length < 10) {
            e.preventDefault();
            showToast('Nomor HP harus minimal 10 digit!', 'error');
            return false;
        }

        // Validate dana
        const dana = document.getElementById('dana_diajukan').value;
        if (dana > 15000000) {
            e.preventDefault();
            showToast('Dana yang diajukan maksimal Rp 15.000.000!', 'error');
            return false;
        }

        // Validate anggota tim (if any field is filled, all must be filled)
        const anggotaFields = ['anggota1', 'anggota2', 'anggota3'];
        for (let anggota of anggotaFields) {
            const nama = document.getElementById(anggota + '_nama').value.trim();
            const nim = document.getElementById(anggota + '_nim').value.trim();
            
            if (nama && !nim) {
                e.preventDefault();
                showToast(`Jika mengisi nama ${anggota}, NIM juga harus diisi!`, 'error');
                return false;
            }
            if (!nama && nim) {
                e.preventDefault();
                showToast(`Jika mengisi NIM ${anggota}, nama juga harus diisi!`, 'error');
                return false;
            }
        }

        // Confirmation
        if (!confirm('Apakah Anda yakin ingin mengajukan proposal ini? Data yang sudah disubmit tidak dapat diubah.')) {
            e.preventDefault();
            return false;
        }

        // Show loading
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<span class="loading-spinner me-2"></span>Mengajukan...';
        submitBtn.disabled = true;

        showToast('Proposal sedang diajukan...', 'info');
    });

    // Auto-fill data mahasiswa yang login
    document.addEventListener('DOMContentLoaded', function() {
        // Setup file upload areas
        setupFileUpload('proposal_file', 'proposalUploadArea', 'proposalFileInfo', 'proposalFileName', 'proposalFileSize', 'proposalProgress');
        setupFileUpload('persetujuan_file', 'persetujuanUploadArea', 'persetujuanFileInfo', 'persetujuanFileName', 'persetujuanFileSize', 'persetujuanProgress');

        // Auto-fill data if available
        @if(isset($user))
            document.getElementById('ketua_nama').value = '{{ $user->nama_mhs ?? "" }}';
            document.getElementById('ketua_nim').value = '{{ $user->nim_mhs ?? "" }}';
            document.getElementById('ketua_prodi').value = '{{ $user->prodi_mhs ?? "" }}';
            document.getElementById('ketua_fakultas').value = '{{ $user->fakultas_mhs ?? "" }}';
            document.getElementById('ketua_email').value = '{{ $user->email_mhs ?? "" }}';
            document.getElementById('ketua_no_hp').value = '{{ $user->no_hp_mhs ?? "" }}';
        @endif

        // Format currency input
        const danaInput = document.getElementById('dana_diajukan');
        danaInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value > 15000000) {
                value = 15000000;
            }
            this.value = value;
        });
    });

    // Show toast notification (inherited from layout)
    function showToast(message, type = 'info') {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else {
            alert(message);
        }
    }
</script>
@endsection 