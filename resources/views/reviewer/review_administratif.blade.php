@extends('mainlayout.app')

@section('title', 'Review Administratif')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('reviewer.dashboard')],
        ['label' => 'Review Administratif', 'active' => true],
    ]" />

    {{-- Header + Filter --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h4 fw-bold mb-1" style="color: var(--text-900);">Review Administratif</h1>
            <p class="mb-0" style="color: var(--text-600); font-size: 0.9rem;">Periksa kelengkapan dokumen dan kesesuaian format proposal</p>
        </div>
        <form method="GET" action="{{ route('reviewer.review.administratif') }}">
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

    {{-- Progress stat mini-cards --}}
    @php
        $totalAssigned   = $proposals->count();
        $completedReview = $proposals->filter(fn($p) => $p->nilaiAdministratif->where('id_reviewer', auth()->id())->first()?->note_administratif)->count();
        $pendingReview   = $totalAssigned - $completedReview;
        $progress        = $totalAssigned > 0 ? round(($completedReview / $totalAssigned) * 100) : 0;
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
                <div class="stat-card-icon orange"><i class="fas fa-clock"></i></div>
                <div class="stat-card-info">
                    <div class="stat-label">Pending</div>
                    <div class="stat-value">{{ $pendingReview }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="stat-card">
                <div class="stat-card-icon teal"><i class="fas fa-percentage"></i></div>
                <div class="stat-card-info">
                    <div class="stat-label">Progress</div>
                    <div class="stat-value">{{ $progress }}%</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Proposal Table --}}
    <div class="card card-custom mb-4">
        <div class="card-header card-header-custom">
            <i class="fas fa-clipboard-check"></i>
            <span>Daftar Proposal — Review Administratif {{ $tahunAjaranTerpilih }}</span>
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
                                    $adminReview  = $proposal->nilaiAdministratif->where('id_reviewer', auth()->id())->first();
                                    $isSelesai    = $adminReview && $adminReview->note_administratif;
                                    $assignedDate = $proposal->updated_at;
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
                                        @if($isSelesai)
                                            <span class="badge rounded-pill" style="background:rgba(5,150,105,0.1);color:#059669;font-size:0.72rem;padding:0.3rem 0.65rem;">
                                                <i class="fas fa-check me-1" style="font-size:0.6rem;"></i>Selesai
                                            </span>
                                        @else
                                            <span class="badge rounded-pill" style="background:rgba(245,158,11,0.12);color:#D97706;font-size:0.72rem;padding:0.3rem 0.65rem;">
                                                <i class="fas fa-clock me-1" style="font-size:0.6rem;"></i>Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3" style="color:var(--text-600);font-size:0.82rem;">
                                        {{ $assignedDate ? $assignedDate->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td class="py-3 text-center">
                                        <a href="{{ route('reviewer.detail.proposal', $proposal->id_proposal) }}"
                                           class="btn btn-sm fw-semibold"
                                           style="background:var(--primary-100);color:var(--primary-700);border-radius:8px;padding:0.3rem 0.85rem;font-size:0.8rem;">
                                            <i class="fas fa-eye me-1"></i>Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div style="width:64px;height:64px;border-radius:16px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <i class="fas fa-clipboard-check" style="font-size:1.5rem;color:var(--text-400);"></i>
                    </div>
                    <h6 class="fw-semibold mb-1" style="color:var(--text-900);">Belum Ada Proposal</h6>
                    <p style="color:var(--text-600);font-size:0.875rem;">Proposal akan muncul setelah operator menugaskan Anda sebagai reviewer administratif.</p>
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
                    <span>Panduan Review Administratif</span>
                </div>
                <div class="card-body">
                    <p class="mb-3" style="font-size:0.875rem;color:var(--text-600);">Review administratif meliputi pemeriksaan kelengkapan dokumen dan kesesuaian format:</p>
                    <div class="d-flex flex-column gap-2">
                        @foreach(['Kelengkapan dokumen proposal', 'Format dan struktur dokumen', 'Kesesuaian dengan ketentuan PKM', 'Kelengkapan data mahasiswa dan dosen', 'Kesesuaian dengan skim yang dipilih'] as $item)
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

        {{-- Progress --}}
        <div class="col-lg-6">
            <div class="card card-custom h-100">
                <div class="card-header card-header-custom">
                    <i class="fas fa-tasks"></i>
                    <span>Progres Review</span>
                </div>
                <div class="card-body">
                    <div class="row text-center g-0 mb-4">
                        <div class="col-6" style="border-right:1px solid var(--border);">
                            <div class="fw-bold" style="font-size:2rem;color:#059669;">{{ $completedReview }}</div>
                            <div style="font-size:0.8rem;color:var(--text-400);">Selesai</div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold" style="font-size:2rem;color:#D97706;">{{ $pendingReview }}</div>
                            <div style="font-size:0.8rem;color:var(--text-400);">Pending</div>
                        </div>
                    </div>
                    @if($totalAssigned > 0)
                        <div style="background:var(--surface-2);border-radius:100px;height:8px;overflow:hidden;">
                            <div style="height:100%;width:{{ $progress }}%;background:linear-gradient(90deg,#059669,#34D399);border-radius:100px;transition:width 0.5s ease;"></div>
                        </div>
                        <div class="text-end mt-1" style="font-size:0.78rem;color:var(--text-400);">{{ $progress }}% selesai</div>
                    @else
                        <p class="text-center mb-0" style="font-size:0.85rem;color:var(--text-400);">Belum ada proposal yang ditugaskan</p>
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
