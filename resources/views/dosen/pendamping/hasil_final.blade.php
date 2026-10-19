@extends('mainlayout.app')

@section('title', 'Hasil Final Proposal')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard Dosen', 'url' => route('dosen.pendamping.dashboard')],
        ['label' => 'Hasil Final', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Hasil Seleksi Final Proposal" 
        subtitle="Keputusan kelolosan akhir dan akumulasi nilai proposal mahasiswa yang telah dinilai reviewer" 
        badge="Dosen Pendamping"
        icon="fa-trophy"
    />

    <!-- Filter & Search Card -->
    <div class="ui-card p-3 mb-4 shadow-sm">
        <div class="row g-3 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0 shadow-none" id="searchInput" 
                           placeholder="Cari berdasarkan judul usulan proposal...">
                </div>
            </div>
            <div class="col-md-6 text-md-end text-muted small">
                Menampilkan <strong id="visibleCount">{{ $proposals->count() }}</strong> usulan binaan
            </div>
        </div>
    </div>

    <!-- Table of Final Proposals -->
    <div class="ui-card shadow-sm">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-award text-warning"></i>
                <h6 class="mb-0 fw-bold fs-6">Daftar Hasil Keputusan Final</h6>
            </div>
        </div>
        <div class="ui-card-body p-0">
            @if($proposals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-modern">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center py-3">No</th>
                                <th style="width: 25%;" class="py-3">Mahasiswa Pengusul</th>
                                <th style="width: 33%;" class="py-3">Judul Usulan Proposal</th>
                                <th style="width: 10%;" class="py-3">Skim</th>
                                <th style="width: 14%;" class="py-3">Status & Nilai Final</th>
                                <th style="width: 13%;" class="text-center py-3">Rincian Review</th>
                            </tr>
                        </thead>
                        <tbody id="proposalList">
                            @foreach($proposals as $index => $proposal)
                                <tr class="proposal-item" data-title="{{ strtolower($proposal->judul_proposal) }}">
                                    <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold text-dark small">{{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                                        <small class="text-muted d-block">NIM: {{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                                        <small class="text-secondary d-block">{{ $proposal->mahasiswa->prodi->nama_prodi ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark small line-clamp-2" title="{{ $proposal->judul_proposal }}">
                                            {{ $proposal->judul_proposal }}
                                        </div>
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d F Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                            {{ $proposal->skim }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="mb-1">
                                            <x-status-badge :status="$proposal->status" :label="$proposal->status_label" />
                                        </div>
                                        @if($proposal->hasilFinal)
                                            <span class="badge bg-primary fs-7">Nilai: @formatId($proposal->hasilFinal->nilai, 2)</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex flex-column gap-1">
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold text-nowrap" 
                                                    onclick="showReviewModal('administratif', {{ $proposal->id_proposal }})">
                                                <i class="fas fa-clipboard-check me-1"></i>Administratif
                                            </button>
                                            <button class="btn btn-sm btn-outline-info rounded-pill px-3 fw-semibold text-nowrap" 
                                                    onclick="showReviewModal('substantif', {{ $proposal->id_proposal }})">
                                                <i class="fas fa-user-check me-1"></i>Substantif
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state-box py-5">
                    <div class="rounded-circle p-4 bg-light text-secondary d-inline-flex mb-3">
                        <i class="fas fa-trophy fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Belum Ada Keputusan Final</h5>
                    <p class="text-secondary small mb-0" style="max-width: 480px;">
                        Proposal yang telah menyelesaikan seluruh tahapan penilaian akan ditampilkan di sini setelah Operator menetapkan hasil akhir.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold text-dark" id="reviewModalTitle">
                    <i class="fas fa-clipboard-list me-2 text-primary"></i>Hasil Review
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="reviewModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-secondary small">Memuat data review...</p>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary rounded-3 px-3 fw-semibold" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('searchInput')?.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const proposalItems = document.querySelectorAll('.proposal-item');
        let visibleCount = 0;
        
        proposalItems.forEach(item => {
            const title = item.getAttribute('data-title') || '';
            if (title.includes(searchTerm)) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        const countEl = document.getElementById('visibleCount');
        if (countEl) countEl.textContent = visibleCount;
    });

    function showReviewModal(type, proposalId) {
        const modalEl = document.getElementById('reviewModal');
        const modal = new bootstrap.Modal(modalEl);
        const title = document.getElementById('reviewModalTitle');
        const body = document.getElementById('reviewModalBody');
        
        if (type === 'administratif') {
            title.innerHTML = '<i class="fas fa-clipboard-check me-2 text-primary"></i>Hasil Review Administratif';
        } else {
            title.innerHTML = '<i class="fas fa-user-check me-2 text-info"></i>Hasil Review Substantif';
        }
        
        body.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-secondary small">Memuat data review...</p>
            </div>
        `;
        
        modal.show();
        
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
                        <div class="alert alert-danger py-2 px-3 small">
                            <i class="fas fa-exclamation-triangle me-2"></i>${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                body.innerHTML = `
                    <div class="alert alert-danger py-2 px-3 small">
                        <i class="fas fa-exclamation-triangle me-2"></i>Gagal memuat data review. Silakan coba lagi.
                    </div>
                `;
            });
    }

    function displayAdministratifReview(data, proposalInfo) {
        const body = document.getElementById('reviewModalBody');
        
        if (!data) {
            body.innerHTML = `
                <div class="empty-state-box py-4">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-2"></i>
                    <h6 class="text-dark fw-bold">Belum Ada Review Administratif</h6>
                    <p class="text-secondary small">Review administratif belum tersedia untuk proposal ini.</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="p-3 bg-light rounded-3 border mb-3">
                <h6 class="text-primary fw-bold mb-2"><i class="fas fa-file-alt me-2"></i>Informasi Proposal</h6>
                <div class="small text-secondary mb-1"><strong>Judul:</strong> ${proposalInfo.judul}</div>
                <div class="small text-secondary mb-1"><strong>Skim:</strong> <span class="badge bg-primary-subtle text-primary border border-primary-subtle">${proposalInfo.skim}</span></div>
                <div class="small text-secondary"><strong>Mahasiswa:</strong> ${proposalInfo.mahasiswa}</div>
            </div>
            
            <div class="ui-card border shadow-none mb-0">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark small"><i class="fas fa-clipboard-check text-primary me-2"></i>Catatan & Kesalahan Administratif</span>
                    <small class="text-muted">${data.reviewer} • ${data.created_at}</small>
                </div>
                <div class="ui-card-body p-3">
        `;

        if (data.checklist && data.checklist.length > 0) {
            html += '<div class="mb-3"><strong class="small text-danger d-block mb-2">Koreksi Format/Kelengkapan yang Perlu Diperbaiki:</strong><ul class="list-unstyled mb-0">';
            data.checklist.forEach((item) => {
                html += `
                    <li class="mb-2 p-2 rounded bg-danger-subtle border border-danger-subtle d-flex align-items-start gap-2 small">
                        <i class="fas fa-times-circle text-danger mt-1"></i>
                        <div><strong>${item}</strong></div>
                    </li>
                `;
            });
            html += '</ul></div>';
        } else {
            html += `
                <div class="p-3 rounded-3 bg-success-subtle text-success border border-success-subtle mb-3 small d-flex align-items-center gap-2">
                    <i class="fas fa-check-circle fa-lg"></i>
                    <span>Semua kriteria format dan kelengkapan administratif telah memenuhi standar.</span>
                </div>
            `;
        }

        html += `
                    <div class="p-3 bg-light rounded-3 border">
                        <strong class="small text-dark d-block mb-1"><i class="fas fa-comment-alt text-primary me-1"></i>Catatan Tambahan:</strong>
                        <div class="small text-secondary">${data.catatan || 'Tidak ada catatan tertulis.'}</div>
                    </div>
                </div>
            </div>
        `;

        body.innerHTML = html;
    }

    function displaySubstantifReview(data, proposalInfo) {
        const body = document.getElementById('reviewModalBody');
        
        if (!data || data.length === 0) {
            body.innerHTML = `
                <div class="empty-state-box py-4">
                    <i class="fas fa-user-check fa-3x text-muted mb-2"></i>
                    <h6 class="text-dark fw-bold">Belum Ada Review Substantif</h6>
                    <p class="text-secondary small">Review substantif belum tersedia untuk proposal ini.</p>
                </div>
            `;
            return;
        }

        let html = `
            <div class="p-3 bg-light rounded-3 border mb-3">
                <h6 class="text-primary fw-bold mb-2"><i class="fas fa-file-alt me-2"></i>Informasi Proposal</h6>
                <div class="small text-secondary mb-1"><strong>Judul:</strong> ${proposalInfo.judul}</div>
                <div class="small text-secondary mb-1"><strong>Skim:</strong> <span class="badge bg-primary-subtle text-primary border border-primary-subtle">${proposalInfo.skim}</span></div>
                <div class="small text-secondary"><strong>Mahasiswa:</strong> ${proposalInfo.mahasiswa}</div>
            </div>
        `;

        data.forEach((review, index) => {
            html += `
                <div class="ui-card border shadow-none ${index > 0 ? 'mt-3' : ''}">
                    <div class="ui-card-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark small"><i class="fas fa-user-check text-info me-2"></i>Reviewer ${index + 1} (${review.reviewer})</span>
                        <small class="text-muted">${review.created_at}</small>
                    </div>
                    <div class="ui-card-body p-3">
                        <div class="p-3 bg-light rounded-3 border">
                            <strong class="small text-dark d-block mb-1"><i class="fas fa-comment-dots text-info me-1"></i>Catatan Substantif:</strong>
                            <div class="small text-secondary">${review.catatan || 'Tidak ada catatan khusus.'}</div>
                        </div>
                    </div>
                </div>
            `;
        });

        body.innerHTML = html;
    }
</script>
@endpush
@endsection
