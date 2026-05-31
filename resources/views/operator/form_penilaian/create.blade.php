@extends('mainlayout.app')

@section('title', 'Buat Form Penilaian')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda',       'url' => route('operator.dashboard')],
        ['label' => 'Form Penilaian','url' => route('operator.form.penilaian.index')],
        ['label' => 'Buat Form',     'active' => true],
    ]" />

    <div class="mb-3">
        <a href="{{ route('operator.form.penilaian.index') }}"
           class="btn btn-sm" style="border:1px solid var(--border);border-radius:8px;font-size:0.82rem;color:var(--text-600);">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="row g-4">
        {{-- Left: Form metadata --}}
        <div class="col-lg-5">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <i class="fas fa-info-circle"></i>
                    <span>Informasi Form</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('operator.form.penilaian.store') }}" method="POST" id="formPenilaianCreate">
                        @csrf

                        {{-- Nama Form --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-700);">
                                Nama Form <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control form-control-sm @error('nama_form') is-invalid @enderror"
                                   name="nama_form" value="{{ old('nama_form') }}"
                                   placeholder="contoh: Penilaian Substantif PKM-RE 2025"
                                   style="border-radius:8px;" required maxlength="255">
                            @error('nama_form')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Penempatan --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-700);">
                                Penempatan Form <span class="text-danger">*</span>
                            </label>
                            <select name="jenis_form" class="form-select form-select-sm @error('jenis_form') is-invalid @enderror"
                                    style="border-radius:8px;" required>
                                <option value="">— Pilih —</option>
                                <option value="administratif"      {{ old('jenis_form')==='administratif'?'selected':'' }}>📋 Reviewer Administratif</option>
                                <option value="substantif"         {{ old('jenis_form')==='substantif'?'selected':'' }}>🔬 Reviewer Substantif</option>
                                <option value="substantif_seleksi" {{ old('jenis_form')==='substantif_seleksi'?'selected':'' }}>🔎 Reviewer Substantif Seleksi</option>
                                <option value="semi_final"         {{ old('jenis_form')==='semi_final'?'selected':'' }}>🏅 Hasil Semi Final (Operator)</option>
                                <option value="final"              {{ old('jenis_form')==='final'?'selected':'' }}>🏆 Hasil Final (Operator)</option>
                            </select>
                            @error('jenis_form')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="mt-1" style="font-size:0.75rem;color:var(--text-400);">Pilih di bagian mana form ini akan ditampilkan.</div>
                        </div>

                        {{-- Skim --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-700);">Skim</label>
                            <select name="skim" class="form-select form-select-sm @error('skim') is-invalid @enderror" style="border-radius:8px;">
                                <option value="">— Kosongkan untuk semua skim —</option>
                                @foreach(['RE','RSH','KC','PM','PI','K','KI','VGK','AI','GFT'] as $s)
                                    <option value="{{ $s }}" {{ old('skim')===$s?'selected':'' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                            @error('skim')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Tahun Ajaran --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-700);">Tahun Ajaran</label>
                            <input type="text" class="form-control form-control-sm @error('tahun_ajaran') is-invalid @enderror"
                                   name="tahun_ajaran" value="{{ old('tahun_ajaran') }}"
                                   placeholder="contoh: 2024/2025" style="border-radius:8px;" maxlength="20">
                            @error('tahun_ajaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Status --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded" style="background:var(--surface-2);border:1px solid var(--border);">
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }} style="cursor:pointer;">
                                    <label class="form-check-label fw-semibold" for="is_active" style="font-size:0.82rem;color:var(--text-700);cursor:pointer;">
                                        Form Aktif
                                    </label>
                                </div>
                                <span style="font-size:0.75rem;color:var(--text-400);">Form aktif akan digunakan oleh reviewer</span>
                            </div>
                        </div>

                        {{-- Hidden field for builder output --}}
                        <input type="hidden" name="fields" id="fieldsJson">

                        {{-- Buttons --}}
                        <div class="d-flex gap-2">
                            <button type="submit" id="btnSubmit"
                                    class="btn fw-bold text-white flex-fill"
                                    style="background:linear-gradient(135deg,var(--primary-900),var(--primary-700));border-radius:10px;font-size:0.875rem;padding:0.6rem 1.25rem;box-shadow:0 4px 12px rgba(143,11,19,0.25);">
                                <i class="fas fa-save me-2"></i>Simpan Form
                            </button>
                            <a href="{{ route('operator.form.penilaian.index') }}"
                               class="btn" style="border:1px solid var(--border);border-radius:10px;font-size:0.875rem;padding:0.6rem 1rem;color:var(--text-600);">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Visual Field Builder --}}
        <div class="col-lg-7">
            <div class="card card-custom">
                <div class="card-header card-header-custom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-layer-group"></i>
                        <span>Field Builder</span>
                    </div>
                    <button type="button" id="btnAddField"
                            class="btn btn-sm fw-semibold"
                            style="background:var(--primary-100);color:var(--primary-700);border-radius:8px;padding:0.3rem 0.85rem;font-size:0.8rem;">
                        <i class="fas fa-plus me-1"></i>Tambah Field
                    </button>
                </div>
                <div class="card-body">

                    {{-- Field type legend --}}
                    <div class="d-flex gap-2 mb-3 flex-wrap">
                        <div class="d-flex align-items-center gap-1 px-3 py-1 rounded" style="background:rgba(99,102,241,0.08);font-size:0.75rem;color:#6366F1;">
                            <i class="fas fa-align-left"></i> Textarea — teks panjang / catatan
                        </div>
                        <div class="d-flex align-items-center gap-1 px-3 py-1 rounded" style="background:rgba(8,145,178,0.08);font-size:0.75rem;color:#0891B2;">
                            <i class="fas fa-sort-numeric-up"></i> Integer 1-7 — skala penilaian PKM
                        </div>
                    </div>

                    {{-- Field list --}}
                    <div id="fieldList" class="d-flex flex-column gap-3">
                        <div id="emptyState" class="text-center py-4" style="color:var(--text-400);">
                            <i class="fas fa-layer-group mb-2" style="font-size:2rem;opacity:0.3;"></i>
                            <p class="mb-0" style="font-size:0.85rem;">Klik tombol <strong>+ Tambah Field</strong> untuk mulai membangun form</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Preview --}}
            <div class="card card-custom mt-3" id="previewCard" style="display:none!important;">
                <div class="card-header card-header-custom">
                    <i class="fas fa-eye"></i>
                    <span>Preview Form</span>
                </div>
                <div class="card-body" id="previewBody"></div>
            </div>
        </div>
    </div>
