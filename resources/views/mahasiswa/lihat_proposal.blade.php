@extends('mainlayout.app')

@section('title', 'Daftar Proposal PKM - Mahasiswa')

@section('styles')
<style>
    .proposal-card-item {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 12px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .proposal-card-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.1);
        border-color: rgba(143, 11, 19, 0.3);
    }
    
    .proposal-card-header {
        background: linear-gradient(135deg, var(--surface) 0%, var(--surface-2) 100%);
        border-bottom: 1px solid var(--border);
        padding: 1.25rem 1.5rem;
        position: relative;
    }
    
    .proposal-card-body {
        padding: 1.25rem 1.5rem;
        flex: 1;
    }

    .proposal-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--border);
        font-size: 0.85rem;
    }

    .proposal-meta-row:last-child {
        border-bottom: none;
    }

    .proposal-card-actions {
        padding: 1rem 1.5rem;
        background: var(--surface-2);
        border-top: 1px solid var(--border);
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Action Floating Button & Sidebar */
    .mahasiswa-action-toggle-btn {
        position: fixed;
        right: 24px;
        bottom: 24px;
        width: 52px;
        height: 52px;
        background: var(--primary-700);
        color: white;
        border: none;
        border-radius: 50%;
        font-size: 1.25rem;
        cursor: pointer;
        box-shadow: 0 6px 18px rgba(143, 11, 19, 0.35);
        transition: all 0.25s ease;
        z-index: 999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mahasiswa-action-toggle-btn:hover {
        background: var(--primary-900);
        transform: scale(1.08);
        color: white;
    }

    .mahasiswa-action-toggle-btn.shifted {
        right: 324px;
    }

    .mahasiswa-action-sidebar {
        position: fixed;
        right: -320px;
        top: 0;
        width: 320px;
        height: 100vh;
        background: white;
        box-shadow: -6px 0 24px rgba(0, 0, 0, 0.12);
        transition: right 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 1000;
        overflow-y: auto;
    }

    .mahasiswa-action-sidebar.show {
        right: 0;
    }

    .mahasiswa-sidebar-header {
        background: var(--primary-700);
        color: white;
        padding: 1.5rem;
    }

    .mahasiswa-sidebar-content {
        padding: 1.25rem;
    }

    .mahasiswa-sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .mahasiswa-sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 0.85rem 1rem;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 8px;
        text-decoration: none;
        color: var(--text-800);
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .mahasiswa-sidebar-menu a:hover {
        background: white;
        border-color: var(--primary-500);
        color: var(--primary-700);
        transform: translateX(4px);
    }

    /* Modal Overlay */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        padding: 1rem;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal-content-custom {
        background: white;
        border-radius: 14px;
        width: 100%;
        max-width: 800px;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        border: 1px solid var(--border);
        animation: modalScaleIn 0.25s ease;
    }

    @keyframes modalScaleIn {
        from { opacity: 0; transform: scale(0.96); }
        to { opacity: 1; transform: scale(1); }
    }

    .modal-header-custom {
        background: var(--primary-700);
        color: white;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-close {
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .modal-body-custom {
        padding: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
        ['label' => 'Lihat Proposal', 'active' => true],
    ]" />

    <x-page-header 
        title="Data Proposal PKM Anda" 
        subtitle="Pantau status verifikasi, catatan hasil review, serta pembaruan revisi proposal PKM yang telah diajukan"
        badge="Portal Mahasiswa"
        icon="fa-file-lines"
    >
        <x-slot:actions>
            @if(\App\Helpers\RuangKontrolHelper::isPendaftaranActive())
                <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 fw-semibold shadow-sm">
                    <i class="fas fa-plus"></i>
                    <span>Ajukan Proposal Baru</span>
                </a>
            @endif
        </x-slot:actions>
    </x-page-header>

    <!-- Proposal Cards List -->
    <div class="row g-4" id="proposalCards">
        @if($proposals->count() > 0)
            @foreach($proposals as $proposal)
                <div class="col-lg-6 col-xl-4">
                    <div class="proposal-card-item">
                        {{-- Card Header --}}
                        <div class="proposal-card-header">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small fw-semibold">
                                    <i class="fas fa-tag me-1"></i>{{ $proposal->skim }}
                                </span>
                                
                                {{-- Status Badge --}}
                                @php
                                    $computedStatus = $proposal->status_final === 'revisi' ? 'revisi' : ($proposal->status_final === 'lolos' ? 'lolos' : ($proposal->status_final === 'tidak_lolos' ? 'tidak_lolos' : $proposal->status_validasi));
                                    $computedLabel = $proposal->status_final === 'revisi' ? 'Perlu Revisi' : ($proposal->status_final === 'lolos' ? 'Lolos' : ($proposal->status_final === 'tidak_lolos' ? 'Tidak Lolos' : ($proposal->status === 'revisi_submitted' ? 'Menunggu Validasi Revisi' : ($proposal->status_validasi == 'pending' ? 'Menunggu Validasi' : ($proposal->status_validasi == 'valid' ? 'Valid (Terverifikasi)' : ($proposal->status_validasi == 'tidak_valid' ? 'Ditolak' : ucfirst(str_replace('_', ' ', $proposal->status_validasi)))))));
                                @endphp
                                <x-status-badge :status="$computedStatus" :label="$computedLabel" />
                            </div>
                            
                            <h6 class="fw-bold text-dark mb-2 fs-6" style="line-height: 1.35; min-height: 42px;">
                                {{ $proposal->judul_proposal }}
                            </h6>
                            
                            <div class="small text-muted d-flex align-items-center justify-content-between pt-1">
                                <span><i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d M Y') }}</span>
                                
                                @php
                                    $userRole = 'Pengaju';
                                    if ($proposal->id_mahasiswa != $user->id_mahasiswa) {
                                        $userTeamMember = $proposal->semuaAnggotaTim->where('nim', $user->nim)->first();
                                        if ($userTeamMember) {
                                            $userRole = $userTeamMember->is_ketua ? 'Ketua Tim' : 'Anggota Tim';
                                        }
                                    }
                                @endphp
                                <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill">
                                    <i class="fas fa-user-circle me-1 text-primary"></i>{{ $userRole }}
                                </span>
                            </div>
                        </div>
                        
                        {{-- Card Body --}}
                        <div class="proposal-card-body">
                            <div class="proposal-meta-row">
                                <span class="text-secondary">Ketua Tim</span>
                                <span class="fw-semibold text-dark text-truncate ms-2" style="max-width: 180px;">
                                    {{ $proposal->ketuaTim ? $proposal->ketuaTim->nama_mhs : 'N/A' }}
                                </span>
                            </div>
                            <div class="proposal-meta-row">
                                <span class="text-secondary">Dosen Pendamping</span>
                                <span class="fw-semibold text-dark text-truncate ms-2" style="max-width: 180px;">
                                    {{ $proposal->dosen_pembimbing ?? '-' }}
                                </span>
                            </div>
                            <div class="proposal-meta-row">
                                <span class="text-secondary">Usulan Dana Belmawa</span>
                                <span class="fw-bold text-primary">@rupiahId($proposal->dana_diajukan_belmawa ?? 0)</span>
                            </div>
                            <div class="proposal-meta-row">
                                <span class="text-secondary">Usulan Dana Univ</span>
                                <span class="fw-bold text-success">@rupiahId($proposal->dana_diajukan_operator ?? 0)</span>
                            </div>
                            <div class="proposal-meta-row">
                                <span class="text-secondary">Anggota Tim</span>
                                <span class="fw-semibold text-dark">@formatId($proposal->semuaAnggotaTim->count()) orang</span>
                            </div>

                            @if($proposal->proposalRevisi->count() > 0)
                                @php
                                    $revisiTerbaru = $proposal->proposalRevisi->sortByDesc('tanggal_submit')->first();
                                    switch($proposal->status) {
                                        case 'revisi':
                                            $statusText = 'Menunggu Revisi';
                                            $statusClass = 'warning';
                                            break;
                                        case 'revisi_submitted':
                                            $statusText = 'Menunggu Validasi Revisi';
                                            $statusClass = 'info';
                                            break;
                                        case 'lolos':
                                        case 'tidak_lolos':
                                            $statusText = 'Selesai';
                                            $statusClass = 'success';
                                            break;
                                        default:
                                            $statusText = 'Belum Direvisi';
                                            $statusClass = 'secondary';
                                            break;
                                    }
                                @endphp
                                <div class="proposal-meta-row">
                                    <span class="text-secondary">Status Revisi</span>
                                    <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} border border-{{ $statusClass }}-subtle px-2 py-0.5 rounded-pill">
                                        {{ $statusText }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Card Actions --}}
                        <div class="proposal-card-actions">
                            <a href="{{ route('mahasiswa.proposal.show', $proposal->id_proposal) }}" class="btn btn-primary btn-sm rounded-3 px-2.5 fw-semibold d-inline-flex align-items-center gap-1">
                                <i class="fas fa-eye small"></i>
                                <span>Detail</span>
                            </a>
                            
                            @if($proposal->dokumen)
                                <a href="{{ route('mahasiswa.proposal.download', [$proposal->id_proposal, 'proposal']) }}" class="btn btn-outline-secondary btn-sm rounded-3 px-2.5 d-inline-flex align-items-center gap-1" title="Unduh File Proposal PDF">
                                    <i class="fas fa-download small"></i>
                                    <span>Unduh</span>
                                </a>
                            @endif

                            <button type="button" class="btn btn-outline-info btn-sm rounded-3 px-2.5 d-inline-flex align-items-center gap-1" onclick="showReviewModal('administrative', '{{ $proposal->id_proposal }}')">
                                <i class="fas fa-clipboard-check small"></i>
                                <span>Review</span>
                            </button>

                            @if($proposal->status === 'revisi' && $proposal->status_validasi !== 'pending')
                                <a href="{{ route('mahasiswa.proposal.revisi', $proposal->id_proposal) }}" class="btn btn-warning btn-sm rounded-3 px-2.5 fw-semibold d-inline-flex align-items-center gap-1">
                                    <i class="fas fa-edit small"></i>
                                    <span>Revisi</span>
                                </a>
                            @elseif($proposal->status === 'revisi' && $proposal->status_validasi === 'pending')
                                <button type="button" class="btn btn-light btn-sm text-secondary rounded-3 px-2.5 border" disabled title="Menunggu validasi dosen pembimbing">
                                    <i class="fas fa-clock text-warning small me-1"></i>Validasi Dosen
                                </button>
                            @elseif($proposal->status === 'revisi_akhir')
                                <a href="{{ route('mahasiswa.proposal.revisi.akhir', $proposal->id_proposal) }}" class="btn btn-warning btn-sm rounded-3 px-2.5 fw-semibold d-inline-flex align-items-center gap-1">
                                    <i class="fas fa-edit small"></i>
                                    <span>Revisi Akhir</span>
                                </a>
                            @elseif($proposal->status === 'revisi_submitted' || $proposal->status === 'validasi_akhir_dosen_univ')
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1.5 rounded-3 small d-inline-flex align-items-center gap-1">
                                    <i class="fas fa-clock small"></i> Menunggu Validasi
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="col-12">
                <div class="empty-state-box text-center py-5 border rounded-3 bg-white">
                    <div class="empty-state-icon mb-3">
                        <i class="fas fa-file-invoice fa-3x text-muted opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Belum Ada Proposal Diajukan</h5>
                    <p class="text-muted small mb-4" style="max-width: 480px; margin: 0 auto;">
                        Anda belum mengajukan proposal PKM. Pastikan telah membentuk tim dan melengkapi berkas sebelum batas waktu pendaftaran berakhir.
                    </p>
                    @if(\App\Helpers\RuangKontrolHelper::isPendaftaranActive())
                        <div>
                            <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                                <i class="fas fa-plus me-1"></i> Ajukan Proposal Pertama
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 small">
                            <i class="fas fa-lock"></i>
                            <span>Periode pendaftaran proposal PKM saat ini sedang ditutup.</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Action Sidebar (Sliding Drawer) -->
<div class="mahasiswa-action-sidebar" id="actionSidebar">
    <div class="mahasiswa-sidebar-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold fs-6"><i class="fas fa-tasks me-2"></i>Menu Aksi Penilaian</h6>
        <button class="btn btn-sm btn-link text-white text-decoration-none p-0" onclick="toggleActionSidebar()">
            <i class="fas fa-times fs-6"></i>
        </button>
    </div>
    <div class="mahasiswa-sidebar-content">
        <ul class="mahasiswa-sidebar-menu">
            <li>
                <a href="#" onclick="showReviewModal('administrative', '{{ $proposals->first()->id_proposal ?? "" }}')">
                    <i class="fas fa-clipboard-check text-primary"></i>
                    <span>Hasil Review Administratif</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="showReviewModal('substantive', '{{ $proposals->first()->id_proposal ?? "" }}')">
                    <i class="fas fa-search text-info"></i>
                    <span>Hasil Review Substantif</span>
                </a>
            </li>
            <li>
                <a href="#" onclick="showReviewModal('final', '{{ $proposals->first()->id_proposal ?? "" }}')">
                    <i class="fas fa-trophy text-warning"></i>
                    <span>Hasil Penilaian Final</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Modal Overlay for Dynamic Reviews -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-content-custom">
        <div class="modal-header-custom">
            <h5 class="fs-6 fw-bold mb-0" id="modalTitle">Hasil Review</h5>
            <button type="button" class="modal-close" onclick="closeModal()">
                <i class="fas fa-times small"></i>
            </button>
        </div>
        <div class="modal-body-custom" id="modalBody">
            <!-- Content will be loaded dynamically via AJAX -->
        </div>
    </div>
</div>

<!-- Action Toggle Button -->
<button type="button" class="mahasiswa-action-toggle-btn" id="actionToggleBtn" onclick="toggleActionSidebar()" title="Buka Menu Hasil Review">
    <i class="fas fa-bars"></i>
</button>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth < 768) {
            const actionSidebar = document.getElementById('actionSidebar');
            const toggleBtn = document.getElementById('actionToggleBtn');
            if (actionSidebar && actionSidebar.classList.contains('show')) {
                actionSidebar.classList.remove('show');
                if (toggleBtn) toggleBtn.classList.remove('shifted');
            }
        }
    });

    function toggleActionSidebar() {
        const actionSidebar = document.getElementById('actionSidebar');
        const toggleBtn = document.getElementById('actionToggleBtn');
        if (actionSidebar) {
            actionSidebar.classList.toggle('show');
            if (toggleBtn) toggleBtn.classList.toggle('shifted');
        }
    }

    function showReviewModal(type, proposalId) {
        const modalOverlay = document.getElementById('modalOverlay');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        
        modalBody.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary mb-2" role="status"></div><p class="text-muted small">Memuat data review...</p></div>';
        modalOverlay.classList.add('show');
        
        switch(type) {
            case 'administrative':
                modalTitle.textContent = 'Hasil Review Administratif';
                loadAdministrativeReview(proposalId);
                break;
            case 'substantive':
                modalTitle.textContent = 'Hasil Review Substantif';
                loadSubstantiveReview(proposalId);
                break;
            case 'final':
                modalTitle.textContent = 'Hasil Penilaian Final';
                loadFinalReview(proposalId);
                break;
        }
    }

    function closeModal() {
        const modalOverlay = document.getElementById('modalOverlay');
        if (modalOverlay) modalOverlay.classList.remove('show');
    }

    function loadAdministrativeReview(proposalId) {
        if (!proposalId) {
            document.getElementById('modalBody').innerHTML = '<p class="text-center text-muted py-4">Tidak ada data review administratif.</p>';
            return;
        }

        fetch(`/mahasiswa/proposal/${proposalId}/review/administrative`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    document.getElementById('modalBody').innerHTML = generateAdministrativeReviewHTML(data.data, data.proposal_info);
                } else {
                    document.getElementById('modalBody').innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted opacity-50 mb-3"></i>
                            <h6 class="fw-bold text-dark">Belum Ada Review Administratif</h6>
                            <p class="text-muted small">Review administratif akan muncul setelah reviewer melakukan pemeriksaan berkas.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalBody').innerHTML = `
                    <div class="alert alert-danger small">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Terjadi kesalahan saat memuat data review administratif.
                    </div>
                `;
            });
    }

    function loadSubstantiveReview(proposalId) {
        if (!proposalId) {
            document.getElementById('modalBody').innerHTML = '<p class="text-center text-muted py-4">Tidak ada data review substantif.</p>';
            return;
        }

        fetch(`/mahasiswa/proposal/${proposalId}/review/substantive`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data.length > 0) {
                    document.getElementById('modalBody').innerHTML = generateSubstantiveReviewHTML(data.data, data.proposal_info);
                } else {
                    document.getElementById('modalBody').innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-user-check fa-3x text-muted opacity-50 mb-3"></i>
                            <h6 class="fw-bold text-dark">Belum Ada Review Substantif</h6>
                            <p class="text-muted small">Review substantif akan muncul setelah proposal lolos tahapan seleksi administratif.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading substantive review:', error);
                document.getElementById('modalBody').innerHTML = `
                    <div class="alert alert-danger small">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Terjadi kesalahan saat memuat data review substantif.
                    </div>
                `;
            });
    }

    function loadFinalReview(proposalId) {
        if (!proposalId) {
            document.getElementById('modalBody').innerHTML = '<p class="text-center text-muted py-4">Tidak ada data hasil final.</p>';
            return;
        }

        fetch(`/mahasiswa/proposal/${proposalId}/review/final`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    document.getElementById('modalBody').innerHTML = generateFinalReviewHTML(
                        data.data, 
                        data.proposal_info, 
                        data.dosen_universitas,
                        data.hasil_semi_final
                    );
                } else {
                    document.getElementById('modalBody').innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-trophy fa-3x text-muted opacity-50 mb-3"></i>
                            <h6 class="fw-bold text-dark">Belum Ada Hasil Final</h6>
                            <p class="text-muted small">Hasil penilaian final akan muncul setelah seluruh rangkaian seleksi selesai.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('modalBody').innerHTML = `
                    <div class="alert alert-danger small">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Terjadi kesalahan saat memuat data hasil final.
                    </div>
                `;
            });
    }

    function generateAdministrativeReviewHTML(reviews, proposalInfo) {
        let html = `
            <div class="mb-3 p-3 bg-light rounded-3 border">
                <h6 class="text-primary fw-bold fs-6 mb-2"><i class="fas fa-file-alt me-1"></i> Informasi Proposal</h6>
                <div class="small"><strong class="text-secondary">Judul:</strong> <span class="text-dark">${proposalInfo.judul}</span></div>
                <div class="small"><strong class="text-secondary">Skim:</strong> <span class="badge bg-light text-secondary border ms-1">${proposalInfo.skim}</span></div>
            </div>
        `;
        
        if (reviews.length > 0) {
            const review = reviews[0];
            html += `
                <div class="card border rounded-3 mb-3">
                    <div class="card-header bg-primary text-white py-2 px-3 fw-semibold small">
                        <i class="fas fa-clipboard-check me-1"></i> Catatan Evaluasi Administratif
                        ${review.reviewer ? ` — ${review.reviewer.nama_reviewer}` : ''}
                    </div>
                    <div class="card-body p-3">
            `;
            
            if (review.checklist && review.checklist.length > 0) {
                html += '<div class="mb-3"><span class="small fw-bold text-danger">Kekurangan Administratif Ditemukan:</span><ul class="list-unstyled mt-2 mb-0">';
                review.checklist.forEach((item) => {
                    html += `
                        <li class="mb-2 p-2 bg-danger-subtle text-danger rounded-2 small d-flex align-items-center gap-2">
                            <i class="fas fa-times-circle"></i>
                            <div><strong>${item}</strong> — Perlu Perbaikan</div>
                        </li>
                    `;
                });
                html += '</ul></div>';
            } else {
                html += `
                    <div class="p-3 bg-success-subtle text-success rounded-3 small text-center mb-3">
                        <i class="fas fa-check-circle me-1 fs-5 d-block mb-1"></i>
                        <strong>Semua kriteria administratif telah memenuhi standar.</strong>
                    </div>
                `;
            }
            
            if (review.note_administratif) {
                html += `
                    <div class="p-3 bg-light border rounded-3 small">
                        <strong class="text-secondary d-block mb-1"><i class="fas fa-comment-dots text-primary me-1"></i>Catatan Reviewer:</strong>
                        <div class="text-dark">${review.note_administratif}</div>
                    </div>
                `;
            }
            
            html += '</div></div>';
        }
        return html;
    }

    function generateSubstantiveReviewHTML(reviews, proposalInfo) {
        let html = `
            <div class="mb-3 p-3 bg-light rounded-3 border">
                <h6 class="text-primary fw-bold fs-6 mb-2"><i class="fas fa-file-alt me-1"></i> Informasi Proposal</h6>
                <div class="small"><strong class="text-secondary">Judul:</strong> <span class="text-dark">${proposalInfo.judul}</span></div>
                <div class="small"><strong class="text-secondary">Skim:</strong> <span class="badge bg-light text-secondary border ms-1">${proposalInfo.skim}</span></div>
            </div>
        `;
        
        if (reviews && reviews.length > 0) {
            reviews.forEach((review, index) => {
                html += `
                    <div class="card border rounded-3 mb-3">
                        <div class="card-header bg-info text-white py-2 px-3 fw-semibold small">
                            <i class="fas fa-user-check me-1"></i> Reviewer Substantif ${index + 1}
                            ${review.reviewer ? ` — ${review.reviewer.nama_reviewer}` : ''}
                        </div>
                        <div class="card-body p-3">
                            <div class="p-3 bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-3 small">
                                <strong class="d-block mb-1"><i class="fas fa-edit me-1"></i>Catatan Substantif:</strong>
                                <div>${review.note_substantif || 'Tidak ada catatan khusus.'}</div>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
        return html;
    }

    function generateFinalReviewHTML(finalResult, proposalInfo, dosenUniversitas = null, hasilSemiFinal = null) {
        let statusClass = 'secondary';
        let statusText = 'BELUM DINILAI';
        let statusIcon = 'clock';
        
        if (finalResult) {
            const statusPimnas = finalResult.status_pimnas;
            const statusPendanaan = finalResult.status_pendanaan;
            
            if (statusPimnas === 'lolos' && statusPendanaan === 'lolos') {
                statusClass = 'success';
                statusText = 'LOLOS PIMNAS & PENDANAAN';
                statusIcon = 'trophy';
            } else if (statusPimnas === 'lolos' && statusPendanaan === 'tidak_lolos') {
                statusClass = 'warning';
                statusText = 'LOLOS PIMNAS (BELUM DIDANAI)';
                statusIcon = 'trophy';
            } else if (statusPimnas === 'tidak_lolos' && statusPendanaan === 'lolos') {
                statusClass = 'info';
                statusText = 'LOLOS PENDANAAN (NON-PIMNAS)';
                statusIcon = 'money-bill-wave';
            } else {
                statusClass = 'danger';
                statusText = 'TIDAK LOLOS FINAL';
                statusIcon = 'times-circle';
            }
        }
        
        return `
            <div>
                <div class="mb-3 p-3 bg-light rounded-3 border">
                    <h6 class="text-primary fw-bold fs-6 mb-2"><i class="fas fa-file-alt me-1"></i> Informasi Proposal</h6>
                    <div class="small"><strong class="text-secondary">Judul:</strong> <span class="text-dark">${proposalInfo.judul}</span></div>
                    <div class="small"><strong class="text-secondary">Skim:</strong> <span class="badge bg-light text-secondary border ms-1">${proposalInfo.skim}</span></div>
                </div>
                
                ${dosenUniversitas ? `
                <div class="card border rounded-3 mb-3">
                    <div class="card-header bg-light py-2 px-3 fw-semibold small text-dark border-bottom">
                        <i class="fas fa-user-tie text-primary me-1"></i> Dosen Pendamping Universitas
                    </div>
                    <div class="card-body p-3 small">
                        <div><strong>Nama:</strong> ${dosenUniversitas.nama_dosen}</div>
                        ${dosenUniversitas.no_hp_dosen ? `<div><strong>Kontak:</strong> ${dosenUniversitas.no_hp_dosen}</div>` : ''}
                        ${dosenUniversitas.email_dosen ? `<div><strong>Email:</strong> ${dosenUniversitas.email_dosen}</div>` : ''}
                    </div>
                </div>
                ` : ''}
                
                ${hasilSemiFinal ? `
                <div class="card border rounded-3 mb-3">
                    <div class="card-header bg-light py-2 px-3 fw-semibold small text-dark border-bottom">
                        <i class="fas fa-clipboard-check text-info me-1"></i> Hasil Tahap Semi Final
                    </div>
                    <div class="card-body p-3 small">
                        <div class="mb-1"><strong>Status:</strong> 
                            <span class="badge ${hasilSemiFinal.status_final === 'lolos_tingkat_universitas' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger'} px-2 py-0.5 rounded-pill">
                                ${hasilSemiFinal.status_final === 'lolos_tingkat_universitas' ? 'Lolos Universitas' : 'Tidak Lolos Universitas'}
                            </span>
                        </div>
                        <div><strong>Nilai Rata-rata:</strong> ${hasilSemiFinal.nilai ? parseFloat(hasilSemiFinal.nilai).toFixed(2) : '-'}</div>
                        ${hasilSemiFinal.catatan_final ? `<div class="mt-2 text-muted"><strong>Catatan:</strong> ${hasilSemiFinal.catatan_final}</div>` : ''}
                    </div>
                </div>
                ` : ''}
                
                <div class="card border rounded-3">
                    <div class="card-header bg-${statusClass} text-white py-2 px-3 fw-semibold small">
                        <i class="fas fa-${statusIcon} me-1"></i> ${statusText}
                    </div>
                    <div class="card-body p-3">
                        ${finalResult ? `
                        <div class="row g-2 mb-3">
                            <div class="col-sm-6">
                                <div class="p-2.5 rounded-3 border ${finalResult.status_pimnas === 'lolos' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-light text-secondary'} small">
                                    <div class="fw-bold">Status PIMNAS</div>
                                    <div class="fs-6 fw-bold">${finalResult.status_pimnas === 'lolos' ? 'LOLOS' : 'TIDAK LOLOS'}</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-2.5 rounded-3 border ${finalResult.status_pendanaan === 'lolos' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-light text-secondary'} small">
                                    <div class="fw-bold">Status Pendanaan</div>
                                    <div class="fs-6 fw-bold">${finalResult.status_pendanaan === 'lolos' ? 'DIDANAI' : 'TIDAK DIDANAI'}</div>
                                </div>
                            </div>
                        </div>
                        
                        ${finalResult.catatan_final ? `
                        <div class="p-3 bg-light border rounded-3 small">
                            <strong class="text-secondary d-block mb-1">Catatan Pimpinan PT:</strong>
                            <div class="text-dark">${finalResult.catatan_final}</div>
                        </div>
                        ` : ''}
                        ` : `
                        <div class="text-center py-4 text-muted small">
                            <i class="fas fa-clock fa-2x mb-2 d-block opacity-50"></i>
                            Hasil final akan ditampilkan setelah Pimpinan PT menyelesaikan penilaian akhir.
                        </div>
                        `}
                    </div>
                </div>
            </div>
        `;
    }

    document.addEventListener('click', function(event) {
        const modalOverlay = document.getElementById('modalOverlay');
        if (event.target === modalOverlay) closeModal();
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') closeModal();
    });

    function formatRupiah(value) {
        if (!value) return '0';
        const num = parseFloat(value);
        return num.toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function showToast(message, type = 'info') {
        if (window.AppUI && typeof window.AppUI.showToast === 'function') {
            window.AppUI.showToast(message, type === 'error' ? 'danger' : type);
            return;
        }
        alert(message);
    }
</script>
@endsection