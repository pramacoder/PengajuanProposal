@extends('mainlayout.app')

@section('title', 'Pengajuan Proposal PKM')

@if(isset($statusPendaftaran) && $statusPendaftaran === 'tertutup')
    @section('content')
    <div class="container mt-5">
        <x-breadcrumb :items="[
            ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
            ['label' => 'Ajukan Proposal', 'active' => true],
        ]" />

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-lock me-2"></i>
                            Sistem Pendaftaran Ditutup
                        </h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-calendar-times fa-4x text-danger mb-3"></i>
                            <h5 class="text-danger">Pendaftaran PKM Sedang Ditutup</h5>
                            <p class="text-muted">
                                Saat ini sistem pendaftaran proposal PKM sedang ditutup oleh operator.
                                Silakan cek kembali nanti atau hubungi operator untuk informasi lebih lanjut.
                            </p>
                        </div>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-outline-primary">
                                <i class="fas fa-home me-2"></i>
                                Kembali ke Dashboard
                            </a>
                            <a href="{{ route('mahasiswa.proposal.index') }}" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>
                                Lihat Proposal Saya
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
@else

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

    .error-highlight {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: none;
    }

    .error-message.show {
        display: block;
    }

    .btn-submit:disabled {
        background: #6c757d;
        border-color: #6c757d;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .btn-submit:disabled:hover {
        transform: none;
        box-shadow: none;
    }

    .submit-help-text {
        transition: all 0.3s ease;
    }

    .submit-help-text.ready {
        color: #28a745;
    }

    .submit-help-text.incomplete {
        color: #dc3545;
    }
</style>
@endsection

@endif

