@extends('mainlayout.app')

@section('title', 'Dashboard Dosen Pendamping')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-user-check me-2"></i>Dashboard Dosen Pendamping</h2>
                    <p class="text-muted">Validasi proposal yang ditugaskan kepada Anda</p>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('dosen.pembimbing.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chalkboard-teacher me-1"></i>Pembimbing
                    </a>
                    <a href="{{ route('dosen.pendamping.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-user-check me-1"></i>Pendamping
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $totalProposal }}</h4>
                            <p class="card-text">Total Proposal</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $proposalPending }}</h4>
                            <p class="card-text">Pending</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $proposalValid }}</h4>
                            <p class="card-text">Valid</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $proposalTidakValid }}</h4>
                            <p class="card-text">Tidak Valid</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Proposal untuk Validasi -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Proposal untuk Validasi
                    </h5>
                </div>
                <div class="card-body">
                    @if($proposals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Mahasiswa</th>
                                        <th>NIM</th>
                                        <th>Judul Proposal</th>
                                        <th>Skim</th>
                                        <th>Status</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proposals as $index => $proposal)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ substr($proposal->mahasiswa->nama_mhs, 0, 1) }}
                                                    </div>
                                                    {{ $proposal->mahasiswa->nama_mhs }}
                                                </div>
                                            </td>
                                            <td>{{ $proposal->mahasiswa->nim }}</td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 250px;" title="{{ $proposal->judul }}">
                                                    {{ $proposal->judul }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $proposal->skim }}</span>
                                            </td>
                                            <td>
                                                @switch($proposal->status_validasi)
                                                    @case('pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                        @break
                                                    @case('valid')
                                                        <span class="badge bg-success">Valid</span>
                                                        @break
                                                    @case('tidak_valid')
                                                        <span class="badge bg-danger">Tidak Valid</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">Unknown</span>
                                                @endswitch
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('dosen.pendamping.proposal.detail', $proposal->id_proposal) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>Detail
                                                    </a>
                                                    @if($proposal->status_validasi == 'pending')
                                                        <a href="{{ route('dosen.pendamping.proposal.detail', $proposal->id_proposal) }}#validasi" 
                                                           class="btn btn-sm btn-warning">
                                                            <i class="fas fa-check me-1"></i>Validasi
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Proposal</h5>
                            <p class="text-muted">Proposal yang ditugaskan kepada Anda untuk divalidasi akan muncul di sini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 35px;
    height: 35px;
    font-size: 14px;
    font-weight: 600;
}
</style>
@endsection
