@extends('mainlayout.app')

@section('title', 'Pengajuan Proposal PKM')

@if(isset($statusPendaftaran) && $statusPendaftaran === 'tertutup')
    @section('content')
    <div class="max-w-3xl mx-auto mt-8">
        <x-breadcrumb :items="[
            ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
            ['label' => 'Ajukan Proposal', 'active' => true],
        ]" />

        <x-ui.card className="overflow-hidden border border-red-200 mt-6 shadow-md shadow-red-500/10">
            <div class="bg-gradient-to-r from-red-600 to-rose-700 text-white p-6 text-center">
                <h4 class="font-bold text-xl m-0 flex items-center justify-center">
                    <i class="fas fa-lock mr-3 text-red-200"></i>
                    Sistem Pendaftaran Ditutup
                </h4>
            </div>
            <div class="p-10 text-center bg-white">
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-red-50 text-red-500 mb-6 border-4 border-red-100">
                        <i class="fas fa-calendar-times text-4xl"></i>
                    </div>
                    <h5 class="text-red-600 font-bold text-2xl mb-3">Pendaftaran PKM Sedang Ditutup</h5>
                    <p class="text-slate-600 max-w-lg mx-auto text-lg leading-relaxed">
                        Saat ini sistem pendaftaran proposal PKM sedang ditutup oleh operator.
                        Silakan cek kembali nanti atau hubungi operator untuk informasi lebih lanjut.
                    </p>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('mahasiswa.dashboard') }}" class="px-6 py-3 bg-white border border-navy-600 text-navy-700 font-bold rounded-lg hover:bg-navy-50 transition-colors shadow-sm inline-flex items-center justify-center">
                        <i class="fas fa-home mr-2"></i>
                        Kembali ke Dashboard
                    </a>
                    <a href="{{ route('mahasiswa.proposal.index') }}" class="px-6 py-3 bg-navy-600 text-white font-bold rounded-lg hover:bg-navy-700 transition-colors shadow-md inline-flex items-center justify-center">
                        <i class="fas fa-eye mr-2"></i>
                        Lihat Proposal Saya
                    </a>
                </div>
            </div>
        </x-ui.card>
    </div>
    @endsection
@else

@section('styles')
<style>
    .file-upload-area {
        transition: all 0.3s ease;
    }
    .file-upload-area.dragover {
        border-color: var(--color-navy-500);
        background-color: var(--color-navy-50);
        transform: scale(1.02);
    }
    .file-info {
        display: none;
    }
    .file-info.show {
        display: block;
    }
    .error-highlight {
        border-color: #dc2626 !important;
        box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.2) !important;
    }
    .error-message {
        color: #dc2626;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: none;
    }
    .error-message.show {
        display: block;
    }
</style>
@endsection

