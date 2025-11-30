@extends('mainlayout.app')

@section('title', 'Detail Proposal PKM')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('styles')
<style>
    .proposal-detail-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
        overflow: hidden;
    }
    
    .proposal-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 2rem;
    }
    
    .proposal-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 1rem;
        line-height: 1.4;
    }
    
    .proposal-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        margin-bottom: 1rem;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .meta-item i {
        font-size: 1.1rem;
        opacity: 0.8;
    }
    
    .status-badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-pending {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .status-valid {
        background: linear-gradient(135deg, #d1ecf1 0%, #b8daff 100%);
        color: #0c5460;
        border: 1px solid #b8daff;
    }
    
    .status-approved {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-rejected {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .proposal-body {
        padding: 2rem;
    }
    
    .info-section {
        margin-bottom: 2rem;
    }
    
    .info-section h5 {
        color: var(--primary-color);
        margin-bottom: 1rem;
        font-weight: 600;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 0.5rem;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1rem;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid var(--primary-color);
    }
    
    .info-label {
        font-weight: 600;
        color: #555;
    }
    
    .info-value {
        color: #333;
        text-align: right;
    }
    
    .team-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .team-member {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        border-left: 4px solid var(--primary-color);
    }
    
    .team-member.ketua {
        border-left-color: #28a745;
        background: linear-gradient(135deg, #f8fff9 0%, #e8f5e8 100%);
    }
    
    .member-info {
        flex: 1;
    }
    
    .member-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.25rem;
    }
    
    .member-details {
        font-size: 0.9rem;
        color: #666;
    }
    
    .member-role {
        background: var(--primary-color);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .member-role.ketua {
        background: #28a745;
    }
    
    .pdf-viewer-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .pdf-header {
        background: #f8f9fa;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: between;
        align-items: center;
    }
    
    .pdf-title {
        font-weight: 600;
        color: #333;
        margin: 0;
        margin-right: 2rem;
    }
    
    .pdf-controls {
        display: flex;
        gap: 0.5rem;
    }
    
    .pdf-viewer {
        width: 100%;
        height: 700px;
        border: none;
    }
    
    .pdf-iframe {
        width: 100%;
        height: 700px;
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        background: white;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 1;
    }
    
    .pdf-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 400px;
        background: #f8f9fa;
    }
    
    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        transition: opacity 0.3s ease;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .pdf-iframe {
        transition: opacity 0.3s ease;
    }

    .pdf-loading {
        position: relative;
        min-height: 700px;
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
    }

    .pdf-loading .spinner {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
    }
    
    .action-buttons {
        padding: 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .btn-action {
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        color: #dee2e6;
    }
    
    .empty-state h4 {
        margin-bottom: 1rem;
        color: #495057;
    }
    
    .empty-state p {
        margin-bottom: 2rem;
        font-size: 1.1rem;
    }

    /* Fullscreen styles */
    .pdf-viewer-container:fullscreen {
        background: white;
        padding: 20px;
    }
    
    .pdf-viewer-container:fullscreen .pdf-iframe {
        height: calc(100vh - 100px);
    }
    
    .pdf-viewer-container:-webkit-full-screen {
        background: white;
        padding: 20px;
    }
    
    .pdf-viewer-container:-webkit-full-screen .pdf-iframe {
        height: calc(100vh - 100px);
    }
    
    .pdf-viewer-container:-ms-fullscreen {
        background: white;
        padding: 20px;
    }
    
    .pdf-viewer-container:-ms-fullscreen .pdf-iframe {
        height: calc(100vh - 100px);
    }

    /* PDF Controls Bar */
    .pdf-controls-bar {
        background: #f8f9fa;
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .pdf-controls-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .pdf-controls-right {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .page-info {
        font-weight: 500;
        color: #495057;
        background: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    .pdf-canvas-container {
        padding: 1rem;
        background: white;
    }

    /* Header Styles */
    .page-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .header-logo {
        width: 80px;
        height: 80px;
        object-fit: contain;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
    }

    .header-title {
        color: var(--primary-color);
        font-weight: 700;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    /* Revision Tabs Styles */
    .revision-tabs {
        background: white;
    }

    .revision-tabs .nav-tabs {
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 0;
    }

    .revision-tabs .nav-tabs .nav-link {
        border: none;
        border-radius: 0;
        color: #6c757d;
        font-weight: 500;
        padding: 1rem 1.5rem;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .revision-tabs .nav-tabs .nav-link:hover {
        border-color: transparent;
        border-bottom-color: #007bff;
        color: #007bff;
        background-color: #f8f9fa;
    }

    .revision-tabs .nav-tabs .nav-link.active {
        color: #007bff;
        background-color: white;
        border-color: transparent;
        border-bottom-color: #007bff;
        font-weight: 600;
    }

    .revision-tabs .nav-tabs .nav-link i {
        margin-right: 0.5rem;
    }

    .revision-info {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        font-size: 0.9rem;
    }

    .revision-info strong {
        color: #495057;
    }

    .tab-content {
        background: white;
    }

    .tab-pane {
        min-height: 400px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <x-page-header 
        title="Detail Proposal PKM" 
        description="Detail lengkap proposal PKM yang telah Anda ajukan"
        :showBackButton="true"
    />

    <!-- Proposal Detail Card -->
    <div class="proposal-detail-card">
        <div class="proposal-header">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <span class="status-badge status-{{ strtolower($proposal->status_validasi) }}">
                    @if($proposal->status_validasi == 'pending')
                        Menunggu Validasi
                    @elseif($proposal->status_validasi == 'valid')
                        @if($proposal->status_final == 'approved')
                            Disetujui
                        @else
                            Sedang Direview
                        @endif
                    @else
                        {{ ucfirst($proposal->status_validasi) }}
                    @endif
                </span>
            </div>
            
            <div class="proposal-title">
                {{ $proposal->judul }}
            </div>
            
            <div class="proposal-meta">
                <div class="meta-item">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Diajukan: {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d F Y') }}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-tag"></i>
                    <span>Skim: {{ $proposal->skim }}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Dana: Rp {{ number_format($proposal->dana_diajukan, 0, ',', '.') }}</span>
                </div>
                
                @php
                    $userRole = null;
                    if ($proposal->id_mahasiswa == $user->id_mahasiswa) {
                        $userRole = 'Pengaju';
                    } else {
                        $userTeamMember = $proposal->semuaAnggotaTim->where('nim', $user->nim)->first();
                        if ($userTeamMember) {
                            $userRole = $userTeamMember->is_ketua ? 'Ketua' : 'Anggota';
                        }
                    }
                @endphp
                
                @if($userRole)
                    <div class="meta-item">
                        <i class="fas fa-user"></i>
                        <span>Role: <span class="badge bg-info">{{ $userRole }}</span></span>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="proposal-body">
            <!-- Informasi Proposal -->
            <div class="info-section">
                <h5><i class="fas fa-info-circle me-2"></i>Informasi Proposal</h5>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Judul Proposal</span>
                        <span class="info-value">{{ $proposal->judul }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Skim PKM</span>
                        <span class="info-value">{{ $proposal->skim }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tahun Ajaran</span>
                        <span class="info-value">{{ $proposal->tahun_ajaran ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Dana Diajukan</span>
                        <span class="info-value">Rp {{ number_format($proposal->dana_diajukan, 0, ',', '.') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status Validasi</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ strtolower($proposal->status_validasi) }}">
                                @if($proposal->status_validasi == 'pending')
                                    Pending
                                @elseif($proposal->status_validasi == 'valid')
                                    Valid
                                @else
                                    {{ ucfirst($proposal->status_validasi) }}
                                @endif
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status Final</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ strtolower($proposal->status_final ?? 'pending') }}">
                                {{ ucfirst($proposal->status_final ?? 'Pending') }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Informasi Dosen Pendamping -->
            <div class="info-section">
                <h5><i class="fas fa-user-tie me-2"></i>Dosen Pendamping</h5>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Nama Dosen</span>
                        <span class="info-value">{{ $proposal->dosen_pembimbing ?? 'N/A' }}</span>
                    </div>
                    @if($proposal->dosen)
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value">{{ $proposal->dosen->email_dosen ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">No. HP</span>
                        <span class="info-value">{{ $proposal->dosen->no_hp_dosen ?? 'N/A' }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informasi Dosen Pendamping Universitas -->
            @if($proposal->dosenPendampingUniversitas)
            <div class="info-section" style="border-left: 4px solid #8B0000; background: linear-gradient(135deg, rgba(139, 0, 0, 0.05) 0%, rgba(255, 255, 255, 0.1) 100%); padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                <h5 style="color: #8B0000; font-weight: 600; margin-bottom: 1.5rem; display: flex; align-items: center;">
                    <i class="fas fa-user-graduate me-2" style="font-size: 1.3rem;"></i>Dosen Pendamping Universitas
                </h5>
                <div class="info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1rem;">
                    <div class="info-item" style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="info-label" style="color: #6c757d; font-size: 0.875rem; font-weight: 500;">Nama Dosen</span>
                        <span class="info-value" style="color: #8B0000; font-weight: 600; font-size: 1rem;">
                            {{ $proposal->dosenPendampingUniversitas->nama_dosen }}
                            @if($proposal->dosenPendampingUniversitas->gelar_depan)
                                , {{ $proposal->dosenPendampingUniversitas->gelar_depan }}
                            @endif
                            @if($proposal->dosenPendampingUniversitas->gelar_belakang)
                                , {{ $proposal->dosenPendampingUniversitas->gelar_belakang }}
                            @endif
                        </span>
                    </div>
                    @if($proposal->dosenPendampingUniversitas->email_dosen)
                    <div class="info-item" style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="info-label" style="color: #6c757d; font-size: 0.875rem; font-weight: 500;">Email</span>
                        <span class="info-value">
                            <a href="mailto:{{ $proposal->dosenPendampingUniversitas->email_dosen }}" style="color: #8B0000; text-decoration: none; font-weight: 500; transition: color 0.3s;" onmouseover="this.style.color='#a00000'" onmouseout="this.style.color='#8B0000'">
                                <i class="fas fa-envelope me-1"></i>{{ $proposal->dosenPendampingUniversitas->email_dosen }}
                            </a>
                        </span>
                    </div>
                    @endif
                    @if($proposal->dosenPendampingUniversitas->no_hp_dosen)
                    <div class="info-item" style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <span class="info-label" style="color: #6c757d; font-size: 0.875rem; font-weight: 500;">No. HP</span>
                        <span class="info-value">
                            <a href="tel:{{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}" style="color: #8B0000; text-decoration: none; font-weight: 500; transition: color 0.3s;" onmouseover="this.style.color='#a00000'" onmouseout="this.style.color='#8B0000'">
                                <i class="fas fa-phone me-1"></i>{{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}
                            </a>
                        </span>
                    </div>
                    @endif
                </div>
                <div class="alert mt-3 mb-0" style="background: rgba(139, 0, 0, 0.08); border-left: 3px solid #8B0000; border-radius: 6px; padding: 1rem;">
                    <small style="color: #495057; display: flex; align-items: center;">
                        <i class="fas fa-info-circle me-2" style="color: #8B0000;"></i>
                        Anda dapat menghubungi dosen pendamping universitas untuk konsultasi sebelum mengupload revisi akhir.
                    </small>
                </div>
            </div>
            @endif

            <!-- Catatan Review dari Reviewer -->
            @php
                $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', $proposal->id_reviewer_administratif)->first();
                $checklistConfig = \App\Helpers\ProposalHelper::getReviewChecklist($proposal->skim);
                $checklistChecked = $adminReview && $adminReview->checklist ? $adminReview->checklist : [];
                $hasilSemiFinal = $proposal->hasilSemiFinal;
                
                // Collect substantive reviews
                $substantifReviews = collect();
                if ($proposal->id_reviewer_substantif_1) {
                    $review1 = $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_1)->first();
                    if ($review1 && $review1->note_substantif && 
                        $review1->note_substantif !== 'Review substantif dimulai' &&
                        !empty(trim($review1->note_substantif))) {
                        $substantifReviews->push($review1);
                    }
                }
                if ($proposal->id_reviewer_substantif_2) {
                    $review2 = $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_2)->first();
                    if ($review2 && $review2->note_substantif && 
                        $review2->note_substantif !== 'Review substantif dimulai' &&
                        !empty(trim($review2->note_substantif))) {
                        $substantifReviews->push($review2);
                    }
                }
                
                $hasAnyReview = $adminReview || $hasilSemiFinal || $substantifReviews->count() > 0;
            @endphp
            
            @if($hasAnyReview)
            <div class="info-section" style="margin-bottom: 2rem;">
                <h5 style="color: var(--primary-color); font-weight: 600; margin-bottom: 1.5rem; display: flex; align-items: center;">
                    <i class="fas fa-comments me-2"></i>Catatan Review dari Reviewer
                </h5>
                
                <!-- Review Administratif -->
                @if($adminReview)
                <div style="background: white; border: 2px solid #e0e0e0; border-left: 5px solid #8b3a3a; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #f0f0f0;">
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #8B0000 0%, #a00000 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; margin-right: 1rem;">
                            <i class="fas fa-clipboard-check"></i>
                        </div>
                        <div>
                            <h5 style="color: #8B0000; font-weight: 600; margin: 0; font-size: 1.1rem;">Review Administratif</h5>
                            <small style="color: #6c757d; font-size: 0.875rem;">
                                <i class="fas fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($adminReview->updated_at)->format('d M Y H:i') }}
                            </small>
                        </div>
                    </div>
                    
                    @if(!empty($checklistChecked) && is_array($checklistChecked))
                    <div style="background: #fff5f5; border: 1px solid #fecaca; border-radius: 8px; padding: 1.25rem; margin-bottom: 1rem;">
                        <h6 style="color: #8B0000; font-weight: 600; margin-bottom: 1rem; font-size: 1rem;">
                            <i class="fas fa-exclamation-triangle me-2"></i>Kesalahan Administratif yang Ditemukan:
                        </h6>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            @foreach($checklistChecked as $checkedItem)
                                @php
                                    $itemText = is_string($checkedItem) ? $checkedItem : (isset($checkedItem['text']) ? $checkedItem['text'] : '');
                                @endphp
                                @if(!empty($itemText))
                                    <div style="display: flex; align-items: flex-start; padding: 0.75rem; background: white; border-radius: 6px; border-left: 3px solid #dc3545;">
                                        <i class="fas fa-times-circle text-danger me-2" style="margin-top: 0.2rem; flex-shrink: 0;"></i>
                                        <span style="color: #333; line-height: 1.5;">{{ $itemText }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    @if($adminReview->note_administratif)
                    <div style="background: #f8f9fa; border-left: 4px solid #8B0000; border-radius: 6px; padding: 1rem; margin-top: 1rem;">
                        <h6 style="color: #8B0000; font-weight: 600; margin-bottom: 0.75rem; font-size: 0.95rem;">
                            <i class="fas fa-sticky-note me-2"></i>Catatan Reviewer:
                        </h6>
                        <div style="color: #495057; line-height: 1.6;">
                            <p class="mb-0">{{ $adminReview->note_administratif }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                
                <!-- Hasil Semi Final -->
                @if($hasilSemiFinal)
                <div style="background: white; border: 2px solid #e0e0e0; border-left: 5px solid #28a745; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #f0f0f0;">
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; margin-right: 1rem;">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div>
                            <h5 style="color: #28a745; font-weight: 600; margin: 0; font-size: 1.1rem;">Hasil Semi Final - Tingkat Universitas</h5>
                            <small style="color: #6c757d; font-size: 0.875rem;">
                                <i class="fas fa-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($hasilSemiFinal->updated_at)->format('d M Y H:i') }}
                            </small>
                        </div>
                    </div>
                    
                    @if($hasilSemiFinal->status_final == 'lolos_tingkat_universitas')
                        <div style="background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: 2px solid #28a745; border-radius: 8px; padding: 1rem 1.5rem; color: #155724; font-size: 1.1rem; text-align: center; margin-bottom: 1rem;">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Lolos Tingkat Universitas</strong>
                        </div>
                    @else
                        <div style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border: 2px solid #dc3545; border-radius: 8px; padding: 1rem 1.5rem; color: #721c24; font-size: 1.1rem; text-align: center; margin-bottom: 1rem;">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>Tidak Lolos Tingkat Universitas</strong>
                        </div>
                    @endif
                    
                    @if($hasilSemiFinal->catatan_final)
                    <div style="background: #f8f9fa; border-left: 4px solid #28a745; border-radius: 6px; padding: 1rem; margin-top: 1rem;">
                        <h6 style="color: #28a745; font-weight: 600; margin-bottom: 0.75rem; font-size: 0.95rem;">
                            <i class="fas fa-sticky-note me-2"></i>Catatan:
                        </h6>
                        <div style="color: #495057; line-height: 1.6;">
                            <p class="mb-0">{{ $hasilSemiFinal->catatan_final }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                
                <!-- Review Substantif (Catatan Saja) -->
                @if($substantifReviews->count() > 0)
                    @foreach($substantifReviews as $index => $review)
                    <div style="background: white; border: 2px solid #e0e0e0; border-left: 5px solid #6c757d; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #f0f0f0;">
                            <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; margin-right: 1rem;">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div>
                                <h5 style="color: #6c757d; font-weight: 600; margin: 0; font-size: 1.1rem;">Review Substantif - Reviewer {{ $index + 1 }}</h5>
                                <small style="color: #6c757d; font-size: 0.875rem;">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($review->updated_at)->format('d M Y H:i') }}
                                </small>
                            </div>
                        </div>
                        
                        <div style="background: #f8f9fa; border-left: 4px solid #6c757d; border-radius: 6px; padding: 1rem;">
                            <h6 style="color: #6c757d; font-weight: 600; margin-bottom: 0.75rem; font-size: 0.95rem;">
                                <i class="fas fa-sticky-note me-2"></i>Catatan Reviewer:
                            </h6>
                            <div style="color: #495057; line-height: 1.6;">
                                <p class="mb-0">{{ $review->note_substantif }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
            @endif

            <!-- Informasi Tim -->
            <div class="info-section">
                <h5><i class="fas fa-users me-2"></i>Anggota Tim ({{ $proposal->semuaAnggotaTim->count() }} orang)</h5>
                <div class="team-section">
                    @foreach($proposal->semuaAnggotaTim as $member)
                    <div class="team-member {{ $member->is_ketua ? 'ketua' : '' }}">
                        <div class="member-info">
                            <div class="member-name">{{ $member->nama_mhs }}</div>
                            <div class="member-details">
                                NIM: {{ $member->nim }} | {{ $member->prodi_mhs }} | {{ $member->fakultas_mhs }}
                            </div>
                        </div>
                        <span class="member-role {{ $member->is_ketua ? 'ketua' : '' }}">
                            {{ $member->is_ketua ? 'Ketua' : 'Anggota' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Catatan dan File Koreksi jika Proposal Ditolak -->
            @if($proposal->status_validasi === 'tidak_valid' && $proposal->catatan)
            <div class="info-section">
                <div class="alert alert-danger">
                    <h5 class="alert-heading">
                        <i class="fas fa-times-circle me-2"></i>Proposal Ditolak oleh Dosen Pendamping
                    </h5>
                    <hr>
                    <p class="mb-2"><strong>Alasan Penolakan:</strong></p>
                    <p class="mb-3">{{ $proposal->catatan }}</p>
                    @if($proposal->tanggal_validasi)
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>
                            Tanggal: {{ \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d F Y H:i') }}
                        </small>
                    @endif
                </div>
            </div>
            @elseif($proposal->catatan)
            <!-- Catatan (jika ada tapi bukan penolakan) -->
            <div class="info-section">
                <h5><i class="fas fa-sticky-note me-2"></i>Catatan</h5>
                <div class="info-item">
                    <span class="info-label">Catatan</span>
                    <span class="info-value">{{ $proposal->catatan }}</span>
                </div>
            </div>
            @endif

            <!-- Review PDF Dosen (untuk validasi) -->
            @if($proposal->status_validasi === 'valid' && $proposal->path_review_dosen)
            <div class="info-section">
                <h5><i class="fas fa-file-pdf me-2"></i>Review PDF dari Dosen</h5>
                <div class="info-item">
                    <span class="info-label">File Review</span>
                    <span class="info-value">
                        <a href="{{ asset('storage/' . $proposal->path_review_dosen) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download me-1"></i>
                            {{ $proposal->nama_file_review_dosen ?? 'Download Review PDF' }}
                        </a>
                        @if($proposal->tanggal_review_dosen)
                            <br><small class="text-muted mt-2 d-block">
                                <i class="fas fa-calendar me-1"></i>
                                Diupload pada: {{ \Carbon\Carbon::parse($proposal->tanggal_review_dosen)->format('d F Y H:i') }}
                            </small>
                        @endif
                    </span>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- PDF Viewer -->
    <div class="pdf-viewer-container">
        <div class="pdf-header">
            <h5 class="pdf-title">
                <i class="fas fa-file-pdf me-2"></i>
                Dokumen Proposal
            </h5>
            <div class="pdf-controls">
                @if($proposal->dokumen && $proposal->dokumen->path_file)
                <button id="fullscreenBtn" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="fas fa-expand me-1"></i>Fullscreen
                </button>
                <a href="{{ route('mahasiswa.proposal.download', [$proposal->id_proposal, 'proposal']) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-download me-1"></i>Download
                </a>
                @endif
            </div>
        </div>
        @if($proposal->dokumen && $proposal->dokumen->path_file)
        <div id="pdfViewer" class="pdf-loading">
            <div class="spinner"></div>
            <!-- PDF iframe will be inserted here -->
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-file-pdf"></i>
            <h4>Tidak Ada Dokumen</h4>
            <p>Dokumen proposal belum diunggah atau tidak tersedia.</p>
        </div>
        @endif
    </div>

    <!-- Revision Documents -->
    @if($proposal->proposalRevisi->count() > 0)
    <div class="pdf-viewer-container mt-20">
        <div class="pdf-header">
            <h5 class="pdf-title">
                <i class="fas fa-edit me-2"></i>
                Dokumen Revisi ({{ $proposal->proposalRevisi->count() }} file)
            </h5>
            <div class="pdf-controls">
                <button id="revisiFullscreenBtn" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="fas fa-expand me-1"></i>Fullscreen
                </button>
            </div>
        </div>
        
        <!-- Revision Tabs -->
        <div class="revision-tabs">
            <ul class="nav nav-tabs" id="revisionTabs" role="tablist">
                @foreach($proposal->proposalRevisi as $index => $revisi)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                            id="revisi-tab-{{ $revisi->id_revisi }}" 
                            data-bs-toggle="tab" 
                            data-bs-target="#revisi-{{ $revisi->id_revisi }}" 
                            type="button" 
                            role="tab">
                        <i class="fas fa-file-pdf me-1"></i>
                        Revisi {{ $index + 1 }}
                        <small class="d-block text-muted">{{ $revisi->tanggal_submit->format('d/m/Y H:i') }}</small>
                    </button>
                </li>
                @endforeach
            </ul>
            
            <div class="tab-content" id="revisionTabContent">
                @foreach($proposal->proposalRevisi as $index => $revisi)
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                     id="revisi-{{ $revisi->id_revisi }}" 
                     role="tabpanel">
                    <div class="revision-info p-3 bg-light border-bottom">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Nama File:</strong> {{ $revisi->nama_file }}
                            </div>
                            <div class="col-md-3">
                                <strong>Tanggal Submit:</strong> {{ $revisi->tanggal_submit->format('d/m/Y H:i') }}
                            </div>
                            <div class="col-md-3 text-end">
                                <a href="{{ asset('storage/' . $revisi->path_file) }}" 
                                   class="btn btn-outline-primary btn-sm" 
                                   download="{{ $revisi->nama_file }}">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="revisiPdfViewer-{{ $revisi->id_revisi }}" class="pdf-loading">
                        <div class="spinner"></div>
                        <!-- PDF iframe will be inserted here -->
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('mahasiswa.proposal.index') }}" class="btn btn-outline-secondary btn-action">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
        </a>
        
        @if($proposal->status_validasi === 'tidak_valid')
        <a href="{{ route('mahasiswa.proposal.create') }}" class="btn btn-primary btn-action">
            <i class="fas fa-redo me-2"></i>Ajukan Ulang Proposal
        </a>
        @elseif($proposal->status === 'revisi')
        <a href="{{ route('mahasiswa.proposal.revisi', $proposal->id_proposal) }}" class="btn btn-warning btn-action">
            <i class="fas fa-edit me-2"></i>Revisi Proposal
        </a>
        @elseif($proposal->status === 'revisi_akhir')
        <a href="{{ route('mahasiswa.proposal.revisi.akhir', $proposal->id_proposal) }}" class="btn btn-warning btn-action">
            <i class="fas fa-edit me-2"></i>Revisi Akhir Proposal
        </a>
        @elseif($proposal->status === 'revisi_submitted' || $proposal->status === 'validasi_akhir_dosen_univ')
        <span class="btn btn-info btn-action disabled">
            <i class="fas fa-clock me-2"></i>Menunggu Validasi
        </span>
        @elseif(in_array($proposal->status, ['draft', 'pending']))
        <a href="{{ route('mahasiswa.proposal.edit', $proposal->id_proposal) }}" class="btn btn-warning btn-action">
            <i class="fas fa-edit me-2"></i>Edit Proposal
        </a>
        @endif
        
        @if($proposal->status == 'draft')
        <form action="{{ route('mahasiswa.proposal.destroy', $proposal->id_proposal) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proposal ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-action">
                <i class="fas fa-trash me-2"></i>Hapus Proposal
            </button>
        </form>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Debug: Log proposal data
        console.log('Proposal Data:', {
            hasDokumen: {{ $proposal->dokumen ? 'true' : 'false' }},
            pathFile: '{{ $proposal->dokumen ? $proposal->dokumen->path_file : "null" }}',
            dokumenId: {{ $proposal->dokumen ? $proposal->dokumen->id_dokumen : 'null' }}
        });

        @if($proposal->dokumen && $proposal->dokumen->path_file)
            console.log('Loading PDF document...');
            loadPDFDocument();
        @else
            console.log('No document found, showing empty state');
        @endif

        // Load revision documents if any
        @if($proposal->proposalRevisi->count() > 0)
            console.log('Loading revision documents...');
            loadRevisionDocuments();
        @endif

        // Initialize fullscreen functionality
        initializeFullscreen();

        // Show success/error messages
        @if(session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif
    });

    function loadPDFDocument() {
        const pdfViewer = document.getElementById('pdfViewer');
        const pdfUrl = '{{ $proposal->dokumen ? asset('storage/' . $proposal->dokumen->path_file) : "" }}';
        
        console.log('Loading PDF from URL:', pdfUrl);
        
        if (!pdfUrl) {
            pdfViewer.innerHTML = '<div class="empty-state"><i class="fas fa-file-pdf"></i><h4>Dokumen Tidak Tersedia</h4><p>Dokumen proposal tidak ditemukan.</p></div>';
            return;
        }

        // Create iframe first (before clearing content)
        const iframe = document.createElement('iframe');
        iframe.src = pdfUrl;
        iframe.className = 'pdf-iframe';
        iframe.style.width = '100%';
        iframe.style.height = '700px';
        iframe.style.border = 'none';
        iframe.style.borderRadius = '8px';
        iframe.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
        iframe.style.opacity = '0'; // Start invisible
        iframe.style.transition = 'opacity 0.3s ease'; // Smooth transition
        
        // Pre-load iframe before showing
        iframe.onload = function() {
            console.log('Iframe loaded successfully');
            // Smooth fade in
            setTimeout(() => {
                iframe.style.opacity = '1';
                // Remove spinner after iframe is visible
                const spinner = pdfViewer.querySelector('.spinner');
                if (spinner) {
                    spinner.style.opacity = '0';
                    setTimeout(() => {
                        if (spinner.parentNode) {
                            spinner.parentNode.removeChild(spinner);
                        }
                    }, 300);
                }
            }, 100);
        };

        // Error handler
        iframe.onerror = function() {
            console.log('Iframe failed, showing download option');
            showDownloadOption(pdfViewer);
        };

        // Add iframe to container (but keep it invisible initially)
        pdfViewer.appendChild(iframe);
        
        // Set timeout for iframe
        setTimeout(() => {
            const spinner = pdfViewer.querySelector('.spinner');
            if (spinner && iframe.style.opacity === '0') {
                console.log('Iframe timeout, showing download option');
                showDownloadOption(pdfViewer);
            }
        }, 5000);
    }

    function showDownloadOption(pdfViewer) {
        pdfViewer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-file-pdf"></i>
                <h4>PDF Tidak Dapat Ditampilkan</h4>
                <p>Browser Anda tidak dapat menampilkan PDF secara langsung.</p>
                <p>Silakan download file untuk melihat dokumen:</p>
                <div style="margin-top: 1rem;">
                    <a href="{{ route('mahasiswa.proposal.download', [$proposal->id_proposal, 'proposal']) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-download me-1"></i>Download PDF
                    </a>
                </div>
            </div>
        `;
    }

    

    function loadRevisionDocuments() {
        @if($proposal->proposalRevisi->count() > 0)
            @foreach($proposal->proposalRevisi as $revisi)
                loadRevisionPDF({{ $revisi->id_revisi }}, '{{ asset('storage/' . $revisi->path_file) }}');
            @endforeach
        @endif
    }

    function loadRevisionPDF(revisiId, pdfUrl) {
        const pdfViewer = document.getElementById(`revisiPdfViewer-${revisiId}`);
        
        console.log('Loading revision PDF:', { revisiId, pdfUrl });
        
        if (!pdfUrl) {
            pdfViewer.innerHTML = '<div class="empty-state"><i class="fas fa-file-pdf"></i><h4>Dokumen Tidak Tersedia</h4><p>Dokumen revisi tidak ditemukan.</p></div>';
            return;
        }

        // Create iframe
        const iframe = document.createElement('iframe');
        iframe.src = pdfUrl;
        iframe.className = 'pdf-iframe';
        iframe.style.width = '100%';
        iframe.style.height = '700px';
        iframe.style.border = 'none';
        iframe.style.borderRadius = '8px';
        iframe.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
        iframe.style.opacity = '0';
        iframe.style.transition = 'opacity 0.3s ease';
        
        // Pre-load iframe before showing
        iframe.onload = function() {
            console.log('Revision iframe loaded successfully:', revisiId);
            setTimeout(() => {
                iframe.style.opacity = '1';
                const spinner = pdfViewer.querySelector('.spinner');
                if (spinner) {
                    spinner.style.opacity = '0';
                    setTimeout(() => {
                        if (spinner.parentNode) {
                            spinner.parentNode.removeChild(spinner);
                        }
                    }, 300);
                }
            }, 100);
        };

        // Error handler
        iframe.onerror = function() {
            console.log('Revision iframe failed:', revisiId);
            showRevisionDownloadOption(pdfViewer, revisiId);
        };

        pdfViewer.appendChild(iframe);
        
        // Set timeout for iframe
        setTimeout(() => {
            const spinner = pdfViewer.querySelector('.spinner');
            if (spinner && iframe.style.opacity === '0') {
                console.log('Revision iframe timeout:', revisiId);
                showRevisionDownloadOption(pdfViewer, revisiId);
            }
        }, 5000);
    }

    function showRevisionDownloadOption(pdfViewer, revisiId) {
        pdfViewer.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-file-pdf"></i>
                <h4>PDF Tidak Dapat Ditampilkan</h4>
                <p>Browser Anda tidak dapat menampilkan PDF secara langsung.</p>
                <p>Silakan download file untuk melihat dokumen:</p>
                <div style="margin-top: 1rem;">
                    <a href="{{ asset('storage/' . ($proposal->proposalRevisi->first()->path_file ?? '')) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-download me-1"></i>Download PDF
                    </a>
                </div>
            </div>
        `;
    }

    function initializeFullscreen() {
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        const revisiFullscreenBtn = document.getElementById('revisiFullscreenBtn');
        const pdfViewer = document.getElementById('pdfViewer');
        
        // Original PDF fullscreen
        if (fullscreenBtn && pdfViewer) {
            fullscreenBtn.addEventListener('click', function() {
                const iframe = pdfViewer.querySelector('.pdf-iframe');
                if (iframe) {
                    if (iframe.requestFullscreen) {
                        iframe.requestFullscreen();
                    } else if (iframe.webkitRequestFullscreen) {
                        iframe.webkitRequestFullscreen();
                    } else if (iframe.msRequestFullscreen) {
                        iframe.msRequestFullscreen();
                    }
                } else {
                    if (pdfViewer.requestFullscreen) {
                        pdfViewer.requestFullscreen();
                    } else if (pdfViewer.webkitRequestFullscreen) {
                        pdfViewer.webkitRequestFullscreen();
                    } else if (pdfViewer.msRequestFullscreen) {
                        pdfViewer.msRequestFullscreen();
                    }
                }
            });
        }

        // Revision PDF fullscreen
        if (revisiFullscreenBtn) {
            revisiFullscreenBtn.addEventListener('click', function() {
                const activeTab = document.querySelector('#revisionTabContent .tab-pane.active');
                if (activeTab) {
                    const iframe = activeTab.querySelector('.pdf-iframe');
                    if (iframe) {
                        if (iframe.requestFullscreen) {
                            iframe.requestFullscreen();
                        } else if (iframe.webkitRequestFullscreen) {
                            iframe.webkitRequestFullscreen();
                        } else if (iframe.msRequestFullscreen) {
                            iframe.msRequestFullscreen();
                        }
                    }
                }
            });
        }
    }

    function showToast(message, type = 'info') {
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
</script>
@endsection
