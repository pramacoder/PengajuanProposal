@extends('dosen.layout')

@section('page_title', 'Dashboard Dosen Pendamping Universitas')

@section('dosen_content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header card-header-custom">
                <h5 class="mb-0">
                    <i class="fas fa-user-graduate me-2"></i>
                    Dashboard Dosen Pendamping Universitas
                </h5>
            </div>
            <div class="card-body">
                <!-- Section Deskripsi -->
                <div class="alert alert-info mb-4" role="alert">
                    <h5 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>
                        Tentang Dosen Pendamping Universitas
                    </h5>
                    <hr>
                    <p class="mb-2">
                        <strong>Sebagai Dosen Pendamping Universitas, Anda memiliki peran penting dalam proses penilaian proposal PKM:</strong>
                    </p>
                    <ul class="mb-0">
                        <li><strong>Validasi Akhir Proposal:</strong> Setelah mahasiswa melakukan revisi akhir, Anda akan melakukan validasi akhir terhadap proposal yang telah diperbaiki.</li>
                        <li><strong>Mentoring & Bimbingan:</strong> Anda dapat memberikan bimbingan dan konsultasi kepada mahasiswa terkait proposal mereka melalui kontak yang telah disediakan.</li>
                        <li><strong>Keputusan Final:</strong> Setelah melakukan validasi, Anda dapat:
                            <ul>
                                <li><strong>Menyetujui (Valid):</strong> Proposal akan dikirim ke Pimpinan PT untuk penilaian final dan keputusan pendanaan.</li>
                                <li><strong>Menolak (Tidak Valid):</strong> Proposal akan dikembalikan ke mahasiswa untuk perbaikan lebih lanjut dengan catatan yang jelas.</li>
                            </ul>
                        </li>
                        <li><strong>Review Dokumen:</strong> Anda dapat melihat dan meninjau proposal asli serta revisi akhir yang telah diupload oleh mahasiswa.</li>
                    </ul>
                    <hr>
                    <p class="mb-0">
                        <small>
                            <i class="fas fa-lightbulb me-1"></i>
                            <strong>Tips:</strong> Pastikan untuk memberikan catatan yang jelas dan konstruktif jika proposal perlu diperbaiki, agar mahasiswa dapat melakukan perbaikan dengan baik.
                        </small>
                    </p>
                </div>

                <!-- Statistik Cards -->
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title">{{ $proposalsValidasi->count() }}</h4>
                                        <p class="card-text">Perlu Validasi Akhir</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title">{{ $proposalsValid->count() }}</h4>
                                        <p class="card-text">Sudah Valid</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title">{{ $proposals->count() }}</h4>
                                        <p class="card-text">Total Proposal</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-file-alt fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Proposal -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Daftar Proposal yang Didampingi
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($proposals->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Judul Proposal</th>
                                            <th>Skim</th>
                                            <th>Mahasiswa</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($proposals as $index => $proposal)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ Str::limit($proposal->judul_proposal, 50) }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $proposal->skim }}</span>
                                                </td>
                                                <td>
                                                    {{ $proposal->mahasiswa->nama_mahasiswa ?? 'N/A' }}
                                                    <br><small class="text-muted">{{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    @if($proposal->status == 'validasi_akhir_dosen_univ')
                                                        <span class="badge bg-warning">Perlu Validasi Akhir</span>
                                                    @elseif($proposal->status_validasi == 'valid')
                                                        <span class="badge bg-success">Valid</span>
                                                    @elseif($proposal->status_validasi == 'tidak_valid')
                                                        <span class="badge bg-danger">Tidak Valid</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $proposal->status)) }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d M Y') }}</small>
                                                </td>
                                                <td>
                                                    @if($proposal->status == 'validasi_akhir_dosen_univ')
                                                        <a href="{{ route('dosen.universitas.validasi.akhir.detail', $proposal->id_proposal) }}" 
                                                           class="btn btn-sm btn-warning">
                                                            <i class="fas fa-check-double me-1"></i>Validasi Akhir
                                                        </a>
                                                    @else
                                                        <a href="{{ route('dosen.universitas.validasi.akhir.detail', $proposal->id_proposal) }}" 
                                                           class="btn btn-sm btn-info">
                                                            <i class="fas fa-eye me-1"></i>Lihat Detail
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum Ada Proposal</h5>
                                <p class="text-muted">Proposal yang Anda dampingi sebagai Dosen Pendamping Universitas akan muncul di sini.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

