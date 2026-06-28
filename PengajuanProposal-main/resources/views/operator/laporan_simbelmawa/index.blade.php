@extends('mainlayout.app')

@section('title', 'Laporan SIMBELMAWA')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('operator.dashboard')],
        ['label' => 'Laporan SIMBELMAWA', 'active' => true],
    ]" />

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-file-alt me-2"></i>Laporan SIMBELMAWA
        </h4>
        <a href="{{ route('operator.laporan.simbelmawa.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Tambah Laporan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter -->
    <div class="card card-custom mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('operator.laporan.simbelmawa.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" class="form-control" value="{{ request('tahun_ajaran') }}" placeholder="Contoh: 2024/2025" maxlength="20">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('operator.laporan.simbelmawa.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Daftar Laporan SIMBELMAWA
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Tahun Ajaran</th>
                            <th>Proposal Tervalidasi</th>
                            <th>Dapat Pendanaan</th>
                            <th>Total Dana</th>
                            <th>Lolos PIMNAS</th>
                            <th>Jumlah Prestasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $index => $report)
                        <tr>
                            <td>{{ $reports->firstItem() + $index }}</td>
                            <td>{{ $report->tahun_ajaran }}</td>
                            <td>@formatId($report->jumlah_proposal_tervalidasi_pimpinan_pt)</td>
                            <td>@formatId($report->jumlah_proposal_dapat_pendanaan)</td>
                            <td>@rupiahId($report->total_dana_pendanaan)</td>
                            <td>@formatId($report->jumlah_proposal_lolos_pimnas)</td>
                            <td>@formatId($report->jumlah_prestasi)</td>
                            <td>
                                <a href="{{ route('operator.laporan.simbelmawa.edit', $report->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('operator.laporan.simbelmawa.destroy', $report->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus laporan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada data laporan SIMBELMAWA.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $reports->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
