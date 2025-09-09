@extends('operator.layout')

@section('title', 'Beranda - Operator')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <x-page-header 
        title="BERANDA" 
        subtitle="UNIVERSITAS UDAYANA" />
    
    <!-- Year Selector -->
    <div class="row mb-4">
        <div class="col-md-3">
            <select class="form-select" id="yearSelector">
                <option value="2025" {{ $tahun == '2025' ? 'selected' : '' }}>2025</option>
                <option value="2024" {{ $tahun == '2024' ? 'selected' : '' }}>2024</option>
                <option value="2023" {{ $tahun == '2023' ? 'selected' : '' }}>2023</option>
            </select>
        </div>
    </div>
    
    <!-- Summary Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold mb-3">RINGKASAN</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-custom">
                        <div class="card-body text-center">
                            <h5 class="card-title text-primary">Jumlah Total</h5>
                            <h2 class="display-4 fw-bold text-primary">{{ number_format($totalKeseluruhan + $totalInsentif) }}</h2>
                            <p class="text-muted">Total Semua Proposal PKM</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom">
                        <div class="card-body text-center">
                            <h5 class="card-title text-success">Jumlah PKM-8 Bidang</h5>
                            <h2 class="display-4 fw-bold text-success">{{ number_format($totalKeseluruhan) }}</h2>
                            <p class="text-muted">Proposal yang Memerlukan Dana</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom">
                        <div class="card-body text-center">
                            <h5 class="card-title text-info">Jumlah PKM Insentif</h5>
                            <h2 class="display-4 fw-bold text-info">{{ number_format($totalInsentif) }}</h2>
                            <p class="text-muted">Proposal Insentif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- PKM-8 Bidang Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>
                        PKM-8 BIDANG
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Skim</th>
                                    <th>Jumlah</th>
                                    <th>Sudah Valid</th>
                                    <th>Belum Valid</th>
                                    <th>Tolak Valid</th>
                                    <th>Sedang Review</th>
                                    <th>Selesai Review</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pkm8Bidang as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $data['skim'] }}</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ $data['jumlah'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $data['sudah_valid'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $data['belum_valid'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $data['tolak_valid'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $data['sedang_review'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $data['selesai_review'] }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="pilihReviewer('{{ $data['skim'] }}')">
                                            <i class="fas fa-user-plus me-1"></i>
                                            Pilih Reviewer
                                        </button>
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
    
    <!-- PKM Insentif Section -->
    <div class="row mb-4">
        <div class="col-12">           
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-gift me-2"></i>
                        PKM INSENTIF
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Skim</th>
                                    <th>Jumlah</th>
                                    <th>Sudah Valid</th>
                                    <th>Belum Valid</th>
                                    <th>Tolak Valid</th>
                                    <th>Sedang Review</th>
                                    <th>Selesai Review</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pkmInsentif as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $data['skim'] }}</strong></td>
                                    <td>
                                        <span class="badge bg-primary">{{ $data['jumlah'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $data['sudah_valid'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $data['belum_valid'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger">{{ $data['tolak_valid'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $data['sedang_review'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $data['selesai_review'] }}</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="pilihReviewer('{{ $data['skim'] }}')">
                                            <i class="fas fa-user-plus me-1"></i>
                                            Pilih Reviewer
                                        </button>
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
</div>


@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Year selector change
    document.getElementById('yearSelector').addEventListener('change', function() {
        const year = this.value;
        window.location.href = `{{ route('operator.dashboard') }}?tahun=${year}`;
    });
});

function pilihReviewer(skim) {
    window.location.href = `{{ route('operator.pilih.reviewer') }}?tahun={{ $tahun }}&filter=${skim}`;
}
</script>
@endsection
