@extends('mainlayout.mainlayout')

@section('title', 'Dashboard Dosen Pembimbing')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-chalkboard-teacher me-2"></i>Dashboard Dosen Pembimbing</h2>
                    <p class="text-muted">Monitor proposal mahasiswa bimbingan Anda</p>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('dosen.pembimbing.dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-chalkboard-teacher me-1"></i>Pembimbing
                    </a>
                    <a href="{{ route('dosen.pendamping.dashboard') }}" class="btn btn-outline-primary">
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
                            <h4 class="card-title">{{ $totalMahasiswa }}</h4>
                            <p class="card-text">Total Mahasiswa</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
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
            <div class="card bg-info text-white">
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
    </div>

    <!-- Tabel Mahasiswa Bimbingan -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Mahasiswa Bimbingan
                    </h5>
                </div>
                <div class="card-body">
                    @if($mahasiswaBimbingan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>NIM</th>
                                        <th>Prodi</th>
                                        <th>Status Proposal</th>
                                        <th>Judul Proposal</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($mahasiswaBimbingan as $index => $mahasiswa)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ substr($mahasiswa->nama_mhs, 0, 1) }}
                                                    </div>
                                                    {{ $mahasiswa->nama_mhs }}
                                                </div>
                                            </td>
                                            <td>{{ $mahasiswa->nim }}</td>
                                            <td>{{ $mahasiswa->prodi_mhs }}</td>
                                            <td>
                                                @if($mahasiswa->proposal)
                                                    @switch($mahasiswa->proposal->status_validasi)
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
                                                @else
                                                    <span class="badge bg-light text-dark">Belum Ada Proposal</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($mahasiswa->proposal)
                                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $mahasiswa->proposal->judul }}">
                                                        {{ $mahasiswa->proposal->judul }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($mahasiswa->proposal)
                                                    {{ \Carbon\Carbon::parse($mahasiswa->proposal->tanggal_pengajuan)->format('d/m/Y') }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($mahasiswa->proposal)
                                                    <a href="{{ route('dosen.pembimbing.proposal.detail', $mahasiswa->proposal->id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i>Detail
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Mahasiswa Bimbingan</h5>
                            <p class="text-muted">Mahasiswa yang mendaftar dengan Anda sebagai dosen pembimbing akan muncul di sini.</p>
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
