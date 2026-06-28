@extends('mainlayout.app')

@section('title', 'Pilih Reviewer Seleksi - Operator')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('operator.dashboard')],
        ['label' => 'Pilih Reviewer Seleksi', 'active' => true],
    ]" />

    <!-- Header Section -->
    <x-page-header 
        title="PILIH REVIEWER SELEKSI" 
        subtitle="Penugasan reviewer seleksi untuk review substantif seleksi" />
    
    <!-- Filter and Search Section -->
    <div class="row mb-4">
        <div class="col-md-3">
            <select class="form-select" id="yearSelector">
                <option value="2025" {{ $tahun == '2025' ? 'selected' : '' }}>2025</option>
                <option value="2024" {{ $filter == '2024' ? 'selected' : '' }}>2024</option>
                <option value="2023" {{ $filter == '2023' ? 'selected' : '' }}>2023</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" id="filterSelector">
                <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Filter by</option>
                <option value="RE" {{ $filter == 'RE' ? 'selected' : '' }}>RE - Riset Eksakta</option>
                <option value="RSH" {{ $filter == 'RSH' ? 'selected' : '' }}>RSH - Riset Sosial Humaniora</option>
                <option value="KC" {{ $filter == 'KC' ? 'selected' : '' }}>KC - Kewirausahaan</option>
                <option value="PM" {{ $filter == 'PM' ? 'selected' : '' }}>PM - Pengabdian Masyarakat</option>
                <option value="PI" {{ $filter == 'PI' ? 'selected' : '' }}>PI - Penerapan IPTEK</option>
                <option value="K" {{ $filter == 'K' ? 'selected' : '' }}>K - Karsa Cipta</option>
                <option value="KI" {{ $filter == 'KI' ? 'selected' : '' }}>KI - Karya Inovatif</option>
                <option value="VGK" {{ $filter == 'VGK' ? 'selected' : '' }}>VGK - Video Gagasan Konkret</option>
                <option value="AI" {{ $filter == 'AI' ? 'selected' : '' }}>AI - Artikel Ilmiah</option>
                <option value="GFT" {{ $filter == 'GFT' ? 'selected' : '' }}>GFT - Gagasan Futuristik Tertulis</option>
            </select>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control" id="searchInput" placeholder="Cari judul proposal">
            </div>
        </div>
    </div>
    
    <!-- Proposals Table -->
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Daftar Proposal yang Perlu Reviewer Seleksi
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="proposalsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Skim</th>
                            <th>Ketua</th>
                            <th>Reviewer Seleksi 1</th>
                            <th>Reviewer Seleksi 2</th>
                            <th>Submit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proposals as $index => $proposal)
                        <tr data-proposal-id="{{ $proposal->id_proposal }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $proposal->judul_proposal ?? 'Judul proposal..' }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $proposal->skim ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $proposal->mahasiswa->nama_mhs ?? 'Nama ketua..' }}</td>
                            <td>
                                <div class="input-group reviewer-input-group">
                                    <select class="form-select reviewer-select" 
                                           data-type="substantif_seleksi_1"
                                           data-proposal-id="{{ $proposal->id_proposal }}">
                                        <option value="">Pilih reviewer seleksi 1</option>
                                        @foreach($reviewers as $reviewer)
                                            <option value="{{ $reviewer->id }}" 
                                                    {{ $reviewer->id == $proposal->id_reviewer_substantif_seleksi_1 ? 'selected' : '' }}
                                                    data-reviewer-name="{{ $reviewer->name ?? $reviewer->nama_reviewer ?? '' }}">
                                                {{ $reviewer->name ?? $reviewer->nama_reviewer ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="input-group reviewer-input-group">
                                    <select class="form-select reviewer-select" 
                                           data-type="substantif_seleksi_2"
                                           data-proposal-id="{{ $proposal->id_proposal }}">
                                        <option value="">Pilih reviewer seleksi 2</option>
                                        @foreach($reviewers as $reviewer)
                                            <option value="{{ $reviewer->id }}" 
                                                    {{ $reviewer->id == $proposal->id_reviewer_substantif_seleksi_2 ? 'selected' : '' }}
                                                    data-reviewer-name="{{ $reviewer->name ?? $reviewer->nama_reviewer ?? '' }}">
                                                {{ $reviewer->name ?? $reviewer->nama_reviewer ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-secondary btn-sm submit-reviewer" 
                                        data-proposal-id="{{ $proposal->id_proposal }}"
                                        disabled>
                                    <i class="fas fa-paper-plane me-1"></i>
                                    Submit
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Tidak ada proposal yang perlu ditugaskan reviewer seleksi</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Table Proposal yang Sudah Dapat Reviewer Seleksi -->
    <div class="card card-custom mt-4">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">
                <i class="fas fa-check-circle me-2"></i>
                Proposal yang Sudah Mendapatkan Reviewer Seleksi
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="assignedProposalsTable">
                    <thead class="table-success">
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Skim</th>
                            <th>Ketua</th>
                            <th>Reviewer Seleksi 1</th>
                            <th>Reviewer Seleksi 2</th>
                            <th>Status</th>
                            <th>Tanggal Ditugaskan</th>
                        </tr>
                    </thead>
                    <tbody id="assignedProposalsTableBody">
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-spinner fa-spin fa-2x mb-3"></i>
                                    <p>Memuat data proposal yang sudah ditugaskan...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add CSRF token meta tag -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Toast notification container -->
<div class="toast-container" id="toastContainer"></div>

@endsection

@section('styles')
<style>
.reviewer-input-group {
    position: relative;
}

.reviewer-select {
    font-size: 0.85rem;
}

.reviewer-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.table td {
    position: relative;
    vertical-align: top;
}

@media (max-width: 768px) {
    .reviewer-select {
        font-size: 0.8rem;
    }
}

.reviewer-select option {
    font-size: 0.85rem;
    padding: 8px 12px;
}

.reviewer-select option:first-child {
    color: #999;
    font-style: italic;
}

.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

.toast {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    margin-bottom: 10px;
    min-width: 300px;
    border-left: 4px solid;
    animation: slideIn 0.3s ease-out;
}

.toast.success { border-left-color: #28a745; }
.toast.error { border-left-color: #dc3545; }
.toast.info { border-left-color: #17a2b8; }

.toast-header {
    padding: 12px 16px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.toast-body {
    padding: 12px 16px;
    color: #333;
}

.toast-close {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    color: #999;
}

.toast-close:hover { color: #333; }

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}

.toast.hide {
    animation: slideOut 0.3s ease-in forwards;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('yearSelector').addEventListener('change', function() {
        const year = this.value;
        const filter = document.getElementById('filterSelector').value;
        window.location.href = `{{ route('operator.pilih.reviewer.seleksi') }}?tahun=${year}&filter=${filter}`;
    });
    
    document.getElementById('filterSelector').addEventListener('change', function() {
        const year = document.getElementById('yearSelector').value;
        const filter = this.value;
        window.location.href = `{{ route('operator.pilih.reviewer.seleksi') }}?tahun=${year}&filter=${filter}`;
    });
    
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#proposalsTable tbody tr');
        rows.forEach(row => {
            if (!row.dataset.proposalId) return;
            const title = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const ketua = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            row.style.display = (title.includes(searchTerm) || ketua.includes(searchTerm)) ? '' : 'none';
        });
    });
    
    setupReviewerSelection();
    loadAssignedProposals();
    checkAllSubmitButtons();
    
    document.getElementById('yearSelector').addEventListener('change', function() {
        setTimeout(loadAssignedProposals, 100);
    });
    document.getElementById('filterSelector').addEventListener('change', function() {
        setTimeout(loadAssignedProposals, 100);
    });
});

function checkAllSubmitButtons() {
    document.querySelectorAll('.submit-reviewer').forEach(button => {
        checkReviewerAssignment(button.dataset.proposalId);
    });
}

function setupReviewerSelection() {
    document.querySelectorAll('.reviewer-select').forEach(select => {
        if (select.value) select.dataset.selectedReviewerId = select.value;
        select.addEventListener('change', function() {
            if (this.value) this.dataset.selectedReviewerId = this.value;
            else delete this.dataset.selectedReviewerId;
            checkReviewerAssignment(this.dataset.proposalId);
        });
    });
}

function checkReviewerAssignment(proposalId) {
    const row = document.querySelector(`[data-proposal-id="${proposalId}"]`);
    if (!row) return;
    const sel1 = row.querySelector('[data-type="substantif_seleksi_1"]');
    const sel2 = row.querySelector('[data-type="substantif_seleksi_2"]');
    const has1 = sel1?.value;
    const has2 = sel2?.value;
    const submitBtn = row.querySelector('.submit-reviewer');
    if (!submitBtn) return;
    if (has1 && has2 && has1 !== has2) {
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('btn-primary');
    } else {
        submitBtn.disabled = true;
        submitBtn.classList.remove('btn-primary');
        submitBtn.classList.add('btn-secondary');
    }
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('submit-reviewer')) {
        e.preventDefault();
        e.stopPropagation();
        const proposalId = e.target.dataset.proposalId;
        if (e.target.disabled) return;
        submitReviewerAssignment(proposalId);
    }
});

function submitReviewerAssignment(proposalId) {
    const row = document.querySelector(`[data-proposal-id="${proposalId}"]`);
    if (!row) {
        showToast('Proposal tidak ditemukan', 'error');
        return;
    }
    const sel1 = row.querySelector('[data-type="substantif_seleksi_1"]');
    const sel2 = row.querySelector('[data-type="substantif_seleksi_2"]');
    const id1 = sel1?.value;
    const id2 = sel2?.value;
    if (!id1 || !id2 || id1 === id2) {
        showToast('Pilih dua reviewer seleksi yang berbeda', 'error');
        return;
    }
    const submitBtn = row.querySelector('.submit-reviewer');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
    submitBtn.disabled = true;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        showToast('CSRF token tidak ditemukan', 'error');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        return;
    }
    const url = '{{ route("operator.assign.reviewer.seleksi") }}';
    const post = (data) => fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    });
    Promise.all([
        post({ proposal_id: proposalId, reviewer_id: id1, review_type: 'substantif_seleksi_1' }),
        post({ proposal_id: proposalId, reviewer_id: id2, review_type: 'substantif_seleksi_2' })
    ])
    .then(([r1, r2]) => Promise.all([r1.json(), r2.json()]))
    .then(([d1, d2]) => {
        if (d1.success && d2.success) {
            showToast('Reviewer seleksi berhasil ditugaskan!', 'success');
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '0';
            row.style.transform = 'translateX(-100%)';
            setTimeout(() => {
                row.remove();
                const tbody = document.querySelector('#proposalsTable tbody');
                if (tbody.children.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                    <p>Semua proposal telah ditugaskan reviewer seleksi</p>
                                </div>
                            </td>
                        </tr>
                    `;
                }
                loadAssignedProposals();
            }, 300);
        } else {
            showToast(d1.message || d2.message || 'Gagal menugaskan reviewer seleksi', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showToast('Terjadi kesalahan saat menugaskan reviewer seleksi', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function showToast(message, type) {
    const toastContainer = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; padding: 15px 20px; margin-bottom: 10px; border-radius: 5px; color: white; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: slideIn 0.3s ease;';
    if (type === 'success') toast.style.backgroundColor = '#28a745';
    else if (type === 'error') toast.style.backgroundColor = '#dc3545';
    else toast.style.backgroundColor = '#17a2b8';
    toast.textContent = message;
    toastContainer.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.parentNode?.removeChild(toast), 300);
    }, 5000);
}

function loadAssignedProposals() {
    const year = document.getElementById('yearSelector').value;
    const filter = document.getElementById('filterSelector').value;
    const routeUrl = '{{ route("operator.assigned.proposals.seleksi") }}';
    fetch(`${routeUrl}?tahun=${year}&filter=${filter}`)
        .then(r => r.ok ? r.json() : Promise.reject(new Error('Network error')))
        .then(data => {
            if (data.success) updateAssignedProposalsTable(data.proposals || []);
            else showToast(data.message || 'Gagal memuat data', 'error');
        })
        .catch(err => {
            console.error(err);
            document.getElementById('assignedProposalsTableBody').innerHTML = `
                <tr><td colspan="8" class="text-center py-4 text-muted">
                    <i class="fas fa-info-circle me-2"></i>Belum ada proposal yang ditugaskan reviewer seleksi
                </td></tr>
            `;
        });
}

function updateAssignedProposalsTable(proposals) {
    const tbody = document.getElementById('assignedProposalsTableBody');
    if (!proposals.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <p>Belum ada proposal yang ditugaskan reviewer seleksi</p>
                </td>
            </tr>
        `;
        return;
    }
    tbody.innerHTML = proposals.map((p, i) => {
        const judul = p.judul_proposal || p.judul || 'Judul tidak tersedia';
        const skim = p.skim || 'N/A';
        const ketua = p.mahasiswa?.nama_mhs || 'N/A';
        const rev1 = p.reviewer_substantif_seleksi_1?.name || p.reviewer_substantif_seleksi_1?.nama_reviewer || 'Belum ditugaskan';
        const rev2 = p.reviewer_substantif_seleksi_2?.name || p.reviewer_substantif_seleksi_2?.nama_reviewer || 'Belum ditugaskan';
        const status = p.status || 'N/A';
        const updatedAt = p.updated_at ? new Date(p.updated_at).toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A';
        return `<tr>
            <td>${i + 1}</td>
            <td><strong>${judul}</strong></td>
            <td><span class="badge bg-primary">${skim}</span></td>
            <td>${ketua}</td>
            <td>${rev1}</td>
            <td>${rev2}</td>
            <td><span class="badge bg-secondary">${status}</span></td>
            <td>${updatedAt}</td>
        </tr>`;
    }).join('');
}

const style = document.createElement('style');
style.textContent = `@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } } @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }`;
document.head.appendChild(style);
</script>
@endsection
