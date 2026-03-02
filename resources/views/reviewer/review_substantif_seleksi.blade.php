@extends('mainlayout.app')

@section('title', 'Review Substantif Seleksi')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('reviewer.dashboard')],
        ['label' => 'Review Substantif Seleksi', 'active' => true],
    ]" />

    <!-- Header -->
    <x-page-header 
        title="Review Substantif Seleksi" 
        subtitle="UNIVERSITAS UDAYANA"
        description="Lakukan review substantif seleksi terhadap proposal yang ditugaskan" />
    
    <!-- Tahun Ajaran Filter -->
    <div class="row mb-4">
        <div class="col-md-3">
            <form method="GET" action="{{ route('reviewer.review.substantif.seleksi') }}" class="d-inline">
                <div class="input-group">
                    <label class="input-group-text" for="tahun_ajaran">
                        <i class="fas fa-calendar me-2"></i>Tahun Ajaran
                    </label>
                    <select name="tahun_ajaran" id="tahun_ajaran" class="form-select" onchange="this.form.submit()" style="min-width: 150px;">
                        @foreach($tahunAjaranList as $tahunAjaran)
                            <option value="{{ $tahunAjaran }}" {{ $tahunAjaranTerpilih == $tahunAjaran ? 'selected' : '' }}>
                                {{ $tahunAjaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Proposal Review Substantif Seleksi -->
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>Daftar Proposal untuk Review Substantif Seleksi
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
                                <th>Status Review</th>
                                <th>Tanggal Ditugaskan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $index => $proposal)
                                @php
                                    $substantifReview = $proposal->nilaiSubstantif->where('id_reviewer', auth()->id())->where('jenis_review', 'seleksi')->first();
                                    $isAssignedSeleksi = $proposal->id_reviewer_substantif_seleksi_1 == auth()->id() || $proposal->id_reviewer_substantif_seleksi_2 == auth()->id();
                                    
                                    if ($substantifReview && $substantifReview->note_substantif && $substantifReview->note_substantif !== 'Review substantif dimulai') {
                                        $reviewStatus = 'Selesai';
                                        $statusClass = 'bg-success';
                                    } elseif ($isAssignedSeleksi) {
                                        $reviewStatus = 'Siap Review';
                                        $statusClass = 'bg-primary';
                                    } else {
                                        $reviewStatus = 'Menunggu';
                                        $statusClass = 'bg-warning';
                                    }
                                    
                                    $assignedDate = $substantifReview ? $substantifReview->created_at : $proposal->updated_at;
                                @endphp
                                
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $proposal->judul_proposal }}</strong>
                                        <br>
                                    </td>
                                    <td>
                                        {{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">{{ $proposal->mahasiswa ? ($proposal->mahasiswa->getMetadataValue('nim') ?? $proposal->mahasiswa->nim ?? 'N/A') : 'N/A' }}</small>
                                    </td>
                                    <td>
                                        {{ $proposal->dosen->nama_dosen ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">{{ $proposal->dosen ? ($proposal->dosen->getMetadataValue('nuptk') ?? $proposal->dosen->nuptk ?? 'N/A') : 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $proposal->skim }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $statusClass }}">{{ $reviewStatus }}</span>
                                        @if($reviewStatus === 'Selesai')
                                            <br>
                                            <small class="text-muted">Review selesai</small>
                                        @elseif($reviewStatus === 'Siap Review')
                                            <br>
                                            <small class="text-muted">Dapat direview</small>
                                        @else
                                            <br>
                                            <small class="text-muted">Belum ditugaskan</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $assignedDate ? $assignedDate->format('d/m/Y H:i') : 'N/A' }}
                                    </td>
                                    <td>
                                        @if($isAssignedSeleksi)
                                            <a href="{{ route('reviewer.detail.proposal.substantif', $proposal->id_proposal) }}?seleksi=1" 
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Review
                                            </a>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="fas fa-clock me-1"></i>Menunggu
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-search fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Belum ada proposal untuk review substantif seleksi</h5>
                    <p class="text-gray-400">Proposal akan muncul di sini setelah operator menugaskan Anda sebagai reviewer substantif seleksi</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Informasi Review Substantif Seleksi -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Panduan Review Substantif Seleksi
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Review substantif seleksi meliputi penilaian kualitas konten dan substansi proposal untuk tahap seleksi:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Kualitas dan orisinalitas ide</li>
                        <li><i class="fas fa-check text-success me-2"></i>Metodologi penelitian</li>
                        <li><i class="fas fa-check text-success me-2"></i>Kelayakan teknis dan ekonomis</li>
                        <li><i class="fas fa-check text-success me-2"></i>Dampak dan manfaat</li>
                        <li><i class="fas fa-check text-success me-2"></i>Kemampuan tim pelaksana</li>
                        <li><i class="fas fa-check text-success me-2"></i>Kesinambungan program</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-tasks me-2"></i>Status Review
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $totalAssigned = $proposals->count();
                        $completedReview = $proposals->filter(function($proposal) {
                            $substantifReview = $proposal->nilaiSubstantif->where('id_reviewer', auth()->id())->where('jenis_review', 'seleksi')->first();
                            return $substantifReview && $substantifReview->note_substantif && $substantifReview->note_substantif !== 'Review substantif dimulai';
                        })->count();
                        
                        $readyForReview = $proposals->filter(function($proposal) {
                            $isAssignedSeleksi = $proposal->id_reviewer_substantif_seleksi_1 == auth()->id() || $proposal->id_reviewer_substantif_seleksi_2 == auth()->id();
                            return $isAssignedSeleksi;
                        })->count();
                        
                        $pendingReview = $totalAssigned - $completedReview;
                    @endphp
                    
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="border-end">
                                <h4 class="text-success">{{ $completedReview }}</h4>
                                <small class="text-muted">Selesai</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <h4 class="text-primary">{{ $readyForReview }}</h4>
                            <small class="text-muted">Siap Review</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-warning">{{ $pendingReview }}</h4>
                            <small class="text-muted">Menunggu</small>
                        </div>
                    </div>
                    
                    @if($totalAssigned > 0)
                        <div class="progress mt-3">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ ($completedReview / $totalAssigned) * 100 }}%">
                                {{ round(($completedReview / $totalAssigned) * 100) }}%
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Kriteria Penilaian -->
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <h6 class="mb-0">
                <i class="fas fa-star me-2"></i>Kriteria Penilaian Substantif Seleksi
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Kriteria Utama (70%)</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-circle text-primary me-2"></i>Kreativitas dan orisinalitas ide (25%)</li>
                        <li><i class="fas fa-circle text-primary me-2"></i>Kelayakan program (25%)</li>
                        <li><i class="fas fa-circle text-primary me-2"></i>Dampak dan manfaat (20%)</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success">Kriteria Pendukung (30%)</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-circle text-success me-2"></i>Kemampuan tim pelaksana (15%)</li>
                        <li><i class="fas fa-circle text-success me-2"></i>Kesinambungan program (15%)</li>
                    </ul>
                </div>
            </div>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Catatan:</strong> Berikan nilai 0-10 per kriteria berdasarkan penilaian Anda terhadap proposal. 
                Nilai yang lebih tinggi menunjukkan kualitas proposal yang lebih baik. Lengkapi review melalui halaman detail proposal.
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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
