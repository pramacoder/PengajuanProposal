@extends('operator.layout')

@section('title', 'Detail Hasil Final - Operator')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <x-page-header 
        title="DETAIL HASIL FINAL" 
        subtitle="UNIVERSITAS UDAYANA" />
    
    <!-- Back Button -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('operator.hasil.final') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Hasil Final
            </a>
        </div>
    </div>

    <!-- Proposal Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>
                        Informasi Proposal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4 class="text-primary">{{ $proposal->judul_proposal }}</h4>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <p><strong>Skim:</strong> <span class="badge bg-primary">{{ $proposal->skim }}</span></p>
                                    <p><strong>Dana Diajukan:</strong> Rp {{ number_format($proposal->dana_diajukan, 0, ',', '.') }}</p>
                                    <p><strong>Tahun Ajaran:</strong> {{ $proposal->tahun_ajaran }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-{{ $proposal->status == 'revisi' ? 'warning' : ($proposal->status == 'lolos' ? 'success' : 'danger') }}">
                                            {{ ucfirst($proposal->status) }}
                                        </span>
                                    </p>
                                    <p><strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mahasiswa Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Informasi Mahasiswa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Ketua Tim</h6>
                            <p><strong>Nama:</strong> {{ $proposal->ketua_nama }}</p>
                            <p><strong>NIM:</strong> {{ $proposal->ketua_nim }}</p>
                            <p><strong>Prodi:</strong> {{ $proposal->ketua_prodi }}</p>
                            <p><strong>Fakultas:</strong> {{ $proposal->ketua_fakultas }}</p>
                            <p><strong>Email:</strong> {{ $proposal->ketua_email }}</p>
                            <p><strong>No. HP:</strong> {{ $proposal->ketua_no_hp }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Dosen Pembimbing</h6>
                            <p><strong>Nama:</strong> {{ $proposal->dosen_pembimbing }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- File Revisi Section -->
    @if($proposal->proposalRevisi->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-file-pdf me-2"></i>
                        File Revisi yang Dikumpulkan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($proposal->proposalRevisi as $revisi)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                <strong>{{ $revisi->nama_file }}</strong>
                                <br><small class="text-muted">
                                    Diupload: {{ \Carbon\Carbon::parse($revisi->tanggal_submit)->format('d M Y H:i') }}
                                </small>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-info me-2" 
                                        onclick="viewPDF('{{ route('operator.revisi.download', $revisi->id_revisi) }}', '{{ $revisi->nama_file }}')">
                                    <i class="fas fa-eye me-1"></i>Lihat
                                </button>
                                <a href="{{ route('operator.revisi.download', $revisi->id_revisi) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   target="_blank">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Viewer Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-pdf me-2"></i>
                        <span id="pdfViewerTitle">Pilih file untuk dilihat</span>
                    </h5>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary me-2" id="prevPage" onclick="changePage(-1)" disabled>
                            <i class="fas fa-chevron-left"></i> Sebelumnya
                        </button>
                        <span id="pageInfo" class="me-2">Halaman 1 dari 1</span>
                        <button class="btn btn-sm btn-outline-secondary me-2" id="nextPage" onclick="changePage(1)" disabled>
                            Selanjutnya <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="zoomOut()">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <span class="mx-2" id="zoomLevel">100%</span>
                        <button class="btn btn-sm btn-outline-secondary" onclick="zoomIn()">
                            <i class="fas fa-search-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="pdfViewer" class="text-center" style="min-height: 600px; border: 1px solid #dee2e6; border-radius: 0.375rem; background-color: #f8f9fa;">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="text-muted">
                                <i class="fas fa-file-pdf fa-3x mb-3"></i>
                                <p>Klik tombol "Lihat" pada file revisi untuk menampilkan PDF</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Belum ada file revisi yang dikumpulkan.</strong> Mahasiswa belum mengumpulkan file revisi proposal.
            </div>
        </div>
    </div>
    @endif

    <!-- Form Hasil Final -->
    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-gavel me-2"></i>
                        Penilaian Hasil Final
                    </h5>
                </div>
                <div class="card-body">
                    @if($proposal->hasilFinal)
                        <!-- Display existing result -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Hasil Final Sudah Ditentukan</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-{{ $proposal->hasilFinal->status_final == 'lolos' ? 'success' : 'danger' }}">
                                            {{ $proposal->hasilFinal->status_final == 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                                        </span>
                                    </p>
                                    <p><strong>Nilai:</strong> 
                                        <span class="badge bg-primary fs-6">{{ number_format($proposal->hasilFinal->nilai, 2) }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Ditentukan pada:</strong> {{ \Carbon\Carbon::parse($proposal->hasilFinal->created_at)->format('d M Y H:i') }}</p>
                                </div>
                            </div>
                            @if($proposal->hasilFinal->catatan_final)
                                <p><strong>Catatan:</strong> {{ $proposal->hasilFinal->catatan_final }}</p>
                            @endif
                        </div>
                        
                        <!-- Edit button -->
                        <button type="button" class="btn btn-warning" onclick="toggleEditForm()">
                            <i class="fas fa-edit me-2"></i>Edit Hasil Final
                        </button>
                    @endif

                    <!-- Form for input/update -->
                    <form id="hasilFinalForm" action="{{ route('operator.update.hasil.final') }}" method="POST" 
                          style="{{ $proposal->hasilFinal ? 'display: none;' : '' }}">
                        @csrf
                        <input type="hidden" name="proposal_id" value="{{ $proposal->id_proposal }}">
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required-field">Status Final</label>
                                    <select class="form-select" name="status_final" required>
                                        <option value="">Pilih status final</option>
                                        <option value="lolos" {{ $proposal->hasilFinal && $proposal->hasilFinal->status_final == 'lolos' ? 'selected' : '' }}>Lolos</option>
                                        <option value="tidak_lolos" {{ $proposal->hasilFinal && $proposal->hasilFinal->status_final == 'tidak_lolos' ? 'selected' : '' }}>Tidak Lolos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required-field">Nilai (0-100)</label>
                                    <input type="number" class="form-control" name="nilai" 
                                           value="{{ $proposal->hasilFinal ? $proposal->hasilFinal->nilai : '' }}"
                                           min="0" max="100" step="0.01" required
                                           placeholder="Masukkan nilai 0-100">
                                    <div class="form-text">Nilai untuk perangkingan proposal (0.00 - 100.00)</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Catatan Final</label>
                            <textarea class="form-control" name="catatan_final" rows="4" 
                                      placeholder="Berikan catatan untuk mahasiswa...">{{ $proposal->hasilFinal ? $proposal->hasilFinal->catatan_final : '' }}</textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>{{ $proposal->hasilFinal ? 'Update Hasil Final' : 'Simpan Hasil Final' }}
                            </button>
                            @if($proposal->hasilFinal)
                                <button type="button" class="btn btn-secondary" onclick="toggleEditForm()">
                                    <i class="fas fa-times me-2"></i>Batal
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<script>
// PDF.js configuration
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

// PDF viewer variables
let pdfDoc = null;
let pageNum = 1;
let pageRendering = false;
let pageNumPending = null;
let scale = 1.0;
const canvas = document.createElement('canvas');
const ctx = canvas.getContext('2d');

function toggleEditForm() {
    const form = document.getElementById('hasilFinalForm');
    const editBtn = document.querySelector('button[onclick="toggleEditForm()"]');
    
    if (form.style.display === 'none') {
        form.style.display = 'block';
        editBtn.innerHTML = '<i class="fas fa-times me-2"></i>Batal';
    } else {
        form.style.display = 'none';
        editBtn.innerHTML = '<i class="fas fa-edit me-2"></i>Edit Hasil Final';
    }
}

// PDF Viewer Functions
function viewPDF(pdfUrl, fileName) {
    document.getElementById('pdfViewerTitle').textContent = fileName;
    
    // Show loading
    const viewer = document.getElementById('pdfViewer');
    viewer.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100"><div class="text-center"><i class="fas fa-spinner fa-spin fa-2x mb-3"></i><br>Memuat PDF...</div></div>';
    
    // Load PDF
    pdfjsLib.getDocument(pdfUrl).promise.then(function(pdfDoc_) {
        pdfDoc = pdfDoc_;
        pageNum = 1;
        scale = 1.0;
        
        // Update page info
        document.getElementById('pageInfo').textContent = `Halaman ${pageNum} dari ${pdfDoc.numPages}`;
        document.getElementById('zoomLevel').textContent = '100%';
        
        // Enable/disable buttons
        document.getElementById('prevPage').disabled = pageNum <= 1;
        document.getElementById('nextPage').disabled = pageNum >= pdfDoc.numPages;
        
        // Render first page
        renderPage(pageNum);
    }).catch(function(error) {
        console.error('Error loading PDF:', error);
        viewer.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100"><div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><br>Gagal memuat PDF</div></div>';
    });
}

function renderPage(num) {
    pageRendering = true;
    
    pdfDoc.getPage(num).then(function(page) {
        const viewport = page.getViewport({scale: scale});
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        
        const renderContext = {
            canvasContext: ctx,
            viewport: viewport
        };
        
        const renderTask = page.render(renderContext);
        
        renderTask.promise.then(function() {
            pageRendering = false;
            if (pageNumPending !== null) {
                renderPage(pageNumPending);
                pageNumPending = null;
            }
            
            // Update viewer
            const viewer = document.getElementById('pdfViewer');
            viewer.innerHTML = '';
            viewer.appendChild(canvas);
        });
    });
    
    // Update page info
    document.getElementById('pageInfo').textContent = `Halaman ${num} dari ${pdfDoc.numPages}`;
}

function queueRenderPage(num) {
    if (pageRendering) {
        pageNumPending = num;
    } else {
        renderPage(num);
    }
}

function changePage(delta) {
    if (pdfDoc === null) return;
    
    const newPageNum = pageNum + delta;
    if (newPageNum >= 1 && newPageNum <= pdfDoc.numPages) {
        pageNum = newPageNum;
        queueRenderPage(pageNum);
        
        // Update button states
        document.getElementById('prevPage').disabled = pageNum <= 1;
        document.getElementById('nextPage').disabled = pageNum >= pdfDoc.numPages;
    }
}

function zoomIn() {
    if (pdfDoc === null) return;
    
    scale += 0.25;
    if (scale > 3.0) scale = 3.0;
    
    document.getElementById('zoomLevel').textContent = Math.round(scale * 100) + '%';
    queueRenderPage(pageNum);
}

function zoomOut() {
    if (pdfDoc === null) return;
    
    scale -= 0.25;
    if (scale < 0.5) scale = 0.5;
    
    document.getElementById('zoomLevel').textContent = Math.round(scale * 100) + '%';
    queueRenderPage(pageNum);
}

// Form submission
document.getElementById('hasilFinalForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
    
    // Make API call
    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON:', text);
                throw new Error('Invalid JSON response');
            }
        });
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            showToast(data.message || 'Hasil final berhasil disimpan', 'success');
            
            // Reload page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            
        } else {
            showToast(data.message || 'Gagal menyimpan hasil final', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat menyimpan hasil final: ' + error.message, 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

function showToast(message, type) {
    // Use the existing toast function from the layout
    if (typeof window.showToast === 'function') {
        window.showToast(message, type);
    } else {
        // Fallback alert
        alert(message);
    }
}
</script>
@endsection
