@extends('mainlayout.app')

@section('title', 'Detail Proposal - Review Administratif')

@section('styles')
<style>
    .pdf-viewer-section {
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

    .pdf-container-full {
        position: relative;
        background: #f8fafc;
        min-height: 70vh;
    }

    .pdf-viewer-section.fullscreen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999;
        margin: 0;
        border-radius: 0;
    }
    
    .pdf-viewer-section.fullscreen .pdf-container-full,
    .pdf-viewer-section.fullscreen #pdfViewer {
        height: calc(100vh - 70px) !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('reviewer.dashboard')],
        ['label' => 'Review Administratif', 'url' => route('reviewer.review.administratif')],
        ['label' => 'Detail & Penilaian', 'active' => true],
    ]" />

    <!-- Page Header -->
    <x-page-header 
        title="Penilaian Review Administratif" 
        subtitle="Verifikasi kepatuhan format sistematika, berkas lampiran, dan kelengkapan dokumen" 
        badge="Reviewer Portal"
        icon="fa-clipboard-check"
    >
        <x-slot:actions>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold" onclick="goBackToReviewerDashboard()">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </button>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- PDF Viewer Section - Full Width -->
    <div class="pdf-viewer-section mb-4">
        <div class="pdf-header">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-file-pdf text-danger fs-5"></i>
                <h6 class="mb-0 fw-bold text-dark text-truncate" style="max-width: 600px;">
                    Dokumen Proposal: {{ $proposal->judul_proposal }}
                </h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if($proposal->dokumen && $proposal->dokumen->path_file)
                    <a href="{{ route('file.serve', ['path' => $proposal->dokumen->path_file], false) }}" 
                       class="btn btn-sm btn-outline-primary rounded-pill px-3" target="_blank">
                        <i class="fas fa-download me-1"></i>Unduh PDF
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="toggleFullscreen()">
                        <i class="fas fa-expand me-1"></i>Fullscreen
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" onclick="refreshPDFViewer()">
                        <i class="fas fa-redo me-1"></i>Refresh
                    </button>
                @endif
            </div>
        </div>
        <div class="p-0">
            @if($proposal->dokumen && $proposal->dokumen->path_file)
                <div class="pdf-container-full">
                    <iframe 
                        id="pdfViewer"
                        src="{{ route('file.serve', ['path' => $proposal->dokumen->path_file], false) }}"
                        style="width: 100%; height: 70vh; border: none;"
                        allowfullscreen>
                    </iframe>
                </div>
            @else
                <div class="empty-state-box py-5">
                    <i class="fas fa-file-pdf text-muted mb-3 fs-1"></i>
                    <h5 class="fw-bold text-dark">Berkas Dokumen Tidak Tersedia</h5>
                    <p class="text-secondary small">Dokumen proposal belum diunggah atau tidak dapat diakses.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Metadata -->
        <div class="col-lg-4">
            <!-- Info Proposal -->
            <div class="ui-card mb-4">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Informasi Proposal</h6>
                    </div>
                </div>
                <div class="ui-card-body p-3">
                    <div class="mb-2">
                        <small class="text-secondary fw-semibold d-block">Judul Usulan</small>
                        <div class="fw-bold text-dark small">{{ $proposal->judul_proposal }}</div>
                    </div>
                    <div class="row g-2 pt-2 border-top">
                        <div class="col-6">
                            <small class="text-secondary fw-semibold d-block">Skim</small>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">{{ $proposal->skim }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-secondary fw-semibold d-block">Tahun</small>
                            <span class="text-dark small">{{ $proposal->tahun ?? 'N/A' }}</span>
                        </div>
                        <div class="col-12 mt-2">
                            <small class="text-secondary fw-semibold d-block">Status Proposal</small>
                            <x-status-badge :status="$proposal->status" :label="$proposal->status_label" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Mahasiswa -->
            <div class="ui-card mb-4">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-user-graduate text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Ketua Pengaju</h6>
                    </div>
                </div>
                <div class="ui-card-body p-3">
                    <div class="fw-bold text-dark">{{ $proposal->mahasiswa->nama_mhs ?? 'N/A' }}</div>
                    <small class="text-muted d-block">NIM: {{ $proposal->mahasiswa->nim ?? 'N/A' }}</small>
                    <small class="text-muted d-block">{{ $proposal->mahasiswa->prodi_mhs ?? 'N/A' }} - {{ $proposal->mahasiswa->fakultas_mhs ?? 'N/A' }}</small>
                    @if(isset($proposal->mahasiswa->email_mhs))
                        <small class="text-muted d-block mt-1"><i class="fas fa-envelope me-1"></i>{{ $proposal->mahasiswa->email_mhs }}</small>
                    @endif
                </div>
            </div>

            <!-- Info Dosen Pendamping -->
            <div class="ui-card mb-4">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-chalkboard-teacher text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Dosen Pendamping</h6>
                    </div>
                </div>
                <div class="ui-card-body p-3">
                    <div class="fw-bold text-dark">{{ $proposal->dosen->nama_dosen ?? 'N/A' }}</div>
                    @if(isset($proposal->dosen->nuptk))
                        <small class="text-muted d-block">NIDN/NUPTK: {{ $proposal->dosen->nuptk }}</small>
                    @endif
                    @if(isset($proposal->dosen->email_dosen))
                        <small class="text-muted d-block mt-1"><i class="fas fa-envelope me-1"></i>{{ $proposal->dosen->email_dosen }}</small>
                    @endif
                </div>
            </div>

            <!-- Status Review Saat Ini -->
            @php
                $existingReview = $proposal->nilaiAdministratif->where('id_reviewer', auth()->user()->id_reviewer)->first();
                $isReviewCompleted = $existingReview && $existingReview->note_administratif;
            @endphp
            <div class="ui-card mb-4">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-history text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Status Review Anda</h6>
                    </div>
                </div>
                <div class="ui-card-body p-3 text-center">
                    @if($isReviewCompleted)
                        <div class="p-2.5 rounded-3 bg-success-subtle text-success mb-2">
                            <i class="fas fa-check-circle fs-3 mb-1 d-block"></i>
                            <span class="fw-bold small">Review Selesai Disimpan</span>
                        </div>
                        <small class="text-muted">Terakhir diupdate: {{ $existingReview->updated_at->format('d/m/Y H:i') }}</small>
                    @elseif($existingReview)
                        <div class="p-2.5 rounded-3 bg-warning-subtle text-warning mb-2">
                            <i class="fas fa-clock fs-3 mb-1 d-block"></i>
                            <span class="fw-bold small">Draft Tersimpan (Belum Final)</span>
                        </div>
                        <small class="text-muted">Silakan lengkapi catatan review Anda</small>
                    @else
                        <div class="p-2.5 rounded-3 bg-info-subtle text-info mb-2">
                            <i class="fas fa-plus-circle fs-3 mb-1 d-block"></i>
                            <span class="fw-bold small">Belum Direview</span>
                        </div>
                        <small class="text-muted">Belum ada penilaian yang tersimpan</small>
                    @endif
                </div>
            </div>
        </div>

        <!-- Form Review Column -->
        <div class="col-lg-8">
            <div class="ui-card mb-4">
                <div class="ui-card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-edit text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Formulir Evaluasi Kesesuaian Administratif</h6>
                    </div>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1 rounded-pill small">
                        Tahap Administratif
                    </span>
                </div>
                <div class="ui-card-body p-4">
                    <form id="formReviewAdministratif" method="POST" action="{{ route('reviewer.submit.review.administratif', $proposal->id_proposal) }}" data-no-loading="true">
                        @csrf
                        
                        <!-- Checklist Kesalahan Administratif -->
                        <div class="mb-4">
                            <label class="form-label fw-bold d-block mb-1 fs-6">
                                <i class="fas fa-list-check text-primary me-2"></i>Daftar Checklist Kesalahan Administratif
                            </label>
                            <p class="text-muted small mb-3">
                                <strong>Perhatian:</strong> Centang butir di bawah <em>hanya jika ditemukan kesalahan/ketidaksesuaian</em> pada proposal mahasiswa. Jika proposal sudah sesuai pada butir tersebut, biarkan tidak tercentang.
                            </p>
                            
                            @php
                                $checklistItems = $checklist ?? [];
                                $existingChecklist = $existingReview ? ($existingReview->checklist ?? []) : [];
                                
                                if (empty($checklistItems)) {
                                    $checklistItems = \App\Helpers\ProposalHelper::getReviewChecklist('default');
                                }
                                
                                $allChecklistItems = [];
                                foreach ($checklistItems as $kategori => $items) {
                                    if (is_array($items)) {
                                        $allChecklistItems = array_merge($allChecklistItems, $items);
                                    }
                                }
                                
                                $orphanedItems = [];
                                if (!empty($existingChecklist) && is_array($existingChecklist)) {
                                    foreach ($existingChecklist as $item) {
                                        if (!in_array($item, $allChecklistItems)) {
                                            $orphanedItems[] = $item;
                                        }
                                    }
                                }
                            @endphp
                            
                            @if(!$dynamicForm)
                                @foreach($checklistItems as $kategori => $items)
                                    @if(is_array($items) && count($items) > 0)
                                        <div class="checklist-category mb-3">
                                            <div class="ui-card border mb-0">
                                                <div class="ui-card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                                    <span class="fw-semibold text-dark small">
                                                        <i class="fas fa-folder me-1 text-primary"></i>{{ $kategori }}
                                                    </span>
                                                    <span class="badge bg-light text-secondary border small" id="counter-{{ md5($kategori) }}">
                                                        0 / {{ count($items) }}
                                                    </span>
                                                </div>
                                                <div class="ui-card-body p-3">
                                                    <div class="row g-2">
                                                        @foreach($items as $item)
                                                            @php
                                                                $itemId = 'error_' . md5($kategori . '_' . $item);
                                                                $isChecked = in_array($item, $existingChecklist);
                                                            @endphp
                                                            <div class="col-md-6">
                                                                <div class="form-check p-2 rounded-2 hover-bg-light border mb-0" style="background: #fafafa;">
                                                                    <input class="form-check-input checklist-item ms-0 me-2" 
                                                                           type="checkbox" 
                                                                           name="kesalahan_administratif[]" 
                                                                           value="{{ $item }}" 
                                                                           id="{{ $itemId }}"
                                                                           data-category="{{ md5($kategori) }}"
                                                                           {{ $isChecked ? 'checked' : '' }}>
                                                                    <label class="form-check-label small text-dark" for="{{ $itemId }}" style="cursor: pointer;">
                                                                        {{ $item }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                
                                @if(!empty($orphanedItems))
                                    <div class="checklist-category mb-3">
                                        <div class="ui-card border border-warning mb-0">
                                            <div class="ui-card-header bg-warning-subtle py-2 px-3 d-flex justify-content-between align-items-center">
                                                <span class="fw-semibold text-warning small">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>Item dari Review Sebelumnya
                                                </span>
                                                <span class="badge bg-light text-warning border small">
                                                    {{ count($orphanedItems) }} item
                                                </span>
                                            </div>
                                            <div class="ui-card-body p-3">
                                                <div class="row g-2">
                                                    @foreach($orphanedItems as $item)
                                                        @php $itemId = 'error_orphaned_' . md5($item); @endphp
                                                        <div class="col-md-6">
                                                            <div class="form-check p-2 rounded-2 border mb-0 bg-light">
                                                                <input class="form-check-input checklist-item ms-0 me-2" 
                                                                       type="checkbox" 
                                                                       name="kesalahan_administratif[]" 
                                                                       value="{{ $item }}" 
                                                                       id="{{ $itemId }}"
                                                                       checked>
                                                                <label class="form-check-label small text-dark" for="{{ $itemId }}">
                                                                    {{ $item }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="form-text text-danger" id="errorKesalahan" style="display: none;">
                                    Pilih minimal satu kesalahan administratif
                                </div>
                                <div class="form-text text-danger" id="serverErrorKesalahan" style="display: none;"></div>
                            @endif
                        </div>

                        {{-- Dynamic Fields from FormPenilaian --}}
                        @include('reviewer.partials.dynamic_fields', [
                            'dynamicForm'    => $dynamicForm ?? null,
                            'existingAnswers' => $existingReview->extra_fields ?? [],
                            'inputPrefix'    => 'extra_fields',
                        ])

                        <!-- Catatan Review -->
                        <div class="mb-4">
                            <label for="catatan" class="form-label fw-bold mb-1 fs-6">
                                <i class="fas fa-comment-alt text-primary me-2"></i>Catatan Review Administratif <span class="text-danger">*</span>
                            </label>
                            <p class="text-muted small mb-2">Tuliskan temuan kesalahan secara detail dan berikan arahan korektif bagi mahasiswa.</p>
                            <textarea class="form-control rounded-3" id="catatan" name="catatan" rows="5" 
                                      placeholder="Tuliskan catatan deskriptif mengenai kepatuhan dokumen..." 
                                      required>{{ $existingReview->note_administratif ?? '' }}</textarea>
                            <div class="form-text text-danger" id="errorCatatan" style="display: none;">
                                Catatan review wajib diisi
                            </div>
                            <div class="form-text text-danger" id="serverErrorCatatan" style="display: none;"></div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold shadow-sm" id="submitBtn">
                                <i class="fas fa-save me-1.5"></i>
                                <span id="submitText">Simpan Review Administratif</span>
                            </button>
                            
                            @if($existingReview)
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    Terakhir disimpan: {{ $existingReview->updated_at->format('d/m/Y H:i') }}
                                </small>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function goBackToReviewerDashboard() {
        if (window.history.length > 1 && document.referrer && document.referrer !== window.location.href) {
            window.history.back();
            return;
        }
        window.location.href = "{{ route('reviewer.review.administratif') }}";
    }

    function showSuccessModal(message, backUrl) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-3 border-0 shadow">
                    <div class="modal-header border-bottom">
                        <h6 class="modal-title fw-bold text-success">
                            <i class="fas fa-check-circle me-2"></i>Penilaian Berhasil Disimpan
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <p class="mb-0 text-dark">${message || 'Review administratif berhasil disimpan.'}</p>
                    </div>
                    <div class="modal-footer border-top bg-light">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                        <a href="${backUrl || window.location.href}" class="btn btn-primary rounded-pill px-4 fw-semibold">
                            <i class="fas fa-arrow-left me-1"></i>Selesai & Muat Ulang
                        </a>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        modal.addEventListener('hidden.bs.modal', () => {
            modal.remove();
        });
    }

    function toggleFullscreen() {
        const pdfSection = document.querySelector('.pdf-viewer-section');
        const fullscreenBtn = document.querySelector('[onclick="toggleFullscreen()"]');
        
        if (pdfSection) {
            if (pdfSection.classList.contains('fullscreen')) {
                pdfSection.classList.remove('fullscreen');
                if (fullscreenBtn) fullscreenBtn.innerHTML = '<i class="fas fa-expand me-1"></i>Fullscreen';
                document.body.style.overflow = '';
            } else {
                pdfSection.classList.add('fullscreen');
                if (fullscreenBtn) fullscreenBtn.innerHTML = '<i class="fas fa-compress me-1"></i>Tutup Fullscreen';
                document.body.style.overflow = 'hidden';
            }
        }
    }

    function refreshPDFViewer() {
        const pdfViewer = document.getElementById('pdfViewer');
        if (pdfViewer) {
            const currentSrc = pdfViewer.src.split('?')[0];
            pdfViewer.src = currentSrc + '?t=' + Date.now();
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const pdfSection = document.querySelector('.pdf-viewer-section.fullscreen');
            if (pdfSection) toggleFullscreen();
        }
    });

    function updateCategoryCounters() {
        const checkboxes = document.querySelectorAll('.checklist-item');
        const categoryCounts = {};
        
        checkboxes.forEach(checkbox => {
            const category = checkbox.getAttribute('data-category');
            if (!category) return;
            if (!categoryCounts[category]) {
                categoryCounts[category] = { total: 0, checked: 0 };
            }
            categoryCounts[category].total++;
            if (checkbox.checked) {
                categoryCounts[category].checked++;
            }
        });
        
        Object.keys(categoryCounts).forEach(category => {
            const counter = document.getElementById('counter-' + category);
            if (counter) {
                const { checked, total } = categoryCounts[category];
                counter.textContent = `${checked} / ${total}`;
                if (checked === 0) {
                    counter.className = 'badge bg-light text-secondary border small';
                } else if (checked === total) {
                    counter.className = 'badge bg-danger text-white border small';
                } else {
                    counter.className = 'badge bg-warning text-dark border small';
                }
            }
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        updateCategoryCounters();
        document.querySelectorAll('.checklist-item').forEach(checkbox => {
            checkbox.addEventListener('change', updateCategoryCounters);
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formReviewAdministratif');
        if (!form) return;
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (document.getElementById('errorKesalahan')) document.getElementById('errorKesalahan').style.display = 'none';
            document.getElementById('errorCatatan').style.display = 'none';
            if (document.getElementById('serverErrorKesalahan')) document.getElementById('serverErrorKesalahan').style.display = 'none';
            document.getElementById('serverErrorCatatan').style.display = 'none';
            
            const catatan = document.getElementById('catatan').value.trim();
            if (!catatan) {
                document.getElementById('errorCatatan').style.display = 'block';
                return;
            }
            
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            if (submitBtn) submitBtn.disabled = true;
            if (submitText) submitText.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(async (response) => {
                const payload = await response.json();
                if (!response.ok) {
                    const httpError = new Error(`HTTP error! status: ${response.status}`);
                    httpError.responsePayload = payload;
                    throw httpError;
                }
                return payload;
            })
            .then(data => {
                if (data.success) {
                    showSuccessModal(data.message, window.location.href);
                    setTimeout(() => {
                        if (document.querySelector('.modal.show') === null) {
                            window.location.reload();
                        }
                    }, 2000);
                } else {
                    alert(data.message || 'Terjadi kesalahan');
                }
            })
            .catch(error => {
                let errorMessage = 'Terjadi kesalahan saat menyimpan review';
                const serverErrors = error?.responsePayload?.errors || {};
                if (Object.keys(serverErrors).length > 0) {
                    const genericErrors = Object.values(serverErrors).flat();
                    if (genericErrors.length > 0) errorMessage = genericErrors[0];
                } else if (error?.responsePayload?.message) {
                    errorMessage = error.responsePayload.message;
                }
                alert('Gagal: ' + errorMessage);
            })
            .finally(() => {
                if (submitBtn) submitBtn.disabled = false;
                if (submitText) submitText.innerHTML = 'Simpan Review Administratif';
            });
        });
    });
</script>
@endpush
