@extends('mainlayout.app')

@section('title', 'Review Administratif')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <x-page-header 
        title="Review Administratif" 
        subtitle="UNIVERSITAS UDAYANA"
        description="Lakukan review administratif terhadap proposal yang ditugaskan" />
    
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
        <strong>Informasi:</strong> Halaman ini menampilkan proposal yang sudah divalidasi dan ditugaskan untuk review administratif. Hanya proposal dengan status "Review Administratif" yang dapat direview.
    </div>

    <!-- Tabel Proposal Review Administratif -->
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>Daftar Proposal untuk Review Administratif
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
                                    $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', auth()->id())->first();
                                    $reviewStatus = ($adminReview && $adminReview->note_administratif) ? 'Selesai' : 'Pending';
                                    $statusClass = ($adminReview && $adminReview->note_administratif) ? 'bg-success' : 'bg-warning';
                                    $assignedDate = $proposal->updated_at;
                                @endphp
                                
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $proposal->judul_proposal }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $proposal->id_proposal }}</small>
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
                                        <span class="badge {{ $statusClass }}">{{ $reviewStatus }}</span>
                                        @if($adminReview && $adminReview->note_administratif)
                                            <br>
                                            <small class="text-muted">Catatan tersimpan</small>
                                        @endif
                                    </td>
                                    <td>
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
                    <i class="fas fa-clipboard-check fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Belum ada proposal untuk review administratif</h5>
                    <p class="text-gray-400">Proposal akan muncul di sini setelah operator menugaskan Anda sebagai reviewer administratif dan proposal sudah divalidasi</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Informasi Review Administratif -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Panduan Review Administratif
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Review administratif meliputi pemeriksaan kelengkapan dokumen dan kesesuaian format:</p>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Kelengkapan dokumen proposal</li>
                        <li><i class="fas fa-check text-success me-2"></i>Format dan struktur dokumen</li>
                        <li><i class="fas fa-check text-success me-2"></i>Kesesuaian dengan ketentuan PKM</li>
                        <li><i class="fas fa-check text-success me-2"></i>Kelengkapan data mahasiswa dan dosen</li>
                        <li><i class="fas fa-check text-success me-2"></i>Kesesuaian dengan skim yang dipilih</li>
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
                            $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', auth()->id())->first();
                            return $adminReview && $adminReview->note_administratif;
                        })->count();
                        $pendingReview = $totalAssigned - $completedReview;
                    @endphp
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-success">{{ $completedReview }}</h4>
                                <small class="text-muted">Selesai</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">{{ $pendingReview }}</h4>
                            <small class="text-muted">Pending</small>
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

    <!-- Kriteria Penilaian Administratif -->
    <div class="card card-custom mt-4">
        <div class="card-header card-header-custom">
            <h6 class="mb-0">
                <i class="fas fa-clipboard-list me-2"></i>Kriteria Review Administratif
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Kelengkapan Dokumen</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-circle text-primary me-2"></i>Proposal lengkap</li>
                        <li><i class="fas fa-circle text-primary me-2"></i>Data mahasiswa lengkap</li>
                        <li><i class="fas fa-circle text-primary me-2"></i>Data dosen lengkap</li>
                        <li><i class="fas fa-circle text-primary me-2"></i>Dokumen pendukung</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success">Kesesuaian Format</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-circle text-success me-2"></i>Struktur proposal</li>
                        <li><i class="fas fa-circle text-success me-2"></i>Format penulisan</li>
                        <li><i class="fas fa-circle text-success me-2"></i>Kesesuaian skim</li>
                        <li><i class="fas fa-circle text-success me-2"></i>Ketentuan PKM</li>
                    </ul>
                </div>
            </div>
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Catatan:</strong> Berikan catatan detail untuk setiap kesalahan administratif yang ditemukan. Review administratif harus selesai sebelum proposal dapat lanjut ke review substantif.
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Filter tahun
    document.getElementById('tahunFilter').addEventListener('change', function() {
        const tahun = this.value;
        window.location.href = `{{ route('reviewer.review.administratif') }}?tahun=${tahun}`;
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

