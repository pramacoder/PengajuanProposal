@extends('mainlayout.app')

@section('title', 'Beranda Reviewer')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'active' => true],
    ]" />

    <!-- Header -->
    <x-page-header 
        title="Beranda Reviewer" 
        subtitle="Evaluasi dan penilaian usulan proposal PKM Universitas Udayana"
        badge="Reviewer Portal"
        icon="fa-clipboard-check"
    />

    <!-- Filter Card -->
    <div class="filter-card mb-4">
        <form method="GET" action="{{ route('reviewer.dashboard') }}" class="row g-3 align-items-center">
            <div class="col-md-4 col-lg-3">
                <label class="form-label small fw-semibold text-secondary mb-1">Tahun Ajaran</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <select name="tahun_ajaran" id="tahun_ajaran" class="form-select border-start-0 rounded-end-3" onchange="this.form.submit()">
                        @foreach($tahunAjaranList as $tahunAjaran)
                            <option value="{{ $tahunAjaran }}" {{ $tahunAjaranTerpilih == $tahunAjaran ? 'selected' : '' }}>
                                {{ $tahunAjaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-8 col-lg-9 text-md-end pt-md-3">
                <span class="text-secondary small">
                    <i class="fas fa-info-circle text-primary me-1"></i>Hanya proposal berstatus <strong>Review Administratif</strong> atau <strong>Review Substantif</strong> yang dapat dinilai.
                </span>
            </div>
        </form>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-4">
            <div class="ui-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-semibold text-uppercase tracking-wider">Total Ditugaskan</div>
                        <div class="display-6 fw-bold text-primary mt-1">{{ $totalAssigned }}</div>
                        <small class="text-muted">Proposal valid dalam penugasan Anda</small>
                    </div>
                    <div class="p-3 bg-primary-subtle text-primary rounded-3">
                        <i class="fas fa-clipboard-list fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4">
            <div class="ui-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-semibold text-uppercase tracking-wider">Review Selesai</div>
                        <div class="display-6 fw-bold text-success mt-1">{{ $completedReview }}</div>
                        <small class="text-muted">Proposal yang telah Anda beri penilaian</small>
                    </div>
                    <div class="p-3 bg-success-subtle text-success rounded-3">
                        <i class="fas fa-check-circle fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4">
            <div class="ui-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-semibold text-uppercase tracking-wider">Review Pending</div>
                        <div class="display-6 fw-bold text-warning mt-1">{{ $pendingReview }}</div>
                        <small class="text-muted">Proposal menanti proses penilaian</small>
                    </div>
                    <div class="p-3 bg-warning-subtle text-warning rounded-3">
                        <i class="fas fa-clock fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Proposal -->
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-list text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Daftar Proposal yang Ditugaskan ({{ $tahunAjaranTerpilih }})</h6>
            </div>
            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                {{ $proposals->count() }} Usulan
            </span>
        </div>
        <div class="ui-card-body p-0">
            @if($proposals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern align-middle mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th width="4%" class="text-center">No</th>
                                <th width="28%">Judul Proposal</th>
                                <th width="16%">Mahasiswa Pengaju</th>
                                <th width="16%">Dosen Pendamping</th>
                                <th width="8%" class="text-center">Skim</th>
                                <th width="10%" class="text-center">Status Review</th>
                                <th width="10%">Tanggal Ditugaskan</th>
                                <th width="8%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $index => $proposal)
                                @php
                                    $isAdminReviewer = $proposal->id_reviewer_administratif == auth()->user()->id_reviewer;
                                    $isSubstantifReviewer = $proposal->id_reviewer_substantif_1 == auth()->user()->id_reviewer || 
                                                           $proposal->id_reviewer_substantif_2 == auth()->user()->id_reviewer;
                                    
                                    $reviewStatus = 'Pending';
                                    $isSelesai = false;
                                    
                                    if ($isAdminReviewer) {
                                        $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', auth()->user()->id_reviewer)->first();
                                        if ($adminReview && $adminReview->note_administratif) {
                                            $reviewStatus = 'Selesai';
                                            $isSelesai = true;
                                        }
                                    }
                                    
                                    if ($isSubstantifReviewer) {
                                        $substantifReview = $proposal->nilaiSubstantif->where('id_reviewer', auth()->user()->id_reviewer)->first();
                                        if ($substantifReview && $substantifReview->note_substantif && $substantifReview->note_substantif !== 'Review substantif dimulai') {
                                            $reviewStatus = 'Selesai';
                                            $isSelesai = true;
                                        }
                                    }
                                    
                                    $assignedDate = $proposal->updated_at;
                                @endphp
                                
                                <tr>
                                    <td class="text-center text-secondary small">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 320px;" title="{{ $proposal->judul_proposal }}">
                                            {{ $proposal->judul_proposal }}
                                        </div>
                                        <div class="d-flex align-items-center gap-1 mt-1">
                                            @if($isAdminReviewer)
                                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill" style="font-size: 0.7rem;">Administratif</span>
                                            @endif
                                            @if($isSubstantifReviewer)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size: 0.7rem;">Substantif</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-dark">{{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="text-dark">{{ $proposal->dosen->nama_dosen ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $proposal->dosen->nuptk ?? '' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small fw-semibold">
                                            {{ $proposal->skim }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($isSelesai)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small">
                                                <i class="fas fa-check me-1"></i>Selesai
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill small">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-secondary small">
                                        <i class="far fa-calendar-alt me-1"></i>{{ $assignedDate ? $assignedDate->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('reviewer.detail.proposal', $proposal->id_proposal) }}" 
                                           class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold">
                                            <i class="fas fa-eye me-1"></i>Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state-box">
                    <i class="fas fa-inbox"></i>
                    <h5 class="fw-bold text-dark">Belum Ada Proposal Ditugaskan</h5>
                    <p class="text-muted small">Proposal akan muncul di sini setelah operator menugaskan Anda dan proposal telah divalidasi oleh dosen pembimbing.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Panduan Review Cards -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="ui-card h-100 p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="fas fa-info-circle text-primary fs-5"></i>
                    <h6 class="mb-0 fw-bold fs-6">Tanggung Jawab Reviewer</h6>
                </div>
                <ul class="list-unstyled d-flex flex-column gap-2 mb-0 small text-secondary">
                    <li class="d-flex align-items-start gap-2">
                        <i class="fas fa-check-circle text-success mt-1"></i>
                        <span><strong>Review Administratif:</strong> Memeriksa kepatuhan format sistematika, berkas lampiran, dan kelengkapan dokumen pengajuan.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="fas fa-check-circle text-success mt-1"></i>
                        <span><strong>Review Substantif:</strong> Menilai kelayakan ilmiah, urgensi masalah, inovasi metode, serta potensi capaian program.</span>
                    </li>
                    <li class="d-flex align-items-start gap-2">
                        <i class="fas fa-check-circle text-success mt-1"></i>
                        <span>Memberikan nilai rubrik objektif beserta catatan deskriptif konstruktif untuk pengembangan mahasiswa.</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="ui-card h-100 p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="fas fa-shield-halved text-warning fs-5"></i>
                    <h6 class="mb-0 fw-bold fs-6">Tahapan & Batasan Review</h6>
                </div>
                <div class="alert alert-warning border-0 rounded-3 small p-3 mb-2">
                    <i class="fas fa-exclamation-triangle me-1"></i>Hanya proposal yang telah lolos validasi dosen pembimbing yang dapat diproses oleh reviewer.
                </div>
                <div class="d-flex flex-column gap-1 small text-secondary">
                    <div><i class="fas fa-circle text-warning me-1.5" style="font-size: 0.6rem;"></i><strong>Review Administratif:</strong> Tahap pertama pengecekan kelengkapan format.</div>
                    <div><i class="fas fa-circle text-info me-1.5" style="font-size: 0.6rem;"></i><strong>Review Substantif:</strong> Tahap kedua penilaian konten oleh 2 reviewer substantif.</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('#dataTable tbody tr').length > 0) {
            $('#dataTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[0, 'asc']]
            });
        }
    });
</script>
@endpush
