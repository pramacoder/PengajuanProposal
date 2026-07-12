@extends('mainlayout.app')

@section('title', 'Pilih Reviewer - Pimpinan PT')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('pimpinan_pt.dashboard')],
        ['label' => 'Pilih Reviewer', 'active' => true],
    ]" />

    <!-- Header Section -->
    <x-page-header 
        title="PILIH REVIEWER" 
        subtitle="UNIVERSITAS UDAYANA" />
    
    <!-- Filter and Search Section -->
    <div class="row mb-4">
        <div class="col-md-3">
            <span class="badge bg-secondary">Mode Read-Only</span>
                                </div>
                            </td>
                            <td>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="input-group reviewer-input-group">
                                            <span class="badge bg-secondary">Mode Read-Only</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group reviewer-input-group">
                                            <span class="badge bg-secondary">Mode Read-Only</span>
                                        </div>
                                    </div>
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
                                    <p>Tidak ada proposal yang perlu ditugaskan reviewer</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Table Proposal yang Sudah Dapat Reviewer -->
    <div class="card card-custom mt-4">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">
                <i class="fas fa-check-circle me-2"></i>
                Proposal yang Sudah Mendapatkan Reviewer
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
                            <th>Reviewer Administratif</th>
                            <th>Reviewer Substantif</th>
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

/* Ensure proper positioning for table cells */
.table td {
    position: relative;
    vertical-align: top;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .reviewer-select {
        font-size: 0.8rem;
    }
}

/* Custom styling for select dropdowns */
.reviewer-select option {
    font-size: 0.85rem;
    padding: 8px 12px;
}

.reviewer-select option:first-child {
    color: #999;
    font-style: italic;
}

/* Toast notification styles */
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

.toast.success {
    border-left-color: #28a745;
}

.toast.error {
    border-left-color: #dc3545;
}

.toast.info {
    border-left-color: #17a2b8;
}

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

