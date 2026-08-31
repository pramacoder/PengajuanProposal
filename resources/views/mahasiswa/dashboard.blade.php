@extends('mainlayout.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')

@php
    $now = now();
    
    // Helper function to determine phase state
    $getPhaseState = function($startDate, $endDate, $status) use ($now) {
        if (!$startDate || !$endDate) {
            return $status === 'terbuka' ? 'current' : 'upcoming';
        }
        
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate)->endOfDay();
        
        if ($now->lt($start) && $status !== 'terbuka') return 'upcoming';
        if ($now->between($start, $end) || $status === 'terbuka') return 'current';
        return 'ended';
    };

    $ruangKontrolExists = isset($ruangKontrol) && $ruangKontrol;

    $phase1State = $ruangKontrolExists ? $getPhaseState($ruangKontrol->tanggal_pendaftaran_mulai, $ruangKontrol->tanggal_pendaftaran_selesai, $ruangKontrol->status_pendaftaran) : 'upcoming';
    $phase2State = $ruangKontrolExists ? $getPhaseState($ruangKontrol->tanggal_review_mulai, $ruangKontrol->tanggal_review_selesai, $ruangKontrol->status_review) : 'upcoming';
    $phase3State = $ruangKontrolExists ? $getPhaseState($ruangKontrol->tanggal_perbaikan_mulai, $ruangKontrol->tanggal_perbaikan_selesai, $ruangKontrol->status_perbaikan) : 'upcoming';
    $phase4State = $ruangKontrolExists ? $getPhaseState($ruangKontrol->tanggal_penilaian_akhir_mulai, $ruangKontrol->tanggal_penilaian_akhir_selesai, $ruangKontrol->status_penilaian_akhir) : 'upcoming';


    // Current phase info for text below
    $currentPhaseName = '-';
    $currentPhaseDates = '';
    $currentPhaseStatus = 'DITUTUP';
    $alertBannerText = 'Sistem PKM saat ini ditutup. Nantikan informasi selanjutnya.';
    $alertBannerSub = 'Anda dapat mempersiapkan draf proposal Anda kapan saja.';
    
    if ($ruangKontrolExists) {
        if ($phase1State === 'current') {
            $currentPhaseName = 'Fase 1: Pendaftaran';
            $currentPhaseDates = $ruangKontrol->tanggal_pendaftaran_mulai ? \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_mulai)->format('d M') . ' - ' . \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_selesai)->format('d M Y') : 'Jadwal belum ditentukan';
            $currentPhaseStatus = 'DIBUKA';
            $alertBannerText = 'Fase 1 Pengajuan Proposal PKM Sedang Berlangsung!';
            $alertBannerSub = 'Segera ajukan proposal Anda sebelum batas waktu berakhir.';
        } elseif ($phase2State === 'current') {
            $currentPhaseName = 'Fase 2: Review';
            $currentPhaseDates = $ruangKontrol->tanggal_review_mulai ? \Carbon\Carbon::parse($ruangKontrol->tanggal_review_mulai)->format('d M') . ' - ' . \Carbon\Carbon::parse($ruangKontrol->tanggal_review_selesai)->format('d M Y') : 'Jadwal belum ditentukan';
            $currentPhaseStatus = 'DIBUKA';
            $alertBannerText = 'Fase 2 Review Proposal PKM Sedang Berlangsung.';
            $alertBannerSub = 'Proposal sedang dalam tahap penilaian oleh reviewer.';
        } elseif ($phase3State === 'current') {
            $currentPhaseName = 'Fase 3: Perbaikan';
            $currentPhaseDates = $ruangKontrol->tanggal_perbaikan_mulai ? \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_mulai)->format('d M') . ' - ' . \Carbon\Carbon::parse($ruangKontrol->tanggal_perbaikan_selesai)->format('d M Y') : 'Jadwal belum ditentukan';
            $currentPhaseStatus = 'DIBUKA';
            $alertBannerText = 'Fase 3 Perbaikan Proposal PKM Sedang Berlangsung!';
            $alertBannerSub = 'Bagi yang lolos revisi, segera perbaiki proposal Anda.';
        } elseif ($phase4State === 'current') {
            $currentPhaseName = 'Fase 4: Penilaian Akhir';
            $currentPhaseDates = $ruangKontrol->tanggal_penilaian_akhir_mulai ? \Carbon\Carbon::parse($ruangKontrol->tanggal_penilaian_akhir_mulai)->format('d M') . ' - ' . \Carbon\Carbon::parse($ruangKontrol->tanggal_penilaian_akhir_selesai)->format('d M Y') : 'Jadwal belum ditentukan';
            $currentPhaseStatus = 'DIBUKA';
            $alertBannerText = 'Fase 4 Penilaian Akhir Proposal PKM Sedang Berlangsung.';
            $alertBannerSub = 'Menunggu pengumuman final pendanaan PKM.';
        } else {
            if ($phase4State === 'ended') {
                $currentPhaseName = 'Siklus PKM ' . $ruangKontrol->tahun_ajaran . ' Selesai';
                $alertBannerText = 'Siklus PKM ' . $ruangKontrol->tahun_ajaran . ' Telah Selesai.';
            }
            elseif ($phase3State === 'ended') {
                $currentPhaseName = 'Menunggu Penilaian Akhir';
                $alertBannerText = 'Fase 3 Perbaikan Telah Berakhir. Menunggu Fase 4.';
            }
            elseif ($phase2State === 'ended') {
                $currentPhaseName = 'Menunggu Perbaikan';
                $alertBannerText = 'Fase 2 Review Telah Berakhir. Menunggu Pengumuman Lolos.';
            }
            elseif ($phase1State === 'ended') {
                $currentPhaseName = 'Menunggu Review';
                $alertBannerText = 'Fase 1 Pendaftaran Telah Berakhir. (Tutup: ' . ($ruangKontrol->tanggal_pendaftaran_selesai ? \Carbon\Carbon::parse($ruangKontrol->tanggal_pendaftaran_selesai)->format('d M Y') : '-') . ').';
                $alertBannerSub = 'Mohon nantikan pengumuman Fase 2. Anda masih dapat mempersiapkan draf Anda.';
            }
            else {
                $currentPhaseName = 'Belum Dimulai';
                $alertBannerText = 'Siklus PKM Belum Dimulai.';
            }
        }
    }
