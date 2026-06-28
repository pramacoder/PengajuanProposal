@extends('mainlayout.app')

@section('title', 'Review Substantif')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('reviewer.dashboard')],
        ['label' => 'Review Substantif', 'active' => true],
    ]" />

    {{-- Header + Filter --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h4 fw-bold mb-1" style="color: var(--text-900);">Review Substantif</h1>
            <p class="mb-0" style="color: var(--text-600); font-size: 0.9rem;">Nilai kualitas konten dan substansi proposal yang ditugaskan</p>
        </div>
        <form method="GET" action="{{ route('reviewer.review.substantif') }}">
            <div class="d-flex align-items-center gap-2">
                <label class="form-label mb-0 fw-semibold" style="font-size:0.8rem;color:var(--text-600);">Tahun Ajaran</label>
                <select name="tahun_ajaran" id="tahun_ajaran" class="form-select form-select-sm" onchange="this.form.submit()"
                        style="border-color:var(--border);border-radius:8px;min-width:130px;font-size:0.85rem;">
                    @foreach($tahunAjaranList as $tahunAjaran)
                        <option value="{{ $tahunAjaran }}" {{ $tahunAjaranTerpilih == $tahunAjaran ? 'selected' : '' }}>
                            {{ $tahunAjaran }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- Stat mini-cards --}}
    @php
        $totalAssigned   = $proposals->count();
        $completedReview = $proposals->filter(function($p) {
            $r = $p->nilaiSubstantif->where('id_reviewer', auth()->id())->first();
            return $r && $r->note_substantif && $r->note_substantif !== 'Review substantif dimulai';
        })->count();
        $readyForReview = $proposals->filter(function($p) {
            $isAssigned = $p->id_reviewer_substantif_1 == auth()->id() || $p->id_reviewer_substantif_2 == auth()->id();
            $adminDone  = $p->nilaiAdministratif()->where('id_reviewer', $p->id_reviewer_administratif)->whereNotNull('note_administratif')->exists();
            return $adminDone && $isAssigned;
        })->count();
        $pendingReview = max(0, $totalAssigned - $completedReview - $readyForReview);
        $progress      = $totalAssigned > 0 ? round(($completedReview / $totalAssigned) * 100) : 0;
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon maroon"><i class="fas fa-file-alt"></i></div>
                <div class="stat-card-info">
                    <div class="stat-label">Total Ditugaskan</div>
                    <div class="stat-value">{{ $totalAssigned }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon green"><i class="fas fa-check-circle"></i></div>
                <div class="stat-card-info">
                    <div class="stat-label">Selesai</div>
                    <div class="stat-value">{{ $completedReview }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon teal"><i class="fas fa-play-circle"></i></div>
                <div class="stat-card-info">
                    <div class="stat-label">Siap Review</div>
                    <div class="stat-value">{{ $readyForReview }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon orange"><i class="fas fa-hourglass-half"></i></div>
                <div class="stat-card-info">
                    <div class="stat-label">Menunggu Admin</div>
                    <div class="stat-value">{{ $pendingReview }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Proposal Table --}}
    <div class="card card-custom mb-4">
        <div class="card-header card-header-custom">
            <i class="fas fa-star-half-alt"></i>
            <span>Daftar Proposal — Review Substantif {{ $tahunAjaranTerpilih }}</span>
        </div>
        <div class="card-body p-0">
            @if($proposals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="dataTable" style="font-size:0.875rem;">
                        <thead style="background:var(--surface-2);border-bottom:1px solid var(--border);">
                            <tr>
                                <th class="px-4 py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">No</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Judul Proposal</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Mahasiswa</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Dosen Pendamping</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Skim</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Status Review</th>
                                <th class="py-3 fw-semibold" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Ditugaskan</th>
                                <th class="py-3 fw-semibold text-center" style="color:var(--text-600);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.04em;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $index => $proposal)
                                @php
                                    $substantifReview   = $proposal->nilaiSubstantif->where('id_reviewer', auth()->id())->first();
                                    $isAssignedSubstantif = $proposal->id_reviewer_substantif_1 == auth()->id() || $proposal->id_reviewer_substantif_2 == auth()->id();
                                    $adminReviewDone    = $proposal->nilaiAdministratif()->where('id_reviewer', $proposal->id_reviewer_administratif)->whereNotNull('note_administratif')->exists();

                                    if ($substantifReview && $substantifReview->note_substantif && $substantifReview->note_substantif !== 'Review substantif dimulai') {
                                        $reviewStatus = 'selesai';
                                    } elseif ($adminReviewDone && $isAssignedSubstantif) {
                                        $reviewStatus = 'siap';
                                    } else {
                                        $reviewStatus = 'menunggu';
                                    }

                                    $assignedDate = $substantifReview ? $substantifReview->created_at : $proposal->updated_at;
                                @endphp
                                <tr style="border-bottom: 1px solid var(--border);">
                                    <td class="px-4 py-3" style="color:var(--text-400);">{{ $index + 1 }}</td>
                                    <td class="py-3">
                                        <div class="fw-semibold text-truncate" style="max-width:220px;color:var(--text-900);" title="{{ $proposal->judul_proposal }}">
                                            {{ $proposal->judul_proposal }}
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-medium" style="color:var(--text-900);">{{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                                        <div style="font-size:0.78rem;color:var(--text-400);">{{ $proposal->mahasiswa->nim ?? 'N/A' }}</div>
                                    </td>
                                    <td class="py-3">
                                        <div style="color:var(--text-700);">{{ $proposal->dosen->nama_dosen ?? 'N/A' }}</div>
                                        <div style="font-size:0.78rem;color:var(--text-400);">{{ $proposal->dosen->nuptk ?? '' }}</div>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge rounded-pill" style="background:rgba(8,145,178,0.1);color:#0891B2;font-size:0.72rem;padding:0.3rem 0.65rem;">
                                            {{ $proposal->skim }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        @if($reviewStatus === 'selesai')
                                            <span class="badge rounded-pill" style="background:rgba(5,150,105,0.1);color:#059669;font-size:0.72rem;padding:0.3rem 0.65rem;">
                                                <i class="fas fa-check me-1" style="font-size:0.6rem;"></i>Selesai
                                            </span>
                                        @elseif($reviewStatus === 'siap')
                                            <span class="badge rounded-pill" style="background:rgba(8,145,178,0.1);color:#0891B2;font-size:0.72rem;padding:0.3rem 0.65rem;">
                                                <i class="fas fa-play me-1" style="font-size:0.6rem;"></i>Siap Review
                                            </span>
                                        @else
                                            <span class="badge rounded-pill" style="background:rgba(245,158,11,0.12);color:#D97706;font-size:0.72rem;padding:0.3rem 0.65rem;">
                                                <i class="fas fa-hourglass-half me-1" style="font-size:0.6rem;"></i>Menunggu Admin
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3" style="color:var(--text-600);font-size:0.82rem;">
                                        {{ $assignedDate ? $assignedDate->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($adminReviewDone && $isAssignedSubstantif)
                                            <a href="{{ route('reviewer.detail.proposal.substantif', $proposal->id_proposal) }}"
                                               class="btn btn-sm fw-semibold"
                                               style="background:var(--primary-100);color:var(--primary-700);border-radius:8px;padding:0.3rem 0.85rem;font-size:0.8rem;">
                                                <i class="fas fa-eye me-1"></i>Review
                                            </a>
                                        @else
                                            <button class="btn btn-sm fw-semibold" disabled
                                                    style="background:var(--surface-2);color:var(--text-400);border-radius:8px;padding:0.3rem 0.85rem;font-size:0.8rem;">
                                                <i class="fas fa-clock me-1"></i>Menunggu
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div style="width:64px;height:64px;border-radius:16px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <i class="fas fa-search" style="font-size:1.5rem;color:var(--text-400);"></i>
                    </div>
                    <h6 class="fw-semibold mb-1" style="color:var(--text-900);">Belum Ada Proposal</h6>
                    <p style="color:var(--text-600);font-size:0.875rem;">Proposal akan muncul setelah operator menugaskan Anda sebagai reviewer substantif.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Bottom info cards --}}
    <div class="row g-3">
        {{-- Panduan --}}
        <div class="col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-header card-header-custom">
                    <i class="fas fa-info-circle"></i>
                    <span>Panduan Review Substantif</span>
                </div>
                <div class="card-body">
                    <p class="mb-3" style="font-size:0.875rem;color:var(--text-600);">Review substantif meliputi penilaian kualitas konten dan substansi proposal:</p>
                    <div class="d-flex flex-column gap-2">
                        @foreach(['Kualitas dan orisinalitas ide', 'Metodologi penelitian', 'Kelayakan teknis dan ekonomis', 'Dampak dan manfaat', 'Kemampuan tim pelaksana', 'Kesinambungan program'] as $item)
                            <div class="d-flex align-items-center gap-2" style="font-size:0.85rem;color:var(--text-700);">
                                <div style="width:20px;height:20px;border-radius:6px;background:rgba(5,150,105,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-check" style="font-size:0.65rem;color:#059669;"></i>
                                </div>
                                {{ $item }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Kriteria Bobot + Progress --}}
        <div class="col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-header card-header-custom">
                    <i class="fas fa-star"></i>
                    <span>Kriteria Penilaian &amp; Progres</span>
                </div>
                <div class="card-body">
                    {{-- Kriteria --}}
                    <div class="mb-4">
                        @foreach([['Kreativitas dan orisinalitas ide','25%','maroon'],['Kelayakan program','25%','maroon'],['Dampak dan manfaat','20%','maroon'],['Kemampuan tim pelaksana','15%','teal'],['Kesinambungan program','15%','teal']] as $k)
                            <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid var(--border);">
                                <span style="font-size:0.83rem;color:var(--text-700);">{{ $k[0] }}</span>
                                <span class="badge rounded-pill fw-bold" style="background:rgba(143,11,19,0.1);color:var(--primary-700);font-size:0.72rem;">{{ $k[1] }}</span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Progress --}}
                    <div class="row text-center g-0 mb-3">
                        <div class="col-4" style="border-right:1px solid var(--border);">
                            <div class="fw-bold" style="font-size:1.5rem;color:#059669;">{{ $completedReview }}</div>
                            <div style="font-size:0.75rem;color:var(--text-400);">Selesai</div>
                        </div>
                        <div class="col-4" style="border-right:1px solid var(--border);">
                            <div class="fw-bold" style="font-size:1.5rem;color:#0891B2;">{{ $readyForReview }}</div>
                            <div style="font-size:0.75rem;color:var(--text-400);">Siap</div>
                        </div>
                        <div class="col-4">
                            <div class="fw-bold" style="font-size:1.5rem;color:#D97706;">{{ $pendingReview }}</div>
                            <div style="font-size:0.75rem;color:var(--text-400);">Menunggu</div>
                        </div>
                    </div>
                    @if($totalAssigned > 0)
                        <div style="background:var(--surface-2);border-radius:100px;height:8px;overflow:hidden;">
                            <div style="height:100%;width:{{ $progress }}%;background:linear-gradient(90deg,#059669,#34D399);border-radius:100px;transition:width 0.5s ease;"></div>
                        </div>
                        <div class="text-end mt-1" style="font-size:0.78rem;color:var(--text-400);">{{ $progress }}% selesai</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-card-icon.orange { background: rgba(245,158,11,0.1); color: #D97706; }
.stat-card-icon.teal   { background: rgba(8,145,178,0.1);  color: #0891B2; }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' },
            pageLength: 10,
            order: [[0, 'asc']]
        });
    });
</script>
@endpush
@endsection
