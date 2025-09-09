@extends('operator.layout')

@section('title', 'Ruang Kontrol - Operator')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <x-page-header 
        title="RUANG KONTROL" 
        subtitle="UNIVERSITAS UDAYANA" />
    
    <!-- Year Selector -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text">2025</span>
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Control Sections -->
    <div class="row">
        <!-- Pendaftaran PKM Section -->
        <div class="col-md-6 mb-4">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Pendaftaran PKM
                    </h5>
                </div>
                <div class="card-body">
                    <form id="pendaftaranForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="pendaftaranMulai" 
                                           value="{{ $ruangKontrol->tanggal_pendaftaran_mulai ?? '' }}">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="pendaftaranSelesai" 
                                           value="{{ $ruangKontrol->tanggal_pendaftaran_selesai ?? '' }}">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success flex-fill" id="openPendaftaran">
                                <i class="fas fa-unlock me-2"></i>
                                OPEN
                            </button>
                            <button type="button" class="btn btn-danger flex-fill" id="closePendaftaran">
                                <i class="fas fa-lock me-2"></i>
                                CLOSE
                            </button>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                Status: 
                                <span class="badge bg-{{ $ruangKontrol->status_pendaftaran == 'terbuka' ? 'success' : 'danger' }}" id="statusPendaftaran">
                                    {{ ucfirst($ruangKontrol->status_pendaftaran ?? 'tertutup') }}
                                </span>
                            </small>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    <span id="statusPendaftaranInfo">
                                        @if($ruangKontrol->tanggal_pendaftaran_mulai && $ruangKontrol->tanggal_pendaftaran_selesai)
                                            {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_mulai)->format('d M Y') }} - 
                                            {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_selesai)->format('d M Y') }}
                                        @else
                                            Tanggal belum diatur
                                        @endif
                                    </span>
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Perbaikan Proposal Section -->
        <div class="col-md-6 mb-4">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>
                        Perbaikan Proposal
                    </h5>
                </div>
                <div class="card-body">
                    <form id="perbaikanForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="perbaikanMulai" 
                                           value="{{ $ruangKontrol->tanggal_perbaikan_mulai ?? '' }}">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="perbaikanSelesai" 
                                           value="{{ $ruangKontrol->tanggal_perbaikan_selesai ?? '' }}">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success flex-fill" id="openPerbaikan">
                                <i class="fas fa-unlock me-2"></i>
                                OPEN
                            </button>
                            <button type="button" class="btn btn-danger flex-fill" id="closePerbaikan">
                                <i class="fas fa-lock me-2"></i>
                                CLOSE
                            </button>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                Status: 
                                <span class="badge bg-{{ $ruangKontrol->status_perbaikan == 'terbuka' ? 'success' : 'danger' }}" id="statusPerbaikan">
                                    {{ ucfirst($ruangKontrol->status_perbaikan ?? 'tertutup') }}
                                </span>
                            </small>
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    <span id="statusPerbaikanInfo">
                                        @if($ruangKontrol->tanggal_perbaikan_mulai && $ruangKontrol->tanggal_perbaikan_selesai)
                                            {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_mulai)->format('d M Y') }} - 
                                            {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai)->format('d M Y') }}
                                        @else
                                            Tanggal belum diatur
                                        @endif
                                    </span>
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Current Status Overview -->
    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Status Sistem Saat Ini
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-edit fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Pendaftaran PKM</h6>
                                    <p class="mb-0">
                                        <span class="badge bg-{{ $ruangKontrol->status_pendaftaran == 'terbuka' ? 'success' : 'danger' }} me-2">
                                            {{ ucfirst($ruangKontrol->status_pendaftaran ?? 'tertutup') }}
                                        </span>
                                        @if($ruangKontrol->tanggal_pendaftaran_mulai && $ruangKontrol->tanggal_pendaftaran_selesai)
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_mulai)->format('d M Y') }} - 
                                                {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_selesai)->format('d M Y') }}
                                            </small>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <i class="fas fa-tools fa-2x text-warning"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Perbaikan Proposal</h6>
                                    <p class="mb-0">
                                        <span class="badge bg-{{ $ruangKontrol->status_perbaikan == 'terbuka' ? 'success' : 'danger' }} me-2">
                                            {{ ucfirst($ruangKontrol->status_perbaikan ?? 'tertutup') }}
                                        </span>
                                        @if($ruangKontrol->tanggal_perbaikan_mulai && $ruangKontrol->tanggal_perbaikan_selesai)
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_mulai)->format('d M Y') }} - 
                                                {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai)->format('d M Y') }}
                                            </small>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default dates if empty
    const today = new Date();
    const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
    
    if (!document.getElementById('pendaftaranMulai').value) {
        document.getElementById('pendaftaranMulai').value = today.toISOString().split('T')[0];
    }
    if (!document.getElementById('pendaftaranSelesai').value) {
        document.getElementById('pendaftaranSelesai').value = nextMonth.toISOString().split('T')[0];
    }
    if (!document.getElementById('perbaikanMulai').value) {
        document.getElementById('perbaikanMulai').value = today.toISOString().split('T')[0];
    }
    if (!document.getElementById('perbaikanSelesai').value) {
        document.getElementById('perbaikanSelesai').value = nextMonth.toISOString().split('T')[0];
    }
    
    // Event listeners for pendaftaran buttons
    document.getElementById('openPendaftaran').addEventListener('click', function() {
        updateRuangKontrol('pendaftaran', 'terbuka');
    });
    
    document.getElementById('closePendaftaran').addEventListener('click', function() {
        updateRuangKontrol('pendaftaran', 'tertutup');
    });
    
    // Event listeners for perbaikan buttons
    document.getElementById('openPerbaikan').addEventListener('click', function() {
        updateRuangKontrol('perbaikan', 'terbuka');
    });
    
    document.getElementById('closePerbaikan').addEventListener('click', function() {
        updateRuangKontrol('perbaikan', 'tertutup');
    });
    
    // Date validation
    document.getElementById('pendaftaranSelesai').addEventListener('change', function() {
        const mulai = document.getElementById('pendaftaranMulai').value;
        const selesai = this.value;
        
        if (mulai && selesai && new Date(selesai) <= new Date(mulai)) {
            alert('Tanggal selesai harus setelah tanggal mulai');
            this.value = '';
        }
    });
    
    document.getElementById('perbaikanSelesai').addEventListener('change', function() {
        const mulai = document.getElementById('perbaikanMulai').value;
        const selesai = this.value;
        
        if (mulai && selesai && new Date(selesai) <= new Date(mulai)) {
            alert('Tanggal selesai harus setelah tanggal mulai');
            this.value = '';
        }
    });
});