@endphp

<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
        ['label' => 'Dashboard', 'active' => true],
    ]" />

    <x-page-header 
        title="Dashboard Mahasiswa" 
        subtitle="UNIVERSITAS UDAYANA" />

    <div class="flex justify-between items-center mt-2">
        <div>
            <h3 class="font-serif text-3xl font-bold text-navy-900 mb-1">
                Selamat Datang, {{ auth()->user()->name }}
            </h3>
        </div>
    </div>

    {{-- Alert blocking proposal --}}
    @if(isset($blockingProposal) && $blockingProposal)
        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-r-lg relative" role="alert">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-orange-600 text-xl mt-0.5"></i>
                <div>
                    <strong class="text-orange-800">Anda sudah terdaftar dalam proposal "{{ $blockingProposal->judul }}"</strong>
                    <p class="text-orange-700 text-sm mt-1">Satu mahasiswa hanya dapat terdaftar dalam satu proposal PKM per tahun akademik.</p>
                </div>
            </div>
            <button type="button" class="absolute top-4 right-4 text-orange-500 hover:text-orange-700" onclick="this.parentElement.remove()" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- PKM Cycle Timeline Component --}}
    <div class="mb-10 mt-6">
        <h4 class="font-serif text-2xl font-bold mb-10 text-center text-navy-900">Jadwal & Siklus Penilaian</h4>
        
        <div class="relative flex justify-between items-center my-10 px-4 max-w-4xl mx-auto">
            <!-- Connecting Line -->
            <div class="absolute top-[28px] left-0 w-full h-[2px] bg-slate-200 z-0"></div>
            
            {{-- Phase 1 --}}
            <div class="text-center relative z-10 w-[120px]">
                <div class="font-serif mb-3 text-xl {{ $phase1State === 'current' ? 'text-navy-700 font-bold' : 'text-slate-400' }}">I</div>
                <div class="rounded-full mx-auto flex items-center justify-center w-4 h-4 {{ $phase1State === 'current' ? 'bg-yellow-500 ring-4 ring-yellow-200' : ($phase1State === 'ended' ? 'bg-navy-500 ring-4 ring-navy-100' : 'bg-slate-200 border-2 border-slate-300') }}"></div>
                <div class="mt-4 font-medium text-sm {{ $phase1State === 'current' ? 'text-slate-900' : 'text-slate-500' }}">Pendaftaran</div>
                @if($phase1State === 'ended') <div class="uppercase mt-1 text-[10px] tracking-wider text-slate-400 font-bold">Selesai</div> @endif
            </div>

            {{-- Phase 2 --}}
            <div class="text-center relative z-10 w-[120px]">
                <div class="font-serif mb-3 text-xl {{ $phase2State === 'current' ? 'text-navy-700 font-bold' : 'text-slate-400' }}">II</div>
                <div class="rounded-full mx-auto flex items-center justify-center w-4 h-4 {{ $phase2State === 'current' ? 'bg-yellow-500 ring-4 ring-yellow-200' : ($phase2State === 'ended' ? 'bg-navy-500 ring-4 ring-navy-100' : 'bg-slate-200 border-2 border-slate-300') }}"></div>
                <div class="mt-4 font-medium text-sm {{ $phase2State === 'current' ? 'text-slate-900' : 'text-slate-500' }}">Review</div>
                @if($phase2State === 'ended') <div class="uppercase mt-1 text-[10px] tracking-wider text-slate-400 font-bold">Selesai</div> @endif
            </div>

            {{-- Phase 3 --}}
            <div class="text-center relative z-10 w-[120px]">
                <div class="font-serif mb-3 text-xl {{ $phase3State === 'current' ? 'text-navy-700 font-bold' : 'text-slate-400' }}">III</div>
                <div class="rounded-full mx-auto flex items-center justify-center w-4 h-4 {{ $phase3State === 'current' ? 'bg-yellow-500 ring-4 ring-yellow-200' : ($phase3State === 'ended' ? 'bg-navy-500 ring-4 ring-navy-100' : 'bg-slate-200 border-2 border-slate-300') }}"></div>
                <div class="mt-4 font-medium text-sm {{ $phase3State === 'current' ? 'text-slate-900' : 'text-slate-500' }}">Perbaikan</div>
                @if($phase3State === 'ended') <div class="uppercase mt-1 text-[10px] tracking-wider text-slate-400 font-bold">Selesai</div> @endif
            </div>

            {{-- Phase 4 --}}
            <div class="text-center relative z-10 w-[120px]">
                <div class="font-serif mb-3 text-xl {{ $phase4State === 'current' ? 'text-navy-700 font-bold' : 'text-slate-400' }}">IV</div>
                <div class="rounded-full mx-auto flex items-center justify-center w-4 h-4 {{ $phase4State === 'current' ? 'bg-yellow-500 ring-4 ring-yellow-200' : ($phase4State === 'ended' ? 'bg-navy-500 ring-4 ring-navy-100' : 'bg-slate-200 border-2 border-slate-300') }}"></div>
                <div class="mt-4 font-medium text-sm {{ $phase4State === 'current' ? 'text-slate-900' : 'text-slate-500' }}">Penilaian Akhir</div>
                @if($phase4State === 'ended') <div class="uppercase mt-1 text-[10px] tracking-wider text-slate-400 font-bold">Selesai</div> @endif
            </div>
        </div>

        <div class="text-center mt-10">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-slate-50 border border-slate-200 shadow-sm text-sm text-slate-600">
                <i class="fas fa-calendar-alt text-navy-400"></i>
                <span>Fase Saat Ini ({{ $currentPhaseDates }}):</span>
                <span class="font-bold font-serif text-navy-700">{{ $currentPhaseName }}</span>
            </div>
        </div>
    </div>

    {{-- Info Banner Alert --}}
    @if(isset($ruangKontrol) && $ruangKontrol)
        <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-900 rounded-xl p-4 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1 h-full bg-rose-600"></div>
            <div class="flex items-center gap-4 pl-2">
                <div class="rounded-full flex items-center justify-center shrink-0 text-white w-10 h-10 bg-rose-700 shadow-md">
                    <i class="fas fa-info"></i>
                </div>
                <div>
                    <strong class="text-[0.95rem] block mb-0.5">{{ $alertBannerText }}</strong>
                    <span class="text-[0.9rem] text-rose-700">
                        {{ $alertBannerSub }}
                    </span>
                </div>
            </div>
        </div>
    @endif

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Left Column: Submissions Overview --}}
        <div class="lg:col-span-5">
            <x-ui.card className="h-full">
                <div class="px-5 pt-5 pb-0 flex justify-between items-center">
                    <h5 class="font-bold text-navy-900 m-0">Ringkasan Proposal Saya</h5>
                    <i class="far fa-question-circle text-slate-400"></i>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.stat-card title="Total Proposal" value="{{ $totalProposals }}" accent="blue" />
                        <x-ui.stat-card title="Sedang Direview" value="{{ $underReview }}" accent="yellow" />
                        <x-ui.stat-card title="Menunggu Validasi" value="{{ $waitingValidation }}" accent="navy" />
                        <x-ui.stat-card title="Disetujui" value="{{ $approved }}" accent="green" />
                    </div>
                </div>
            </x-ui.card>
        </div>

        {{-- Right Column: Submissions Saya --}}
        <div class="lg:col-span-7">
            <x-ui.card className="h-full">
                <div class="px-5 pt-5 pb-0">
                    <h5 class="font-bold text-navy-900 m-0">Proposal Saya</h5>
                </div>
                <div class="p-5">
                    @if($proposals->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse" id="dataTable">
                                <thead>
                                    <tr class="border-b border-slate-200">
                                        <th class="py-3 px-4 font-semibold text-sm text-slate-600">Judul Proposal</th>
                                        <th class="py-3 px-4 font-semibold text-sm text-slate-600">Skim</th>
                                        <th class="py-3 px-4 font-semibold text-sm text-slate-600">Status</th>
                                        <th class="py-3 px-4 font-semibold text-sm text-slate-600">Tanggal</th>
                                        <th class="py-3 px-4 font-semibold text-sm text-slate-600 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proposals as $proposal)
                                    <tr class="border-b border-slate-100 hover:bg-slate-50 transition-colors">
                                        <td class="py-3 px-4">
                                            <div class="font-medium text-slate-900 truncate max-w-[200px]" title="{{ $proposal->judul }}">
                                                {{ $proposal->judul }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded bg-blue-50 text-blue-700 text-xs font-semibold">{{ $proposal->skim }}</span>
                                        </td>
                                        <td class="py-3 px-4"><x-status-badge :status="$proposal->status" /></td>
                                        <td class="py-3 px-4 text-sm text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($proposal->created_at)->format('d M Y') }}</td>
                                        <td class="py-3 px-4 text-center">
                                            <a href="{{ route('mahasiswa.proposal.show', $proposal->id_proposal) }}" class="inline-flex items-center justify-center w-8 h-8 rounded bg-blue-100 text-blue-600 hover:bg-blue-200 transition-colors" title="Lihat Detail">
                                                <i class="fas fa-eye text-sm"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        {{-- Empty State Design --}}
                        <div class="flex flex-col sm:flex-row items-center gap-6 py-6">
                            <div class="shrink-0 text-center sm:ml-4">
                                <i class="fas fa-folder-open text-slate-200 text-[8rem]"></i>
                            </div>
                            <div>
                                <p class="text-slate-700 mb-1 text-sm">
                                    Anda belum mengajukan proposal apapun. Meskipun Fase 1 ditutup, Anda tetap dapat menyiapkan draf proposal.
                                </p>
                                <p class="font-bold text-navy-900 mb-5">
                                    Belum Ada Proposal Diajukan. Mulai siapkan draf Anda!
                                </p>
                                <div class="flex flex-wrap gap-3">
                                    <div data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $phase1State === 'current' ? 'Ajukan Sekarang' : 'Akan Aktif Saat Fase Pendaftaran Dibuka' }}">
                                        @if($phase1State === 'current')
                                            <x-ui.button variant="primary" onclick="window.location='{{ route('mahasiswa.proposal.create') }}'">
                                                <i class="fas fa-plus mr-1"></i> Buat Draf Proposal Baru
                                            </x-ui.button>
                                        @else
                                            <x-ui.button variant="primary" disabled>
                                                <i class="fas fa-plus mr-1"></i> Buat Draf Proposal Baru
                                            </x-ui.button>
                                        @endif
                                    </div>
                                    <x-ui.button variant="secondary">
                                        Lihat Panduan PKM
                                    </x-ui.button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('#dataTable').length) {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Indonesian.json"
                },
                "pageLength": 5,
                "order": [[ 3, "desc" ]],
                "dom": '<"flex flex-col sm:flex-row justify-between items-center mb-4 gap-4"lf>rtip'
            });
        }
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush
