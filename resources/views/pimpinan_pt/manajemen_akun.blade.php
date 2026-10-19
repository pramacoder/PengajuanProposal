@extends('mainlayout.app')

@section('title', 'Manajemen Akun - Pimpinan PT')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('pimpinan_pt.dashboard')],
        ['label' => 'Manajemen Akun', 'active' => true],
    ]" />

    <x-page-header 
        title="Manajemen Akun Pengguna" 
        subtitle="Kelola kredensial dan hak akses akun mahasiswa, dosen, reviewer, dan operator sistem"
        badge="Pimpinan PT Workspace"
        icon="fa-users-cog"
    />

    {{-- Main Container Card --}}
    <div class="ui-card mb-4">
        <div class="ui-card-header p-3 border-bottom bg-light">
            <ul class="nav nav-pills gap-2" id="accountTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" id="mahasiswa-tab" data-bs-toggle="tab" data-bs-target="#mahasiswa" type="button" role="tab">
                        <i class="fas fa-user-graduate"></i>
                        <span>Mahasiswa</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" id="dosen-tab" data-bs-toggle="tab" data-bs-target="#dosen" type="button" role="tab">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span>Dosen</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" id="reviewer-tab" data-bs-toggle="tab" data-bs-target="#reviewer" type="button" role="tab">
                        <i class="fas fa-user-check"></i>
                        <span>Reviewer</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" id="operator-tab" data-bs-toggle="tab" data-bs-target="#operator" type="button" role="tab">
                        <i class="fas fa-user-shield"></i>
                        <span>Operator</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="ui-card-body p-4">
            <div class="tab-content" id="accountTabContent">
                
                {{-- TAB: MAHASISWA --}}
                <div class="tab-pane fade show active" id="mahasiswa" role="tabpanel">
                    {{-- Form Tambah Mahasiswa --}}
                    <div class="card border rounded-3 mb-4 shadow-none bg-light-subtle">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-user-plus text-primary"></i>
                                <h6 class="mb-0 fw-bold fs-6 text-dark">Tambah Akun Mahasiswa Baru</h6>
                            </div>
                            <button class="btn btn-sm btn-link text-decoration-none text-muted p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFormMahasiswa">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                        <div class="collapse show" id="collapseFormMahasiswa">
                            <div class="card-body p-4 bg-white">
                                <form class="row g-3" method="POST" action="{{ route('pimpinan_pt.accounts.store', 'mahasiswa') }}">
                                    @csrf
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                                        <input type="text" name="nama" class="form-control form-control-sm rounded-3" placeholder="Contoh: I Made Sudarma" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-secondary">NIM <span class="text-danger">*</span></label>
                                        <input type="text" name="nim" class="form-control form-control-sm rounded-3" placeholder="Contoh: 2105551001" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-secondary">Email Kampus <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control form-control-sm rounded-3" placeholder="Contoh: mhs@student.unud.ac.id" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-secondary">Nomor WhatsApp/HP <span class="text-danger">*</span></label>
                                        <input type="text" name="no_hp" class="form-control form-control-sm rounded-3" placeholder="08xxxxxxxxxx" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-secondary">Fakultas <span class="text-danger">*</span></label>
                                        <select name="fakultas" id="fakultasSelect" class="form-select form-select-sm rounded-3" required>
                                            <option value="">Pilih Fakultas</option>
                                            @foreach($fakultas as $f)
                                                <option value="{{ $f->id_fakultas }}">{{ $f->nama_fakultas }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-secondary">Program Studi <span class="text-danger">*</span></label>
                                        <select name="prodi" id="prodiSelect" class="form-select form-select-sm rounded-3" required>
                                            <option value="">Pilih Prodi</option>
                                            @foreach($prodis as $p)
                                                <option value="{{ $p->id_prodi }}">{{ $p->nama_prodi }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-secondary">Kata Sandi <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control form-control-sm rounded-3" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-semibold text-secondary">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control form-control-sm rounded-3" required>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-sm btn-primary w-100 rounded-3 py-2 fw-semibold">
                                            <i class="fas fa-plus me-1"></i> Simpan Akun Mahasiswa
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Section --}}
                    <div class="filter-card mb-4">
                        <form method="GET" action="{{ route('pimpinan_pt.manage.accounts') }}" id="filterForm">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Cari Nama</label>
                                    <input type="text" name="filter_nama" id="filterNama" class="form-control form-control-sm rounded-3" placeholder="Ketik nama mahasiswa..." value="{{ request('filter_nama') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Fakultas</label>
                                    <select name="filter_fakultas" id="filterFakultas" class="form-select form-select-sm rounded-3">
                                        <option value="">Semua Fakultas</option>
                                        @foreach($fakultas as $f)
                                            <option value="{{ $f->id_fakultas }}" {{ request('filter_fakultas') == $f->id_fakultas ? 'selected' : '' }}>{{ $f->nama_fakultas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Program Studi</label>
                                    <select name="filter_prodi" id="filterProdi" class="form-select form-select-sm rounded-3">
                                        <option value="">Semua Prodi</option>
                                        @foreach($prodis as $p)
                                            <option value="{{ $p->id_prodi }}" {{ request('filter_prodi') == $p->id_prodi ? 'selected' : '' }}>{{ $p->nama_prodi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary mb-1">NIM</label>
                                    <input type="text" name="filter_nim" id="filterNim" class="form-control form-control-sm rounded-3" placeholder="Ketik NIM..." value="{{ request('filter_nim') }}">
                                </div>
                                <div class="col-12 d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3 fw-semibold">
                                        <i class="fas fa-search me-1"></i> Terapkan Filter
                                    </button>
                                    <a href="{{ route('pimpinan_pt.manage.accounts') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Bulk Actions & Table --}}
                    <form id="bulkDeleteForm" method="POST" action="{{ route('pimpinan_pt.accounts.mahasiswa.bulk-delete') }}" onsubmit="return confirm('Yakin ingin menghapus seluruh akun mahasiswa terpilih?')">
                        @csrf
                        <div class="mb-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <button type="submit" id="bulkDeleteBtn" class="btn btn-sm btn-danger rounded-3 fw-semibold px-3" disabled>
                                    <i class="fas fa-trash me-1"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
                                </button>
                            </div>
                            <div class="text-muted small">
                                Total Ditemukan: <strong class="text-dark">@formatId($mahasiswas->count())</strong> mahasiswa
                            </div>
                        </div>

                        @if(!isset($hasFilter) || !$hasFilter)
                            <div class="empty-state-box text-center py-5 border rounded-3 bg-light">
                                <div class="empty-state-icon mb-3">
                                    <i class="fas fa-filter fa-3x text-muted opacity-50"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Data Belum Ditampilkan</h6>
                                <p class="text-muted small mb-0">Silakan gunakan filter pencarian di atas untuk memuat daftar data mahasiswa secara terarah.</p>
                            </div>
                        @else
                            <div class="table-responsive rounded-3 border">
                                <table class="table-modern mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width: 44px;" class="text-center">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th style="width: 120px;">NIM</th>
                                            <th style="min-width: 180px;">Nama Mahasiswa</th>
                                            <th>Email</th>
                                            <th>Prodi</th>
                                            <th>Fakultas</th>
                                            <th class="text-center" style="width: 90px;">Status</th>
                                            <th class="text-center" style="width: 100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($mahasiswas as $m)
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" name="ids[]" value="{{ $m->id_mahasiswa }}" class="form-check-input mahasiswa-checkbox">
                                            </td>
                                            <td class="fw-semibold text-secondary">{{ $m->nim }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $m->nama_mhs }}</div>
                                                <small class="text-muted" style="font-size: 0.75rem;">HP: {{ $m->no_hp_mhs ?? '-' }}</small>
                                            </td>
                                            <td class="small">{{ $m->email_mhs }}</td>
                                            <td class="small">{{ $m->prodi_mhs }}</td>
                                            <td class="small">{{ $m->fakultas_mhs }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $m->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary' }} px-2.5 py-1 rounded-pill small">
                                                    {{ $m->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary edit-mahasiswa-btn" 
                                                            data-id="{{ $m->id_mahasiswa }}"
                                                            data-nama="{{ $m->nama_mhs }}"
                                                            data-nim="{{ $m->nim }}"
                                                            data-email="{{ $m->email_mhs }}"
                                                            data-no-hp="{{ $m->no_hp_mhs }}"
                                                            data-fakultas="{{ $m->fakultas_mhs }}"
                                                            data-prodi="{{ $m->prodi_mhs }}"
                                                            data-is-active="{{ $m->is_active ? '1' : '0' }}"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editMahasiswaModal"
                                                            title="Edit Mahasiswa">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="if(confirm('Hapus akun mahasiswa ini?')) document.getElementById('del-mhs-{{ $m->id_mahasiswa }}').submit();"
                                                            title="Hapus Mahasiswa">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                                Tidak ada data mahasiswa ditemukan dengan filter saat ini.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </form>
                    {{-- Hidden delete forms for Mahasiswa --}}
                    @if(isset($mahasiswas) && $mahasiswas->count() > 0)
                        @foreach($mahasiswas as $m)
                            <form id="del-mhs-{{ $m->id_mahasiswa }}" method="POST" action="{{ route('pimpinan_pt.accounts.delete', ['type' => 'mahasiswa', 'id' => $m->id_mahasiswa]) }}" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                    @endif
                </div>

                {{-- TAB: DOSEN --}}
                <div class="tab-pane fade" id="dosen" role="tabpanel">
                    {{-- Filter Section Dosen --}}
                    <div class="filter-card mb-4">
                        <form method="GET" action="{{ route('pimpinan_pt.manage.accounts') }}" id="filterDosenForm">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Cari Nama / NUPTK Dosen</label>
                                    <input type="text" name="filter_nama_dosen" id="filterNamaDosen" class="form-control form-control-sm rounded-3" placeholder="Ketik nama atau NUPTK..." value="{{ request('filter_nama_dosen') }}">
                                </div>
                                <div class="col-md-6 col-lg-8 d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3 fw-semibold">
                                        <i class="fas fa-search me-1"></i> Terapkan Filter
                                    </button>
                                    <a href="{{ route('pimpinan_pt.manage.accounts') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if(!isset($hasFilter) || !$hasFilter)
                        <div class="empty-state-box text-center py-5 border rounded-3 bg-light">
                            <div class="empty-state-icon mb-3">
                                <i class="fas fa-filter fa-3x text-muted opacity-50"></i>
                            </div>
                            <h6 class="fw-bold text-dark">Data Belum Ditampilkan</h6>
                            <p class="text-muted small mb-0">Silakan gunakan filter di atas untuk menampilkan data dosen.</p>
                        </div>
                    @else
                        {{-- Form Tambah Dosen --}}
                        <div class="card border rounded-3 mb-4 shadow-none bg-light-subtle">
                            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-user-plus text-primary"></i>
                                    <h6 class="mb-0 fw-bold fs-6 text-dark">Tambah Akun Dosen Baru</h6>
                                </div>
                                <button class="btn btn-sm btn-link text-decoration-none text-muted p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFormDosen">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="collapse show" id="collapseFormDosen">
                                <div class="card-body p-4 bg-white">
                                    <form class="row g-3" method="POST" action="{{ route('pimpinan_pt.accounts.store', 'dosen') }}">
                                        @csrf
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" name="nama" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">NUPTK <span class="text-danger">*</span></label>
                                            <input type="text" name="nuptk" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">No HP <span class="text-danger">*</span></label>
                                            <input type="text" name="no_hp" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">Kata Sandi <span class="text-danger">*</span></label>
                                            <input type="password" name="password" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                                            <input type="password" name="password_confirmation" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-sm btn-primary rounded-3 px-3 fw-semibold">
                                                <i class="fas fa-plus me-1"></i> Tambah Dosen
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Table Dosen --}}
                        <div class="table-responsive rounded-3 border">
                            <table class="table-modern mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 140px;">NUPTK</th>
                                        <th style="min-width: 200px;">Nama Dosen</th>
                                        <th>Email</th>
                                        <th>No HP</th>
                                        <th class="text-center" style="width: 90px;">Status</th>
                                        <th class="text-center" style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dosens as $d)
                                    <tr>
                                        <td class="fw-semibold text-secondary">{{ $d->nuptk }}</td>
                                        <td class="fw-bold text-dark">{{ $d->nama_dosen }}</td>
                                        <td class="small">{{ $d->email_dosen }}</td>
                                        <td class="small">{{ $d->no_hp_dosen }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $d->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary' }} px-2.5 py-1 rounded-pill small">
                                                {{ $d->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary edit-dosen-btn" 
                                                        data-id="{{ $d->id_dosen }}"
                                                        data-nama="{{ $d->nama_dosen }}"
                                                        data-nuptk="{{ $d->nuptk }}"
                                                        data-email="{{ $d->email_dosen }}"
                                                        data-no-hp="{{ $d->no_hp_dosen }}"
                                                        data-is-active="{{ $d->is_active ? '1' : '0' }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editDosenModal"
                                                        title="Edit Dosen">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" action="{{ route('pimpinan_pt.accounts.delete', ['type' => 'dosen', 'id' => $d->id_dosen]) }}" onsubmit="return confirm('Hapus akun dosen ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- TAB: REVIEWER --}}
                <div class="tab-pane fade" id="reviewer" role="tabpanel">
                    {{-- Filter Section Reviewer --}}
                    <div class="filter-card mb-4">
                        <form method="GET" action="{{ route('pimpinan_pt.manage.accounts') }}" id="filterReviewerForm">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Cari Nama Reviewer</label>
                                    <input type="text" name="filter_nama_reviewer" id="filterNamaReviewer" class="form-control form-control-sm rounded-3" placeholder="Ketik nama reviewer..." value="{{ request('filter_nama_reviewer') }}">
                                </div>
                                <div class="col-md-6 col-lg-8 d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3 fw-semibold">
                                        <i class="fas fa-search me-1"></i> Terapkan Filter
                                    </button>
                                    <a href="{{ route('pimpinan_pt.manage.accounts') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if(!isset($hasFilter) || !$hasFilter)
                        <div class="empty-state-box text-center py-5 border rounded-3 bg-light">
                            <div class="empty-state-icon mb-3">
                                <i class="fas fa-filter fa-3x text-muted opacity-50"></i>
                            </div>
                            <h6 class="fw-bold text-dark">Data Belum Ditampilkan</h6>
                            <p class="text-muted small mb-0">Silakan gunakan filter di atas untuk menampilkan data reviewer.</p>
                        </div>
                    @else
                        {{-- Form Tambah Reviewer --}}
                        <div class="card border rounded-3 mb-4 shadow-none bg-light-subtle">
                            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-user-plus text-primary"></i>
                                    <h6 class="mb-0 fw-bold fs-6 text-dark">Tambah Akun Reviewer Baru</h6>
                                </div>
                                <button class="btn btn-sm btn-link text-decoration-none text-muted p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFormReviewer">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="collapse show" id="collapseFormReviewer">
                                <div class="card-body p-4 bg-white">
                                    <form class="row g-3" method="POST" action="{{ route('pimpinan_pt.accounts.store', 'reviewer') }}">
                                        @csrf
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                                            <input type="text" name="nama" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">No HP <span class="text-danger">*</span></label>
                                            <input type="text" name="no_hp" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">Kata Sandi <span class="text-danger">*</span></label>
                                            <input type="password" name="password" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                                            <input type="password" name="password_confirmation" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button class="btn btn-sm btn-primary w-100 rounded-3 py-2 fw-semibold">
                                                <i class="fas fa-plus me-1"></i> Tambah Reviewer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Table Reviewer --}}
                        <div class="table-responsive rounded-3 border">
                            <table class="table-modern mb-0">
                                <thead>
                                    <tr>
                                        <th style="min-width: 200px;">Nama Reviewer</th>
                                        <th>Email</th>
                                        <th>No HP</th>
                                        <th class="text-center" style="width: 90px;">Status</th>
                                        <th class="text-center" style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reviewers as $r)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $r->nama_reviewer }}</td>
                                        <td class="small">{{ $r->email_reviewer }}</td>
                                        <td class="small">{{ $r->no_hp_reviewer }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $r->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary' }} px-2.5 py-1 rounded-pill small">
                                                {{ $r->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary edit-reviewer-btn" 
                                                        data-id="{{ $r->id_reviewer }}"
                                                        data-nama="{{ $r->nama_reviewer }}"
                                                        data-email="{{ $r->email_reviewer }}"
                                                        data-no-hp="{{ $r->no_hp_reviewer }}"
                                                        data-is-active="{{ $r->is_active ? '1' : '0' }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editReviewerModal"
                                                        title="Edit Reviewer">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" action="{{ route('pimpinan_pt.accounts.delete', ['type' => 'reviewer', 'id' => $r->id_reviewer]) }}" onsubmit="return confirm('Hapus akun reviewer ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                {{-- TAB: OPERATOR --}}
                <div class="tab-pane fade" id="operator" role="tabpanel">
                    {{-- Filter Section Operator --}}
                    <div class="filter-card mb-4">
                        <form method="GET" action="{{ route('pimpinan_pt.manage.accounts') }}" id="filterOperatorForm">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-6 col-lg-4">
                                    <label class="form-label small fw-semibold text-secondary mb-1">Cari Nama Operator</label>
                                    <input type="text" name="filter_nama_operator" id="filterNamaOperator" class="form-control form-control-sm rounded-3" placeholder="Ketik nama operator..." value="{{ request('filter_nama_operator') }}">
                                </div>
                                <div class="col-md-6 col-lg-8 d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3 fw-semibold">
                                        <i class="fas fa-search me-1"></i> Terapkan Filter
                                    </button>
                                    <a href="{{ route('pimpinan_pt.manage.accounts') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if(!isset($hasFilter) || !$hasFilter)
                        <div class="empty-state-box text-center py-5 border rounded-3 bg-light">
                            <div class="empty-state-icon mb-3">
                                <i class="fas fa-filter fa-3x text-muted opacity-50"></i>
                            </div>
                            <h6 class="fw-bold text-dark">Data Belum Ditampilkan</h6>
                            <p class="text-muted small mb-0">Silakan gunakan filter di atas untuk menampilkan data operator.</p>
                        </div>
                    @else
                        {{-- Form Tambah Operator --}}
                        <div class="card border rounded-3 mb-4 shadow-none bg-light-subtle">
                            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-user-plus text-primary"></i>
                                    <h6 class="mb-0 fw-bold fs-6 text-dark">Tambah Akun Operator Baru</h6>
                                </div>
                                <button class="btn btn-sm btn-link text-decoration-none text-muted p-0" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFormOperator">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            <div class="collapse show" id="collapseFormOperator">
                                <div class="card-body p-4 bg-white">
                                    <form class="row g-3" method="POST" action="{{ route('pimpinan_pt.accounts.store', 'operator') }}">
                                        @csrf
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">Nama Operator <span class="text-danger">*</span></label>
                                            <input type="text" name="nama" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">No HP <span class="text-danger">*</span></label>
                                            <input type="text" name="no_hp" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">Kata Sandi <span class="text-danger">*</span></label>
                                            <input type="password" name="password" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-semibold text-secondary">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                                            <input type="password" name="password_confirmation" class="form-control form-control-sm rounded-3" required>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button class="btn btn-sm btn-primary w-100 rounded-3 py-2 fw-semibold">
                                                <i class="fas fa-plus me-1"></i> Tambah Operator
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Table Operator --}}
                        <div class="table-responsive rounded-3 border">
                            <table class="table-modern mb-0">
                                <thead>
                                    <tr>
                                        <th style="min-width: 200px;">Nama Operator</th>
                                        <th>Email</th>
                                        <th>No HP</th>
                                        <th class="text-center" style="width: 90px;">Status</th>
                                        <th class="text-center" style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($operators as $o)
                                    <tr>
                                        <td class="fw-bold text-dark">{{ $o->nama_pt }}</td>
                                        <td class="small">{{ $o->email_pt }}</td>
                                        <td class="small">{{ $o->no_hp_pt }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $o->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary' }} px-2.5 py-1 rounded-pill small">
                                                {{ $o->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary edit-operator-btn" 
                                                        data-id="{{ $o->id_pt }}"
                                                        data-nama="{{ $o->nama_pt }}"
                                                        data-email="{{ $o->email_pt }}"
                                                        data-no-hp="{{ $o->no_hp_pt }}"
                                                        data-is-active="{{ $o->is_active ? '1' : '0' }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editOperatorModal"
                                                        title="Edit Operator">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" action="{{ route('pimpinan_pt.accounts.delete', ['type' => 'operator', 'id' => $o->id_pt]) }}" onsubmit="return confirm('Hapus akun operator ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Mahasiswa -->
<div class="modal fade" id="editMahasiswaModal" tabindex="-1" aria-labelledby="editMahasiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editMahasiswaForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fs-6 fw-bold" id="editMahasiswaModalLabel">
                        <i class="fas fa-user-edit me-2"></i>Perbarui Akun Mahasiswa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_nama_mhs" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">NIM <span class="text-danger">*</span></label>
                            <input type="text" name="nim" id="edit_nim_mhs" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Email Kampus <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email_mhs" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">No HP / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" id="edit_no_hp_mhs" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Fakultas</label>
                            <select name="fakultas" id="edit_fakultas_mhs" class="form-select rounded-3">
                                <option value="">Tidak diubah</option>
                                @foreach($fakultas as $f)
                                    <option value="{{ $f->id_fakultas }}">{{ $f->nama_fakultas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Program Studi</label>
                            <select name="prodi" id="edit_prodi_mhs" class="form-select rounded-3">
                                <option value="">Tidak diubah</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id_prodi }}">{{ $p->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Kata Sandi Baru <span class="text-muted">(opsional)</span></label>
                            <input type="password" name="password" class="form-control rounded-3" placeholder="Kosongkan jika tidak diganti">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3" placeholder="Ulangi kata sandi baru">
                        </div>
                        <div class="col-12 pt-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active_mhs">
                                <label class="form-check-label fw-semibold text-dark" for="edit_is_active_mhs">Status Akun Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Dosen -->
<div class="modal fade" id="editDosenModal" tabindex="-1" aria-labelledby="editDosenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editDosenForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fs-6 fw-bold" id="editDosenModalLabel">
                        <i class="fas fa-user-edit me-2"></i>Perbarui Akun Dosen
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_nama_dosen" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">NUPTK <span class="text-danger">*</span></label>
                            <input type="text" name="nuptk" id="edit_nuptk_dosen" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email_dosen" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">No HP <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" id="edit_no_hp_dosen" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Kata Sandi Baru <span class="text-muted">(opsional)</span></label>
                            <input type="password" name="password" class="form-control rounded-3" placeholder="Kosongkan jika tidak diganti">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3">
                        </div>
                        <div class="col-12 pt-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active_dosen">
                                <label class="form-check-label fw-semibold text-dark" for="edit_is_active_dosen">Status Akun Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Reviewer -->
<div class="modal fade" id="editReviewerModal" tabindex="-1" aria-labelledby="editReviewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editReviewerForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fs-6 fw-bold" id="editReviewerModalLabel">
                        <i class="fas fa-user-edit me-2"></i>Perbarui Akun Reviewer
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_nama_reviewer" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email_reviewer" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">No HP <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" id="edit_no_hp_reviewer" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Kata Sandi Baru <span class="text-muted">(opsional)</span></label>
                            <input type="password" name="password" class="form-control rounded-3" placeholder="Kosongkan jika tidak diganti">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3">
                        </div>
                        <div class="col-12 pt-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active_reviewer">
                                <label class="form-check-label fw-semibold text-dark" for="edit_is_active_reviewer">Status Akun Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Operator -->
<div class="modal fade" id="editOperatorModal" tabindex="-1" aria-labelledby="editOperatorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="editOperatorForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fs-6 fw-bold" id="editOperatorModalLabel">
                        <i class="fas fa-user-edit me-2"></i>Perbarui Akun Operator
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Nama Operator <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="edit_nama_operator" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email_operator" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">No HP <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" id="edit_no_hp_operator" class="form-control rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Kata Sandi Baru <span class="text-muted">(opsional)</span></label>
                            <input type="password" name="password" class="form-control rounded-3" placeholder="Kosongkan jika tidak diganti">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" class="form-control rounded-3">
                        </div>
                        <div class="col-12 pt-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active_operator">
                                <label class="form-check-label fw-semibold text-dark" for="edit_is_active_operator">Status Akun Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-sm btn-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Fakultas-Prodi dependent dropdown
    const fakultasSelect = document.getElementById('fakultasSelect');
    const prodiSelect = document.getElementById('prodiSelect');

    if (fakultasSelect && prodiSelect) {
        function resetProdi() {
            prodiSelect.innerHTML = '';
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = 'Pilih Prodi';
            prodiSelect.appendChild(opt);
        }

        async function loadProdiByFakultas(fakultasId) {
            resetProdi();
            if (!fakultasId) return;

            try {
                const response = await fetch(`/get-prodi/${fakultasId}`);
                if (!response.ok) throw new Error('Gagal mengambil data prodi');
                const data = await response.json();

                data.forEach(function (item) {
                    const option = document.createElement('option');
                    option.value = item.id_prodi;
                    option.textContent = item.nama_prodi;
                    prodiSelect.appendChild(option);
                });
            } catch (e) {
                console.error(e);
                alert('Terjadi kesalahan saat mengambil data program studi.');
            }
        }

        // Initial
        resetProdi();

        // Change handler
        fakultasSelect.addEventListener('change', function () {
            loadProdiByFakultas(this.value);
        });
    }

    // Bulk Delete Functionality
    const selectAll = document.getElementById('selectAll');
    const mahasiswaCheckboxes = document.querySelectorAll('.mahasiswa-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');

    function updateBulkDeleteButton() {
        if (!bulkDeleteBtn || !selectedCount) return;
        const checked = document.querySelectorAll('.mahasiswa-checkbox:checked');
        const count = checked.length;
        selectedCount.textContent = count;
        
        if (count > 0) {
            bulkDeleteBtn.disabled = false;
            bulkDeleteBtn.classList.remove('disabled');
        } else {
            bulkDeleteBtn.disabled = true;
            bulkDeleteBtn.classList.add('disabled');
        }
        
        // Update select all checkbox state
        if (selectAll && mahasiswaCheckboxes.length > 0) {
            if (checked.length === 0) {
                selectAll.indeterminate = false;
                selectAll.checked = false;
            } else if (checked.length === mahasiswaCheckboxes.length) {
                selectAll.indeterminate = false;
                selectAll.checked = true;
            } else {
                selectAll.indeterminate = true;
            }
        }
    }

    // Select All functionality
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            mahasiswaCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkDeleteButton();
        });
    }

    // Individual checkbox change
    mahasiswaCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkDeleteButton);
    });

    // Initial state
    updateBulkDeleteButton();

    // Filter Fakultas - Prodi dependency for filter form
    const filterFakultas = document.getElementById('filterFakultas');
    const filterProdi = document.getElementById('filterProdi');
    
    if (filterFakultas && filterProdi) {
        async function loadFilterProdiByFakultas(fakultasId) {
            filterProdi.innerHTML = '<option value="">Semua Prodi</option>';
            if (!fakultasId) return;

            try {
                const response = await fetch(`/get-prodi/${fakultasId}`);
                if (!response.ok) throw new Error('Gagal mengambil data prodi');
                const data = await response.json();

                data.forEach(function (item) {
                    const option = document.createElement('option');
                    option.value = item.id_prodi;
                    option.textContent = item.nama_prodi;
                    filterProdi.appendChild(option);
                });
            } catch (e) {
                console.error(e);
            }
        }

        filterFakultas.addEventListener('change', function () {
            loadFilterProdiByFakultas(this.value);
        });
    }

    // Edit Mahasiswa Modal
    document.querySelectorAll('.edit-mahasiswa-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const nim = this.getAttribute('data-nim');
            const email = this.getAttribute('data-email');
            const noHp = this.getAttribute('data-no-hp');
            const fakultas = this.getAttribute('data-fakultas');
            const prodi = this.getAttribute('data-prodi');
            const isActive = this.getAttribute('data-is-active');

            const form = document.getElementById('editMahasiswaForm');
            if (form) form.action = `/pimpinan-pt/akun/mahasiswa/${id}`;
            const elNama = document.getElementById('edit_nama_mhs');
            if (elNama) elNama.value = nama;
            const elNim = document.getElementById('edit_nim_mhs');
            if (elNim) elNim.value = nim;
            const elEmail = document.getElementById('edit_email_mhs');
            if (elEmail) elEmail.value = email;
            const elNoHp = document.getElementById('edit_no_hp_mhs');
            if (elNoHp) elNoHp.value = noHp;
            const elFakultas = document.getElementById('edit_fakultas_mhs');
            if (elFakultas) elFakultas.value = '';
            const elProdi = document.getElementById('edit_prodi_mhs');
            if (elProdi) elProdi.value = '';
            const elActive = document.getElementById('edit_is_active_mhs');
            if (elActive) elActive.checked = isActive === '1';
        });
    });

    // Edit Dosen Modal
    document.querySelectorAll('.edit-dosen-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const nuptk = this.getAttribute('data-nuptk');
            const email = this.getAttribute('data-email');
            const noHp = this.getAttribute('data-no-hp');
            const isActive = this.getAttribute('data-is-active');

            const form = document.getElementById('editDosenForm');
            if (form) form.action = `/pimpinan-pt/akun/dosen/${id}`;
            const elNama = document.getElementById('edit_nama_dosen');
            if (elNama) elNama.value = nama;
            const elNuptk = document.getElementById('edit_nuptk_dosen');
            if (elNuptk) elNuptk.value = nuptk;
            const elEmail = document.getElementById('edit_email_dosen');
            if (elEmail) elEmail.value = email;
            const elNoHp = document.getElementById('edit_no_hp_dosen');
            if (elNoHp) elNoHp.value = noHp;
            const elActive = document.getElementById('edit_is_active_dosen');
            if (elActive) elActive.checked = isActive === '1';
        });
    });

    // Edit Reviewer Modal
    document.querySelectorAll('.edit-reviewer-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const email = this.getAttribute('data-email');
            const noHp = this.getAttribute('data-no-hp');
            const isActive = this.getAttribute('data-is-active');

            const form = document.getElementById('editReviewerForm');
            if (form) form.action = `/pimpinan-pt/akun/reviewer/${id}`;
            const elNama = document.getElementById('edit_nama_reviewer');
            if (elNama) elNama.value = nama;
            const elEmail = document.getElementById('edit_email_reviewer');
            if (elEmail) elEmail.value = email;
            const elNoHp = document.getElementById('edit_no_hp_reviewer');
            if (elNoHp) elNoHp.value = noHp;
            const elActive = document.getElementById('edit_is_active_reviewer');
            if (elActive) elActive.checked = isActive === '1';
        });
    });

    // Edit Operator Modal
    document.querySelectorAll('.edit-operator-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const email = this.getAttribute('data-email');
            const noHp = this.getAttribute('data-no-hp');
            const isActive = this.getAttribute('data-is-active');

            const form = document.getElementById('editOperatorForm');
            if (form) form.action = `/pimpinan-pt/akun/operator/${id}`;
            const elNama = document.getElementById('edit_nama_operator');
            if (elNama) elNama.value = nama;
            const elEmail = document.getElementById('edit_email_operator');
            if (elEmail) elEmail.value = email;
            const elNoHp = document.getElementById('edit_no_hp_operator');
            if (elNoHp) elNoHp.value = noHp;
            const elActive = document.getElementById('edit_is_active_operator');
            if (elActive) elActive.checked = isActive === '1';
        });
    });
});
</script>
@endpush
