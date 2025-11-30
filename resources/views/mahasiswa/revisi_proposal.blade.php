@extends('mainlayout.app')

@section('title', 'Revisi Proposal PKM')

@section('styles')
<style>
    .revisi-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .revisi-section:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .section-title {
        color: var(--primary-color);
        font-weight: bold;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color);
        display: flex;
        align-items: center;
    }
    
    .file-upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        padding: 3rem 2rem;
        text-align: center;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
        cursor: pointer;
    }
    
    .file-upload-area:hover {
        border-color: var(--primary-color);
        background-color: rgba(139, 58, 58, 0.05);
    }
    
    .file-upload-area.dragover {
        border-color: var(--primary-color);
        background-color: rgba(139, 58, 58, 0.1);
        transform: scale(1.02);
    }
    
    .file-upload-icon {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }
    
    .file-info {
        background-color: #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        display: none;
    }
    
    .file-info.show {
        display: block;
    }
    
    .progress-bar-custom {
        height: 8px;
        border-radius: 4px;
        background-color: #e9ecef;
        overflow: hidden;
        margin-top: 0.5rem;
    }
    
    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        width: 0%;
        transition: width 0.3s ease;
    }
    
    .revisi-item {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .revisi-item:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .file-icon {
        font-size: 2rem;
        color: var(--primary-color);
    }
    
    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .btn-download {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
    }
    
    .btn-download:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        color: white;
    }
    
    .btn-delete {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        color: white;
    }
    
    .btn-delete:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        color: white;
    }
    
    .status-badge {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
    }
    
    .alert-info {
        border-left: 4px solid var(--primary-color);
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    }
    
    /* Review Section Styles */
    .review-section {
        background: white;
    }
    
    .review-card {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }
    
    .review-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(139, 58, 58, 0.15);
    }
    
    .administratif-review {
        border-left: 5px solid #8b3a3a;
    }
    
    .semi-final-review {
        border-left: 5px solid #28a745;
    }
    
    .substantif-review {
        border-left: 5px solid #6c757d;
    }
    
    .review-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .review-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .semi-final-review .review-icon {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }
    
    .substantif-review .review-icon {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    }
    
    .review-title h5 {
        color: var(--primary-color);
        font-weight: 600;
        margin: 0;
        font-size: 1.1rem;
    }
    
    .semi-final-review .review-title h5 {
        color: #28a745;
    }
    
    .substantif-review .review-title h5 {
        color: #6c757d;
    }
    
    .review-date {
        color: #6c757d;
        font-size: 0.875rem;
    }
    
    /* Checklist Errors */
    .checklist-errors {
        background: #fff5f5;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1rem;
    }
    
    .checklist-title {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 1rem;
    }
    
    .checklist-items {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .checklist-item {
        display: flex;
        align-items: flex-start;
        padding: 0.75rem;
        background: white;
        border-radius: 6px;
        border-left: 3px solid #dc3545;
        transition: all 0.2s ease;
    }
    
    .checklist-item:hover {
        background: #fff5f5;
        transform: translateX(5px);
    }
    
    .checklist-item i {
        margin-top: 0.2rem;
        flex-shrink: 0;
    }
    
    .checklist-item span {
        color: #333;
        line-height: 1.5;
    }
    
    /* Review Note */
    .review-note {
        background: #f8f9fa;
        border-left: 4px solid var(--primary-color);
        border-radius: 6px;
        padding: 1rem;
        margin-top: 1rem;
    }
    
    .note-title {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }
    
    .note-content {
        color: #495057;
        line-height: 1.6;
    }
    
    /* Status Badge */
    .status-badge-lolos {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 2px solid #28a745;
        border-radius: 8px;
        padding: 1rem 1.5rem;
        color: #155724;
        font-size: 1.1rem;
        text-align: center;
    }
    
    .status-badge-tidak-lolos {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border: 2px solid #dc3545;
        border-radius: 8px;
        padding: 1rem 1.5rem;
        color: #721c24;
        font-size: 1.1rem;
        text-align: center;
    }
    
    /* Dosen Info */
    .dosen-info {
        background: #e7f3ff;
        border: 1px solid #b3d9ff;
        border-radius: 8px;
        padding: 1.25rem;
        margin-top: 1rem;
    }
    
    .info-title {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
    }
    
    .info-content {
        color: #495057;
    }
    
    .info-content p {
        margin-bottom: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <x-page-header 
        title="Revisi Proposal PKM" 
    />
    <!-- Status Perbaikan -->
    <div class="alert alert-info">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle me-3 fa-2x"></i>
            <div>
                <h5 class="mb-1">Status Perbaikan: 
                    <span class="badge bg-success status-badge">
                        {{ ucfirst($ruangKontrol ? $ruangKontrol->status_perbaikan : 'tertutup') }}
                    </span>
                </h5>
                @if(!$ruangKontrol)
                    <p class="mb-0 text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Ruang kontrol tidak ditemukan untuk tahun ajaran ini.</strong>
                    </p>
                @endif
                @if($ruangKontrol && $ruangKontrol->tanggal_perbaikan_mulai && $ruangKontrol->tanggal_perbaikan_selesai)
                    <p class="mb-0">
                        Periode: {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_mulai)->format('d M Y') }} - 
                        {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai)->format('d M Y') }}
                    </p>
                    @php
                        $deadline = \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai);
                        $daysLeft = (int) now()->diffInDays($deadline, false);
                    @endphp
                    @if($daysLeft > 0)
                        <p class="mb-0 text-warning">
                            <i class="fas fa-clock me-1"></i>
                            <strong>Sisa waktu: {{ $daysLeft }} hari</strong>
                        </p>
                    @elseif($daysLeft == 0)
                        <p class="mb-0 text-danger">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Hari terakhir!</strong>
                        </p>
                    @else
                        <p class="mb-0 text-danger">
                            <i class="fas fa-times-circle me-1"></i>
                            <strong>Batas waktu telah terlampaui {{ abs($daysLeft) }} hari</strong>
                        </p>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Informasi Proposal -->
    <div class="revisi-section">
        <h4 class="section-title">
            <i class="fas fa-clipboard-list me-2"></i>Informasi Proposal
        </h4>
        
        <div class="row">
            <div class="col-md-8">
                <h5 class="text-primary">{{ $proposal->judul_proposal }}</h5>
                <p class="text-muted mb-2">
                    <strong>Skim:</strong> {{ $proposal->skim }} | 
                    <strong>Dana:</strong> Rp {{ number_format($proposal->dana_diajukan, 0, ',', '.') }}
                </p>
                <p class="text-muted mb-0">
                    <strong>Status:</strong> 
                    <span class="badge bg-warning">{{ ucfirst($proposal->status) }}</span>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <small class="text-muted">
                    <i class="fas fa-calendar me-1"></i>
                    Diajukan: {{ \Carbon\Carbon::parse($proposal->created_at)->format('d M Y H:i') }}
                </small>
            </div>
        </div>
    </div>

    <!-- Catatan Review -->
    <div class="revisi-section review-section">
        <h4 class="section-title">
            <i class="fas fa-comments me-2"></i>Catatan Review dari Reviewer
        </h4>
        
        @php
            $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', $proposal->id_reviewer_administratif)->first();
            $checklistConfig = \App\Helpers\ProposalHelper::getReviewChecklist($proposal->skim);
            $checklistChecked = $adminReview && $adminReview->checklist ? $adminReview->checklist : [];
            $hasilSemiFinal = $proposal->hasilSemiFinal;
        @endphp
        
        <!-- Review Administratif -->
        @if($adminReview)
        <div class="review-card administratif-review">
            <div class="review-header">
                <div class="review-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="review-title">
                    <h5>Review Administratif</h5>
                    <small class="review-date">
                    <i class="fas fa-clock me-1"></i>
                    {{ \Carbon\Carbon::parse($adminReview->updated_at)->format('d M Y H:i') }}
                </small>
            </div>
            </div>
            
            <!-- Kesalahan Administratif yang Dicentang -->
            @if(!empty($checklistChecked) && is_array($checklistChecked))
            <div class="checklist-errors">
                <h6 class="checklist-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Kesalahan Administratif yang Ditemukan:
                </h6>
                <div class="checklist-items">
                    @php
                        // Flatten checklist config untuk mapping
                        $checklistMap = [];
                        foreach ($checklistConfig as $category => $items) {
                            if (is_array($items)) {
                                if (isset($items['items'])) {
                                    // Format baru dengan kategori
                                    foreach ($items['items'] as $item) {
                                        if (is_array($item) && isset($item['text'])) {
                                            $checklistMap[$item['text']] = [
                                                'text' => $item['text'],
                                                'category' => $category
                                            ];
                                        } elseif (is_string($item)) {
                                            $checklistMap[$item] = [
                                                'text' => $item,
                                                'category' => $category
                                            ];
                                        }
                                    }
                                } else {
                                    // Format lama (array langsung)
                                    foreach ($items as $item) {
                                        if (is_array($item) && isset($item['text'])) {
                                            $checklistMap[$item['text']] = [
                                                'text' => $item['text'],
                                                'category' => $category
                                            ];
                                        } elseif (is_string($item)) {
                                            $checklistMap[$item] = [
                                                'text' => $item,
                                                'category' => $category
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    @endphp
                    
                    @if(count($checklistChecked) > 0)
                        @foreach($checklistChecked as $checkedItem)
                            @php
                                $itemText = is_string($checkedItem) ? $checkedItem : (isset($checkedItem['text']) ? $checkedItem['text'] : '');
                            @endphp
                            @if(!empty($itemText))
                                <div class="checklist-item">
                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                    <span>{{ $itemText }}</span>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-muted text-center py-2">
                            <i class="fas fa-info-circle me-2"></i>
                            Tidak ada kesalahan administratif yang dicentang
                        </div>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Catatan Review Administratif -->
            @if($adminReview->note_administratif)
            <div class="review-note">
                <h6 class="note-title">
                    <i class="fas fa-sticky-note me-2"></i>Catatan Reviewer:
                </h6>
                <div class="note-content">
                    <p class="mb-0">{{ $adminReview->note_administratif }}</p>
                </div>
            </div>
            @endif
        </div>
        @endif
        
        <!-- Hasil Semi Final (Status Tingkat Universitas) -->
        @if($hasilSemiFinal)
        <div class="review-card semi-final-review">
            <div class="review-header">
                <div class="review-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="review-title">
                    <h5>Hasil Semi Final - Tingkat Universitas</h5>
                    <small class="review-date">
                        <i class="fas fa-calendar me-1"></i>
                        {{ \Carbon\Carbon::parse($hasilSemiFinal->updated_at)->format('d M Y H:i') }}
                    </small>
                </div>
            </div>
            
            <div class="semi-final-status">
                @if($hasilSemiFinal->status_final == 'lolos_tingkat_universitas')
                    <div class="status-badge-lolos">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Lolos Tingkat Universitas</strong>
                    </div>
                    @if($proposal->dosenPendampingUniversitas)
                    <div class="dosen-info mt-3">
                        <h6 class="info-title">
                            <i class="fas fa-user-tie me-2"></i>Dosen Pendamping Universitas:
                        </h6>
                        <div class="info-content">
                            <p class="mb-1"><strong>Nama:</strong> {{ $proposal->dosenPendampingUniversitas->nama_dosen }}</p>
                            <p class="mb-1"><strong>Email:</strong> {{ $proposal->dosenPendampingUniversitas->email_dosen }}</p>
                            <p class="mb-0"><strong>No. HP:</strong> {{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}</p>
                        </div>
                    </div>
                    @endif
                @else
                    <div class="status-badge-tidak-lolos">
                        <i class="fas fa-times-circle me-2"></i>
                        <strong>Tidak Lolos Tingkat Universitas</strong>
                    </div>
                @endif
                
                @if($hasilSemiFinal->catatan_final)
                <div class="review-note mt-3">
                    <h6 class="note-title">
                        <i class="fas fa-sticky-note me-2"></i>Catatan:
                    </h6>
                    <div class="note-content">
                        <p class="mb-0">{{ $hasilSemiFinal->catatan_final }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Review Substantif (Catatan Saja, Tanpa Nilai) -->
        @php
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
        @endphp
        
        @if($substantifReviews->count() > 0)
            @foreach($substantifReviews as $index => $review)
                <div class="review-card substantif-review">
                    <div class="review-header">
                        <div class="review-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="review-title">
                            <h5>Review Substantif - Reviewer {{ $index + 1 }}</h5>
                            <small class="review-date">
                                <i class="fas fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($review->updated_at)->format('d M Y H:i') }}
                            </small>
                        </div>
                    </div>
                    
                    <div class="review-note">
                        <h6 class="note-title">
                            <i class="fas fa-sticky-note me-2"></i>Catatan Reviewer:
                        </h6>
                        <div class="note-content">
                            <p class="mb-0">{{ $review->note_substantif }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
        
        @if(!$adminReview && !$hasilSemiFinal && $substantifReviews->count() == 0)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Belum ada catatan review yang tersedia. Silakan tunggu hingga reviewer menyelesaikan review mereka.
        </div>
        @endif
    </div>

    <!-- Upload File Revisi -->
    <div class="revisi-section">
        <h4 class="section-title">
            <i class="fas fa-upload me-2"></i>Upload File Revisi
        </h4>
        
        <form action="{{ route('mahasiswa.revisi.store') }}" method="POST" enctype="multipart/form-data" id="revisiForm">
            @csrf
            
            <div class="file-upload-area" id="revisiUploadArea">
                <div class="file-upload-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <h5>Upload File Revisi Proposal</h5>
                <p class="text-muted">Drag & drop file PDF di sini atau klik untuk memilih file</p>
                <input type="file" id="file_revisi" name="file_revisi" accept=".pdf" style="display: none;" required>
                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('file_revisi').click()">
                    <i class="fas fa-folder-open me-2"></i>Pilih File
                </button>
            </div>
            
            <div class="file-info" id="revisiFileInfo">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong id="revisiFileName">Nama file</strong>
                        <br><small id="revisiFileSize">Ukuran file</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="progress-bar-custom">
                    <div class="progress-fill" id="revisiProgress"></div>
                </div>
            </div>
            
            <div class="alert alert-warning mt-3">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Ketentuan Upload:</strong>
                <ul class="mb-0 mt-2">
                    <li>Format file harus PDF</li>
                    <li>Ukuran maksimal 5MB</li>
                    <li>File harus berisi proposal yang sudah direvisi sesuai catatan reviewer</li>
                </ul>
            </div>
            
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                    <i class="fas fa-upload me-2"></i>Upload File Revisi
                </button>
            </div>
        </form>
    </div>

    <!-- File Revisi Aktif -->
    @if($revisi->count() > 0)
    <div class="revisi-section">
        <h4 class="section-title">
            <i class="fas fa-file-pdf me-2"></i>File Revisi
        </h4>
        
        @php
            $revisiAktif = $revisi->first(); // Ambil file revisi terbaru (hanya ada 1)
        @endphp
        
        <div class="revisi-item">
            <div class="d-flex align-items-start">
                <div class="file-icon me-3">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $revisiAktif->nama_file }}</h6>
                    <p class="text-muted mb-2">
                        <small>
                            <i class="fas fa-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($revisiAktif->tanggal_submit)->format('d M Y H:i') }}
                        </small>
                    </p>
                    <div class="alert alert-info mb-2">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Jika Anda mengupload file revisi baru, file ini akan otomatis digantikan.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('mahasiswa.revisi.download', $revisiAktif->id_revisi) }}" 
                           class="btn btn-download btn-action">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                        <button type="button" class="btn btn-delete btn-action" 
                                onclick="deleteRevisi({{ $revisiAktif->id_revisi }})">
                            <i class="fas fa-trash me-1"></i>Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tombol Kembali -->
    <div class="text-center mt-4">
        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus file revisi ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let deleteId = null;

    // File upload handling
    function setupFileUpload() {
        const input = document.getElementById('file_revisi');
        const area = document.getElementById('revisiUploadArea');
        const info = document.getElementById('revisiFileInfo');
        const fileName = document.getElementById('revisiFileName');
        const fileSize = document.getElementById('revisiFileSize');
        const progress = document.getElementById('revisiProgress');

        // Click to upload
        area.addEventListener('click', () => input.click());

        // Drag and drop
        area.addEventListener('dragover', (e) => {
            e.preventDefault();
            area.classList.add('dragover');
        });

        area.addEventListener('dragleave', () => {
            area.classList.remove('dragover');
        });

        area.addEventListener('drop', (e) => {
            e.preventDefault();
            area.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                input.files = files;
                handleFileSelect();
            }
        });

        // File selection
        input.addEventListener('change', handleFileSelect);
    }

    function handleFileSelect() {
        const input = document.getElementById('file_revisi');
        const info = document.getElementById('revisiFileInfo');
        const fileName = document.getElementById('revisiFileName');
        const fileSize = document.getElementById('revisiFileSize');
        const progress = document.getElementById('revisiProgress');
        
        const file = input.files[0];
        if (file) {
            // Validate file type
            if (file.type !== 'application/pdf') {
                showToast('File harus berformat PDF!', 'error');
                input.value = '';
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showToast('Ukuran file maksimal 5MB!', 'error');
                input.value = '';
                return;
            }

            // Display file info
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            info.classList.add('show');

            // Simulate upload progress
            simulateUpload(progress);
        }
    }

    function removeFile() {
        const input = document.getElementById('file_revisi');
        const info = document.getElementById('revisiFileInfo');
        const progress = document.getElementById('revisiProgress');
        
        input.value = '';
        info.classList.remove('show');
        progress.style.width = '0%';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function simulateUpload(progressElement) {
        let width = 0;
        const interval = setInterval(() => {
            if (width >= 100) {
                clearInterval(interval);
            } else {
                width++;
                progressElement.style.width = width + '%';
            }
        }, 20);
    }

    function deleteRevisi(id) {
        deleteId = id;
        const modalElement = document.getElementById('deleteModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            console.error('Modal tidak ditemukan');
            if (confirm('Apakah Anda yakin ingin menghapus file revisi ini?')) {
                submitDelete(id);
            }
        }
    }

    function submitDelete(id) {
        if (!id) {
            console.error('ID tidak valid');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('/mahasiswa/revisi') }}/${id}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (csrfMeta) {
            csrfToken.value = csrfMeta.getAttribute('content');
        } else {
            console.error('CSRF token tidak ditemukan');
            showToast('CSRF token tidak ditemukan. Silakan refresh halaman.', 'error');
            return;
        }
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }

    // Initialize delete confirmation on page load
    document.addEventListener('DOMContentLoaded', function() {
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        if (confirmDeleteBtn) {
            // Remove existing event listeners if any
            const newConfirmDeleteBtn = confirmDeleteBtn.cloneNode(true);
            confirmDeleteBtn.parentNode.replaceChild(newConfirmDeleteBtn, confirmDeleteBtn);
            
            newConfirmDeleteBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (deleteId) {
                    // Close modal first
                    const modalElement = document.getElementById('deleteModal');
                    if (modalElement) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }
                    }
                    // Submit delete
                    setTimeout(() => {
                        submitDelete(deleteId);
                    }, 300);
                } else {
                    showToast('ID file tidak valid', 'error');
                }
            });
        } else {
            console.warn('Tombol confirmDelete tidak ditemukan');
        }
    });

    // Form submission
    document.getElementById('revisiForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengupload...';
        submitBtn.disabled = true;
    });

    // Show toast notification
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : type === 'warning' ? 'warning' : 'info'} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            <strong>${type === 'error' ? 'Error' : type === 'success' ? 'Sukses' : type === 'warning' ? 'Peringatan' : 'Info'}:</strong> ${message}
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 5000);
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        setupFileUpload();
    });
</script>
@endsection