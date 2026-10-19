@extends('mainlayout.app')

@section('title', 'Revisi Proposal PKM - Mahasiswa')

@section('styles')
<style>
    .file-upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 2.5rem 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        background-color: #f8fafc;
        cursor: pointer;
    }
    
    .file-upload-area:hover, .file-upload-area.dragover {
        border-color: var(--primary-700, #8B0000);
        background-color: rgba(139, 0, 0, 0.03);
    }
    
    .file-upload-icon {
        font-size: 2.5rem;
        color: var(--primary-700, #8B0000);
        margin-bottom: 0.75rem;
    }
    
    .file-info {
        background-color: #f1f5f9;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        display: none;
    }
    
    .file-info.show {
        display: block;
    }
    
    .progress-bar-custom {
        height: 6px;
        border-radius: 3px;
        background-color: #e2e8f0;
        overflow: hidden;
        margin-top: 0.5rem;
    }
    
    .progress-fill {
        height: 100%;
        background: var(--primary-700, #8B0000);
        width: 0%;
        transition: width 0.3s ease;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
        ['label' => 'Revisi Proposal', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Revisi Usulan Proposal PKM" 
        subtitle="Perbaiki dan unggah kembali berkas proposal sesuai catatan evaluasi tim reviewer" 
        badge="Mahasiswa Portal"
        icon="fa-file-pen"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Status Perbaikan Banner -->
    <div class="alert alert-info border-0 rounded-3 p-3.5 mb-4 shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <div class="p-2.5 bg-info text-white rounded-circle d-flex align-items-center justify-content-center">
                <i class="fas fa-calendar-check fs-5"></i>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="fw-bold fs-6">Jadwal Periode Perbaikan:</span>
                    <x-status-badge :status="$ruangKontrol ? $ruangKontrol->status_perbaikan : 'tertutup'" />
                </div>
                @if(!$ruangKontrol)
                    <div class="text-warning small">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Ruang kontrol jadwal belum diatur untuk tahun ajaran aktif ini.
                    </div>
                @endif
                @if($ruangKontrol && $ruangKontrol->tanggal_perbaikan_mulai && $ruangKontrol->tanggal_perbaikan_selesai)
                    <div class="small text-secondary">
                        Rentang Waktu: {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai)->format('d M Y') }}
                    </div>
                    @php
                        $deadline = \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai);
                        $daysLeft = (int) now()->diffInDays($deadline, false);
                    @endphp
                    @if($daysLeft > 0)
                        <div class="text-primary small fw-semibold mt-1">
                            <i class="fas fa-hourglass-half me-1"></i>Sisa waktu pengunggahan: {{ $daysLeft }} hari lagi
                        </div>
                    @elseif($daysLeft == 0)
                        <div class="text-danger small fw-bold mt-1">
                            <i class="fas fa-exclamation-circle me-1"></i>Hari ini adalah batas waktu terakhir pengunggahan!
                        </div>
                    @else
                        <div class="text-danger small fw-bold mt-1">
                            <i class="fas fa-times-circle me-1"></i>Batas waktu perbaikan telah terlewati.
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Informasi Proposal Card -->
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-file-alt text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Informasi Proposal</h6>
            </div>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 rounded-pill small">
                Skim: {{ $proposal->skim }}
            </span>
        </div>
        <div class="ui-card-body p-4">
            <h5 class="fw-bold text-dark mb-3" style="line-height: 1.4;">
                {{ $proposal->judul_proposal }}
            </h5>
            <div class="row g-3 pt-2 border-top">
                <div class="col-sm-6 col-md-3">
                    <label class="small text-secondary fw-semibold d-block mb-1">Skim Kegiatan</label>
                    <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill fw-semibold">{{ $proposal->skim }}</span>
                </div>
                <div class="col-sm-6 col-md-3">
                    <label class="small text-secondary fw-semibold d-block mb-1">Usulan Dana</label>
                    <span class="fw-bold text-primary">@rupiahId($proposal->dana_diajukan)</span>
                </div>
                <div class="col-sm-6 col-md-3">
                    <label class="small text-secondary fw-semibold d-block mb-1">Status Proposal</label>
                    <x-status-badge :status="$proposal->status" :label="$proposal->status_label" />
                </div>
                <div class="col-sm-6 col-md-3">
                    <label class="small text-secondary fw-semibold d-block mb-1">Waktu Diajukan</label>
                    <span class="text-secondary small"><i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::parse($proposal->created_at)->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Catatan Review dari Reviewer -->
    @php
        $adminReview = $proposal->nilaiAdministratif->where('id_reviewer', $proposal->id_reviewer_administratif)->first();
        $checklistChecked = $adminReview && $adminReview->checklist ? $adminReview->checklist : [];
        $hasilSemiFinal = $proposal->hasilSemiFinal;
        
        $substantifReviews = collect();
        if ($proposal->id_reviewer_substantif_1) {
            $review1 = $proposal->nilaiSubstantif
                ->where('id_reviewer', $proposal->id_reviewer_substantif_1)
                ->where('jenis_review', 'pertama')
                ->first() ?? $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_1)->first();
            $note1 = $review1->note_substantif ?? $review1?->catatan;
            if ($review1 && $note1 && $note1 !== 'Review substantif dimulai' && !empty(trim($note1))) {
                $substantifReviews->push($review1);
            }
        }
        if ($proposal->id_reviewer_substantif_2) {
            $review2 = $proposal->nilaiSubstantif
                ->where('id_reviewer', $proposal->id_reviewer_substantif_2)
                ->where('jenis_review', 'pertama')
                ->first() ?? $proposal->nilaiSubstantif->where('id_reviewer', $proposal->id_reviewer_substantif_2)->first();
            $note2 = $review2->note_substantif ?? $review2?->catatan;
            if ($review2 && $note2 && $note2 !== 'Review substantif dimulai' && !empty(trim($note2))) {
                $substantifReviews->push($review2);
            }
        }
    @endphp

    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-comments text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Catatan Evaluasi Reviewer</h6>
            </div>
            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                Panduan Perbaikan
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
                    <div class="small fw-bold text-danger mb-2"><i class="fas fa-exclamation-triangle me-1"></i>Kesalahan Administratif yang Ditemukan:</div>
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
                    <h6 class="fw-bold text-success mb-0"><i class="fas fa-trophy me-2"></i>Hasil Evaluasi Semi Final</h6>
                    <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($hasilSemiFinal->updated_at)->format('d M Y H:i') }}</small>
                </div>
                <div class="p-3 bg-white rounded-3 border">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        @if($hasilSemiFinal->status_final == 'lolos_tingkat_universitas')
                            <span class="badge bg-success text-white px-3 py-1.5 rounded-pill fw-bold"><i class="fas fa-check-circle me-1"></i>Lolos Tingkat Universitas</span>
                        @else
                            <span class="badge bg-danger text-white px-3 py-1.5 rounded-pill fw-bold"><i class="fas fa-times-circle me-1"></i>Tidak Lolos Tingkat Universitas</span>
                        @endif
                    </div>
                    @if($hasilSemiFinal->catatan_final)
                        <div class="small text-secondary fw-semibold mt-2">Catatan:</div>
                        <div class="small text-dark">{{ $hasilSemiFinal->catatan_final }}</div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Review Substantif -->
            @if($substantifReviews->count() > 0)
                @foreach($substantifReviews as $index => $review)
                <div class="p-3 rounded-3 border mb-3 bg-light" style="border-left: 4px solid #0284c7 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold text-info mb-0"><i class="fas fa-user-graduate me-2"></i>Review Substantif - Reviewer {{ $index + 1 }}</h6>
                        <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($review->updated_at)->format('d M Y H:i') }}</small>
                    </div>
                    <div class="p-3 bg-white rounded-3 border mt-2">
                        <div class="small text-dark">{{ $review->note_substantif ?? $review->catatan }}</div>
                    </div>
                </div>
                @endforeach
            @endif

            @if(!$adminReview && !$hasilSemiFinal && $substantifReviews->count() == 0)
            <div class="text-center py-4 text-muted">
                <i class="fas fa-info-circle fs-3 mb-2 text-secondary"></i>
                <p class="mb-0">Belum ada catatan evaluasi dari reviewer untuk proposal ini.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Upload File Revisi -->
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-upload text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Formulir Upload File Revisi</h6>
            </div>
            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                PDF Maksimal 5MB
            </span>
        </div>
        <div class="ui-card-body p-4">
            <form action="{{ route('mahasiswa.revisi.store') }}" method="POST" enctype="multipart/form-data" id="revisiForm">
                @csrf
                
                <div class="file-upload-area" id="revisiUploadArea">
                    <div class="file-upload-icon">
                        <i class="fas fa-cloud-arrow-up"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Tarik & Letakkan File PDF Revisi Di Sini</h6>
                    <p class="text-muted small mb-3">atau klik tombol di bawah untuk memilih berkas dari perangkat Anda</p>
                    <input type="file" id="file_revisi" name="file_revisi" accept=".pdf" style="display: none;" required>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4" onclick="document.getElementById('file_revisi').click()">
                        <i class="fas fa-folder-open me-2"></i>Pilih Berkas PDF
                    </button>
                </div>
                @error('file_revisi')
                    <div class="invalid-feedback d-block mt-2">
                        {{ $message }}
                    </div>
                @enderror
                
                <div class="file-info" id="revisiFileInfo">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong id="revisiFileName" class="text-dark">Nama file</strong>
                            <br><small id="revisiFileSize" class="text-muted">Ukuran file</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle p-1" style="width: 28px; height: 28px;" onclick="removeFile()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" id="revisiProgress"></div>
                    </div>
                </div>
                
                <div class="alert alert-warning mt-3 rounded-3 border-0 small">
                    <div class="fw-bold mb-1"><i class="fas fa-circle-info me-1"></i>Ketentuan Pengunggahan Revisi:</div>
                    <ul class="mb-0 ps-3">
                        <li>Format berkas wajib PDF (.pdf) dengan batas ukuran file maksimal 5MB.</li>
                        <li>Pastikan seluruh perbaikan telah mengakomodasi catatan evaluasi tim reviewer di atas.</li>
                    </ul>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm" id="submitBtn">
                        <i class="fas fa-upload me-2"></i>Kirim Berkas Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat File Revisi -->
    @if($revisi->count() > 0)
    <div class="ui-card mb-4">
        <div class="ui-card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-history text-primary"></i>
                <h6 class="mb-0 fw-bold fs-6">Riwayat Berkas Revisi yang Diunggah</h6>
            </div>
            <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill small">
                {{ $revisi->count() }} Berkas
            </span>
        </div>
        <div class="ui-card-body p-4">
            <div class="row g-3">
                @foreach($revisi as $item)
                <div class="col-md-6">
                    <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2.5 bg-danger-subtle text-danger rounded-3">
                                <i class="fas fa-file-pdf fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $item->nama_file }}</h6>
                                <small class="text-muted"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($item->tanggal_submit)->format('d M Y H:i') }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('mahasiswa.revisi.download', $item->id_revisi) }}" 
                               class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-download me-1"></i>Download
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5" 
                                    onclick="deleteRevisi({{ $item->id_revisi }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 border-0 shadow">
            <div class="modal-header border-bottom">
                <h6 class="modal-title fw-bold text-dark">
                    <i class="fas fa-trash text-danger me-2"></i>Konfirmasi Hapus Berkas Revisi
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0 text-secondary">Apakah Anda yakin ingin menghapus berkas revisi ini? Berkas yang dihapus tidak dapat dipulihkan kembali.</p>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger rounded-pill px-4 fw-semibold" id="confirmDelete">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let deleteId = null;

    function setupFileUpload() {
        const input = document.getElementById('file_revisi');
        const area = document.getElementById('revisiUploadArea');
        const info = document.getElementById('revisiFileInfo');
        const fileName = document.getElementById('revisiFileName');
        const fileSize = document.getElementById('revisiFileSize');
        const progress = document.getElementById('revisiProgress');

        area.addEventListener('click', () => input.click());

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
            if (file.type !== 'application/pdf') {
                showToast('File harus berformat PDF!', 'error');
                input.value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showToast('Ukuran file maksimal 5MB!', 'error');
                input.value = '';
                return;
            }

            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            info.classList.add('show');
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
        }, 15);
    }

    function deleteRevisi(id) {
        deleteId = id;
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    document.getElementById('confirmDelete').addEventListener('click', function() {
        if (deleteId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/mahasiswa/revisi/${deleteId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });

    document.getElementById('revisiForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengupload Berkas...';
        submitBtn.disabled = true;
    });

    function showToast(message, type = 'info', duration = 3000) {
        if (window.AppUI?.showToast) {
            window.AppUI.showToast(message, type, duration);
        } else {
            console.log(`[Toast ${type}] ${message}`);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        setupFileUpload();
    });
</script>
@endsection