function updateRuangKontrol(type, status) {
    const data = {
        status_pendaftaran: type === 'pendaftaran' ? status : getCurrentStatus('pendaftaran'),
        status_perbaikan: type === 'perbaikan' ? status : getCurrentStatus('perbaikan'),
        tanggal_pendaftaran_mulai: document.getElementById('pendaftaranMulai').value,
        tanggal_pendaftaran_selesai: document.getElementById('pendaftaranSelesai').value,
        tanggal_perbaikan_mulai: document.getElementById('perbaikanMulai').value,
        tanggal_perbaikan_selesai: document.getElementById('perbaikanSelesai').value
    };
    
    // Debug: log data yang akan dikirim
    console.log('Data yang akan dikirim:', data);
    
    // Validate dates
    if (!data.tanggal_pendaftaran_mulai || !data.tanggal_pendaftaran_selesai || 
        !data.tanggal_perbaikan_mulai || !data.tanggal_perbaikan_selesai) {
        alert('Mohon lengkapi semua tanggal terlebih dahulu');
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    button.disabled = true;
    
    // Make API call
    fetch('{{ route("operator.update.ruang.kontrol") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            showToast(data.message, 'success');
            
            // Update UI
            updateStatusDisplay(type, status);
            
            // Reload page to show updated status
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat memperbarui pengaturan', 'error');
    })
    .finally(() => {
        // Reset button state
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function getCurrentStatus(type) {
    const statusElement = document.querySelector(`#status${type.charAt(0).toUpperCase() + type.slice(1)}`);
    return statusElement ? statusElement.textContent.toLowerCase() : 'tertutup';
}

function updateStatusDisplay(type, status) {
    // Update badge status
    const statusElement = document.querySelector(`#status${type.charAt(0).toUpperCase() + type.slice(1)}`);
    if (statusElement) {
        statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        statusElement.className = `badge bg-${status === 'terbuka' ? 'success' : 'danger'}`;
    }
    
    // Update status overview section
    const overviewStatusElement = document.querySelector(`.card-body .badge.bg-${status === 'terbuka' ? 'success' : 'danger'}`);
    if (overviewStatusElement) {
        overviewStatusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    }
    
    // Update form badge
    const formStatusElement = document.querySelector(`#${type}Form .badge`);
    if (formStatusElement) {
        formStatusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        formStatusElement.className = `badge bg-${status === 'terbuka' ? 'success' : 'danger'}`;
    }
}

function showToast(message, type) {
    // Use the existing toast function from the layout
    if (typeof showToast === 'function') {
        showToast(message, type);
    } else {
        // Fallback alert
        alert(message);
    }
}
</script>
@endsection
