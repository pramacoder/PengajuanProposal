@extends('mainlayout.app')

@section('title', 'Beranda - Operator')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'active' => true],
    ]" />

    {{-- Welcome Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1" style="color: var(--text-900);">
                Selamat datang, <span style="color: var(--primary-700);">{{ \App\Helpers\UserHelper::getCurrentUserName() }}</span> 👋
            </h1>
            <p class="mb-0" style="color: var(--text-600); font-size: 0.9rem;">Dashboard Operator PKM — Kelola proposal dan reviewer</p>
        </div>
    </div>

    <!-- Tahun Ajaran Selector -->
    <div class="row mb-4">
        <div class="col-md-3">
            <label for="tahunAjaranSelector" class="form-label">Tahun Ajaran</label>
            <select class="form-select" id="tahunAjaranSelector" name="tahun_ajaran">
                @foreach($tahunAjaranList as $tahunAjaran)
                    <option value="{{ $tahunAjaran }}" {{ $tahunAjaranTerpilih == $tahunAjaran ? 'selected' : '' }}>
                        {{ $tahunAjaran }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    
    <!-- Top Proposals Ranking -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>
                        Perangkingan 10 Proposal Terbaik ({{ $tahunAjaranTerpilih }})
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
                                            <span class="badge bg-success fs-6">@formatId($proposal['nilai'], 2)</span>
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
    
    {{-- Summary Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon maroon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">Total Semua Proposal</div>
                    <div class="stat-value">@formatId($totalKeseluruhan + $totalInsentif)</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon teal">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">PKM-8 Bidang</div>
                    <div class="stat-value">@formatId($totalKeseluruhan)</div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon purple">
                    <i class="fas fa-gift"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-label">PKM Insentif</div>
                    <div class="stat-value">@formatId($totalInsentif)</div>
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
                                    <td class="text-dark fw-bold">@formatId($data['jumlah'])</td>
                                    <td class="text-success fw-bold">@formatId($data['sudah_valid'])</td>
                                    <td class="text-warning fw-bold">@formatId($data['belum_valid'])</td>
                                    <td class="text-danger fw-bold">@formatId($data['tolak_valid'])</td>
                                    <td class="text-info fw-bold">@formatId($data['sedang_review'])</td>
                                    <td class="text-primary fw-bold">@formatId($data['selesai_review'])</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="pilihReviewer('{{ $data['skim'] }}')">
                                            <i class="fas fa-user-plus me-1"></i>
                                            Pilih Reviewer
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                <!-- Total Row -->
                                <tr class="table-dark fw-bold">
                                    <td colspan="2" class="text-white text-center"><strong>TOTAL</strong></td>
                                    <td class="text-white text-center">@formatId($pkm8Bidang->sum('jumlah'))</td>
                                    <td class="text-white text-center">@formatId($pkm8Bidang->sum('sudah_valid'))</td>
                                    <td class="text-white text-center">@formatId($pkm8Bidang->sum('belum_valid'))</td>
                                    <td class="text-white text-center">@formatId($pkm8Bidang->sum('tolak_valid'))</td>
                                    <td class="text-white text-center">@formatId($pkm8Bidang->sum('sedang_review'))</td>
                                    <td class="text-white text-center">@formatId($pkm8Bidang->sum('selesai_review'))</td>
                                    <td></td>
                                </tr>
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
                                    <td class="text-dark fw-bold">@formatId($data['jumlah'])</td>
                                    <td class="text-success fw-bold">@formatId($data['sudah_valid'])</td>
                                    <td class="text-warning fw-bold">@formatId($data['belum_valid'])</td>
                                    <td class="text-danger fw-bold">@formatId($data['tolak_valid'])</td>
                                    <td class="text-info fw-bold">@formatId($data['sedang_review'])</td>
                                    <td class="text-primary fw-bold">@formatId($data['selesai_review'])</td>
                                    <td>
                                        <button class="btn btn-primary btn-sm" onclick="pilihReviewer('{{ $data['skim'] }}')">
                                            <i class="fas fa-user-plus me-1"></i>
                                            Pilih Reviewer
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                                <!-- Total Row -->
                                <tr class="table-dark fw-bold">
                                    <td colspan="2" class="text-white text-center"><strong>TOTAL</strong></td>
                                    <td class="text-white text-center">@formatId($pkmInsentif->sum('jumlah'))</td>
                                    <td class="text-white text-center">@formatId($pkmInsentif->sum('sudah_valid'))</td>
                                    <td class="text-white text-center">@formatId($pkmInsentif->sum('belum_valid'))</td>
                                    <td class="text-white text-center">@formatId($pkmInsentif->sum('tolak_valid'))</td>
                                    <td class="text-white text-center">@formatId($pkmInsentif->sum('sedang_review'))</td>
                                    <td class="text-white text-center">@formatId($pkmInsentif->sum('selesai_review'))</td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
                                <option value="per_skim">Jumlah Proposal per Skim ({{ $tahunAjaranTerpilih }})</option>
                                <option value="per_fakultas">Jumlah Proposal per Fakultas ({{ $tahunAjaranTerpilih }})</option>
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
    
    <!-- Filter Proposal Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Filter & Tampilkan Proposal
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('operator.dashboard') }}" id="filterProposalForm">
                        <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaranTerpilih }}">
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Fakultas</label>
                                <select name="filter_fakultas" id="filterFakultasProposal" class="form-select">
                                    <option value="">Semua Fakultas</option>
                                    @foreach($fakultas as $f)
                                        <option value="{{ $f->id_fakultas }}" {{ request('filter_fakultas') == $f->id_fakultas ? 'selected' : '' }}>
                                            {{ $f->nama_fakultas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Prodi</label>
                                <select name="filter_prodi" id="filterProdiProposal" class="form-select">
                                    <option value="">Semua Prodi</option>
                                    @foreach($prodis as $p)
                                        <option value="{{ $p->id_prodi }}" {{ request('filter_prodi') == $p->id_prodi ? 'selected' : '' }}>
                                            {{ $p->nama_prodi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Skim</label>
                                <select name="filter_skim" id="filterSkim" class="form-select">
                                    <option value="">Semua Skim</option>
                                    @foreach($skims as $skim)
                                        <option value="{{ $skim }}" {{ request('filter_skim') == $skim ? 'selected' : '' }}>
                                            {{ $skim }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Status Lolos</label>
                                <select name="filter_status" id="filterStatus" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="lolos" {{ request('filter_status') == 'lolos' ? 'selected' : '' }}>Lolos</option>
                                    <option value="tidak_lolos" {{ request('filter_status') == 'tidak_lolos' ? 'selected' : '' }}>Tidak Lolos</option>
                                    <option value="belum_final" {{ request('filter_status') == 'belum_final' ? 'selected' : '' }}>Belum Final</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Terapkan Filter
                                </button>
                                <a href="{{ route('operator.dashboard', ['tahun_ajaran' => $tahunAjaranTerpilih]) }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Proposal Table -->
                    @if(request()->has('filter_fakultas') || request()->has('filter_prodi') || request()->has('filter_skim') || request()->has('filter_status'))
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0">
                                    <i class="fas fa-list me-2"></i>
                                    Hasil Filter (@formatId($filteredProposals->count()) proposal ditemukan)
                                </h6>
                                @if($filteredProposals->count() > 20 && !request()->has('show_all'))
                                    <form method="GET" action="{{ route('operator.dashboard') }}" style="display: inline;">
                                        <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaranTerpilih }}">
                                        @if(request()->has('filter_fakultas'))
                                            <input type="hidden" name="filter_fakultas" value="{{ request('filter_fakultas') }}">
                                        @endif
                                        @if(request()->has('filter_prodi'))
                                            <input type="hidden" name="filter_prodi" value="{{ request('filter_prodi') }}">
                                        @endif
                                        @if(request()->has('filter_skim'))
                                            <input type="hidden" name="filter_skim" value="{{ request('filter_skim') }}">
                                        @endif
                                        @if(request()->has('filter_status'))
                                            <input type="hidden" name="filter_status" value="{{ request('filter_status') }}">
                                        @endif
                                        <input type="hidden" name="show_all" value="1">
                                        <button type="submit" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-list-ul me-1"></i> Tampilkan Semua
                                        </button>
                                    </form>
                                @endif
                            </div>
                            
                            @if($filteredProposals->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped proposal-filter-table">
                                        <thead class="table-dark">
                                            <tr>
                                                <th class="text-white">No</th>
                                                <th class="text-white">Judul Proposal</th>
                                                <th class="text-white">Skim</th>
                                                <th class="text-white">Ketua Tim</th>
                                                <th class="text-white">Prodi</th>
                                                <th class="text-white">Fakultas</th>
                                                <th class="text-white">Status Validasi</th>
                                                <th class="text-white">Status Final</th>
                                                <th class="text-white">Nilai</th>
                                                <th class="text-white">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($filteredProposals as $index => $proposal)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ Str::limit($proposal->judul ?? $proposal->judul, 50) }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge" style="background-color: #800000; color: white;">{{ $proposal->skim }}</span>
                                                </td>
                                                <td>{{ $proposal->mahasiswa->nama_mhs ?? '-' }}</td>
                                                <td>{{ $proposal->mahasiswa->prodi_mhs ?? '-' }}</td>
                                                <td>{{ $proposal->mahasiswa->fakultas_mhs ?? '-' }}</td>
                                                <td>
                                                    @if($proposal->status_validasi === 'valid')
                                                        <span class="badge bg-success">Valid</span>
                                                    @elseif($proposal->status_validasi === 'tidak_valid')
                                                        <span class="badge bg-danger">Tidak Valid</span>
                                                    @else
                                                        <span class="badge bg-warning">Belum Valid</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($proposal->hasilFinal)
                                                        @if($proposal->hasilFinal->status_final === 'lolos')
                                                            <span class="badge bg-success">Lolos</span>
                                                        @else
                                                            <span class="badge bg-danger">Tidak Lolos</span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">Belum Final</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($proposal->hasilFinal)
                                                        <span class="badge" style="background-color: #800000; color: white;">@formatId($proposal->hasilFinal->nilai, 2)</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('operator.proposal.detail', $proposal->id_proposal) }}" class="btn btn-sm" style="background-color: #800000; color: white; border: none;">
                                                        <i class="fas fa-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Tidak ada proposal yang sesuai dengan filter yang dipilih.
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-light text-center">
                            <i class="fas fa-info-circle me-2"></i>
                            Pilih filter untuk menampilkan proposal
                        </div>
                    @endif
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

/* Styling untuk proposal filter table - Konsisten maroon, hitam, putih */
.proposal-filter-table {
    border-collapse: collapse;
    border: 1px solid #212529;
}

.proposal-filter-table thead th {
    background-color: #212529 !important;
    color: white !important;
    font-weight: 600;
    text-align: center;
    padding: 0.75rem;
    border: 1px solid #212529;
}

.proposal-filter-table tbody td {
    text-align: center;
    vertical-align: middle;
    padding: 0.75rem;
    border: 1px solid #dee2e6;
}

.proposal-filter-table tbody tr:nth-child(even) {
    background-color: #f8f9fa;
}

.proposal-filter-table tbody tr:hover {
    background-color: #fff5f5;
}

.proposal-filter-table tbody td:nth-child(2) {
    text-align: left;
}

.proposal-filter-table .badge {
    padding: 0.375rem 0.75rem;
    font-weight: 500;
}

/* Button maroon untuk aksi */
.proposal-filter-table .btn {
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
    transition: background-color 0.2s;
}

.proposal-filter-table .btn:hover {
    background-color: #660000 !important;
    color: white !important;
}
</style>
@endsection

@section('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tahun ajaran selector change
    const tahunAjaranSelector = document.getElementById('tahunAjaranSelector');
    if (tahunAjaranSelector) {
        tahunAjaranSelector.addEventListener('change', function() {
            const tahunAjaran = this.value;
            const url = new URL(window.location.href);
            url.searchParams.set('tahun_ajaran', tahunAjaran);
            window.location.href = url.toString();
        });
    }
    
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
                title = `Jumlah Proposal per Skim ({{ $tahunAjaranTerpilih }})`;
                break;
            case 'per_fakultas':
                labels = chartData.proposal_per_fakultas.map(item => item.fakultas);
                data = chartData.proposal_per_fakultas.map(item => item.jumlah);
                title = `Jumlah Proposal per Fakultas ({{ $tahunAjaranTerpilih }})`;
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
    window.location.href = `{{ route('operator.pilih.reviewer') }}?tahun_ajaran={{ $tahunAjaranTerpilih }}&filter=${skim}`;
}

// Filter Fakultas-Prodi dependency
const filterFakultasProposal = document.getElementById('filterFakultasProposal');
const filterProdiProposal = document.getElementById('filterProdiProposal');

if (filterFakultasProposal && filterProdiProposal) {
    async function loadFilterProdiByFakultas(fakultasId) {
        filterProdiProposal.innerHTML = '<option value="">Semua Prodi</option>';
        if (!fakultasId) return;

        try {
            const response = await fetch(`/get-prodi/${fakultasId}`);
            if (!response.ok) throw new Error('Gagal mengambil data prodi');
            const data = await response.json();

            data.forEach(function (item) {
                const option = document.createElement('option');
                option.value = item.id_prodi;
                option.textContent = item.nama_prodi;
                // Maintain selected value if exists
                if (item.id_prodi == '{{ request('filter_prodi') }}') {
                    option.selected = true;
                }
                filterProdiProposal.appendChild(option);
            });
        } catch (e) {
            console.error(e);
        }
    }

    filterFakultasProposal.addEventListener('change', function () {
        loadFilterProdiByFakultas(this.value);
    });

    // Load prodi on page load if fakultas is selected
    if (filterFakultasProposal.value) {
        loadFilterProdiByFakultas(filterFakultasProposal.value);
    }
}
</script>
@endsection
