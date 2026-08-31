@extends('mainlayout.app')

@section('title', 'Detail Proposal PKM')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('styles')

@endsection

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
        ['label' => 'Lihat Proposal', 'url' => route('mahasiswa.proposal.index')],
        ['label' => 'Detail Proposal', 'active' => true],
    ]" />

    <!-- Header -->
    <x-page-header 
        title="Detail Proposal PKM" 
        description="Detail lengkap proposal PKM yang telah Anda ajukan"
        :showBackButton="true"
    />

    <!-- Proposal Detail Card -->
    <x-ui.card className="mb-8 overflow-hidden">
        <div class="bg-gradient-to-br from-navy-600 to-navy-800 text-white p-8">
            <div class="flex justify-between items-start mb-4">
                <x-status-badge :status="$proposal->status_validasi" />
            </div>
            
            <h2 class="text-2xl font-bold mb-6 leading-relaxed">
                {{ $proposal->judul }}
            </h2>
            
            <div class="flex flex-wrap gap-6 text-sm">
                <div class="flex items-center gap-2">
                    <i class="fas fa-calendar-alt opacity-80"></i>
                    <span>Diajukan: {{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d F Y') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-tag opacity-80"></i>
                    <span>Skim: {{ $proposal->skim }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-university opacity-80"></i>
                    <span>Dana Belmawa: @rupiahId($proposal->dana_diajukan_belmawa ?? 0)</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fas fa-building opacity-80"></i>
                    <span>Dana Univ: @rupiahId($proposal->dana_diajukan_operator ?? 0)</span>
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
                    <div class="flex items-center gap-2">
                        <i class="fas fa-user opacity-80"></i>
                        <span>Role: <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-bold">{{ $userRole }}</span></span>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="p-8">
            <!-- Informasi Proposal -->
            <div class="mb-10">
                <h5 class="text-navy-700 font-semibold mb-6 pb-3 border-b-2 border-slate-100 flex items-center">
                    <i class="fas fa-info-circle me-3 text-navy-500"></i>Informasi Proposal
                </h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-lg border-l-4 border-navy-500">
                        <span class="font-semibold text-slate-600">Judul Proposal</span>
                        <span class="text-slate-900 font-medium text-right ml-4">{{ $proposal->judul }}</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-lg border-l-4 border-navy-500">
                        <span class="font-semibold text-slate-600">Skim PKM</span>
                        <span class="text-slate-900 font-medium text-right">{{ $proposal->skim }}</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-lg border-l-4 border-navy-500">
                        <span class="font-semibold text-slate-600">Tahun Ajaran</span>
                        <span class="text-slate-900 font-medium text-right">{{ $proposal->tahun_ajaran ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-lg border-l-4 border-navy-500">
                        <span class="font-semibold text-slate-600 flex flex-col">Dana dari Belmawa <small class="text-slate-400 text-xs font-normal">(Kemendiktisaintek)</small></span>
                        <span class="text-navy-600 font-bold text-right">@rupiahId($proposal->dana_diajukan_belmawa ?? 0)</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-lg border-l-4 border-navy-500">
                        <span class="font-semibold text-slate-600">Dana dari Universitas</span>
                        <span class="text-green-600 font-bold text-right">@rupiahId($proposal->dana_diajukan_operator ?? 0)</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-lg border-l-4 border-navy-500">
                        <span class="font-semibold text-slate-600">Status Validasi</span>
                        <div class="text-right">
                            <x-status-badge :status="$proposal->status_validasi" />
                        </div>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-lg border-l-4 border-navy-500">
                        <span class="font-semibold text-slate-600">Status Final</span>
                        <div class="text-right">
                            <x-status-badge :status="$proposal->status_final ?? 'pending'" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Dosen Pendamping -->
            <div class="mb-10">
                <h5 class="text-navy-700 font-semibold mb-6 pb-3 border-b-2 border-slate-100 flex items-center">
                    <i class="fas fa-user-tie me-3 text-navy-500"></i>Dosen Pendamping
                </h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-lg border-l-4 border-navy-500">
                        <span class="font-semibold text-slate-600">Nama Dosen</span>
                        <span class="text-slate-900 font-medium text-right">{{ $proposal->dosen_pembimbing ?? 'N/A' }}</span>
                    </div>
                    @if($proposal->dosen)
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-lg border-l-4 border-navy-500">
                        <span class="font-semibold text-slate-600">Email</span>
                        <span class="text-slate-900 font-medium text-right">{{ $proposal->dosen->email_dosen ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-lg border-l-4 border-navy-500">
                        <span class="font-semibold text-slate-600">No. HP</span>
                        <span class="text-slate-900 font-medium text-right">{{ $proposal->dosen->no_hp_dosen ?? 'N/A' }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informasi Dosen Pendamping Universitas -->
            @if($proposal->dosenPendampingUniversitas)
            <div class="mb-10 bg-rose-50/50 border-l-4 border-rose-700 p-6 rounded-lg">
                <h5 class="text-rose-800 font-semibold mb-6 flex items-center text-lg">
                    <i class="fas fa-user-graduate me-3 text-xl"></i>Dosen Pendamping Universitas
                </h5>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="flex flex-col gap-2">
                        <span class="text-slate-500 text-sm font-medium">Nama Dosen</span>
                        <span class="text-rose-900 font-semibold text-base">
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
                    <div class="flex flex-col gap-2">
                        <span class="text-slate-500 text-sm font-medium">Email</span>
                        <span>
                            <a href="mailto:{{ $proposal->dosenPendampingUniversitas->email_dosen }}" class="text-rose-800 font-medium hover:text-rose-600 transition-colors">
                                <i class="fas fa-envelope me-2"></i>{{ $proposal->dosenPendampingUniversitas->email_dosen }}
                            </a>
                        </span>
                    </div>
                    @endif
                    @if($proposal->dosenPendampingUniversitas->no_hp_dosen)
                    <div class="flex flex-col gap-2">
                        <span class="text-slate-500 text-sm font-medium">No. HP</span>
                        <span>
                            <a href="tel:{{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}" class="text-rose-800 font-medium hover:text-rose-600 transition-colors">
                                <i class="fas fa-phone me-2"></i>{{ $proposal->dosenPendampingUniversitas->no_hp_dosen }}
                            </a>
                        </span>
                    </div>
                    @endif
                </div>
                <div class="bg-rose-100/50 border-l-4 border-rose-800 rounded-md p-4">
                    <small class="text-slate-700 flex items-center">
                        <i class="fas fa-info-circle me-3 text-rose-800 text-lg"></i>
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
                $hasilFinal = $proposal->hasilFinal;
                
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
                
                $hasAnyReview = $adminReview || $hasilSemiFinal || $hasilFinal || $substantifReviews->count() > 0;
            @endphp
            
            @if($hasAnyReview)
            <div class="info-section" style="margin-bottom: 2rem;">
                <h5 style="color: var(--primary-color); font-weight: 600; margin-bottom: 1.5rem; display: flex; align-items: center;">
                    <i class="fas fa-comments me-2"></i>Catatan Review dari Reviewer
                </h5>
                
                <!-- Review Administratif -->
                @if($adminReview)
                <div class="bg-white border-2 border-slate-200 border-l-4 border-l-rose-700 rounded-xl p-6 mb-6">
                    <div class="flex items-center mb-6 pb-4 border-b-2 border-slate-100">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-rose-700 to-rose-900 flex items-center justify-center text-white text-xl mr-4 shrink-0 shadow-md">
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
                    
                    @if(!empty($checklistChecked) && is_array($checklistChecked))
                    <div class="bg-rose-50 border border-rose-200 rounded-lg p-5 mb-4">
                        <h6 class="text-rose-800 font-semibold mb-4 text-base">
                            <i class="fas fa-exclamation-triangle me-2"></i>Kesalahan Administratif yang Ditemukan:
                        </h6>
                        <div class="flex flex-col gap-3">
                            @foreach($checklistChecked as $checkedItem)
                                @php
                                    $itemText = is_string($checkedItem) ? $checkedItem : (isset($checkedItem['text']) ? $checkedItem['text'] : '');
                                @endphp
                                @if(!empty($itemText))
                                    <div class="flex items-start p-3 bg-white rounded-md border-l-4 border-rose-600 shadow-sm">
                                        <i class="fas fa-times-circle text-rose-600 me-3 mt-1 shrink-0"></i>
                                        <span class="text-slate-700 leading-relaxed">{{ $itemText }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
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
                
                <!-- Hasil Semi Final -->
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
                    
                    @if($hasilSemiFinal->status_final == 'lolos_tingkat_universitas')
                        <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-500 rounded-lg p-4 text-green-800 text-lg text-center mb-4 shadow-sm">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Lolos Tingkat Universitas</strong>
                        </div>
                    @else
                        <div class="bg-gradient-to-br from-red-50 to-red-100 border-2 border-red-500 rounded-lg p-4 text-red-800 text-lg text-center mb-4 shadow-sm">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>Tidak Lolos Tingkat Universitas</strong>
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

                    {{-- Card Info Dosen Universitas (muncul jika lolos) --}}
                    @if($hasilSemiFinal->status_final == 'lolos_tingkat_universitas' && $proposal->dosenPendampingUniversitas)
                    @php $dosenUniv = $proposal->dosenPendampingUniversitas; @endphp
                    <div class="bg-gradient-to-br from-green-50 to-green-100/50 border-2 border-green-500 rounded-xl p-5 mt-4">
                        <h6 class="text-green-800 font-bold mb-4 text-sm flex items-center">
                            <i class="fas fa-user-tie me-2 text-lg"></i>Dosen Pendamping Universitas Anda
                        </h6>
                        <div class="flex flex-wrap gap-6">
                            <div>
                                <div class="text-xs text-slate-500 mb-0.5">Nama</div>
                                <div class="font-semibold text-slate-900">
                                    {{ $dosenUniv->gelar_depan ? $dosenUniv->gelar_depan . ' ' : '' }}{{ $dosenUniv->name }}{{ $dosenUniv->gelar_belakang ? ', ' . $dosenUniv->gelar_belakang : '' }}
                                </div>
                            </div>
                            @if($dosenUniv->email)
                            <div>
                                <div class="text-xs text-slate-500 mb-0.5">Email</div>
                                <div><a href="mailto:{{ $dosenUniv->email }}" class="text-blue-600 font-medium hover:text-blue-800 transition-colors">{{ $dosenUniv->email }}</a></div>
                            </div>
                            @endif
                            @if($dosenUniv->phone)
                            <div>
                                <div class="text-xs text-slate-500 mb-0.5">No. HP / WhatsApp</div>
                                <div class="font-medium text-slate-900">{{ $dosenUniv->phone }}</div>
                            </div>
                            @endif
                        </div>
                        <div class="mt-4 p-3 bg-white/70 rounded-md text-sm text-green-800 font-medium border border-green-200">
                            <i class="fas fa-info-circle me-1"></i>
                            Hubungi Dosen Pendamping Universitas, lalu upload <strong>revisi akhir</strong> proposal Anda melalui menu <strong>Revisi Akhir</strong>.
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                
                <!-- Hasil Final -->
                @if($hasilFinal)
                <div class="bg-white border-2 border-slate-200 border-l-4 border-l-purple-600 rounded-xl p-6 mb-6 shadow-md shadow-purple-900/5">
                    <div class="flex items-center mb-6 pb-4 border-b-2 border-slate-100">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white text-xl mr-4 shrink-0 shadow-md">
                            <i class="fas fa-medal"></i>
                        </div>
                        <div>
                            <h5 class="text-purple-700 font-bold m-0 text-lg">Hasil Final - Keputusan Pimpinan PT</h5>
                            <small class="text-slate-500 text-sm">
                                <i class="fas fa-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($hasilFinal->updated_at)->format('d M Y H:i') }}
                            </small>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <!-- Status PIMNAS -->
                        <div class="bg-slate-50 rounded-lg p-4 border-l-4 {{ $hasilFinal->status_pimnas == 'lolos' ? 'border-green-500' : 'border-red-500' }}">
                            <div class="text-sm text-slate-500 mb-2 font-medium">
                                <i class="fas fa-flag me-2"></i>Status PIMNAS
                            </div>
                            <div class="text-lg font-bold {{ $hasilFinal->status_pimnas == 'lolos' ? 'text-green-600' : 'text-red-600' }}">
                                @if($hasilFinal->status_pimnas == 'lolos')
                                    <i class="fas fa-check-circle me-1"></i>Lolos
                                @else
                                    <i class="fas fa-times-circle me-1"></i>Tidak Lolos
                                @endif
                            </div>
                        </div>
                        
                        <!-- Status Pendanaan -->
                        <div class="bg-slate-50 rounded-lg p-4 border-l-4 {{ $hasilFinal->status_pendanaan == 'lolos' ? 'border-green-500' : 'border-red-500' }}">
                            <div class="text-sm text-slate-500 mb-2 font-medium">
                                <i class="fas fa-money-bill-wave me-2"></i>Status Pendanaan
                            </div>
                            <div class="text-lg font-bold {{ $hasilFinal->status_pendanaan == 'lolos' ? 'text-green-600' : 'text-red-600' }}">
                                @if($hasilFinal->status_pendanaan == 'lolos')
                                    <i class="fas fa-check-circle me-1"></i>Lolos
                                @else
                                    <i class="fas fa-times-circle me-1"></i>Tidak Lolos
                                @endif
                            </div>
                        </div>
                        
                        <!-- Nilai -->
                        @if($hasilFinal->nilai)
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border-l-4 border-blue-500">
                            <div class="text-sm text-slate-500 mb-2 font-medium">
                                <i class="fas fa-star me-2"></i>Nilai Akhir
                            </div>
                            <div class="text-xl font-bold text-blue-700">
                                @formatId($hasilFinal->nilai, 2)
                            </div>
                        </div>
                        @endif
                        
                        <!-- Dana yang Didapatkan -->
                        @if($hasilFinal->status_pendanaan == 'lolos' && ($hasilFinal->dana_didapatkan_belmawa || $hasilFinal->dana_didapatkan_operator))
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border-l-4 border-green-500 col-span-1 lg:col-span-2">
                            <div class="flex flex-col gap-3">
                                <div>
                                    <div class="text-sm text-slate-500 mb-1 font-medium">
                                        <i class="fas fa-coins me-2"></i>Dana Didapatkan (Belmawa)
                                    </div>
                                    <div class="text-lg font-bold text-green-700">
                                        @rupiahId($hasilFinal->dana_didapatkan_belmawa ?? 0)
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm text-slate-500 mb-1 font-medium">
                                        <i class="fas fa-coins me-2"></i>Dana Didapatkan (Universitas)
                                    </div>
                                    <div class="text-lg font-bold text-green-700">
                                        @rupiahId($hasilFinal->dana_didapatkan_operator ?? 0)
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if($hasilFinal->catatan_final)
                    <div class="bg-slate-50 border-l-4 border-purple-600 rounded-md p-4 mt-4">
                        <h6 class="text-purple-700 font-semibold mb-3 text-sm">
                            <i class="fas fa-sticky-note me-2"></i>Catatan Pimpinan PT:
                        </h6>
                        <div class="text-slate-600 leading-relaxed">
                            <p class="mb-0">{{ $hasilFinal->catatan_final }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                
                <!-- Review Substantif (Catatan Saja) -->
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
                        
                        <div class="bg-slate-50 border-l-4 border-slate-500 rounded-md p-4">
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
            </div>
            @endif

            <!-- Informasi Tim -->
            <div class="mb-10">
                <h5 class="text-navy-700 font-semibold mb-6 pb-3 border-b-2 border-slate-100 flex items-center">
                    <i class="fas fa-users me-3 text-navy-500"></i>Anggota Tim (@formatId($proposal->semuaAnggotaTim->count()) orang)
                </h5>
                <div class="bg-slate-50 rounded-lg p-6">
                    @foreach($proposal->semuaAnggotaTim as $member)
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 bg-white rounded-lg mb-3 border-l-4 {{ $member->is_ketua ? 'border-green-500 bg-green-50/30' : 'border-navy-500' }} shadow-sm">
                        <div class="mb-2 sm:mb-0">
                            <div class="font-bold text-slate-800 text-lg mb-1">{{ $member->nama_mhs }}</div>
                            <div class="text-sm text-slate-500">
                                <span class="font-medium text-slate-700">NIM:</span> {{ $member->nim }} &bull; {{ $member->prodi_mhs }} &bull; {{ $member->fakultas_mhs }}
                            </div>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $member->is_ketua ? 'bg-green-100 text-green-700' : 'bg-navy-100 text-navy-700' }}">
                            {{ $member->is_ketua ? 'Ketua' : 'Anggota' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Catatan dan File Koreksi jika Proposal Ditolak -->
            @if($proposal->status_validasi === 'tidak_valid' && $proposal->catatan)
            <div class="mb-10">
                <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-r-lg">
                    <h5 class="text-red-700 font-bold mb-4 flex items-center text-lg">
                        <i class="fas fa-times-circle me-3"></i>Proposal Ditolak oleh Dosen Pendamping
                    </h5>
                    <hr class="border-red-200 mb-4">
                    <p class="mb-2 text-red-900 font-semibold">Alasan Penolakan:</p>
                    <p class="mb-4 text-red-800">{{ $proposal->catatan }}</p>
                    @if($proposal->tanggal_validasi)
                        <small class="text-red-600 font-medium">
                            <i class="fas fa-calendar me-2"></i>
                            Tanggal: {{ \Carbon\Carbon::parse($proposal->tanggal_validasi)->format('d F Y H:i') }}
                        </small>
                    @endif
                </div>
            </div>
            @elseif($proposal->catatan)
            <!-- Catatan (jika ada tapi bukan penolakan) -->
            <div class="mb-10">
                <h5 class="text-navy-700 font-semibold mb-6 pb-3 border-b-2 border-slate-100 flex items-center">
                    <i class="fas fa-sticky-note me-3 text-navy-500"></i>Catatan
                </h5>
                <div class="flex justify-between items-center p-4 bg-slate-50 rounded-lg border-l-4 border-navy-500">
                    <span class="font-semibold text-slate-600">Catatan</span>
                    <span class="text-slate-900 font-medium text-right ml-4">{{ $proposal->catatan }}</span>
                </div>
            </div>
            @endif

            <!-- Review PDF Dosen (untuk validasi) -->
            @if($proposal->status_validasi === 'valid' && $proposal->path_review_dosen)
            <div class="mb-10">
                <h5 class="text-navy-700 font-semibold mb-6 pb-3 border-b-2 border-slate-100 flex items-center">
                    <i class="fas fa-file-pdf me-3 text-navy-500"></i>Review PDF dari Dosen
                </h5>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 bg-slate-50 rounded-lg border-l-4 border-navy-500">
                    <span class="font-semibold text-slate-600 mb-3 sm:mb-0">File Review</span>
                    <span class="text-right flex flex-col items-end">
                        <a href="{{ route('file.serve', ['path' => $proposal->path_review_dosen]) }}" target="_blank" class="px-4 py-2 bg-white border border-navy-500 text-navy-600 rounded-lg font-medium hover:bg-navy-50 hover:text-navy-700 transition-colors shadow-sm inline-flex items-center">
                            <i class="fas fa-download me-2"></i>
                            {{ $proposal->nama_file_review_dosen ?? 'Download Review PDF' }}
                        </a>
                        @if($proposal->tanggal_review_dosen)
                            <small class="text-slate-500 mt-2 block font-medium">
                                <i class="fas fa-calendar me-1"></i>
                                Diupload pada: {{ \Carbon\Carbon::parse($proposal->tanggal_review_dosen)->format('d F Y H:i') }}
                            </small>
                        @endif
                    </span>
                </div>
            </div>
            @endif
        </div>
    </x-ui.card>

    <!-- PDF Viewer -->
    <x-ui.card className="mb-8 overflow-hidden pdf-viewer-container" id="proposalPdfContainer">
        <div class="bg-slate-50 p-4 border-b border-slate-200 flex justify-between items-center flex-wrap gap-4">
            <h5 class="font-semibold text-slate-800 m-0 flex items-center text-lg">
                <i class="fas fa-file-pdf me-3 text-red-500 text-xl"></i>
                Dokumen Proposal
            </h5>
            <div class="flex gap-2">
                @if($proposal->dokumen && $proposal->dokumen->path_file)
                <button id="fullscreenBtn" class="px-3 py-1.5 bg-white border border-slate-300 text-slate-600 rounded hover:bg-slate-50 transition-colors text-sm font-medium inline-flex items-center">
                    <i class="fas fa-expand me-2"></i>Fullscreen
                </button>
                <a href="{{ route('mahasiswa.proposal.download', [$proposal->id_proposal, 'proposal']) }}" class="px-3 py-1.5 bg-navy-600 text-white rounded hover:bg-navy-700 transition-colors shadow-sm text-sm font-medium inline-flex items-center">
                    <i class="fas fa-download me-2"></i>Download
                </a>
                @endif
            </div>
        </div>
        @if($proposal->dokumen && $proposal->dokumen->path_file)
        <div id="pdfViewer" class="relative min-h-[700px] bg-slate-100 flex items-center justify-center">
            <div class="w-10 h-10 border-4 border-slate-200 border-t-navy-600 rounded-full animate-spin absolute z-10"></div>
            <!-- PDF iframe will be inserted here -->
        </div>
        @else
        <div class="text-center py-16 px-8 text-slate-500">
            <i class="fas fa-file-pdf text-6xl text-slate-300 mb-6 block"></i>
            <h4 class="text-xl font-semibold text-slate-700 mb-3">Tidak Ada Dokumen</h4>
            <p class="text-slate-500 text-lg">Dokumen proposal belum diunggah atau tidak tersedia.</p>
        </div>
        @endif
    </x-ui.card>

    <!-- Revision Documents -->
    @if($proposal->proposalRevisi->count() > 0)
    <x-ui.card className="mb-8 overflow-hidden pdf-viewer-container" id="revisiPdfContainer">
        <div class="bg-slate-50 p-4 border-b border-slate-200 flex justify-between items-center flex-wrap gap-4">
            <h5 class="font-semibold text-slate-800 m-0 flex items-center text-lg">
                <i class="fas fa-edit me-3 text-orange-500 text-xl"></i>
                Dokumen Revisi (@formatId($proposal->proposalRevisi->count()) file)
            </h5>
            <div class="flex gap-2">
                <button id="revisiFullscreenBtn" class="px-3 py-1.5 bg-white border border-slate-300 text-slate-600 rounded hover:bg-slate-50 transition-colors text-sm font-medium inline-flex items-center">
                    <i class="fas fa-expand me-2"></i>Fullscreen
                </button>
            </div>
        </div>
        
        <!-- Revision Tabs -->
        <div class="bg-white">
            <ul class="flex border-b border-slate-200 overflow-x-auto" id="revisionTabs" role="tablist">
                @foreach($proposal->proposalRevisi as $index => $revisi)
                <li class="mr-1" role="presentation">
                    <button class="inline-flex flex-col items-center justify-center py-4 px-6 border-b-2 font-medium text-sm transition-colors {{ $index === 0 ? 'border-navy-600 text-navy-600 bg-navy-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 hover:bg-slate-50' }}" 
                            id="revisi-tab-{{ $revisi->id_revisi }}" 
                            data-bs-toggle="tab" 
                            data-bs-target="#revisi-{{ $revisi->id_revisi }}" 
                            type="button" 
                            role="tab">
                        <span class="flex items-center mb-1">
                            <i class="fas fa-file-pdf me-2"></i> Revisi {{ $index + 1 }}
                        </span>
                        <small class="text-xs font-normal opacity-80">{{ $revisi->tanggal_submit->format('d/m/Y H:i') }}</small>
                    </button>
                </li>
                @endforeach
            </ul>
            
            <div class="tab-content" id="revisionTabContent">
                @foreach($proposal->proposalRevisi as $index => $revisi)
                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                     id="revisi-{{ $revisi->id_revisi }}" 
                     role="tabpanel">
                    <div class="bg-slate-50 p-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex-1">
                            <div class="text-sm text-slate-500 mb-1">Nama File:</div>
                            <div class="font-medium text-slate-800 break-all">{{ $revisi->nama_file }}</div>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm text-slate-500 mb-1">Tanggal Submit:</div>
                            <div class="font-medium text-slate-800">{{ $revisi->tanggal_submit->format('d/m/Y H:i') }}</div>
                        </div>
                        <div>
                            <a href="{{ route('file.serve', ['path' => $revisi->path_file]) }}" 
                               class="px-4 py-2 bg-white border border-navy-500 text-navy-600 rounded-lg font-medium hover:bg-navy-50 hover:text-navy-700 transition-colors shadow-sm inline-flex items-center whitespace-nowrap" 
                               download="{{ $revisi->nama_file }}">
                                <i class="fas fa-download me-2"></i>Download
                            </a>
                        </div>
                    </div>
                    <div id="revisiPdfViewer-{{ $revisi->id_revisi }}" class="relative min-h-[500px] bg-slate-100 flex items-center justify-center pdf-loading">
                        <div class="w-10 h-10 border-4 border-slate-200 border-t-navy-600 rounded-full animate-spin absolute z-10 spinner"></div>
                        <!-- PDF iframe will be inserted here -->
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </x-ui.card>
    @endif

    <!-- Action Buttons -->
    <div class="flex flex-wrap gap-4 mt-8 mb-12 p-6 bg-slate-50 rounded-xl border border-slate-200">
        <a href="{{ route('mahasiswa.proposal.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg font-medium hover:bg-slate-50 transition-colors inline-flex items-center shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
        </a>
        
        @if($proposal->status_validasi === 'tidak_valid')
        <a href="{{ route('mahasiswa.proposal.create') }}" class="px-5 py-2.5 bg-navy-600 text-white rounded-lg font-medium hover:bg-navy-700 transition-colors inline-flex items-center shadow-sm">
            <i class="fas fa-redo me-2"></i>Ajukan Ulang Proposal
        </a>
        @elseif($proposal->status === 'revisi' && $proposal->status_validasi !== 'pending')
        <a href="{{ route('mahasiswa.proposal.revisi', $proposal->id_proposal) }}" class="px-5 py-2.5 bg-orange-500 text-white rounded-lg font-medium hover:bg-orange-600 transition-colors inline-flex items-center shadow-sm">
            <i class="fas fa-edit me-2"></i>Revisi Proposal
        </a>
        @elseif($proposal->status === 'revisi' && $proposal->status_validasi === 'pending')
        <span class="px-5 py-2.5 bg-blue-100 text-blue-800 rounded-lg font-medium inline-flex items-center border border-blue-200 cursor-not-allowed">
            <i class="fas fa-clock me-2"></i>Menunggu Validasi Dosen Pendamping
        </span>
        @elseif($proposal->status === 'revisi_akhir')
        <a href="{{ route('mahasiswa.proposal.revisi.akhir', $proposal->id_proposal) }}" class="px-5 py-2.5 bg-orange-500 text-white rounded-lg font-medium hover:bg-orange-600 transition-colors inline-flex items-center shadow-sm">
            <i class="fas fa-edit me-2"></i>Revisi Akhir Proposal
        </a>
        @elseif($proposal->status === 'revisi_submitted' || $proposal->status === 'validasi_akhir_dosen_univ')
        <span class="px-5 py-2.5 bg-blue-100 text-blue-800 rounded-lg font-medium inline-flex items-center border border-blue-200 cursor-not-allowed">
            <i class="fas fa-clock me-2"></i>Menunggu Validasi
        </span>
        @elseif(in_array($proposal->status, ['draft', 'pending']))
        <a href="{{ route('mahasiswa.proposal.edit', $proposal->id_proposal) }}" class="px-5 py-2.5 bg-orange-500 text-white rounded-lg font-medium hover:bg-orange-600 transition-colors inline-flex items-center shadow-sm">
            <i class="fas fa-edit me-2"></i>Edit Proposal
        </a>
        @endif
        
        @if($proposal->status == 'draft')
        <form action="{{ route('mahasiswa.proposal.destroy', $proposal->id_proposal) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus proposal ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors inline-flex items-center shadow-sm">
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
        const pdfUrl = '{{ $proposal->dokumen ? route("mahasiswa.proposal.view-pdf", $proposal->id_proposal) : "" }}';
        
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
                loadRevisionPDF({{ $revisi->id_revisi }}, '{{ route('file.serve', ['path' => $revisi->path_file]) }}');
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
                    <a href="{{ $proposal->proposalRevisi->first() ? route('file.serve', ['path' => $proposal->proposalRevisi->first()->path_file]) : '#' }}" 
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
