@extends('mainlayout.app')

@section('title', 'Laporan SIMBELMAWA')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('pimpinan_pt.dashboard')],
        ['label' => 'Laporan SIMBELMAWA', 'active' => true],
    ]" />

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-file-alt me-2"></i>Laporan SIMBELMAWA
        </h4>
        <div class="col-md-6 text-end">
            <!-- No Create button for Pimpinan PT -->
        </div>
    </div>

    <!-- Alert / Toast -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card card-custom mb-4">
        <div class="card-header card-header-custom">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('pimpinan_pt.laporan.simbelmawa') }}" class="row g-3">
                <div class="col-md-4">
                    <select name="tahun_pelaksanaan" class="form-select">
                        <option value="">-- Semua Tahun --</option>
                        @foreach($tahuns as $t)
                            <option value="{{ $t }}" {{ request('tahun_pelaksanaan') == $t ? 'selected' : '' }}>
                                {{ $t }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
                    <a href="{{ route('pimpinan_pt.laporan.simbelmawa') }}" class="btn btn-outline-secondary">Reset</a>
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
                                @if(isset($report->id))
                                    <!-- Read-only view doesn't have edit/delete -->
                                @endif
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
