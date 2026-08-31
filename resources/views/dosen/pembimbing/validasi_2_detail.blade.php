@extends('mainlayout.app')

@section('title', 'Detail Validasi 2')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('dosen.pembimbing.dashboard')],
        ['label' => 'Validasi 2', 'url' => route('dosen.pembimbing.validasi.2')],
        ['label' => 'Detail', 'active' => true],
    ]" />

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-check-double me-2 text-warning"></i>Validasi Tahap 2</h2>
            <a href="{{ route('dosen.pembimbing.validasi.2') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Kiri: Info + PDF -->
        <div class="col-lg-7 mb-4">
            <!-- Info Proposal -->
            <div class="card mb-3">
                <div class="card-header bg-warning bg-opacity-10">
                    <h5 class="mb-0 text-warning"><i class="fas fa-file-alt me-2"></i>Informasi Proposal</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="fw-semibold" style="width:40%">Judul</td><td>{{ $proposal->judul }}</td></tr>
                        <tr><td class="fw-semibold">Skim</td><td><span class="badge bg-secondary">{{ $proposal->skim }}</span></td></tr>
                        <tr><td class="fw-semibold">Mahasiswa</td><td>{{ $proposal->mahasiswa->nama_mhs ?? $proposal->ketua_nama }} ({{ $proposal->ketua_nim }})</td></tr>
                        <tr><td class="fw-semibold">Tanggal Submit</td><td>{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d M Y') }}</td></tr>
                        <tr>
                            <td class="fw-semibold">Status Validasi 2</td>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i>Menunggu Validasi
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Catatan Reviewer (referensi) -->
            @if($catatanAdministratif || $catatanSubstantif->isNotEmpty())
            <div class="card mb-3">
                <div class="card-header bg-info bg-opacity-10">
                    <h6 class="mb-0 text-info"><i class="fas fa-comments me-2"></i>Catatan dari Reviewer (Referensi)</h6>
                </div>
                <div class="card-body">
                    @if($catatanAdministratif && $catatanAdministratif->catatan)
                    <div class="mb-3">
                        <h6 class="text-secondary" style="font-size:0.85rem">Review Administratif</h6>
                        <div class="bg-light rounded p-2" style="font-size:0.875rem">{{ $catatanAdministratif->catatan }}</div>
                    </div>
                    @endif
                    @foreach($catatanSubstantif as $idx => $nilai)
                    <div class="mb-2">
                        <h6 class="text-secondary" style="font-size:0.85rem">Review Substantif {{ $idx+1 }} — {{ $nilai->reviewer->name ?? 'Reviewer' }}</h6>
                        <div class="bg-light rounded p-2" style="font-size:0.875rem">{{ $nilai->catatan ?? '(Tidak ada catatan)' }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- PDF Viewer -->
            @if($fileProposal && $fileProposal->path_file)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-file-pdf me-2 text-danger"></i>
                        Dokumen Revisi
                        @if($jenisFile === 'revisi')<span class="badge bg-success ms-2">Revisi Terbaru</span>@endif
                    </h6>
                </div>
                <div class="card-body p-0">
                    <iframe src="{{ route('file.serve', ['path' => $fileProposal->path_file]) }}"
                            width="100%" height="600px" style="border: none;"></iframe>
                </div>
            </div>
            @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>Dokumen revisi belum tersedia.
            </div>
            @endif
        </div>

        <!-- Kanan: Form Validasi -->
        <div class="col-lg-5">
            <div class="card border-warning sticky-top" style="top: 1rem;">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-gavel me-2"></i>Keputusan Validasi 2</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning py-2 mb-3" style="font-size:0.85rem">
                        <i class="fas fa-info-circle me-1"></i>
                        Ini adalah validasi <strong>setelah mahasiswa merevisi</strong> proposalnya berdasarkan catatan reviewer. Periksa PDF revisi sebelum mengambil keputusan.
                    </div>

                    <form method="POST" action="{{ route('dosen.pembimbing.validasi.2.submit', $proposal->id_proposal) }}" id="validasiForm2">
                        @csrf

                        <!-- Valid -->
                        <div class="form-check border border-success rounded p-3 mb-2 cursor-pointer" id="card-valid"
                             onclick="selectAction('valid')" style="cursor:pointer">
                            <input class="form-check-input" type="radio" name="action" id="action_valid" value="valid" required>
                            <label class="form-check-label w-100" for="action_valid" style="cursor:pointer">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-check-circle text-success fa-lg"></i>
                                    <div>
                                        <div class="fw-semibold">Valid — Loloskan ke Tahap Seleksi</div>
                                        <small class="text-muted">Proposal revisi sudah sesuai dan siap untuk reviewer seleksi</small>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Tolak -->
                        <div class="form-check border border-danger rounded p-3 mb-3 cursor-pointer" id="card-tolak"
                             onclick="selectAction('tolak')" style="cursor:pointer">
                            <input class="form-check-input" type="radio" name="action" id="action_tolak" value="tolak">
                            <label class="form-check-label w-100" for="action_tolak" style="cursor:pointer">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-times-circle text-danger fa-lg"></i>
                                    <div>
                                        <div class="fw-semibold">Kembalikan — Revisi Ulang</div>
                                        <small class="text-muted">Masih ada kekurangan, mahasiswa perlu merevisi ulang</small>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Catatan (wajib jika tolak) -->
                        <div id="catatanSection" class="d-none mb-3">
                            <label class="form-label fw-semibold">Catatan untuk Mahasiswa <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="catatan" id="catatan" rows="4"
                                      placeholder="Jelaskan apa yang perlu diperbaiki...">{{ old('catatan') }}</textarea>
                            @error('catatan')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-warning w-100 fw-bold" id="submitBtn" disabled>
                            <i class="fas fa-paper-plane me-2"></i>Submit Validasi 2
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectAction(action) {
    document.getElementById('action_' + action).checked = true;
    document.getElementById('catatanSection').classList.toggle('d-none', action !== 'tolak');
    document.getElementById('catatan').required = (action === 'tolak');
    document.getElementById('submitBtn').disabled = false;

    // Visual highlight
    document.getElementById('card-valid').classList.toggle('border-success', action === 'valid');
    document.getElementById('card-valid').classList.toggle('border-2', action === 'valid');
    document.getElementById('card-tolak').classList.toggle('border-danger', action === 'tolak');
    document.getElementById('card-tolak').classList.toggle('border-2', action === 'tolak');
}
</script>
@endsection