.toast-close:hover {
    color: #333;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.toast.hide {
    animation: slideOut 0.3s ease-in forwards;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Year selector change
    document.getElementById('yearSelector').addEventListener('change', function() {
        const year = this.value;
        const filter = document.getElementById('filterSelector').value;
        window.location.href = `{{ route('pimpinan_pt.pilih.reviewer') }}?tahun=${year}&filter=${filter}`;
    });
    
    // Filter selector change
    document.getElementById('filterSelector').addEventListener('change', function() {
        const year = document.getElementById('yearSelector').value;
        const filter = this.value;
        window.location.href = `{{ route('pimpinan_pt.pilih.reviewer') }}?tahun=${year}&filter=${filter}`;
    });
    
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#proposalsTable tbody tr');
        
        rows.forEach(row => {
            const title = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const ketua = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || ketua.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
    // Reviewer selection functionality
    setupReviewerSelection();
    
    // Load assigned proposals table
    loadAssignedProposals();
    
    // Check initial state of submit buttons
    checkAllSubmitButtons();
    
    // Add event listeners for filter changes to reload assigned proposals
    document.getElementById('yearSelector').addEventListener('change', function() {
        setTimeout(() => {
            loadAssignedProposals();
        }, 100);
    });
    
    document.getElementById('filterSelector').addEventListener('change', function() {
        setTimeout(() => {
            loadAssignedProposals();
        }, 100);
    });
});

function checkAllSubmitButtons() {
    console.log('Checking all submit buttons...');
    
    const submitButtons = document.querySelectorAll('.submit-reviewer');
    submitButtons.forEach(button => {
        const proposalId = button.dataset.proposalId;
        console.log('Checking submit button for proposal:', proposalId);
        checkReviewerAssignment(proposalId);
    });
}

function setupReviewerSelection() {
    console.log('Setting up reviewer selection event handlers');
    
    const reviewerSelects = document.querySelectorAll('.reviewer-select');
    console.log('Found reviewer selects:', reviewerSelects.length);
    
    reviewerSelects.forEach((select, index) => {
        console.log(`Setting up select ${index + 1}:`, select.dataset.type, 'for proposal:', select.dataset.proposalId);
        
        select.addEventListener('change', function(e) {
            console.log('Reviewer select changed:', {
                type: this.dataset.type,
                proposalId: this.dataset.proposalId,
                selectedValue: this.value
            });
            
            const selectedValue = this.value;
            const type = this.dataset.type;
            const proposalId = this.dataset.proposalId;
            const row = document.querySelector(`tr[data-proposal-id="${proposalId}"]`) || document.querySelector(`[data-proposal-id="${proposalId}"]`).closest('tr');
            
            if (type === 'substantif1' || type === 'substantif2') {
                const otherType = type === 'substantif1' ? 'substantif2' : 'substantif1';
                const otherSelect = row.querySelector(`[data-type="${otherType}"]`);
                
                if (selectedValue !== '' && selectedValue === otherSelect.value) {
                    showToast('Reviewer substantif 1 dan 2 tidak boleh sama', 'error');
                    this.value = '';
                    delete this.dataset.selectedReviewerId;
                    checkReviewerAssignment(proposalId);
                    return;
                }
                
                // Disable option in other select
                Array.from(otherSelect.options).forEach(opt => {
                    if (opt.value !== '') {
                        opt.disabled = (opt.value === selectedValue && selectedValue !== '');
                    }
                });
            }
            
            if (selectedValue !== '') {
                // Store selected reviewer ID
                this.dataset.selectedReviewerId = selectedValue;
                // Check if all reviewers are selected
                checkReviewerAssignment(proposalId);
            } else {
                console.log('Reviewer deselected:', type);
                // Clear selected reviewer ID
                delete this.dataset.selectedReviewerId;
                // Check if all reviewers are selected
                checkReviewerAssignment(proposalId);
            }
        });
        
        // Also add input event for real-time validation
        select.addEventListener('input', function(e) {
            console.log('Reviewer select input:', {
                type: this.dataset.type,
                proposalId: this.dataset.proposalId,
                value: this.value
            });
        });
    });
}

function checkReviewerAssignment(proposalId) {
    console.log('Checking reviewer assignment for proposal:', proposalId);
    
    const row = document.querySelector(`[data-proposal-id="${proposalId}"]`);
    if (!row) {
        console.error('Row not found for proposal:', proposalId);
        return;
    }
    
    const administratifSelect = row.querySelector('[data-type="administratif"]');
    const substantif1Select = row.querySelector('[data-type="substantif1"]');
    const substantif2Select = row.querySelector('[data-type="substantif2"]');
    
    const hasAdministratif = administratifSelect?.dataset.selectedReviewerId;
    const hasSubstantif1 = substantif1Select?.dataset.selectedReviewerId;
    const hasSubstantif2 = substantif2Select?.dataset.selectedReviewerId;
    
    console.log('Reviewer selection status:', {
        administratif: hasAdministratif,
        substantif1: hasSubstantif1,
        substantif2: hasSubstantif2
    });
    
    const submitBtn = row.querySelector('.submit-reviewer');
    if (!submitBtn) {
        console.error('Submit button not found for proposal:', proposalId);
        return;
    }
    
    if (hasAdministratif && hasSubstantif1 && hasSubstantif2) {
        console.log('All reviewers selected, enabling submit button');
        submitBtn.disabled = false;
        submitBtn.classList.remove('btn-secondary');
        submitBtn.classList.add('btn-primary');
    } else {
        console.log('Not all reviewers selected, disabling submit button');
        submitBtn.disabled = true;
        submitBtn.classList.remove('btn-primary');
        submitBtn.classList.add('btn-secondary');
    }
}

// Submit reviewer assignment
document.addEventListener('click', function(e) {
    console.log('Click event detected:', e.target);
    
    if (e.target.classList.contains('submit-reviewer')) {
        e.preventDefault();
        e.stopPropagation();
        
        const proposalId = e.target.dataset.proposalId;
        console.log('Submit button clicked for proposal:', proposalId);
        
        // Double check if button is enabled
        if (e.target.disabled) {
            console.log('Button is disabled, cannot submit');
            return;
        }
        
        submitReviewerAssignment(proposalId);
    }
});

// Also add event listener for button clicks specifically
document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to all submit buttons
    const submitButtons = document.querySelectorAll('.submit-reviewer');
    console.log('Found submit buttons:', submitButtons.length);
    
    submitButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const proposalId = this.dataset.proposalId;
            console.log('Submit button event listener triggered for proposal:', proposalId);
            
            if (this.disabled) {
                console.log('Button is disabled, cannot submit');
                return;
            }
            
            submitReviewerAssignment(proposalId);
        });
    });
});