@section('content')
<div class="container-fluid">
                <x-breadcrumb :items="[
                    ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
                    ['label' => 'Ajukan Proposal', 'active' => true],
                ]" />

                <x-page-header 
                    title="Ajukan Proposal PKM" 
                    subtitle="UNIVERSITAS UDAYANA" />

    <!-- Error Alert -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Terdapat Kesalahan dalam Form
            </h5>
            <p class="mb-2">Mohon perbaiki kesalahan berikut sebelum melanjutkan:</p>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Special Error Messages for Duplicate NIMs -->
    @if($errors->has('nim_duplicate'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">
                <i class="fas fa-user-times me-2"></i>
                NIM Sudah Terdaftar dalam Proposal Lain
            </h5>
            <p class="mb-2">Beberapa anggota tim sudah terdaftar dalam proposal lain:</p>
            <ul class="mb-0">
                @foreach($errors->get('nim_duplicate') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->has('team_nim'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">
                <i class="fas fa-users-slash me-2"></i>
                NIM Duplikat dalam Tim
            </h5>
            <p class="mb-2">Terdapat NIM yang sama dalam satu tim:</p>
            <ul class="mb-0">
                @foreach($errors->get('team_nim') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->has('team_size'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">
                <i class="fas fa-users me-2"></i>
                Jumlah Anggota Tim Tidak Sesuai
            </h5>
            <p class="mb-2">Jumlah anggota tim tidak memenuhi ketentuan:</p>
            <ul class="mb-0">
                @foreach($errors->get('team_size') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->has('optional_members'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">
                <i class="fas fa-user-plus me-2"></i>
                Data Anggota Opsional Tidak Lengkap
            </h5>
            <p class="mb-2">Jika mengisi data anggota opsional, semua field harus diisi:</p>
            <ul class="mb-0">
                @foreach($errors->get('optional_members') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Warning Message -->
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                    <div class="error-message" id="judul_error"></div>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="skim" class="form-label required-field">Skim/Jenis PKM</label>
                    <select class="form-select @error('skim') is-invalid @enderror" id="skim" name="skim" required>
                        <option value="">Pilih Skim</option>
                        <optgroup label="PKM Pendanaan">
                            <option value="RE" {{ old('skim') == 'RE' ? 'selected' : '' }}>PKM-RE (Riset Eksak)</option>
                            <option value="RSH" {{ old('skim') == 'RSH' ? 'selected' : '' }}>PKM-RSH (Riset Sosial Humaniora)</option>
                            <option value="K" {{ old('skim') == 'K' ? 'selected' : '' }}>PKM-K (Kewirausahaan)</option>
                            <option value="PM" {{ old('skim') == 'PM' ? 'selected' : '' }}>PKM-PM (Pengabdian Masyarakat)</option>
                            <option value="PI" {{ old('skim') == 'PI' ? 'selected' : '' }}>PKM-PI (Penerapan Iptek)</option>
                            <option value="KC" {{ old('skim') == 'KC' ? 'selected' : '' }}>PKM-KC (Karsa Cipta)</option>
                            <option value="KI" {{ old('skim') == 'KI' ? 'selected' : '' }}>PKM-KI (Karsa Ilmiah)</option>
                            <option value="VGK" {{ old('skim') == 'VGK' ? 'selected' : '' }}>PKM-VGK (Video Gagasan Konstruktif)</option>
                        </optgroup>
                        <optgroup label="PKM Insentif">
                            <option value="GFT" {{ old('skim') == 'GFT' ? 'selected' : '' }}>PKM-GFT (Gagasan Futuristik Tertulis)</option>
                            <option value="AI" {{ old('skim') == 'AI' ? 'selected' : '' }}>PKM-AI (Artikel Ilmiah)</option>
                        </optgroup>
                    </select>
                    <div class="error-message" id="skim_error"></div>
                    @error('skim')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                    <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" value="{{ \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru() }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tanggal_pengajuan" class="form-label">Tanggal Pengajuan</label>
                    <input type="date" class="form-control" id="tanggal_pengajuan" name="tanggal_pengajuan" value="{{ date('Y-m-d') }}" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="dana_diajukan" class="form-label required-field">Dana yang Diajukan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control js-format-id-int @error('dana_diajukan') is-invalid @enderror" id="dana_diajukan" name="dana_diajukan" placeholder="0" inputmode="numeric" data-max="15000000" value="{{ old('dana_diajukan') }}" required>
                    </div>
                    <div class="form-text" id="dana_help_text">Maksimal Rp 15.000.000</div>
                    <div class="error-message" id="dana_diajukan_error"></div>
                    @error('dana_diajukan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="dosen_pembimbing" class="form-label required-field">Dosen Pendamping</label>
                    <select class="form-control @error('dosen_pembimbing') is-invalid @enderror" id="dosen_pembimbing" name="dosen_pembimbing" required>
                        <option value="">-- Pilih Dosen Pendamping --</option>
                        @foreach($dosens as $dosen)
                            <option value="{{ $dosen->nama_dosen }}" {{ old('dosen_pembimbing') == $dosen->nama_dosen ? 'selected' : '' }}>{{ $dosen->nama_dosen }}{{ $dosen->gelar_belakang ? ', '.$dosen->gelar_belakang : '' }}</option>
                        @endforeach
                    </select>
                    <div class="error-message" id="dosen_pembimbing_error"></div>
                    @error('dosen_pembimbing')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                    <input type="text" class="form-control @error('ketua_nama') is-invalid @enderror" id="ketua_nama" name="ketua_nama" placeholder="Masukkan nama lengkap" value="{{ old('ketua_nama') }}" required>
                    <div class="error-message" id="ketua_nama_error"></div>
                    @error('ketua_nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ketua_nim" class="form-label required-field">NIM</label>
                    <input type="text" class="form-control @error('ketua_nim') is-invalid @enderror" id="ketua_nim" name="ketua_nim" placeholder="Masukkan NIM" value="{{ old('ketua_nim') }}" required>
                    <div class="error-message" id="ketua_nim_error"></div>
                    @error('ketua_nim')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ketua_fakultas" class="form-label required-field">Fakultas</label>
                    <select class="form-select @error('ketua_fakultas') is-invalid @enderror" id="ketua_fakultas" name="ketua_fakultas" required>
                        <option value="">-- Pilih Fakultas --</option>
                        @foreach($fakultas as $fak)
                            <option value="{{ $fak->nama_fakultas }}" {{ old('ketua_fakultas') == $fak->nama_fakultas ? 'selected' : '' }}>{{ $fak->nama_fakultas }}</option>
                        @endforeach
                    </select>
                    <div class="error-message" id="ketua_fakultas_error"></div>
                    @error('ketua_fakultas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ketua_prodi" class="form-label required-field">Program Studi</label>
                    <select class="form-select @error('ketua_prodi') is-invalid @enderror" id="ketua_prodi" name="ketua_prodi" required>
                        <option value="">-- Pilih Program Studi --</option>
                    </select>
                    <div class="error-message" id="ketua_prodi_error"></div>
                    @error('ketua_prodi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ketua_email" class="form-label required-field">Email</label>
                    <input type="email" class="form-control @error('ketua_email') is-invalid @enderror" id="ketua_email" name="ketua_email" placeholder="Masukkan email" value="{{ old('ketua_email') }}" required>
                    <div class="error-message" id="ketua_email_error"></div>
                    @error('ketua_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ketua_no_hp" class="form-label required-field">No. HP</label>
                    <input type="tel" class="form-control @error('ketua_no_hp') is-invalid @enderror" id="ketua_no_hp" name="ketua_no_hp" placeholder="Masukkan nomor HP" value="{{ old('ketua_no_hp') }}" required>
                    <div class="error-message" id="ketua_no_hp_error"></div>
                    @error('ketua_no_hp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Anggota Tim -->
        <div class="form-section">
            <h4 class="section-title">
                <i class="fas fa-users me-2"></i>Anggota Tim
            </h4>
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Ketentuan Anggota Tim:</strong>
                <ul class="mb-0 mt-2">
                    <li><strong>Total Tim:</strong> Minimal 3 orang, maksimal 5 orang</li>
                </ul>
            </div>
            
            <!-- Anggota 1 -->
            <div class="member-group">
                <div class="member-title">
                    <i class="fas fa-user me-2"></i>Anggota 1 <span class="badge bg-danger ms-2">Wajib</span>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="anggota1_nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('anggota1_nama') is-invalid @enderror" id="anggota1_nama" name="anggota1_nama" placeholder="Masukkan nama" value="{{ old('anggota1_nama') }}">
                        <div class="error-message" id="anggota1_nama_error"></div>
                        @error('anggota1_nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota1_nim" class="form-label">NIM</label>
                        <input type="text" class="form-control @error('anggota1_nim') is-invalid @enderror" id="anggota1_nim" name="anggota1_nim" placeholder="Masukkan NIM" value="{{ old('anggota1_nim') }}">
                        <div class="error-message" id="anggota1_nim_error"></div>
                        @error('anggota1_nim')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota1_fakultas" class="form-label">Fakultas</label>
                        <select class="form-select @error('anggota1_fakultas') is-invalid @enderror" id="anggota1_fakultas" name="anggota1_fakultas">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $fak)
                                <option value="{{ $fak->nama_fakultas }}" {{ old('anggota1_fakultas') == $fak->nama_fakultas ? 'selected' : '' }}>{{ $fak->nama_fakultas }}</option>
                            @endforeach
                        </select>
                        <div class="error-message" id="anggota1_fakultas_error"></div>
                        @error('anggota1_fakultas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota1_prodi" class="form-label">Program Studi</label>
                        <select class="form-select @error('anggota1_prodi') is-invalid @enderror" id="anggota1_prodi" name="anggota1_prodi">
                            <option value="">-- Pilih Program Studi --</option>
                        </select>
                        <div class="error-message" id="anggota1_prodi_error"></div>
                        @error('anggota1_prodi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota1_email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('anggota1_email') is-invalid @enderror" id="anggota1_email" name="anggota1_email" placeholder="Masukkan email" value="{{ old('anggota1_email') }}">
                        <div class="error-message" id="anggota1_email_error"></div>
                        @error('anggota1_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota1_no_hp" class="form-label">No. HP</label>
                        <input type="tel" class="form-control @error('anggota1_no_hp') is-invalid @enderror" id="anggota1_no_hp" name="anggota1_no_hp" placeholder="Masukkan nomor HP" value="{{ old('anggota1_no_hp') }}">
                        <div class="error-message" id="anggota1_no_hp_error"></div>
                        @error('anggota1_no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Anggota 2 -->
            <div class="member-group">
                <div class="member-title">
                    <i class="fas fa-user me-2"></i>Anggota 2 <span class="badge bg-danger ms-2">Wajib</span>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="anggota2_nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('anggota2_nama') is-invalid @enderror" id="anggota2_nama" name="anggota2_nama" placeholder="Masukkan nama" value="{{ old('anggota2_nama') }}">
                        <div class="error-message" id="anggota2_nama_error"></div>
                        @error('anggota2_nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota2_nim" class="form-label">NIM</label>
                        <input type="text" class="form-control @error('anggota2_nim') is-invalid @enderror" id="anggota2_nim" name="anggota2_nim" placeholder="Masukkan NIM" value="{{ old('anggota2_nim') }}">
                        <div class="error-message" id="anggota2_nim_error"></div>
                        @error('anggota2_nim')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota2_fakultas" class="form-label">Fakultas</label>
                        <select class="form-select @error('anggota2_fakultas') is-invalid @enderror" id="anggota2_fakultas" name="anggota2_fakultas">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $fak)
                                <option value="{{ $fak->nama_fakultas }}" {{ old('anggota2_fakultas') == $fak->nama_fakultas ? 'selected' : '' }}>{{ $fak->nama_fakultas }}</option>
                            @endforeach
                        </select>
                        <div class="error-message" id="anggota2_fakultas_error"></div>
                        @error('anggota2_fakultas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota2_prodi" class="form-label">Program Studi</label>
                        <select class="form-select @error('anggota2_prodi') is-invalid @enderror" id="anggota2_prodi" name="anggota2_prodi">
                            <option value="">-- Pilih Program Studi --</option>
                        </select>
                        <div class="error-message" id="anggota2_prodi_error"></div>
                        @error('anggota2_prodi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota2_email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('anggota2_email') is-invalid @enderror" id="anggota2_email" name="anggota2_email" placeholder="Masukkan email" value="{{ old('anggota2_email') }}">
                        <div class="error-message" id="anggota2_email_error"></div>
                        @error('anggota2_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota2_no_hp" class="form-label">No. HP</label>
                        <input type="tel" class="form-control @error('anggota2_no_hp') is-invalid @enderror" id="anggota2_no_hp" name="anggota2_no_hp" placeholder="Masukkan nomor HP" value="{{ old('anggota2_no_hp') }}">
                        <div class="error-message" id="anggota2_no_hp_error"></div>
                        @error('anggota2_no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Anggota 3 -->
            <div class="member-group">
                <div class="member-title">
                    <i class="fas fa-user me-2"></i>Anggota 3 <span class="badge bg-secondary ms-2">Opsional</span>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="anggota3_nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('anggota3_nama') is-invalid @enderror" id="anggota3_nama" name="anggota3_nama" placeholder="Masukkan nama" value="{{ old('anggota3_nama') }}">
                        <div class="error-message" id="anggota3_nama_error"></div>
                        @error('anggota3_nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota3_nim" class="form-label">NIM</label>
                        <input type="text" class="form-control @error('anggota3_nim') is-invalid @enderror" id="anggota3_nim" name="anggota3_nim" placeholder="Masukkan NIM" value="{{ old('anggota3_nim') }}">
                        <div class="error-message" id="anggota3_nim_error"></div>
                        @error('anggota3_nim')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota3_fakultas" class="form-label">Fakultas</label>
                        <select class="form-select @error('anggota3_fakultas') is-invalid @enderror" id="anggota3_fakultas" name="anggota3_fakultas">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $fak)
                                <option value="{{ $fak->nama_fakultas }}" {{ old('anggota3_fakultas') == $fak->nama_fakultas ? 'selected' : '' }}>{{ $fak->nama_fakultas }}</option>
                            @endforeach
                        </select>
                        <div class="error-message" id="anggota3_fakultas_error"></div>
                        @error('anggota3_fakultas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota3_prodi" class="form-label">Program Studi</label>
                        <select class="form-select @error('anggota3_prodi') is-invalid @enderror" id="anggota3_prodi" name="anggota3_prodi">
                            <option value="">-- Pilih Program Studi --</option>
                        </select>
                        <div class="error-message" id="anggota3_prodi_error"></div>
                        @error('anggota3_prodi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota3_email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('anggota3_email') is-invalid @enderror" id="anggota3_email" name="anggota3_email" placeholder="Masukkan email" value="{{ old('anggota3_email') }}">
                        <div class="error-message" id="anggota3_email_error"></div>
                        @error('anggota3_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota3_no_hp" class="form-label">No. HP</label>
                        <input type="tel" class="form-control @error('anggota3_no_hp') is-invalid @enderror" id="anggota3_no_hp" name="anggota3_no_hp" placeholder="Masukkan nomor HP" value="{{ old('anggota3_no_hp') }}">
                        <div class="error-message" id="anggota3_no_hp_error"></div>
                        @error('anggota3_no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Anggota 4 -->
            <div class="member-group">
                <div class="member-title">
                    <i class="fas fa-user me-2"></i>Anggota 4 <span class="badge bg-secondary ms-2">Opsional</span>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="anggota4_nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control @error('anggota4_nama') is-invalid @enderror" id="anggota4_nama" name="anggota4_nama" placeholder="Masukkan nama" value="{{ old('anggota4_nama') }}">
                        <div class="error-message" id="anggota4_nama_error"></div>
                        @error('anggota4_nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota4_nim" class="form-label">NIM</label>
                        <input type="text" class="form-control @error('anggota4_nim') is-invalid @enderror" id="anggota4_nim" name="anggota4_nim" placeholder="Masukkan NIM" value="{{ old('anggota4_nim') }}">
                        <div class="error-message" id="anggota4_nim_error"></div>
                        @error('anggota4_nim')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota4_fakultas" class="form-label">Fakultas</label>
                        <select class="form-select @error('anggota4_fakultas') is-invalid @enderror" id="anggota4_fakultas" name="anggota4_fakultas">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $fak)
                                <option value="{{ $fak->nama_fakultas }}" {{ old('anggota4_fakultas') == $fak->nama_fakultas ? 'selected' : '' }}>{{ $fak->nama_fakultas }}</option>
                            @endforeach
                        </select>
                        <div class="error-message" id="anggota4_fakultas_error"></div>
                        @error('anggota4_fakultas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota4_prodi" class="form-label">Program Studi</label>
                        <select class="form-select @error('anggota4_prodi') is-invalid @enderror" id="anggota4_prodi" name="anggota4_prodi">
                            <option value="">-- Pilih Program Studi --</option>
                        </select>
                        <div class="error-message" id="anggota4_prodi_error"></div>
                        @error('anggota4_prodi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota4_email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('anggota4_email') is-invalid @enderror" id="anggota4_email" name="anggota4_email" placeholder="Masukkan email" value="{{ old('anggota4_email') }}">
                        <div class="error-message" id="anggota4_email_error"></div>
                        @error('anggota4_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="anggota4_no_hp" class="form-label">No. HP</label>
                        <input type="tel" class="form-control @error('anggota4_no_hp') is-invalid @enderror" id="anggota4_no_hp" name="anggota4_no_hp" placeholder="Masukkan nomor HP" value="{{ old('anggota4_no_hp') }}">
                        <div class="error-message" id="anggota4_no_hp_error"></div>
                        @error('anggota4_no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                <div class="file-upload-area @error('proposal_file') border-danger @enderror" id="proposalUploadArea">
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
                @error('proposal_file')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Ketentuan Upload:</strong>
                <ul class="mb-0 mt-2">
                    <li>Format file harus PDF.  </li>
                    <li>Ukuran maksimal 5MB per file.</li>
                    <li>File proposal harus lengkap sesuai template.</li>
                </ul>
            </div>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Perhatian Mahasiswa:</strong>
                <ul class="mb-0 mt-2">
                    <li>Pastikan anggota tim tidak pernah ikut mengajukan proposal di tahun ini.</li>
                    <li>Jika mahasiswa ingin keluar dari sebuah tim setelah pengajuan, disarankan untuk menghubungi dosen pendamping proposal untuk menolak validasi proposal.</li>
                </ul>
            </div>

        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-submit" id="submitBtn" disabled>
                <i class="fas fa-paper-plane me-2"></i>Ajukan Proposal
            </button>
            <div class="mt-2">
                <small class="text-muted" id="submitHelpText">
                    <i class="fas fa-info-circle me-1"></i>
                    Lengkapi semua field wajib untuk mengaktifkan tombol submit
                </small>
            </div>
        </div>
        
    </form>
</div>
@endsection

@section('scripts')
@php
    // Prepare user data for JavaScript
    $currentUserNim = isset($user) ? ($user->nim ?? '') : '';
    $userData = null;
    if (isset($user)) {
        $userData = [
            'nama' => $user->nama_mhs ?? '',
            'nim' => $user->nim ?? '',
            'prodi' => $user->prodi_mhs ?? '',
            'fakultas' => $user->fakultas_mhs ?? '',
            'email' => $user->email_mhs ?? '',
            'no_hp' => $user->no_hp_mhs ?? ''
        ];
    }
    
    // Prepare error message for JavaScript
    $errorMessage = null;
    if ($errors->any()) {
        if ($errors->has('nim_duplicate')) {
            $errorMessage = 'Beberapa anggota tim sudah terdaftar dalam proposal lain. Silakan ganti anggota tim.';
        } elseif ($errors->has('team_nim')) {
            $errorMessage = 'Terdapat NIM yang sama dalam satu tim. Silakan periksa data anggota.';
        } elseif ($errors->has('team_size')) {
            $errorMessage = 'Jumlah anggota tim tidak sesuai ketentuan (minimal 3, maksimal 5 orang).';
        } elseif ($errors->has('optional_members')) {
            $errorMessage = 'Data anggota opsional tidak lengkap. Jika diisi, semua field harus diisi.';
        } else {
            $errorMessage = 'Terdapat kesalahan dalam form. Silakan periksa field yang ditandai dengan warna merah.';
        }
    }
    
    // Encode to JSON for JavaScript
    $currentUserNimJson = json_encode($currentUserNim);
    $userDataJson = json_encode($userData);
    $errorMessageJson = json_encode($errorMessage);
@endphp
<script>
    // Set data from PHP
    const CURRENT_USER_NIM = {!! $currentUserNimJson !!};
    const USER_DATA = {!! $userDataJson !!};
    const ERROR_MESSAGE = {!! $errorMessageJson !!};
    
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
        
        if (input && info && progress) {
            input.value = '';
            info.classList.remove('show');
            progress.style.width = '0%';
        }
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

    // Clear error styling
    function clearError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '_error');
        
        if (field) {
            field.classList.remove('error-highlight');
        }
        if (errorDiv) {
            errorDiv.classList.remove('show');
            errorDiv.textContent = '';
        }
    }

    // Show error styling
    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '_error');
        
        if (field) {
            field.classList.add('error-highlight');
            field.focus();
        }
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.classList.add('show');
        }
    }

    // Form validation
    document.getElementById('proposalForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Clear all previous errors
        clearAllErrors();
        
        let hasErrors = false;
        const requiredFields = [
            'judul', 'skim', 'dana_diajukan', 'dosen_pembimbing',
            'ketua_nama', 'ketua_nim', 'ketua_prodi', 'ketua_fakultas', 
            'ketua_email', 'ketua_no_hp'
        ];

        // Check required fields
        for (let field of requiredFields) {
            const element = document.getElementById(field);
            if (!element || !element.value.trim()) {
                showError(field, `Field ini wajib diisi!`);
                hasErrors = true;
            }
        }

        // Validate judul proposal length
        const judulField = document.getElementById('judul');
        if (judulField) {
            const judul = judulField.value.trim();
            if (judul.length < 10) {
                showError('judul', 'Judul proposal minimal 10 karakter!');
                hasErrors = true;
            }
            if (judul.length > 200) {
                showError('judul', 'Judul proposal maksimal 200 karakter!');
                hasErrors = true;
            }
        }

        // Validate skim PKM
        if (skimField) {
            const selectedSkimType = skimField.value;
            if (!selectedSkimType) {
                showError('skim', 'Skim/Jenis PKM wajib dipilih!');
                hasErrors = true;
            }
        }

        // Check file uploads - Proposal file is required
        const proposalFile = document.getElementById('proposal_file');
        if (!proposalFile || !proposalFile.files[0]) {
            showToast('Mohon upload file proposal!', 'error');
            hasErrors = true;
        } else {
            // Validate file size
            const fileSize = proposalFile.files[0].size;
            if (fileSize > 5 * 1024 * 1024) {
                showToast('Ukuran file proposal maksimal 5MB!', 'error');
                hasErrors = true;
            }
            
            // Validate file type
            const fileName = proposalFile.files[0].name;
            if (!fileName.toLowerCase().endsWith('.pdf')) {
                showToast('File proposal harus berformat PDF!', 'error');
                hasErrors = true;
            }
        }


        // Validate NIM format
        if (ketuaNimField) {
            const ketuaNim = ketuaNimField.value.trim();
            if (ketuaNim.length < 8) {
                showError('ketua_nim', 'NIM harus minimal 8 digit!');
                hasErrors = true;
            }
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const ketuaEmailField = document.getElementById('ketua_email');
        if (ketuaEmailField) {
            const ketuaEmail = ketuaEmailField.value.trim();
            if (!emailRegex.test(ketuaEmail)) {
                showError('ketua_email', 'Format email tidak valid!');
                hasErrors = true;
            }
        }

        // Validate phone number
        const ketuaNoHpField = document.getElementById('ketua_no_hp');
        if (ketuaNoHpField) {
            const ketuaNoHp = ketuaNoHpField.value.trim();
            if (ketuaNoHp.length < 10) {
                showError('ketua_no_hp', 'Nomor HP harus minimal 10 digit!');
                hasErrors = true;
            }
        }

        // Validate dana based on skim type
        if (!danaField) {
            showError('dana_diajukan', 'Field dana tidak ditemukan!');
            hasErrors = true;
        } else {
            const dana = danaField.value;
            const selectedSkim = document.getElementById('skim').value;
            
            // Define PKM Insentif skims (tidak memiliki pendanaan)
            const insentifSkims = ['GFT', 'AI'];
            const isInsentif = insentifSkims.includes(selectedSkim);
            
            // Convert dana to number, default to 0 if empty
            const danaValue = dana ? parseInt((window.parseAngkaIndonesia ? window.parseAngkaIndonesia(dana) : dana).toString(), 10) : 0;
            
            if (isInsentif) {
                // For PKM Insentif, dana harus 0 (tidak ada pendanaan)
                if (danaValue !== 0) {
                    showError('dana_diajukan', 'PKM Insentif tidak memiliki pendanaan. Dana harus 0!');
                    hasErrors = true;
                }
            } else {
                // For PKM Pendanaan, dana must be at least 1,000,000
                if (!dana || danaValue < 1000000) {
                    showError('dana_diajukan', 'Dana yang diajukan minimal Rp 1.000.000!');
                    hasErrors = true;
                }
                if (danaValue > 15000000) {
                    showError('dana_diajukan', 'Dana yang diajukan maksimal Rp 15.000.000!');
                    hasErrors = true;
                }
            }
        }

        // Validate dosen pendamping
        const dosenPembimbingField = document.getElementById('dosen_pembimbing');
        if (dosenPembimbingField) {
            const dosenPembimbing = dosenPembimbingField.value;
            if (!dosenPembimbing) {
                showError('dosen_pembimbing', 'Dosen pendamping wajib dipilih!');
                hasErrors = true;
            }
        }

        // Validate anggota tim wajib (anggota 1 & 2)
        const anggotaWajib = ['anggota1', 'anggota2'];
        for (let anggota of anggotaWajib) {
            const namaField = document.getElementById(anggota + '_nama');
            const nimField = document.getElementById(anggota + '_nim');
            const prodiField = document.getElementById(anggota + '_prodi');
            const fakultasField = document.getElementById(anggota + '_fakultas');
            const emailField = document.getElementById(anggota + '_email');
            const noHpField = document.getElementById(anggota + '_no_hp');
            
            if (!namaField || !nimField || !prodiField || !fakultasField || !emailField || !noHpField) {
                showError(anggota + '_nama', `Field ${anggota} tidak ditemukan!`);
                hasErrors = true;
                continue;
            }
            
            const nama = namaField.value.trim();
            const nim = nimField.value.trim();
            const prodi = prodiField.value.trim();
            const fakultas = fakultasField.value.trim();
            const email = emailField.value.trim();
            const noHp = noHpField.value.trim();
            
            // Anggota 1 & 2 wajib diisi lengkap
            if (!nama) {
                showError(anggota + '_nama', `Nama ${anggota} wajib diisi!`);
                hasErrors = true;
            }
            if (!nim) {
                showError(anggota + '_nim', `NIM ${anggota} wajib diisi!`);
                hasErrors = true;
            }
            if (!prodi) {
                showError(anggota + '_prodi', `Program studi ${anggota} wajib diisi!`);
                hasErrors = true;
            }
            if (!fakultas) {
                showError(anggota + '_fakultas', `Fakultas ${anggota} wajib diisi!`);
                hasErrors = true;
            }
            if (!email) {
                showError(anggota + '_email', `Email ${anggota} wajib diisi!`);
                hasErrors = true;
            }
            if (!noHp) {
                showError(anggota + '_no_hp', `No. HP ${anggota} wajib diisi!`);
                hasErrors = true;
            }
        }

        // Validate anggota tim opsional (anggota 3 & 4)
        const anggotaOpsional = ['anggota3', 'anggota4'];
        for (let anggota of anggotaOpsional) {
            const namaField = document.getElementById(anggota + '_nama');
            const nimField = document.getElementById(anggota + '_nim');
            const prodiField = document.getElementById(anggota + '_prodi');
            const fakultasField = document.getElementById(anggota + '_fakultas');
            const emailField = document.getElementById(anggota + '_email');
            const noHpField = document.getElementById(anggota + '_no_hp');
            
            if (!namaField || !nimField || !prodiField || !fakultasField || !emailField || !noHpField) {
                continue; // Skip if fields don't exist
            }
            
            const nama = namaField.value.trim();
            const nim = nimField.value.trim();
            const prodi = prodiField.value.trim();
            const fakultas = fakultasField.value.trim();
            const email = emailField.value.trim();
            const noHp = noHpField.value.trim();
            
            // Check if any field is filled
            const hasAnyData = nama || nim || prodi || fakultas || email || noHp;
            
            if (hasAnyData) {
                // If any field is filled, all required fields must be filled
                if (!nama) {
                    showError(anggota + '_nama', `Nama ${anggota} wajib diisi jika ada data anggota!`);
                    hasErrors = true;
                }
                if (!nim) {
                    showError(anggota + '_nim', `NIM ${anggota} wajib diisi jika ada data anggota!`);
                    hasErrors = true;
                }
                if (!prodi) {
                    showError(anggota + '_prodi', `Program studi ${anggota} wajib diisi jika ada data anggota!`);
                    hasErrors = true;
                }
                if (!fakultas) {
                    showError(anggota + '_fakultas', `Fakultas ${anggota} wajib diisi jika ada data anggota!`);
                    hasErrors = true;
                }
                if (!email) {
                    showError(anggota + '_email', `Email ${anggota} wajib diisi jika ada data anggota!`);
                    hasErrors = true;
                }
                if (!noHp) {
                    showError(anggota + '_no_hp', `No. HP ${anggota} wajib diisi jika ada data anggota!`);
                    hasErrors = true;
                }
            }
        }

        // Check for duplicate members within the same team
        const memberNims = [];
        if (ketuaNimField) {
            const ketuaNimValue = ketuaNimField.value.trim();
            if (ketuaNimValue) memberNims.push(ketuaNimValue);
        }

        // Check all anggota fields for duplicates within the team
        const allAnggotaFields = ['anggota1', 'anggota2', 'anggota3', 'anggota4'];
        for (let anggota of allAnggotaFields) {
            const nimField = document.getElementById(anggota + '_nim');
            if (nimField) {
                const nim = nimField.value.trim();
                if (nim) {
                    if (memberNims.includes(nim)) {
                        showError(anggota + '_nim', `NIM ${nim} sudah digunakan oleh anggota tim lain!`);
                        hasErrors = true;
                    } else {
                        memberNims.push(nim);
                    }
                }
            }
        }

        // Validate anggota email format if filled
        for (let anggota of allAnggotaFields) {
            const emailField = document.getElementById(anggota + '_email');
            if (emailField) {
                const email = emailField.value.trim();
                if (email && !emailRegex.test(email)) {
                    showError(anggota + '_email', `Format email ${anggota} tidak valid!`);
                    hasErrors = true;
                }
            }
        }

        // Validate anggota phone number if filled
        for (let anggota of allAnggotaFields) {
            const noHpField = document.getElementById(anggota + '_no_hp');
            if (noHpField) {
                const noHp = noHpField.value.trim();
                if (noHp && noHp.length < 10) {
                    showError(anggota + '_no_hp', `Nomor HP ${anggota} harus minimal 10 digit!`);
                    hasErrors = true;
                }
            }
        }

        // Validate anggota NIM format if filled
        for (let anggota of allAnggotaFields) {
            const nimField = document.getElementById(anggota + '_nim');
            if (nimField) {
                const nim = nimField.value.trim();
                if (nim && nim.length < 8) {
                    showError(anggota + '_nim', `NIM ${anggota} harus minimal 8 digit!`);
                    hasErrors = true;
                }
            }
        }

        // Check if ketua tim is also listed as anggota
        if (ketuaNimField) {
            const ketuaNimForCheck = ketuaNimField.value.trim();
            for (let anggota of allAnggotaFields) {
                const anggotaNimField = document.getElementById(anggota + '_nim');
                if (anggotaNimField) {
                    const anggotaNim = anggotaNimField.value.trim();
                    if (anggotaNim && anggotaNim === ketuaNimForCheck) {
                        showError(anggota + '_nim', `Ketua tim tidak bisa menjadi anggota tim! NIM ${ketuaNimForCheck} sudah digunakan sebagai ketua.`);
                        hasErrors = true;
                    }
                }
            }
        }

        if (hasErrors) {
            showToast('Mohon periksa kembali form yang telah diisi!', 'error');
            return false;
        }

        // Check if user already has a proposal (double check)
        if (CURRENT_USER_NIM) {
            const currentUserNim = CURRENT_USER_NIM;
            const tahunAjaran = document.getElementById('tahun_ajaran')?.value || '2024/2025';
            try {
                const response = await fetch(`/api/mahasiswa/check-proposal/${currentUserNim}?tahun_ajaran=${encodeURIComponent(tahunAjaran)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    if (data.success && data.hasProposal) {
                        showToast(`Anda sudah terdaftar dalam proposal tahun ${tahunAjaran}: "${data.proposalTitle}". Satu mahasiswa hanya dapat terdaftar dalam satu proposal PKM per tahun akademik.`, 'error');
                        return false;
                    }
                } else {
                    console.error('Error checking user proposal:', response.status, response.statusText);
                    showToast('Gagal memverifikasi status proposal. Silakan coba lagi.', 'error');
                    return false;
                }
            } catch (error) {
                console.error('Error checking user proposal:', error);
                showToast('Gagal memverifikasi status proposal. Silakan coba lagi.', 'error');
                return false;
            }
        }

        // Custom confirmation dialog dengan styling yang lebih baik
        const confirmed = await new Promise((resolve) => {
            // Create custom modal confirmation
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="background: #343a40; color: white; border: 1px solid #495057;">
                        <div class="modal-header" style="border-bottom: 1px solid #495057;">
                            <h5 class="modal-title" style="color: white;">
                                <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                Konfirmasi Pengajuan Proposal
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="this.closest('.modal').remove()"></button>
                        </div>
                        <div class="modal-body ">
                            <p class="mb-3">Apakah Anda yakin ingin mengajukan proposal ini?</p>
                            <div class="alert alert-warning mb-0" style="background: rgba(255, 254, 250, 0.84); border-color: #ffc107;">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Perhatian:</strong> Data yang sudah disubmit tidak dapat diubah.
                            </div>
                        </div>
                        <div class="modal-footer" style="border-top: 1px solid #495057;">
                            <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove(); this.closest('.modal').dispatchEvent(new Event('canceled'))">
                                <i class="fas fa-times me-2"></i>Batal
                            </button>
                            <button type="button" class="btn btn-primary" onclick="this.closest('.modal').remove(); this.closest('.modal').dispatchEvent(new Event('confirmed'))">
                                <i class="fas fa-check me-2"></i>Ya, Ajukan Proposal
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
        
        if (!confirmed) {
            return false;
        }

        // Show loading
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<span class="loading-spinner me-2"></span>Mengajukan...';
        submitBtn.disabled = true;

        showToast('Proposal sedang diajukan...', 'info');

        // Submit form
        this.submit();
    });

    // Function to validate form for submit button state
    function validateFormForSubmit() {
        if (!submitBtn || !submitHelpText) return;
        
        const requiredFields = [
            'judul', 'skim', 'dana_diajukan', 'dosen_pembimbing',
            'ketua_nama', 'ketua_nim', 'ketua_prodi', 'ketua_fakultas', 
            'ketua_email', 'ketua_no_hp', 'anggota1_nama', 'anggota1_nim',
            'anggota1_prodi', 'anggota1_fakultas', 'anggota1_email', 'anggota1_no_hp',
            'anggota2_nama', 'anggota2_nim', 'anggota2_prodi', 'anggota2_fakultas',
            'anggota2_email', 'anggota2_no_hp'
        ];
        
        let isValid = true;
        let missingFields = [];
        
        // Check required fields
        for (let fieldId of requiredFields) {
            const field = document.getElementById(fieldId);
            if (!field || !field.value.trim()) {
                isValid = false;
                missingFields.push(fieldId);
            }
        }
        
        // Check file upload
        const proposalFile = document.getElementById('proposal_file');
        if (!proposalFile || !proposalFile.files[0]) {
            isValid = false;
            missingFields.push('proposal_file');
        }
        
        // Check judul length
        const judulField = document.getElementById('judul');
        if (judulField && judulField.value.trim()) {
            const judul = judulField.value.trim();
            if (judul.length < 10 || judul.length > 200) {
                isValid = false;
            }
        }
        
        // Check NIM format for ketua and required anggota
        const nimFields = ['ketua_nim', 'anggota1_nim', 'anggota2_nim'];
        for (let nimFieldId of nimFields) {
            const nimField = document.getElementById(nimFieldId);
            if (nimField && nimField.value.trim()) {
                const nim = nimField.value.trim();
                if (nim.length < 8) {
                    isValid = false;
                }
            }
        }
        
        // Check email format for ketua and required anggota
        const emailFields = ['ketua_email', 'anggota1_email', 'anggota2_email'];
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        for (let emailFieldId of emailFields) {
            const emailField = document.getElementById(emailFieldId);
            if (emailField && emailField.value.trim()) {
                const email = emailField.value.trim();
                if (!emailRegex.test(email)) {
                    isValid = false;
                }
            }
        }
        
        // Check phone number format for ketua and required anggota
        const phoneFields = ['ketua_no_hp', 'anggota1_no_hp', 'anggota2_no_hp'];
        for (let phoneFieldId of phoneFields) {
            const phoneField = document.getElementById(phoneFieldId);
            if (phoneField && phoneField.value.trim()) {
                const phone = phoneField.value.trim();
                if (phone.length < 10) {
                    isValid = false;
                }
            }
        }
        
        // Check optional members (anggota 3 & 4) - if any field is filled, all must be filled
        const optionalMembers = ['anggota3', 'anggota4'];
        for (let member of optionalMembers) {
            const fields = ['nama', 'nim', 'prodi', 'fakultas', 'email', 'no_hp'];
            let hasAnyData = false;
            let allFieldsFilled = true;
            
            for (let field of fields) {
                const fieldId = member + '_' + field;
                const fieldElement = document.getElementById(fieldId);
                if (fieldElement && fieldElement.value.trim()) {
                    hasAnyData = true;
                }
            }
            
            if (hasAnyData) {
                for (let field of fields) {
                    const fieldId = member + '_' + field;
                    const fieldElement = document.getElementById(fieldId);
                    if (!fieldElement || !fieldElement.value.trim()) {
                        allFieldsFilled = false;
                        break;
                    }
                }
                
                if (!allFieldsFilled) {
                    isValid = false;
                }
            }
        }
        
        // Check dana based on skim type
        const selectedSkim = document.getElementById('skim').value;
        const insentifSkims = ['GFT', 'AI'];
        const isInsentif = insentifSkims.includes(selectedSkim);
        const danaValue = danaField ? (parseInt((window.parseAngkaIndonesia ? window.parseAngkaIndonesia(danaField.value) : danaField.value).toString(), 10) || 0) : 0;
        
        if (isInsentif) {
            if (danaValue !== 0) {
                isValid = false;
            }
        } else {
            if (!danaValue || danaValue < 1000000 || danaValue > 15000000) {
                isValid = false;
            }
        }
        
        // Update button state
        if (isValid) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-primary');
            submitHelpText.textContent = '✓ Form sudah lengkap, Anda dapat mengajukan proposal';
            submitHelpText.className = 'mt-2 small text-success submit-help-text ready';
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-secondary');
            const missingCount = missingFields.length;
            submitHelpText.textContent = `Lengkapi ${missingCount} field wajib untuk mengaktifkan tombol submit`;
            submitHelpText.className = 'mt-2 small text-muted submit-help-text incomplete';
        }
    }

    function clearAllErrors() {
        const errorDivs = document.querySelectorAll('.error-message');
        const errorFields = document.querySelectorAll('.error-highlight');
        
        errorDivs.forEach(div => {
            div.classList.remove('show');
            div.textContent = '';
        });
        
        errorFields.forEach(field => {
            field.classList.remove('error-highlight');
        });
    }

    // Global variables for form elements
    let ketuaNimField = null;
    let danaField = null;
    let skimField = null;
    let submitBtn = null;
    let submitHelpText = null;
    
    // Auto-fill data mahasiswa yang login
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded - Setting up auto-fill...');
        
        // Initialize global variables
        ketuaNimField = document.getElementById('ketua_nim');
        danaField = document.getElementById('dana_diajukan');
        skimField = document.getElementById('skim');
        submitBtn = document.getElementById('submitBtn');
        submitHelpText = document.getElementById('submitHelpText');
        
        // Setup file upload areas
        setupFileUpload('proposal_file', 'proposalUploadArea', 'proposalFileInfo', 'proposalFileName', 'proposalFileSize', 'proposalProgress');

        // Auto-fill data if available - moved inside DOMContentLoaded
        if (USER_DATA) {
            const userData = USER_DATA;
            console.log('Auto-filling ketua data from user session...');
            console.log('User data:', userData);
            
            // Check if fields exist before setting values
            const ketuaNama = document.getElementById('ketua_nama');
            const ketuaNim = document.getElementById('ketua_nim');
            const ketuaProdi = document.getElementById('ketua_prodi');
            const ketuaFakultas = document.getElementById('ketua_fakultas');
            const ketuaEmail = document.getElementById('ketua_email');
            const ketuaNoHp = document.getElementById('ketua_no_hp');
            
            console.log('Field elements found:', {
                ketuaNama: ketuaNama,
                ketuaNim: ketuaNim,
                ketuaProdi: ketuaProdi,
                ketuaFakultas: ketuaFakultas,
                ketuaEmail: ketuaEmail,
                ketuaNoHp: ketuaNoHp
            });
            
            if (ketuaNama && userData.nama) {
                ketuaNama.value = userData.nama;
                console.log('Set ketua_nama to:', ketuaNama.value);
            }
            if (ketuaNim && userData.nim) {
                ketuaNim.value = userData.nim;
                console.log('Set ketua_nim to:', ketuaNim.value);
            }
            if (ketuaFakultas && userData.fakultas) {
                ketuaFakultas.value = userData.fakultas;
                console.log('Set ketua_fakultas to:', ketuaFakultas.value);
                
                // Update prodi dropdown after setting fakultas
                if (userData.fakultas) {
                    updateProdiDropdown(userData.fakultas, 'ketua_prodi', userData.prodi);
                }
            }
            if (ketuaEmail && userData.email) {
                ketuaEmail.value = userData.email;
                console.log('Set ketua_email to:', ketuaEmail.value);
            }
            if (ketuaNoHp && userData.no_hp) {
                ketuaNoHp.value = userData.no_hp;
                console.log('Set ketua_no_hp to:', ketuaNoHp.value);
            }
        } else {
            console.log('No user data available for auto-fill');
        }

        // Format currency input
        danaField.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value > 15000000) {
                value = 15000000;
            }
            this.value = value;
        });

        // Clear errors on input and validate form
        const inputs = document.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                clearError(this.id);
                validateFormForSubmit();
            });
            
            input.addEventListener('change', function() {
                clearError(this.id);
                validateFormForSubmit();
            });
        });

        // Setup file input validation
        const proposalFile = document.getElementById('proposal_file');
        if (proposalFile) {
            proposalFile.addEventListener('change', function() {
                validateFormForSubmit();
            });
        }

        // Initial validation
        validateFormForSubmit();
        
        // Show error toast if there are server-side errors
        if (ERROR_MESSAGE) {
            showToast(ERROR_MESSAGE, 'error');
            
            // Scroll to first error field
            setTimeout(() => {
                const firstErrorField = document.querySelector('.is-invalid');
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    firstErrorField.focus();
                }
            }, 500);
        }

        // Handle skim change to update dana field behavior
        const danaLabel = document.querySelector('label[for="dana_diajukan"]');
        const danaHelpText = document.getElementById('dana_help_text');
        
        function updateDanaFieldBehavior() {
            const selectedSkim = skimField.value;
            const insentifSkims = ['GFT', 'AI'];
            const isInsentif = insentifSkims.includes(selectedSkim);
            
            // Clear any existing errors
            clearError('dana_diajukan');
            
            if (isInsentif) {
                // For PKM Insentif - tidak ada pendanaan
                danaField.min = '0';
                danaField.max = '0';
                danaField.value = '0';
                danaField.readOnly = true;
                danaField.placeholder = '0 (PKM Insentif tidak memiliki pendanaan)';
                danaHelpText.textContent = 'PKM Insentif tidak memiliki pendanaan. Dana otomatis 0.';
                danaHelpText.className = 'form-text text-info';
                danaField.style.backgroundColor = '#f8f9fa';
            } else {
                // For PKM Pendanaan
                danaField.min = '1000000';
                danaField.max = '15000000';
                danaField.readOnly = false;
                danaField.placeholder = '1000000';
                danaHelpText.textContent = 'Minimal Rp 1.000.000, maksimal Rp 15.000.000';
                danaHelpText.className = 'form-text';
                danaField.style.backgroundColor = '';
                
                // If current value is less than 1000000, set to 1000000
                if (danaField.value && (parseInt((window.parseAngkaIndonesia ? window.parseAngkaIndonesia(danaField.value) : danaField.value).toString(), 10) < 1000000)) {
                    danaField.value = '1000000';
                }
            }
            
            // Trigger form validation after dana field behavior update
            validateFormForSubmit();
        }
        
        // Add event listener for skim change
        skimField.addEventListener('change', updateDanaFieldBehavior);
        
        // Initialize dana field behavior on page load
        updateDanaFieldBehavior();

        // Setup fakultas-prodi dropdown functionality
        setupFakultasProdiDropdowns();

        // Auto-fill ketua tim if NIM is changed
        if (ketuaNimField) {
            console.log('Setting up ketua NIM event listeners...');
            
            ketuaNimField.addEventListener('input', function() {
                console.log('Ketua NIM input event, value:', this.value);
                // Clear fields if NIM is empty
                if (!this.value.trim()) {
                    clearKetuaFields();
                }
            });
            
            // Add blur event for auto-fill
            ketuaNimField.addEventListener('blur', function() {
                console.log('Ketua NIM blur event triggered, value:', this.value);
                console.log('Ketua NIM field element:', this);
                if (this.value.trim()) {
                    console.log('Calling autofillKetua...');
                    autofillKetua();
                } else {
                    console.log('Ketua NIM is empty, not calling autofill');
                }
            });
            
            // Add keypress event for Enter key
            ketuaNimField.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    console.log('Ketua NIM Enter key pressed, calling autofillKetua...');
                    e.preventDefault();
                    autofillKetua();
                }
            });
        } else {
            console.error('Ketua NIM field not found!');
        }

        // Setup auto-fill for all anggota fields
        const anggotaFields = ['anggota1', 'anggota2', 'anggota3', 'anggota4'];
        anggotaFields.forEach(anggota => {
            const nimField = document.getElementById(anggota + '_nim');
            if (nimField) {
                console.log(`Setting up ${anggota} NIM event listeners...`);
                
                // Add blur event for auto-fill
                nimField.addEventListener('blur', function() {
                    console.log(`${anggota} NIM blur event triggered, value:`, this.value);
                    console.log(`${anggota} NIM field element:`, this);
                    if (this.value.trim()) {
                        const no = anggota.replace('anggota', '');
                        console.log(`Calling autofillAnggota(${no})...`);
                        autofillAnggota(no);
                    } else {
                        console.log(`${anggota} NIM is empty, not calling autofill`);
                    }
                });
                
                // Add input event to clear fields when NIM is cleared
                nimField.addEventListener('input', function() {
                    console.log(`${anggota} NIM input event, value:`, this.value);
                    if (!this.value.trim()) {
                        const no = anggota.replace('anggota', '');
                        clearAnggotaFields(no);
                    }
                });
                
                // Add keypress event for Enter key
                nimField.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        console.log(`${anggota} NIM Enter key pressed, calling autofillAnggota...`);
                        e.preventDefault();
                        const no = anggota.replace('anggota', '');
                        autofillAnggota(no);
                    }
                });
                
                // Add change event for auto-fill
                nimField.addEventListener('change', function() {
                    console.log(`${anggota} NIM change event triggered, value:`, this.value);
                    if (this.value.trim()) {
                        const no = anggota.replace('anggota', '');
                        console.log(`Calling autofillAnggota(${no}) from change event...`);
                        autofillAnggota(no);
                    }
                });
            } else {
                console.error(`${anggota} NIM field not found!`);
            }
        });
        
        console.log('Auto-fill setup completed!');
        
        // Test function untuk debugging
        window.testAutofill = function(nim, memberType = 'anggota1') {
            console.log('Testing autofill with NIM:', nim, 'for member:', memberType);
            if (memberType === 'ketua') {
                autofillKetua();
            } else {
                const no = memberType.replace('anggota', '');
                autofillAnggota(no);
            }
        };
        
        console.log('Test function available: window.testAutofill("2308561093", "anggota1")');
        
        // Test API call
        window.testAPI = function(nim) {
            console.log('Testing API call for NIM:', nim);
            const apiUrl = `/api/mahasiswa/by-nim/${nim}`;
            console.log('API URL:', apiUrl);
            
            fetch(apiUrl, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            })
            .then(res => {
                console.log('API Response status:', res.status);
                console.log('API Response headers:', res.headers);
                return res.json();
            })
            .then(data => {
                console.log('API Response data:', data);
            })
            .catch(error => {
                console.error('API Error:', error);
            });
        };
        
        console.log('API test function available: window.testAPI("2308561093")');
        
        // Debug: Check if all required fields exist
        const requiredFields = [
            'ketua_nim', 'ketua_nama', 'ketua_prodi', 'ketua_fakultas', 'ketua_email', 'ketua_no_hp',
            'anggota1_nim', 'anggota1_nama', 'anggota1_prodi', 'anggota1_fakultas', 'anggota1_email', 'anggota1_no_hp',
            'anggota2_nim', 'anggota2_nama', 'anggota2_prodi', 'anggota2_fakultas', 'anggota2_email', 'anggota2_no_hp',
            'anggota3_nim', 'anggota3_nama', 'anggota3_prodi', 'anggota3_fakultas', 'anggota3_email', 'anggota3_no_hp',
            'anggota4_nim', 'anggota4_nama', 'anggota4_prodi', 'anggota4_fakultas', 'anggota4_email', 'anggota4_no_hp'
        ];
        
        console.log('Checking required fields...');
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                console.log(`✓ Field ${fieldId} found`);
            } else {
                console.error(`✗ Field ${fieldId} NOT found!`);
            }
        });
    });

    function autofillAnggota(no) {
        console.log(`=== AUTOFILL ANGGOTA ${no} STARTED ===`);
        const nimField = document.getElementById('anggota'+no+'_nim');
        if (!nimField) {
            console.error(`NIM field for anggota ${no} not found!`);
            return;
        }
        
        const nim = nimField.value.trim();
        console.log('Autofill anggota', no, 'dengan NIM:', nim);
        console.log('Current NIM field value:', nimField.value);
        
        if (!nim) {
            console.log('NIM kosong, membersihkan field anggota', no);
            clearAnggotaFields(no);
            return;
        }
        
        
        // Clear previous data
        clearAnggotaFields(no);
        
        // Debug: log the API URL
        const apiUrl = `/api/mahasiswa/by-nim/${nim}`;
        console.log('Fetching from:', apiUrl);
        console.log('Current window location:', window.location.href);
        console.log('Full API URL:', window.location.origin + apiUrl);
        console.log('Base URL:', window.location.origin);
        console.log('Pathname:', window.location.pathname);
        
        // Add CSRF token to headers
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        console.log('CSRF Token for anggota:', csrfToken);
        console.log('CSRF Token element:', document.querySelector('meta[name="csrf-token"]'));
        
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken || ''
        };
        
        fetch(apiUrl, {
            method: 'GET',
            headers: headers,
            credentials: 'same-origin'
        })
        .then(res => {
            console.log('Anggota response status:', res.status);
            console.log('Anggota response headers:', res.headers);
            console.log('Anggota response URL:', res.url);
            
            if (!res.ok) {
                console.error('HTTP Error:', res.status, res.statusText);
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            console.log('Response data:', data);
            console.log('Data success:', data.success);
            console.log('Data data:', data.data);
            
            if (data.success && data.data) {
                console.log(`Data anggota ${no} berhasil diambil, mulai auto-fill...`);
                
                
                // Auto-fill all fields
                const namaField = document.getElementById('anggota'+no+'_nama');
                const prodiField = document.getElementById('anggota'+no+'_prodi');
                const fakultasField = document.getElementById('anggota'+no+'_fakultas');
                const emailField = document.getElementById('anggota'+no+'_email');
                const noHpField = document.getElementById('anggota'+no+'_no_hp');
                
                console.log(`Field elements found for anggota ${no}:`, {
                    nama: namaField,
                    prodi: prodiField,
                    fakultas: fakultasField,
                    email: emailField,
                    noHp: noHpField
                });
                
                console.log(`Data to fill for anggota ${no}:`, {
                    nama: data.data.nama,
                    prodi: data.data.prodi,
                    fakultas: data.data.fakultas,
                    email: data.data.email,
                    noHp: data.data.no_hp
                });
                
                if (namaField) {
                    namaField.value = data.data.nama || '';
                    console.log(`Set nama anggota ${no} to:`, data.data.nama);
                    console.log(`Nama field value after setting:`, namaField.value);
                } else {
                    console.error(`Nama field not found for anggota ${no}`);
                }
                if (fakultasField) {
                    fakultasField.value = data.data.fakultas || '';
                    console.log(`Set fakultas anggota ${no} to:`, data.data.fakultas);
                    
                    // Update prodi dropdown after setting fakultas
                    if (data.data.fakultas) {
                        updateProdiDropdown(data.data.fakultas, 'anggota'+no+'_prodi', data.data.prodi);
                    }
                }
                if (emailField) {
                    emailField.value = data.data.email || '';
                    console.log(`Set email anggota ${no} to:`, data.data.email);
                    console.log(`Email field value after setting:`, emailField.value);
                } else {
                    console.error(`Email field not found for anggota ${no}`);
                }
                if (noHpField) {
                    noHpField.value = data.data.no_hp || '';
                    console.log(`Set no_hp anggota ${no} to:`, data.data.no_hp);
                    console.log(`No HP field value after setting:`, noHpField.value);
                } else {
                    console.error(`No HP field not found for anggota ${no}`);
                }
                
                // Show success message (disabled)
                // showToast(`Data mahasiswa dengan NIM ${nim} berhasil diisi otomatis!`, 'success');
                console.log(`Auto-fill anggota ${no} berhasil!`);
                
                // Clear any previous errors
                clearError('anggota'+no+'_nim');
                
                // Trigger form validation
                validateFormForSubmit();
            } else {
                console.log(`Data anggota ${no} tidak ditemukan atau format response salah:`, data);
                showError('anggota'+no+'_nim', 'Mahasiswa dengan NIM tersebut tidak ditemukan!');
                showToast('Mahasiswa dengan NIM tersebut tidak ditemukan!', 'error');
            }
        })
        .catch((error) => {
            console.error(`Error in autofillAnggota ${no}:`, error);
            console.error('Error details:', error.message);
            console.error('Error stack:', error.stack);
            showError('anggota'+no+'_nim', 'Gagal mengambil data mahasiswa! Silakan coba lagi.');
            showToast('Gagal mengambil data mahasiswa! Silakan coba lagi.', 'error');
        });
    }

    function clearAnggotaFields(no) {
        // Don't clear NIM field - only clear other fields
        document.getElementById('anggota'+no+'_nama').value = '';
        document.getElementById('anggota'+no+'_fakultas').value = '';
        document.getElementById('anggota'+no+'_email').value = '';
        document.getElementById('anggota'+no+'_no_hp').value = '';
        
        // Clear prodi dropdown
        const prodiSelect = document.getElementById('anggota'+no+'_prodi');
        if (prodiSelect) {
            prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';
        }
        
        // Clear any error messages
        clearError('anggota'+no+'_nim');
        clearError('anggota'+no+'_nama');
        clearError('anggota'+no+'_prodi');
        clearError('anggota'+no+'_fakultas');
        clearError('anggota'+no+'_email');
        clearError('anggota'+no+'_no_hp');
    }



    function autofillKetua() {
        console.log('=== AUTOFILL KETUA STARTED ===');
        if (!ketuaNimField) {
            console.error('NIM field for ketua not found!');
            return;
        }
        
        const nim = ketuaNimField.value.trim();
        console.log('Autofill ketua dengan NIM:', nim);
        console.log('Current NIM field value:', ketuaNimField.value);
        
        if (!nim) {
            console.log('NIM kosong, membersihkan field ketua');
            clearKetuaFields();
            return;
        }
        
        
        // Clear previous data
        clearKetuaFields();
        
        // Debug: log the API URL
        const apiUrl = `/api/mahasiswa/by-nim/${nim}`;
        console.log('Fetching ketua from:', apiUrl);
        console.log('Current window location:', window.location.href);
        console.log('Full API URL:', window.location.origin + apiUrl);
        console.log('Base URL:', window.location.origin);
        console.log('Pathname:', window.location.pathname);
        
        // Add CSRF token to headers
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        console.log('CSRF Token for ketua:', csrfToken);
        console.log('CSRF Token element for ketua:', document.querySelector('meta[name="csrf-token"]'));
        
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken || ''
        };
        
        fetch(apiUrl, {
            method: 'GET',
            headers: headers,
            credentials: 'same-origin'
        })
        .then(res => {
            console.log('Ketua response status:', res.status);
            console.log('Ketua response headers:', res.headers);
            console.log('Ketua response URL:', res.url);
            
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            return res.json();
        })
        .then(data => {
            console.log('Ketua response data:', data);
            console.log('Ketua data success:', data.success);
            console.log('Ketua data data:', data.data);
            
            if (data.success && data.data) {
                console.log('Data ketua berhasil diambil, mulai auto-fill...');
                
                // Auto-fill all fields
                const namaField = document.getElementById('ketua_nama');
                const prodiField = document.getElementById('ketua_prodi');
                const fakultasField = document.getElementById('ketua_fakultas');
                const emailField = document.getElementById('ketua_email');
                const noHpField = document.getElementById('ketua_no_hp');
                
                console.log('Field elements found:', {
                    nama: namaField,
                    prodi: prodiField,
                    fakultas: fakultasField,
                    email: emailField,
                    noHp: noHpField
                });
                
                if (namaField) {
                    namaField.value = data.data.nama || '';
                    console.log('Set nama to:', data.data.nama);
                    console.log('Nama field value after setting:', namaField.value);
                } else {
                    console.error('Nama field not found for ketua');
                }
                if (fakultasField) {
                    fakultasField.value = data.data.fakultas || '';
                    console.log('Set fakultas to:', data.data.fakultas);
                    
                    // Update prodi dropdown after setting fakultas
                    if (data.data.fakultas) {
                        updateProdiDropdown(data.data.fakultas, 'ketua_prodi', data.data.prodi);
                    }
                }
                if (emailField) {
                    emailField.value = data.data.email || '';
                    console.log('Set email to:', data.data.email);
                    console.log('Email field value after setting:', emailField.value);
                } else {
                    console.error('Email field not found for ketua');
                }
                if (noHpField) {
                    noHpField.value = data.data.no_hp || '';
                    console.log('Set no_hp to:', data.data.no_hp);
                    console.log('No HP field value after setting:', noHpField.value);
                } else {
                    console.error('No HP field not found for ketua');
                }
                
                // Show success message (disabled)
                // showToast(`Data ketua tim dengan NIM ${nim} berhasil diisi otomatis!`, 'success');
                console.log('Auto-fill ketua berhasil!');
                
                // Clear any previous errors
                clearError('ketua_nim');
                clearError('ketua_nama');
                clearError('ketua_prodi');
                clearError('ketua_fakultas');
                clearError('ketua_email');
                clearError('ketua_no_hp');
                
                // Trigger form validation
                validateFormForSubmit();
            } else {
                console.log('Data ketua tidak ditemukan atau format response salah:', data);
                showError('ketua_nim', 'Mahasiswa dengan NIM tersebut tidak ditemukan!');
                showToast('Mahasiswa dengan NIM tersebut tidak ditemukan!', 'error');
            }
        })
        .catch((error) => {
            console.error('Error in autofillKetua:', error);
            showError('ketua_nim', 'Gagal mengambil data mahasiswa! Silakan coba lagi.');
            showToast('Gagal mengambil data mahasiswa! Silakan coba lagi.', 'error');
        });
    }

    function clearKetuaFields() {
        // Don't clear NIM field - only clear other fields
        document.getElementById('ketua_nama').value = '';
        document.getElementById('ketua_prodi').value = '';
        document.getElementById('ketua_fakultas').value = '';
        document.getElementById('ketua_email').value = '';
        document.getElementById('ketua_no_hp').value = '';
        
        // Clear prodi dropdown options
        const prodiSelect = document.getElementById('ketua_prodi');
        prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';
        
        // Clear any error messages
        clearError('ketua_nama');
        clearError('ketua_prodi');
        clearError('ketua_fakultas');
        clearError('ketua_email');
        clearError('ketua_no_hp');
    }

    // Setup fakultas-prodi dropdown functionality
    function setupFakultasProdiDropdowns() {
        const fakultasFields = [
            'ketua_fakultas', 'anggota1_fakultas', 'anggota2_fakultas', 
            'anggota3_fakultas', 'anggota4_fakultas'
        ];
        
        fakultasFields.forEach(fakultasField => {
            const select = document.getElementById(fakultasField);
            if (select) {
                select.addEventListener('change', function() {
                    const selectedFakultas = this.value;
                    const memberType = fakultasField.replace('_fakultas', '');
                    const prodiField = memberType + '_prodi';
                    
                    // Get the selected option text instead of value
                    const selectedOption = this.options[this.selectedIndex];
                    const fakultasNama = selectedOption ? selectedOption.textContent : '';
                    
                    updateProdiDropdown(fakultasNama, prodiField);
                });
            }
        });
    }

    // Update prodi dropdown based on selected fakultas
    function updateProdiDropdown(fakultasNama, prodiFieldId, selectedProdi = null) {
        const prodiSelect = document.getElementById(prodiFieldId);
        if (!prodiSelect) return;
        
        // Clear existing options
        prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';
        
        if (!fakultasNama) {
            return;
        }
        
        // Fetch prodi data - get fakultas ID first
        const fakultasSelect = document.querySelector('select[id*="fakultas"]');
        let fakultasId = null;
        
        // Find the fakultas ID from the first fakultas dropdown
        if (fakultasSelect) {
            const options = fakultasSelect.querySelectorAll('option');
            for (let option of options) {
                if (option.textContent === fakultasNama) {
                    fakultasId = option.value;
                    break;
                }
            }
        }
        
        if (!fakultasId) {
            console.error('Fakultas ID not found for:', fakultasNama);
            return;
        }
        
        fetch(`/get-prodi/${fakultasId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Prodi response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Prodi response data:', data);
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(prodi => {
                    const option = document.createElement('option');
                    option.value = prodi.nama_prodi;
                    option.textContent = prodi.nama_prodi;
                    prodiSelect.appendChild(option);
                });
                
                // Set selected prodi if provided
                if (selectedProdi) {
                    prodiSelect.value = selectedProdi;
                    console.log(`Set ${prodiFieldId} to:`, selectedProdi);
                }
                
                // Trigger form validation after prodi dropdown is updated
                validateFormForSubmit();
            }
        })
        .catch(error => {
            console.error('Error fetching prodi data:', error);
            showToast('Gagal memuat data program studi. Silakan coba lagi.', 'error');
        });
    }







    // Show toast notification (inherited from layout)
    function showToast(message, type = 'info') {
        // Check if global showToast function exists and is different from this one
        if (typeof window.showToast === 'function' && window.showToast !== showToast) {
            window.showToast(message, type);
        } else {
            // Fallback toast implementation
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
    }



</script>
@endsection 