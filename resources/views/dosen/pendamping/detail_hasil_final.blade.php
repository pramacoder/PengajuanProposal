@extends('mainlayout.app')

@section('title', 'Detail Hasil Final - Proposal')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard Dosen', 'url' => route('dosen.pendamping.dashboard')],
        ['label' => 'Hasil Final', 'url' => route('dosen.hasil.final')],
        ['label' => 'Detail Keputusan Final', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Detail Hasil Keputusan Final" 
        subtitle="Rincian akumulasi penilaian, status kelolosan akhir, dan riwayat evaluasi proposal" 
        badge="Dosen Pendamping"
        icon="fa-trophy"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dosen.hasil.final') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="row g-4 mb-4">
        <!-- Informasi Proposal -->
        <div class="col-lg-7">
            <div class="ui-card h-100 shadow-sm">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-file-alt text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Informasi Proposal Mahasiswa</h6>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">{{ $proposal->skim }}</span>
                </div>
                <div class="ui-card-body p-4">
                    <div class="mb-3">
                        <small class="text-secondary fw-semibold d-block">Judul Usulan</small>
                        <h6 class="fw-bold text-dark mb-0">{{ $proposal->judul_proposal }}</h6>
                    </div>

                    <div class="row g-3 pt-3 border-top">
                        <div class="col-sm-6">
                            <small class="text-secondary fw-semibold d-block">Ketua Mahasiswa</small>
                            <span class="fw-semibold text-dark small">{{ $proposal->mahasiswa->nama_mahasiswa ?? $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</span>
                            <small class="text-muted d-block">NIM: {{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-secondary fw-semibold d-block">Program Studi & Fakultas</small>
                            <span class="text-dark small">{{ $proposal->mahasiswa->prodi->nama_prodi ?? 'N/A' }}</span>
                            <small class="text-muted d-block">{{ $proposal->mahasiswa->fakultas->nama_fakultas ?? 'N/A' }}</small>
                        </div>
                    </div>

                    <div class="row g-2 pt-3 border-top mt-3 align-items-center">
                        <div class="col-sm-6">
                            <small class="text-secondary fw-semibold d-block">Status Final</small>
                            @if($proposal->status_final === 'lolos')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-bold">LOLOS FINAL</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-bold">TIDAK LOLOS</span>
                            @endif
                        </div>
                        @if($proposal->hasilFinal)
                        <div class="col-sm-6">
                            <small class="text-secondary fw-semibold d-block">Nilai Akhir</small>
                            <span class="fw-bold fs-5 text-primary">@formatId($proposal->hasilFinal->nilai, 2)</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Berkas Dokumen -->
        <div class="col-lg-5">
            <div class="ui-card h-100 shadow-sm">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-folder text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Dokumen Pendukung</h6>
                    </div>
                </div>
                <div class="ui-card-body p-4">
                    <div class="d-flex flex-column gap-2">
                        @if($proposal->dokumen && $proposal->dokumen->file_proposal)
                            <a href="{{ route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'proposal']) }}" 
                               class="btn btn-outline-primary rounded-3 py-2 px-3 d-flex align-items-center justify-content-between fw-semibold" target="_blank">
                                <span class="d-flex align-items-center gap-2">
                                    <i class="fas fa-file-pdf text-danger fs-5"></i>
                                    <span>Unduh Naskah Proposal</span>
                                </span>
                                <i class="fas fa-download"></i>
                            </a>
                        @endif
                        @if($proposal->dokumen && $proposal->dokumen->file_lampiran)
                            <a href="{{ route('dosen.proposal.download', ['id' => $proposal->id_proposal, 'jenis' => 'lampiran']) }}" 
                               class="btn btn-outline-secondary rounded-3 py-2 px-3 d-flex align-items-center justify-content-between fw-semibold" target="_blank">
                                <span class="d-flex align-items-center gap-2">
                                    <i class="fas fa-paperclip text-secondary fs-5"></i>
                                    <span>Unduh Berkas Lampiran</span>
                                </span>
                                <i class="fas fa-download"></i>
                            </a>
                        @endif
                        @if(!$proposal->dokumen || (!$proposal->dokumen->file_proposal && !$proposal->dokumen->file_lampiran))
                            <div class="empty-state-box py-4">
                                <i class="fas fa-file-alt text-muted mb-2 fs-1"></i>
                                <p class="text-secondary small mb-0">Tidak ada berkas terlampir.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hasil Keputusan Final Hero Card -->
    <div class="ui-card shadow-sm mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-trophy text-warning"></i>
                <h6 class="mb-0 fw-bold fs-6">Keputusan & Rekomendasi Hasil Akhir</h6>
            </div>
        </div>
        <div class="ui-card-body p-4">
            @if($proposal->hasilFinal)
                <div class="p-4 rounded-3 {{ $proposal->status_final === 'lolos' ? 'bg-success-subtle border border-success-subtle' : 'bg-danger-subtle border border-danger-subtle' }} mb-3">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle p-3 {{ $proposal->status_final === 'lolos' ? 'bg-success text-white' : 'bg-danger text-white' }}">
                            <i class="fas {{ $proposal->status_final === 'lolos' ? 'fa-award' : 'fa-times-circle' }} fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold {{ $proposal->status_final === 'lolos' ? 'text-success' : 'text-danger' }} mb-1">
                                {{ $proposal->status_final === 'lolos' ? 'PROPOSAL LOLOS SELEKSI FINAL' : 'PROPOSAL BELUM BERHASIL LOLOS SELEKSI' }}
                            </h5>
                            <p class="text-dark small mb-0">
                                {{ $proposal->status_final === 'lolos' ? 'Selamat! Proposal telah memenuhi standar substantif dan ditetapkan lolos untuk tahap pengusulan nasional Belmawa.' : 'Proposal belum memenuhi ambang batas passing grade seleksi final periode ini. Jadikan catatan evaluasi reviewer sebagai acuan perbaikan periode mendatang.' }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($proposal->hasilFinal->catatan_final)
                    <div class="p-3 bg-light rounded-3 border">
                        <strong class="small text-dark d-block mb-1"><i class="fas fa-comment text-primary me-1"></i>Catatan Keputusan Final:</strong>
                        <div class="small text-secondary">{{ $proposal->hasilFinal->catatan_final }}</div>
                    </div>
                @endif
            @else
                <div class="empty-state-box py-4">
                    <i class="fas fa-clock text-warning mb-2 fs-1"></i>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Keputusan Final</h6>
                    <p class="text-secondary small mb-0">Penetapan hasil akhir akan diumumkan setelah seluruh rangkaian penilaian diselesaikan oleh pengelola program.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Evaluasi Administratif & Substantif -->
    <div class="row g-4 mb-4">
        <!-- Review Administratif -->
        <div class="col-md-6">
            <div class="ui-card h-100 shadow-sm">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-clipboard-check text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Review Administratif</h6>
                    </div>
                </div>
                <div class="ui-card-body p-4">
                    @if($proposal->nilaiAdministratif && $proposal->nilaiAdministratif->count() > 0)
                        @php $adminRecord = $proposal->nilaiAdministratif->first(); @endphp
                        <div class="p-3 rounded-3 bg-light border mb-3">
                            <strong class="small text-dark d-block mb-1">Catatan Evaluator:</strong>
                            <div class="small text-secondary">{{ $adminRecord->note_administratif ?? 'Tidak ada catatan tertulis.' }}</div>
                        </div>
                    @else
                        <div class="empty-state-box py-3">
                            <p class="text-secondary small mb-0">Belum ada evaluasi administratif tercatat.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Review Substantif -->
        <div class="col-md-6">
            <div class="ui-card h-100 shadow-sm">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-user-check text-info"></i>
                        <h6 class="mb-0 fw-bold fs-6">Review Substantif</h6>
                    </div>
                </div>
                <div class="ui-card-body p-4">
                    @if($proposal->nilaiSubstantif && $proposal->nilaiSubstantif->count() > 0)
                        <div class="d-flex flex-column gap-2">
                            @foreach($proposal->nilaiSubstantif as $index => $review)
                                <div class="p-3 rounded-3 bg-light border">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="badge bg-primary-subtle text-primary border">Reviewer {{ $index + 1 }}</span>
                                        <small class="text-muted">{{ $review->reviewer ? ($review->reviewer->nama_reviewer ?? $review->reviewer->name) : 'Reviewer' }}</small>
                                    </div>
                                    <div class="small text-secondary mt-1">{{ $review->note_substantif ?? $review->catatan ?? 'Tidak ada catatan khusus.' }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-box py-3">
                            <p class="text-secondary small mb-0">Belum ada evaluasi substantif tercatat.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
