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
    
    <!-- Chart Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Grafik Analisis Proposal
                        </h5>
                        <div class="d-flex gap-2">
                            <select class="form-select form-select-sm" id="chartTypeSelector" style="width: auto;">
                                <option value="per_tahun">Jumlah Proposal per Tahun</option>
                                <option value="per_skim">Jumlah Proposal per Skim ({{ $tahun }})</option>
                                <option value="per_fakultas">Jumlah Proposal per Fakultas ({{ $tahun }})</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="proposalChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Proposals Ranking -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        Perangkingan 10 Proposal Terbaik ({{ $tahun }})
                    </h5>
                </div>
                <div class="card-body">
                    @if($topProposals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%">Ranking</th>
                                        <th width="40%">Judul Proposal</th>
                                        <th width="15%">Skim</th>
                                        <th width="20%">Mahasiswa</th>
                                        <th width="10%">Nilai</th>
                                        <th width="10%">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topProposals as $proposal)
                                    <tr>
                                        <td>
                                            @if($proposal['ranking'] <= 3)
                                                <span class="badge bg-{{ $proposal['ranking'] == 1 ? 'warning' : ($proposal['ranking'] == 2 ? 'secondary' : 'danger') }} fs-6">
                                                    {{ $proposal['ranking'] }}
                                                </span>
                                            @else
                                                <span class="badge bg-primary">{{ $proposal['ranking'] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ Str::limit($proposal['judul'], 60) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $proposal['skim'] }}</span>
                                        </td>
                                        <td>{{ $proposal['mahasiswa'] }}</td>
                                        <td>
                                            <span class="badge bg-success fs-6">{{ number_format($proposal['nilai'], 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $proposal['status'] == 'lolos' ? 'success' : 'danger' }}">
                                                {{ $proposal['status'] == 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Belum ada data perangkingan</h6>
                            <p class="text-muted">Data perangkingan akan muncul setelah ada proposal yang dinilai final.</p>
                        </div>
                    @endif
                </div>
            </div>
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
                            <thead>
                                <tr>
                                    <th class="bg-dark text-white">No</th>
                                    <th class="bg-dark text-white">Skim</th>
                                    <th class="bg-dark text-white">Jumlah</th>
                                    <th class="bg-success text-white">Sudah Valid</th>
                                    <th class="bg-warning text-white">Belum Valid</th>
                                    <th class="bg-danger text-white">Tolak Valid</th>
                                    <th class="bg-info text-white">Sedang Review</th>
                                    <th class="bg-primary text-white">Selesai Review</th>
                                    <th class="bg-dark text-white">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pkm8Bidang as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $data['skim'] }}</strong></td>
                                    <td class="text-dark fw-bold">{{ $data['jumlah'] }}</td>
                                    <td class="text-success fw-bold">{{ $data['sudah_valid'] }}</td>
                                    <td class="text-warning fw-bold">{{ $data['belum_valid'] }}</td>
                                    <td class="text-danger fw-bold">{{ $data['tolak_valid'] }}</td>
                                    <td class="text-info fw-bold">{{ $data['sedang_review'] }}</td>
                                    <td class="text-primary fw-bold">{{ $data['selesai_review'] }}</td>
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
                            <thead>
                                <tr>
                                    <th class="bg-dark text-white">No</th>
                                    <th class="bg-dark text-white">Skim</th>
                                    <th class="bg-dark text-white">Jumlah</th>
                                    <th class="bg-success text-white">Sudah Valid</th>
                                    <th class="bg-warning text-white">Belum Valid</th>
                                    <th class="bg-danger text-white">Tolak Valid</th>
                                    <th class="bg-info text-white">Sedang Review</th>
                                    <th class="bg-primary text-white">Selesai Review</th>
                                    <th class="bg-dark text-white">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pkmInsentif as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><strong>{{ $data['skim'] }}</strong></td>
                                    <td class="text-dark fw-bold">{{ $data['jumlah'] }}</td>
                                    <td class="text-success fw-bold">{{ $data['sudah_valid'] }}</td>
                                    <td class="text-warning fw-bold">{{ $data['belum_valid'] }}</td>
                                    <td class="text-danger fw-bold">{{ $data['tolak_valid'] }}</td>
                                    <td class="text-info fw-bold">{{ $data['sedang_review'] }}</td>
                                    <td class="text-primary fw-bold">{{ $data['selesai_review'] }}</td>
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

@section('styles')
<style>
/* Konsistensi font untuk tabel operator */
.table th {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    font-weight: 600;
    font-size: 0.875rem;
    text-align: center;
    border: 1px solid #dee2e6;
    padding: 0.75rem;
}

.table td {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    font-size: 0.875rem;
    vertical-align: middle;
    text-align: center;
    border: 1px solid #dee2e6;
    padding: 0.75rem;
}

/* Khusus untuk kolom Skim agar left-aligned */
.table td:nth-child(2) {
    text-align: left;
}

/* Khusus untuk kolom Aksi agar left-aligned */
.table td:last-child {
    text-align: left;
}

/* Memastikan warna text konsisten */
.text-success {
    color: #198754 !important;
}

.text-warning {
    color: #ffc107 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-info {
    color: #0dcaf0 !important;
}

.text-primary {
    color: #0d6efd !important;
}

.text-dark {
    color: #212529 !important;
}

/* Header tabel dengan warna berbeda */
.bg-success {
    background-color: #198754 !important;
}

.bg-warning {
    background-color: #ffc107 !important;
}

.bg-danger {
    background-color: #dc3545 !important;
}

.bg-info {
    background-color: #0dcaf0 !important;
}

.bg-primary {
    background-color: #0d6efd !important;
}

.bg-dark {
    background-color: #212529 !important;
}

/* Border untuk tabel */
.table {
    border-collapse: collapse;
    border: 1px solid #dee2e6;
}

.table th,
.table td {
    border-right: 1px solid #dee2e6;
}

.table th:last-child,
.table td:last-child {
    border-right: none;
}

/* Hover effect untuk row */
.table tbody tr:hover {
    background-color: #f8f9fa;
}
</style>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Year selector change
    document.getElementById('yearSelector').addEventListener('change', function() {
        const year = this.value;
        window.location.href = `{{ route('operator.dashboard') }}?tahun=${year}`;
    });
    
    // Chart data from Laravel
    const chartData = @json($chartData);
    
    // Initialize chart
    const ctx = document.getElementById('proposalChart').getContext('2d');
    let chart = null;
    
    // Chart configuration
    const chartConfig = {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Jumlah Proposal',
                data: [],
                backgroundColor: '#800000', // Maroon color
                borderColor: '#660000',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Jumlah Proposal per Tahun'
                }
            }
        }
    };
    
    // Function to update chart
    function updateChart(type) {
        let labels = [];
        let data = [];
        let title = '';
        
        switch(type) {
            case 'per_tahun':
                labels = chartData.proposal_per_tahun.map(item => item.tahun);
                data = chartData.proposal_per_tahun.map(item => item.jumlah);
                title = 'Jumlah Proposal per Tahun';
                break;
            case 'per_skim':
                labels = chartData.proposal_per_skim.map(item => item.skim);
                data = chartData.proposal_per_skim.map(item => item.jumlah);
                title = `Jumlah Proposal per Skim ({{ $tahun }})`;
                break;
            case 'per_fakultas':
                labels = chartData.proposal_per_fakultas.map(item => item.fakultas);
                data = chartData.proposal_per_fakultas.map(item => item.jumlah);
                title = `Jumlah Proposal per Fakultas ({{ $tahun }})`;
                break;
        }
        
        if (chart) {
            chart.destroy();
        }
        
        chartConfig.data.labels = labels;
        chartConfig.data.datasets[0].data = data;
        chartConfig.options.plugins.title.text = title;
        
        chart = new Chart(ctx, chartConfig);
    }
    
    // Chart type selector change
    document.getElementById('chartTypeSelector').addEventListener('change', function() {
        updateChart(this.value);
    });
    
    // Initialize with default chart (per tahun)
    updateChart('per_tahun');
});

function pilihReviewer(skim) {
    window.location.href = `{{ route('operator.pilih.reviewer') }}?tahun={{ $tahun }}&filter=${skim}`;
}
</script>
@endsection
