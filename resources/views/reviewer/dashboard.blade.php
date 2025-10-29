@section('styles')
<style>
    .reviewer-compact { font-size: 0.875rem; }
    .reviewer-compact h1, .reviewer-compact .h1 { font-size: 1.5rem; }
    .reviewer-compact h2, .reviewer-compact .h2 { font-size: 1.25rem; }
    .reviewer-compact h3, .reviewer-compact .h3 { font-size: 1.1rem; }
    .reviewer-compact .btn { padding: 0.35rem 0.6rem; font-size: 0.85rem; border-radius: 6px; }
    .reviewer-compact .badge { padding: 0.3rem 0.5rem; font-size: 0.7rem; }
    .reviewer-compact .form-select, .reviewer-compact .form-control { padding: 0.35rem 0.6rem; font-size: 0.875rem; }
    .reviewer-compact .card-body { padding: 0.9rem; }
    .reviewer-compact .card-header { padding: 0.7rem 0.9rem; }
    .reviewer-compact table.table th, 
    .reviewer-compact table.table td { padding: 0.5rem 0.6rem; }
    .reviewer-compact .alert { padding: 0.6rem 0.8rem; font-size: 0.875rem; }
    /* Tabel proposal: layout fixed dan skala lebih kecil */
    .reviewer-compact .table { table-layout: fixed; font-size: 0.82rem; }
    /* Lebar kolom: No, Judul, Mahasiswa, Dosen, Skim, Status, Jenis, Status Review, Tanggal, Aksi */
    .reviewer-compact .table th:nth-child(1),
    .reviewer-compact .table td:nth-child(1) { width: 4.5rem; }
    .reviewer-compact .table th:nth-child(2),
    .reviewer-compact .table td:nth-child(2) { width: 40%; }
    .reviewer-compact .table th:nth-child(3),
    .reviewer-compact .table td:nth-child(3) { width: 14%; }
    .reviewer-compact .table th:nth-child(4),
    .reviewer-compact .table td:nth-child(4) { width: 18%; }
    .reviewer-compact .table th:nth-child(5),
    .reviewer-compact .table td:nth-child(5) { width: 6rem; }
    .reviewer-compact .table th:nth-child(6),
    .reviewer-compact .table td:nth-child(6) { width: 9rem; }
    .reviewer-compact .table th:nth-child(7),
    .reviewer-compact .table td:nth-child(7) { width: 8rem; }
    .reviewer-compact .table th:nth-child(8),
    .reviewer-compact .table td:nth-child(8) { width: 8rem; }
    .reviewer-compact .table th:nth-child(9),
    .reviewer-compact .table td:nth-child(9) { width: 9rem; }
    .reviewer-compact .table th:nth-child(10),
    .reviewer-compact .table td:nth-child(10) { width: 6.5rem; }
    /* Clamp teks judul agar tidak mendorong kolom lain */
    .reviewer-compact .title-clamp { 
        display: -webkit-box; 
        -webkit-line-clamp: 2; 
        -webkit-box-orient: vertical; 
        overflow: hidden; 
        word-wrap: break-word; 
        white-space: normal; 
    }
</style>
@endsection

@extends('mainlayout.app')

@section('title', 'Beranda Reviewer')

