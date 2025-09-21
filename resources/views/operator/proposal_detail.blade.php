@extends('operator.layout')

@section('title', 'Detail Proposal - Operator')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('operator.hasil.final') }}" class="btn btn-outline-secondary me-3">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <div>
                <h2 class="mb-0 fw-bold">Detail Proposal</h2>
                <p class="text-muted mb-0">Review lengkap proposal dan tentukan hasil final</p>
            </div>
        </div>
        <div>
            @if(!$proposal->hasilFinal)
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#hasilFinalModal">
                    <i class="fas fa-gavel me-2"></i>Tentukan Hasil Final
                </button>
            @else
                <span class="badge bg-{{ $proposal->hasilFinal->status_final == 'lolos' ? 'success' : 'danger' }} fs-6">
                    {{ $proposal->hasilFinal->status_final == 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                </span>
            @endif
        </div>
    </div>
    
    <hr class="mb-4">
    
    <!-- Proposal Information -->
    <div class="row">
        <!-- Left Column - Proposal Details -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informasi Dasar Proposal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Judul Proposal</label>
                                <p class="form-control-plaintext">{{ $proposal->judul_proposal }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Skim</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-primary">{{ $proposal->skim }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Dana Diajukan</label>
                                <p class="form-control-plaintext">{{ $proposal->dana_formatted }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tahun Ajaran</label>
                                <p class="form-control-plaintext">{{ $proposal->tahun_ajaran ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal Pengajuan</label>
                                <p class="form-control-plaintext">{{ $proposal->tanggal_pengajuan->format('d F Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status Saat Ini</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $proposal->status == 'lolos' ? 'success' : ($proposal->status == 'tidak_lolos' ? 'danger' : 'warning') }}">
                                        {{ $proposal->status_label }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Informasi Tim
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Ketua Tim</h6>
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <p class="form-control-plaintext">{{ $proposal->ketua_nama ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">NIM</label>
                                <p class="form-control-plaintext">{{ $proposal->ketua_nim ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prodi</label>
                                <p class="form-control-plaintext">{{ $proposal->ketua_prodi ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Anggota 1</h6>
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <p class="form-control-plaintext">{{ $proposal->anggota1_nama ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">NIM</label>
                                <p class="form-control-plaintext">{{ $proposal->anggota1_nim ?? 'N/A' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prodi</label>
                                <p class="form-control-plaintext">{{ $proposal->anggota1_prodi ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($proposal->anggota2_nama)
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Anggota 2</h6>
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <p class="form-control-plaintext">{{ $proposal->anggota2_nama }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">NIM</label>
                                <p class="form-control-plaintext">{{ $proposal->anggota2_nim }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prodi</label>
                                <p class="form-control-plaintext">{{ $proposal->anggota2_prodi }}</p>
                            </div>
                        </div>
                        @if($proposal->anggota3_nama)
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">Anggota 3</h6>
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <p class="form-control-plaintext">{{ $proposal->anggota3_nama }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">NIM</label>
                                <p class="form-control-plaintext">{{ $proposal->anggota3_nim }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Prodi</label>
                                <p class="form-control-plaintext">{{ $proposal->anggota3_prodi }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Review Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>Hasil Review
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Review Administratif -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary">Review Administratif</h6>
                        @if($proposal->nilaiAdministratif->count() > 0)
                            @foreach($proposal->nilaiAdministratif as $review)
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <strong>Reviewer: {{ $review->reviewer->nama_reviewer ?? 'N/A' }}</strong>
                                        <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    @if($review->checklist)
                                        <div class="mb-2">
                                            <strong>Checklist Kesalahan:</strong>
                                            <ul class="mb-0">
                                                @foreach($review->checklist as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if($review->note_administratif)
                                        <div>
                                            <strong>Catatan:</strong>
                                            <p class="mb-0">{{ $review->note_administratif }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Belum ada review administratif</p>
                        @endif
                    </div>

                    <!-- Review Substantif -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary">Review Substantif</h6>
                        @if($proposal->nilaiSubstantif->count() > 0)
                            @foreach($proposal->nilaiSubstantif as $review)
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <strong>Reviewer: {{ $review->reviewer->nama_reviewer ?? 'N/A' }}</strong>
                                        <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    @if($review->note_substantif)
                                        <div>
                                            <strong>Catatan:</strong>
                                            <p class="mb-0">{{ $review->note_substantif }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Belum ada review substantif</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Revisi Information -->
            @if($proposal->proposalRevisi->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Riwayat Revisi
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($proposal->proposalRevisi as $index => $revisi)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong>Revisi #{{ $index + 1 }}</strong>
                                <span class="badge bg-{{ $revisi->status_revisi == 'approved' ? 'success' : ($revisi->status_revisi == 'rejected' ? 'danger' : 'info') }}">
                                    {{ ucfirst(str_replace('_', ' ', $revisi->status_revisi)) }}
                                </span>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <small class="text-muted">Tanggal Submit: {{ $revisi->tanggal_submit ? $revisi->tanggal_submit->format('d/m/Y H:i') : 'N/A' }}</small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Ukuran File: {{ $revisi->ukuran_file_readable }}</small>
                                </div>
                            </div>
                            
                            @if($revisi->catatan_mahasiswa)
                                <div class="mb-2">
                                    <strong>Catatan Mahasiswa:</strong>
                                    <p class="mb-0">{{ $revisi->catatan_mahasiswa }}</p>
                                </div>
                            @endif
                            
                            @if($revisi->catatan_reviewer)
                                <div class="mb-2">
                                    <strong>Catatan Reviewer:</strong>
                                    <p class="mb-0">{{ $revisi->catatan_reviewer }}</p>
                                </div>
                            @endif
                            
                            @if($revisi->catatan_operator)
                                <div class="mb-2">
                                    <strong>Catatan Operator:</strong>
                                    <p class="mb-0">{{ $revisi->catatan_operator }}</p>
                                </div>
                            @endif
                            
                            <div class="mt-2">
                                <a href="{{ $revisi->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download me-1"></i>Download File
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Actions and Status -->
        <div class="col-lg-4">
            <!-- Status Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Status Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Validasi</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-{{ $proposal->status_validasi == 'valid' ? 'success' : ($proposal->status_validasi == 'tidak_valid' ? 'danger' : 'warning') }}">
                                {{ ucfirst(str_replace('_', ' ', $proposal->status_validasi)) }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status Revisi</label>
                        <p class="form-control-plaintext">
                            @if($proposal->proposalRevisi->count() > 0)
                                @php
                                    $revisiTerbaru = $proposal->revisiTerbaru;
                                    $statusClass = $revisiTerbaru->status_revisi == 'approved' ? 'success' : ($revisiTerbaru->status_revisi == 'rejected' ? 'danger' : 'info');
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $revisiTerbaru->status_revisi)) }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Belum Direvisi</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hasil Final</label>
                        <p class="form-control-plaintext">
                            @if($proposal->hasilFinal)
                                <span class="badge bg-{{ $proposal->hasilFinal->status_final == 'lolos' ? 'success' : 'danger' }}">
                                    {{ $proposal->hasilFinal->status_final == 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Belum Dinilai</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Dosen Pendamping -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>Dosen Pendamping
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama</label>
                        <p class="form-control-plaintext">{{ $proposal->dosen->nama_dosen ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">NIDN</label>
                        <p class="form-control-plaintext">{{ $proposal->dosen->nidn ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <p class="form-control-plaintext">{{ $proposal->dosen->email_dosen ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Mahasiswa Pengaju -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate me-2"></i>Mahasiswa Pengaju
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama</label>
                        <p class="form-control-plaintext">{{ $proposal->mahasiswa->nama_mahasiswa ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">NIM</label>
                        <p class="form-control-plaintext">{{ $proposal->mahasiswa->nim ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <p class="form-control-plaintext">{{ $proposal->mahasiswa->email_mahasiswa ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Document Download -->
            @if($proposal->dokumen)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-pdf me-2"></i>Dokumen Proposal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">File Original</label>
                        <p class="form-control-plaintext">{{ $proposal->dokumen->nama_file ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ukuran</label>
                        <p class="form-control-plaintext">{{ number_format($proposal->dokumen->ukuran_file / 1024, 2) }} KB</p>
                    </div>
                    <div>
                        <a href="{{ Storage::disk('public')->url($proposal->dokumen->path_file) }}" 
                           target="_blank" class="btn btn-primary w-100">
                            <i class="fas fa-download me-2"></i>Download PDF
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Hasil Final -->
@if(!$proposal->hasilFinal)
<div class="modal fade" id="hasilFinalModal" tabindex="-1" aria-labelledby="hasilFinalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hasilFinalModalLabel">
                    <i class="fas fa-gavel me-2"></i>Tentukan Hasil Final
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="hasilFinalForm">
                    <input type="hidden" id="proposalId" name="proposal_id" value="{{ $proposal->id_proposal }}">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Judul Proposal</label>
                            <input type="text" class="form-control" value="{{ $proposal->judul_proposal }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mahasiswa</label>
                            <input type="text" class="form-control" value="{{ $proposal->mahasiswa->nama_mahasiswa ?? 'N/A' }}" readonly>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Skim</label>
                            <input type="text" class="form-control" value="{{ $proposal->skim }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Revisi</label>
                            <input type="text" class="form-control" value="{{ $proposal->getRevisiStatus() ? ucfirst(str_replace('_', ' ', $proposal->getRevisiStatus())) : 'Belum Direvisi' }}" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required-field">Hasil Final</label>
                        <select class="form-select" id="statusFinal" name="status_final" required>
                            <option value="">Pilih hasil final</option>
                            <option value="lolos">Lolos</option>
                            <option value="tidak_lolos">Tidak Lolos</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Final</label>
                        <textarea class="form-control" id="catatanFinal" name="catatan_final" rows="4" 
                                  placeholder="Berikan catatan untuk mahasiswa..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitHasilFinal">
                    <i class="fas fa-save me-2"></i>Simpan Hasil Final
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle submit hasil final
    document.getElementById('submitHasilFinal')?.addEventListener('click', function() {
        const proposalId = document.getElementById('proposalId').value;
        const statusFinal = document.getElementById('statusFinal').value;
        const catatanFinal = document.getElementById('catatanFinal').value;
        
        // Validate
        if (!statusFinal) {
            alert('Mohon pilih hasil final terlebih dahulu');
            return;
        }
        
        const data = {
            proposal_id: proposalId,
            status_final: statusFinal,
            catatan_final: catatanFinal
        };
        
        // Show loading state
        const submitBtn = this;
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        submitBtn.disabled = true;
        
        // Make API call
        fetch('{{ route("operator.update.hasil.final") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('hasilFinalModal'));
                modal.hide();
                
                // Reload page to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
                
            } else {
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan saat menyimpan hasil final', 'error');
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});

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

