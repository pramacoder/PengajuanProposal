<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    
    <ul class="sidebar-menu">
        <li>
            <a href="#" class="menu-toggle" data-target="pkm" id="pkmMenu">
                <i class="fas fa-lightbulb me-2"></i>PKM
                <i class="fas fa-chevron-down float-end mt-1"></i>
            </a>
            <ul class="submenu" id="pkm">
                @if(auth()->check() && auth()->user()->role === 'mahasiswa')
                    <li class="menu-mahasiswa"><a href="{{ route('mahasiswa.dashboard') }}" class="@if(request()->routeIs('mahasiswa.dashboard')) active @endif">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a></li>
                    <li class="menu-mahasiswa"><a href="{{ route('mahasiswa.proposal.create') }}" class="@if(request()->routeIs('mahasiswa.proposal.create')) active @endif">
                        <i class="fas fa-plus me-2"></i>Ajukan Proposal
                    </a></li>
                    <li class="menu-mahasiswa"><a href="{{ route('mahasiswa.proposal.index') }}" class="@if(request()->routeIs('mahasiswa.proposal.index')) active @endif">
                        <i class="fas fa-eye me-2"></i>Lihat Proposal
                    </a></li>
                    <li class="menu-mahasiswa"><a href="{{ route('mahasiswa.revisi.index') }}" class="@if(request()->routeIs('mahasiswa.revisi.*')) active @endif">
                        <i class="fas fa-edit me-2"></i>Revisi Proposal
                    </a></li>
                    <li class="menu-mahasiswa"><a href="{{ route('mahasiswa.proposal.index') }}" class="@if(request()->routeIs('mahasiswa.proposal.revisi.akhir*')) active @endif">
                        <i class="fas fa-file-edit me-2"></i>Revisi Akhir
                    </a></li>
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
                        <li class="menu-operator"><a href="{{ route('pimpinan_pt.dashboard') }}" class="@if(request()->routeIs('pimpinan_pt.dashboard')) active @endif">
                            <i class="fas fa-home me-2"></i>Beranda
                        </a></li>
                        <li class="menu-operator"><a href="{{ route('pimpinan_pt.dashboard') }}" class="@if(request()->routeIs('pimpinan_pt.dashboard') || request()->routeIs('pimpinan_pt.detail.hasil.final')) active @endif">
                            <i class="fas fa-trophy me-2"></i>Hasil Final
                        </a></li>
                        <li class="menu-operator"><a href="{{ route('pimpinan_pt.manage.accounts') }}" class="@if(request()->routeIs('pimpinan_pt.manage.accounts*')) active @endif">
                            <i class="fas fa-id-card me-2"></i>Manajemen Akun
                        </a></li>
                    @endif
                @endif
            </ul>
        </li>
        
    </ul>
</div>
