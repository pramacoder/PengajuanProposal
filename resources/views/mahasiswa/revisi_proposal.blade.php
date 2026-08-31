@extends('mainlayout.app')

@section('title', 'Revisi Proposal PKM')

@section('styles')

@endsection

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
        ['label' => 'Revisi Proposal', 'active' => true],
    ]" />

    <x-page-header 
        title="Revisi Proposal PKM" 
    />
    <!-- Status Perbaikan -->
    <div class="mb-10">
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-lg shadow-sm">
            <div class="flex items-start">
                <div class="text-blue-500 mt-1 mr-4">
                    <i class="fas fa-info-circle fa-2xl"></i>
                </div>
                <div>
                    <h5 class="text-blue-800 font-bold mb-2 flex items-center gap-2">Status Perbaikan: 
                        <x-status-badge class="text-sm px-3 py-1" :status="$ruangKontrol ? $ruangKontrol->status_perbaikan : 'tertutup'" />
                    </h5>
                    @if(!$ruangKontrol)
                        <p class="mb-0 text-amber-600 font-medium flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Ruang kontrol tidak ditemukan untuk tahun ajaran ini.
                        </p>
                    @endif
                    @if($ruangKontrol && $ruangKontrol->tanggal_perbaikan_mulai && $ruangKontrol->tanggal_perbaikan_selesai)
                        <p class="mb-2 text-blue-700">
                            <span class="font-semibold">Periode:</span> {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_mulai)->format('d M Y') }} - 
                            {{ \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai)->format('d M Y') }}
                        </p>
                        @php
                            $deadline = \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai);
                            $daysLeft = (int) now()->diffInDays($deadline, false);
                        @endphp
                        @if($daysLeft > 0)
                            <p class="mb-0 text-amber-600 font-bold flex items-center bg-amber-50 inline-block px-3 py-1 rounded-md">
                                <i class="fas fa-clock mr-2"></i>
                                Sisa waktu: {{ $daysLeft }} hari
                            </p>
                        @elseif($daysLeft == 0)
                            <p class="mb-0 text-red-600 font-bold flex items-center bg-red-50 inline-block px-3 py-1 rounded-md">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Hari terakhir!
                            </p>
                        @else
                            <p class="mb-0 text-red-600 font-bold flex items-center bg-red-50 inline-block px-3 py-1 rounded-md">
                                <i class="fas fa-times-circle mr-2"></i>
                                Batas waktu telah terlampaui {{ abs($daysLeft) }} hari
                            </p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Proposal -->
    <x-ui.card className="mb-8 overflow-hidden">
        <div class="bg-gradient-to-br from-navy-600 to-navy-800 text-white p-6">
            <h4 class="font-bold text-xl m-0 flex items-center">
                <i class="fas fa-clipboard-list mr-3 text-navy-200"></i>Informasi Proposal
            </h4>
        </div>
        
        <div class="p-6 bg-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h5 class="text-navy-700 font-bold text-xl mb-3">{{ $proposal->judul }}</h5>
                    <div class="flex flex-wrap gap-4 text-slate-600 mb-3">
                        <span class="flex items-center">
                            <i class="fas fa-layer-group text-slate-400 mr-2"></i>
                            <strong class="mr-1">Skim:</strong> {{ $proposal->skim }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                            <strong class="mr-1">Dana:</strong> @rupiahId($proposal->dana_diajukan)
                        </span>
                    </div>
                    <div class="flex items-center">
                        <strong class="text-slate-600 mr-3">Status:</strong> 
                        <x-status-badge :status="$proposal->status" :label="$proposal->status_label" />
                    </div>
                </div>
                <div class="text-right whitespace-nowrap bg-slate-50 p-3 rounded-lg border border-slate-200">
                    <small class="text-slate-500 font-medium">
                        <i class="fas fa-calendar mr-2"></i>
                        Diajukan: {{ \Carbon\Carbon::parse($proposal->created_at)->format('d M Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </x-ui.card>

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
        <div class="bg-white border-2 border-slate-200 border-l-4 border-l-rose-700 rounded-xl p-6 mb-6">
            <div class="flex items-center mb-6 pb-4 border-b-2 border-slate-100">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-rose-600 to-rose-800 flex items-center justify-center text-white text-xl mr-4 shrink-0 shadow-md">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div>
                    <h5 class="text-rose-800 font-bold m-0 text-lg">Review Administratif</h5>
                    <small class="text-slate-500 text-sm">
                    <i class="fas fa-clock me-1"></i>
                    {{ \Carbon\Carbon::parse($adminReview->updated_at)->format('d M Y H:i') }}
                </small>
            </div>
            </div>
            
            <!-- Kesalahan Administratif yang Dicentang -->
            @if(!empty($checklistChecked) && is_array($checklistChecked))
            <div class="bg-rose-50 border border-rose-200 rounded-lg p-5 mb-4">
                <h6 class="text-rose-700 font-bold mb-4 text-base">
                    <i class="fas fa-exclamation-triangle me-2"></i>Kesalahan Administratif yang Ditemukan:
                </h6>
                <div class="flex flex-col gap-3">
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
                                <div class="flex items-start p-3 bg-white rounded-md border-l-4 border-red-500 shadow-sm transition-transform hover:translate-x-1">
                                    <i class="fas fa-times-circle text-red-500 me-3 mt-1 shrink-0"></i>
                                    <span class="text-slate-700 leading-relaxed">{{ $itemText }}</span>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <div class="text-slate-500 text-center py-2">
                            <i class="fas fa-info-circle me-2"></i>
                            Tidak ada kesalahan administratif yang dicentang
                        </div>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Catatan Review Administratif -->
            @if($adminReview->note_administratif)
            <div class="bg-slate-50 border-l-4 border-rose-700 rounded-md p-4 mt-4">
                <h6 class="text-rose-800 font-semibold mb-3 text-sm">
                    <i class="fas fa-sticky-note me-2"></i>Catatan Reviewer:
                </h6>
                <div class="text-slate-600 leading-relaxed">
                    <p class="mb-0">{{ $adminReview->note_administratif }}</p>
                </div>
            </div>
            @endif
        </div>
        @endif
        
        <!-- Hasil Semi Final (Status Tingkat Universitas) -->
        @if($hasilSemiFinal)
        <div class="bg-white border-2 border-slate-200 border-l-4 border-l-green-600 rounded-xl p-6 mb-6">
            <div class="flex items-center mb-6 pb-4 border-b-2 border-slate-100">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-500 to-green-700 flex items-center justify-center text-white text-xl mr-4 shrink-0 shadow-md">
                    <i class="fas fa-trophy"></i>
                </div>
                <div>
                    <h5 class="text-green-700 font-bold m-0 text-lg">Hasil Semi Final - Tingkat Universitas</h5>
                    <small class="text-slate-500 text-sm">
                        <i class="fas fa-calendar me-1"></i>
                        {{ \Carbon\Carbon::parse($hasilSemiFinal->updated_at)->format('d M Y H:i') }}
                    </small>
                </div>
            </div>
            
            <div class="mt-2">
                @if($hasilSemiFinal->status_final == 'lolos_tingkat_universitas')
                    <div class="bg-gradient-to-br from-green-100 to-green-200 border-2 border-green-500 rounded-lg p-4 text-green-800 text-lg font-bold text-center">
                        <i class="fas fa-check-circle me-2"></i>
                        Lolos Tingkat Universitas
                    </div>
                    @if($proposal->dosenPendampingUniversitas)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 mt-4">
                        <h6 class="text-blue-700 font-bold mb-3 text-sm">
                            <i class="fas fa-user-tie me-2"></i>Dosen Pendamping Universitas:
                        </h6>
                        <div class="text-slate-700">
                            <p class="mb-2"><strong class="font-semibold text-slate-800">Nama:</strong> {{ $proposal->dosenPendampingUniversitas->nama_dosen }}</p>
                            <p class="mb-2"><strong class="font-semibold text-slate-800">Email:</strong> {{ $proposal->dosenPendampingUniversitas->email_dosen }}</p>
                            <p class="mb-0"><strong class="font-semibold text-slate-800">No. HP:</strong> {{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}</p>
                        </div>
                    </div>
                    @endif
                @else
                    <div class="bg-gradient-to-br from-red-100 to-red-200 border-2 border-red-500 rounded-lg p-4 text-red-800 text-lg font-bold text-center">
                        <i class="fas fa-times-circle me-2"></i>
                        Tidak Lolos Tingkat Universitas
                    </div>
                @endif
                
                @if($hasilSemiFinal->catatan_final)
                <div class="bg-slate-50 border-l-4 border-green-600 rounded-md p-4 mt-4">
                    <h6 class="text-green-700 font-semibold mb-3 text-sm">
                        <i class="fas fa-sticky-note me-2"></i>Catatan:
                    </h6>
                    <div class="text-slate-600 leading-relaxed">
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
                <div class="bg-white border-2 border-slate-200 border-l-4 border-l-slate-500 rounded-xl p-6 mb-6">
                    <div class="flex items-center mb-6 pb-4 border-b-2 border-slate-100">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center text-white text-xl mr-4 shrink-0 shadow-md">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div>
                            <h5 class="text-slate-600 font-bold m-0 text-lg">Review Substantif - Reviewer {{ $index + 1 }}</h5>
                            <small class="text-slate-500 text-sm">
                                <i class="fas fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($review->updated_at)->format('d M Y H:i') }}
                            </small>
                        </div>
                    </div>
                    
                    <div class="bg-slate-50 border-l-4 border-slate-500 rounded-md p-4 mt-4">
                        <h6 class="text-slate-600 font-semibold mb-3 text-sm">
                            <i class="fas fa-sticky-note me-2"></i>Catatan Reviewer:
                        </h6>
                        <div class="text-slate-600 leading-relaxed">
                            <p class="mb-0">{{ $review->note_substantif }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
        
        @if(!$adminReview && !$hasilSemiFinal && $substantifReviews->count() == 0)
        <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg">
            <div class="flex items-center text-amber-800">
                <i class="fas fa-exclamation-triangle mr-3 text-xl"></i>
                <p class="mb-0 font-medium">Belum ada catatan review yang tersedia. Silakan tunggu hingga reviewer menyelesaikan review mereka.</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Upload File Revisi -->
    <x-ui.card className="mb-8 overflow-hidden">
        <div class="bg-gradient-to-br from-navy-600 to-navy-800 text-white p-6">
            <h4 class="font-bold text-xl m-0 flex items-center">
                <i class="fas fa-upload mr-3 text-navy-200"></i>Upload File Revisi
            </h4>
        </div>
        
        <div class="p-6 bg-white">
            <form action="{{ route('mahasiswa.revisi.store') }}" method="POST" enctype="multipart/form-data" id="revisiForm">
                @csrf
                
                <div class="border-2 border-dashed border-slate-300 rounded-xl p-10 text-center transition-all bg-slate-50 hover:border-navy-500 hover:bg-navy-50/50 cursor-pointer group" id="revisiUploadArea">
                    <div class="text-5xl text-navy-500 mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <h5 class="text-slate-800 font-bold mb-2">Upload File Revisi Proposal</h5>
                    <p class="text-slate-500 mb-6">Drag & drop file PDF di sini atau klik untuk memilih file</p>
                    <input type="file" id="file_revisi" name="file_revisi" accept=".pdf" style="display: none;" required>
                    <button type="button" class="px-5 py-2.5 bg-white border border-navy-600 text-navy-700 rounded-lg font-medium hover:bg-navy-600 hover:text-white transition-colors shadow-sm inline-flex items-center" onclick="document.getElementById('file_revisi').click()">
                        <i class="fas fa-folder-open me-2"></i>Pilih File
                    </button>
                </div>
                @error('file_revisi')
                    <div class="text-red-500 text-sm mt-2 font-medium">
                        {{ $message }}
                    </div>
                @enderror
                
                <div class="bg-slate-100 rounded-lg p-4 mt-4 hidden" id="revisiFileInfo">
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <strong id="revisiFileName" class="text-slate-800">Nama file</strong>
                            <br><small id="revisiFileSize" class="text-slate-500">Ukuran file</small>
                        </div>
                        <button type="button" class="text-red-500 hover:text-red-700 p-2 rounded-full hover:bg-red-50 transition-colors" onclick="removeFile()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-navy-500 to-navy-700 w-0 transition-all duration-300" id="revisiProgress"></div>
                    </div>
                </div>
                
                <div class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-r-lg mt-6">
                    <div class="flex items-start text-amber-800">
                        <i class="fas fa-exclamation-triangle mt-1 mr-3"></i>
                        <div>
                            <strong class="block mb-2 font-bold">Ketentuan Upload:</strong>
                            <ul class="list-disc pl-5 mb-0 text-amber-700">
                                <li>Format file harus PDF</li>
                                <li>Ukuran maksimal 5MB</li>
                                <li>File harus berisi proposal yang sudah direvisi sesuai catatan reviewer</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-8">
                    <button type="submit" class="px-8 py-3 bg-navy-600 text-white rounded-lg font-bold text-lg hover:bg-navy-700 transition-colors shadow-md inline-flex items-center" id="submitBtn">
                        <i class="fas fa-upload me-2"></i>Upload File Revisi
                    </button>
                </div>
            </form>
        </div>
    </x-ui.card>

    <!-- Daftar File Revisi -->
    @if($revisi->count() > 0)
    <x-ui.card className="mb-8 overflow-hidden">
        <div class="bg-gradient-to-br from-navy-600 to-navy-800 text-white p-6">
            <h4 class="font-bold text-xl m-0 flex items-center">
                <i class="fas fa-history mr-3 text-navy-200"></i>Riwayat File Revisi
            </h4>
        </div>
        
        <div class="p-6 bg-white">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($revisi as $item)
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-5 hover:border-navy-500 hover:shadow-md transition-all group">
                    <div class="flex items-start">
                        <div class="text-3xl text-red-500 mr-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="flex-grow">
                            <h6 class="font-bold text-slate-800 mb-1 line-clamp-1" title="{{ $item->nama_file }}">{{ $item->nama_file }}</h6>
                            <p class="text-slate-500 mb-3 text-sm font-medium">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ \Carbon\Carbon::parse($item->tanggal_submit)->format('d M Y H:i') }}
                            </p>
                            <div class="flex gap-2">
                                <a href="{{ route('mahasiswa.revisi.download', $item->id_revisi) }}" 
                                   class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 hover:bg-green-100 hover:border-green-300 rounded text-sm font-medium transition-colors flex items-center">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                                <button type="button" class="px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 hover:border-red-300 rounded text-sm font-medium transition-colors flex items-center" 
                                        onclick="deleteRevisi({{ $item->id_revisi }})">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </x-ui.card>
    @endif

    <!-- Tombol Kembali -->
    <div class="text-center mt-8 mb-12">
        <a href="{{ route('mahasiswa.dashboard') }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition-colors inline-flex items-center shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
        </a>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="fixed inset-0 z-50 hidden" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" data-bs-dismiss="modal"></div>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="relative bg-white rounded-xl shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full overflow-hidden">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-bold text-slate-900" id="deleteModalLabel">
                            Konfirmasi Hapus
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-slate-500">
                                Apakah Anda yakin ingin menghapus file revisi ini?
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-200">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors" id="confirmDelete">
                    Hapus
                </button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors" data-bs-dismiss="modal">
                    Batal
                </button>
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
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');
    }

    // Modal dismiss logic
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('deleteModal');
        const dismissButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
        
        dismissButtons.forEach(button => {
            button.addEventListener('click', () => {
                modal.classList.add('hidden');
                deleteId = null;
            });
        });
    });

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

    // Form submission
    document.getElementById('revisiForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengupload...';
        submitBtn.disabled = true;
    });

    // Show toast notification
    function showToast(message, type = 'info', duration = 3000) {
        if (window.AppUI?.showToast) {
            window.AppUI.showToast(message, type, duration);
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        setupFileUpload();
    });
</script>
@endsection