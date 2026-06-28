@extends('dosen.layout')

@section('page_title', 'Validasi Proposal')

@section('dosen_content')
<div class="row">
    <div class="col-12">
        <x-breadcrumb :items="[
            ['label' => 'Dashboard Dosen', 'url' => route('dosen.pendamping.dashboard')],
            ['label' => 'Validasi Proposal', 'active' => true],
        ]" />

        <div class="card card-custom">
            <div class="card-header card-header-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Validasi Proposal
                    </h5>
                    <!-- Filter Tahun Ajaran -->
                    <form method="GET" action="{{ route('dosen.pendamping.proposal.validasi') }}" class="d-inline">
                        <div class="input-group">
                            <label class="input-group-text" for="tahun_ajaran">
                                <i class="fas fa-calendar me-2"></i>Tahun Ajaran
                            </label>
                            <select name="tahun_ajaran" id="tahun_ajaran" class="form-select" onchange="this.form.submit()" style="min-width: 150px;">
                                @foreach($tahunAjaranList as $tahunAjaran)
                                    <option value="{{ $tahunAjaran }}" {{ $tahunAjaranTerpilih == $tahunAjaran ? 'selected' : '' }}>
                                        {{ $tahunAjaran }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if($proposals->count() > 0)
                    <div class="row">
                        @foreach($proposals as $proposal)
                            <div class="col-12 mb-3">
                                <div class="proposal-card">
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <div class="proposal-thumbnail">
                                                <div class="text-center d-flex align-items-center justify-content-center h-100">
                                                    <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <small class="text-muted">{{ Str::limit($proposal->judul_proposal, 30) }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1">
                                                        <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d F Y, H.i') }}
                                                    </p>
                                                    <p class="mb-1">
                                                        <strong>Dikirim Oleh:</strong> {{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}
                                                    </p>
                                                    <p class="mb-1">
                                                        <strong>Skim:</strong> {{ $proposal->skim }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1">
                                                        <strong>Prodi:</strong> {{ $proposal->mahasiswa->prodi->nama_prodi ?? 'N/A' }}
                                                    </p>
                                                    <p class="mb-1">
                                                        <strong>Fakultas:</strong> {{ $proposal->mahasiswa->fakultas->nama_fakultas ?? 'N/A' }}
                                                    </p>
                                                    <p class="mb-1">
                                                        <strong>Status:</strong> 
                                                        @switch($proposal->status_validasi)
                                                            @case('pending')
                                                                <span class="status-badge status-pending">
                                                                    <i class="fas fa-clock me-1"></i>
                                                                    Belum dilakukan Validasi
                                                                </span>
                                                                @break
                                                            @case('valid')
                                                                <span class="status-badge status-valid">
                                                                    <i class="fas fa-check-circle me-1"></i>
                                                                    Sudah Divalidasi
                                                                </span>
                                                                @break
                                                            @case('tidak_valid')
                                                                <span class="status-badge status-invalid">
                                                                    <i class="fas fa-times-circle me-1"></i>
                                                                    Ditolak
                                                                </span>
                                                                @break
                                                            @default
                                                                <span class="status-badge status-pending">
                                                                    <i class="fas fa-clock me-1"></i>
                                                                    Belum dilakukan Validasi
                                                                </span>
                                                        @endswitch
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <a href="{{ route('dosen.pendamping.proposal.detail', $proposal->id_proposal) }}" 
                                               class="btn btn-success">
                                                <i class="fas fa-eye me-2"></i>Validasi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada proposal yang perlu divalidasi</h5>
                        <p class="text-muted">Semua proposal mahasiswa Anda sudah divalidasi atau belum ada yang diajukan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('dosen_scripts')
<script>
    // Auto refresh setiap 30 detik untuk update status proposal
    setInterval(function() {
        location.reload();
    }, 30000);
</script>
@endsection
