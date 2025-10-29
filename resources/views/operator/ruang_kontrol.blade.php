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
    
    <!-- Workflow Information -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h6 class="alert-heading">
                    <i class="fas fa-info-circle me-2"></i>
                    Informasi Ruang Kontrol
                </h6>
                <p class="mb-2">Sistem pengajuan proposal berjalan dalam 2 fase yang saling eksklusif:</p>
                <ul class="mb-0">
                    <li><strong>Fase 1 - Pengajuan Proposal:</strong> Mahasiswa mengajukan proposal, dosen memvalidasi</li>
                    <li><strong>Fase 2 - Perbaikan Proposal:</strong> Review proposal, perbaikan, dan penilaian akhir</li>
                </ul>
                <small class="text-muted">Hanya satu fase yang dapat aktif pada satu waktu.</small>
            </div>
        </div>
    </div>

    <!-- Control Sections -->
    <div class="row">
        <!-- Pendaftaran PKM Section -->
        <div class="col-md-6 mb-4">
            <div class="card card-custom {{ $ruangKontrol->status_pendaftaran == 'terbuka' ? 'border-success' : '' }}">
                <div class="card-header card-header-custom {{ $ruangKontrol->status_pendaftaran == 'terbuka' ? 'bg-success' : '' }}">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Fase 1: Pengajuan Proposal
                        @if($ruangKontrol->status_pendaftaran == 'terbuka')
                            <span class="badge bg-light text-success ms-2">AKTIF</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Mahasiswa dapat mengajukan proposal baru dan dosen dapat memvalidasi
                        </small>
                    </div>
                    
                    <form id="pendaftaranForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="pendaftaranMulai" 
                                           value="{{ $ruangKontrol->tanggal_pendaftaran_mulai ?? '' }}"
                                           {{ $ruangKontrol->status_pendaftaran == 'terbuka' ? 'disabled' : '' }}>
                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="pendaftaranSelesai" 
                                           value="{{ $ruangKontrol->tanggal_pendaftaran_selesai ?? '' }}"
                                           {{ $ruangKontrol->status_pendaftaran == 'terbuka' ? 'disabled' : '' }}>
                                    
                                           
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success flex-fill" id="openPendaftaran"
                                    {{ $ruangKontrol->status_perbaikan == 'terbuka' ? 'disabled' : '' }}>
                                <i class="fas fa-unlock me-2"></i>
                                BUKA FASE INI
                            </button>
                            <button type="button" class="btn btn-danger flex-fill" id="closePendaftaran">
                                <i class="fas fa-lock me-2"></i>
                                TUTUP FASE INI
                            </button>
                        </div>
                        
                        @if($ruangKontrol->status_perbaikan == 'terbuka')
                            <div class="mt-2">
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Tidak dapat membuka fase ini karena Fase 2 sedang aktif
                                </small>
                            </div>
                        @endif
                        
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
            <div class="card card-custom {{ $ruangKontrol->status_perbaikan == 'terbuka' ? 'border-warning' : '' }}">
                <div class="card-header card-header-custom {{ $ruangKontrol->status_perbaikan == 'terbuka' ? 'bg-warning' : '' }}">
                    <h5 class="mb-0">
                        <i class="fas fa-tools me-2"></i>
                        Fase 2: Perbaikan Proposal
                        @if($ruangKontrol->status_perbaikan == 'terbuka')
                            <span class="badge bg-light text-warning ms-2">AKTIF</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Review proposal, perbaikan, dan penilaian akhir
                        </small>
                    </div>
                    
                    <form id="perbaikanForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="perbaikanMulai" 
                                           value="{{ $ruangKontrol->tanggal_perbaikan_mulai ?? '' }}"
                                           {{ $ruangKontrol->status_perbaikan == 'terbuka' ? 'disabled' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="perbaikanSelesai" 
                                           value="{{ $ruangKontrol->tanggal_perbaikan_selesai ?? '' }}"
                                           {{ $ruangKontrol->status_perbaikan == 'terbuka' ? 'disabled' : '' }}>
                                    
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning flex-fill" id="openPerbaikan"
                                    {{ $ruangKontrol->status_pendaftaran == 'terbuka' ? 'disabled' : '' }}>
                                <i class="fas fa-unlock me-2"></i>
                                BUKA FASE INI
                            </button>
                            <button type="button" class="btn btn-danger flex-fill" id="closePerbaikan">
                                <i class="fas fa-lock me-2"></i>
                                TUTUP FASE INI
                            </button>
                        </div>
                        
                        @if($ruangKontrol->status_pendaftaran == 'terbuka')
                            <div class="mt-2">
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Tidak dapat membuka fase ini karena Fase 1 sedang aktif
                                </small>
                            </div>
                        @endif
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                Status: 
                                <span class="badge bg-{{ $ruangKontrol->status_perbaikan == 'terbuka' ? 'warning' : 'danger' }}" id="statusPerbaikan">
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
                                    <i class="fas fa-edit fa-2x {{ $ruangKontrol->status_pendaftaran == 'terbuka' ? 'text-success' : 'text-muted' }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">
                                        Fase 1: Pengajuan Proposal
                                        @if($ruangKontrol->status_pendaftaran == 'terbuka')
                                            <span class="badge bg-success ms-2">AKTIF</span>
                                        @endif
                                    </h6>
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
                                    <i class="fas fa-tools fa-2x {{ $ruangKontrol->status_perbaikan == 'terbuka' ? 'text-warning' : 'text-muted' }}"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">
                                        Fase 2: Perbaikan Proposal
                                        @if($ruangKontrol->status_perbaikan == 'terbuka')
                                            <span class="badge bg-warning ms-2">AKTIF</span>
                                        @endif
                                    </h6>
                                    <p class="mb-0">
                                        <span class="badge bg-{{ $ruangKontrol->status_perbaikan == 'terbuka' ? 'warning' : 'danger' }} me-2">
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
                    
                    <!-- Workflow Status Indicator -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert {{ $ruangKontrol->status_pendaftaran == 'terbuka' ? 'alert-success' : ($ruangKontrol->status_perbaikan == 'terbuka' ? 'alert-warning' : 'alert-secondary') }}">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $ruangKontrol->status_pendaftaran == 'terbuka' ? 'play-circle' : ($ruangKontrol->status_perbaikan == 'terbuka' ? 'cog' : 'pause-circle') }} me-2"></i>
                                    <div>
                                        <strong>
                                            @if($ruangKontrol->status_pendaftaran == 'terbuka')
                                                Fase 1 Sedang Berjalan
                                            @elseif($ruangKontrol->status_perbaikan == 'terbuka')
                                                Fase 2 Sedang Berjalan
                                            @else
                                                Sistem Dalam Mode Standby
                                            @endif
                                        </strong>
                                        <br>
                                        <small>
                                            @if($ruangKontrol->status_pendaftaran == 'terbuka')
                                                Mahasiswa dapat mengajukan proposal baru dan dosen dapat memvalidasi
                                            @elseif($ruangKontrol->status_perbaikan == 'terbuka')
                                                Review proposal, perbaikan, dan penilaian akhir sedang berlangsung
                                            @else
                                                Tidak ada fase yang aktif. Silakan buka salah satu fase untuk memulai proses
                                            @endif
                                        </small>
                                    </div>
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
    
    // Initialize button states
    updateButtonStates();
    
    // Event listeners for pendaftaran buttons
    document.getElementById('openPendaftaran').addEventListener('click', function() {
        // Check if perbaikan is currently open
        const perbaikanStatus = document.getElementById('statusPerbaikan').textContent.toLowerCase();
        if (perbaikanStatus === 'terbuka') {
            if (!confirm('Membuka Fase 1 akan menutup Fase 2 yang sedang aktif. Apakah Anda yakin?')) {
                return;
            }
        }
        updateRuangKontrol('pendaftaran', 'terbuka');
    });
    
    document.getElementById('closePendaftaran').addEventListener('click', function() {
        updateRuangKontrol('pendaftaran', 'tertutup');
    });
    
    // Event listeners for perbaikan buttons
    document.getElementById('openPerbaikan').addEventListener('click', function() {
        // Check if pendaftaran is currently open
        const pendaftaranStatus = document.getElementById('statusPendaftaran').textContent.toLowerCase();
        if (pendaftaranStatus === 'terbuka') {
            if (!confirm('Membuka Fase 2 akan menutup Fase 1 yang sedang aktif. Apakah Anda yakin?')) {
                return;
            }
        }
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
    // Implement mutual exclusive logic
    let statusPendaftaran, statusPerbaikan;
    
    if (type === 'pendaftaran') {
        statusPendaftaran = status;
        // If opening pendaftaran, close perbaikan
        statusPerbaikan = status === 'terbuka' ? 'tertutup' : getCurrentStatus('perbaikan');
    } else if (type === 'perbaikan') {
        statusPerbaikan = status;
        // If opening perbaikan, close pendaftaran
        statusPendaftaran = status === 'terbuka' ? 'tertutup' : getCurrentStatus('pendaftaran');
    }
    
    const data = {
        status_pendaftaran: statusPendaftaran,
        status_perbaikan: statusPerbaikan,
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
            
            // Update status info dates
            updateStatusInfo();
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
    // Update badge status for the specific type
    const statusElement = document.querySelector(`#status${type.charAt(0).toUpperCase() + type.slice(1)}`);
    if (statusElement) {
        statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        if (type === 'perbaikan') {
            statusElement.className = `badge bg-${status === 'terbuka' ? 'warning' : 'danger'}`;
        } else {
            statusElement.className = `badge bg-${status === 'terbuka' ? 'success' : 'danger'}`;
        }
    }
    
    // Update the other phase status (mutual exclusive)
    const otherType = type === 'pendaftaran' ? 'perbaikan' : 'pendaftaran';
    const otherStatus = status === 'terbuka' ? 'tertutup' : getCurrentStatus(otherType);
    const otherStatusElement = document.querySelector(`#status${otherType.charAt(0).toUpperCase() + otherType.slice(1)}`);
    if (otherStatusElement) {
        otherStatusElement.textContent = otherStatus.charAt(0).toUpperCase() + otherStatus.slice(1);
        if (otherType === 'perbaikan') {
            otherStatusElement.className = `badge bg-${otherStatus === 'terbuka' ? 'warning' : 'danger'}`;
        } else {
            otherStatusElement.className = `badge bg-${otherStatus === 'terbuka' ? 'success' : 'danger'}`;
        }
    }
    
    // Update card headers and borders
    updateCardAppearance(type, status);
    updateCardAppearance(otherType, otherStatus);
    
    // Update button states
    updateButtonStates();
}

function updateCardAppearance(type, status) {
    const card = document.querySelector(`#${type}Form`).closest('.card');
    const header = card.querySelector('.card-header');
    
    if (type === 'pendaftaran') {
        if (status === 'terbuka') {
            card.classList.add('border-success');
            header.classList.add('bg-success');
        } else {
            card.classList.remove('border-success');
            header.classList.remove('bg-success');
        }
    } else if (type === 'perbaikan') {
        if (status === 'terbuka') {
            card.classList.add('border-warning');
            header.classList.add('bg-warning');
        } else {
            card.classList.remove('border-warning');
            header.classList.remove('bg-warning');
        }
    }
}

function updateButtonStates() {
    const pendaftaranStatus = getCurrentStatus('pendaftaran');
    const perbaikanStatus = getCurrentStatus('perbaikan');
    
    // Update pendaftaran buttons
    const openPendaftaran = document.getElementById('openPendaftaran');
    const closePendaftaran = document.getElementById('closePendaftaran');
    
    if (perbaikanStatus === 'terbuka') {
        openPendaftaran.disabled = true;
        openPendaftaran.title = 'Tidak dapat membuka karena Fase 2 sedang aktif';
    } else {
        openPendaftaran.disabled = false;
        openPendaftaran.title = '';
    }
    
    // Update perbaikan buttons
    const openPerbaikan = document.getElementById('openPerbaikan');
    const closePerbaikan = document.getElementById('closePerbaikan');
    
    if (pendaftaranStatus === 'terbuka') {
        openPerbaikan.disabled = true;
        openPerbaikan.title = 'Tidak dapat membuka karena Fase 1 sedang aktif';
    } else {
        openPerbaikan.disabled = false;
        openPerbaikan.title = '';
    }
}

function updateStatusInfo() {
    // Update pendaftaran info
    const pendaftaranMulai = document.getElementById('pendaftaranMulai').value;
    const pendaftaranSelesai = document.getElementById('pendaftaranSelesai').value;
    const pendaftaranInfo = document.getElementById('statusPendaftaranInfo');
    
    if (pendaftaranMulai && pendaftaranSelesai) {
        const mulaiDate = new Date(pendaftaranMulai);
        const selesaiDate = new Date(pendaftaranSelesai);
        pendaftaranInfo.textContent = `${mulaiDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })} - ${selesaiDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}`;
    }
    
    // Update perbaikan info
    const perbaikanMulai = document.getElementById('perbaikanMulai').value;
    const perbaikanSelesai = document.getElementById('perbaikanSelesai').value;
    const perbaikanInfo = document.getElementById('statusPerbaikanInfo');
    
    if (perbaikanMulai && perbaikanSelesai) {
        const mulaiDate = new Date(perbaikanMulai);
        const selesaiDate = new Date(perbaikanSelesai);
        perbaikanInfo.textContent = `${mulaiDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })} - ${selesaiDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}`;
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
