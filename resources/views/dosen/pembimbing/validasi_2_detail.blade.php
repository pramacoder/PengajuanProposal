@extends('mainlayout.app')

@section('title', 'Detail Validasi 2')

@section('styles')
<style>
    .pdf-viewer-section {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .pdf-header {
        background: #f8fafc;
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .action-choice-card {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #ffffff;
    }

    .action-choice-card:hover {
        background-color: #f8fafc;
    }

    .action-choice-card.selected-valid {
        border-color: #198754 !important;
        background-color: rgba(25, 135, 84, 0.04);
    }

    .action-choice-card.selected-tolak {
        border-color: #dc3545 !important;
        background-color: rgba(220, 53, 69, 0.04);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'url' => route('dosen.pembimbing.dashboard')],
        ['label' => 'Validasi 2', 'url' => route('dosen.pembimbing.validasi.2')],
        ['label' => 'Detail Validasi', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Validasi Tahap 2 — Usulan Pasca Revisi" 
        subtitle="Verifikasi naskah revisi berdasarkan catatan perbaikan reviewer sebelum diajukan ke tahap seleksi" 
        badge="Dosen Pembimbing"
        icon="fa-check-double"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dosen.pembimbing.validasi.2') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </x-slot:actions>
    </x-page-header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle fs-5"></i>
                <div class="fw-medium">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-times-circle fs-5"></i>
                <div class="fw-medium">{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Kolom Kiri: Info + Referensi Reviewer + PDF -->
        <div class="col-lg-7">
            <!-- Informasi Proposal -->
            <div class="ui-card mb-4 shadow-sm">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-alt text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Informasi Proposal Mahasiswa</h6>
                    </div>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Menunggu Validasi 2</span>
                </div>
                <div class="ui-card-body p-3">
                    <div class="mb-3">
                        <small class="text-secondary fw-semibold d-block">Judul Usulan</small>
                        <div class="fw-bold text-dark">{{ $proposal->judul_proposal }}</div>
                    </div>
                    <div class="row g-2 pt-2 border-top">
                        <div class="col-sm-6">
                            <small class="text-secondary fw-semibold d-block">Skim PKM</small>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">{{ $proposal->skim }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-secondary fw-semibold d-block">Tanggal Pengajuan Awal</small>
                            <span class="text-dark small">{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d M Y') }}</span>
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-secondary fw-semibold d-block">Mahasiswa Pengusul</small>
                            <div class="fw-semibold text-dark small">{{ $proposal->mahasiswa->nama_mhs ?? $proposal->ketua_nama }} (NIM: {{ $proposal->ketua_nim }})</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan Reviewer Sebelumnya (Referensi) -->
            @if($catatanAdministratif || $catatanSubstantif->isNotEmpty())
            <div class="ui-card mb-4 shadow-sm">
                <div class="ui-card-header bg-info-subtle text-info d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-comments"></i>
                        <h6 class="mb-0 fw-bold fs-6">Catatan dari Reviewer (Dasar Revisi)</h6>
                    </div>
                </div>
                <div class="ui-card-body p-3">
                    @if($catatanAdministratif && $catatanAdministratif->catatan)
                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-secondary-subtle text-dark border">Review Administratif</span>
                        </div>
                        <div class="text-secondary small">{{ $catatanAdministratif->catatan }}</div>
                    </div>
                    @endif

                    @foreach($catatanSubstantif as $idx => $nilai)
                    <div class="mb-2 p-3 rounded-3 bg-light border">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary-subtle text-primary border">Review Substantif {{ $idx+1 }}</span>
                            <small class="text-muted">{{ $nilai->reviewer->name ?? 'Reviewer' }}</small>
                        </div>
                        <div class="text-secondary small">{{ $nilai->catatan ?? $nilai->note_substantif ?? '(Tidak ada catatan)' }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- PDF Viewer Dokumen Revisi -->
            <div class="pdf-viewer-section shadow-sm mb-4">
                <div class="pdf-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-pdf text-danger fs-5"></i>
                        <h6 class="mb-0 fw-bold text-dark">
                            Dokumen Revisi Mahasiswa
                            @if($jenisFile === 'revisi')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill ms-2">Revisi Terbaru</span>
                            @endif
                        </h6>
                    </div>
                    @if($fileProposal && $fileProposal->path_file)
                        <a href="{{ route('file.serve', ['path' => $fileProposal->path_file]) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="fas fa-download me-1"></i>Unduh PDF
                        </a>
                    @endif
                </div>
                <div class="p-0">
                    @if($fileProposal && $fileProposal->path_file)
                        <iframe src="{{ route('file.serve', ['path' => $fileProposal->path_file]) }}"
                                width="100%" height="650px" style="border: none;" allowfullscreen></iframe>
                    @else
                        <div class="empty-state-box py-5">
                            <i class="fas fa-file-pdf text-muted mb-3 fs-1"></i>
                            <h5 class="fw-bold text-dark">Dokumen Revisi Belum Tersedia</h5>
                            <p class="text-secondary small">Mahasiswa belum mengunggah berkas revisi atau file tidak ditemukan.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Form Keputusan Validasi -->
        <div class="col-lg-5">
            <div class="ui-card sticky-top shadow-sm border-top border-warning border-3" style="top: 1rem;">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-gavel text-warning"></i>
                        <h6 class="mb-0 fw-bold fs-6">Keputusan Validasi 2</h6>
                    </div>
                </div>
                <div class="ui-card-body p-4">
                    <div class="alert alert-warning py-2 px-3 mb-4 rounded-3 d-flex align-items-start gap-2 small">
                        <i class="fas fa-info-circle text-warning mt-1"></i>
                        <div>
                            Validasi ini dilakukan <strong>setelah mahasiswa merevisi usulannya</strong>. Pastikan Anda telah memeriksa kesesuaian dokumen revisi di samping sebelum menentukan keputusan.
                        </div>
                    </div>

                    <form method="POST" action="{{ route('dosen.pembimbing.validasi.2.submit', $proposal->id_proposal) }}" id="validasiForm2">
                        @csrf

                        <!-- Opsi Valid -->
                        <div class="action-choice-card mb-3" id="card-valid" onclick="selectAction('valid')">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action" id="action_valid" value="valid" required>
                                <label class="form-check-label w-100 ps-1" for="action_valid" style="cursor: pointer;">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="fas fa-check-circle text-success fs-5 mt-1"></i>
                                        <div>
                                            <div class="fw-bold text-dark">Valid — Loloskan ke Tahap Seleksi</div>
                                            <small class="text-muted">Revisi proposal sudah sesuai dengan catatan perbaikan dan siap dinilai reviewer seleksi.</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Opsi Tolak -->
                        <div class="action-choice-card mb-3" id="card-tolak" onclick="selectAction('tolak')">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action" id="action_tolak" value="tolak">
                                <label class="form-check-label w-100 ps-1" for="action_tolak" style="cursor: pointer;">
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="fas fa-times-circle text-danger fs-5 mt-1"></i>
                                        <div>
                                            <div class="fw-bold text-dark">Kembalikan — Perlu Revisi Ulang</div>
                                            <small class="text-muted">Masih terdapat kekurangan atau ketidaksesuaian yang wajib diperbaiki kembali oleh mahasiswa.</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Catatan Pembimbing (Wajib jika tolak) -->
                        <div id="catatanSection" class="d-none mb-3">
                            <label class="form-label fw-bold text-dark" for="catatan">
                                Catatan Perbaikan untuk Mahasiswa <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control rounded-3" name="catatan" id="catatan" rows="4"
                                      placeholder="Uraikan secara jelas bagian yang masih perlu disempurnakan oleh mahasiswa...">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="pt-3 border-top mt-4">
                            <button type="submit" class="btn btn-warning w-100 d-inline-flex align-items-center justify-content-center gap-2 rounded-3 py-2 fw-bold text-dark shadow-sm" id="submitBtn" disabled>
                                <i class="fas fa-paper-plane"></i>
                                <span>Simpan Keputusan Validasi 2</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function selectAction(action) {
    const radioValid = document.getElementById('action_valid');
    const radioTolak = document.getElementById('action_tolak');
    const cardValid = document.getElementById('card-valid');
    const cardTolak = document.getElementById('card-tolak');
    const catatanSection = document.getElementById('catatanSection');
    const catatanField = document.getElementById('catatan');
    const submitBtn = document.getElementById('submitBtn');

    if (action === 'valid') {
        radioValid.checked = true;
        catatanSection.classList.add('d-none');
        catatanField.required = false;
        cardValid.classList.add('selected-valid');
        cardTolak.classList.remove('selected-tolak');
    } else {
        radioTolak.checked = true;
        catatanSection.classList.remove('d-none');
        catatanField.required = true;
        cardTolak.classList.add('selected-tolak');
        cardValid.classList.remove('selected-valid');
        catatanField.focus();
    }

    submitBtn.disabled = false;
}
</script>
@endpush
@endsection