</div>

{{-- Field Row Template --}}
<template id="fieldRowTemplate">
    <div class="field-row p-3 rounded position-relative" style="background:var(--surface-2);border:1px solid var(--border);" data-index="">
        <div class="row g-2 align-items-start">
            <div class="col-md-5">
                <label class="form-label mb-1 fw-semibold" style="font-size:0.75rem;color:var(--text-600);">Label Field <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm field-label" placeholder="contoh: Catatan untuk Mahasiswa"
                       style="border-radius:8px;" required>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 fw-semibold" style="font-size:0.75rem;color:var(--text-600);">Tipe Input <span class="text-danger">*</span></label>
                <select class="form-select form-select-sm field-type" style="border-radius:8px;">
                    <option value="textarea">📝 Textarea (teks)</option>
                    <option value="integer_scale">🔢 Integer 1-7</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1 fw-semibold" style="font-size:0.75rem;color:var(--text-600);">Deskripsi / Petunjuk</label>
                <input type="text" class="form-control form-control-sm field-description" placeholder="(opsional)"
                       style="border-radius:8px;">
            </div>
            <div class="col-md-1 d-flex align-items-end pb-1">
                <div class="form-check form-switch mb-0" title="Wajib diisi">
                    <input class="form-check-input field-required" type="checkbox" checked style="cursor:pointer;">
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-2">
            <span class="field-index-badge" style="font-size:0.72rem;color:var(--text-400);">Field #<span class="fn"></span></span>
            <button type="button" class="btn-remove-field btn btn-sm"
                    style="background:rgba(220,38,38,0.08);color:#DC2626;border-radius:6px;padding:0.2rem 0.6rem;font-size:0.75rem;">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</template>