@endif

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
        ['label' => 'Ajukan Proposal', 'active' => true],
    ]" />

    <x-page-header 
        title="Ajukan Proposal PKM" 
        subtitle="UNIVERSITAS UDAYANA" />

    <!-- Error Alert -->
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md relative mb-4">
            <h5 class="text-red-800 font-bold flex items-center mb-2">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Terdapat Kesalahan dalam Form
            </h5>
            <p class="text-red-700 text-sm mb-2">Mohon perbaiki kesalahan berikut sebelum melanjutkan:</p>
            <ul class="text-red-700 text-sm list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Special Error Messages for Duplicate NIMs -->
    @if(isset($errors) && $errors->has('nim_duplicate'))
        <div class="mb-8">
            <x-ui.alert type="danger" icon="fas fa-users-slash">
                <div class="font-bold mb-2">Anggota Tim Sudah Terdaftar:</div>
                <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->get('nim_duplicate') as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            </x-ui.alert>
        </div>
    @endif

    @if(isset($errors) && $errors->has('team_nim'))
        <div class="mb-8">
            <x-ui.alert type="warning" icon="fas fa-exclamation-triangle">
                <div class="font-bold mb-2">NIM Ganda Terdeteksi:</div>
                <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->get('team_nim') as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            </x-ui.alert>
        </div>
    @endif

    @if(isset($errors) && $errors->has('team_size'))
        <div class="mb-8">
            <x-ui.alert type="warning" icon="fas fa-users">
                <div class="font-bold mb-2">Jumlah Anggota Tim:</div>
                <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->get('team_size') as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            </x-ui.alert>
        </div>
    @endif

    @if(isset($errors) && $errors->has('optional_members'))
        <div class="mb-8">
            <x-ui.alert type="info" icon="fas fa-info-circle">
                <div class="font-bold mb-2">Anggota Opsional:</div>
                <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->get('optional_members') as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            </x-ui.alert>
        </div>
    @endif

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-md relative mb-4">
            <div class="flex items-center text-green-800">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Warning Message -->
    @if(session('warning'))
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-r-md relative mb-4">
            <div class="flex items-center text-yellow-800">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('warning') }}
            </div>
        </div>
    @endif

    <form action="{{ route('mahasiswa.proposal.store') }}" method="POST" enctype="multipart/form-data" id="proposalForm">
        @csrf
        
        <!-- Informasi Proposal -->
        <x-ui.card class="mb-6 p-6">
            <h4 class="text-lg font-bold text-navy-700 mb-6 pb-2 border-b-2 border-navy-700 flex items-center">
                <i class="fas fa-clipboard-list me-2"></i>Informasi Proposal PKM
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div class="md:col-span-8">
                    <label for="judul" class="block text-sm font-medium text-slate-700 mb-1 required-field">Judul Proposal</label>
                    <input type="text" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('judul') border-red-500 @enderror" id="judul" name="judul" placeholder="Masukkan judul proposal PKM" value="{{ old('judul') }}" required>
                    <div class="text-xs text-slate-500 mt-1">Judul harus jelas, spesifik, dan mencerminkan isi proposal</div>
                    <div class="error-message" id="judul_error"></div>
                    @error('judul')
                        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="md:col-span-4">
                    <label for="skim" class="block text-sm font-medium text-slate-700 mb-1 required-field">Skim/Jenis PKM</label>
                    <select class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('skim') border-red-500 @enderror" id="skim" name="skim" required>
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
                        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="md:col-span-6">
                    <label for="tahun_ajaran" class="block text-sm font-medium text-slate-700 mb-1">Tahun Ajaran</label>
                    <input type="text" class="flex h-10 w-full rounded-md border border-slate-300 bg-slate-100 px-3 py-2 text-sm focus:outline-none" id="tahun_ajaran" name="tahun_ajaran" value="{{ \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru() }}" readonly>
                </div>
                <div class="md:col-span-6">
                    <label for="tanggal_pengajuan" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Pengajuan</label>
                    <input type="date" class="flex h-10 w-full rounded-md border border-slate-300 bg-slate-100 px-3 py-2 text-sm focus:outline-none" id="tanggal_pengajuan" name="tanggal_pengajuan" value="{{ date('Y-m-d') }}" readonly>
                </div>
                {{-- Dana Belmawa (Kemendiktisaintek) --}}
                <div class="md:col-span-6">
                    <label for="dana_diajukan_belmawa" class="block text-sm font-medium text-slate-700 mb-1 required-field">
                        <i class="fas fa-university me-1 text-navy-500"></i>Dana dari Belmawa (Kemendiktisaintek)
                    </label>
                    <div class="flex w-full">
                        <span class="flex items-center px-3 rounded-l-md border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-sm">Rp</span>
                        <input type="text"
                            class="flex-1 h-10 rounded-r-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent js-format-id-int @error('dana_diajukan_belmawa') border-red-500 @enderror"
                            id="dana_diajukan_belmawa"
                            name="dana_diajukan_belmawa"
                            placeholder="0"
                            inputmode="numeric"
                            value="{{ old('dana_diajukan_belmawa') }}"
                            data-dana-type="belmawa">
                    </div>
                    <div class="text-xs text-slate-500 mt-1" id="dana_belmawa_help_text">
                        Rp 0 – Rp 8.000.000
                    </div>
                    <div class="error-message" id="dana_diajukan_belmawa_error"></div>
                    @error('dana_diajukan_belmawa')
                        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                    @enderror
                </div>
                {{-- Dana Universitas --}}
                <div class="md:col-span-6">
                    <label for="dana_diajukan_operator" class="block text-sm font-medium text-slate-700 mb-1 required-field">
                        <i class="fas fa-building me-1 text-green-600"></i>Dana dari Universitas
                    </label>
                    <div class="flex w-full">
                        <span class="flex items-center px-3 rounded-l-md border border-r-0 border-slate-300 bg-slate-50 text-slate-500 text-sm">Rp</span>
                        <input type="text"
                            class="flex-1 h-10 rounded-r-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent js-format-id-int @error('dana_diajukan_operator') border-red-500 @enderror"
                            id="dana_diajukan_operator"
                            name="dana_diajukan_operator"
                            placeholder="0"
                            inputmode="numeric"
                            value="{{ old('dana_diajukan_operator') }}"
                            data-dana-type="operator">
                    </div>
                    <div class="text-xs text-slate-500 mt-1" id="dana_operator_help_text">
                        Rp 0 – Rp 2.000.000
                    </div>
                    <div class="error-message" id="dana_diajukan_operator_error"></div>
                    @error('dana_diajukan_operator')
                        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="md:col-span-6">
                    <label for="dosen_pembimbing" class="block text-sm font-medium text-slate-700 mb-1 required-field">Dosen Pendamping</label>
                    <select class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('dosen_pembimbing') border-red-500 @enderror" id="dosen_pembimbing" name="dosen_pembimbing" required>
                        <option value="">-- Pilih Dosen Pendamping --</option>
                        @foreach($dosens as $dosen)
                            <option value="{{ $dosen->nama_dosen }}" {{ old('dosen_pembimbing') == $dosen->nama_dosen ? 'selected' : '' }}>{{ $dosen->nama_dosen }}{{ $dosen->gelar_belakang ? ', '.$dosen->gelar_belakang : '' }}</option>
                        @endforeach
                    </select>
                    <div class="error-message" id="dosen_pembimbing_error"></div>
                    @error('dosen_pembimbing')
                        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </x-ui.card>

        <!-- Identitas Ketua Tim -->
        <x-ui.card class="mb-6 p-6">
            <h4 class="text-lg font-bold text-navy-700 mb-6 pb-2 border-b-2 border-navy-700 flex items-center">
                <i class="fas fa-user-tie me-2"></i>Identitas Ketua Tim
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="ketua_nama" class="block text-sm font-medium text-slate-700 mb-1 required-field">Nama Lengkap</label>
                    <input type="text" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('ketua_nama') border-red-500 @enderror" id="ketua_nama" name="ketua_nama" placeholder="Masukkan nama lengkap" value="{{ old('ketua_nama') }}" required>
                    <div class="error-message" id="ketua_nama_error"></div>
                    @error('ketua_nama')
                        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="ketua_nim" class="block text-sm font-medium text-slate-700 mb-1 required-field">NIM</label>
                    <input type="text" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('ketua_nim') border-red-500 @enderror" id="ketua_nim" name="ketua_nim" placeholder="Masukkan NIM" value="{{ old('ketua_nim') }}" required>
                    <div class="error-message" id="ketua_nim_error"></div>
                    @error('ketua_nim')
                        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="ketua_fakultas" class="block text-sm font-medium text-slate-700 mb-1 required-field">Fakultas</label>
                    <select class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('ketua_fakultas') border-red-500 @enderror" id="ketua_fakultas" name="ketua_fakultas" required>
                        <option value="">-- Pilih Fakultas --</option>
                        @foreach($fakultas as $fak)
                            <option value="{{ $fak->nama_fakultas }}" {{ old('ketua_fakultas') == $fak->nama_fakultas ? 'selected' : '' }}>{{ $fak->nama_fakultas }}</option>
                        @endforeach
                    </select>
                    <div class="error-message" id="ketua_fakultas_error"></div>
                    @error('ketua_fakultas')
                        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="ketua_prodi" class="block text-sm font-medium text-slate-700 mb-1 required-field">Program Studi</label>
                    <select class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('ketua_prodi') border-red-500 @enderror" id="ketua_prodi" name="ketua_prodi" required>
                        <option value="">-- Pilih Program Studi --</option>
                    </select>
                    <div class="error-message" id="ketua_prodi_error"></div>
                    @error('ketua_prodi')
                        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="ketua_email" class="block text-sm font-medium text-slate-700 mb-1 required-field">Email</label>
                    <input type="email" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('ketua_email') border-red-500 @enderror" id="ketua_email" name="ketua_email" placeholder="Masukkan email" value="{{ old('ketua_email') }}" required>
                    <div class="error-message" id="ketua_email_error"></div>
                    @error('ketua_email')
                        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label for="ketua_no_hp" class="block text-sm font-medium text-slate-700 mb-1 required-field">No. HP</label>
                    <input type="tel" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('ketua_no_hp') border-red-500 @enderror" id="ketua_no_hp" name="ketua_no_hp" placeholder="Masukkan nomor HP" value="{{ old('ketua_no_hp') }}" required>
                    <div class="error-message" id="ketua_no_hp_error"></div>
                    @error('ketua_no_hp')
                        <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </x-ui.card>

        <!-- Anggota Tim -->
        <x-ui.card class="mb-6 p-6">
            <h4 class="text-lg font-bold text-navy-700 mb-6 pb-2 border-b-2 border-navy-700 flex items-center">
                <i class="fas fa-users me-2"></i>Anggota Tim
            </h4>
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-md mb-6">
                <div class="flex items-center mb-2 text-blue-800 font-bold">
                    <i class="fas fa-info-circle me-2"></i>
                    Ketentuan Anggota Tim:
                </div>
                <ul class="list-disc pl-5 text-sm text-blue-700 space-y-1">
                    <li><strong>Total Tim:</strong> Minimal 3 orang, maksimal 5 orang</li>
                </ul>
            </div>
            
            <!-- Anggota 1 -->
            <div class="border border-slate-200 rounded-xl p-6 mb-6 bg-slate-50 hover:border-navy-400 hover:shadow-md transition-all duration-300">
                <div class="text-navy-700 font-bold mb-4 flex items-center">
                    <i class="fas fa-user me-2"></i>Anggota 1 <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full ms-2">Wajib</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="anggota1_nama" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota1_nama') border-red-500 @enderror" id="anggota1_nama" name="anggota1_nama" placeholder="Masukkan nama" value="{{ old('anggota1_nama') }}">
                        <div class="error-message" id="anggota1_nama_error"></div>
                        @error('anggota1_nama')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota1_nim" class="block text-sm font-medium text-slate-700 mb-1">NIM</label>
                        <input type="text" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota1_nim') border-red-500 @enderror" id="anggota1_nim" name="anggota1_nim" placeholder="Masukkan NIM" value="{{ old('anggota1_nim') }}">
                        <div class="error-message" id="anggota1_nim_error"></div>
                        @error('anggota1_nim')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota1_fakultas" class="block text-sm font-medium text-slate-700 mb-1">Fakultas</label>
                        <select class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota1_fakultas') border-red-500 @enderror" id="anggota1_fakultas" name="anggota1_fakultas">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $fak)
                                <option value="{{ $fak->nama_fakultas }}" {{ old('anggota1_fakultas') == $fak->nama_fakultas ? 'selected' : '' }}>{{ $fak->nama_fakultas }}</option>
                            @endforeach
                        </select>
                        <div class="error-message" id="anggota1_fakultas_error"></div>
                        @error('anggota1_fakultas')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota1_prodi" class="block text-sm font-medium text-slate-700 mb-1">Program Studi</label>
                        <select class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota1_prodi') border-red-500 @enderror" id="anggota1_prodi" name="anggota1_prodi">
                            <option value="">-- Pilih Program Studi --</option>
                        </select>
                        <div class="error-message" id="anggota1_prodi_error"></div>
                        @error('anggota1_prodi')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota1_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota1_email') border-red-500 @enderror" id="anggota1_email" name="anggota1_email" placeholder="Masukkan email" value="{{ old('anggota1_email') }}">
                        <div class="error-message" id="anggota1_email_error"></div>
                        @error('anggota1_email')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota1_no_hp" class="block text-sm font-medium text-slate-700 mb-1">No. HP</label>
                        <input type="tel" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota1_no_hp') border-red-500 @enderror" id="anggota1_no_hp" name="anggota1_no_hp" placeholder="Masukkan nomor HP" value="{{ old('anggota1_no_hp') }}">
                        <div class="error-message" id="anggota1_no_hp_error"></div>
                        @error('anggota1_no_hp')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Anggota 2 -->
            <div class="border border-slate-200 rounded-xl p-6 mb-6 bg-slate-50 hover:border-navy-400 hover:shadow-md transition-all duration-300">
                <div class="text-navy-700 font-bold mb-4 flex items-center">
                    <i class="fas fa-user me-2"></i>Anggota 2 <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full ms-2">Wajib</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="anggota2_nama" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota2_nama') border-red-500 @enderror" id="anggota2_nama" name="anggota2_nama" placeholder="Masukkan nama" value="{{ old('anggota2_nama') }}">
                        <div class="error-message" id="anggota2_nama_error"></div>
                        @error('anggota2_nama')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota2_nim" class="block text-sm font-medium text-slate-700 mb-1">NIM</label>
                        <input type="text" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota2_nim') border-red-500 @enderror" id="anggota2_nim" name="anggota2_nim" placeholder="Masukkan NIM" value="{{ old('anggota2_nim') }}">
                        <div class="error-message" id="anggota2_nim_error"></div>
                        @error('anggota2_nim')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota2_fakultas" class="block text-sm font-medium text-slate-700 mb-1">Fakultas</label>
                        <select class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota2_fakultas') border-red-500 @enderror" id="anggota2_fakultas" name="anggota2_fakultas">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $fak)
                                <option value="{{ $fak->nama_fakultas }}" {{ old('anggota2_fakultas') == $fak->nama_fakultas ? 'selected' : '' }}>{{ $fak->nama_fakultas }}</option>
                            @endforeach
                        </select>
                        <div class="error-message" id="anggota2_fakultas_error"></div>
                        @error('anggota2_fakultas')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota2_prodi" class="block text-sm font-medium text-slate-700 mb-1">Program Studi</label>
                        <select class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota2_prodi') border-red-500 @enderror" id="anggota2_prodi" name="anggota2_prodi">
                            <option value="">-- Pilih Program Studi --</option>
                        </select>
                        <div class="error-message" id="anggota2_prodi_error"></div>
                        @error('anggota2_prodi')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota2_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota2_email') border-red-500 @enderror" id="anggota2_email" name="anggota2_email" placeholder="Masukkan email" value="{{ old('anggota2_email') }}">
                        <div class="error-message" id="anggota2_email_error"></div>
                        @error('anggota2_email')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota2_no_hp" class="block text-sm font-medium text-slate-700 mb-1">No. HP</label>
                        <input type="tel" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota2_no_hp') border-red-500 @enderror" id="anggota2_no_hp" name="anggota2_no_hp" placeholder="Masukkan nomor HP" value="{{ old('anggota2_no_hp') }}">
                        <div class="error-message" id="anggota2_no_hp_error"></div>
                        @error('anggota2_no_hp')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Anggota 3 -->
            <div class="border border-slate-200 rounded-xl p-6 mb-6 bg-slate-50 hover:border-navy-400 hover:shadow-md transition-all duration-300">
                <div class="text-navy-700 font-bold mb-4 flex items-center">
                    <i class="fas fa-user me-2"></i>Anggota 3 <span class="bg-slate-200 text-slate-700 text-xs font-semibold px-2 py-1 rounded-full ms-2">Opsional</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="anggota3_nama" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota3_nama') border-red-500 @enderror" id="anggota3_nama" name="anggota3_nama" placeholder="Masukkan nama" value="{{ old('anggota3_nama') }}">
                        <div class="error-message" id="anggota3_nama_error"></div>
                        @error('anggota3_nama')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota3_nim" class="block text-sm font-medium text-slate-700 mb-1">NIM</label>
                        <input type="text" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota3_nim') border-red-500 @enderror" id="anggota3_nim" name="anggota3_nim" placeholder="Masukkan NIM" value="{{ old('anggota3_nim') }}">
                        <div class="error-message" id="anggota3_nim_error"></div>
                        @error('anggota3_nim')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota3_fakultas" class="block text-sm font-medium text-slate-700 mb-1">Fakultas</label>
                        <select class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota3_fakultas') border-red-500 @enderror" id="anggota3_fakultas" name="anggota3_fakultas">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $fak)
                                <option value="{{ $fak->nama_fakultas }}" {{ old('anggota3_fakultas') == $fak->nama_fakultas ? 'selected' : '' }}>{{ $fak->nama_fakultas }}</option>
                            @endforeach
                        </select>
                        <div class="error-message" id="anggota3_fakultas_error"></div>
                        @error('anggota3_fakultas')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota3_prodi" class="block text-sm font-medium text-slate-700 mb-1">Program Studi</label>
                        <select class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota3_prodi') border-red-500 @enderror" id="anggota3_prodi" name="anggota3_prodi">
                            <option value="">-- Pilih Program Studi --</option>
                        </select>
                        <div class="error-message" id="anggota3_prodi_error"></div>
                        @error('anggota3_prodi')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota3_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota3_email') border-red-500 @enderror" id="anggota3_email" name="anggota3_email" placeholder="Masukkan email" value="{{ old('anggota3_email') }}">
                        <div class="error-message" id="anggota3_email_error"></div>
                        @error('anggota3_email')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota3_no_hp" class="block text-sm font-medium text-slate-700 mb-1">No. HP</label>
                        <input type="tel" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota3_no_hp') border-red-500 @enderror" id="anggota3_no_hp" name="anggota3_no_hp" placeholder="Masukkan nomor HP" value="{{ old('anggota3_no_hp') }}">
                        <div class="error-message" id="anggota3_no_hp_error"></div>
                        @error('anggota3_no_hp')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Anggota 4 -->
            <div class="border border-slate-200 rounded-xl p-6 mb-6 bg-slate-50 hover:border-navy-400 hover:shadow-md transition-all duration-300">
                <div class="text-navy-700 font-bold mb-4 flex items-center">
                    <i class="fas fa-user me-2"></i>Anggota 4 <span class="bg-slate-200 text-slate-700 text-xs font-semibold px-2 py-1 rounded-full ms-2">Opsional</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="anggota4_nama" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota4_nama') border-red-500 @enderror" id="anggota4_nama" name="anggota4_nama" placeholder="Masukkan nama" value="{{ old('anggota4_nama') }}">
                        <div class="error-message" id="anggota4_nama_error"></div>
                        @error('anggota4_nama')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota4_nim" class="block text-sm font-medium text-slate-700 mb-1">NIM</label>
                        <input type="text" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota4_nim') border-red-500 @enderror" id="anggota4_nim" name="anggota4_nim" placeholder="Masukkan NIM" value="{{ old('anggota4_nim') }}">
                        <div class="error-message" id="anggota4_nim_error"></div>
                        @error('anggota4_nim')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota4_fakultas" class="block text-sm font-medium text-slate-700 mb-1">Fakultas</label>
                        <select class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota4_fakultas') border-red-500 @enderror" id="anggota4_fakultas" name="anggota4_fakultas">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach($fakultas as $fak)
                                <option value="{{ $fak->nama_fakultas }}" {{ old('anggota4_fakultas') == $fak->nama_fakultas ? 'selected' : '' }}>{{ $fak->nama_fakultas }}</option>
                            @endforeach
                        </select>
                        <div class="error-message" id="anggota4_fakultas_error"></div>
                        @error('anggota4_fakultas')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota4_prodi" class="block text-sm font-medium text-slate-700 mb-1">Program Studi</label>
                        <select class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota4_prodi') border-red-500 @enderror" id="anggota4_prodi" name="anggota4_prodi">
                            <option value="">-- Pilih Program Studi --</option>
                        </select>
                        <div class="error-message" id="anggota4_prodi_error"></div>
                        @error('anggota4_prodi')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota4_email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                        <input type="email" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota4_email') border-red-500 @enderror" id="anggota4_email" name="anggota4_email" placeholder="Masukkan email" value="{{ old('anggota4_email') }}">
                        <div class="error-message" id="anggota4_email_error"></div>
                        @error('anggota4_email')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="anggota4_no_hp" class="block text-sm font-medium text-slate-700 mb-1">No. HP</label>
                        <input type="tel" class="flex h-10 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-navy-400 focus:border-transparent @error('anggota4_no_hp') border-red-500 @enderror" id="anggota4_no_hp" name="anggota4_no_hp" placeholder="Masukkan nomor HP" value="{{ old('anggota4_no_hp') }}">
                        <div class="error-message" id="anggota4_no_hp_error"></div>
                        @error('anggota4_no_hp')
                            <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </x-ui.card>

        <!-- Upload Dokumen -->
        <x-ui.card class="mb-6 p-6">
            <h4 class="text-lg font-bold text-navy-700 mb-6 pb-2 border-b-2 border-navy-700 flex items-center">
                <i class="fas fa-upload me-2"></i>Upload Dokumen
            </h4>
            
            <!-- Upload Proposal -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2 required-field">File Proposal (PDF)</label>
                <div class="file-upload-area border-2 border-dashed border-slate-300 rounded-xl p-8 text-center cursor-pointer hover:bg-slate-50 transition-colors @error('proposal_file') border-red-500 bg-red-50 @enderror" id="proposalUploadArea">
                    <div class="text-4xl text-navy-500 mb-3">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <h5 class="text-lg font-medium text-slate-800 mb-1">Upload File Proposal</h5>
                    <p class="text-sm text-slate-500 mb-4">Drag & drop file PDF di sini atau klik untuk memilih file</p>
                    <input type="file" id="proposal_file" name="proposal_file" accept=".pdf" style="display: none;" required>
                    <button type="button" class="inline-flex items-center justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-navy-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50" onclick="document.getElementById('proposal_file').click()">
                        <i class="fas fa-folder-open me-2"></i>Pilih File
                    </button>
                </div>
                <div class="file-info mt-4 p-4 border border-slate-200 rounded-lg bg-slate-50" id="proposalFileInfo">
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <strong class="text-sm text-slate-800 block" id="proposalFileName">Nama file</strong>
                            <small class="text-xs text-slate-500" id="proposalFileSize">Ukuran file</small>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700 p-1" onclick="removeFile('proposal')">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-1.5 mb-1 overflow-hidden">
                        <div class="bg-navy-500 h-1.5 rounded-full transition-all duration-300" id="proposalProgress" style="width: 0%"></div>
                    </div>
                </div>
                @error('proposal_file')
                    <div class="text-xs text-red-500 mt-2 block">{{ $message }}</div>
                @enderror
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-md mb-4">
                <div class="flex items-center mb-2 text-blue-800 font-bold">
                    <i class="fas fa-info-circle me-2"></i>
                    Ketentuan Upload:
                </div>
                <ul class="list-disc pl-5 text-sm text-blue-700 space-y-1">
                    <li>Format file harus PDF.  </li>
                    <li>Ukuran maksimal 5MB per file.</li>
                    <li>File proposal harus lengkap sesuai template.</li>
                </ul>
            </div>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md">
                <div class="flex items-center mb-2 text-red-800 font-bold">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Perhatian Mahasiswa:
                </div>
                <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
                    <li>Pastikan anggota tim tidak pernah ikut mengajukan proposal di tahun ini.</li>
                    <li>Jika mahasiswa ingin keluar dari sebuah tim setelah pengajuan, disarankan untuk menghubungi dosen pendamping proposal untuk menolak validasi proposal.</li>
                </ul>
            </div>

        </x-ui.card>

        <!-- Submit Button -->
        <div class="text-center mt-8 mb-4">
            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-navy-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-navy-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors" id="submitBtn" disabled>
                <i class="fas fa-paper-plane me-2"></i>Ajukan Proposal
            </button>
            <div class="mt-3">
                <small class="text-sm text-slate-500" id="submitHelpText">
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
    if (isset($errors) && $errors->any()) {
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
            'judul', 'skim', 'dana_diajukan_belmawa', 'dana_diajukan_operator', 'dosen_pembimbing',
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

        // Validate dana Belmawa & Universitas
        const selectedSkim = document.getElementById('skim') ? document.getElementById('skim').value : '';
        const insentifSkims = ['GFT', 'AI'];
        const isInsentif = insentifSkims.includes(selectedSkim);

        const danaBelmawa = document.getElementById('dana_diajukan_belmawa');
        const danaOperator = document.getElementById('dana_diajukan_operator');

        if (danaBelmawa) {
            const belmawaNilai = danaBelmawa.value ? parseInt((window.parseAngkaIndonesia ? window.parseAngkaIndonesia(danaBelmawa.value) : danaBelmawa.value).toString().replace(/\D/g,''), 10) : 0;
            if (isInsentif) {
                if (belmawaNilai !== 0) {
                    showError('dana_diajukan_belmawa', 'PKM Insentif tidak memiliki pendanaan. Dana harus 0!');
                    hasErrors = true;
                }
            } else {
                if (belmawaNilai > 8000000) {
                    showError('dana_diajukan_belmawa', 'Dana Belmawa maksimal Rp 8.000.000!');
                    hasErrors = true;
                }
            }
        }

        if (danaOperator) {
            const operatorNilai = danaOperator.value ? parseInt((window.parseAngkaIndonesia ? window.parseAngkaIndonesia(danaOperator.value) : danaOperator.value).toString().replace(/\D/g,''), 10) : 0;
            if (isInsentif) {
                if (operatorNilai !== 0) {
                    showError('dana_diajukan_operator', 'PKM Insentif tidak memiliki pendanaan. Dana harus 0!');
                    hasErrors = true;
                }
            } else {
                if (operatorNilai > 2000000) {
                    showError('dana_diajukan_operator', 'Dana Universitas maksimal Rp 2.000.000!');
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
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 p-4 sm:p-0 backdrop-blur-sm transition-opacity';
            modal.innerHTML = `
                <div class="relative w-full max-w-md transform overflow-hidden rounded-xl bg-white text-left align-middle shadow-xl transition-all">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-bold leading-6 text-navy-700">Konfirmasi Pengajuan Proposal</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-slate-500 mb-4">Apakah Anda yakin ingin mengajukan proposal ini?</p>
                                    <div class="rounded-md bg-yellow-50 p-4 border border-yellow-200">
                                        <div class="flex">
                                            <div class="flex-shrink-0"><i class="fas fa-info-circle text-yellow-500 mt-0.5"></i></div>
                                            <div class="ml-3"><p class="text-sm text-yellow-800"><strong>Perhatian:</strong> Data yang sudah disubmit tidak dapat diubah.</p></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" class="inline-flex w-full justify-center rounded-md bg-navy-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-navy-700 sm:ml-3 sm:w-auto" onclick="this.closest('.fixed').dispatchEvent(new Event('confirmed'))">
                            <i class="fas fa-check me-2 mt-0.5"></i>Ya, Ajukan
                        </button>
                        <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto" onclick="this.closest('.fixed').dispatchEvent(new Event('canceled'))">
                            Batal
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            modal.addEventListener('confirmed', () => {
                modal.remove();
                resolve(true);
            });
            modal.addEventListener('canceled', () => {
                modal.remove();
                resolve(false);
            });
            
            // Close on backdrop click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                    resolve(false);
                }
            });
        });
        
        if (!confirmed) {
            return false;
        }

        // Show loading
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mengajukan...';
        submitBtn.disabled = true;

        showToast('Proposal sedang diajukan...', 'info');

        // Submit form
        this.submit();
    });

    // Function to validate form for submit button state
    function validateFormForSubmit() {
        if (!submitBtn || !submitHelpText) return;
        
        const requiredFields = [
            'judul', 'skim', 'dana_diajukan_belmawa', 'dana_diajukan_operator', 'dosen_pembimbing',
            'ketua_nama', 'ketua_nim', 'ketua_prodi', 'ketua_fakultas', 
            'ketua_email', 'ketua_no_hp', 'anggota1_nama', 'anggota1_nim',
            'anggota1_prodi', 'anggota1_fakultas', 'anggota1_email', 'anggota1_no_hp',
            'anggota2_nama', 'anggota2_nim', 'anggota2_prodi', 'anggota2_fakultas',
            'anggota2_email', 'anggota2_no_hp'
        ];
        
        let isValid = true;
        let missingFields = [];
        let validationErrors = [];
        
        // Check required fields
        for (let fieldId of requiredFields) {
            const field = document.getElementById(fieldId);
            if (!field || !field.value.trim()) {
                isValid = false;
                missingFields.push(fieldId);
            }
        }
        
        if (missingFields.length > 0) {
            validationErrors.push(`Lengkapi ${missingFields.length} field wajib`);
        }
        
        // Check file upload
        const proposalFile = document.getElementById('proposal_file');
        if (!proposalFile || !proposalFile.files[0]) {
            isValid = false;
            if (!missingFields.includes('proposal_file')) missingFields.push('proposal_file');
            if (missingFields.length === 1) validationErrors.push('Upload file proposal (.pdf)');
        }
        
        // Check judul length
        const judulField = document.getElementById('judul');
        if (judulField && judulField.value.trim()) {
            const judul = judulField.value.trim();
            if (judul.length < 10 || judul.length > 200) {
                isValid = false;
                validationErrors.push('Judul harus antara 10-200 karakter');
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
                    validationErrors.push('Format NIM tidak valid (minimal 8 angka)');
                    break;
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
                    validationErrors.push('Format email tidak valid');
                    break;
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
                    validationErrors.push('Nomor HP tidak valid (minimal 10 angka)');
                    break;
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
                    validationErrors.push(`Lengkapi semua data untuk ${member.replace('anggota', 'Anggota ')} atau kosongkan semuanya`);
                }
            }
        }
        
        // Check dana based on skim type
        const selectedSkim = document.getElementById('skim').value;
        const insentifSkims = ['GFT', 'AI'];
        const isInsentif = insentifSkims.includes(selectedSkim);
        
        const belmawaField = document.getElementById('dana_diajukan_belmawa');
        const operatorField = document.getElementById('dana_diajukan_operator');
        
        const belmawaVal = belmawaField ? (parseInt((window.parseAngkaIndonesia ? window.parseAngkaIndonesia(belmawaField.value) : belmawaField.value).toString().replace(/\D/g,''), 10) || 0) : 0;
        const operatorVal = operatorField ? (parseInt((window.parseAngkaIndonesia ? window.parseAngkaIndonesia(operatorField.value) : operatorField.value).toString().replace(/\D/g,''), 10) || 0) : 0;
        
        if (isInsentif) {
            if (belmawaVal !== 0 || operatorVal !== 0) {
                isValid = false;
                validationErrors.push('Dana untuk skema Insentif harus Rp 0');
            }
        } else {
            if (belmawaVal > 8000000 || operatorVal > 2000000 || (belmawaVal === 0 && operatorVal === 0)) {
                isValid = false;
                validationErrors.push('Total dana tidak valid (maks 8jt Belmawa, maks 2jt Universitas)');
            }
        }
        
        // Update button state
        if (isValid) {
            submitBtn.disabled = false;
            submitHelpText.textContent = '✓ Form sudah lengkap, Anda dapat mengajukan proposal';
            submitHelpText.className = 'mt-3 text-sm text-green-600 font-medium block';
        } else {
            submitBtn.disabled = true;
            submitHelpText.textContent = validationErrors.length > 0 ? validationErrors[0] : 'Form belum lengkap';
            submitHelpText.className = 'mt-3 text-sm text-red-500 block';
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
    let danaField = null;       // legacy ref
    let danaBelmawdField = null;
    let danaOperatorField = null;
    let skimField = null;
    let submitBtn = null;
    let submitHelpText = null;
    
    // Auto-fill data mahasiswa yang login
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded - Setting up auto-fill...');
        
        // Initialize global variables
        ketuaNimField = document.getElementById('ketua_nim');
        danaField = document.getElementById('dana_diajukan_belmawa');  // legacy pointer
        danaBelmawdField = document.getElementById('dana_diajukan_belmawa');
        danaOperatorField = document.getElementById('dana_diajukan_operator');
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

        // Format currency inputs (handled by js-format-id-int class)

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

        // Handle skim change to update dana fields behavior
        function updateDanaFieldBehavior() {
            if (!skimField) return;
            const selectedSkim = skimField.value;
            const insentifSkims = ['GFT', 'AI'];
            const isInsentif = insentifSkims.includes(selectedSkim);

            const belmawdHelp = document.getElementById('dana_belmawa_help_text');
            const operatorHelp = document.getElementById('dana_operator_help_text');

            clearError('dana_diajukan_belmawa');
            clearError('dana_diajukan_operator');

            if (isInsentif) {
                // PKM Insentif - tidak ada pendanaan
                [danaBelmawdField, danaOperatorField].forEach(f => {
                    if (!f) return;
                    f.value = '0';
                    f.readOnly = true;
                    f.style.backgroundColor = '#f8f9fa';
                    f.placeholder = '0 (PKM Insentif tidak memiliki pendanaan)';
                });
                if (belmawdHelp) { belmawdHelp.textContent = 'PKM Insentif tidak memiliki pendanaan. Dana otomatis 0.'; belmawdHelp.className = 'form-text text-info'; }
                if (operatorHelp) { operatorHelp.textContent = 'PKM Insentif tidak memiliki pendanaan. Dana otomatis 0.'; operatorHelp.className = 'form-text text-info'; }
            } else {
                // PKM Pendanaan
                if (danaBelmawdField) {
                    danaBelmawdField.readOnly = false;
                    danaBelmawdField.style.backgroundColor = '';
                    danaBelmawdField.placeholder = '0';
                }
                if (danaOperatorField) {
                    danaOperatorField.readOnly = false;
                    danaOperatorField.style.backgroundColor = '';
                    danaOperatorField.placeholder = '0';
                }
                if (belmawdHelp) { belmawdHelp.textContent = 'Rp 0 – Rp 8.000.000'; belmawdHelp.className = 'form-text'; }
                if (operatorHelp) { operatorHelp.textContent = 'Rp 0 – Rp 2.000.000'; operatorHelp.className = 'form-text'; }
            }

            validateFormForSubmit();
        }

        // Add event listener for skim change
        if (skimField) skimField.addEventListener('change', updateDanaFieldBehavior);

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
            
            let bgColor, iconClass, textColor;
            if (type === 'error') {
                bgColor = 'bg-red-100 border-red-500';
                textColor = 'text-red-800';
                iconClass = 'fas fa-exclamation-circle text-red-500';
            } else if (type === 'success') {
                bgColor = 'bg-emerald-100 border-emerald-500';
                textColor = 'text-emerald-800';
                iconClass = 'fas fa-check-circle text-emerald-500';
            } else if (type === 'warning') {
                bgColor = 'bg-amber-100 border-amber-500';
                textColor = 'text-amber-800';
                iconClass = 'fas fa-exclamation-triangle text-amber-500';
            } else {
                bgColor = 'bg-blue-100 border-blue-500';
                textColor = 'text-blue-800';
                iconClass = 'fas fa-info-circle text-blue-500';
            }

            toast.className = `flex items-center p-4 mb-4 text-sm rounded-lg border shadow-lg transition-opacity duration-300 fixed z-[9999] top-5 right-5 min-w-[300px] ${bgColor} ${textColor}`;
            
            toast.innerHTML = `
                <i class="${iconClass} text-xl mr-3"></i>
                <div class="font-medium mr-4">
                    <strong class="block mb-1">${type === 'error' ? 'Error' : type === 'success' ? 'Sukses' : type === 'warning' ? 'Peringatan' : 'Info'}:</strong>
                    ${message}
                </div>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-slate-400 p-1.5 hover:bg-slate-200 inline-flex h-8 w-8 transition-colors text-slate-500" onclick="this.parentElement.remove()" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <i class="fas fa-times"></i>
                </button>
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