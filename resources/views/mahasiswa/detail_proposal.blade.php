@extends('mainlayout.app')

@section('title', 'Detail Proposal PKM - Mahasiswa')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('styles')
<style>
    .pdf-viewer-container {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    
    .pdf-header {
        background: #f8fafc;
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .pdf-title {
        font-weight: 600;
        font-size: 0.95rem;
        color: #1e293b;
        margin: 0;
    }
    
    .pdf-controls {
        display: flex;
        gap: 0.5rem;
    }
    
    .pdf-viewer {
        width: 100%;
        height: 650px;
        border: none;
    }
    
    .pdf-iframe {
        width: 100%;
        height: 650px;
        border: none;
        background: white;
    }
    
    .pdf-loading {
        position: relative;
        min-height: 650px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #e2e8f0;
        border-top: 4px solid var(--primary-700, #8B0000);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        transition: opacity 0.3s ease;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #64748b;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #cbd5e1;
    }

    .team-badge-ketua {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .team-badge-anggota {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
        ['label' => 'Lihat Proposal', 'url' => route('mahasiswa.proposal.index')],
        ['label' => 'Detail Proposal', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Detail Usulan Proposal PKM" 
        subtitle="Informasi menyeluruh usulan program, keanggotaan tim, evaluasi reviewer, dan pratinjau berkas" 
        badge="Mahasiswa Portal"
        icon="fa-file-lines"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('mahasiswa.proposal.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Daftar</span>
                </a>
                @if($proposal->status === 'revisi' && $proposal->status_validasi !== 'pending')
                    <a href="{{ route('mahasiswa.proposal.revisi', $proposal->id_proposal) }}" class="btn btn-warning d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold shadow-sm">
                        <i class="fas fa-edit"></i>
                        <span>Unggah Revisi</span>
                    </a>
                @elseif($proposal->status === 'revisi_akhir')
                    <a href="{{ route('mahasiswa.proposal.revisi.akhir', $proposal->id_proposal) }}" class="btn btn-warning d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold shadow-sm">
                        <i class="fas fa-edit"></i>
                        <span>Unggah Revisi Akhir</span>
                    </a>
                @endif
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Overview Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Proposal Main Info -->
        <div class="col-lg-8">
            <div class="ui-card h-100">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Informasi Proposal</h6>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small">
                        Skim: {{ $proposal->skim }}
                    </span>
                </div>
                <div class="ui-card-body p-4">
                    <h5 class="fw-bold text-dark mb-3" style="line-height: 1.4;">
                        {{ $proposal->judul }}
                    </h5>
                    
                    <div class="row g-3 pt-2 border-top">
                        <div class="col-sm-6 col-md-4">
                            <label class="small text-secondary fw-semibold d-block mb-1">Skim Kegiatan</label>
                            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill fw-semibold">{{ $proposal->skim }}</span>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="small text-secondary fw-semibold d-block mb-1">Usulan Dana Belmawa</label>
                            <span class="fw-bold text-primary">@rupiahId($proposal->dana_diajukan_belmawa ?? 0)</span>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="small text-secondary fw-semibold d-block mb-1">Usulan Dana Universitas</label>
                            <span class="fw-bold text-success">@rupiahId($proposal->dana_diajukan_operator ?? 0)</span>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="small text-secondary fw-semibold d-block mb-1">Tahun Ajaran</label>
                            <span class="fw-semibold text-dark">{{ $proposal->tahun_ajaran ?? 'N/A' }}</span>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="small text-secondary fw-semibold d-block mb-1">Status Validasi Dosen</label>
                            <x-status-badge :status="$proposal->status_validasi" :label="ucfirst($proposal->status_validasi)" />
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="small text-secondary fw-semibold d-block mb-1">Status Tahap Sistem</label>
                            <x-status-badge :status="$proposal->status" :label="ucfirst(str_replace('_', ' ', $proposal->status))" />
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <label class="small text-secondary fw-semibold d-block mb-1">Tanggal Diajukan</label>
                            <span class="text-secondary small"><i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d F Y') }}</span>
                        </div>
                        @php
                            $userRole = null;
                            if (isset($user) && $proposal->id_mahasiswa == $user->id_mahasiswa) {
                                $userRole = 'Pengaju';
                            } elseif(isset($user)) {
                                $userTeamMember = $proposal->semuaAnggotaTim->where('nim', $user->nim)->first();
                                if ($userTeamMember) {
                                    $userRole = $userTeamMember->is_ketua ? 'Ketua' : 'Anggota';
                                }
                            }
                        @endphp
                        @if($userRole)
                        <div class="col-sm-6 col-md-4">
                            <label class="small text-secondary fw-semibold d-block mb-1">Peran Anda</label>
                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 rounded-pill small fw-semibold">{{ $userRole }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Dosen Pendamping Cards -->
        <div class="col-lg-4">
            <div class="ui-card h-100">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-user-tie text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Dosen Pendamping</h6>
                    </div>
                </div>
                <div class="ui-card-body p-4">
                    <div class="mb-3 pb-3 border-bottom">
                        <span class="badge bg-primary-subtle text-primary mb-1 small fw-bold">Dosen Pembimbing Proposal</span>
                        <div class="fw-bold text-dark">{{ $proposal->dosen_pembimbing ?? '-' }}</div>
                        @if($proposal->dosen)
                            <div class="text-muted small mt-1"><i class="fas fa-envelope me-1"></i>{{ $proposal->dosen->email_dosen }}</div>
                            @if($proposal->dosen->no_hp_dosen)
                                <div class="text-muted small"><i class="fas fa-phone me-1"></i>{{ $proposal->dosen->no_hp_dosen }}</div>
                            @endif
                        @endif
                    </div>

                    @if($proposal->dosenPendampingUniversitas)
                    <div class="p-3 rounded-3 bg-danger-subtle border border-danger-subtle">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fas fa-award text-danger"></i>
                            <span class="badge bg-danger text-white small fw-bold">Dosen Pendamping Universitas</span>
                        </div>
                        <div class="fw-bold text-dark">
                            {{ $proposal->dosenPendampingUniversitas->nama_dosen }}
                            @if($proposal->dosenPendampingUniversitas->gelar_depan), {{ $proposal->dosenPendampingUniversitas->gelar_depan }}@endif
                            @if($proposal->dosenPendampingUniversitas->gelar_belakang), {{ $proposal->dosenPendampingUniversitas->gelar_belakang }}@endif
                        </div>
                        @if($proposal->dosenPendampingUniversitas->email_dosen)
                            <div class="text-muted small mt-1">
                                <a href="mailto:{{ $proposal->dosenPendampingUniversitas->email_dosen }}" class="text-decoration-none text-danger fw-semibold">
                                    <i class="fas fa-envelope me-1"></i>{{ $proposal->dosenPendampingUniversitas->email_dosen }}
                                </a>
                            </div>
                        @endif
                        @if($proposal->dosenPendampingUniversitas->no_hp_dosen)
                            <div class="text-muted small">
                                <a href="tel:{{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}" class="text-decoration-none text-danger fw-semibold">
                                    <i class="fas fa-phone me-1"></i>{{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}
                                </a>
                            </div>
                        @endif
                        <div class="mt-2 text-secondary" style="font-size: 0.78rem;">
                            <i class="fas fa-info-circle me-1"></i>Konsultasikan proposal Anda dengan dosen pendamping universitas sebelum submit revisi akhir.
                        </div>
                    </div>
                    @else
                    <div class="text-muted small fst-italic">
                        Dosen pendamping universitas akan ditugaskan setelah proposal lolos seleksi semi final.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tim Mahasiswa Card -->
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-users text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Susunan Anggota Tim (@formatId($proposal->semuaAnggotaTim->count()) Orang)</h6>
            </div>
            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                Mahasiswa Udayana
            </span>
        </div>
        <div class="ui-card-body p-4">
            <div class="row g-3">
                @foreach($proposal->semuaAnggotaTim as $member)
                <div class="col-md-6 col-lg-4">
                    <div class="p-3 rounded-3 border h-100 {{ $member->is_ketua ? 'bg-success-subtle border-success-subtle' : 'bg-light' }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge {{ $member->is_ketua ? 'team-badge-ketua' : 'team-badge-anggota' }} rounded-pill px-2.5 py-1 small fw-bold">
                                <i class="fas {{ $member->is_ketua ? 'fa-crown text-warning' : 'fa-user' }} me-1"></i>{{ $member->is_ketua ? 'Ketua Tim' : 'Anggota Tim' }}
                            </span>
                            <span class="small text-muted fw-semibold">{{ $member->nim }}</span>
                        </div>
                        <div class="fw-bold text-dark fs-6 mb-1">{{ $member->nama_mhs }}</div>
                        <div class="text-secondary small">{{ $member->prodi_mhs }}</div>
                        <div class="text-secondary small">{{ $member->fakultas_mhs }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Catatan Penolakan Validasi Jika Ada -->
    @if($proposal->status_validasi === 'tidak_valid' && $proposal->catatan)
    <div class="alert alert-danger p-4 rounded-3 mb-4 shadow-sm border-danger-subtle">
        <div class="d-flex align-items-start gap-3">
            <i class="fas fa-times-circle fs-3 text-danger mt-1"></i>
            <div>
                <h5 class="fw-bold mb-1">Proposal Perlu Perbaikan / Ditolak oleh Dosen Pembimbing</h5>
                <p class="mb-2 text-dark">{{ $proposal->catatan }}</p>
                @if($proposal->tanggal_validasi)
                    <small class="text-muted"><i class="far fa-calendar-alt me-1"></i>Tanggal Validasi: {{ \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d F Y H:i') }}</small>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Catatan Review dari Reviewer Section -->
    @php
        $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', $proposal->id_reviewer_administratif)->first();
        $checklistChecked = $adminReview && $adminReview->checklist ? $adminReview->checklist : [];
        $hasilSemiFinal = $proposal->hasilSemiFinal;
        $hasilFinal = $proposal->hasilFinal;
        
        $substantifAwalReviews = collect();
        if ($proposal->id_reviewer_substantif_1) {
            $r1 = $proposal->nilaiSubstantif
                ->where('id_reviewer', $proposal->id_reviewer_substantif_1)
                ->where('jenis_review', 'pertama')
                ->first();
            if ($r1 && ($r1->note_substantif ?? $r1->catatan) && $r1->note_substantif !== 'Review substantif dimulai' && !empty(trim($r1->note_substantif))) {
                $substantifAwalReviews->push($r1);
            }
        }
        if ($proposal->id_reviewer_substantif_2) {
            $r2 = $proposal->nilaiSubstantif
                ->where('id_reviewer', $proposal->id_reviewer_substantif_2)
                ->where('jenis_review', 'pertama')
                ->first();
            if ($r2 && ($r2->note_substantif ?? $r2->catatan) && $r2->note_substantif !== 'Review substantif dimulai' && !empty(trim($r2->note_substantif))) {
                $substantifAwalReviews->push($r2);
            }
        }

        $substantifSeleksiReviews = collect();
        if ($proposal->id_reviewer_substantif_seleksi_1) {
            $rs1 = $proposal->nilaiSubstantif
                ->where('id_reviewer', $proposal->id_reviewer_substantif_seleksi_1)
                ->where('jenis_review', 'seleksi')
                ->first();
            if ($rs1 && ($rs1->note_substantif ?? $rs1->catatan) && $rs1->note_substantif !== 'Review substantif dimulai' && !empty(trim($rs1->note_substantif))) {
                $substantifSeleksiReviews->push($rs1);
            }
        }
        if ($proposal->id_reviewer_substantif_seleksi_2) {
            $rs2 = $proposal->nilaiSubstantif
                ->where('id_reviewer', $proposal->id_reviewer_substantif_seleksi_2)
                ->where('jenis_review', 'seleksi')
                ->first();
            if ($rs2 && ($rs2->note_substantif ?? $rs2->catatan) && $rs2->note_substantif !== 'Review substantif dimulai' && !empty(trim($rs2->note_substantif))) {
                $substantifSeleksiReviews->push($rs2);
            }
        }

        $handledIds = $substantifAwalReviews->pluck('id')->merge($substantifSeleksiReviews->pluck('id'))->toArray();
        $extraSubstantifReviews = $proposal->nilaiSubstantif
            ->whereNotIn('id', $handledIds)
            ->filter(function($item) {
                $note = $item->note_substantif ?? $item->catatan;
                return $note && $note !== 'Review substantif dimulai' && !empty(trim($note));
            });

        $hasAnyReview = $adminReview || $hasilSemiFinal || $hasilFinal || 
                        $substantifAwalReviews->count() > 0 || 
                        $substantifSeleksiReviews->count() > 0 || 
                        $extraSubstantifReviews->count() > 0;
    @endphp

    @if($hasAnyReview)
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-comments text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Catatan Evaluasi & Hasil Seleksi</h6>
            </div>
            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                Feedback Reviewer & Operator
            </span>
        </div>
        <div class="ui-card-body p-4">
            <!-- Review Administratif -->
            @if($adminReview)
            <div class="p-3 rounded-3 border mb-3 bg-light" style="border-left: 4px solid #b91c1c !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-danger mb-0"><i class="fas fa-clipboard-check me-2"></i>Review Administratif</h6>
                    <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($adminReview->updated_at)->format('d M Y H:i') }}</small>
                </div>
                
                @if(!empty($checklistChecked) && is_array($checklistChecked))
                <div class="p-3 bg-white rounded-3 border mb-2">
                    <div class="small fw-bold text-danger mb-2"><i class="fas fa-exclamation-triangle me-1"></i>Catatan Kesalahan Berkas Administratif:</div>
                    <div class="d-flex flex-column gap-1">
                        @foreach($checklistChecked as $checkedItem)
                            @php
                                $itemText = is_string($checkedItem) ? $checkedItem : (isset($checkedItem['text']) ? $checkedItem['text'] : '');
                            @endphp
                            @if(!empty($itemText))
                                <div class="d-flex align-items-center gap-2 small text-dark">
                                    <i class="fas fa-times-circle text-danger"></i>
                                    <span>{{ $itemText }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
                
                @if($adminReview->note_administratif)
                <div class="p-3 bg-white rounded-3 border">
                    <div class="small fw-semibold text-secondary mb-1">Catatan Tambahan Reviewer:</div>
                    <div class="small text-dark">{{ $adminReview->note_administratif }}</div>
                </div>
                @endif
            </div>
            @endif

            <!-- Hasil Semi Final -->
            @if($hasilSemiFinal)
            <div class="p-3 rounded-3 border mb-3 bg-light" style="border-left: 4px solid #16a34a !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-success mb-0"><i class="fas fa-trophy me-2"></i>Hasil Semi Final (Tingkat Universitas)</h6>
                    <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($hasilSemiFinal->updated_at)->format('d M Y H:i') }}</small>
                </div>
                <div class="p-3 bg-white rounded-3 border">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @if($hasilSemiFinal->status_final == 'lolos_tingkat_universitas')
                            <span class="badge bg-success text-white px-3 py-1.5 rounded-pill fw-bold"><i class="fas fa-check-circle me-1"></i>Lolos Tingkat Universitas</span>
                        @else
                            <span class="badge bg-danger text-white px-3 py-1.5 rounded-pill fw-bold"><i class="fas fa-times-circle me-1"></i>Tidak Lolos Tingkat Universitas</span>
                        @endif
                        @if($hasilSemiFinal->nilai)
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill">Nilai: @formatId($hasilSemiFinal->nilai, 2)</span>
                        @endif
                    </div>
                    @if($hasilSemiFinal->catatan_final)
                        <div class="small text-secondary fw-semibold mt-2">Catatan:</div>
                        <div class="small text-dark">{{ $hasilSemiFinal->catatan_final }}</div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Hasil Final Pimpinan PT -->
            @if($hasilFinal)
            <div class="p-3 rounded-3 border mb-3 bg-light" style="border-left: 4px solid #9333ea !important;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-primary mb-0"><i class="fas fa-medal me-2"></i>Hasil Keputusan Final</h6>
                    <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($hasilFinal->updated_at)->format('d M Y H:i') }}</small>
                </div>
                <div class="p-3 bg-white rounded-3 border">
                    <div class="row g-2 mb-2">
                        <div class="col-sm-6 col-md-3">
                            <label class="small text-secondary d-block">Status Final</label>
                            <span class="badge {{ $hasilFinal->status_final == 'lolos' ? 'bg-success' : 'bg-danger' }} text-white px-2.5 py-1 rounded-pill fw-bold">
                                {{ $hasilFinal->status_final == 'lolos' ? 'Lolos Didanai' : 'Tidak Lolos' }}
                            </span>
                        </div>
                        @if($hasilFinal->nilai)
                        <div class="col-sm-6 col-md-3">
                            <label class="small text-secondary d-block">Nilai Final</label>
                            <span class="fw-bold text-primary fs-6">@formatId($hasilFinal->nilai, 2)</span>
                        </div>
                        @endif
                        @if($hasilFinal->dana_disetujui_belmawa > 0)
                        <div class="col-sm-6 col-md-3">
                            <label class="small text-secondary d-block">Dana Belmawa</label>
                            <span class="fw-bold text-dark">@rupiahId($hasilFinal->dana_disetujui_belmawa)</span>
                        </div>
                        @endif
                        @if($hasilFinal->dana_disetujui_operator > 0)
                        <div class="col-sm-6 col-md-3">
                            <label class="small text-secondary d-block">Dana Universitas</label>
                            <span class="fw-bold text-dark">@rupiahId($hasilFinal->dana_disetujui_operator)</span>
                        </div>
                        @endif
                    </div>
                    @if($hasilFinal->catatan_final)
                        <div class="small text-secondary fw-semibold mt-2">Catatan Final:</div>
                        <div class="small text-dark">{{ $hasilFinal->catatan_final }}</div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Substantif Awal Reviews -->
            @if($substantifAwalReviews->count() > 0)
                @foreach($substantifAwalReviews as $index => $review)
                <div class="p-3 rounded-3 border mb-3 bg-light" style="border-left: 4px solid #0284c7 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold text-info mb-0"><i class="fas fa-user-graduate me-2"></i>Review Substantif Tahap Awal - Reviewer {{ $index + 1 }}</h6>
                        <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($review->updated_at)->format('d M Y H:i') }}</small>
                    </div>
                    <div class="p-3 bg-white rounded-3 border mt-2">
                        <div class="small text-dark">{{ $review->note_substantif ?? $review->catatan }}</div>
                    </div>
                </div>
                @endforeach
            @endif

            <!-- Substantif Seleksi Reviews -->
            @if($substantifSeleksiReviews->count() > 0)
                @foreach($substantifSeleksiReviews as $index => $review)
                <div class="p-3 rounded-3 border mb-3 bg-light" style="border-left: 4px solid #0891b2 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold text-dark mb-0"><i class="fas fa-award me-2"></i>Review Substantif Seleksi - Reviewer {{ $index + 1 }}</h6>
                        <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($review->updated_at)->format('d M Y H:i') }}</small>
                    </div>
                    <div class="p-3 bg-white rounded-3 border mt-2">
                        <div class="small text-dark">{{ $review->note_substantif ?? $review->catatan }}</div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
    @endif

    <!-- PDF Document Viewer -->
    <div class="pdf-viewer-container">
        <div class="pdf-header">
            <h5 class="pdf-title">
                <i class="fas fa-file-pdf text-danger me-2"></i>
                Dokumen Berkas Proposal Utama
            </h5>
            <div class="pdf-controls">
                @if($proposal->dokumen && $proposal->dokumen->path_file)
                <button id="fullscreenBtn" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-expand me-1"></i>Fullscreen
                </button>
                <a href="{{ route('mahasiswa.proposal.download', [$proposal->id_proposal, 'proposal']) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="fas fa-download me-1"></i>Download
                </a>
                @endif
            </div>
        </div>
        @if($proposal->dokumen && $proposal->dokumen->path_file)
        <div id="pdfViewer" class="pdf-loading">
            <div class="spinner"></div>
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-file-pdf"></i>
            <h5 class="fw-bold text-dark">Tidak Ada Dokumen Proposal</h5>
            <p class="text-secondary small">Dokumen proposal belum diunggah atau tidak tersedia pada penyimpanan sistem.</p>
        </div>
        @endif
    </div>

    <!-- Revision Documents Viewer -->
    @if($proposal->proposalRevisi->count() > 0)
    <div class="pdf-viewer-container">
        <div class="pdf-header">
            <h5 class="pdf-title">
                <i class="fas fa-history text-primary me-2"></i>
                Riwayat Berkas Revisi (@formatId($proposal->proposalRevisi->count()) File)
            </h5>
            <div class="pdf-controls">
                <button id="revisiFullscreenBtn" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fas fa-expand me-1"></i>Fullscreen
                </button>
            </div>
        </div>
        
        <!-- Revision Tabs -->
        <div class="p-3 bg-light border-bottom">
            <ul class="nav nav-pills gap-2" id="revisionTabs" role="tablist">
                @foreach($proposal->proposalRevisi as $index => $revisi)
                <li class="nav-item" role="presentation">
                    <button class="nav-link rounded-pill px-3 py-1.5 {{ $index === 0 ? 'active' : '' }}" 
                            id="revisi-tab-{{ $revisi->id_revisi }}" 
                            data-bs-toggle="tab" 
                            data-bs-target="#revisi-{{ $revisi->id_revisi }}" 
                            type="button" 
                            role="tab">
                        <i class="fas fa-file-pdf me-1"></i>
                        Revisi {{ $index + 1 }}
                        <small class="ms-1 opacity-75">({{ $revisi->tanggal_submit->format('d/m/Y') }})</small>
                    </button>
                </li>
                @endforeach
            </ul>
        </div>
            
        <div class="tab-content" id="revisionTabContent">
            @foreach($proposal->proposalRevisi as $index => $revisi)
            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                 id="revisi-{{ $revisi->id_revisi }}" 
                 role="tabpanel">
                <div class="d-flex justify-content-between align-items-center p-3 bg-white border-bottom">
                    <div>
                        <span class="fw-bold text-dark">{{ $revisi->nama_file }}</span>
                        <span class="text-muted small ms-2"><i class="far fa-clock me-1"></i>{{ $revisi->tanggal_submit->format('d/m/Y H:i') }}</span>
                    </div>
                    <a href="{{ route('file.serve', ['path' => $revisi->path_file]) }}" 
                       class="btn btn-outline-primary btn-sm rounded-pill px-3" 
                       download="{{ $revisi->nama_file }}">
                        <i class="fas fa-download me-1"></i>Download
                    </a>
                </div>
                <div id="revisiPdfViewer-{{ $revisi->id_revisi }}" class="pdf-loading">
                    <div class="spinner"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Bottom Action Bar -->
    <div class="p-3 bg-white rounded-3 border d-flex flex-wrap align-items-center gap-2">
        <a href="{{ route('mahasiswa.proposal.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-semibold">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar
        </a>
        
        @if($proposal->status_validasi === 'tidak_valid')
        <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary rounded-pill px-3 py-2 fw-semibold">
            <i class="fas fa-redo me-1"></i>Ajukan Ulang Proposal
        </a>
        @elseif($proposal->status === 'revisi' && $proposal->status_validasi !== 'pending')
        <a href="{{ route('mahasiswa.proposal.revisi', $proposal->id_proposal) }}" class="btn btn-warning rounded-pill px-3 py-2 fw-semibold">
            <i class="fas fa-edit me-1"></i>Revisi Proposal
        </a>
        @elseif($proposal->status === 'revisi' && $proposal->status_validasi === 'pending')
        <span class="badge bg-info text-white px-3 py-2 rounded-pill fs-6 fw-normal">
            <i class="fas fa-clock me-1"></i>Menunggu Validasi Dosen Pendamping
        </span>
        @elseif($proposal->status === 'revisi_akhir')
        <a href="{{ route('mahasiswa.proposal.revisi.akhir', $proposal->id_proposal) }}" class="btn btn-warning rounded-pill px-3 py-2 fw-semibold">
            <i class="fas fa-edit me-1"></i>Revisi Akhir Proposal
        </a>
        @elseif($proposal->status === 'revisi_submitted' || $proposal->status === 'validasi_akhir_dosen_univ')
        <span class="badge bg-info text-white px-3 py-2 rounded-pill fs-6 fw-normal">
            <i class="fas fa-clock me-1"></i>Menunggu Validasi
        </span>
        @elseif(in_array($proposal->status, ['draft', 'pending']))
        <a href="{{ route('mahasiswa.proposal.edit', $proposal->id_proposal) }}" class="btn btn-warning rounded-pill px-3 py-2 fw-semibold">
            <i class="fas fa-edit me-1"></i>Edit Proposal
        </a>
        @endif
        
        @if($proposal->status == 'draft')
        <form action="{{ route('mahasiswa.proposal.destroy', $proposal->id_proposal) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proposal ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger rounded-pill px-3 py-2 fw-semibold">
                <i class="fas fa-trash me-1"></i>Hapus Proposal
            </button>
        </form>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($proposal->dokumen && $proposal->dokumen->path_file)
            loadPDFDocument();
        @endif

        @if($proposal->proposalRevisi->count() > 0)
            loadRevisionDocuments();
        @endif

        initializeFullscreen();

        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
    });

    function loadPDFDocument() {
        const pdfViewer = document.getElementById('pdfViewer');
        const pdfUrl = '{{ $proposal->dokumen ? route("mahasiswa.proposal.view-pdf", $proposal->id_proposal) : "" }}';
        
        if (!pdfUrl) {
            pdfViewer.innerHTML = '<div class="empty-state"><i class="fas fa-file-pdf"></i><h4>Dokumen Tidak Tersedia</h4><p>Dokumen proposal tidak ditemukan.</p></div>';
            return;
        }

        const iframe = document.createElement('iframe');
        iframe.src = pdfUrl;
        iframe.className = 'pdf-iframe';
        iframe.style.width = '100%';
        iframe.style.height = '650px';
        iframe.style.border = 'none';
        iframe.style.opacity = '0';
        iframe.style.transition = 'opacity 0.3s ease';
        
        iframe.onload = function() {
            setTimeout(() => {
                iframe.style.opacity = '1';
                const spinner = pdfViewer.querySelector('.spinner');
                if (spinner && spinner.parentNode) {
                    spinner.parentNode.removeChild(spinner);
                }
            }, 100);
        };

        iframe.onerror = function() {
            showDownloadOption(pdfViewer);
        };

        pdfViewer.appendChild(iframe);
        
        setTimeout(() => {
            const spinner = pdfViewer.querySelector('.spinner');
            if (spinner && iframe.style.opacity === '0') {
                showDownloadOption(pdfViewer);
            }
        }, 5000);
    }

    function showDownloadOption(pdfViewer) {
        pdfViewer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-file-pdf"></i>
                <h5 class="fw-bold text-dark">PDF Tidak Dapat Ditampilkan</h5>
                <p class="text-secondary small">Silakan unduh dokumen untuk membuka berkas proposal secara utuh:</p>
                <div class="mt-3">
                    <a href="{{ route('mahasiswa.proposal.download', [$proposal->id_proposal, 'proposal']) }}" 
                       class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-download me-2"></i>Download PDF
                    </a>
                </div>
            </div>
        `;
    }

    function loadRevisionDocuments() {
        @if($proposal->proposalRevisi->count() > 0)
            @foreach($proposal->proposalRevisi as $revisi)
                loadRevisionPDF({{ $revisi->id_revisi }}, '{{ route('file.serve', ['path' => $revisi->path_file]) }}');
            @endforeach
        @endif
    }

    function loadRevisionPDF(revisiId, pdfUrl) {
        const pdfViewer = document.getElementById(`revisiPdfViewer-${revisiId}`);
        if (!pdfViewer) return;
        
        if (!pdfUrl) {
            pdfViewer.innerHTML = '<div class="empty-state"><i class="fas fa-file-pdf"></i><h4>Dokumen Tidak Tersedia</h4><p>Dokumen revisi tidak ditemukan.</p></div>';
            return;
        }

        const iframe = document.createElement('iframe');
        iframe.src = pdfUrl;
        iframe.className = 'pdf-iframe';
        iframe.style.width = '100%';
        iframe.style.height = '650px';
        iframe.style.border = 'none';
        iframe.style.opacity = '0';
        iframe.style.transition = 'opacity 0.3s ease';
        
        iframe.onload = function() {
            setTimeout(() => {
                iframe.style.opacity = '1';
                const spinner = pdfViewer.querySelector('.spinner');
                if (spinner && spinner.parentNode) {
                    spinner.parentNode.removeChild(spinner);
                }
            }, 100);
        };

        iframe.onerror = function() {
            showRevisionDownloadOption(pdfViewer, revisiId);
        };

        pdfViewer.appendChild(iframe);
        
        setTimeout(() => {
            const spinner = pdfViewer.querySelector('.spinner');
            if (spinner && iframe.style.opacity === '0') {
                showRevisionDownloadOption(pdfViewer, revisiId);
            }
        }, 5000);
    }

    function showRevisionDownloadOption(pdfViewer, revisiId) {
        pdfViewer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-file-pdf"></i>
                <h5 class="fw-bold text-dark">PDF Tidak Dapat Ditampilkan</h5>
                <p class="text-secondary small">Silakan unduh dokumen revisi untuk melihat isi berkas:</p>
                <div class="mt-3">
                    <a href="{{ $proposal->proposalRevisi->first() ? route('file.serve', ['path' => $proposal->proposalRevisi->first()->path_file]) : '#' }}" 
                       class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-download me-2"></i>Download PDF
                    </a>
                </div>
            </div>
        `;
    }

    function initializeFullscreen() {
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const revisiFullscreenBtn = document.getElementById('revisiFullscreenBtn');
        const pdfViewer = document.getElementById('pdfViewer');
        
        if (fullscreenBtn && pdfViewer) {
            fullscreenBtn.addEventListener('click', function() {
                const iframe = pdfViewer.querySelector('.pdf-iframe');
                const target = iframe || pdfViewer;
                if (target.requestFullscreen) target.requestFullscreen();
                else if (target.webkitRequestFullscreen) target.webkitRequestFullscreen();
                else if (target.msRequestFullscreen) target.msRequestFullscreen();
            });
        }

        if (revisiFullscreenBtn) {
            revisiFullscreenBtn.addEventListener('click', function() {
                const activeTab = document.querySelector('#revisionTabContent .tab-pane.active');
                if (activeTab) {
                    const iframe = activeTab.querySelector('.pdf-iframe');
                    const target = iframe || activeTab;
                    if (target.requestFullscreen) target.requestFullscreen();
                    else if (target.webkitRequestFullscreen) target.webkitRequestFullscreen();
                    else if (target.msRequestFullscreen) target.msRequestFullscreen();
                }
            });
        }
    }

    function showToast(message, type = 'info') {
        if (window.AppUI?.showToast) {
            window.AppUI.showToast(message, type);
        } else {
            console.log(`[Toast ${type}] ${message}`);
        }
    }
</script>
@endsection
