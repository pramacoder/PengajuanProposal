@extends('dosen.layout')

@section('page_title', 'Detail Validasi Akhir - Dosen Pendamping Universitas')

@section('dosen_content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-check-double me-2"></i>
                    Detail Validasi Akhir Proposal
                </h5>
                <a href="{{ route('dosen.universitas.validasi.akhir') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
            <div class="card-body">
                <!-- Success/Error Messages -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Informasi Mahasiswa -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-user me-2"></i>Informasi Mahasiswa
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nama:</strong> {{ $proposal->mahasiswa->nama_mahasiswa ?? 'N/A' }}</p>
                                        <p><strong>NIM:</strong> {{ $proposal->mahasiswa->nim ?? 'N/A' }}</p>
                                        <p><strong>Prodi:</strong> {{ $proposal->mahasiswa->prodi->nama_prodi ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Fakultas:</strong> {{ $proposal->mahasiswa->fakultas->nama_fakultas ?? 'N/A' }}</p>
                                        <p><strong>Email:</strong> {{ $proposal->mahasiswa->email_mahasiswa ?? 'N/A' }}</p>
                                        <p><strong>No. HP:</strong> {{ $proposal->mahasiswa->no_hp_mahasiswa ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Proposal -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h6 class="fw-bold mb-0">
                                    <i class="fas fa-info-circle me-2"></i>Informasi Proposal
                                </h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td width="30%"><strong>Judul:</strong></td>
                                        <td><div class="text-wrap" style="max-width: 100%; word-wrap: break-word;">{{ $proposal->judul_proposal }}</div></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Skim:</strong></td>
                                        <td><span class="badge bg-primary">{{ $proposal->skim }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge bg-warning">Perlu Validasi Akhir</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Pengajuan:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d F Y, H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-success text-white">
                                <h6 class="fw-bold mb-0">
                                    <i class="fas fa-trophy me-2"></i>Hasil Semi Final
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($proposal->hasilSemiFinal)
                                    <table class="table table-borderless mb-0">
                                        <tr>
                                            <td width="40%"><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'success' : 'danger' }}">
                                                    {{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'Lolos Tingkat Universitas' : 'Tidak Lolos Tingkat Universitas' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nilai:</strong></td>
                                            <td><strong class="text-primary">{{ number_format($proposal->hasilSemiFinal->nilai, 2) }}</strong></td>
                                        </tr>
                                        @if($proposal->hasilSemiFinal->catatan_final)
                                        <tr>
                                            <td><strong>Catatan:</strong></td>
                                            <td><small>{{ $proposal->hasilSemiFinal->catatan_final }}</small></td>
                                        </tr>
                                        @endif
                                    </table>
                                @else
                                    <p class="text-muted mb-0">Belum ada hasil semi final</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PDF Proposal Asli -->
                @if($proposal->dokumen && $proposal->dokumen->path_file)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="fw-bold mb-0">
                                        <i class="fas fa-file-pdf me-2"></i>Proposal Asli
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="btn-group" role="group">
                                            <button id="fullscreenBtnProposal" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-expand me-1"></i>Fullscreen
                                            </button>
                                            <a href="{{ route('dosen.universitas.proposal.view.pdf', $proposal->id_proposal) }}" 
                                               class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        </div>
                                    </div>
                                    <div id="pdfViewerProposal" class="pdf-loading">
                                        <div class="spinner"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- PDF Revisi Akhir -->
                @php
                    $revisiAkhir = $proposal->proposalRevisi->where('path_file', 'like', '%revisi_akhir%')->first();
                @endphp
                @if($revisiAkhir)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="fw-bold mb-0">
                                        <i class="fas fa-file-pdf me-2"></i>Revisi Akhir
                                        <small class="ms-2">({{ \Carbon\Carbon::parse($revisiAkhir->tanggal_submit)->format('d F Y, H:i') }})</small>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="btn-group" role="group">
                                            <button id="fullscreenBtnRevisi" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-expand me-1"></i>Fullscreen
                                            </button>
                                            <a href="{{ route('dosen.universitas.revisi.akhir.download', $proposal->id_proposal) }}" 
                                               class="btn btn-sm btn-outline-primary" target="_blank">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        </div>
                                    </div>
                                    <div id="pdfViewerRevisi" class="pdf-loading">
                                        <div class="spinner"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form Validasi Akhir -->
                @if($proposal->status == 'validasi_akhir_dosen_univ')
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-danger text-white">
                                <h6 class="fw-bold mb-0">
                                    <i class="fas fa-check-double me-2"></i>Form Validasi Akhir
                                </h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('dosen.universitas.validasi.akhir.submit', $proposal->id_proposal) }}" 
                                      method="POST" id="validasiForm" enctype="multipart/form-data" onsubmit="return validateForm()">
                                    @csrf
                                    
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-success btn-lg" onclick="submitValidasi('valid')">
                                                    <i class="fas fa-check me-2"></i>Validasi (Kirim ke Pimpinan PT)
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-grid gap-2">
                                                <button type="button" class="btn btn-danger btn-lg" onclick="showRejectForm()">
                                                    <i class="fas fa-times me-2"></i>Tolak (Kembalikan ke Revisi)
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Hidden input untuk action -->
                                    <input type="hidden" name="action" id="actionInput" value="">
                                    
                                    <!-- Form Penolakan (Hidden by default) -->
                                    <div id="rejectForm" class="mt-4" style="display: none;">
                                        <div class="alert alert-warning">
                                            <h6 class="alert-heading">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                Alasan Penolakan
                                            </h6>
                                            <p class="mb-2">Berikan alasan penolakan yang jelas agar mahasiswa dapat melakukan perbaikan:</p>
                                            <textarea name="catatan" class="form-control" rows="4" 
                                                      placeholder="Masukkan alasan penolakan proposal..." required></textarea>
                                        </div>
                                        
                                        <!-- Upload File Review (Opsional) -->
                                        <div class="mb-3">
                                            <label for="file_review" class="form-label">
                                                <i class="fas fa-file-pdf me-2"></i>
                                                Upload File Review <small class="text-muted">(Opsional - PDF)</small>
                                            </label>
                                            <input type="file" class="form-control" id="file_review" name="file_review" accept=".pdf">
                                            <small class="form-text text-muted">
                                                Format: PDF saja. Maksimal: 5MB. File ini akan dikirim ke mahasiswa sebagai referensi perbaikan.
                                            </small>
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="button" class="btn btn-secondary me-md-2" onclick="hideRejectForm()">
                                                <i class="fas fa-times me-2"></i>Batal
                                            </button>
                                            <button type="button" class="btn btn-danger" onclick="submitValidasi('tolak')">
                                                <i class="fas fa-times me-2"></i>Tolak Proposal
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <!-- Status Validasi -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-{{ $proposal->status_validasi == 'valid' ? 'success' : 'danger' }} text-white">
                                <h6 class="fw-bold mb-0">
                                    <i class="fas fa-{{ $proposal->status_validasi == 'valid' ? 'check-circle' : 'times-circle' }} me-2"></i>
                                    Status Validasi
                                </h6>
                            </div>
                            <div class="card-body">
                                @if($proposal->status_validasi == 'valid')
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i>
                                        <strong>Proposal sudah divalidasi!</strong> Proposal telah dikirim ke Pimpinan PT untuk penilaian final.
                                        @if($proposal->tanggal_validasi)
                                            <br><small>Tanggal validasi: {{ \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d F Y, H:i') }}</small>
                                        @endif
                                    </div>
                                @elseif($proposal->status_validasi == 'tidak_valid')
                                    <div class="alert alert-danger">
                                        <i class="fas fa-times-circle me-2"></i>
                                        <strong>Proposal ditolak!</strong>
                                        @if($proposal->catatan)
                                            <br><strong>Alasan:</strong> {{ $proposal->catatan }}
                                        @endif
                                        @if($proposal->tanggal_validasi)
                                            <br><small>Tanggal validasi: {{ \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d F Y, H:i') }}</small>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.pdf-loading {
    position: relative;
    min-height: 600px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #f8f9fa;
}

.pdf-loading .spinner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
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

@section('dosen_scripts')
<script>
    // Load PDF Proposal
    @if($proposal->dokumen && $proposal->dokumen->path_file)
    document.addEventListener('DOMContentLoaded', function() {
        const pdfViewerProposal = document.getElementById('pdfViewerProposal');
        const proposalUrl = '{{ route("dosen.universitas.proposal.view.pdf", $proposal->id_proposal) }}';
        
        // Clear loading spinner
        pdfViewerProposal.innerHTML = '';
        
        const iframeProposal = document.createElement('iframe');
        iframeProposal.src = proposalUrl;
        iframeProposal.style.width = '100%';
        iframeProposal.style.height = '600px';
        iframeProposal.style.border = 'none';
        iframeProposal.style.borderRadius = '4px';
        
        iframeProposal.onload = function() {
            console.log('PDF Proposal loaded successfully');
        };
        
        iframeProposal.onerror = function() {
            console.error('Error loading PDF Proposal');
            pdfViewerProposal.innerHTML = '<div class="alert alert-danger">Gagal memuat PDF. <a href="' + proposalUrl + '" target="_blank">Klik di sini untuk membuka di tab baru</a></div>';
        };
        
        pdfViewerProposal.appendChild(iframeProposal);
        
        // Fullscreen button
        const fullscreenBtn = document.getElementById('fullscreenBtnProposal');
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', function() {
                if (iframeProposal.requestFullscreen) {
                    iframeProposal.requestFullscreen();
                } else if (iframeProposal.webkitRequestFullscreen) {
                    iframeProposal.webkitRequestFullscreen();
                } else if (iframeProposal.mozRequestFullScreen) {
                    iframeProposal.mozRequestFullScreen();
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
        
        // Clear loading spinner
        pdfViewerRevisi.innerHTML = '';
        
        const iframeRevisi = document.createElement('iframe');
        iframeRevisi.src = revisiUrl;
        iframeRevisi.style.width = '100%';
        iframeRevisi.style.height = '600px';
        iframeRevisi.style.border = 'none';
        iframeRevisi.style.borderRadius = '4px';
        
        iframeRevisi.onload = function() {
            console.log('PDF Revisi Akhir loaded successfully');
        };
        
        iframeRevisi.onerror = function() {
            console.error('Error loading PDF Revisi Akhir');
            pdfViewerRevisi.innerHTML = '<div class="alert alert-danger">Gagal memuat PDF. <a href="' + revisiUrl + '" target="_blank">Klik di sini untuk membuka di tab baru</a></div>';
        };
        
        pdfViewerRevisi.appendChild(iframeRevisi);
        
        // Fullscreen button
        const fullscreenBtn = document.getElementById('fullscreenBtnRevisi');
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', function() {
                if (iframeRevisi.requestFullscreen) {
                    iframeRevisi.requestFullscreen();
                } else if (iframeRevisi.webkitRequestFullscreen) {
                    iframeRevisi.webkitRequestFullscreen();
                } else if (iframeRevisi.mozRequestFullScreen) {
                    iframeRevisi.mozRequestFullScreen();
                } else if (iframeRevisi.msRequestFullscreen) {
                    iframeRevisi.msRequestFullscreen();
                }
            });
        }
    });
    @endif

    // Show reject form
    function showRejectForm() {
        document.getElementById('rejectForm').style.display = 'block';
    }

    // Hide reject form
    function hideRejectForm() {
        document.getElementById('rejectForm').style.display = 'none';
        document.getElementById('actionInput').value = '';
    }

    // Submit validasi
    async function submitValidasi(action) {
        const actionText = action === 'valid' ? 'Validasi' : 'Tolak';
        const confirmed = confirm(`Apakah Anda yakin ingin ${actionText.toLowerCase()} proposal ini?`);
        
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
            
            // Show loading state
            const submitBtn = event.target;
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
            submitBtn.disabled = true;
            
            // Submit form
            const form = document.getElementById('validasiForm');
            form.submit();
        }
    }

    // Validate form before submission
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
@endsection
@endsection

