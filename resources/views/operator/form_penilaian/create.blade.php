@extends('operator.layout')

@section('title', 'Tambah Form Penilaian')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('operator.dashboard')],
        ['label' => 'Form Penilaian', 'url' => route('operator.form.penilaian.index')],
        ['label' => 'Tambah Form', 'active' => true],
    ]" />

    <div class="mb-4">
        <a href="{{ route('operator.form.penilaian.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">
                <i class="fas fa-plus me-2"></i>Tambah Form Penilaian
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('operator.form.penilaian.store') }}" method="POST" id="formPenilaianCreate">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama_form" class="form-label">Nama Form <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_form') is-invalid @enderror" id="nama_form" name="nama_form" value="{{ old('nama_form') }}" required maxlength="255">
                        @error('nama_form')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="jenis_form" class="form-label">Jenis Form <span class="text-danger">*</span></label>
                        <select class="form-select @error('jenis_form') is-invalid @enderror" id="jenis_form" name="jenis_form" required>
                            <option value="">-- Pilih --</option>
                            <option value="administratif" {{ old('jenis_form') === 'administratif' ? 'selected' : '' }}>Administratif</option>
                            <option value="substantif" {{ old('jenis_form') === 'substantif' ? 'selected' : '' }}>Substantif</option>
                            <option value="substantif_seleksi" {{ old('jenis_form') === 'substantif_seleksi' ? 'selected' : '' }}>Substantif Seleksi</option>
                            <option value="semi_final" {{ old('jenis_form') === 'semi_final' ? 'selected' : '' }}>Semi Final</option>
                            <option value="final" {{ old('jenis_form') === 'final' ? 'selected' : '' }}>Final</option>
                        </select>
                        @error('jenis_form')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="skim" class="form-label">Skim</label>
                        <select class="form-select @error('skim') is-invalid @enderror" id="skim" name="skim">
                            <option value="">-- Kosongkan jika umum --</option>
                            <option value="RE" {{ old('skim') === 'RE' ? 'selected' : '' }}>RE</option>
                            <option value="RSH" {{ old('skim') === 'RSH' ? 'selected' : '' }}>RSH</option>
                            <option value="KC" {{ old('skim') === 'KC' ? 'selected' : '' }}>KC</option>
                            <option value="PM" {{ old('skim') === 'PM' ? 'selected' : '' }}>PM</option>
                            <option value="PI" {{ old('skim') === 'PI' ? 'selected' : '' }}>PI</option>
                            <option value="PK" {{ old('skim') === 'PK' ? 'selected' : '' }}>PK</option>
                            <option value="GT" {{ old('skim') === 'GT' ? 'selected' : '' }}>GT</option>
                            <option value="GFK" {{ old('skim') === 'GFK' ? 'selected' : '' }}>GFK</option>
                            <option value="VGK" {{ old('skim') === 'VGK' ? 'selected' : '' }}>VGK</option>
                            <option value="KI" {{ old('skim') === 'KI' ? 'selected' : '' }}>KI</option>
                        </select>
                        @error('skim')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="tahun_ajaran" class="form-label">Tahun Ajaran</label>
                        <input type="text" class="form-control @error('tahun_ajaran') is-invalid @enderror" id="tahun_ajaran" name="tahun_ajaran" value="{{ old('tahun_ajaran') }}" maxlength="20" placeholder="Contoh: 2024/2025">
                        @error('tahun_ajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="config" class="form-label">Config (JSON)</label>
                        <textarea class="form-control font-monospace @error('config') is-invalid @enderror" id="config" name="config" rows="6" placeholder='{"kategori": [], "kriteria": []}'>{{ old('config') }}</textarea>
                        @error('config')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Opsional. Format JSON untuk konfigurasi form.</small>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="btnSubmitFormPenilaianCreate">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                    <a href="{{ route('operator.form.penilaian.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('formPenilaianCreate')?.addEventListener('submit', function (event) {
        const configField = document.getElementById('config');
        if (configField?.value?.trim()) {
            try {
                JSON.parse(configField.value);
            } catch (error) {
                event.preventDefault();
                window.AppUI?.showToast?.('Format JSON pada Config tidak valid.', 'error');
                configField.focus();
                return;
            }
        }

        const submitBtn = document.getElementById('btnSubmitFormPenilaianCreate');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
    });
</script>
@endsection