@section('content')
<div class="container-fluid reviewer-compact">
    <!-- Header -->
    <x-page-header 
        title="Beranda Reviewer" 
        subtitle="UNIVERSITAS UDAYANA"
        description="Selamat datang di dashboard reviewer PKM" />
    
    <!-- Year Filter -->
    <div class="row mb-4">
        <div class="col-md-3">
            <select class="form-select" id="tahunFilter">
                @for($year = date('Y'); $year >= 2020; $year--)
                    <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                        Tahun {{ $year }}
                    </option>
                @endfor
            </select>
        </div>
    </div>

    <!-- Informasi Penting -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Informasi:</strong> Halaman ini menampilkan proposal yang sudah divalidasi dan ditugaskan untuk Anda review. Hanya proposal dengan status "Review Administratif" atau "Review Substantif" yang dapat direview.
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>Total Proposal Ditugaskan
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="h2 mb-0 font-weight-bold text-primary">{{ $totalAssigned }}</div>
                    <small class="text-muted">Proposal valid yang ditugaskan untuk review</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Review Selesai
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="h2 mb-0 font-weight-bold text-success">{{ $completedReview }}</div>
                    <small class="text-muted">Proposal yang sudah direview</small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Review Pending
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="h2 mb-0 font-weight-bold text-warning">{{ $pendingReview }}</div>
                    <small class="text-muted">Proposal yang belum direview</small>
                </div>
            </div>
        </div>

        </div>
    </div>

    <!-- Tabel Proposal -->
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>Daftar Proposal yang Ditugaskan
            </h6>
        </div>
        <div class="card-body">
            @if($proposals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Proposal</th>
                                <th>Mahasiswa</th>
                                <th>Dosen Pendamping</th>
                                <th>Skim</th>
                                <th>Status Proposal</th>
                                <th>Jenis Review</th>
                                <th>Status Review</th>
                                <th>Tanggal Ditugaskan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $index => $proposal)
                                @php
                                    $isAdminReviewer = $proposal->id_reviewer_administratif == auth()->user()->id_reviewer;
                                    $isSubstantifReviewer = $proposal->id_reviewer_substantif_1 == auth()->user()->id_reviewer || 
                                                           $proposal->id_reviewer_substantif_2 == auth()->user()->id_reviewer;
                                    
                                    // Cek status review berdasarkan jenis reviewer
                                    $reviewStatus = 'Pending';
                                    $reviewStatusClass = 'bg-warning';
                                    
                                    if ($isAdminReviewer) {
                                        $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', auth()->user()->id_reviewer)->first();
                                        if ($adminReview && $adminReview->note_administratif) {
                                            $reviewStatus = 'Selesai';
                                            $reviewStatusClass = 'bg-success';
                                        }
                                    }
                                    
                                    if ($isSubstantifReviewer) {
                                        $substantifReview = $proposal->nilaiSubstantif->where('id_reviewer', auth()->user()->id_reviewer)->first();
                                        if ($substantifReview && $substantifReview->note_substantif && $substantifReview->note_substantif !== 'Review substantif dimulai') {
                                            $reviewStatus = 'Selesai';
                                            $reviewStatusClass = 'bg-success';
                                        }
                                    }
                                @endphp
                                
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong class="title-clamp">{{ $proposal->judul_proposal }}</strong>
                                        <br>
                                        <small class="text-muted"></small>
                                    </td>
                                    <td>
                                        {{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">{{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        {{ $proposal->dosen->nama_dosen ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">{{ $proposal->dosen->nuptk ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $proposal->skim }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch($proposal->status) {
                                                case 'review_administratif':
                                                    $statusClass = 'bg-warning';
                                                    $statusText = 'Review Administratif';
                                                    break;
                                                case 'review_substantif':
                                                    $statusClass = 'bg-info';
                                                    $statusText = 'Review Substantif';
                                                    break;
                                                case 'review_completed':
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Review Selesai';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-secondary';
                                                    $statusText = ucfirst(str_replace('_', ' ', $proposal->status));
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>
                                        @if($isAdminReviewer)
                                            <span class="badge bg-info me-1">Administratif</span>
                                        @endif
                                        @if($isSubstantifReviewer)
                                            <span class="badge bg-success">Substantif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $reviewStatusClass }}">{{ $reviewStatus }}</span>
                                        @if($reviewStatus == 'Selesai')
                                            @if($isAdminReviewer)
                                                <br><small class="text-muted">Catatan tersimpan</small>
                                            @elseif($isSubstantifReviewer)
                                                <br><small class="text-muted">Review selesai</small>
                                            @endif
                                        @else
                                            <br><small class="text-muted">Belum direview</small>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // Tentukan tanggal penugasan berdasarkan jenis reviewer
                                            $assignedDate = null;
                                            if ($isAdminReviewer) {
                                                $assignedDate = $proposal->updated_at; // Tanggal update terakhir
                                            } elseif ($isSubstantifReviewer) {
                                                $assignedDate = $proposal->updated_at; // Tanggal update terakhir
                                            }
                                        @endphp
                                        {{ $assignedDate ? $assignedDate->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td>
                                        <a href="{{ route('reviewer.detail.proposal', $proposal->id_proposal) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Review
                                        </a>
                                        <!-- Debug info -->
                                        <small class="d-block text-muted mt-1">
                                            ID: {{ $proposal->id_proposal }} | 
                                            Status: {{ $proposal->status }} | 
                                            Valid: {{ $proposal->status_validasi }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Belum ada proposal yang ditugaskan</h5>
                    <p class="text-gray-400">Proposal akan muncul di sini setelah operator menugaskan Anda sebagai reviewer dan proposal sudah divalidasi</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Panduan Review -->
    <div class="row mt-4">
        <div class="col-lg-6">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Panduan Review
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Sebagai reviewer, Anda bertanggung jawab untuk:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Review administratif: kelengkapan dokumen dan format</li>
                        <li><i class="fas fa-check text-success me-2"></i>Review substantif: kualitas konten dan substansi</li>
                        <li><i class="fas fa-check text-success me-2"></i>Memberikan nilai dan catatan yang objektif</li>
                        <li><i class="fas fa-check text-success me-2"></i>Menyelesaikan review sesuai timeline</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Penting!
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Perhatian:</strong> Hanya review proposal yang sudah ditugaskan oleh operator dan memiliki status yang sesuai. Proposal yang belum divalidasi tidak dapat direview.
                    </div>
                    <div class="mt-3">
                        <h6 class="text-primary">Status Proposal yang Dapat Direview:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="fas fa-circle text-warning me-2"></i><strong>Review Administratif:</strong> Proposal yang sudah divalidasi dan siap untuk review administratif</li>
                            <li><i class="fas fa-circle text-info me-2"></i><strong>Review Substantif:</strong> Proposal yang sudah melewati review administratif dan siap untuk review substantif</li>
                            <li><i class="fas fa-circle text-success me-2"></i><strong>Review Completed:</strong> Proposal yang sudah selesai direview oleh semua reviewer</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Filter tahun
    document.getElementById('tahunFilter').addEventListener('change', function() {
        const tahun = this.value;
        window.location.href = `{{ route('reviewer.dashboard') }}?tahun=${tahun}`;
    });

    // DataTable initialization
    $(document).ready(function() {
        $('#dataTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
            },
            pageLength: 10,
            order: [[0, 'asc']]
        });
    });
</script>
@endpush
@endsection

