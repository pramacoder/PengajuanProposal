@extends('mainlayout.app')

@section('title', 'Detail Proposal Mahasiswa')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-file-alt me-2"></i>Detail Proposal</h2>
                    <p class="text-muted">Informasi lengkap proposal mahasiswa bimbingan</p>
                </div>
                <div>
                    <a href="{{ route('dosen.pembimbing.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Mahasiswa -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Informasi Mahasiswa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nama:</strong> {{ $proposal->mahasiswa->nama_mhs }}</p>
                            <p><strong>NIM:</strong> {{ $proposal->mahasiswa->nim }}</p>
                            <p><strong>Prodi:</strong> {{ $proposal->mahasiswa->prodi_mhs }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Fakultas:</strong> {{ $proposal->mahasiswa->fakultas_mhs }}</p>
                            <p><strong>Email:</strong> {{ $proposal->mahasiswa->email_mhs }}</p>
                            <p><strong>No. HP:</strong> {{ $proposal->mahasiswa->no_hp_mhs }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Proposal -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i>Informasi Proposal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Judul:</strong> {{ $proposal->judul }}</p>
                            <p><strong>Skim:</strong> <span class="badge bg-info">{{ $proposal->skim }}</span></p>
                            <p><strong>Dana Diajukan:</strong> Rp {{ number_format($proposal->dana_diajukan, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tanggal Pengajuan:</strong> {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d/m/Y H:i') }}</p>
                            <p><strong>Status:</strong> 
                                @switch($proposal->status_validasi)
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
                            </p>
                            <p><strong>Dosen Pendamping:</strong> {{ $proposal->dosen_pembimbing ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dokumen Proposal -->
    @if($proposal->dokumen)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-pdf me-2"></i>Dokumen Proposal
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis Dokumen</th>
                                    <th>Nama File</th>
                                    <th>Ukuran</th>
                                    <th>Tanggal Upload</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($proposal->dokumen)
                                    <tr>
                                        <td>1</td>
                                        <td>Proposal</td>
                                        <td>{{ $proposal->dokumen->file_proposal ?: 'proposal.pdf' }}</td>
                                        <td>-</td>
                                        <td>{{ \Carbon\Carbon::parse($proposal->dokumen->created_at)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('mahasiswa.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
                                        </td>
                                    </tr>
                                    @if($proposal->dokumen->file_lampiran)
                                        <tr>
                                            <td>2</td>
                                            <td>Lampiran</td>
                                            <td>{{ $proposal->dokumen->file_lampiran }}</td>
                                            <td>-</td>
                                            <td>{{ \Carbon\Carbon::parse($proposal->dokumen->created_at)->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('mahasiswa.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'lampiran']) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download me-1"></i>Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Tidak ada dokumen</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Hasil Review -->
    @if($proposal->nilaiAdministratif->count() > 0 || $proposal->nilaiSubstantif->count() > 0 || $proposal->hasilFinal)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star me-2"></i>Hasil Review
                    </h5>
                </div>
                <div class="card-body">
                    @if($proposal->nilaiAdministratif->count() > 0)
                        <div class="mb-3">
                            <h6>Review Administratif</h6>
                            @foreach($proposal->nilaiAdministratif as $nilai)
                                <p><strong>Nilai:</strong> {{ $nilai->nilai_administratif }}</p>
                                <p><strong>Komentar:</strong> {{ $nilai->komentar ?? 'Tidak ada komentar' }}</p>
                                @if(!$loop->last)<hr>@endif
                            @endforeach
                        </div>
                    @endif

                    @if($proposal->nilaiSubstantif->count() > 0)
                        <div class="mb-3">
                            <h6>Review Substantif</h6>
                            @foreach($proposal->nilaiSubstantif as $nilai)
                                <p><strong>Nilai:</strong> {{ $nilai->nilai_substantif }}</p>
                                <p><strong>Komentar:</strong> {{ $nilai->komentar ?? 'Tidak ada komentar' }}</p>
                                @if(!$loop->last)<hr>@endif
                            @endforeach
                        </div>
                    @endif

                    @if($proposal->hasilFinal)
                        <div class="mb-3">
                            <h6>Hasil Final</h6>
                            <p><strong>Status:</strong> 
                                @if($proposal->hasilFinal->status_final == 'diterima')
                                    <span class="badge bg-success">Diterima</span>
                                @elseif($proposal->hasilFinal->status_final == 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($proposal->hasilFinal->status_final) }}</span>
                                @endif
                            </p>
                            <p><strong>Komentar Final:</strong> {{ $proposal->hasilFinal->catatan_final ?? 'Tidak ada komentar' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Catatan Validasi -->
    @if($proposal->catatan_validasi)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-comment me-2"></i>Catatan Validasi
                    </h5>
                </div>
                <div class="card-body">
                    <p>{{ $proposal->catatan_validasi }}</p>
                    @if($proposal->tanggal_validasi)
                        <small class="text-muted">Divalidasi pada: {{ \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d/m/Y H:i') }}</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