function submitReviewerAssignment(proposalId) {
    console.log('Starting reviewer assignment for proposal:', proposalId);
    
    const row = document.querySelector(`[data-proposal-id="${proposalId}"]`);
    if (!row) {
        console.error('Row not found for proposal:', proposalId);
        showToast('Proposal tidak ditemukan', 'error');
        return;
    }
    
    const administratifSelect = row.querySelector('[data-type="administratif"]');
    const substantif1Select = row.querySelector('[data-type="substantif1"]');
    const substantif2Select = row.querySelector('[data-type="substantif2"]');
    
    // Validate that all reviewers are selected
    if (!administratifSelect?.dataset.selectedReviewerId || 
        !substantif1Select?.dataset.selectedReviewerId || 
        !substantif2Select?.dataset.selectedReviewerId) {
        showToast('Mohon pilih semua reviewer terlebih dahulu', 'error');
        return;
    }
    
    const data = {
        proposal_id: proposalId,
        reviewer_administratif: administratifSelect.dataset.selectedReviewerId,
        reviewer_substantif_1: substantif1Select.dataset.selectedReviewerId,
        reviewer_substantif_2: substantif2Select.dataset.selectedReviewerId
    };
    
    // Debug logging
    console.log('Submitting reviewer assignment:', data);
    
    // Show loading state
    const submitBtn = row.querySelector('.submit-reviewer');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
    submitBtn.disabled = true;
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token not found');
        showToast('CSRF token tidak ditemukan', 'error');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        return;
    }
    
    console.log('CSRF Token:', csrfToken);
    
    // Make API call
    fetch('{{ route("operator.assign.reviewer") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Server response status:', response.status);
        console.log('Server response headers:', response.headers);
        
        if (!response.ok) {
            return response.text().then(text => {
                console.error('Error response body:', text);
                throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Server response data:', data);
        if (data.success) {
            // Show success message
            showToast('Reviewer berhasil ditugaskan!', 'success');
            
            console.log('Reviewer assignment successful, updating tables...');
            
            // Remove row from proposals table with animation
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '0';
            row.style.transform = 'translateX(-100%)';
            
            setTimeout(() => {
                row.remove();
                console.log('Row removed from proposals table');
                
                // Check if proposals table is empty
                const tbody = document.querySelector('#proposalsTable tbody');
                if (tbody.children.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                    <p>Semua proposal telah ditugaskan reviewer</p>
                                </div>
                            </td>
                        </tr>
                    `;
                    console.log('Proposals table is now empty');
                }
                
                // Refresh assigned proposals table to show the newly assigned proposal
                console.log('Refreshing assigned proposals table...');
                loadAssignedProposals();
            }, 300);
        } else {
            showToast(data.message || 'Gagal menugaskan reviewer', 'error');
        }
    })
    .catch(error => {
        console.error('Error submitting reviewer assignment:', error);
        showToast('Terjadi kesalahan saat menugaskan reviewer: ' + error.message, 'error');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer');
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        padding: 15px 20px;
        margin-bottom: 10px;
        border-radius: 5px;
        color: white;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease;
    `;
    
    // Set background color based on type
    switch(type) {
        case 'success':
            toast.style.backgroundColor = '#28a745';
            break;
        case 'error':
            toast.style.backgroundColor = '#dc3545';
            break;
        case 'warning':
            toast.style.backgroundColor = '#ffc107';
            toast.style.color = '#212529';
            break;
        default:
            toast.style.backgroundColor = '#17a2b8';
    }
    
    toast.textContent = message;
    
    // Add to container
    toastContainer.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 5000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

function loadAssignedProposals() {
    console.log('Loading assigned proposals...');
    
    const year = document.getElementById('yearSelector').value;
    const filter = document.getElementById('filterSelector').value;
    
    console.log('Loading with params:', { year, filter });
    
    fetch(`{{ route('pimpinan_pt.assigned.proposals') }}?tahun=${year}&filter=${filter}`)
        .then(response => {
            console.log('Assigned proposals response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Assigned proposals data received:', data);
            if (data.success) {
                updateAssignedProposalsTable(data.proposals);
            } else {
                console.error('Failed to load assigned proposals:', data.message);
                showToast('Gagal memuat data proposal yang sudah ditugaskan: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error loading assigned proposals:', error);
            showToast('Gagal memuat data proposal yang sudah ditugaskan: ' + error.message, 'error');
        });
}

function updateAssignedProposalsTable(proposals) {
    console.log('Updating assigned proposals table with:', proposals);
    
    const tbody = document.getElementById('assignedProposalsTableBody');
    
    if (proposals.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <p>Belum ada proposal yang ditugaskan reviewer</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = proposals.map((proposal, index) => {
        console.log('Processing proposal:', proposal);
        console.log('Reviewer data:', {
            admin: proposal.reviewer_administratif,
            sub1: proposal.reviewer_substantif1, 
            sub2: proposal.reviewer_substantif2
        });
        
        const judul = proposal.judul_proposal || proposal.judul || 'Judul tidak tersedia';
        const skim = proposal.skim || 'N/A';
        const ketua = proposal.mahasiswa?.name || 'Nama tidak tersedia';
        const reviewerAdmin = proposal.reviewer_administratif?.name || 'Belum ditugaskan';
        const reviewerSub1 = proposal.reviewer_substantif1?.name || 'Belum ditugaskan';
        const reviewerSub2 = proposal.reviewer_substantif2?.name || 'Belum ditugaskan';
        const status = proposal.status || 'N/A';
        const updatedAt = proposal.updated_at || 'N/A';
        
        return `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${judul}</strong></td>
                <td><span class="badge bg-primary">${skim}</span></td>
                <td>${ketua}</td>
                <td>${reviewerAdmin}</td>
                <td>
                    ${reviewerSub1}<br>
                    ${reviewerSub2}
                </td>
                <td>
                    <span class="badge bg-${getStatusBadgeColor(status)}">
                        ${getStatusLabel(status)}
                    </span>
                </td>
                <td>${formatDate(updatedAt)}</td>
            </tr>
        `;
    }).join('');
    
    console.log('Assigned proposals table updated successfully');
}

function getStatusBadgeColor(status) {
    switch(status) {
        case 'review_administratif': return 'info';
        case 'review_substantif': return 'warning';
        case 'review_completed': return 'success';
        case 'lolos': return 'success';
        case 'tidak_lolos': return 'danger';
        default: return 'secondary';
    }
}

function getStatusLabel(status) {
    switch(status) {
        case 'review_administratif': return 'Review Administratif';
        case 'review_substantif': return 'Review Substantif';
        case 'review_completed': return 'Review Selesai';
        case 'lolos': return 'Lolos';
        case 'tidak_lolos': return 'Tidak Lolos';
        default: return status;
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>
@endsection
