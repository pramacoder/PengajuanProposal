@extends('operator.layout')

@section('title', 'Tambah Laporan SIMBELMAWA')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('operator.dashboard')],
        ['label' => 'Laporan SIMBELMAWA', 'url' => route('operator.laporan.simbelmawa.index')],
        ['label' => 'Tambah Laporan', 'active' => true],
    ]" />

    <div class="mb-4">
        <a href="{{ route('operator.laporan.simbelmawa.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card card-custom">
        <div class="card-header card-header-custom">
            <h5 class="mb-0">
                <i class="fas fa-plus me-2"></i>Tambah Laporan SIMBELMAWA
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('operator.laporan.simbelmawa.store') }}" method="POST" id="formLaporanCreate">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="tahun_ajaran" class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('tahun_ajaran') is-invalid @enderror" id="tahun_ajaran" name="tahun_ajaran" value="{{ old('tahun_ajaran') }}" required maxlength="20" placeholder="Contoh: 2024/2025">
                        @error('tahun_ajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="jumlah_proposal_tervalidasi_pimpinan_pt" class="form-label">Jumlah Proposal Tervalidasi Pimpinan PT <span class="text-danger">*</span></label>
                        <input type="text" class="form-control js-format-id-int @error('jumlah_proposal_tervalidasi_pimpinan_pt') is-invalid @enderror" id="jumlah_proposal_tervalidasi_pimpinan_pt" name="jumlah_proposal_tervalidasi_pimpinan_pt" value="{{ old('jumlah_proposal_tervalidasi_pimpinan_pt', 0) }}" inputmode="numeric" required>
                        @error('jumlah_proposal_tervalidasi_pimpinan_pt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="jumlah_proposal_dapat_pendanaan" class="form-label">Jumlah Proposal Dapat Pendanaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control js-format-id-int @error('jumlah_proposal_dapat_pendanaan') is-invalid @enderror" id="jumlah_proposal_dapat_pendanaan" name="jumlah_proposal_dapat_pendanaan" value="{{ old('jumlah_proposal_dapat_pendanaan', 0) }}" inputmode="numeric" required>
                        @error('jumlah_proposal_dapat_pendanaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="total_dana_pendanaan" class="form-label">Total Dana Pendanaan (Rp) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control js-format-id-int @error('total_dana_pendanaan') is-invalid @enderror" id="total_dana_pendanaan" name="total_dana_pendanaan" value="{{ old('total_dana_pendanaan', 0) }}" inputmode="numeric" required>
                        @error('total_dana_pendanaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="jumlah_proposal_lolos_pimnas" class="form-label">Jumlah Proposal Lolos PIMNAS <span class="text-danger">*</span></label>
                        <input type="text" class="form-control js-format-id-int @error('jumlah_proposal_lolos_pimnas') is-invalid @enderror" id="jumlah_proposal_lolos_pimnas" name="jumlah_proposal_lolos_pimnas" value="{{ old('jumlah_proposal_lolos_pimnas', 0) }}" inputmode="numeric" required>
                        @error('jumlah_proposal_lolos_pimnas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="jumlah_prestasi" class="form-label">Jumlah Prestasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control js-format-id-int @error('jumlah_prestasi') is-invalid @enderror" id="jumlah_prestasi" name="jumlah_prestasi" value="{{ old('jumlah_prestasi', 0) }}" inputmode="numeric" required>
                        @error('jumlah_prestasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="judul_proposal_lolos_pimnas" class="form-label">Judul Proposal Lolos PIMNAS (JSON)</label>
                        <textarea class="form-control font-monospace @error('judul_proposal_lolos_pimnas') is-invalid @enderror" id="judul_proposal_lolos_pimnas" name="judul_proposal_lolos_pimnas" rows="4" placeholder='["Judul 1", "Judul 2"]'>{{ old('judul_proposal_lolos_pimnas') }}</textarea>
                        @error('judul_proposal_lolos_pimnas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Opsional. Format JSON array string, contoh: ["Judul 1", "Judul 2"]</small>
                    </div>
                    <div class="col-12">
                        <label for="prestasi" class="form-label">Prestasi (JSON)</label>
                        <textarea class="form-control font-monospace @error('prestasi') is-invalid @enderror" id="prestasi" name="prestasi" rows="4" placeholder='[{"nama": "Prestasi 1", "tahun": "2024"}]'>{{ old('prestasi') }}</textarea>
                        @error('prestasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Opsional. Format JSON array of objects.</small>
                    </div>
                </div>
                <hr>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="btnSubmitLaporanCreate">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                    <a href="{{ route('operator.laporan.simbelmawa.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('formLaporanCreate')?.addEventListener('submit', function (event) {
        const jsonFields = [
            { id: 'judul_proposal_lolos_pimnas', label: 'Judul Proposal Lolos PIMNAS' },
            { id: 'prestasi', label: 'Prestasi' },
        ];

        for (const fieldConfig of jsonFields) {
            const field = document.getElementById(fieldConfig.id);
            if (!field?.value?.trim()) continue;
            try {
                JSON.parse(field.value);
            } catch (error) {
                event.preventDefault();
                window.AppUI?.showToast?.(`Format JSON pada ${fieldConfig.label} tidak valid.`, 'error');
                field.focus();
                return;
            }
        }

        const submitBtn = document.getElementById('btnSubmitLaporanCreate');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
    });
</script>
@endsection
