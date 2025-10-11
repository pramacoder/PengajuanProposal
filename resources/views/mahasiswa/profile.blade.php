@extends('mainlayout.app')

@section('title', 'Profil Mahasiswa')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-user me-2"></i>Profil Mahasiswa</h2>
                    <p class="text-muted">Informasi detail profil dan data akademik Anda</p>
                </div>
                <div class="btn-group" role="group">
                    <a href="{{ route('mahasiswa.proposal.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Card -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card card-custom h-100">
                <div class="card-header-custom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-circle me-2"></i>Informasi Pribadi
                    </h5>
                </div>
                <div class="card-body text-center">
                    <!-- Avatar -->
                    <div class="mb-4">
                        <div class="avatar-large mx-auto">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    
                    <!-- Basic Info -->
                    <h4 class="mb-2">{{ $user->nama_mhs }}</h4>
                    <p class="text-muted mb-3">{{ $user->nim }}</p>
                    
                    <!-- Contact Info -->
                    <div class="contact-info">
                        <div class="contact-item mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <span>{{ $user->email_mhs ?? 'Tidak tersedia' }}</span>
                        </div>
                        <div class="contact-item mb-2">
                            <i class="fas fa-phone text-success me-2"></i>
                            <span>{{ $user->no_telp_mhs ?? 'Tidak tersedia' }}</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt text-warning me-2"></i>
                            <span>{{ $user->alamat_mhs ?? 'Tidak tersedia' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Info -->
        <div class="col-lg-8 col-md-6 mb-4">
            <div class="card card-custom h-100">
                <div class="card-header-custom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>Informasi Akademik
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Program Studi -->
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-university text-primary me-2"></i>Program Studi
                                </label>
                                <div class="info-value">
                                    {{ $user->prodi->nama_prodi ?? 'Tidak tersedia' }}
                                </div>
                            </div>
                        </div>

                        <!-- Fakultas -->
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-building text-success me-2"></i>Fakultas
                                </label>
                                <div class="info-value">
                                    {{ $user->prodi->fakultas->nama_fakultas ?? 'Tidak tersedia' }}
                                </div>
                            </div>
                        </div>

                        <!-- Tahun Masuk -->
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-calendar-alt text-info me-2"></i>Tahun Masuk
                                </label>
                                <div class="info-value">
                                    {{ $user->tahun_masuk ?? 'Tidak tersedia' }}
                                </div>
                            </div>
                        </div>

                        <!-- Semester -->
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-layer-group text-warning me-2"></i>Semester
                                </label>
                                <div class="info-value">
                                    {{ $user->semester ?? 'Tidak tersedia' }}
                                </div>
                            </div>
                        </div>

                        <!-- IPK -->
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-chart-line text-danger me-2"></i>IPK
                                </label>
                                <div class="info-value">
                                    {{ $user->ipk ?? 'Tidak tersedia' }}
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-check-circle text-success me-2"></i>Status
                                </label>
                                <div class="info-value">
                                    <span class="badge bg-success">Aktif</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Proposal History -->
    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header-custom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i>Riwayat Proposal PKM
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        // Gabungkan proposal sebagai ketua tim dan sebagai anggota tim
                        $allProposals = collect();
                        
                        // Proposal sebagai ketua tim
                        if ($user->proposals) {
                            foreach ($user->proposals as $proposal) {
                                $allProposals->push([
                                    'proposal' => $proposal,
                                    'role' => 'Ketua Tim',
                                    'role_class' => 'primary'
                                ]);
                            }
                        }
                        
                        // Proposal sebagai anggota tim
                        if ($user->teams) {
                            foreach ($user->teams as $team) {
                                if ($team->proposal && !$allProposals->contains('proposal.id_proposal', $team->proposal->id_proposal)) {
                                    $allProposals->push([
                                        'proposal' => $team->proposal,
                                        'role' => ucfirst(str_replace('anggota', 'Anggota ', $team->role)),
                                        'role_class' => 'secondary'
                                    ]);
                                }
                            }
                        }
                        
                        // Urutkan berdasarkan tanggal pengajuan terbaru
                        $allProposals = $allProposals->sortByDesc('proposal.tanggal_pengajuan');
                    @endphp
                    
                    @if($allProposals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tahun Ajaran</th>
                                        <th>Judul Proposal</th>
                                        <th>Peran</th>
                                        <th>Status</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allProposals as $item)
                                        @php
                                            $proposal = $item['proposal'];
                                            $role = $item['role'];
                                            $roleClass = $item['role_class'];
                                        @endphp
                                        <tr>
                                            <td>{{ $proposal->tahun_ajaran }}</td>
                                            <td>
                                                <div class="proposal-title">
                                                    {{ \Illuminate\Support\Str::limit($proposal->judul, 50) }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $roleClass === 'primary' ? 'primary' : 'info' }}">
                                                    {{ $role }}
                                                </span>
                                            </td>
                                            <td>
                                                @switch($proposal->status_validasi)
                                                    @case('pending')
                                                        <span class="badge bg-warning">Menunggu Validasi</span>
                                                        @break
                                                    @case('valid')
                                                        <span class="badge bg-success">Valid</span>
                                                        @break
                                                    @case('invalid')
                                                        <span class="badge bg-danger">Tidak Valid</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">Draft</span>
                                                @endswitch
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ route('mahasiswa.proposal.show', $proposal->id_proposal) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum Ada Proposal</h5>
                            <p class="text-muted">Anda belum pernah mengajukan proposal PKM.</p>
                            <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Ajukan Proposal Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-large {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: white;
    margin: 0 auto;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.contact-info {
    text-align: left;
}

.contact-item {
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.contact-item:last-child {
    border-bottom: none;
}

.info-item {
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
}

.info-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    display: block;
}

.info-value {
    font-size: 1.1rem;
    color: #212529;
}

.proposal-title {
    font-weight: 500;
    color: #495057;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
}

.card-custom {
    transition: all 0.3s ease;
}

.card-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

@media (max-width: 768px) {
    .avatar-large {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }
    
    .info-item {
        margin-bottom: 1rem;
    }
}
</style>
@endsection