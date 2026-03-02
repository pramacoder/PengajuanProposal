@extends('mainlayout.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'active' => true],
    ]" />

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard Mahasiswa
            </h1>
            <p class="text-muted">Selamat datang, {{ auth()->user()->name }}</p>
        </div>
        
        <div class="d-flex align-items-center">
            @if($proposalForRevision && $ruangKontrol && $ruangKontrol->status_perbaikan === 'terbuka')
                <a href="{{ route('mahasiswa.revisi.index') }}" class="btn btn-warning me-2">
                    <i class="fas fa-edit me-1"></i>Revisi Proposal
                </a>
            @endif
            @if($ruangKontrol && $ruangKontrol->status_pendaftaran === 'terbuka')
                <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Ajukan Proposal
                </a>
            @endif
        </div>
    </div>

    <!-- Alert untuk proposal yang benar-benar masih memblokir pengajuan baru -->
    @if(isset($blockingProposal) && $blockingProposal)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fa-2x"></i>
                <div>
                    <h5 class="mb-1">Anda sudah terdaftar dalam proposal tahun {{ $blockingProposal->tahun_ajaran ?? \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru() }}: "{{ $blockingProposal->judul_proposal }}"</h5>
                    <p class="mb-0">Satu mahasiswa hanya dapat terdaftar dalam satu proposal PKM per tahun akademik.</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Statistik Proposal -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Proposal</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProposals }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Sedang Direview</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $underReview }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Menunggu Validasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $waitingValidation }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Disetujui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $approved }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Proposal -->
    @if($proposals->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-list me-2 text-white"></i>Daftar Proposal
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Skim</th>
                                    <th>Status</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proposals as $proposal)
                                <tr>
                                    <td>{{ $proposal->judul_proposal }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $proposal->skim }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch($proposal->status) {
                                                case 'draft':
                                                    $statusClass = 'bg-secondary';
                                                    $statusText = 'Draft';
                                                    break;
                                                case 'submitted':
                                                    $statusClass = 'bg-info';
                                                    $statusText = 'Submitted';
                                                    break;
                                                case 'validated':
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Validated';
                                                    break;
                                                case 'review_administratif':
                                                    $statusClass = 'bg-warning';
                                                    $statusText = 'Review Administratif';
                                                    break;
                                                case 'review_substantif':
                                                    $statusClass = 'bg-info';
                                                    $statusText = 'Review Substantif';
                                                    break;
                                                case 'review_completed':
                                                    $statusClass = 'bg-warning';
                                                    $statusText = 'Review Completed';
                                                    break;
                                                case 'revisi':
                                                    $statusClass = 'bg-danger';
                                                    $statusText = 'Revisi';
                                                    break;
                                                case 'finalized':
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Finalized';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-secondary';
                                                    $statusText = $proposal->status;
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($proposal->created_at)->format('d M Y H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('mahasiswa.proposal.show', $proposal->id_proposal) }}" 
                                               class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($proposal->status === 'revisi' && $ruangKontrol && $ruangKontrol->status_perbaikan === 'terbuka')
                                                <a href="{{ route('mahasiswa.revisi.index') }}" 
                                                   class="btn btn-sm btn-warning" title="Revisi Proposal">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-file-alt fa-4x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">Belum Ada Proposal</h5>
                    <p class="text-gray-500">Anda belum mengajukan proposal PKM. Klik tombol di atas untuk mengajukan proposal baru.</p>
                    @if($ruangKontrol && $ruangKontrol->status_pendaftaran === 'terbuka')
                        <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Ajukan Proposal Pertama
                        </a>
                    @else
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Pendaftaran proposal sedang ditutup. Silakan tunggu hingga periode pendaftaran dibuka.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
            },
            "pageLength": 10,
            "order": [[ 4, "desc" ]]
        });
    });
</script>
@endpush
