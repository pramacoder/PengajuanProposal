@extends('operator.layout')

@section('title', 'Dashboard Hasil Final - Pimpinan PT')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <x-page-header 
        title="HASIL FINAL" 
        subtitle="PIMPINAN PT - UNIVERSITAS UDAYANA" />
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Proposal</h6>
                            <h3 class="mb-0">{{ $proposals->count() }}</h3>
                        </div>
                        <div class="text-primary" style="font-size: 2.5rem;">
                            <i class="fas fa-file-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Belum Dinilai</h6>
                            <h3 class="mb-0 text-warning">{{ $proposalsBelumDinilai->count() }}</h3>
                        </div>
                        <div class="text-warning" style="font-size: 2.5rem;">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Sudah Dinilai</h6>
                            <h3 class="mb-0 text-success">{{ $proposalsSudahDinilai->count() }}</h3>
                        </div>
                        <div class="text-success" style="font-size: 2.5rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs untuk Kategorisasi -->
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <ul class="nav nav-tabs card-header-tabs" id="proposalTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="belum-dinilai-tab" data-bs-toggle="tab" 
                            data-bs-target="#belum-dinilai" type="button" role="tab">
                        <i class="fas fa-clock me-2"></i>Belum Dinilai 
                        <span class="badge bg-warning ms-2">{{ $proposalsBelumDinilai->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sudah-dinilai-tab" data-bs-toggle="tab" 
                            data-bs-target="#sudah-dinilai" type="button" role="tab">
                        <i class="fas fa-check-circle me-2"></i>Sudah Dinilai 
                        <span class="badge bg-success ms-2">{{ $proposalsSudahDinilai->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="proposalTabContent">
                <!-- Tab Belum Dinilai -->
                <div class="tab-pane fade show active" id="belum-dinilai" role="tabpanel">
                    @if($proposalsBelumDinilai->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Judul Proposal</th>
                                    <th width="10%">Skim</th>
                                    <th width="15%">Mahasiswa</th>
                                    <th width="15%">Hasil Semi Final</th>
                                    <th width="10%">Tanggal</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proposalsBelumDinilai as $index => $proposal)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $proposal->judul_proposal }}</strong>
                                        <br><small class="text-muted">{{ Str::limit($proposal->judul_proposal, 60) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $proposal->skim }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $proposal->mahasiswa->nama_mahasiswa ?? 'N/A' }}</strong>
                                        <br><small class="text-muted">{{ $proposal->ketua_nim ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @if($proposal->hasilSemiFinal)
                                            <span class="badge bg-{{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'success' : 'danger' }}">
                                                {{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'Lolos' : 'Tidak Lolos' }}
                                            </span>
                                            @if($proposal->hasilSemiFinal->nilai)
                                                <br><small class="text-muted">Nilai: {{ number_format($proposal->hasilSemiFinal->nilai, 2) }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d M Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <a href="{{ route('pimpinan_pt.detail.hasil.final', $proposal->id_proposal) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="Tentukan Hasil Final">
                                            <i class="fas fa-gavel me-1"></i>Penilaian Final
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
                        Tidak ada proposal yang perlu dinilai final saat ini.
                    </div>
                    @endif
                </div>

                <!-- Tab Sudah Dinilai -->
                <div class="tab-pane fade" id="sudah-dinilai" role="tabpanel">
                    @if($proposalsSudahDinilai->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Judul Proposal</th>
                                    <th width="10%">Skim</th>
                                    <th width="15%">Mahasiswa</th>
                                    <th width="12%">Status PIMNAS</th>
                                    <th width="12%">Status Pendanaan</th>
                                    <th width="8%">Nilai</th>
                                    <th width="13%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proposalsSudahDinilai as $index => $proposal)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $proposal->judul_proposal }}</strong>
                                        <br><small class="text-muted">{{ Str::limit($proposal->judul_proposal, 50) }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $proposal->skim }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $proposal->mahasiswa->nama_mahasiswa ?? 'N/A' }}</strong>
                                        <br><small class="text-muted">{{ $proposal->ketua_nim ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @if($proposal->hasilFinal)
                                            <span class="badge bg-{{ $proposal->hasilFinal->status_pimnas == 'lolos' ? 'success' : 'danger' }}">
                                                {{ $proposal->hasilFinal->status_pimnas == 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($proposal->hasilFinal)
                                            <span class="badge bg-{{ $proposal->hasilFinal->status_pendanaan == 'lolos' ? 'success' : 'danger' }}">
                                                {{ $proposal->hasilFinal->status_pendanaan == 'lolos' ? 'Lolos' : 'Tidak Lolos' }}
                                            </span>
                                            @if($proposal->hasilFinal->status_pendanaan == 'lolos' && $proposal->hasilFinal->dana_yang_didapatkan)
                                                <br><small class="text-muted">Rp {{ number_format($proposal->hasilFinal->dana_yang_didapatkan, 0, ',', '.') }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($proposal->hasilFinal && $proposal->hasilFinal->nilai)
                                            <span class="badge bg-primary fs-6">{{ number_format($proposal->hasilFinal->nilai, 2) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('pimpinan_pt.detail.hasil.final', $proposal->id_proposal) }}" 
                                           class="btn btn-sm btn-success" 
                                           title="Lihat Hasil Final">
                                            <i class="fas fa-eye me-1"></i>Lihat Hasil
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
                        Belum ada proposal yang sudah dinilai final.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-refresh jika ada perubahan
    // Bisa ditambahkan polling atau websocket jika diperlukan
</script>
@endsection

