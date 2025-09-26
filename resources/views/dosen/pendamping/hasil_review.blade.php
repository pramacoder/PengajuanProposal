@extends('dosen.layout')

@section('page_title', 'Hasil Review Proposal')

@section('dosen_content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header card-header-custom">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Hasil Review Proposal yang Telah Dinilai oleh Reviewer
                </h5>
            </div>
            <div class="card-body">
                <!-- Search Bar -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" 
                                   placeholder="Cari judul proposal...">
                        </div>
                    </div>
                </div>

                @if($proposals->count() > 0)
                    <div class="row" id="proposalList">
                        @foreach($proposals as $proposal)
                            <div class="col-12 mb-3 proposal-item" 
                                 data-title="{{ strtolower($proposal->judul_proposal) }}">
                                <div class="proposal-card">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="proposal-thumbnail">
                                                <div class="text-center">
                                                    <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                                                    <div class="btn btn-sm btn-danger">PDF</div>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <small class="text-muted">{{ Str::limit($proposal->judul_proposal, 30) }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1">
                                                        <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d F Y, H.i') }}
                                                    </p>
                                                    <p class="mb-1">
                                                        <strong>Proposal dari:</strong> {{ $proposal->mahasiswa->nama_mhs }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1">
                                                        <strong>Status:</strong> 
                                                        @switch($proposal->status)
                                                            @case('pending')
                                                                <span class="status-badge status-pending">
                                                                    <i class="fas fa-clock me-1"></i>
                                                                    Menunggu Validasi
                                                                </span>
                                                                @break
                                                            @case('valid')
                                                                <span class="status-badge status-valid">
                                                                    <i class="fas fa-check me-1"></i>
                                                                    Sudah Divalidasi
                                                                </span>
                                                                @break
                                                            @case('tidak_valid')
                                                                <span class="status-badge status-rejected">
                                                                    <i class="fas fa-times me-1"></i>
                                                                    Tidak Valid
                                                                </span>
                                                                @break
                                                            @case('submitted')
                                                                <span class="status-badge status-pending">
                                                                    <i class="fas fa-paper-plane me-1"></i>
                                                                    Sudah Dikirim ke Operator
                                                                </span>
                                                                @break
                                                            @case('review_administratif')
                                                                <span class="status-badge status-pending">
                                                                    <i class="fas fa-clipboard-check me-1"></i>
                                                                    Sedang Review Administratif
                                                                </span>
                                                                @break
                                                            @case('review_substantif')
                                                                <span class="status-badge status-pending">
                                                                    <i class="fas fa-user-check me-1"></i>
                                                                    Sedang Review Substantif
                                                                </span>
                                                                @break
                                                            @case('revisi')
                                                                <span class="status-badge status-pending">
                                                                    <i class="fas fa-edit me-1"></i>
                                                                    Perlu Revisi
                                                                </span>
                                                                @break
                                                            @case('lolos')
                                                                <span class="status-badge status-lolos">
                                                                    <i class="fas fa-trophy me-1"></i>
                                                                    Lolos Final
                                                                </span>
                                                                @break
                                                            @case('tidak_lolos')
                                                                <span class="status-badge status-tidak-lolos">
                                                                    <i class="fas fa-times-circle me-1"></i>
                                                                    Tidak Lolos Final
                                                                </span>
                                                                @break
                                                            @default
                                                                <span class="status-badge status-pending">
                                                                    <i class="fas fa-question me-1"></i>
                                                                    Status Tidak Diketahui
                                                                </span>
                                                        @endswitch
                                                    </p>
                                                    <p class="mb-1">
                                                        <strong>Skim:</strong> {{ $proposal->skim }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <div class="d-grid gap-2">
                                                <button class="btn btn-primary btn-sm" 
                                                        onclick="showReviewModal('administratif', {{ $proposal->id_proposal }})">
                                                    <i class="fas fa-clipboard-check me-2"></i>Hasil Review Administratif
                                                </button>
                                                <button class="btn btn-primary btn-sm" 
                                                        onclick="showReviewModal('substantif', {{ $proposal->id_proposal }})">
                                                    <i class="fas fa-user-check me-2"></i>Hasil Review Substantif
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada hasil review</h5>
                        <p class="text-muted">Proposal yang sudah divalidasi akan muncul di sini setelah direview oleh reviewer.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <h5 class="modal-title" id="reviewModalTitle">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Hasil Review
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="reviewModalBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat data review...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('dosen_scripts')
<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const proposalItems = document.querySelectorAll('.proposal-item');
        
        proposalItems.forEach(item => {
            const title = item.getAttribute('data-title');
            if (title.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Show Review Modal
    function showReviewModal(type, proposalId) {
        const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
        const title = document.getElementById('reviewModalTitle');
        const body = document.getElementById('reviewModalBody');
        
        // Set title berdasarkan tipe review
        if (type === 'administratif') {
            title.innerHTML = '<i class="fas fa-clipboard-check me-2"></i>Hasil Review Administratif';
        } else {
            title.innerHTML = '<i class="fas fa-user-check me-2"></i>Hasil Review Substantif';
        }
        
        // Show loading
        body.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Memuat data review...</p>
            </div>
        `;
        
        modal.show();
        
        // Fetch review data
        fetch(`/dosen/pendamping/review-data/${proposalId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (type === 'administratif') {
                        displayAdministratifReview(data.administratif, data.proposal_info);
                    } else {
                        displaySubstantifReview(data.substantif, data.proposal_info);
                    }
                } else {
                    body.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error:</strong> ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                body.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error:</strong> Gagal memuat data review. Silakan coba lagi.
                    </div>
                `;
            });
    }

    // Display administratif review
    function displayAdministratifReview(data, proposalInfo) {
        const body = document.getElementById('reviewModalBody');
        
        if (!data) {
            body.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Belum ada review administratif</h6>
                    <p class="text-muted">Review administratif akan muncul di sini setelah proposal direview oleh reviewer.</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="proposal-info mb-3">
                <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Informasi Proposal</h6>
                <p><strong>Judul:</strong> ${proposalInfo.judul}</p>
                <p><strong>Skim:</strong> ${proposalInfo.skim}</p>
                <p><strong>Mahasiswa:</strong> ${proposalInfo.mahasiswa}</p>
                <p><strong>Status:</strong> <span class="badge bg-info">${proposalInfo.status}</span></p>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-clipboard-check me-2"></i>
                    <strong>Hasil Review Administratif</strong>
                    <small class="float-end">${data.reviewer} - ${data.created_at}</small>
                </div>
                <div class="card-body">
        `;

        if (data.checklist && data.checklist.length > 0) {
            html += '<div class="mb-3"><strong>Kesalahan Administratif yang Ditemukan:</strong><ul class="list-unstyled mt-2">';
            
            // Display selected errors
            data.checklist.forEach((item) => {
                html += `
                    <li class="mb-2">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-danger me-3">
                                <i class="fas fa-times"></i>
                            </span>
                            <div>
                                <strong>${item}</strong>
                                <br>
                                <small class="text-muted">Status: Perlu Perbaikan</small>
                            </div>
                        </div>
                    </li>
                `;
            });
            html += '</ul></div>';
        } else {
            html += `
                <div class="mb-3">
                    <strong>Checklist Administratif:</strong>
                    <div class="text-center text-success mt-2">
                        <i class="fas fa-check-circle me-2"></i>
                        Semua kriteria telah memenuhi standar
                    </div>
                </div>
            `;
        }

        html += `
                    <div class="alert alert-info">
                        <i class="fas fa-edit me-2"></i>
                        <strong>Catatan:</strong><br>
                        ${data.catatan || 'Tidak ada catatan khusus.'}
                    </div>
                </div>
            </div>
        `;

        body.innerHTML = html;
    }

    // Display substantif review
    function displaySubstantifReview(data, proposalInfo) {
        const body = document.getElementById('reviewModalBody');
        
        if (!data || data.length === 0) {
            body.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-user-check fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">Belum ada review substantif</h6>
                    <p class="text-muted">Review substantif akan muncul di sini setelah proposal lolos review administratif.</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="proposal-info mb-3">
                <h6 class="text-primary"><i class="fas fa-file-alt me-2"></i>Informasi Proposal</h6>
                <p><strong>Judul:</strong> ${proposalInfo.judul}</p>
                <p><strong>Skim:</strong> ${proposalInfo.skim}</p>
                <p><strong>Mahasiswa:</strong> ${proposalInfo.mahasiswa}</p>
                <p><strong>Status:</strong> <span class="badge bg-info">${proposalInfo.status}</span></p>
            </div>
        `;

        data.forEach((review, index) => {
            html += `
                <div class="card ${index > 0 ? 'mt-3' : ''}">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-user me-2"></i>
                        <strong>Reviewer ${index + 1} - ${review.reviewer}</strong>
                        <small class="float-end">${review.created_at}</small>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-edit me-2"></i>
                            <strong>Catatan:</strong><br>
                            ${review.catatan || 'Tidak ada catatan khusus.'}
                        </div>
                    </div>
                </div>
            `;
        });

        body.innerHTML = html;
    }

    // Auto refresh setiap 60 detik untuk update status review
    setInterval(function() {
        location.reload();
    }, 60000);
</script>
@endsection
