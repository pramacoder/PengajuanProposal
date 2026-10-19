@extends('mainlayout.app')

@section('title', 'Review Substantif Seleksi - Reviewer')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('reviewer.dashboard')],
        ['label' => 'Review Substantif Seleksi', 'active' => true],
    ]" />

    <!-- Header Section -->
    <x-page-header 
        title="Review Substantif Seleksi" 
        subtitle="Penilaian mendalam substantif seleksi proposal PKM menuju seleksi tingkat universitas" 
        badge="Reviewer Portal"
        icon="fa-award"
    />

    <!-- Filter Card -->
    <div class="filter-card mb-4">
        <form method="GET" action="{{ route('reviewer.review.substantif.seleksi') }}" class="row g-3 align-items-center">
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
                    <i class="fas fa-info-circle text-primary me-1"></i>Penilaian tahap seleksi khusus proposal yang telah menyelesaikan revisi berkas.
                </span>
            </div>
        </form>
    </div>

    <!-- Tabel Proposal Review Substantif Seleksi -->
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-list text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Daftar Proposal untuk Review Substantif Seleksi ({{ $tahunAjaranTerpilih }})</h6>
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
                                    $substantifReview = $proposal->nilaiSubstantif->where('id_reviewer', auth()->id())->where('jenis_review', 'seleksi')->first();
                                    $isAssignedSeleksi = $proposal->id_reviewer_substantif_seleksi_1 == auth()->id() || $proposal->id_reviewer_substantif_seleksi_2 == auth()->id();
                                    
                                    if ($substantifReview && $substantifReview->note_substantif && $substantifReview->note_substantif !== 'Review substantif dimulai') {
                                        $reviewStatus = 'Selesai';
                                        $badgeClass = 'bg-success-subtle text-success border-success-subtle';
                                    } elseif ($isAssignedSeleksi) {
                                        $reviewStatus = 'Siap Review';
                                        $badgeClass = 'bg-info-subtle text-info border-info-subtle';
                                    } else {
                                        $reviewStatus = 'Menunggu';
                                        $badgeClass = 'bg-warning-subtle text-warning border-warning-subtle';
                                    }
                                    
                                    $assignedDate = $substantifReview ? $substantifReview->created_at : $proposal->updated_at;
                                @endphp
                                
                                <tr>
                                    <td class="text-center text-secondary small">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark text-truncate" style="max-width: 320px;" title="{{ $proposal->judul_proposal }}">
                                            {{ $proposal->judul_proposal }}
                                        </div>
                                        <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $assignedDate ? $assignedDate->format('d/m/Y') : '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-dark">{{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $proposal->mahasiswa ? ($proposal->mahasiswa->getMetadataValue('nim') ?? $proposal->mahasiswa->nim ?? 'N/A') : 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <div class="text-dark">{{ $proposal->dosen->nama_dosen ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $proposal->dosen ? ($proposal->dosen->getMetadataValue('nuptk') ?? $proposal->dosen->nuptk ?? 'N/A') : 'N/A' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small fw-semibold">
                                            {{ $proposal->skim }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge border rounded-pill px-2.5 py-1 small {{ $badgeClass }}">
                                            {{ $reviewStatus }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('reviewer.detail.proposal', ['id' => $proposal->id_proposal, 'seleksi' => 1]) }}" 
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
                    <p class="text-muted small">Belum ada usulan proposal yang ditugaskan untuk review substantif seleksi pada tahun ajaran ini.</p>
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
