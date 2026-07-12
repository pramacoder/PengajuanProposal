@extends('mainlayout.app')

@section('title', 'Manajemen Form Penilaian')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('pimpinan_pt.dashboard')],
        ['label' => 'Form Penilaian', 'active' => true],
    ]" />

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h4 fw-bold mb-1" style="color:var(--text-900);">Manajemen Form Penilaian</h1>
            <p class="mb-0" style="color:var(--text-600);font-size:0.9rem;">Kelola form penilaian dinamis untuk reviewer — administratif, substantif, dan semi final</p>
        </div>
        <!-- No Create button for Pimpinan PT -->
    </div>

    {{-- Filters --}}
    <div class="card card-custom mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('pimpinan_pt.form.penilaian.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-600);">Jenis Form</label>
                    <select name="jenis_form" class="form-select form-select-sm" style="border-radius:8px;border-color:var(--border);">
                        <option value="">— Semua —</option>
                        @foreach(['administratif'=>'Administratif','substantif'=>'Substantif','substantif_seleksi'=>'Substantif Seleksi','semi_final'=>'Semi Final','final'=>'Final'] as $val=>$label)
                            <option value="{{ $val }}" {{ request('jenis_form')===$val?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="font-size:0.8rem;color:var(--text-600);">Skim</label>
                    <select name="skim" class="form-select form-select-sm" style="border-radius:8px;border-color:var(--border);">
                        <option value="">— Semua —</option>
                        @foreach(['RE','RSH','KC','PM','PI','K','KI','VGK','AI','GFT'] as $s)
                            <option value="{{ $s }}" {{ request('skim')===$s?'selected':'' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-sm fw-semibold" style="background:var(--primary-100);color:var(--primary-700);border-radius:8px;padding:0.45rem 1rem;">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('pimpinan_pt.form.penilaian') }}" class="btn btn-sm" style="border:1px solid var(--border);border-radius:8px;padding:0.45rem 1rem;color:var(--text-600);">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <i class="fas fa-list-alt"></i>
            <span>Daftar Form Penilaian</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
                    <thead style="background:var(--surface-2);border-bottom:1px solid var(--border);">
                        <tr>
                            <th class="px-4 py-3 fw-bold" style="color:rgba(255,255,255,0.92);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">No</th>
                            <th class="py-3 fw-bold" style="color:rgba(255,255,255,0.92);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">Nama Form</th>
                            <th class="py-3 fw-bold" style="color:rgba(255,255,255,0.92);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">Penempatan</th>
                            <th class="py-3 fw-bold" style="color:rgba(255,255,255,0.92);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">Skim</th>
                            <th class="py-3 fw-bold" style="color:rgba(255,255,255,0.92);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">Tahun Ajaran</th>
                            <th class="py-3 fw-bold" style="color:rgba(255,255,255,0.92);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">Fields</th>
                            <th class="py-3 fw-bold" style="color:rgba(255,255,255,0.92);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                            <th class="py-3 fw-bold text-center" style="color:rgba(255,255,255,0.92);font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($formPenilaians as $index => $form)
                        @php
                            $jenisLabels = [
                                'administratif'      => ['Administratif',     '#0891B2', 'rgba(8,145,178,0.1)'],
                                'substantif'         => ['Substantif',        '#7C3AED', 'rgba(124,58,237,0.1)'],
                                'substantif_seleksi' => ['Substantif Seleksi','#D97706', 'rgba(245,158,11,0.1)'],
                                'semi_final'         => ['Semi Final',        '#059669', 'rgba(5,150,105,0.1)'],
                                'final'              => ['Final',             '#DC2626', 'rgba(220,38,38,0.1)'],
                            ];
                            [$jLabel, $jColor, $jBg] = $jenisLabels[$form->jenis_form] ?? [$form->jenis_form, '#6B7280', 'rgba(107,114,128,0.1)'];
                            $fieldCount = is_array($form->fields) ? count($form->fields) : 0;
                        @endphp
                        <tr style="border-bottom:1px solid var(--border);">
                            <td class="px-4 py-3" style="color:var(--text-400);">{{ $formPenilaians->firstItem() + $index }}</td>
                            <td class="py-3">
                                <div class="fw-semibold" style="color:var(--text-900);">{{ $form->nama_form }}</div>
                                @if($form->creator)
                                    <div style="font-size:0.75rem;color:var(--text-400);">{{ $form->creator->name }}</div>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="badge rounded-pill fw-semibold" style="background:{{ $jBg }};color:{{ $jColor }};font-size:0.72rem;padding:0.3rem 0.7rem;">
                                    {{ $jLabel }}
                                </span>
                            </td>
                            <td class="py-3">
                                @if($form->skim)
                                    <span class="badge rounded-pill" style="background:rgba(8,145,178,0.1);color:#0891B2;font-size:0.72rem;padding:0.3rem 0.65rem;">{{ $form->skim }}</span>
                                @else
                                    <span style="color:var(--text-400);font-size:0.82rem;">Semua Skim</span>
                                @endif
                            </td>
                            <td class="py-3" style="color:var(--text-600);font-size:0.82rem;">{{ $form->tahun_ajaran ?? '—' }}</td>
                            <td class="py-3">
                                <span class="fw-semibold" style="color:var(--text-900);">{{ $fieldCount }}</span>
                                <span style="color:var(--text-400);font-size:0.78rem;"> field</span>
                            </td>
                            <td class="py-3">
                                @if($form->is_active)
                                    <span class="badge rounded-pill" style="background:rgba(5,150,105,0.1);color:#059669;font-size:0.72rem;padding:0.3rem 0.7rem;">
                                        <i class="fas fa-circle" style="font-size:0.45rem;vertical-align:middle;margin-right:3px;"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge rounded-pill" style="background:rgba(107,114,128,0.1);color:#6B7280;font-size:0.72rem;padding:0.3rem 0.7rem;">
                                        <i class="fas fa-circle" style="font-size:0.45rem;vertical-align:middle;margin-right:3px;"></i>Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <span class="badge rounded-pill bg-secondary">Read Only</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div style="width:56px;height:56px;border-radius:14px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                                    <i class="fas fa-file-alt" style="font-size:1.4rem;color:var(--text-400);"></i>
                                </div>
                                <div class="fw-semibold mb-1" style="color:var(--text-900);">Belum Ada Form</div>
                                <p style="color:var(--text-600);font-size:0.875rem;">Buat form penilaian pertama untuk digunakan reviewer.</p>
                                <a href="{{ route('pimpinan_pt.form.penilaian.create') }}" class="btn btn-sm fw-semibold text-white" style="background:var(--primary-700);border-radius:8px;">
                                    <i class="fas fa-plus me-1"></i>Buat Form
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center py-3">
                {{ $formPenilaians->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
