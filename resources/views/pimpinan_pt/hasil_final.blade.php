@extends('mainlayout.app')

@section('title', 'Penilaian Final - Pimpinan PT')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('pimpinan_pt.dashboard')],
        ['label' => 'Penilaian Final', 'active' => true],
    ]" />

    <!-- Header Section -->
    <x-page-header 
        variant="enterprise"
        badge="Penetapan Nasional"
        icon="fas fa-trophy"
        title="Penilaian Hasil Final" 
        subtitle="Evaluasi dan penetapan status kelolosan tingkat nasional (Simbelmawa) proposal PKM" />
    
    <!-- Filter and Search Section -->
    <div class="filter-card mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-sm-6 col-md-2">
                <label for="yearSelector" class="form-label small fw-semibold text-secondary mb-1">
                    <i class="fas fa-calendar-alt me-1 text-primary"></i> Tahun
                </label>
                <select class="form-select" id="yearSelector">
                    @php $currentYear = date('Y'); @endphp
                    @for($i = $currentYear; $i >= 2023; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2">
                <label for="filterSelector" class="form-label small fw-semibold text-secondary mb-1">
                    <i class="fas fa-filter me-1 text-primary"></i> Skim PKM
                </label>
                <select class="form-select" id="filterSelector">
                    <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Semua Skim</option>
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
            <div class="col-12 col-sm-6 col-md-3">
                <label for="statusRevisiSelector" class="form-label small fw-semibold text-secondary mb-1">
                    <i class="fas fa-edit me-1 text-primary"></i> Status Revisi
                </label>
                <select class="form-select" id="statusRevisiSelector">
                    <option value="all" {{ $statusRevisi == 'all' ? 'selected' : '' }}>Semua Status Revisi</option>
                    <option value="belum" {{ $statusRevisi == 'belum' ? 'selected' : '' }}>Belum Direvisi</option>
                    <option value="tidak_revisi" {{ $statusRevisi == 'tidak_revisi' ? 'selected' : '' }}>Tidak Melakukan Revisi</option>
                    <option value="sudah" {{ $statusRevisi == 'sudah' ? 'selected' : '' }}>Sudah Direvisi</option>
                    <option value="menunggu_review" {{ $statusRevisi == 'menunggu_review' ? 'selected' : '' }}>Menunggu Review / Validasi</option>
                    <option value="selesai" {{ $statusRevisi == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="col-12 col-sm-6 col-md-5">
                <label for="searchInput" class="form-label small fw-semibold text-secondary mb-1">
                    <i class="fas fa-search me-1 text-primary"></i> Pencarian
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Cari judul proposal...">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Results Table -->
    <div class="card card-custom shadow-sm border-0 mb-4">
        <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fas fa-trophy me-2 text-warning"></i>
                Daftar Penilaian Final
            </h5>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1">
                {{ count($proposals) }} Proposal
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-modern mb-0" id="resultsTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="min-width: 250px;">Judul Proposal</th>
                            <th style="width: 90px;">Skim</th>
                            <th style="min-width: 170px;">Ketua Pengusul</th>
                            <th style="min-width: 140px;">Status Revisi</th>
                            <th style="min-width: 130px;">Tanggal Revisi</th>
                            <th style="min-width: 150px;">Hasil Final</th>
                            <th style="width: 90px;" class="text-center">Nilai</th>
                            <th style="width: 120px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proposals as $index => $proposal)
                        <tr data-proposal-id="{{ $proposal->id_proposal }}">
                            <td class="text-muted fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <strong class="text-dark d-block mb-1">{{ $proposal->judul_proposal ?? 'Judul proposal..' }}</strong>
                                <small class="text-muted d-block">{{ Str::limit($proposal->judul_proposal, 60) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold">{{ $proposal->skim ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold flex-shrink-0" style="width: 30px; height: 30px; font-size: 12px; border: 1px solid rgba(13, 110, 253, 0.2);">
                                        {{ strtoupper(substr($proposal->mahasiswa->nama_mahasiswa ?? $proposal->mahasiswa->nama_mhs ?? 'M', 0, 1)) }}
                                    </div>
                                    <div class="text-truncate">
                                        <div class="fw-medium text-dark text-truncate">{{ $proposal->mahasiswa->nama_mahasiswa ?? $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $proposal->mahasiswa->nim ?? $proposal->mahasiswa->nim_mhs ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($proposal->status == 'revisi' || $proposal->status == 'revisi_akhir')
                                    @if($proposal->proposalRevisi->count() > 0)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="fas fa-check-circle me-1"></i>Sudah Direvisi
                                        </span>
                                        <small class="text-muted d-block mt-1">@formatId($proposal->proposalRevisi->count()) berkas</small>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                                            <i class="fas fa-clock me-1"></i>Belum Direvisi
                                        </span>
                                    @endif
                                @elseif($proposal->status == 'revisi_submitted' || $proposal->status == 'validasi_akhir_dosen_univ')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                                        <i class="fas fa-hourglass-half me-1"></i>Menunggu Validasi
                                    </span>
                                @elseif($proposal->status == 'pimpinan_pt')
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-2 py-1">
                                        <i class="fas fa-exclamation-circle me-1"></i>Siap Dinilai Final
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                        <i class="fas fa-check-double me-1"></i>Selesai
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($proposal->proposalRevisi->count() > 0)
                                    <small class="text-secondary fw-medium">
                                        {{ \Carbon\Carbon::parse($proposal->proposalRevisi->first()->tanggal_submit)->format('d M Y H:i') }}
                                    </small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                            <td>
                                @if($proposal->hasilFinal)
                                    <span class="badge bg-{{ $proposal->hasilFinal->status_final == 'lolos' ? 'success' : 'danger' }}-subtle text-{{ $proposal->hasilFinal->status_final == 'lolos' ? 'success' : 'danger' }} border border-{{ $proposal->hasilFinal->status_final == 'lolos' ? 'success' : 'danger' }}-subtle px-2 py-1">
                                        <i class="fas {{ $proposal->hasilFinal->status_final == 'lolos' ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                        {{ $proposal->hasilFinal->status_final == 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">
                                        <i class="fas fa-hourglass-half me-1"></i>Belum Dinilai
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($proposal->hasilFinal)
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-2 py-1">
                                        @formatId($proposal->hasilFinal->nilai, 2)
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if(!$proposal->hasilFinal)
                                    <a href="{{ route('pimpinan_pt.detail.hasil.final', $proposal->id_proposal) }}" 
                                       class="btn btn-sm btn-primary w-100" 
                                       title="Tentukan Hasil Final">
                                        <i class="fas fa-gavel me-1"></i>Nilai
                                    </a>
                                @else
                                    <a href="{{ route('pimpinan_pt.detail.hasil.final', $proposal->id_proposal) }}" 
                                       class="btn btn-sm btn-outline-primary w-100" 
                                       title="Lihat Hasil Final">
                                        <i class="fas fa-eye me-1"></i>Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state-box text-center py-4">
                                    <i class="fas fa-trophy fa-3x text-muted mb-3 opacity-50"></i>
                                    <h6 class="fw-bold text-dark">Tidak ada proposal yang ditemukan</h6>
                                    <p class="text-muted small mb-0">Tidak ada proposal yang sesuai dengan kriteria filter hasil final saat ini.</p>
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
        window.location.href = `{{ route('pimpinan_pt.hasil.final') }}?tahun=${year}&filter=${filter}`;
    });
    
    // Filter selector change
    document.getElementById('filterSelector').addEventListener('change', function() {
        const year = document.getElementById('yearSelector').value;
        const filter = this.value;
        const statusRevisi = document.getElementById('statusRevisiSelector').value;
        window.location.href = `{{ route('pimpinan_pt.hasil.final') }}?tahun=${year}&filter=${filter}&status_revisi=${statusRevisi}`;
    });
    
    // Status revisi selector change
    document.getElementById('statusRevisiSelector').addEventListener('change', function() {
        const year = document.getElementById('yearSelector').value;
        const filter = document.getElementById('filterSelector').value;
        const statusRevisi = this.value;
        window.location.href = `{{ route('pimpinan_pt.hasil.final') }}?tahun=${year}&filter=${filter}&status_revisi=${statusRevisi}`;
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
