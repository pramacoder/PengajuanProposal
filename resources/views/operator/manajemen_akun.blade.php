@extends('operator.layout')

@section('title', 'Manajemen Akun - Operator')

@section('content')
<div class="container-fluid">
    <x-page-header 
        title="MANAJEMEN AKUN" 
        subtitle="Buat, ubah, hapus akun untuk semua role" />

    <div class="card card-custom mb-4">
        <div class="card-header card-header-custom">
            <h5 class="mb-0"><i class="fas fa-users-cog me-2"></i>Kelola Akun</h5>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="accountTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="mahasiswa-tab" data-bs-toggle="tab" data-bs-target="#mahasiswa" type="button" role="tab">Mahasiswa</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="dosen-tab" data-bs-toggle="tab" data-bs-target="#dosen" type="button" role="tab">Dosen</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviewer-tab" data-bs-toggle="tab" data-bs-target="#reviewer" type="button" role="tab">Reviewer</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="operator-tab" data-bs-toggle="tab" data-bs-target="#operator" type="button" role="tab">Operator</button>
                </li>
            </ul>

            <div class="tab-content mt-3">
                <!-- Mahasiswa -->
                <div class="tab-pane fade show active" id="mahasiswa" role="tabpanel">
                    <form class="row g-3 mb-4" method="POST" action="{{ route('operator.accounts.store', 'mahasiswa') }}">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">NIM</label>
                            <input type="text" name="nim" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No HP</label>
                            <input type="text" name="no_hp" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fakultas</label>
                            <select name="fakultas" id="fakultasSelect" class="form-select" required>
                                <option value="">Pilih Fakultas</option>
                                @foreach($fakultas as $f)
                                    <option value="{{ $f->id_fakultas }}">{{ $f->nama_fakultas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Prodi</label>
                            <select name="prodi" id="prodiSelect" class="form-select" required>
                                <option value="">Pilih Prodi</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id_prodi }}">{{ $p->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tambah Mahasiswa</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Prodi</th>
                                    <th>Fakultas</th>
                                    <th>Aktif</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mahasiswas as $m)
                                <tr>
                                    <td>{{ $m->nim }}</td>
                                    <td>{{ $m->nama_mhs }}</td>
                                    <td>{{ $m->email_mhs }}</td>
                                    <td>{{ $m->prodi_mhs }}</td>
                                    <td>{{ $m->fakultas_mhs }}</td>
                                    <td>
                                        <span class="badge bg-{{ $m->is_active ? 'success' : 'secondary' }}">{{ $m->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                    <td class="d-flex gap-2">
                                        <button class="btn btn-sm btn-warning edit-mahasiswa-btn" 
                                                data-id="{{ $m->id_mahasiswa }}"
                                                data-nama="{{ $m->nama_mhs }}"
                                                data-nim="{{ $m->nim }}"
                                                data-email="{{ $m->email_mhs }}"
                                                data-no-hp="{{ $m->no_hp_mhs }}"
                                                data-fakultas="{{ $m->fakultas_mhs }}"
                                                data-prodi="{{ $m->prodi_mhs }}"
                                                data-is-active="{{ $m->is_active ? '1' : '0' }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editMahasiswaModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('operator.accounts.delete', ['type' => 'mahasiswa', 'id' => $m->id_mahasiswa]) }}" onsubmit="return confirm('Hapus akun ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Dosen -->
                <div class="tab-pane fade" id="dosen" role="tabpanel">
                    <form class="row g-3 mb-4" method="POST" action="{{ route('operator.accounts.store', 'dosen') }}">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">NUPTK</label>
                            <input type="text" name="nuptk" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No HP</label>
                            <input type="text" name="no_hp" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tambah Dosen</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>NUPTK</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th>Aktif</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dosens as $d)
                                <tr>
                                    <td>{{ $d->nuptk }}</td>
                                    <td>{{ $d->nama_dosen }}</td>
                                    <td>{{ $d->email_dosen }}</td>
                                    <td>{{ $d->no_hp_dosen }}</td>
                                    <td><span class="badge bg-{{ $d->is_active ? 'success' : 'secondary' }}">{{ $d->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                    <td class="d-flex gap-2">
                                        <button class="btn btn-sm btn-warning edit-dosen-btn" 
                                                data-id="{{ $d->id_dosen }}"
                                                data-nama="{{ $d->nama_dosen }}"
                                                data-nuptk="{{ $d->nuptk }}"
                                                data-email="{{ $d->email_dosen }}"
                                                data-no-hp="{{ $d->no_hp_dosen }}"
                                                data-is-active="{{ $d->is_active ? '1' : '0' }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editDosenModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('operator.accounts.delete', ['type' => 'dosen', 'id' => $d->id_dosen]) }}" onsubmit="return confirm('Hapus akun ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Reviewer -->
                <div class="tab-pane fade" id="reviewer" role="tabpanel">
                    <form class="row g-3 mb-4" method="POST" action="{{ route('operator.accounts.store', 'reviewer') }}">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No HP</label>
                            <input type="text" name="no_hp" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tambah Reviewer</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th>Aktif</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reviewers as $r)
                                <tr>
                                    <td>{{ $r->nama_reviewer }}</td>
                                    <td>{{ $r->email_reviewer }}</td>
                                    <td>{{ $r->no_hp_reviewer }}</td>
                                    <td><span class="badge bg-{{ $r->is_active ? 'success' : 'secondary' }}">{{ $r->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                    <td class="d-flex gap-2">
                                        <button class="btn btn-sm btn-warning edit-reviewer-btn" 
                                                data-id="{{ $r->id_reviewer }}"
                                                data-nama="{{ $r->nama_reviewer }}"
                                                data-email="{{ $r->email_reviewer }}"
                                                data-no-hp="{{ $r->no_hp_reviewer }}"
                                                data-is-active="{{ $r->is_active ? '1' : '0' }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editReviewerModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('operator.accounts.delete', ['type' => 'reviewer', 'id' => $r->id_reviewer]) }}" onsubmit="return confirm('Hapus akun ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Operator -->
                <div class="tab-pane fade" id="operator" role="tabpanel">
                    <form class="row g-3 mb-4" method="POST" action="{{ route('operator.accounts.store', 'operator') }}">
                        @csrf
                        <div class="col-md-4">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">No HP</label>
                            <input type="text" name="no_hp" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tambah Operator</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>No HP</th>
                                    <th>Aktif</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($operators as $o)
                                <tr>
                                    <td>{{ $o->nama_pt }}</td>
                                    <td>{{ $o->email_pt }}</td>
                                    <td>{{ $o->no_hp_pt }}</td>
                                    <td><span class="badge bg-{{ $o->is_active ? 'success' : 'secondary' }}">{{ $o->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                    <td class="d-flex gap-2">
                                        <button class="btn btn-sm btn-warning edit-operator-btn" 
                                                data-id="{{ $o->id_pt }}"
                                                data-nama="{{ $o->nama_pt }}"
                                                data-email="{{ $o->email_pt }}"
                                                data-no-hp="{{ $o->no_hp_pt }}"
                                                data-is-active="{{ $o->is_active ? '1' : '0' }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editOperatorModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="{{ route('operator.accounts.delete', ['type' => 'operator', 'id' => $o->id_pt]) }}" onsubmit="return confirm('Hapus akun ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Mahasiswa -->
<div class="modal fade" id="editMahasiswaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editMahasiswaForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Mahasiswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" id="edit_nama_mhs" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIM</label>
                            <input type="text" name="nim" id="edit_nim_mhs" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email_mhs" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No HP</label>
                            <input type="text" name="no_hp" id="edit_no_hp_mhs" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fakultas</label>
                            <select name="fakultas" id="edit_fakultas_mhs" class="form-select">
                                <option value="">Tidak diubah</option>
                                @foreach($fakultas as $f)
                                    <option value="{{ $f->id_fakultas }}">{{ $f->nama_fakultas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Prodi</label>
                            <select name="prodi" id="edit_prodi_mhs" class="form-select">
                                <option value="">Tidak diubah</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id_prodi }}">{{ $p->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active_mhs">
                                <label class="form-check-label" for="edit_is_active_mhs">Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Dosen -->
<div class="modal fade" id="editDosenModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editDosenForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Dosen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" id="edit_nama_dosen" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">NUPTK</label>
                            <input type="text" name="nuptk" id="edit_nuptk_dosen" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email_dosen" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">No HP</label>
                            <input type="text" name="no_hp" id="edit_no_hp_dosen" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active_dosen">
                                <label class="form-check-label" for="edit_is_active_dosen">Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Reviewer -->
<div class="modal fade" id="editReviewerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editReviewerForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Reviewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" id="edit_nama_reviewer" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email_reviewer" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">No HP</label>
                            <input type="text" name="no_hp" id="edit_no_hp_reviewer" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active_reviewer">
                                <label class="form-check-label" for="edit_is_active_reviewer">Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Operator -->
<div class="modal fade" id="editOperatorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editOperatorForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Ubah Operator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" id="edit_nama_operator" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email_operator" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">No HP</label>
                            <input type="text" name="no_hp" id="edit_no_hp_operator" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active_operator">
                                <label class="form-check-label" for="edit_is_active_operator">Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
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

            document.getElementById('editMahasiswaForm').action = `/operator/akun/mahasiswa/${id}`;
            document.getElementById('edit_nama_mhs').value = nama;
            document.getElementById('edit_nim_mhs').value = nim;
            document.getElementById('edit_email_mhs').value = email;
            document.getElementById('edit_no_hp_mhs').value = noHp;
            document.getElementById('edit_fakultas_mhs').value = '';
            document.getElementById('edit_prodi_mhs').value = '';
            document.getElementById('edit_is_active_mhs').checked = isActive === '1';
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

            document.getElementById('editDosenForm').action = `/operator/akun/dosen/${id}`;
            document.getElementById('edit_nama_dosen').value = nama;
            document.getElementById('edit_nuptk_dosen').value = nuptk;
            document.getElementById('edit_email_dosen').value = email;
            document.getElementById('edit_no_hp_dosen').value = noHp;
            document.getElementById('edit_is_active_dosen').checked = isActive === '1';
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

            document.getElementById('editReviewerForm').action = `/operator/akun/reviewer/${id}`;
            document.getElementById('edit_nama_reviewer').value = nama;
            document.getElementById('edit_email_reviewer').value = email;
            document.getElementById('edit_no_hp_reviewer').value = noHp;
            document.getElementById('edit_is_active_reviewer').checked = isActive === '1';
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

            document.getElementById('editOperatorForm').action = `/operator/akun/operator/${id}`;
            document.getElementById('edit_nama_operator').value = nama;
            document.getElementById('edit_email_operator').value = email;
            document.getElementById('edit_no_hp_operator').value = noHp;
            document.getElementById('edit_is_active_operator').checked = isActive === '1';
        });
    });
});
</script>
@endsection