@endsection

@section('scripts')
<script>
(function() {
    let fields = [];

    const fieldList   = document.getElementById('fieldList');
    const emptyState  = document.getElementById('emptyState');
    const fieldsJson  = document.getElementById('fieldsJson');
    const previewCard = document.getElementById('previewCard');
    const previewBody = document.getElementById('previewBody');
    const template    = document.getElementById('fieldRowTemplate');

    function rebuildIndexes() {
        document.querySelectorAll('.field-row').forEach((row, i) => {
            row.dataset.index = i;
            row.querySelector('.fn').textContent = i + 1;
        });
    }

    function collectFields() {
        const rows = document.querySelectorAll('.field-row');
        fields = Array.from(rows).map(row => ({
            label:       row.querySelector('.field-label').value.trim(),
            type:        row.querySelector('.field-type').value,
            description: row.querySelector('.field-description').value.trim(),
            required:    row.querySelector('.field-required').checked,
        }));
        fieldsJson.value = JSON.stringify(fields);
        updatePreview();
    }

    function updatePreview() {
        if (fields.length === 0) {
            previewCard.style.display = 'none';
            return;
        }
        previewCard.style.removeProperty('display');
        previewBody.innerHTML = fields.map((f, i) => {
            const required = f.required ? '<span class="text-danger ms-1">*</span>' : '';
            const desc     = f.description ? `<div style="font-size:0.75rem;color:var(--text-400);margin-bottom:4px;">${f.description}</div>` : '';
            if (f.type === 'textarea') {
                return `<div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-700);">${f.label}${required}</label>
                    ${desc}
                    <textarea class="form-control form-control-sm" rows="3" disabled style="border-radius:8px;background:var(--surface-2);" placeholder="Reviewer akan mengisi di sini..."></textarea>
                </div>`;
            } else {
                const btns = [1,2,3,4,5,6,7].map(n =>
                    `<button type="button" class="btn btn-sm" style="width:36px;height:36px;border-radius:8px;margin:2px;background:#f3f4f6;color:var(--text-700);font-weight:600;font-size:0.82rem;" disabled>${n}</button>`
                ).join('');
                return `<div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:0.82rem;color:var(--text-700);">${f.label}${required}</label>
                    ${desc}
                    <div class="d-flex flex-wrap gap-1 mt-1">${btns}</div>
                    <div style="font-size:0.72rem;color:var(--text-400);margin-top:4px;">Skala 1-7 (1=sangat kurang, 7=sangat baik)</div>
                </div>`;
            }
        }).join('');
    }

    function addField() {
        const clone = template.content.cloneNode(true);
        const row   = clone.querySelector('.field-row');
        const idx   = document.querySelectorAll('.field-row').length;
        row.dataset.index = idx;
        row.querySelector('.fn').textContent = idx + 1;

        // Remove btn
        row.querySelector('.btn-remove-field').addEventListener('click', function() {
            row.remove();
            if (!document.querySelectorAll('.field-row').length) {
                emptyState.style.display = '';
            }
            rebuildIndexes();
            collectFields();
        });

        // Live update on any change
        row.querySelectorAll('input,select,textarea').forEach(el => {
            el.addEventListener('input', collectFields);
            el.addEventListener('change', collectFields);
        });

        fieldList.appendChild(clone);
        emptyState.style.display = 'none';
        collectFields();
        rebuildIndexes();
    }

    document.getElementById('btnAddField').addEventListener('click', addField);

    // Form submit validation
    document.getElementById('formPenilaianCreate').addEventListener('submit', function(e) {
        collectFields();
        const rows = document.querySelectorAll('.field-row');
        for (const row of rows) {
            if (!row.querySelector('.field-label').value.trim()) {
                e.preventDefault();
                row.querySelector('.field-label').focus();
                alert('Semua field harus memiliki label.');
                return;
            }
        }
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    });
})();
</script>
@endsection
