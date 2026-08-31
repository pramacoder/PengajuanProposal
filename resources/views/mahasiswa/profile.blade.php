@extends('mainlayout.app')

@section('title', 'Profil Mahasiswa')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('mahasiswa.dashboard')],
        ['label' => 'Profil', 'active' => true],
    ]" />

    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <x-page-header 
            title="Profil Mahasiswa" 
            subtitle="Informasi detail profil dan data akademik Anda"
        />
        <div>
            <a href="{{ route('mahasiswa.proposal.index') }}" class="px-4 py-2 border border-navy-600 text-navy-700 rounded-lg hover:bg-navy-50 font-medium transition-colors flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Profile Card -->
        <div class="col-span-1">
            <x-ui.card className="h-full overflow-hidden">
                <div class="bg-gradient-to-br from-navy-600 to-navy-800 text-white p-5">
                    <h5 class="font-bold text-lg m-0 flex items-center">
                        <i class="fas fa-user-circle mr-3 text-navy-200"></i>Informasi Pribadi
                    </h5>
                </div>
                <div class="p-6 text-center">
                    <!-- Avatar -->
                    <div class="mb-5">
                        <div class="w-24 h-24 rounded-full bg-slate-100 flex items-center justify-center mx-auto shadow-sm border-4 border-white text-navy-300 text-4xl">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    
                    <!-- Basic Info -->
                    <h4 class="text-navy-800 font-bold text-xl mb-1">{{ $user->nama_mhs }}</h4>
                    <p class="text-slate-500 font-medium mb-6 bg-slate-50 inline-block px-3 py-1 rounded-full text-sm">{{ $user->nim }}</p>
                    
                    <!-- Contact Info -->
                    <div class="flex flex-col gap-4 text-left border-t border-slate-100 pt-5">
                        <div class="flex items-start">
                            <i class="fas fa-envelope text-blue-500 mt-1 w-6 text-center mr-2"></i>
                            <span class="text-slate-700 break-all">{{ $user->email_mhs ?? 'Tidak tersedia' }}</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-phone text-emerald-500 mt-1 w-6 text-center mr-2"></i>
                            <span class="text-slate-700">{{ $user->no_telp_mhs ?? 'Tidak tersedia' }}</span>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-amber-500 mt-1 w-6 text-center mr-2"></i>
                            <span class="text-slate-700 leading-relaxed">{{ $user->alamat_mhs ?? 'Tidak tersedia' }}</span>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Academic Info -->
        <div class="col-span-1 lg:col-span-2">
            <x-ui.card className="h-full overflow-hidden">
                <div class="bg-gradient-to-br from-navy-600 to-navy-800 text-white p-5">
                    <h5 class="font-bold text-lg m-0 flex items-center">
                        <i class="fas fa-graduation-cap mr-3 text-navy-200"></i>Informasi Akademik
                    </h5>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Program Studi -->
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 transition-all hover:shadow-md hover:border-navy-200">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center">
                                <i class="fas fa-university text-blue-500 mr-2"></i>Program Studi
                            </label>
                            <div class="font-semibold text-slate-800 text-lg">
                                {{ $user->prodi->nama_prodi ?? 'Tidak tersedia' }}
                            </div>
                        </div>

                        <!-- Fakultas -->
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 transition-all hover:shadow-md hover:border-emerald-200">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center">
                                <i class="fas fa-building text-emerald-500 mr-2"></i>Fakultas
                            </label>
                            <div class="font-semibold text-slate-800 text-lg">
                                {{ $user->prodi->fakultas->nama_fakultas ?? 'Tidak tersedia' }}
                            </div>
                        </div>

                        <!-- Tahun Masuk -->
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 transition-all hover:shadow-md hover:border-cyan-200">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center">
                                <i class="fas fa-calendar-alt text-cyan-500 mr-2"></i>Tahun Masuk
                            </label>
                            <div class="font-semibold text-slate-800 text-lg">
                                {{ $user->tahun_masuk ?? 'Tidak tersedia' }}
                            </div>
                        </div>

                        <!-- Semester -->
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 transition-all hover:shadow-md hover:border-amber-200">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center">
                                <i class="fas fa-layer-group text-amber-500 mr-2"></i>Semester
                            </label>
                            <div class="font-semibold text-slate-800 text-lg">
                                {{ $user->semester ?? 'Tidak tersedia' }}
                            </div>
                        </div>

                        <!-- IPK -->
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 transition-all hover:shadow-md hover:border-rose-200">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center">
                                <i class="fas fa-chart-line text-rose-500 mr-2"></i>IPK
                            </label>
                            <div class="font-semibold text-slate-800 text-lg">
                                {{ $user->ipk ?? 'Tidak tersedia' }}
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-100 transition-all hover:shadow-md hover:border-emerald-200">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center">
                                <i class="fas fa-check-circle text-emerald-500 mr-2"></i>Status
                            </label>
                            <div class="mt-1">
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 font-bold rounded-full text-sm inline-block shadow-sm">Aktif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    <!-- Proposal History -->
    <x-ui.card className="mb-8 overflow-hidden">
        <div class="bg-gradient-to-br from-navy-600 to-navy-800 text-white p-5">
            <h5 class="font-bold text-lg m-0 flex items-center">
                <i class="fas fa-file-alt mr-3 text-navy-200"></i>Riwayat Proposal PKM
            </h5>
        </div>
        <div class="p-0">
            @php
                // Ambil proposal berdasarkan team_id atau sebagai anggota tim
                $proposals = \App\Models\Proposal::where(function($query) use ($user) {
                    // Proposal yang dibuat oleh mahasiswa ini
                    $query->where('id_mahasiswa', $user->id_mahasiswa)
                          // ATAU proposal di mana mahasiswa ini terdaftar sebagai anggota tim
                          ->orWhereRaw("EXISTS (SELECT 1 FROM users WHERE users.role = 'mahasiswa' AND users.metadata->>'team_id' = proposals.team_id::text AND users.identifier = ?)", [$user->identifier ?? $user->nim]);
                })
                ->with(['mahasiswa', 'dosen', 'dokumen'])
                ->orderBy('tanggal_pengajuan', 'desc')
                ->get();
                
                $allProposals = collect();
                
                foreach ($proposals as $proposal) {
                    $role = 'Anggota Tim';
                    $roleClass = 'slate';
                    
                    // Cek apakah user adalah ketua tim berdasarkan data di proposal
                    if ($proposal->ketua_nim === $user->nim) {
                        $role = 'Ketua Tim';
                        $roleClass = 'navy';
                    }
                    // Atau cek dari relasi semuaAnggotaTim
                    elseif ($proposal->semuaAnggotaTim->where('nim', $user->nim)->where('is_ketua', true)->count() > 0) {
                        $role = 'Ketua Tim';
                        $roleClass = 'navy';
                    }
                    
                    $allProposals->push([
                        'proposal' => $proposal,
                        'role' => $role,
                        'role_class' => $roleClass
                            ]);
                        }
            @endphp
            
            @if($allProposals->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold text-sm tracking-wider uppercase">
                                <th class="p-4 py-3">Tahun Ajaran</th>
                                <th class="p-4 py-3">Judul Proposal</th>
                                <th class="p-4 py-3">Peran</th>
                                <th class="p-4 py-3">Status</th>
                                <th class="p-4 py-3">Tanggal Pengajuan</th>
                                <th class="p-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($allProposals as $item)
                                @php
                                    $proposal = $item['proposal'];
                                    $role = $item['role'];
                                    $roleClass = $item['role_class'];
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-4 py-3 text-slate-700 font-medium">{{ $proposal->tahun_ajaran }}</td>
                                    <td class="p-4 py-3">
                                        <div class="text-slate-800 font-medium max-w-xs truncate" title="{{ $proposal->judul }}">
                                            {{ \Illuminate\Support\Str::limit($proposal->judul, 50) }}
                                        </div>
                                    </td>
                                    <td class="p-4 py-3">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $roleClass === 'navy' ? 'bg-navy-100 text-navy-700' : 'bg-slate-100 text-slate-700' }}">
                                            {{ $role }}
                                        </span>
                                    </td>
                                    <td class="p-4 py-3">
                                        @switch($proposal->status_validasi)
                                            @case('pending')
                                                <span class="px-2.5 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">Menunggu Validasi</span>
                                                @break
                                            @case('valid')
                                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">Valid</span>
                                                @break
                                            @case('invalid')
                                                <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Tidak Valid</span>
                                                @break
                                            @default
                                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold">Draft</span>
                                        @endswitch
                                    </td>
                                    <td class="p-4 py-3 text-slate-500 text-sm font-medium">{{ \Carbon\Carbon::parse($proposal->tanggal_pengajuan)->format('d/m/Y') }}</td>
                                    <td class="p-4 py-3 text-center">
                                        <a href="{{ route('mahasiswa.proposal.show', $proposal->id_proposal) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-white border border-navy-200 text-navy-700 rounded hover:bg-navy-50 transition-colors text-sm font-medium shadow-sm">
                                            <i class="fas fa-eye mr-2"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-16 px-4">
                    <div class="text-slate-300 text-6xl mb-4">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h5 class="text-slate-700 font-bold text-xl mb-2">Belum Ada Proposal</h5>
                    <p class="text-slate-500 mb-6">Anda belum pernah mengajukan proposal PKM.</p>
                    <a href="{{ route('mahasiswa.proposal.create') }}" class="inline-flex items-center px-6 py-2.5 bg-navy-600 text-white font-bold rounded-lg hover:bg-navy-700 transition-colors shadow-md">
                        <i class="fas fa-plus mr-2"></i>Ajukan Proposal Pertama
                    </a>
                </div>
            @endif
        </div>
    </x-ui.card>
</div>


@endsection