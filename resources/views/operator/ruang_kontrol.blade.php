@extends('operator.layout')

@section('title', 'Ruang Kontrol - Operator')

@section('content')
@php
    // Ensure ruangKontrolAktif is not null
    if (!isset($ruangKontrolAktif) || !$ruangKontrolAktif) {
        $ruangKontrolAktif = (object)[
            'status_pendaftaran' => 'tertutup',
            'status_perbaikan' => 'tertutup',
            'tanggal_pendaftaran_mulai' => null,
            'tanggal_pendaftaran_selesai' => null,
            'tanggal_perbaikan_mulai' => null,
            'tanggal_perbaikan_selesai' => null,
            'tahun_ajaran' => date('Y'),
            'nama_history' => 'Jadwal ' . date('Y'),
            'is_active' => false
        ];
    }
@endphp

<div class="container-fluid">
    <!-- Header Section -->
    <x-page-header 
        title="RUANG KONTROL" 
        subtitle="UNIVERSITAS UDAYANA" />
    
    <!-- Year Selector -->
    <div class="row mb-4">
        <div class="col-md-3">
            <label class="form-label fw-bold mb-2">
                <i class="fas fa-calendar-alt me-2"></i>
                Pilih Tahun Ajaran
            </label>
            <select class="form-select" id="tahunSelector" onchange="window.location.href='?tahun=' + this.value">
                @php
                    $tahunAjaranTerpilih = $tahunAjaranTerpilih ?? \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru();
                    $tahunArray = [];
                    
                    // Tambahkan tahun akademik dari history (hanya format YYYY/YYYY)
                    if(isset($histories) && $histories->count() > 0) {
                        foreach($histories as $tahunAk => $items) {
                            // Hanya tambahkan jika format tahun akademik benar (mengandung slash dan 2 bagian)
                            if (strpos($tahunAk, '/') !== false && count(explode('/', $tahunAk)) === 2) {
                                $tahunArray[] = $tahunAk;
                            }
                        }
                    }
                    
                    // Generate list tahun akademik dari 5 tahun lalu sampai 10 tahun ke depan
                    $tahunSekarang = (int) date('Y');
                    $tahunMulai = $tahunSekarang - 5;
                    $tahunAkhir = $tahunSekarang + 10;
                    
                    for($tahun = $tahunMulai; $tahun <= $tahunAkhir; $tahun++) {
                        $tahunAk = $tahun . '/' . ($tahun + 1);
                        if(!in_array($tahunAk, $tahunArray)) {
                            $tahunArray[] = $tahunAk;
                        }
                    }
                    
                    // Tambahkan tahun akademik sekarang jika belum ada
                    $tahunAkSekarang = \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru();
                    if(!in_array($tahunAkSekarang, $tahunArray)) {
                        $tahunArray[] = $tahunAkSekarang;
                    }
                    
                    // Sort berdasarkan tahun pertama (dari format "2024/2025")
                    // Hanya sort yang memiliki format tahun akademik benar
                    usort($tahunArray, function($a, $b) {
                        // Pastikan kedua nilai memiliki format tahun akademik yang benar
                        if (strpos($a, '/') === false || strpos($b, '/') === false) {
                            return 0;
                        }
                        $partsA = explode('/', $a);
                        $partsB = explode('/', $b);
                        if (count($partsA) !== 2 || count($partsB) !== 2) {
                            return 0;
                        }
                        $tahunA = (int) $partsA[0];
                        $tahunB = (int) $partsB[0];
                        return $tahunB - $tahunA; // Descending
                    });
                    
                    // Filter ulang untuk memastikan hanya format tahun akademik yang benar
                    $tahunArray = array_filter($tahunArray, function($tahunAk) {
                        return strpos($tahunAk, '/') !== false && count(explode('/', $tahunAk)) === 2;
                    });
                    
                    // Re-index array setelah filter
                    $tahunArray = array_values($tahunArray);
                    
                    $tahunAkSekarang = \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru();
                @endphp
                @foreach($tahunArray as $tahunAk)
                    @php
                        // Pastikan format tahun akademik benar sebelum diproses
                        if (strpos($tahunAk, '/') === false || count(explode('/', $tahunAk)) !== 2) {
                            continue; // Skip jika format tidak benar
                        }
                        $tahunPertama = (int) explode('/', $tahunAk)[0];
                        $tahunPertamaSekarang = (int) explode('/', $tahunAkSekarang)[0];
                        $isTahunMasaLalu = $tahunPertama < $tahunPertamaSekarang;
                    @endphp
                    <option value="{{ $tahunAk }}" {{ $tahunAjaranTerpilih == $tahunAk ? 'selected' : '' }}>
                        {{ $tahunAk }}
                        @if($tahunAk == $tahunAkSekarang)
                            (Tahun Akademik Sekarang)
                        @elseif($isTahunMasaLalu)
                            (History Only - Tidak Dapat Digunakan)
                        @else
                            (Tahun Akademik Depan)
                        @endif
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">Pilih tahun akademik untuk melihat atau mengelola jadwal</small>
        </div>
    </div>
    
    <!-- Current Status Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom {{ $ruangKontrolAktif->status_pendaftaran === 'terbuka' || $ruangKontrolAktif->status_perbaikan === 'terbuka' ? 'border-success border-2' : '' }}">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-info-circle me-2"></i>
                        Status Sistem Saat Ini
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4 class="fw-bold mb-2">
                                @if($ruangKontrolAktif->status_pendaftaran === 'terbuka')
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Fase 1: Pengajuan Proposal <span class="badge bg-success ms-2">SEDANG BERJALAN</span>
                                @elseif($ruangKontrolAktif->status_perbaikan === 'terbuka')
                                    <i class="fas fa-tools text-warning me-2"></i>
                                    Fase 2: Perbaikan Proposal <span class="badge bg-warning ms-2">SEDANG BERJALAN</span>
                                @else
                                    <i class="fas fa-pause-circle text-secondary me-2"></i>
                                    Sistem Standby <span class="badge bg-secondary ms-2">TIDAK ADA FASE AKTIF</span>
                                @endif
                            </h4>
                            <p class="text-muted mb-0">
                                @if($ruangKontrolAktif->status_pendaftaran === 'terbuka')
                                    Mahasiswa dapat mengajukan proposal baru, dosen dapat memvalidasi
                                @elseif($ruangKontrolAktif->status_perbaikan === 'terbuka')
                                    Review proposal, perbaikan, dan penilaian akhir sedang berlangsung
                                @else
                                    Tidak ada fase yang aktif. Silakan aktifkan jadwal untuk memulai proses
                                @endif
                            </p>
                        </div>
                        @if($ruangKontrolAktif->is_active)
                            <div class="col-md-4 text-end">
                                <span class="badge bg-success px-3 py-2" style="font-size: 1rem;">
                                    <i class="fas fa-check-circle me-2"></i>Jadwal Aktif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Phase Cards -->
    <div class="row">
        <!-- Fase 1 Card -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom {{ $ruangKontrolAktif->status_pendaftaran === 'terbuka' ? 'border-success border-2' : '' }}">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-edit me-2"></i>
                        Fase 1: Pengajuan Proposal
                        @if($ruangKontrolAktif->status_pendaftaran === 'terbuka')
                            <span class="badge bg-light text-success ms-2">AKTIF</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($ruangKontrolAktif->status_pendaftaran === 'terbuka')
                        <div class="alert alert-success border-0 mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Fase ini sedang aktif!</strong><br>
                            Mahasiswa dapat mengajukan proposal baru dan dosen dapat memvalidasi.
                        </div>
                    @endif
                    
                    @php
                        $tahunAkademikSekarang = \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru();
                        // Extract tahun dari format "2025/2026" (tahun akademik)
                        $tahunAjaranAktif = isset($ruangKontrolAktif->tahun_ajaran) ? $ruangKontrolAktif->tahun_ajaran : $tahunAkademikSekarang;
                        if (strpos($tahunAjaranAktif, '/') !== false) {
                            $tahunJadwal = (int) explode('/', $tahunAjaranAktif)[0];
                            $tahunPertamaSekarang = (int) explode('/', $tahunAkademikSekarang)[0];
                        } else {
                            // Backward compatibility: jika format lama (tanpa slash)
                            $tahunJadwal = (int) $tahunAjaranAktif;
                            $tahunPertamaSekarang = (int) date('Y');
                        }
                        $isTahunMasaLalu = $tahunJadwal < $tahunPertamaSekarang;
                    @endphp
                    @if($ruangKontrolAktif->is_active && !$isTahunMasaLalu)
                    <form id="pendaftaranForm">
                        {{-- Hidden input untuk ID jadwal aktif --}}
                        <input type="hidden" id="ruangKontrolAktifId" value="{{ $ruangKontrolAktif->id_ruang_kontrol ?? '' }}">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="pendaftaranMulai" 
                                       value="{{ $ruangKontrolAktif->tanggal_pendaftaran_mulai ? \Carbon\Carbon::parse($ruangKontrolAktif->tanggal_pendaftaran_mulai)->format('Y-m-d') : '' }}"
                                       {{ $ruangKontrolAktif->status_pendaftaran == 'terbuka' ? 'disabled' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="pendaftaranSelesai" 
                                       value="{{ $ruangKontrolAktif->tanggal_pendaftaran_selesai ? \Carbon\Carbon::parse($ruangKontrolAktif->tanggal_pendaftaran_selesai)->format('Y-m-d') : '' }}"
                                       {{ $ruangKontrolAktif->status_pendaftaran == 'terbuka' ? 'disabled' : '' }}>
                            </div>
                        </div>
                        
                        @if($ruangKontrolAktif->tanggal_pendaftaran_mulai && $ruangKontrolAktif->tanggal_pendaftaran_selesai)
                            <div class="alert alert-info mb-3">
                                <small>
                                    <i class="fas fa-calendar me-2"></i>
                                    <strong>Jadwal:</strong> 
                                    {{ \Carbon\Carbon::parse($ruangKontrolAktif->tanggal_pendaftaran_mulai)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($ruangKontrolAktif->tanggal_pendaftaran_selesai)->format('d M Y') }}
                                </small>
                            </div>
                        @endif
                        
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-success flex-fill" id="openPendaftaran"
                                    {{ $ruangKontrolAktif->status_perbaikan == 'terbuka' ? 'disabled' : '' }}>
                                <i class="fas fa-unlock me-2"></i>Buka Fase Ini
                            </button>
                            <button type="button" class="btn btn-danger flex-fill" id="closePendaftaran">
                                <i class="fas fa-lock me-2"></i>Tutup Fase Ini
                            </button>
                        </div>
                        
                        @if($ruangKontrolAktif->status_perbaikan == 'terbuka')
                            <div class="mt-2">
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Tidak dapat membuka fase ini karena Fase 2 sedang aktif
                                </small>
                            </div>
                        @endif
                        
                        <small class="text-muted mt-3 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Sistem akan membuka fase ini secara otomatis saat tanggal mulai tiba.
                        </small>
                    </form>
                    @else
                        <div class="alert alert-warning border-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Jadwal belum aktif!</strong><br>
                            Silakan aktifkan jadwal terlebih dahulu dari menu History & Jadwal Management di bawah.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Fase 2 Card -->
        <div class="col-lg-6 mb-4">
            <div class="card card-custom {{ $ruangKontrolAktif->status_perbaikan === 'terbuka' ? 'border-warning border-2' : '' }}">
                <div class="card-header card-header-custom">
                    <h5 class="mb-0 text-white">
                        <i class="fas fa-tools me-2"></i>
                        Fase 2: Perbaikan Proposal
                        @if($ruangKontrolAktif->status_perbaikan === 'terbuka')
                            <span class="badge bg-light text-warning ms-2">AKTIF</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($ruangKontrolAktif->status_perbaikan === 'terbuka')
                        <div class="alert alert-warning border-0 mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Fase ini sedang aktif!</strong><br>
                            Review proposal, perbaikan, dan penilaian akhir sedang berlangsung.
                        </div>
                    @endif
                    
                    @if($ruangKontrolAktif->is_active && !$isTahunMasaLalu)
                    <form id="perbaikanForm">
                        {{-- Hidden input untuk ID jadwal aktif (sama dengan Fase 1) --}}
                        <input type="hidden" id="ruangKontrolAktifIdPerbaikan" value="{{ $ruangKontrolAktif->id_ruang_kontrol ?? '' }}">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="perbaikanMulai" 
                                       value="{{ $ruangKontrolAktif->tanggal_perbaikan_mulai ? \Carbon\Carbon::parse($ruangKontrolAktif->tanggal_perbaikan_mulai)->format('Y-m-d') : '' }}"
                                       {{ $ruangKontrolAktif->status_perbaikan == 'terbuka' ? 'disabled' : '' }}>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="perbaikanSelesai" 
                                       value="{{ $ruangKontrolAktif->tanggal_perbaikan_selesai ? \Carbon\Carbon::parse($ruangKontrolAktif->tanggal_perbaikan_selesai)->format('Y-m-d') : '' }}"
                                       {{ $ruangKontrolAktif->status_perbaikan == 'terbuka' ? 'disabled' : '' }}>
                            </div>
                        </div>
                        
                        @if($ruangKontrolAktif->tanggal_perbaikan_mulai && $ruangKontrolAktif->tanggal_perbaikan_selesai)
                            <div class="alert alert-info mb-3">
                                <small>
                                    <i class="fas fa-calendar me-2"></i>
                                    <strong>Jadwal:</strong> 
                                    {{ \Carbon\Carbon::parse($ruangKontrolAktif->tanggal_perbaikan_mulai)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($ruangKontrolAktif->tanggal_perbaikan_selesai)->format('d M Y') }}
                                </small>
                            </div>
                        @endif
                        
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-warning flex-fill" id="openPerbaikan"
                                    {{ $ruangKontrolAktif->status_pendaftaran == 'terbuka' ? 'disabled' : '' }}>
                                <i class="fas fa-unlock me-2"></i>Buka Fase Ini
                            </button>
                            <button type="button" class="btn btn-danger flex-fill" id="closePerbaikan">
                                <i class="fas fa-lock me-2"></i>Tutup Fase Ini
                            </button>
                        </div>
                        
                        @if($ruangKontrolAktif->status_pendaftaran == 'terbuka')
                            <div class="mt-2">
                                <small class="text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Tidak dapat membuka fase ini karena Fase 1 sedang aktif
                                </small>
                            </div>
                        @endif
                        
                        <small class="text-muted mt-3 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            Sistem akan membuka fase ini secara otomatis saat tanggal mulai tiba.
                        </small>
                    </form>
                    @elseif($isTahunMasaLalu)
                        <div class="alert alert-info border-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Tahun Akademik Masa Lalu - Hanya untuk Melihat History</strong><br>
                            Tahun akademik {{ $tahunAjaranAktif }} adalah tahun akademik masa lalu. Fase tidak dapat diatur untuk tahun akademik ini. 
                            Silakan pilih tahun akademik sekarang atau tahun akademik depan untuk mengatur fase.
                        </div>
                    @else
                        <div class="alert alert-warning border-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Jadwal belum aktif!</strong><br>
                            Silakan aktifkan jadwal terlebih dahulu dari menu History & Jadwal Management di bawah.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- History & Jadwal Management -->
    <div class="row">
        <div class="col-12">
            <div class="card card-custom">
                <div class="card-header card-header-custom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-history me-2"></i>History & Jadwal Management
                        </h5>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#createJadwalModal">
                            <i class="fas fa-plus me-2"></i>Tambah Jadwal Baru
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($jadwalTahun) && $jadwalTahun->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nama Jadwal</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Fase 1 (Pendaftaran)</th>
                                        <th>Fase 2 (Perbaikan)</th>
                                        <th>Status</th>
                                        <th>Tanggal Dibuat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalTahun as $jadwal)
                                        @php
                                            $tahunAkademikSekarang = \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru();
                                            // Extract tahun dari format "2025/2026" (tahun akademik)
                                            $tahunAjaranStr = $jadwal->tahun_ajaran;
                                            if (strpos($tahunAjaranStr, '/') !== false) {
                                                $tahunJadwal = (int) explode('/', $tahunAjaranStr)[0];
                                                $tahunPertamaSekarang = (int) explode('/', $tahunAkademikSekarang)[0];
                                            } else {
                                                // Backward compatibility: jika format lama (tanpa slash)
                                                $tahunJadwal = (int) $tahunAjaranStr;
                                                $tahunPertamaSekarang = (int) date('Y');
                                            }
                                            // Status kedaluwarsa berdasarkan tahun ajaran yang sudah lewat
                                            $isKedaluwarsa = $tahunJadwal < $tahunPertamaSekarang;
                                            $isTahunSekarang = $tahunJadwal == $tahunPertamaSekarang;
                                        @endphp
                                        <tr class="{{ $jadwal->is_active ? 'table-success' : '' }}">
                                            <td>
                                                <strong>{{ $jadwal->nama_history ?? 'Jadwal ' . $jadwal->tahun_ajaran }}</strong>
                                                @if($jadwal->is_active)
                                                    <span class="badge bg-success ms-2">AKTIF</span>
                                                @endif
                                                @if($isKedaluwarsa)
                                                    <span class="badge bg-secondary ms-2">KEDALUWARSA</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $jadwal->tahun_ajaran }}
                                                @if($isKedaluwarsa)
                                                    <br><small class="text-muted">(Tahun ajaran sudah lewat - hanya untuk melihat history)</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($jadwal->tanggal_pendaftaran_mulai && $jadwal->tanggal_pendaftaran_selesai)
                                                    {{ \Carbon\Carbon::parse($jadwal->tanggal_pendaftaran_mulai)->format('d M Y') }} - 
                                                    {{ \Carbon\Carbon::parse($jadwal->tanggal_pendaftaran_selesai)->format('d M Y') }}
                                                    <br><small class="text-muted">Status: {{ ucfirst($jadwal->status_pendaftaran) }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($jadwal->tanggal_perbaikan_mulai && $jadwal->tanggal_perbaikan_selesai)
                                                    {{ \Carbon\Carbon::parse($jadwal->tanggal_perbaikan_mulai)->format('d M Y') }} - 
                                                    {{ \Carbon\Carbon::parse($jadwal->tanggal_perbaikan_selesai)->format('d M Y') }}
                                                    <br><small class="text-muted">Status: {{ ucfirst($jadwal->status_perbaikan) }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($isKedaluwarsa)
                                                    <span class="badge bg-secondary">Kedaluwarsa</span>
                                                @elseif($jadwal->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($jadwal->created_at)->format('d M Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    @if($isKedaluwarsa)
                                                        {{-- Jadwal kedaluwarsa (tahun ajaran sudah lewat) - hanya bisa dilihat --}}
                                                        <button type="button" class="btn btn-sm btn-secondary" disabled title="Jadwal tahun ajaran yang sudah lewat hanya dapat dilihat, tidak dapat diubah atau dihapus">
                                                            <i class="fas fa-eye"></i>
                                                            <span class="d-none d-md-inline ms-1">Hanya Lihat</span>
                                                        </button>
                                                    @else
                                                        {{-- Jadwal tahun sekarang atau tahun depan - bisa diatur --}}
                                                        @if(!$jadwal->is_active)
                                                            <button type="button" class="btn btn-sm btn-success" onclick="activateJadwal({{ $jadwal->id_ruang_kontrol }})" title="Aktifkan Jadwal">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @endif
                                                        {{-- Tahun sekarang bisa dihapus (termasuk yang aktif), tahun depan juga bisa --}}
                                                        <button type="button" class="btn btn-sm btn-warning" onclick="editJadwal({{ $jadwal->id_ruang_kontrol }})" title="Edit Jadwal">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteJadwal({{ $jadwal->id_ruang_kontrol }})" title="Hapus Jadwal">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-3x mb-3 text-primary"></i>
                            <h5 class="fw-bold">Belum ada jadwal untuk tahun akademik {{ $tahunAjaranTerpilih ?? TahunAjaranHelper::getTahunAjaranTerbaru() }}</h5>
                            <p class="mb-4">Mulai dengan membuat jadwal baru untuk tahun ini.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createJadwalModal">
                                <i class="fas fa-plus me-2"></i>Buat Jadwal Baru
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Jadwal -->
<div class="modal fade" id="createJadwalModal" tabindex="-1" aria-labelledby="createJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header card-header-custom">
                <h5 class="modal-title text-white" id="createJadwalModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Jadwal Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createJadwalForm">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Jadwal</label>
                        <input type="text" class="form-control" id="createNamaHistory" name="nama_history" required 
                               placeholder="Contoh: Jadwal Semester Ganjil 2026">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tahun Akademik (Referensi - Opsional)</label>
                        <select class="form-select" id="createTahunAjaran" name="tahun_ajaran">
                            <option value="">Pilih Tahun Akademik (Opsional)</option>
                            @php
                                $tahunAkademikSekarang = \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru();
                                $tahunSekarang = (int) date('Y');
                                $tahunMulai = $tahunSekarang - 5;
                                $tahunAkhir = $tahunSekarang + 10;
                            @endphp
                            @for($tahun = $tahunAkhir; $tahun >= $tahunMulai; $tahun--)
                                @php
                                    $tahunAk = $tahun . '/' . ($tahun + 1);
                                    $tahunPertama = $tahun;
                                    $tahunPertamaSekarang = (int) explode('/', $tahunAkademikSekarang)[0];
                                    $isTahunMasaLalu = $tahunPertama < $tahunPertamaSekarang;
                                @endphp
                                <option value="{{ $tahunAk }}" {{ $tahunAk == $tahunAkademikSekarang ? 'selected' : '' }} {{ $isTahunMasaLalu ? 'disabled' : '' }}>
                                    {{ $tahunAk }} 
                                    @if($tahunAk == $tahunAkademikSekarang)
                                        (Tahun Akademik Sekarang)
                                    @elseif($isTahunMasaLalu)
                                        (History Only - Tidak Dapat Digunakan)
                                    @else
                                        (Tahun Akademik Depan)
                                    @endif
                                </option>
                            @endfor
                        </select>
                        <small class="form-text text-muted mt-1 d-block">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Info:</strong> Tahun akademik akan otomatis ditentukan dari tanggal mulai Fase 1 menggunakan logika yang sama dengan proposal.
                            Jika fase 1 dimulai di Juli-Desember, tahun akademik = tahun/tahun+1 (misal: Des 2025 = 2025/2026).
                            Jika fase 1 dimulai di Januari-Juni, tahun akademik = tahun-1/tahun (misal: Jan 2026 = 2025/2026).
                            Field ini hanya sebagai referensi untuk naming jadwal (opsional).
                        </small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-edit me-2 text-primary"></i>Fase 1: Pengajuan Proposal
                            </h6>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="createPendaftaranMulai" name="tanggal_pendaftaran_mulai" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="createPendaftaranSelesai" name="tanggal_pendaftaran_selesai" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-tools me-2 text-warning"></i>Fase 2: Perbaikan Proposal
                            </h6>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="createPerbaikanMulai" name="tanggal_perbaikan_mulai" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="createPerbaikanSelesai" name="tanggal_perbaikan_selesai" required>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info border-0 mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Info:</strong> 
                        <ul class="mb-0 mt-2">
                            <li>Setelah membuat jadwal, Anda perlu mengaktifkannya dari tabel History & Jadwal Management.</li>
                            <li>Sistem akan membuka fase secara otomatis sesuai tanggal yang telah ditentukan.</li>
                            <li><strong>Sistem menggunakan tahun akademik:</strong> Tahun akademik akan otomatis ditentukan dari tanggal mulai Fase 1.
                                Jika Fase 1 dimulai di Juli-Desember, tahun akademik = tahun/tahun+1 (misal: Des 2025 = 2025/2026).
                                Jika Fase 1 dimulai di Januari-Juni, tahun akademik = tahun-1/tahun (misal: Jan 2026 = 2025/2026).
                                Sistem mendukung fase lintas tahun kalender.</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveCreateJadwal()">
                    <i class="fas fa-save me-2"></i>Simpan Jadwal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Jadwal -->
<div class="modal fade" id="editJadwalModal" tabindex="-1" aria-labelledby="editJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header card-header-custom">
                <h5 class="modal-title text-white" id="editJadwalModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Jadwal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editJadwalForm">
                    <input type="hidden" id="editJadwalId" name="id">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Jadwal</label>
                        <input type="text" class="form-control" id="editNamaHistory" name="nama_history" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-edit me-2 text-primary"></i>Fase 1: Pengajuan Proposal
                            </h6>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="editPendaftaranMulai" name="tanggal_pendaftaran_mulai" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="editPendaftaranSelesai" name="tanggal_pendaftaran_selesai" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3">
                                <i class="fas fa-tools me-2 text-warning"></i>Fase 2: Perbaikan Proposal
                            </h6>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="editPerbaikanMulai" name="tanggal_perbaikan_mulai" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date" class="form-control" id="editPerbaikanSelesai" name="tanggal_perbaikan_selesai" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveEditJadwal()">
                    <i class="fas fa-save me-2"></i>Update Jadwal
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default dates if empty
    const today = new Date();
    const nextMonth = new Date(today.getFullYear(), today.getMonth() + 1, today.getDate());
    
    const pendaftaranMulai = document.getElementById('pendaftaranMulai');
    const pendaftaranSelesai = document.getElementById('pendaftaranSelesai');
    const perbaikanMulai = document.getElementById('perbaikanMulai');
    const perbaikanSelesai = document.getElementById('perbaikanSelesai');
    
    if (pendaftaranMulai && !pendaftaranMulai.value) {
        pendaftaranMulai.value = today.toISOString().split('T')[0];
    }
    if (pendaftaranSelesai && !pendaftaranSelesai.value) {
        pendaftaranSelesai.value = nextMonth.toISOString().split('T')[0];
    }
    if (perbaikanMulai && !perbaikanMulai.value) {
        perbaikanMulai.value = today.toISOString().split('T')[0];
    }
    if (perbaikanSelesai && !perbaikanSelesai.value) {
        perbaikanSelesai.value = nextMonth.toISOString().split('T')[0];
    }
    
    // Event listeners for pendaftaran buttons
    const openPendaftaranBtn = document.getElementById('openPendaftaran');
    const closePendaftaranBtn = document.getElementById('closePendaftaran');
    
    if (openPendaftaranBtn) {
        openPendaftaranBtn.addEventListener('click', function() {
            const perbaikanStatus = document.getElementById('statusPerbaikan')?.textContent.toLowerCase() || 'tertutup';
            if (perbaikanStatus === 'terbuka') {
                if (!confirm('Membuka Fase 1 akan menutup Fase 2 yang sedang aktif. Apakah Anda yakin?')) {
                    return;
                }
            }
            updateRuangKontrol('pendaftaran', 'terbuka');
        });
    }
    
    if (closePendaftaranBtn) {
        closePendaftaranBtn.addEventListener('click', function() {
            updateRuangKontrol('pendaftaran', 'tertutup');
        });
    }
    
    // Event listeners for perbaikan buttons
    const openPerbaikanBtn = document.getElementById('openPerbaikan');
    const closePerbaikanBtn = document.getElementById('closePerbaikan');
    
    if (openPerbaikanBtn) {
        openPerbaikanBtn.addEventListener('click', function() {
            const pendaftaranStatus = document.getElementById('statusPendaftaran')?.textContent.toLowerCase() || 'tertutup';
            if (pendaftaranStatus === 'terbuka') {
                if (!confirm('Membuka Fase 2 akan menutup Fase 1 yang sedang aktif. Apakah Anda yakin?')) {
                    return;
                }
            }
            updateRuangKontrol('perbaikan', 'terbuka');
        });
    }
    
    if (closePerbaikanBtn) {
        closePerbaikanBtn.addEventListener('click', function() {
            updateRuangKontrol('perbaikan', 'tertutup');
        });
    }
});

function updateRuangKontrol(type, status) {
    let statusPendaftaran, statusPerbaikan;
    
    if (type === 'pendaftaran') {
        statusPendaftaran = status;
        statusPerbaikan = status === 'terbuka' ? 'tertutup' : (document.getElementById('statusPerbaikan')?.textContent.toLowerCase() || 'tertutup');
    } else if (type === 'perbaikan') {
        statusPerbaikan = status;
        statusPendaftaran = status === 'terbuka' ? 'tertutup' : (document.getElementById('statusPendaftaran')?.textContent.toLowerCase() || 'tertutup');
    }
    
    // Ambil ID jadwal aktif dari hidden input
    const ruangKontrolId = document.getElementById('ruangKontrolAktifId')?.value || 
                          document.getElementById('ruangKontrolAktifIdPerbaikan')?.value || 
                          null;
    
    const tahunSelector = document.getElementById('tahunSelector');
    const tahunAjaranTerpilih = tahunSelector ? tahunSelector.value : '{{ $tahunAjaranTerpilih ?? \App\Helpers\TahunAjaranHelper::getTahunAjaranTerbaru() }}';
    
    const data = {
        id_ruang_kontrol: ruangKontrolId, // Kirim ID jadwal yang akan diupdate
        status_pendaftaran: statusPendaftaran,
        status_perbaikan: statusPerbaikan,
        tanggal_pendaftaran_mulai: document.getElementById('pendaftaranMulai')?.value || '',
        tanggal_pendaftaran_selesai: document.getElementById('pendaftaranSelesai')?.value || '',
        tanggal_perbaikan_mulai: document.getElementById('perbaikanMulai')?.value || '',
        tanggal_perbaikan_selesai: document.getElementById('perbaikanSelesai')?.value || '',
        tahun_ajaran: tahunAjaranTerpilih,
        nama_history: '{{ $ruangKontrolAktif->nama_history ?? "Jadwal " }}' + tahunAjaranTerpilih
    };
    
    if (!data.tanggal_pendaftaran_mulai || !data.tanggal_pendaftaran_selesai || 
        !data.tanggal_perbaikan_mulai || !data.tanggal_perbaikan_selesai) {
        showToast('Mohon lengkapi semua tanggal terlebih dahulu', 'error');
        return;
    }
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    button.disabled = true;
    
    fetch('{{ route("operator.update.ruang.kontrol") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat memperbarui pengaturan', 'error');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function showToast(message, type = 'info') {
    if (window.showToast && window.showToast !== showToast) {
        window.showToast(message, type);
        return;
    }
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background: ${type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : '#17a2b8'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        min-width: 300px;
        max-width: 500px;
        transform: translateX(120%);
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
    `;
    
    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle';
    toast.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        toast.style.transform = 'translateX(120%)';
        setTimeout(() => {
            if (toast.parentNode) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 5000);
}

// CRUD Functions untuk Jadwal Management
function saveCreateJadwal() {
    const data = {
        nama_history: document.getElementById('createNamaHistory').value.trim(),
        tahun_ajaran: document.getElementById('createTahunAjaran').value,
        tanggal_pendaftaran_mulai: document.getElementById('createPendaftaranMulai').value,
        tanggal_pendaftaran_selesai: document.getElementById('createPendaftaranSelesai').value,
        tanggal_perbaikan_mulai: document.getElementById('createPerbaikanMulai').value,
        tanggal_perbaikan_selesai: document.getElementById('createPerbaikanSelesai').value
    };
    
    if (!data.nama_history || !data.nama_history.trim()) {
        showToast('Mohon isi nama jadwal terlebih dahulu', 'error');
        return;
    }
    
    // Tahun akademik tidak perlu divalidasi di frontend karena akan di-determine dari tanggal fase 1
    
    if (!data.tanggal_pendaftaran_mulai || !data.tanggal_pendaftaran_selesai || 
        !data.tanggal_perbaikan_mulai || !data.tanggal_perbaikan_selesai) {
        showToast('Mohon lengkapi semua tanggal terlebih dahulu', 'error');
        return;
    }
    
    if (new Date(data.tanggal_pendaftaran_selesai) <= new Date(data.tanggal_pendaftaran_mulai)) {
        showToast('Tanggal selesai pendaftaran harus setelah tanggal mulai', 'error');
        return;
    }
    
    if (new Date(data.tanggal_perbaikan_selesai) <= new Date(data.tanggal_perbaikan_mulai)) {
        showToast('Tanggal selesai perbaikan harus setelah tanggal mulai', 'error');
        return;
    }
    
    if (new Date(data.tanggal_perbaikan_mulai) < new Date(data.tanggal_pendaftaran_selesai)) {
        showToast('Tanggal mulai perbaikan harus setelah tanggal selesai pendaftaran', 'error');
        return;
    }
    
    const submitBtn = document.querySelector('#createJadwalModal button[onclick="saveCreateJadwal()"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
    submitBtn.disabled = true;
    
    fetch('{{ route("operator.create.jadwal") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return response.text().then(text => {
                console.error('Non-JSON response:', text);
                throw new Error('Server mengembalikan response yang tidak valid');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('createJadwalModal'));
            document.getElementById('createJadwalForm').reset();
            modal.hide();
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Gagal membuat jadwal', 'error');
        }
    })
    .catch(error => {
        console.error('Error creating jadwal:', error);
        showToast('Terjadi kesalahan saat membuat jadwal: ' + error.message, 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function editJadwal(id) {
    fetch(`{{ url('/operator/jadwal') }}/${id}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const jadwal = data.data;
            document.getElementById('editJadwalId').value = jadwal.id_ruang_kontrol;
            document.getElementById('editNamaHistory').value = jadwal.nama_history || '';
            document.getElementById('editPendaftaranMulai').value = jadwal.tanggal_pendaftaran_mulai || '';
            document.getElementById('editPendaftaranSelesai').value = jadwal.tanggal_pendaftaran_selesai || '';
            document.getElementById('editPerbaikanMulai').value = jadwal.tanggal_perbaikan_mulai || '';
            document.getElementById('editPerbaikanSelesai').value = jadwal.tanggal_perbaikan_selesai || '';
            
            const modal = new bootstrap.Modal(document.getElementById('editJadwalModal'));
            modal.show();
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat memuat data jadwal', 'error');
    });
}

function saveEditJadwal() {
    const id = document.getElementById('editJadwalId').value;
    const data = {
        nama_history: document.getElementById('editNamaHistory').value,
        tanggal_pendaftaran_mulai: document.getElementById('editPendaftaranMulai').value,
        tanggal_pendaftaran_selesai: document.getElementById('editPendaftaranSelesai').value,
        tanggal_perbaikan_mulai: document.getElementById('editPerbaikanMulai').value,
        tanggal_perbaikan_selesai: document.getElementById('editPerbaikanSelesai').value
    };
    
    if (!data.tanggal_pendaftaran_mulai || !data.tanggal_pendaftaran_selesai || 
        !data.tanggal_perbaikan_mulai || !data.tanggal_perbaikan_selesai) {
        showToast('Mohon lengkapi semua tanggal terlebih dahulu', 'error');
        return;
    }
    
    if (new Date(data.tanggal_pendaftaran_selesai) <= new Date(data.tanggal_pendaftaran_mulai)) {
        showToast('Tanggal selesai pendaftaran harus setelah tanggal mulai', 'error');
        return;
    }
    
    if (new Date(data.tanggal_perbaikan_selesai) <= new Date(data.tanggal_perbaikan_mulai)) {
        showToast('Tanggal selesai perbaikan harus setelah tanggal mulai', 'error');
        return;
    }
    
    fetch(`{{ url('/operator/update-jadwal') }}/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('editJadwalModal'));
            modal.hide();
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat memperbarui jadwal', 'error');
    });
}

function deleteJadwal(id) {
    if (!confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
        return;
    }
    
    fetch(`{{ url('/operator/delete-jadwal') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat menghapus jadwal', 'error');
    });
}

function activateJadwal(id) {
    if (!confirm('Apakah Anda yakin ingin mengaktifkan jadwal ini? Jadwal aktif sebelumnya akan dinonaktifkan.')) {
        return;
    }
    
    fetch(`{{ url('/operator/activate-jadwal') }}/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Terjadi kesalahan saat mengaktifkan jadwal', 'error');
    });
}
</script>
@endsection