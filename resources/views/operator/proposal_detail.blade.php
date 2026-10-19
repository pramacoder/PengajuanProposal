@extends('mainlayout.app')

@section('title', 'Detail Proposal - Operator')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('operator.dashboard')],
        ['label' => 'Hasil Final', 'url' => route('operator.hasil.final')],
        ['label' => 'Detail Proposal', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Detail Proposal PKM" 
        subtitle="Evaluasi menyeluruh data usulan, anggota tim, berkas proposal, dan catatan review"
        badge="Operator Workspace"
        icon="fa-file-lines"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('operator.hasil.final') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Daftar</span>
                </a>
                @if(!$proposal->hasilFinal)
                    <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#hasilFinalModal">
                        <i class="fas fa-gavel"></i>
                        <span>Tentukan Hasil Final</span>
                    </button>
                @else
                    <span class="badge {{ $proposal->hasilFinal->status_final == 'lolos' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} px-3 py-2 rounded-pill fs-6 fw-bold">
                        <i class="fas {{ $proposal->hasilFinal->status_final == 'lolos' ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                        Hasil: {{ $proposal->hasilFinal->status_final == 'lolos' ? 'Lolos Final' : 'Tidak Lolos' }}
                    </span>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="row g-4">
        <!-- Left Column - Main Details -->
        <div class="col-lg-8">
            <!-- Basic Information Card -->
            <div class="ui-card mb-4">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Informasi Proposal</h6>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small">
                        Skim: {{ $proposal->skim }}
                    </span>
                </div>
                <div class="ui-card-body p-4">
                    <h5 class="fw-bold text-dark mb-3" style="line-height: 1.4;">
                        {{ $proposal->judul_proposal }}
                    </h5>

                    <div class="row g-3 pt-2 border-top">
                        <div class="col-sm-6 col-md-4">
                            <label class="small text-secondary fw-semibold d-block mb-1">Skim Kegiatan</label>
                            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill fw-semibold">{{ $proposal->skim }}</span>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="small text-secondary fw-semibold d-block mb-1">Usulan Dana</label>
                            <span class="fw-bold text-primary">{{ $proposal->dana_formatted }}</span>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="small text-secondary fw-semibold d-block mb-1">Tahun Akademik</label>
                            <span class="fw-semibold text-dark">{{ $proposal->tahun_ajaran ?? 'N/A' }}</span>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="small text-secondary fw-semibold d-block mb-1">Tanggal Pengajuan</label>
                            <span class="text-secondary"><i class="far fa-calendar-alt me-1"></i>{{ $proposal->tanggal_pengajuan->format('d F Y') }}</span>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="small text-secondary fw-semibold d-block mb-1">Status Verifikasi</label>
                            <x-status-badge :status="$proposal->status" :label="$proposal->status_label" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Information Card -->
            <div class="ui-card mb-4">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-users text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Susunan Tim Mahasiswa</h6>
                    </div>
                    <span class="badge bg-light text-secondary border px-2 py-0.5 small">
                        Ketua & Anggota
                    </span>
                </div>
                <div class="ui-card-body p-4">
                    <div class="row g-3">
                        <!-- Ketua Tim -->
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border bg-light h-100" style="border-left: 4px solid var(--primary-700) !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary text-white px-2 py-0.5 rounded-pill small fw-semibold">Ketua Tim</span>
                                    <i class="fas fa-crown text-warning"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">{{ $proposal->ketua_nama ?? 'N/A' }}</h6>
                                <div class="small text-secondary mb-0.5">NIM: <strong class="text-dark">{{ $proposal->ketua_nim ?? 'N/A' }}</strong></div>
                                <div class="small text-secondary">Prodi: <strong class="text-dark">{{ $proposal->ketua_prodi ?? 'N/A' }}</strong></div>
                            </div>
                        </div>

                        <!-- Anggota 1 -->
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border bg-white h-100">
                                <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill small fw-semibold mb-2 d-inline-block">Anggota 1</span>
                                <h6 class="fw-bold text-dark mb-1">{{ $proposal->anggota1_nama ?? 'N/A' }}</h6>
                                <div class="small text-secondary mb-0.5">NIM: <strong class="text-dark">{{ $proposal->anggota1_nim ?? 'N/A' }}</strong></div>
                                <div class="small text-secondary">Prodi: <strong class="text-dark">{{ $proposal->anggota1_prodi ?? 'N/A' }}</strong></div>
                            </div>
                        </div>

                        @if($proposal->anggota2_nama)
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border bg-white h-100">
                                <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill small fw-semibold mb-2 d-inline-block">Anggota 2</span>
                                <h6 class="fw-bold text-dark mb-1">{{ $proposal->anggota2_nama }}</h6>
                                <div class="small text-secondary mb-0.5">NIM: <strong class="text-dark">{{ $proposal->anggota2_nim }}</strong></div>
                                <div class="small text-secondary">Prodi: <strong class="text-dark">{{ $proposal->anggota2_prodi }}</strong></div>
                            </div>
                        </div>
                        @endif

                        @if($proposal->anggota3_nama)
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border bg-white h-100">
                                <span class="badge bg-light text-secondary border px-2 py-0.5 rounded-pill small fw-semibold mb-2 d-inline-block">Anggota 3</span>
                                <h6 class="fw-bold text-dark mb-1">{{ $proposal->anggota3_nama }}</h6>
                                <div class="small text-secondary mb-0.5">NIM: <strong class="text-dark">{{ $proposal->anggota3_nim }}</strong></div>
                                <div class="small text-secondary">Prodi: <strong class="text-dark">{{ $proposal->anggota3_prodi }}</strong></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Review Information Card -->
            <div class="ui-card mb-4">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-clipboard-check text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Hasil & Catatan Review</h6>
                    </div>
                </div>
                <div class="ui-card-body p-4">
                    <!-- Review Administratif -->
                    <div class="mb-4">
                        <h6 class="fw-bold text-secondary text-uppercase small mb-3">
                            <i class="fas fa-check-double text-primary me-1"></i> Review Administratif
                        </h6>
                        @if($proposal->nilaiAdministratif->count() > 0)
                            @foreach($proposal->nilaiAdministratif as $review)
                                <div class="p-3 rounded-3 border bg-light mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="fw-semibold text-dark">
                                            <i class="fas fa-user-tie text-muted me-1"></i>{{ $review->reviewer->nama_reviewer ?? 'Reviewer' }}
                                        </div>
                                        <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    @if($review->checklist)
                                        <div class="mb-2">
                                            <span class="small fw-bold text-danger">Kekurangan Administratif:</span>
                                            <ul class="mb-0 small text-danger ps-3 mt-1">
                                                @foreach($review->checklist as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    @if($review->note_administratif)
                                        <div class="mt-2 pt-2 border-top small">
                                            <strong class="text-secondary">Catatan:</strong>
                                            <p class="mb-0 text-dark">{{ $review->note_administratif }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted small fst-italic mb-0">Belum ada evaluasi administratif.</p>
                        @endif
                    </div>

                    <!-- Review Substantif -->
                    <div>
                        <h6 class="fw-bold text-secondary text-uppercase small mb-3">
                            <i class="fas fa-search-plus text-info me-1"></i> Review Substantif
                        </h6>
                        @if($proposal->nilaiSubstantif->count() > 0)
                            @foreach($proposal->nilaiSubstantif as $review)
                                <div class="p-3 rounded-3 border bg-light mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="fw-semibold text-dark">
                                            <i class="fas fa-user-tie text-muted me-1"></i>{{ $review->reviewer->nama_reviewer ?? 'Reviewer' }}
                                        </div>
                                        <small class="text-muted">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    @if($review->note_substantif)
                                        <div class="small">
                                            <strong class="text-secondary">Catatan:</strong>
                                            <p class="mb-0 text-dark">{{ $review->note_substantif }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted small fst-italic mb-0">Belum ada evaluasi substantif.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Revisi Information Card -->
            @if($proposal->proposalRevisi->count() > 0)
            <div class="ui-card mb-4">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-history text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Riwayat Berkas Revisi</h6>
                    </div>
                    <span class="badge bg-light text-secondary border px-2 py-0.5 small">
                        Total: {{ $proposal->proposalRevisi->count() }} revisi
                    </span>
                </div>
                <div class="ui-card-body p-4">
                    @foreach($proposal->proposalRevisi as $index => $revisi)
                        <div class="p-3 rounded-3 border bg-light mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-dark">Revisi #{{ $index + 1 }}</span>
                                <span class="badge {{ $revisi->status_revisi == 'approved' ? 'bg-success-subtle text-success border border-success-subtle' : ($revisi->status_revisi == 'rejected' ? 'bg-danger-subtle text-danger' : 'bg-info-subtle text-info') }} px-2 py-0.5 rounded-pill small">
                                    {{ ucfirst(str_replace('_', ' ', $revisi->status_revisi)) }}
                                </span>
                            </div>
                            
                            <div class="row g-2 mb-2 small text-secondary">
                                <div class="col-sm-6">
                                    <i class="far fa-calendar-alt me-1"></i> Submit: {{ $revisi->tanggal_submit ? $revisi->tanggal_submit->format('d/m/Y H:i') : '-' }}
                                </div>
                                <div class="col-sm-6">
                                    <i class="fas fa-file-pdf me-1"></i> Ukuran: {{ $revisi->ukuran_file_readable }}
                                </div>
                            </div>
                            
                            @if($revisi->catatan_mahasiswa)
                                <div class="small mb-1"><strong class="text-secondary">Catatan Mahasiswa:</strong> <span class="text-dark">{{ $revisi->catatan_mahasiswa }}</span></div>
                            @endif
                            @if($revisi->catatan_reviewer)
                                <div class="small mb-1"><strong class="text-secondary">Catatan Reviewer:</strong> <span class="text-dark">{{ $revisi->catatan_reviewer }}</span></div>
                            @endif
                            @if($revisi->catatan_operator)
                                <div class="small mb-2"><strong class="text-secondary">Catatan Operator:</strong> <span class="text-dark">{{ $revisi->catatan_operator }}</span></div>
                            @endif
                            
                            <div class="pt-2 border-top">
                                <a href="{{ $revisi->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-3 px-3">
                                    <i class="fas fa-download me-1"></i> Unduh File Revisi
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Sidebar Summary -->
        <div class="col-lg-4">
            <!-- Status Summary Card -->
            <div class="ui-card mb-4">
                <div class="ui-card-header">
                    <h6 class="mb-0 fw-bold fs-6"><i class="fas fa-chart-pie text-primary me-2"></i>Ringkasan Status</h6>
                </div>
                <div class="ui-card-body p-4">
                    <div class="mb-3">
                        <label class="small text-secondary fw-semibold d-block mb-1">Status Validasi Dosen</label>
                        <span class="badge {{ $proposal->status_validasi == 'valid' ? 'bg-success-subtle text-success border border-success-subtle' : ($proposal->status_validasi == 'tidak_valid' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }} px-2.5 py-1 rounded-pill small">
                            {{ ucfirst(str_replace('_', ' ', $proposal->status_validasi)) }}
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="small text-secondary fw-semibold d-block mb-1">Status Revisi</label>
                        @if($proposal->proposalRevisi->count() > 0)
                            @php
                                $revisiTerbaru = $proposal->revisiTerbaru;
                                $statusClass = $revisiTerbaru->status_revisi == 'approved' ? 'bg-success-subtle text-success' : ($revisiTerbaru->status_revisi == 'rejected' ? 'bg-danger-subtle text-danger' : 'bg-info-subtle text-info');
                            @endphp
                            <span class="badge {{ $statusClass }} px-2.5 py-1 rounded-pill small">
                                {{ ucfirst(str_replace('_', ' ', $revisiTerbaru->status_revisi)) }}
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill small">Belum Direvisi</span>
                        @endif
                    </div>
                    
                    <div>
                        <label class="small text-secondary fw-semibold d-block mb-1">Hasil Final Pimpinan PT</label>
                        @if($proposal->hasilFinal)
                            <span class="badge {{ $proposal->hasilFinal->status_final == 'lolos' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger' }} px-2.5 py-1 rounded-pill small">
                                {{ $proposal->hasilFinal->status_final == 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill small">Belum Dinilai</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dosen Pendamping Card -->
            <div class="ui-card mb-4">
                <div class="ui-card-header">
                    <h6 class="mb-0 fw-bold fs-6"><i class="fas fa-user-tie text-primary me-2"></i>Dosen Pendamping</h6>
                </div>
                <div class="ui-card-body p-4 small">
                    <div class="mb-2">
                        <span class="text-secondary d-block mb-0.5">Nama Lengkap</span>
                        <strong class="text-dark">{{ $proposal->dosen->nama_dosen ?? 'N/A' }}</strong>
                    </div>
                    <div class="mb-2">
                        <span class="text-secondary d-block mb-0.5">NIDN</span>
                        <strong class="text-dark">{{ $proposal->dosen->nidn ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <span class="text-secondary d-block mb-0.5">Email</span>
                        <strong class="text-dark">{{ $proposal->dosen->email_dosen ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Mahasiswa Pengaju Card -->
            <div class="ui-card mb-4">
                <div class="ui-card-header">
                    <h6 class="mb-0 fw-bold fs-6"><i class="fas fa-user-graduate text-primary me-2"></i>Mahasiswa Pengaju</h6>
                </div>
                <div class="ui-card-body p-4 small">
                    <div class="mb-2">
                        <span class="text-secondary d-block mb-0.5">Nama Lengkap</span>
                        <strong class="text-dark">{{ $proposal->mahasiswa->nama_mahasiswa ?? 'N/A' }}</strong>
                    </div>
                    <div class="mb-2">
                        <span class="text-secondary d-block mb-0.5">NIM</span>
                        <strong class="text-dark">{{ $proposal->mahasiswa->nim ?? 'N/A' }}</strong>
                    </div>
                    <div>
                        <span class="text-secondary d-block mb-0.5">Email</span>
                        <strong class="text-dark">{{ $proposal->mahasiswa->email_mahasiswa ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Dokumen Proposal Card -->
            @if($proposal->dokumen)
            <div class="ui-card mb-4">
                <div class="ui-card-header">
                    <h6 class="mb-0 fw-bold fs-6"><i class="fas fa-file-pdf text-danger me-2"></i>Berkas Proposal</h6>
                </div>
                <div class="ui-card-body p-4">
                    <div class="small text-secondary mb-1">Nama Berkas:</div>
                    <div class="fw-bold text-dark text-truncate mb-2" title="{{ $proposal->dokumen->nama_file ?? 'Proposal.pdf' }}">
                        {{ $proposal->dokumen->nama_file ?? 'Proposal.pdf' }}
                    </div>
                    <div class="small text-muted mb-3">
                        Ukuran: @formatId($proposal->dokumen->ukuran_file / 1024, 2) KB
                    </div>
                    <a href="{{ route('file.serve', ['path' => $proposal->dokumen->path_file]) }}" target="_blank" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                        <i class="fas fa-download me-1"></i> Unduh File PDF
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Hasil Final -->
@if(!$proposal->hasilFinal)
<div class="modal fade" id="hasilFinalModal" tabindex="-1" aria-labelledby="hasilFinalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold" id="hasilFinalModalLabel">
                    <i class="fas fa-gavel me-2"></i>Tentukan Hasil Penilaian Final
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="hasilFinalForm">
                    <input type="hidden" id="proposalId" name="proposal_id" value="{{ $proposal->id_proposal }}">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Judul Proposal</label>
                            <input type="text" class="form-control form-control-sm rounded-3 bg-light" value="{{ $proposal->judul_proposal }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Pengaju</label>
                            <input type="text" class="form-control form-control-sm rounded-3 bg-light" value="{{ $proposal->mahasiswa->nama_mahasiswa ?? 'N/A' }}" readonly>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Skim PKM</label>
                            <input type="text" class="form-control form-control-sm rounded-3 bg-light" value="{{ $proposal->skim }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Status Revisi Saat Ini</label>
                            <input type="text" class="form-control form-control-sm rounded-3 bg-light" value="{{ $proposal->getRevisiStatus() ? ucfirst(str_replace('_', ' ', $proposal->getRevisiStatus())) : 'Belum Direvisi' }}" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Keputusan Hasil Final <span class="text-danger">*</span></label>
                        <select class="form-select rounded-3" id="statusFinal" name="status_final" required>
                            <option value="">-- Pilih Hasil Final --</option>
                            <option value="lolos">Lolos Final (Didanai / Lanjut PIMNAS)</option>
                            <option value="tidak_lolos">Tidak Lolos</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Catatan Evaluasi Final</label>
                        <textarea class="form-control rounded-3" id="catatanFinal" name="catatan_final" rows="4" 
                                  placeholder="Berikan umpan balik atau catatan resmi bagi tim mahasiswa..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-sm btn-secondary rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-primary rounded-3 px-3 fw-semibold" id="submitHasilFinal">
                    <i class="fas fa-save me-1"></i> Simpan Hasil Final
                </button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('submitHasilFinal')?.addEventListener('click', function() {
        const proposalId = document.getElementById('proposalId').value;
        const statusFinal = document.getElementById('statusFinal').value;
        const catatanFinal = document.getElementById('catatanFinal').value;
        
        if (!statusFinal) {
            alert('Mohon pilih hasil final terlebih dahulu');
            return;
        }
        
        const data = {
            proposal_id: proposalId,
            status_final: statusFinal,
            catatan_final: catatanFinal
        };
        
        const submitBtn = this;
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        submitBtn.disabled = true;
        
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
                if (window.AppUI && typeof window.AppUI.showToast === 'function') {
                    window.AppUI.showToast(data.message, 'success');
                } else {
                    alert(data.message);
                }
                
                const modal = bootstrap.Modal.getInstance(document.getElementById('hasilFinalModal'));
                if (modal) modal.hide();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                alert(data.message || 'Gagal menyimpan hasil final');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan hasil final');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
});
</script>
@endpush
