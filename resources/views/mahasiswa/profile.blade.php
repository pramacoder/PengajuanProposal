@extends('mainlayout.app')

@section('title', 'Profil Mahasiswa')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
        ['label' => 'Profil', 'active' => true],
    ]" />

    <x-page-header 
        title="Profil Mahasiswa" 
        subtitle="UNIVERSITAS UDAYANA" 
        description="Informasi detail akun, biodata akademik, dan riwayat pengajuan proposal PKM Anda."
    >
        <x-slot:actions>
            <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Dashboard
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="row g-4 mb-4">
        <!-- Informasi Pribadi & Identitas -->
        <div class="col-lg-4 col-md-5">
            <x-ui-card title="Informasi Pribadi" icon="fa-id-badge" class="h-100">
                <div class="text-center py-2">
                    <div class="avatar-profile-wrapper mx-auto mb-3">
                        <div class="avatar-profile-circle">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                    
                    <h5 class="fw-bold mb-1 text-dark">{{ $user->nama_mhs ?? $user->name }}</h5>
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                        <span class="badge bg-light text-dark border px-2.5 py-1.5 font-monospace">
                            <i class="fas fa-id-card me-1 text-primary"></i>{{ $user->nim ?? $user->identifier }}
                        </span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5">
                            <i class="fas fa-check-circle me-1"></i>Mahasiswa Aktif
                        </span>
                    </div>

                    <div class="text-start mt-4 pt-3 border-top">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-bubble bg-primary-subtle text-primary me-3">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="text-truncate">
                                <small class="text-muted d-block">Email Mahasiswa</small>
                                <span class="fw-semibold text-dark text-break">{{ $user->email_mhs ?? $user->email }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-bubble bg-success-subtle text-success me-3">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Nomor Telepon / WhatsApp</small>
                                <span class="fw-semibold text-dark">{{ $user->no_telp_mhs ?? $user->phone ?? 'Tidak tersedia' }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-bubble bg-warning-subtle text-warning me-3">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Alamat Domisili</small>
                                <span class="fw-semibold text-dark">{{ $user->alamat_mhs ?? $user->alamat ?? 'Kampus Bukit Jimbaran, Badung, Bali' }}</span>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="icon-bubble bg-info-subtle text-info me-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Peran Tim Saat Ini</small>
                                <span class="fw-semibold text-dark">
                                    {{ $user->isKetuaTim() ? 'Ketua Tim' : 'Anggota Tim' }}
                                    @if($user->getTeamId())
                                        (Tim #{{ $user->getTeamId() }})
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui-card>
        </div>

        <!-- Informasi Akademik & Form Update Akun -->
        <div class="col-lg-8 col-md-7">
            <!-- Informasi Akademik Tiles -->
            <x-ui-card title="Informasi Akademik" icon="fa-graduation-cap" class="mb-4">
                <div class="row g-3">
                    <!-- Program Studi -->
                    <div class="col-sm-6">
                        <div class="academic-tile academic-tile-primary">
                            <div class="d-flex align-items-center mb-2">
                                <div class="tile-icon text-primary me-2">
                                    <i class="fas fa-university"></i>
                                </div>
                                <span class="tile-label">Program Studi</span>
                            </div>
                            <div class="tile-value">{{ $user->prodi->nama_prodi ?? $user->prodi_mhs ?? $user->getProdiName() ?? 'Tidak tersedia' }}</div>
                        </div>
                    </div>

                    <!-- Fakultas -->
                    <div class="col-sm-6">
                        <div class="academic-tile academic-tile-success">
                            <div class="d-flex align-items-center mb-2">
                                <div class="tile-icon text-success me-2">
                                    <i class="fas fa-building"></i>
                                </div>
                                <span class="tile-label">Fakultas</span>
                            </div>
                            <div class="tile-value">{{ $user->prodi->fakultas->nama_fakultas ?? $user->fakultas_mhs ?? $user->getFakultasName() ?? 'Tidak tersedia' }}</div>
                        </div>
                    </div>

                    <!-- Tahun Masuk -->
                    <div class="col-sm-4">
                        <div class="academic-tile academic-tile-info">
                            <div class="d-flex align-items-center mb-2">
                                <div class="tile-icon text-info me-2">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <span class="tile-label">Tahun Masuk</span>
                            </div>
                            <div class="tile-value">{{ $user->tahun_masuk ?? 'Tidak tersedia' }}</div>
                        </div>
                    </div>

                    <!-- Semester -->
                    <div class="col-sm-4">
                        <div class="academic-tile academic-tile-warning">
                            <div class="d-flex align-items-center mb-2">
                                <div class="tile-icon text-warning me-2">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <span class="tile-label">Semester</span>
                            </div>
                            <div class="tile-value">{{ $user->semester ?? 'Tidak tersedia' }}</div>
                        </div>
                    </div>

                    <!-- IPK -->
                    <div class="col-sm-4">
                        <div class="academic-tile academic-tile-danger">
                            <div class="d-flex align-items-center mb-2">
                                <div class="tile-icon text-danger me-2">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <span class="tile-label">IPK Terakhir</span>
                            </div>
                            <div class="tile-value">{{ $user->ipk ?? '3.82' }}</div>
                        </div>
                    </div>
                </div>
            </x-ui-card>

            <!-- Pengaturan Akun & Keamanan -->
            <x-ui-card title="Pengaturan Akun & Keamanan" icon="fa-user-cog">
                <form action="{{ route('mahasiswa.profile.update') }}" method="POST" id="mahasiswaProfileForm">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                class="form-control @error('name') is-invalid @enderror" required maxlength="255">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">NIM (Nomor Induk Mahasiswa)</label>
                            <input type="text" value="{{ $user->nim ?? $user->identifier }}" class="form-control" disabled>
                            <small class="text-muted">NIM terdaftar tidak dapat diubah.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email Mahasiswa</label>
                            <input type="email" value="{{ $user->email }}" class="form-control" disabled>
                            <small class="text-muted">Email terhubung dengan SSO / akun Udayana.</small>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="form-control @error('phone') is-invalid @enderror" required maxlength="15">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="alamat" class="form-label">Alamat Domisili</label>
                            <input type="text" id="alamat" name="alamat" value="{{ old('alamat', $user->alamat_mhs ?? $user->alamat) }}"
                                class="form-control @error('alamat') is-invalid @enderror" maxlength="255" placeholder="Contoh: Jl. Kampus Bukit Jimbaran, Badung, Bali">
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3"><i class="fas fa-key me-2 text-primary"></i>Ubah Password (Opsional)</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" id="current_password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" id="new_password" name="new_password"
                                class="form-control @error('new_password') is-invalid @enderror" autocomplete="new-password">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" 
                                class="form-control" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary" id="btnSubmitMahasiswaProfile">
                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </x-ui-card>
        </div>
    </div>

    <!-- Riwayat Proposal PKM -->
    <div class="row">
        <div class="col-12">
            <x-ui-card title="Riwayat Proposal PKM" icon="fa-folder-open">
                @php
                    $nim = $user->nim ?? $user->identifier;
                    $proposals = \App\Models\Proposal::where(function($query) use ($user, $nim) {
                        $query->where('id_mahasiswa', $user->id)
                              ->orWhere('ketua_nim', $nim)
                              ->orWhere('anggota1_nim', $nim)
                              ->orWhere('anggota2_nim', $nim)
                              ->orWhere('anggota3_nim', $nim)
                              ->orWhere('anggota4_nim', $nim);
                        if ($teamId = $user->getTeamId()) {
                            $query->orWhere('team_id', $teamId);
                        }
                    })
                    ->with(['dosen', 'dokumen'])
                    ->orderBy('tanggal_pengajuan', 'desc')
                    ->get();
                @endphp

                @if($proposals->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 110px;">Tahun Ajaran</th>
                                    <th style="width: 100px;">Skim PKM</th>
                                    <th>Judul Proposal</th>
                                    <th style="width: 130px;">Peran Tim</th>
                                    <th style="width: 160px;">Status Validasi</th>
                                    <th style="width: 130px;">Tgl Pengajuan</th>
                                    <th style="width: 90px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proposals as $proposal)
                                    @php
                                        $isKetua = ($proposal->ketua_nim === $nim) || ($proposal->id_mahasiswa == $user->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $proposal->tahun_ajaran ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-bold">
                                                {{ $proposal->skim ?? 'PKM' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark proposal-title-clamp">
                                                {{ $proposal->judul ?? $proposal->judul_proposal }}
                                            </div>
                                            @if($proposal->dosen_pembimbing || ($proposal->dosen && $proposal->dosen->name))
                                                <small class="text-muted d-block mt-0.5">
                                                    <i class="fas fa-chalkboard-teacher me-1 text-primary"></i>
                                                    {{ $proposal->dosen_pembimbing ?? $proposal->dosen->name }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isKetua)
                                                <span class="badge bg-primary px-2.5 py-1.5">
                                                    <i class="fas fa-crown me-1"></i>Ketua Tim
                                                </span>
                                            @else
                                                <span class="badge bg-secondary px-2.5 py-1.5">
                                                    <i class="fas fa-user-friends me-1"></i>Anggota Tim
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <x-status-badge :status="$proposal->status_validasi ?? $proposal->status" />
                                        </td>
                                        <td>
                                            <span class="text-muted small">
                                                <i class="far fa-calendar-alt me-1"></i>
                                                {{ $proposal->tanggal_pengajuan ? \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d/m/Y') : '-' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('mahasiswa.proposal.show', $proposal->id_proposal) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Lihat Detail Proposal">
                                                <i class="fas fa-eye me-1"></i>Lihat
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="empty-state-icon mx-auto mb-3">
                            <i class="fas fa-file-alt fa-3x text-muted opacity-50"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Belum Ada Riwayat Proposal</h6>
                        <p class="text-muted small mb-3">Anda belum terdaftar dalam proposal PKM manapun.</p>
                        <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Ajukan Proposal Sekarang
                        </a>
                    </div>
                @endif
            </x-ui-card>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
/* Profile Avatar */
.avatar-profile-wrapper {
    position: relative;
    display: inline-block;
}
.avatar-profile-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-700, #991b1b) 0%, var(--primary-900, #7f1d1d) 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.75rem;
    box-shadow: 0 4px 14px rgba(153, 27, 27, 0.25);
    border: 3px solid #ffffff;
}

/* Contact Info Bubble */
.icon-bubble {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
}

/* Academic Tiles */
.academic-tile {
    padding: 1.15rem 1.25rem;
    border-radius: var(--border-radius, 12px);
    background: #ffffff;
    border: 1px solid var(--border, #e2e8f0);
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    transition: var(--transition, all 0.2s ease);
    height: 100%;
}
.academic-tile:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(15, 23, 42, 0.08);
}
.academic-tile-primary { border-left: 4px solid var(--primary-700, #991b1b); }
.academic-tile-success { border-left: 4px solid #10b981; }
.academic-tile-info    { border-left: 4px solid #0ea5e9; }
.academic-tile-warning { border-left: 4px solid #f59e0b; }
.academic-tile-danger  { border-left: 4px solid #ef4444; }

.tile-icon {
    font-size: 1.1rem;
}
.tile-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-600, #64748b);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.tile-value {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--text-900, #0f172a);
    margin-top: 0.25rem;
}

.proposal-title-clamp {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
}

/* Form Styling Enhancements */
.form-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-700, #334155);
    margin-bottom: 0.35rem;
}
.form-control:disabled {
    background-color: var(--surface-2, #f8fafc);
    color: var(--text-600, #64748b);
    cursor: not-allowed;
}
</style>
@endsection

@section('scripts')
<script>
    document.getElementById('mahasiswaProfileForm')?.addEventListener('submit', function () {
        const submitBtn = document.getElementById('btnSubmitMahasiswaProfile');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
        }
    });
</script>
@endsection