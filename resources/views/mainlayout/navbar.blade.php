<nav class="h-[70px] bg-navy-900 text-white flex items-center justify-between px-4 md:px-6 sticky top-0 z-50">
    <div class="flex items-center gap-4">
        <!-- Hamburger Menu -->
        <button class="md:hidden text-white hover:text-navy-200 transition-colors" id="sidebarToggle">
            <i class="fas fa-bars text-xl"></i>
        </button>
        
        <a class="flex items-center gap-2 text-white font-bold text-lg hover:text-white transition-colors" href="#">
            <i class="fas fa-graduation-cap text-navy-200"></i>
            <span class="hidden sm:inline">Pengajuan Proposal</span>
        </a>
    </div>
    
    <div class="flex items-center gap-4">
        <!-- Notifications -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false" class="relative p-2 text-navy-200 hover:text-white transition-colors rounded-full hover:bg-navy-800">
                <i class="fas fa-bell text-xl"></i>
                <span id="notificationCount" class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center hidden">0</span>
            </button>

            <!-- Dropdown -->
            <div x-show="open" style="display: none;" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden z-50">
                <div class="bg-navy-50 px-4 py-3 flex justify-between items-center border-b border-slate-200">
                    <h6 class="text-sm font-bold text-slate-800 flex items-center gap-2 m-0">
                        <i class="fas fa-bell text-navy-600"></i>
                        Notifikasi
                    </h6>
                    <button class="text-xs text-navy-600 hover:text-navy-800 font-medium" id="markAllRead">
                        Tandai Semua Dibaca
                    </button>
                </div>
                
                <div class="max-h-[300px] overflow-y-auto">
                    @php
                        $navJadwal = \App\Models\RuangKontrol::where('is_active', true)->first();
                    @endphp
                    @if($navJadwal)
                    <div class="p-3 border-b border-slate-100 bg-blue-50/50 hover:bg-blue-50 transition-colors">
                        <div class="flex gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar-alt text-xs"></i>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-slate-800">Jadwal Pengajuan Proposal</div>
                                <div class="text-xs text-slate-600 mt-1">Pendaftaran dibuka: {{ \Carbon\Carbon::parse($navJadwal->tanggal_pendaftaran_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($navJadwal->tanggal_pendaftaran_selesai)->format('d M Y') }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div id="notificationList">
                        <!-- Notifications will be populated here -->
                    </div>
                </div>
                <div class="p-2 text-center border-t border-slate-100 bg-slate-50">
                    <a href="#" class="text-xs text-navy-600 hover:text-navy-800 font-medium" id="viewAllNotifications">
                        Lihat Semua Notifikasi
                    </a>
                </div>
            </div>
        </div>
        
        <!-- User Profile -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 hover:bg-navy-800 p-1.5 rounded-lg transition-colors">
                <div class="w-8 h-8 rounded-full bg-navy-200 flex items-center justify-center text-navy-900">
                    <i class="fas fa-user text-sm"></i>
                </div>
                <span class="font-medium text-sm hidden md:block">
                    {{ \App\Helpers\UserHelper::getCurrentUserName() }}
                </span>
                <i class="fas fa-chevron-down text-xs text-navy-300 ml-1 hidden md:block"></i>
            </button>

            <!-- Profile Dropdown (Simplified wrapper around existing component or inline) -->
            <div x-show="open" style="display: none;" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden z-50">
                <div class="p-4 border-b border-slate-100 bg-navy-50">
                    <p class="text-sm font-bold text-slate-800 m-0">{{ \App\Helpers\UserHelper::getCurrentUserName() }}</p>
                    <p class="text-xs text-slate-500 m-0 mt-1 capitalize">{{ auth()->user()->role }}</p>
                </div>
                <div class="p-2">
                    <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 rounded-md transition-colors">
                        <i class="fas fa-user-circle text-slate-400 w-4 text-center"></i> Profil
                    </a>
                    <div class="h-px bg-slate-100 my-1"></div>
                    <form id="logout-form-main" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md transition-colors text-left">
                            <i class="fas fa-sign-out-alt text-red-500 w-4 text-center"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
