@extends('operator.layout')

@section('title', 'Form Penilaian')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('operator.dashboard')],
        ['label' => 'Form Penilaian', 'active' => true],
    ]" />

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fas fa-file-alt me-2"></i>Form Penilaian
        </h4>
        <a href="{{ route('operator.form.penilaian.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>Tambah Form
        </a>
    </div>

    <!-- Filters -->
    <div class="card card-custom mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('operator.form.penilaian.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Jenis Form</label>
                    <select name="jenis_form" class="form-select">
                        <option value="">-- Semua --</option>
                        <option value="administratif" {{ request('jenis_form') === 'administratif' ? 'selected' : '' }}>Administratif</option>
                        <option value="substantif" {{ request('jenis_form') === 'substantif' ? 'selected' : '' }}>Substantif</option>
                        <option value="substantif_seleksi" {{ request('jenis_form') === 'substantif_seleksi' ? 'selected' : '' }}>Substantif Seleksi</option>
                        <option value="semi_final" {{ request('jenis_form') === 'semi_final' ? 'selected' : '' }}>Semi Final</option>
                        <option value="final" {{ request('jenis_form') === 'final' ? 'selected' : '' }}>Final</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Skim</label>
                    <select name="skim" class="form-select">
                        <option value="">-- Semua --</option>
                        <option value="RE" {{ request('skim') === 'RE' ? 'selected' : '' }}>RE</option>
                        <option value="RSH" {{ request('skim') === 'RSH' ? 'selected' : '' }}>RSH</option>
                        <option value="KC" {{ request('skim') === 'KC' ? 'selected' : '' }}>KC</option>
                        <option value="PM" {{ request('skim') === 'PM' ? 'selected' : '' }}>PM</option>
                        <option value="PI" {{ request('skim') === 'PI' ? 'selected' : '' }}>PI</option>
                        <option value="PK" {{ request('skim') === 'PK' ? 'selected' : '' }}>PK</option>
                        <option value="GT" {{ request('skim') === 'GT' ? 'selected' : '' }}>GT</option>
                        <option value="GFK" {{ request('skim') === 'GFK' ? 'selected' : '' }}>GFK</option>
                        <option value="VGK" {{ request('skim') === 'VGK' ? 'selected' : '' }}>VGK</option>
                        <option value="KI" {{ request('skim') === 'KI' ? 'selected' : '' }}>KI</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-outline-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('operator.form.penilaian.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Daftar Form Penilaian
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Form</th>
                            <th>Jenis Form</th>
                            <th>Skim</th>
                            <th>Tahun Ajaran</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($formPenilaians as $index => $form)
                        <tr>
                            <td>{{ $formPenilaians->firstItem() + $index }}</td>
                            <td>{{ $form->nama_form }}</td>
                            <td><span class="badge bg-secondary">{{ $form->jenis_form }}</span></td>
                            <td>{{ $form->skim ?? '-' }}</td>
                            <td>{{ $form->tahun_ajaran ?? '-' }}</td>
                            <td>
                                @if($form->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $form->creator->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('operator.form.penilaian.edit', $form->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('operator.form.penilaian.destroy', $form->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus form ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Belum ada data form penilaian.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">
                {{ $formPenilaians->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
