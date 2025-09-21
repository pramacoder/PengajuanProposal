@extends('mainlayout.mainlayout')

@section('title', 'Detail Proposal untuk Validasi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-file-alt me-2"></i>Detail Proposal</h2>
                    <p class="text-muted">Validasi proposal yang ditugaskan kepada Anda</p>
                </div>
                <div>
                    <a href="{{ route('dosen.pendamping.dashboard') }}" class="btn btn-outline-secondary">
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
    @if($proposal->dokumens->count() > 0)
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
                                @foreach($proposal->dokumens as $index => $dokumen)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $dokumen->jenis_dokumen }}</td>
                                        <td>{{ $dokumen->nama_file }}</td>
                                        <td>{{ number_format($dokumen->ukuran_file / 1024, 2) }} KB</td>
                                        <td>{{ \Carbon\Carbon::parse($dokumen->created_at)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('mahasiswa.proposal.download', ['id' => $proposal->id, 'jenis' => $dokumen->jenis_dokumen]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download me-1"></i>Download
                                            </a>
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
    @endif

    <!-- Form Validasi -->
    @if($proposal->status_validasi == 'pending')
    <div class="row mb-4" id="validasi">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-check-circle me-2"></i>Validasi Proposal
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dosen.pendamping.proposal.validasi.submit', $proposal->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status_validasi" class="form-label required-field">Status Validasi</label>
                                <select class="form-control @error('status_validasi') is-invalid @enderror" 
                                        id="status_validasi" name="status_validasi" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="valid" {{ old('status_validasi') == 'valid' ? 'selected' : '' }}>Valid</option>
                                    <option value="tidak_valid" {{ old('status_validasi') == 'tidak_valid' ? 'selected' : '' }}>Tidak Valid</option>
                                </select>
                                @error('status_validasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="catatan" class="form-label">Catatan</label>
                                <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                          id="catatan" name="catatan" rows="4" 
                                          placeholder="Berikan catatan atau feedback untuk mahasiswa...">{{ old('catatan') }}</textarea>
                                @error('catatan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check me-1"></i>Submit Validasi
                                </button>
                                <a href="{{ route('dosen.pendamping.dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Batal
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- Hasil Validasi -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-check-circle me-2"></i>Hasil Validasi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                @switch($proposal->status_validasi)
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
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tanggal Validasi:</strong> 
                                {{ $proposal->tanggal_validasi ? \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d/m/Y H:i') : 'N/A' }}
                            </p>
                        </div>
                    </div>
                    @if($proposal->catatan_validasi)
                        <div class="row">
                            <div class="col-12">
                                <p><strong>Catatan:</strong></p>
                                <div class="alert alert-info">
                                    {{ $proposal->catatan_validasi }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Hasil Review -->
    @if($proposal->nilaiAdministratif || $proposal->nilaiSubstantif || $proposal->hasilFinal)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star me-2"></i>Hasil Review
                    </h5>
                </div>
                <div class="card-body">
                    @if($proposal->nilaiAdministratif)
                        <div class="mb-3">
                            <h6>Review Administratif</h6>
                            <p><strong>Nilai:</strong> {{ $proposal->nilaiAdministratif->nilai_administratif }}</p>
                            <p><strong>Komentar:</strong> {{ $proposal->nilaiAdministratif->komentar ?? 'Tidak ada komentar' }}</p>
                        </div>
                    @endif

                    @if($proposal->nilaiSubstantif)
                        <div class="mb-3">
                            <h6>Review Substantif</h6>
                            <p><strong>Nilai:</strong> {{ $proposal->nilaiSubstantif->nilai_substantif }}</p>
                            <p><strong>Komentar:</strong> {{ $proposal->nilaiSubstantif->komentar ?? 'Tidak ada komentar' }}</p>
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
</div>

<style>
.required-field::after {
    content: " *";
    color: #dc3545;
}
</style>
@endsection
