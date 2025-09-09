@extends('mainlayout.app')

@section('title', 'Detail Proposal - Review Substantif')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-search me-2"></i>Review Substantif
            </h1>
            <p class="text-muted">Review substantif proposal: {{ $proposal->judul_proposal }}</p>
        </div>
        
        <div class="d-flex align-items-center">
            <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
            <span class="badge bg-success fs-6">Review Substantif</span>
        </div>
    </div>

    <div class="row">
        <!-- Informasi Proposal -->
        <div class="col-lg-4">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informasi Proposal
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>ID Proposal:</strong></td>
                            <td>{{ $proposal->id_proposal }}</td>
                        </tr>
                        <tr>
                            <td><strong>Judul:</strong></td>
                            <td>{{ $proposal->judul_proposal }}</td>
                        </tr>
                        <tr>
                            <td><strong>Skim:</strong></td>
                            <td><span class="badge bg-primary">{{ $proposal->skim }}</span></td>
                        </tr>
                        <tr>
                            <td><strong>Tahun:</strong></td>
                            <td>{{ $proposal->tahun ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                @php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch($proposal->status) {
                                        case 'draft':
                                            $statusClass = 'bg-secondary';
                                            $statusText = 'Draft';
                                            break;
                                        case 'submitted':
                                            $statusClass = 'bg-info';
                                            $statusText = 'Submitted';
                                            break;
                                        case 'validated':
                                            $statusClass = 'bg-success';
                                            $statusText = 'Validated';
                                            break;
                                        case 'review_administratif':
                                            $statusClass = 'bg-warning';
                                            $statusText = 'Review Administratif';
                                            break;
                                        case 'review_substantif':
                                            $statusClass = 'bg-info';
                                            $statusText = 'Review Substantif';
                                            break;
                                        case 'review_completed':
                                            $statusClass = 'bg-warning';
                                            $statusText = 'Review Completed';
                                            break;
                                        case 'revisi':
                                            $statusClass = 'bg-info';
                                            $statusText = 'Revisi';
                                            break;
                                        case 'finalized':
                                            $statusClass = 'bg-primary';
                                            $statusText = 'Finalized';
                                            break;
                                        default:
                                            $statusClass = 'bg-secondary';
                                            $statusText = $proposal->status;
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Informasi Mahasiswa -->
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-user-graduate me-2"></i>Informasi Mahasiswa
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>NIM:</strong></td>
                            <td>{{ $proposal->mahasiswa->nim ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $proposal->mahasiswa->email_mhs ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Program Studi:</strong></td>
                            <td>{{ $proposal->mahasiswa->prodi_mhs ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Fakultas:</strong></td>
                            <td>{{ $proposal->mahasiswa->fakultas_mhs ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Informasi Dosen Pendamping -->
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Dosen Pendamping
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $proposal->dosen->nama_dosen ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>NUPTK:</strong></td>
                            <td>{{ $proposal->dosen->nuptk ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $proposal->dosen->email_dosen ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Status Review Administratif -->
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>Status Review Administratif
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $adminCompleted = $adminReviewCompleted ?? false;
                        $existingReview = $proposal->nilaiSubstantif->where('id_reviewer', auth()->user()->id_reviewer)->first();
                    @endphp
                    
                    @if($adminCompleted)
                        <div class="text-center">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-success mb-0"><strong>Review Administratif Selesai</strong></p>
                            <small class="text-muted">Proposal siap untuk review substantif</small>
                        </div>
                    @else
                        <div class="text-center">
                            <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                            <p class="text-warning mb-0"><strong>Review Administratif Belum Selesai</strong></p>
                            <small class="text-muted">Tunggu review administratif selesai</small>
                        </div>
                    @endif

                    @if($existingReview)
                        <hr>
                        <div class="text-center">
                            <i class="fas fa-history fa-2x text-info mb-2"></i>
                            <p class="text-info mb-0"><strong>Review Substantif Sudah Ada</strong></p>
                            <small class="text-muted">Data akan diupdate</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- PDF Viewer dan Form Review Substantif -->
        <div class="col-lg-8">
            <!-- PDF Viewer -->
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-file-pdf me-2"></i>Dokumen Proposal
                    </h6>
                </div>
                <div class="card-body">
                    @if($proposal->dokumen && $proposal->dokumen->path_file)
                        <div class="pdf-container">
                            <iframe 
                                id="pdfViewer"
                                src="{{ asset('storage/' . $proposal->dokumen->path_file) }}#toolbar=1&navpanes=1&scrollbar=1"
                                style="width: 100%; height: 600px; border: 1px solid #ddd; border-radius: 8px;"
                                frameborder="0"
                                allowfullscreen>
                            </iframe>
                        </div>
                        <div class="pdf-controls">
                            <a href="{{ asset('storage/' . $proposal->dokumen->path_file) }}" 
                               class="btn btn-primary" target="_blank">
                                <i class="fas fa-download me-1"></i>Download PDF
                            </a>
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleFullscreen()">
                                <i class="fas fa-expand me-1"></i>Fullscreen
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="refreshPDFViewer()">
                                <i class="fas fa-redo me-1"></i>Refresh
                            </button>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-file-pdf fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-500">Dokumen proposal tidak tersedia</h5>
                            <p class="text-gray-400">Silakan hubungi operator untuk informasi lebih lanjut</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Form Review Substantif -->
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Form Review Substantif
                        <span class="badge bg-success ms-2">Review Substantif</span>
                    </h6>
                </div>
                <div class="card-body">
                    <form id="formReviewSubstantif" method="POST" action="{{ route('reviewer.submit.review.substantif', $proposal->id_proposal) }}">
                            @csrf
                            
                            <!-- Status Review -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Status Review</label>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2">Substantif</span>
                                        <small class="text-muted">Review substantif proposal</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Catatan Review Substantif -->
                            <div class="mb-3">
                                <label for="catatan" class="form-label fw-bold">Catatan Review Substantif <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="catatan" name="catatan" rows="8" 
                                          placeholder="Berikan penilaian detail tentang kualitas konten, metodologi, kelayakan, dampak, dan saran perbaikan..." 
                                          required>{{ $existingReview->note_substantif ?? '' }}</textarea>
                                <div class="form-text">
                                    <strong>Aspek yang dinilai:</strong><br>
                                    • Kreativitas dan orisinalitas ide (25%)<br>
                                    • Kelayakan program (25%)<br>
                                    • Dampak dan manfaat (20%)<br>
                                    • Kemampuan tim pelaksana (15%)<br>
                                    • Kesinambungan program (15%)
                                </div>
                                <div class="form-text text-danger" id="errorCatatan" style="display: none;">
                                    Catatan review substantif harus diisi minimal 50 karakter
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="fas fa-save me-1"></i>
                                    <span id="submitText">Simpan Review Substantif</span>
                                </button>
                                
                                @if($existingReview)
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Review terakhir: {{ $existingReview->updated_at->format('d/m/Y H:i') }}
                                    </small>
                                @endif
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Form validation dan submission
    document.getElementById('formReviewSubstantif').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset error messages
            document.getElementById('errorCatatan').style.display = 'none';
            
            // Get form data
            const catatan = document.getElementById('catatan').value.trim();
            
            // Validation
            if (!catatan) {
                document.getElementById('errorCatatan').style.display = 'block';
                showToast('Catatan review substantif harus diisi', 'error');
                return;
            }
            
            if (catatan.length < 50) {
                document.getElementById('errorCatatan').style.display = 'block';
                showToast('Catatan review substantif minimal 50 karakter', 'error');
                return;
            }
            
            // Disable submit button
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            submitBtn.disabled = true;
            submitText.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
            
            // Submit form
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // Log response untuk debugging
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    // Tampilkan toast success
                    if (typeof showToast === 'function') {
                        showToast(data.message, 'success');
                    } else {
                        // Fallback jika showToast tidak tersedia
                        alert('Berhasil: ' + data.message);
                    }
                    
                    // Redirect setelah delay
                    console.log('Redirecting in 2 seconds...');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    // Handle error response
                    const errorMessage = data.message || 'Terjadi kesalahan';
                    if (typeof showToast === 'function') {
                        showToast(errorMessage, 'error');
                    } else {
                        alert('Error: ' + errorMessage);
                    }
                }
            })
            .catch(error => {
                console.error('Error during submission:', error);
                
                // Handle network atau parsing errors
                let errorMessage = 'Terjadi kesalahan saat menyimpan review';
                if (error.message.includes('HTTP error')) {
                    errorMessage = 'Server error: ' + error.message;
                }
                
                if (typeof showToast === 'function') {
                    showToast(errorMessage, 'error');
                } else {
                    alert('Error: ' + errorMessage);
                }
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitText.innerHTML = 'Simpan Review Substantif';
                         });
         });

    // PDF Viewer Functions
    function toggleFullscreen() {
        const pdfViewer = document.getElementById('pdfViewer');
        const container = pdfViewer.parentElement;
        
        if (!document.fullscreenElement) {
            if (container.requestFullscreen) {
                container.requestFullscreen();
            } else if (container.webkitRequestFullscreen) {
                container.webkitRequestFullscreen();
            } else if (container.msRequestFullscreen) {
                container.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        }
    }

    function refreshPDFViewer() {
        const pdfViewer = document.getElementById('pdfViewer');
        if (pdfViewer) {
            const currentSrc = pdfViewer.src;
            pdfViewer.src = '';
            setTimeout(() => {
                pdfViewer.src = currentSrc;
            }, 100);
        }
    }

    // Responsive PDF viewer
    function resizePDFViewer() {
        const pdfViewer = document.getElementById('pdfViewer');
        if (pdfViewer) {
            const container = pdfViewer.parentElement;
            const containerWidth = container.offsetWidth;
            
            if (containerWidth < 768) {
                pdfViewer.style.height = '400px';
            } else {
                pdfViewer.style.height = '600px';
            }
        }
    }

    // Event listeners
    window.addEventListener('resize', resizePDFViewer);
    document.addEventListener('DOMContentLoaded', resizePDFViewer);
</script>
@endpush
@endsection
