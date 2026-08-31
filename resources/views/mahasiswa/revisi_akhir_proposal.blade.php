@extends('mainlayout.app')

@section('title', 'Revisi Akhir Proposal PKM')

@section('styles')

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
            <x-page-header 
                title="Revisi Akhir Proposal PKM" 
                subtitle="Upload file revisi akhir proposal untuk divalidasi oleh dosen pendamping universitas"
            />

            <!-- Informasi Dosen Pendamping Universitas -->
            @if($proposal->dosenPendampingUniversitas)
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-xl p-8 mb-8 shadow-lg shadow-indigo-500/20">
                <h5 class="text-white font-bold text-xl mb-6 flex items-center">
                    <i class="fas fa-user-tie mr-3 text-indigo-200"></i>
                    Dosen Pendamping Universitas
                </h5>
                <div class="flex flex-col gap-3 text-indigo-50">
                    <div class="flex items-center">
                        <div class="w-8 shrink-0"><i class="fas fa-user text-indigo-300 text-lg"></i></div>
                        <div>
                            <strong class="text-white mr-1">Nama:</strong> {{ $proposal->dosenPendampingUniversitas->nama_dosen }}
                            @if($proposal->dosenPendampingUniversitas->gelar_depan)
                                , {{ $proposal->dosenPendampingUniversitas->gelar_depan }}
                            @endif
                            @if($proposal->dosenPendampingUniversitas->gelar_belakang)
                                , {{ $proposal->dosenPendampingUniversitas->gelar_belakang }}
                            @endif
                        </div>
                    </div>
                    @if($proposal->dosenPendampingUniversitas->no_hp_dosen)
                    <div class="flex items-center">
                        <div class="w-8 shrink-0"><i class="fas fa-phone text-indigo-300 text-lg"></i></div>
                        <div>
                            <strong class="text-white mr-1">No. HP:</strong> 
                            <a href="tel:{{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}" class="text-white hover:text-indigo-200 hover:underline transition-colors font-medium">
                                {{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}
                            </a>
                        </div>
                    </div>
                    @endif
                    @if($proposal->dosenPendampingUniversitas->email_dosen)
                    <div class="flex items-center">
                        <div class="w-8 shrink-0"><i class="fas fa-envelope text-indigo-300 text-lg"></i></div>
                        <div>
                            <strong class="text-white mr-1">Email:</strong> 
                            <a href="mailto:{{ $proposal->dosenPendampingUniversitas->email_dosen }}" class="text-white hover:text-indigo-200 hover:underline transition-colors font-medium">
                                {{ $proposal->dosenPendampingUniversitas->email_dosen }}
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="mt-6 pt-4 border-t border-indigo-400/30">
                    <small class="text-indigo-100 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Anda dapat menghubungi dosen pendamping universitas untuk konsultasi sebelum mengupload revisi akhir.
                    </small>
                </div>
            </div>
            @else
            <div class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-r-lg mb-8 shadow-sm">
                <div class="flex items-center text-amber-800">
                    <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>
                    <p class="mb-0"><strong>Peringatan:</strong> Dosen pendamping universitas belum ditetapkan. Silakan hubungi operator.</p>
                </div>
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
                        <p class="text-primary">{{ $proposal->judul }}</p>
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

            <!-- Upload File Revisi Akhir & Informasi Proposal & Upload Form -->
            <x-ui.card className="mb-8 overflow-hidden">
                <div class="bg-gradient-to-br from-navy-600 to-navy-800 text-white p-6">
                    <h4 class="font-bold text-xl m-0 flex items-center">
                        <i class="fas fa-upload mr-3 text-navy-200"></i>Upload File Revisi Akhir
                    </h4>
                </div>
                
                <div class="p-6 bg-white">
                    <form action="{{ route('mahasiswa.proposal.revisi.akhir.submit', $proposal->id_proposal) }}" method="POST" enctype="multipart/form-data" id="revisiAkhirForm">
                        @csrf
                        
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-10 text-center transition-all bg-slate-50 hover:border-navy-500 hover:bg-navy-50/50 cursor-pointer group" id="revisiAkhirUploadArea">
                            <div class="text-5xl text-navy-500 mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-file-pdf"></i>
                            </div>
                            <h5 class="text-slate-800 font-bold mb-2">Upload File Revisi Akhir Proposal</h5>
                            <p class="text-slate-500 mb-6">Drag & drop file PDF di sini atau klik untuk memilih file</p>
                            <input type="file" id="revisi_file" name="revisi_file" accept=".pdf" style="display: none;" required>
                            <button type="button" class="px-5 py-2.5 bg-white border border-navy-600 text-navy-700 rounded-lg font-medium hover:bg-navy-600 hover:text-white transition-colors shadow-sm inline-flex items-center" onclick="document.getElementById('revisi_file').click()">
                                <i class="fas fa-folder-open me-2"></i>Pilih File
                            </button>
                        </div>
                        
                        <div class="bg-slate-100 rounded-lg p-4 mt-4 hidden" id="revisiAkhirFileInfo">
                            <div class="flex justify-between items-center mb-2">
                                <div>
                                    <strong id="revisiAkhirFileName" class="text-slate-800">Nama file</strong>
                                    <br><small id="revisiAkhirFileSize" class="text-slate-500">Ukuran file</small>
                                </div>
                                <button type="button" class="text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition-colors" onclick="removeFileAkhir()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-navy-500 to-navy-700 w-0 transition-all duration-300" id="revisiAkhirProgress"></div>
                            </div>
                        </div>
                        
                        <div class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-r-lg mt-6">
                            <div class="flex items-start text-amber-800">
                                <i class="fas fa-exclamation-triangle mt-1 mr-3"></i>
                                <div>
                                    <strong class="block mb-2 font-bold">Ketentuan Upload:</strong>
                                    <ul class="list-disc pl-5 mb-0 text-amber-700">
                                        <li>Format file harus PDF</li>
                                        <li>Ukuran maksimal 5MB</li>
                                        <li>File harus berisi proposal yang sudah direvisi sesuai catatan hasil semi final</li>
                                        <li>File revisi akhir akan divalidasi oleh dosen pendamping universitas</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-8">
                            <button type="submit" class="px-8 py-3 bg-navy-600 text-white rounded-lg font-bold text-lg hover:bg-navy-700 transition-colors shadow-md inline-flex items-center" id="submitBtn">
                                <i class="fas fa-upload me-2"></i>Upload File Revisi Akhir
                            </button>
                        </div>
                    </form>
                </div>
            </x-ui.card>

            <!-- Daftar File Revisi Akhir -->
            @if($revisiAkhir->count() > 0)
            <x-ui.card className="mb-8 overflow-hidden">
                <div class="bg-gradient-to-br from-navy-600 to-navy-800 text-white p-6">
                    <h4 class="font-bold text-xl m-0 flex items-center">
                        <i class="fas fa-history mr-3 text-navy-200"></i>Daftar File Revisi Akhir yang Sudah Diupload
                    </h4>
                </div>
                
                <div class="p-6 bg-white">
                    <div class="flex flex-col gap-4">
                        @foreach($revisiAkhir as $revisi)
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-5 flex flex-col md:flex-row justify-between items-start md:items-center hover:border-navy-500 hover:shadow-md transition-all group">
                            <div class="flex items-center mb-4 md:mb-0">
                                <div class="text-4xl text-red-500 mr-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div>
                                    <h6 class="font-bold text-slate-800 mb-1 line-clamp-1" title="{{ $revisi->nama_file }}">{{ $revisi->nama_file }}</h6>
                                    <small class="text-slate-500 font-medium">
                                        Diupload: {{ \Carbon\Carbon::parse($revisi->tanggal_submit)->format('d M Y H:i') }}
                                    </small>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('mahasiswa.revisi.download', $revisi->id_revisi) }}" 
                                   class="px-4 py-2 bg-white text-navy-700 border border-navy-200 hover:bg-navy-50 hover:border-navy-300 rounded-lg text-sm font-bold transition-colors flex items-center shadow-sm" 
                                   target="_blank">
                                    <i class="fas fa-download mr-2"></i>Download
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </x-ui.card>
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
            info.classList.remove('hidden');

            // Simulate upload progress
            simulateUpload(progress);
        }
    }

    function removeFileAkhir() {
        const input = document.getElementById('revisi_file');
        const info = document.getElementById('revisiAkhirFileInfo');
        const progress = document.getElementById('revisiAkhirProgress');
        
        input.value = '';
        info.classList.add('hidden');
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
        
        let bgColor, iconClass, textColor;
        if (type === 'error') {
            bgColor = 'bg-red-100 border-red-500';
            textColor = 'text-red-800';
            iconClass = 'fas fa-exclamation-circle text-red-500';
        } else if (type === 'success') {
            bgColor = 'bg-green-100 border-green-500';
            textColor = 'text-green-800';
            iconClass = 'fas fa-check-circle text-green-500';
        } else {
            bgColor = 'bg-blue-100 border-blue-500';
            textColor = 'text-blue-800';
            iconClass = 'fas fa-info-circle text-blue-500';
        }

        toast.className = `flex items-center p-4 mb-4 text-sm rounded-lg border shadow-lg transition-opacity duration-300 ${bgColor} ${textColor}`;
        toast.style.minWidth = '300px';
        toast.innerHTML = `
            <i class="${iconClass} text-xl mr-3"></i>
            <span class="font-medium">${message}</span>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 focus:ring-gray-400 p-1.5 hover:bg-gray-200 inline-flex h-8 w-8 transition-colors ${textColor}" onclick="this.parentElement.remove()" aria-label="Close">
                <span class="sr-only">Close</span>
                <i class="fas fa-times"></i>
            </button>
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



