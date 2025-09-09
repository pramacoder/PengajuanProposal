@extends('dosen.layout')

@section('page_title', 'Detail Hasil Final - Proposal')

@section('dosen_content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-trophy me-2"></i>
                    Detail Hasil Final
                </h5>
                <a href="{{ route('dosen.hasil.final') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
            <div class="card-body">
                <!-- Informasi Proposal -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3">Informasi Proposal</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Judul:</strong></td>
                                <td>{{ $proposal->judul_proposal }}</td>
                            </tr>
                            <tr>
                                <td><strong>Skim:</strong></td>
                                <td>{{ $proposal->skim }}</td>
                            </tr>
                            <tr>
                                <td><strong>Ketua:</strong></td>
                                <td>{{ $proposal->mahasiswa->nama_mahasiswa }}</td>
                            </tr>
                            <tr>
                                <td><strong>Prodi:</strong></td>
                                <td>{{ $proposal->mahasiswa->prodi->nama_prodi ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fakultas:</strong></td>
                                <td>{{ $proposal->mahasiswa->fakultas->nama_fakultas ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status Final:</strong></td>
                                <td>
                                    @if($proposal->status_final === 'lolos')
                                        <span class="badge bg-success">LOLOS</span>
                                    @else
                                        <span class="badge bg-danger">TIDAK LOLOS</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold text-primary mb-3">Dokumen</h6>
                        <div class="d-grid gap-2">
                            @if($proposal->dokumen && $proposal->dokumen->file_proposal)
                                <a href="{{ route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-download me-2"></i>Download Proposal
                                </a>
                            @endif
                            @if($proposal->dokumen && $proposal->dokumen->file_lampiran)
                                <a href="{{ route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'lampiran']) }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="fas fa-download me-2"></i>Download Lampiran
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Hasil Review Administratif -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Hasil Review Administratif
                        </h6>
                        @if($proposal->nilaiAdministratif)
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <i class="fas fa-clipboard-check me-2"></i>
                                    <strong>Review Administratif</strong>
                                </div>
                                <div class="card-body">
                                    @if($proposal->nilaiAdministratif && $proposal->nilaiAdministratif->count() > 0)
                                        @php
                                            $adminRecord = $proposal->nilaiAdministratif->first();
                                        @endphp
                                        <div class="mb-3">
                                            <i class="fas fa-edit text-info me-2"></i>
                                            <strong>Catatan:</strong><br>
                                            {{ $adminRecord->note_administratif ?? 'Tidak ada catatan khusus.' }}
                                        </div>
                                        @if($adminRecord->checklist)
                                            <div class="mb-3">
                                                <i class="fas fa-list-check text-warning me-2"></i>
                                                <strong>Checklist Administratif:</strong><br>
                                                <div class="row mt-2">
                                                    @foreach($adminRecord->checklist as $item => $status)
                                                        <div class="col-md-6 mb-2">
                                                            <span class="badge bg-{{ $status ? 'success' : 'danger' }} me-2">
                                                                {{ $status ? '✓' : '✗' }}
                                                            </span>
                                                            {{ ucwords(str_replace('_', ' ', $item)) }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Info:</strong> Belum ada hasil review administratif untuk proposal ini.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Info:</strong> Belum ada hasil review administratif untuk proposal ini.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Hasil Review Substantif -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-user-check me-2"></i>
                            Hasil Review Substantif
                        </h6>
                        @if($proposal->nilaiSubstantif && $proposal->nilaiSubstantif->count() > 0)
                            @foreach($proposal->nilaiSubstantif as $index => $review)
                                <div class="card {{ $index > 0 ? 'mt-3' : '' }}">
                                    <div class="card-header bg-info text-white">
                                        <i class="fas fa-user me-2"></i>
                                        <strong>Reviewer {{ $index + 1 }} - {{ $review->reviewer ? $review->reviewer->nama_reviewer : 'N/A' }}</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <i class="fas fa-edit text-warning me-2"></i>
                                            <strong>Catatan:</strong><br>
                                            {{ $review->note_substantif ?? 'Tidak ada catatan khusus.' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Info:</strong> Belum ada hasil review substantif untuk proposal ini.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Hasil Final -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-trophy me-2"></i>
                            Hasil Final
                        </h6>
                        @if($proposal->status_final === 'lolos')
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Selamat! Proposal LOLOS</strong><br>
                                Proposal ini telah lolos seleksi dan akan dikirim ke tingkat nasional. 
                                Tim mahasiswa dapat melanjutkan ke tahap implementasi sesuai dengan rencana yang telah dibuat.
                            </div>
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-trophy fa-3x mb-3"></i>
                                    <h4>PROPOSAL LOLOS</h4>
                                    <p class="mb-0">Proposal akan dikirim ke tingkat nasional untuk seleksi selanjutnya.</p>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                <strong>Proposal TIDAK LOLOS</strong><br>
                                Proposal ini tidak lolos seleksi. Tim mahasiswa dapat melakukan perbaikan dan mengajukan kembali pada periode berikutnya.
                            </div>
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-times-circle fa-3x mb-3"></i>
                                    <h4>PROPOSAL TIDAK LOLOS</h4>
                                    <p class="mb-0">Proposal tidak lolos seleksi. Silakan lakukan perbaikan untuk pengajuan selanjutnya.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('dosen_scripts')
<script>
    // Auto refresh setiap 60 detik untuk update status final
    setInterval(function() {
        location.reload();
    }, 60000);
</script>
@endsection
