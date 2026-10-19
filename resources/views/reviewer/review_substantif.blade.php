@extends('mainlayout.app')

@section('title', 'Review Substantif - Reviewer')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('reviewer.dashboard')],
        ['label' => 'Review Substantif', 'active' => true],
    ]" />

    <!-- Header Section -->
    <x-page-header 
        title="Review Substantif" 
        subtitle="Penilaian kualitas konten, keilmiahan, dan substansi proposal PKM yang ditugaskan" 
        badge="Reviewer Portal"
        icon="fa-star-half-stroke"
    />

    <!-- Filter Card -->
    <div class="filter-card mb-4">
        <form method="GET" action="{{ route('reviewer.review.substantif') }}" class="row g-3 align-items-center">
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
                    <i class="fas fa-info-circle text-primary me-1"></i>Hanya proposal yang telah lolos review administratif yang dapat dinilai pada tahap ini.
                </span>
            </div>
        </form>
    </div>

    <!-- Stat mini-cards -->
    @php
        $totalAssigned   = $proposals->count();
        $completedReview = $proposals->filter(function($p) {
            $r = $p->nilaiSubstantif->where('id_reviewer', auth()->id())->first();
            return $r && $r->note_substantif && $r->note_substantif !== 'Review substantif dimulai';
        })->count();
        $readyForReview = $proposals->filter(function($p) {
            $isAssigned = $p->id_reviewer_substantif_1 == auth()->id() || $p->id_reviewer_substantif_2 == auth()->id();
            $adminDone  = $p->nilaiAdministratif()->where('id_reviewer', $p->id_reviewer_administratif)->whereNotNull('note_administratif')->exists();
            return $adminDone && $isAssigned;
        })->count();
        $pendingReview = max(0, $totalAssigned - $completedReview - $readyForReview);
        $progress      = $totalAssigned > 0 ? round(($completedReview / $totalAssigned) * 100) : 0;
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="ui-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-semibold text-uppercase tracking-wider">Total Ditugaskan</div>
                        <div class="display-6 fw-bold text-primary mt-1">{{ $totalAssigned }}</div>
                        <small class="text-muted">Proposal substantif</small>
                    </div>
                    <div class="p-3 bg-primary-subtle text-primary rounded-3">
                        <i class="fas fa-file-alt fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="ui-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-semibold text-uppercase tracking-wider">Review Selesai</div>
                        <div class="display-6 fw-bold text-success mt-1">{{ $completedReview }}</div>
                        <small class="text-muted">Telah dinilai</small>
                    </div>
                    <div class="p-3 bg-success-subtle text-success rounded-3">
                        <i class="fas fa-check-circle fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="ui-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-semibold text-uppercase tracking-wider">Siap Direview</div>
                        <div class="display-6 fw-bold text-info mt-1">{{ $readyForReview }}</div>
                        <small class="text-muted">Admin selesai</small>
                    </div>
                    <div class="p-3 bg-info-subtle text-info rounded-3">
                        <i class="fas fa-play-circle fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="ui-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small fw-semibold text-uppercase tracking-wider">Menunggu Admin</div>
                        <div class="display-6 fw-bold text-warning mt-1">{{ $pendingReview }}</div>
                        <small class="text-muted">Belum siap</small>
                    </div>
                    <div class="p-3 bg-warning-subtle text-warning rounded-3">
                        <i class="fas fa-hourglass-half fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Proposal Table -->
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-star-half-alt text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Daftar Usulan — Review Substantif ({{ $tahunAjaranTerpilih }})</h6>
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
                                <th width="30%">Judul Proposal</th>
                                <th width="18%">Mahasiswa Pengaju</th>
                                <th width="18%">Dosen Pendamping</th>
                                <th width="8%" class="text-center">Skim</th>
                                <th width="10%" class="text-center">Status Review</th>
                                <th width="8%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $index => $proposal)
                                @php
                                    $substantifReview   = $proposal->nilaiSubstantif->where('id_reviewer', auth()->id())->first();
                                    $isAssignedSubstantif = $proposal->id_reviewer_substantif_1 == auth()->id() || $proposal->id_reviewer_substantif_2 == auth()->id();
                                    $adminReviewDone    = $proposal->nilaiAdministratif()->where('id_reviewer', $proposal->id_reviewer_administratif)->whereNotNull('note_administratif')->exists();

                                    if ($substantifReview && $substantifReview->note_substantif && $substantifReview->note_substantif !== 'Review substantif dimulai') {
                                        $reviewStatus = 'selesai';
                                    } elseif ($adminReviewDone && $isAssignedSubstantif) {
                                        $reviewStatus = 'siap';
                                    } else {
                                        $reviewStatus = 'menunggu';
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center text-secondary small">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 320px;" title="{{ $proposal->judul_proposal }}">
                                            {{ $proposal->judul_proposal }}
                                        </div>
                                        <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $proposal->updated_at->format('d/m/Y') }}</small>
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
                                        @if($reviewStatus === 'selesai')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small">
                                                <i class="fas fa-check me-1"></i>Selesai
                                            </span>
                                        @elseif($reviewStatus === 'siap')
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill small">
                                                <i class="fas fa-play me-1"></i>Siap Review
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill small">
                                                <i class="fas fa-clock me-1"></i>Menunggu Admin
                                            </span>
                                        @endif
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
                    <h5 class="fw-bold text-dark">Tidak Ada Usulan Ditugaskan</h5>
                    <p class="text-muted small">Belum ada usulan proposal yang ditugaskan untuk review substantif pada tahun ajaran ini.</p>
                </div>
            @endif
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
