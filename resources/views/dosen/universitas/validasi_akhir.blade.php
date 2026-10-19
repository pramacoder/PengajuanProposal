@extends('mainlayout.app')

@section('title', 'Validasi Akhir - Dosen Pendamping Universitas')

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Dashboard Dosen', 'url' => route('dosen.universitas.dashboard')],
        ['label' => 'Validasi Akhir', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Antrean Validasi Akhir Usulan" 
        subtitle="Verifikasi naskah revisi akhir proposal mahasiswa binaan tingkat universitas sebelum penetapan kelolosan resmi" 
        badge="Pendamping Universitas"
        icon="fa-check-double"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dosen.universitas.dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </x-slot:actions>
    </x-page-header>

    <div class="ui-card shadow-sm">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-list-check text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Daftar Usulan yang Memerlukan Validasi Akhir</h6>
            </div>
            <span class="badge bg-secondary-subtle text-dark border">{{ $proposals->count() }} Usulan</span>
        </div>
        <div class="ui-card-body p-0">
            @if($proposals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-modern">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center py-3">No</th>
                                <th style="width: 30%;" class="py-3">Judul Usulan Proposal</th>
                                <th style="width: 10%;" class="py-3">Skim</th>
                                <th style="width: 20%;" class="py-3">Mahasiswa Pengusul</th>
                                <th style="width: 12%;" class="py-3">Hasil Semi Final</th>
                                <th style="width: 11%;" class="py-3">Tanggal Revisi</th>
                                <th style="width: 12%;" class="text-center py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($proposals as $index => $proposal)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark small line-clamp-2" title="{{ $proposal->judul_proposal }}">
                                            {{ $proposal->judul_proposal }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">
                                            {{ $proposal->skim }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark small">{{ $proposal->mahasiswa->nama_mahasiswa ?? $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                                        <small class="text-muted">NIM: {{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        @if($proposal->hasilSemiFinal)
                                            <span class="badge bg-{{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'success' : 'danger' }}-subtle text-{{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'success' : 'danger' }} border border-{{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'success' : 'danger' }}-subtle rounded-pill">
                                                {{ $proposal->hasilSemiFinal->status_final == 'lolos_tingkat_universitas' ? 'Lolos Univ' : 'Tidak Lolos' }}
                                            </span>
                                            <small class="text-muted d-block mt-1">Nilai: @formatId($proposal->hasilSemiFinal->nilai, 2)</small>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $revisiAkhir = $proposal->proposalRevisi->where('path_file', 'like', '%revisi_akhir%')->first();
                                        @endphp
                                        @if($revisiAkhir)
                                            <small class="text-dark">{{ \Carbon\Carbon::parse($revisiAkhir->tanggal_submit)->format('d M Y') }}</small>
                                            <small class="text-muted d-block">{{ \Carbon\Carbon::parse($revisiAkhir->tanggal_submit)->format('H:i') }}</small>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('dosen.universitas.validasi.akhir.detail', $proposal->id_proposal) }}" 
                                           class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold text-dark shadow-sm">
                                            <i class="fas fa-check-double me-1"></i>Validasi
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state-box py-5">
                    <div class="rounded-circle p-4 bg-success-subtle text-success d-inline-flex mb-3">
                        <i class="fas fa-check-circle fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Semua Validasi Akhir Selesai</h5>
                    <p class="text-secondary small mb-0" style="max-width: 480px;">
                        Tidak ada proposal mahasiswa yang sedang menunggu validasi akhir pendamping universitas saat ini.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
