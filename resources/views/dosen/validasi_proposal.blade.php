@extends('dosen.layout')

@section('page_title', 'Validasi Proposal')

@section('dosen_content')
<div class="row">
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header card-header-custom">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-check me-2"></i>
                    Validasi Proposal
                </h5>
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
                                                <div class="text-center">
                                                    <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                                                    <div class="btn btn-sm btn-danger">PDF</div>
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
                                                        <strong>Dikirim Oleh:</strong> {{ $proposal->mahasiswa->nama_mahasiswa ?? 'N/A' }}
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
                                                        <span class="status-badge status-pending">
                                                            <i class="fas fa-clock me-1"></i>
                                                            Belum dilakukan Validasi
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <a href="{{ route('dosen.proposal.detail', $proposal->id_proposal) }}" 
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
