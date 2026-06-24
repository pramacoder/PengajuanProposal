@extends('mainlayout.app')

@section('title', 'Hasil Final - Operator')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('operator.dashboard')],
        ['label' => 'Hasil Final', 'active' => true],
    ]" />

    <!-- Header Section -->
    <x-page-header 
        title="HASIL FINAL" 
        subtitle="UNIVERSITAS UDAYANA" />
    
    <!-- Filter and Search Section -->
    <div class="row mb-4">
        <div class="col-md-2">
            <select class="form-select" id="yearSelector">
                @php $currentYear = date('Y'); @endphp
                @for($i = $currentYear; $i >= 2023; $i--)
                    <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select" id="filterSelector">
                <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Filter by</option>
                <option value="RE" {{ $filter == 'RE' ? 'selected' : '' }}>RE - Riset Eksakta</option>
                <option value="RSH" {{ $filter == 'RSH' ? 'selected' : '' }}>RSH - Riset Sosial Humaniora</option>
                <option value="KC" {{ $filter == 'KC' ? 'selected' : '' }}>KC - Kewirausahaan</option>
                <option value="PM" {{ $filter == 'PM' ? 'selected' : '' }}>PM - Pengabdian Masyarakat</option>
                <option value="PI" {{ $filter == 'PI' ? 'selected' : '' }}>PI - Penerapan IPTEK</option>
                <option value="K" {{ $filter == 'K' ? 'selected' : '' }}>K - Karsa Cipta</option>
                <option value="KI" {{ $filter == 'KI' ? 'selected' : '' }}>KI - Karya Inovatif</option>
                <option value="VGK" {{ $filter == 'VGK' ? 'selected' : '' }}>VGK - Video Gagasan Konkret</option>
                <option value="AI" {{ $filter == 'AI' ? 'selected' : '' }}>AI - Artikel Ilmiah</option>
                <option value="GFT" {{ $filter == 'GFT' ? 'selected' : '' }}>GFT - Gagasan Futuristik Tertulis</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select" id="statusRevisiSelector">
                <option value="all" {{ $statusRevisi == 'all' ? 'selected' : '' }}>Status Revisi</option>
                <option value="belum" {{ $statusRevisi == 'belum' ? 'selected' : '' }}>Belum Direvisi</option>
                <option value="tidak_revisi" {{ $statusRevisi == 'tidak_revisi' ? 'selected' : '' }}>Tidak Melakukan Revisi</option>
                <option value="sudah" {{ $statusRevisi == 'sudah' ? 'selected' : '' }}>Sudah Direvisi</option>
                <option value="menunggu_review" {{ $statusRevisi == 'menunggu_review' ? 'selected' : '' }}>Menunggu Review</option>
                <option value="selesai" {{ $statusRevisi == 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" class="form-control" id="searchInput" placeholder="Cari judul proposal">
            </div>
        </div>
    </div>
    
    <!-- Results Table -->
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">
                <i class="fas fa-trophy me-2"></i>
                Hasil Final
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="resultsTable">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Skim</th>
                            <th>Mahasiswa</th>
                            <th>Status Revisi</th>
                            <th>Tanggal Revisi</th>
                            <th>Hasil Final</th>
                            <th>Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proposals as $index => $proposal)
                        <tr data-proposal-id="{{ $proposal->id_proposal }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $proposal->judul_proposal ?? 'Judul proposal..' }}</strong>
                                <br><small class="text-muted">{{ Str::limit($proposal->judul_proposal, 50) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $proposal->skim ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <strong>{{ $proposal->mahasiswa->nama_mahasiswa ?? 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @if($proposal->status == 'revisi')
                                    @if($proposal->proposalRevisi->count() > 0)
                                        <span class="badge bg-success">Sudah Direvisi</span>
                                        <br><small class="text-muted">@formatId($proposal->proposalRevisi->count()) file</small>
                                    @else
                                        <span class="badge bg-warning">Belum Direvisi</span>
                                    @endif
                                @elseif($proposal->status == 'revisi_submitted')
                                    <span class="badge bg-info">Menunggu Review</span>
                                @else
                                    <span class="badge bg-secondary">Selesai</span>
                                @endif
                            </td>
                            <td>
                                @if($proposal->proposalRevisi->count() > 0)
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($proposal->proposalRevisi->first()->tanggal_submit)->format('d M Y H:i') }}
                                    </small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                @if($proposal->hasilFinal)
                                    <span class="badge bg-{{ $proposal->hasilFinal->status_final == 'lolos' ? 'success' : 'danger' }}">
                                        {{ $proposal->hasilFinal->status_final == 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Belum Dinilai</span>
                                @endif
                            </td>
                            <td>
                                @if($proposal->hasilFinal)
                                    <span class="badge bg-primary fs-6">@formatId($proposal->hasilFinal->nilai, 2)</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if(!$proposal->hasilFinal)
                                    <a href="{{ route('operator.detail.hasil.final', $proposal->id_proposal) }}" 
                                       class="btn btn-sm btn-primary" 
                                       title="Tentukan Hasil Final">
                                        <i class="fas fa-gavel me-1"></i>Hasil Final
                                    </a>
                                @else
                                    <a href="{{ route('operator.detail.hasil.final', $proposal->id_proposal) }}" 
                                       class="btn btn-sm btn-success" 
                                       title="Lihat Hasil Final">
                                        <i class="fas fa-eye me-1"></i>Lihat Hasil
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>Tidak ada proposal yang perlu dinilai final</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
        const filter = document.getElementById('filterSelector').value;
        window.location.href = `{{ route('operator.hasil.final') }}?tahun=${year}&filter=${filter}`;
    });
    
    // Filter selector change
    document.getElementById('filterSelector').addEventListener('change', function() {
        const year = document.getElementById('yearSelector').value;
        const filter = this.value;
        const statusRevisi = document.getElementById('statusRevisiSelector').value;
        window.location.href = `{{ route('operator.hasil.final') }}?tahun=${year}&filter=${filter}&status_revisi=${statusRevisi}`;
    });
    
    // Status revisi selector change
    document.getElementById('statusRevisiSelector').addEventListener('change', function() {
        const year = document.getElementById('yearSelector').value;
        const filter = document.getElementById('filterSelector').value;
        const statusRevisi = this.value;
        window.location.href = `{{ route('operator.hasil.final') }}?tahun=${year}&filter=${filter}&status_revisi=${statusRevisi}`;
    });
    
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#resultsTable tbody tr');
        
        rows.forEach(row => {
            const title = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            
            if (title.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    
});
</script>

@endsection
