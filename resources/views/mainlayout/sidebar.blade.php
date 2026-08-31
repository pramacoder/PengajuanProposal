<!-- Sidebar Overlay for Mobile -->
<div class="fixed inset-0 bg-navy-950/50 backdrop-blur-sm z-40 hidden md:hidden transition-opacity" id="sidebarOverlay"></div>

<!-- Sidebar -->
<div class="fixed md:static inset-y-0 left-0 z-50 w-[280px] bg-white border-r border-slate-200 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col h-full" id="sidebar">
    
    <div class="p-4 border-b border-slate-100 bg-navy-50 md:hidden flex items-center justify-between">
        <div class="flex items-center gap-2 text-navy-900 font-bold text-lg">
            <i class="fas fa-graduation-cap text-navy-600"></i> SISKA
        </div>
        <button class="text-slate-500 hover:text-navy-700" id="closeSidebarMobile">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1 px-3">
            <li>
                <div class="px-3 py-2 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Menu Utama</div>
                <ul class="space-y-1" id="pkm">
                    @if(auth()->check() && auth()->user()->role === 'mahasiswa')
                    <li>
                        <a href="{{ route('mahasiswa.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors @if(request()->routeIs('mahasiswa.dashboard')) bg-navy-50 text-navy-700 @else text-slate-600 hover:bg-slate-50 hover:text-navy-600 @endif">
                            <i class="fas fa-tachometer-alt w-5 text-center @if(request()->routeIs('mahasiswa.dashboard')) text-navy-600 @else text-slate-400 @endif"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mahasiswa.proposal.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors @if(request()->routeIs('mahasiswa.proposal.create')) bg-navy-50 text-navy-700 @else text-slate-600 hover:bg-slate-50 hover:text-navy-600 @endif">
                            <i class="fas fa-plus w-5 text-center @if(request()->routeIs('mahasiswa.proposal.create')) text-navy-600 @else text-slate-400 @endif"></i> Ajukan Proposal
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mahasiswa.proposal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors @if(request()->routeIs('mahasiswa.proposal.index')) bg-navy-50 text-navy-700 @else text-slate-600 hover:bg-slate-50 hover:text-navy-600 @endif">
                            <i class="fas fa-eye w-5 text-center @if(request()->routeIs('mahasiswa.proposal.index')) text-navy-600 @else text-slate-400 @endif"></i> Lihat Proposal
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mahasiswa.revisi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors @if(request()->routeIs('mahasiswa.revisi.*')) bg-navy-50 text-navy-700 @else text-slate-600 hover:bg-slate-50 hover:text-navy-600 @endif">
                            <i class="fas fa-edit w-5 text-center @if(request()->routeIs('mahasiswa.revisi.*')) text-navy-600 @else text-slate-400 @endif"></i> Revisi Proposal
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('mahasiswa.proposal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors @if(request()->routeIs('mahasiswa.proposal.revisi.akhir*')) bg-navy-50 text-navy-700 @else text-slate-600 hover:bg-slate-50 hover:text-navy-600 @endif">
                            <i class="fas fa-file-edit w-5 text-center @if(request()->routeIs('mahasiswa.proposal.revisi.akhir*')) text-navy-600 @else text-slate-400 @endif"></i> Revisi Akhir
                        </a>
                    </li>
                @endif
                
                @if(auth()->check() && auth()->user()->role === 'dosen')
                    <!-- Menu Dosen Pendamping -->
                    <li class="menu-dosen">
                        <a href="#" class="menu-toggle" data-target="pendampingProposal">
                            <i class="fas fa-user-check me-2"></i>Pendamping Proposal
                            <i class="fas fa-chevron-down float-end"></i>
                        </a>
                        <ul class="submenu" id="pendampingProposal">
                            <li><a href="{{ route('dosen.pendamping.dashboard') }}" class="@if(request()->routeIs('dosen.pendamping.dashboard')) active @endif">
                                <i class="fas fa-user-check me-2"></i>Dashboard Pendamping
                            </a></li>
                            <li><a href="{{ route('dosen.pendamping.proposal.validasi') }}" class="@if(request()->routeIs('dosen.pendamping.proposal.validasi')) active @endif">
                                <i class="fas fa-clipboard-check me-2"></i>Validasi Proposal
                            </a></li>
                            <li><a href="{{ route('dosen.hasil.review') }}" class="@if(request()->routeIs('dosen.hasil.review')) active @endif">
                                <i class="fas fa-clipboard-list me-2"></i>Hasil Review
                            </a></li>
                            <li><a href="{{ route('dosen.hasil.final') }}" class="@if(request()->routeIs('dosen.hasil.final')) active @endif">
                                <i class="fas fa-trophy me-2"></i>Hasil Final
                            </a></li>
                        </ul>
                    </li>

                    <!-- Menu Dosen Pembimbing -->
                    <li class="menu-dosen">
                        <a href="#" class="menu-toggle" data-target="pembimbingProposal">
                            <i class="fas fa-user-graduate me-2"></i>Pembimbing
                            <i class="fas fa-chevron-down float-end mt-1"></i>
                        </a>
                        <ul class="submenu" id="pembimbingProposal">
                            <li><a href="{{ route('dosen.pembimbing.dashboard') }}" class="@if(request()->routeIs('dosen.pembimbing.dashboard')) active @endif">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a></li>
                            <li><a href="{{ route('dosen.pembimbing.mahasiswa.bimbingan') }}" class="@if(request()->routeIs('dosen.pembimbing.mahasiswa.bimbingan')) active @endif">
                                <i class="fas fa-users me-2"></i>Mahasiswa Bimbingan
                            </a></li>
                            <li><a href="{{ route('dosen.pembimbing.validasi.2') }}" class="@if(request()->routeIs('dosen.pembimbing.validasi.2*')) active @endif">
                                <i class="fas fa-check-double me-2"></i>Validasi 2
                            </a></li>
                        </ul>
                    </li>
                    
                    <!-- Menu Dosen Universitas (Pendamping Universitas) -->
                    <li class="menu-dosen">
                        <a href="#" class="menu-toggle" data-target="dosenUniversitas">
                            <i class="fas fa-user-graduate me-2"></i>Pendamping Univ
                            <i class="fas fa-chevron-down float-end"></i>
                        </a>
                        <ul class="submenu" id="dosenUniversitas">
                            <li><a href="{{ route('dosen.universitas.dashboard') }}" class="@if(request()->routeIs('dosen.universitas.dashboard')) active @endif">
                                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                            </a></li>
                            <li><a href="{{ route('dosen.universitas.validasi.akhir') }}" class="@if(request()->routeIs('dosen.universitas.validasi.akhir*')) active @endif">
                                <i class="fas fa-check-double me-2"></i>Validasi Akhir
                            </a></li>
                        </ul>
                    </li>
                @endif
                
                @if(auth()->check() && auth()->user()->role === 'reviewer')
                    <li class="menu-reviewer"><a href="{{ route('reviewer.dashboard') }}" class="@if(request()->routeIs('reviewer.dashboard')) active @endif">
                        <i class="fas fa-home me-2"></i>Beranda
                    </a></li>
                    <li class="menu-reviewer"><a href="{{ route('reviewer.review.administratif') }}" class="@if(request()->routeIs('reviewer.review.administratif')) active @endif">
                        <i class="fas fa-clipboard-check me-2"></i>Review Administratif
                    </a></li>
                    <li class="menu-reviewer"><a href="{{ route('reviewer.review.substantif') }}" class="@if(request()->routeIs('reviewer.review.substantif')) active @endif">
                        <i class="fas fa-user-check me-2"></i>Review Substantif
                    </a></li>
                    <li class="menu-reviewer"><a href="{{ route('reviewer.review.substantif.seleksi') }}" class="@if(request()->routeIs('reviewer.review.substantif.seleksi')) active @endif">
                        <i class="fas fa-award me-2"></i>Review Substantif Seleksi
                    </a></li>
                @endif
                
                @if(auth()->check() && in_array(auth()->user()->role, ['operator', 'pimpinan_pt']))
                    @php
                        $operatorUser = auth()->user();
                        $isPimpinanPT = $operatorUser->role === 'pimpinan_pt';
                        $isOperator = $operatorUser->role === 'operator';
                    @endphp
                    
                    @if($isOperator)
                    <li class="menu-operator"><a href="{{ route('operator.pilih.reviewer') }}" class="@if(request()->routeIs('operator.pilih.reviewer')) active @endif">
                        <i class="fas fa-user-plus me-2"></i>Pilih Reviewer
                    </a></li>
                    <li class="menu-operator"><a href="{{ route('operator.pilih.reviewer.seleksi') }}" class="@if(request()->routeIs('operator.pilih.reviewer.seleksi')) active @endif">
                        <i class="fas fa-user-check me-2"></i>Pilih Reviewer Seleksi
                    </a></li>
                    <li class="menu-operator"><a href="{{ route('operator.ruang.kontrol') }}" class="@if(request()->routeIs('operator.ruang.kontrol')) active @endif">
                        <i class="fas fa-cogs me-2"></i>Ruang Kontrol
                    </a></li>
                    <li class="menu-operator"><a href="{{ route('operator.hasil.semi.final') }}" class="@if(request()->routeIs('operator.hasil.semi.final*')) active @endif">
                        <i class="fas fa-clipboard-check me-2"></i>Hasil Semi Final
                    </a></li>
                    <li class="menu-operator"><a href="{{ route('operator.hasil.final') }}" class="@if(request()->routeIs('operator.hasil.final*') || request()->routeIs('operator.detail.hasil.final')) active @endif">
                        <i class="fas fa-trophy me-2"></i>Hasil Final
                    </a></li>
                    <li class="menu-operator"><a href="{{ route('operator.form.penilaian.index') }}" class="@if(request()->routeIs('operator.form.penilaian.*')) active @endif">
                        <i class="fas fa-file-alt me-2"></i>Form Penilaian
                    </a></li>
                    <li class="menu-operator"><a href="{{ route('operator.laporan.simbelmawa.index') }}" class="@if(request()->routeIs('operator.laporan.simbelmawa.*')) active @endif">
                        <i class="fas fa-chart-bar me-2"></i>Laporan SIMBELMAWA
                    </a></li>
                    @elseif($isPimpinanPT)
                        {{-- Dashboard --}}
                        <li class="menu-operator"><a href="{{ route('pimpinan_pt.dashboard') }}" class="@if(request()->routeIs('pimpinan_pt.dashboard')) active @endif">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a></li>

                        {{-- Fase 2: Monitoring Reviewer --}}
                        <li class="menu-operator"><a href="{{ route('pimpinan_pt.pilih.reviewer') }}" class="@if(request()->routeIs('pimpinan_pt.pilih.reviewer')) active @endif">
                            <i class="fas fa-user-plus me-2"></i>Pilih Reviewer
                        </a></li>
                        <li class="menu-operator"><a href="{{ route('pimpinan_pt.pilih.reviewer.seleksi') }}" class="@if(request()->routeIs('pimpinan_pt.pilih.reviewer.seleksi')) active @endif">
                            <i class="fas fa-user-check me-2"></i>Pilih Reviewer Seleksi
                        </a></li>

                        {{-- Ruang Kontrol --}}
                        <li class="menu-operator"><a href="{{ route('pimpinan_pt.ruang.kontrol') }}" class="@if(request()->routeIs('pimpinan_pt.ruang.kontrol')) active @endif">
                            <i class="fas fa-cogs me-2"></i>Ruang Kontrol
                        </a></li>

                        {{-- Fase 3: Hasil Semi Final --}}
                        <li class="menu-operator"><a href="{{ route('pimpinan_pt.hasil.semi.final') }}" class="@if(request()->routeIs('pimpinan_pt.hasil.semi.final*') || request()->routeIs('pimpinan_pt.detail.hasil.semi.final')) active @endif">
                            <i class="fas fa-clipboard-check me-2"></i>Hasil Semi Final
                        </a></li>

                        {{-- Fase 4: Penilaian Final (Khusus Pimpinan PT) --}}
                        <li class="menu-operator"><a href="{{ route('pimpinan_pt.dashboard') }}" class="@if(request()->routeIs('pimpinan_pt.detail.hasil.final')) active @endif">
                            <i class="fas fa-trophy me-2"></i>Penilaian Final
                        </a></li>

                        {{-- Form Penilaian (Read-only) --}}
                        <li class="menu-operator"><a href="{{ route('pimpinan_pt.form.penilaian') }}" class="@if(request()->routeIs('pimpinan_pt.form.penilaian')) active @endif">
                            <i class="fas fa-file-alt me-2"></i>Form Penilaian
                        </a></li>

                        {{-- Laporan SIMBELMAWA (Read-only) --}}
                        <li class="menu-operator"><a href="{{ route('pimpinan_pt.laporan.simbelmawa') }}" class="@if(request()->routeIs('pimpinan_pt.laporan.simbelmawa')) active @endif">
                            <i class="fas fa-chart-bar me-2"></i>Laporan SIMBELMAWA
                        </a></li>

                        {{-- Manajemen Akun (Khusus Pimpinan PT) --}}
                        <li class="menu-operator"><a href="{{ route('pimpinan_pt.manage.accounts') }}" class="@if(request()->routeIs('pimpinan_pt.manage.accounts*')) active @endif">
                            <i class="fas fa-id-card me-2"></i>Manajemen Akun
                        </a></li>
                    @endif
                @endif
                </ul>
            </li>
        </ul>
    </div>
</div>
