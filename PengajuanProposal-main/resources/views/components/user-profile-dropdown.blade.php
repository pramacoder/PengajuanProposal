@props([
    'headerTitle' => 'Profil Pengguna',
    'menuClass' => 'dropdown-menu dropdown-menu-end',
    'menuStyle' => 'min-width: 280px;',
    'menuItemClass' => 'dropdown-item',
    'infoTextClass' => 'text-muted small',
    'logoutFormId' => 'logout-form',
])

@php
    $userInfo = \App\Helpers\UserHelper::getUserProfileInfo();
    $userIcon = \App\Helpers\UserHelper::getUserIcon();
    $currentRole = auth()->check() ? auth()->user()->role : null;
    $profileRoute = '#';

    if ($currentRole) {
        $profileRoute = match ($currentRole) {
            'mahasiswa' => route('mahasiswa.profile'),
            'dosen' => route('dosen.profile'),
            'reviewer' => route('reviewer.profile'),
            'operator', 'pimpinan_pt' => route('operator.profile'),
            default => '#',
        };
    }

    $linkProfile = $profileRoute !== '#' ? $profileRoute : '#';
    $linkProfileSection = $profileRoute !== '#' ? $profileRoute . '#profile-section' : '#';
    $linkSecuritySection = $profileRoute !== '#' ? $profileRoute . '#security-section' : '#';
@endphp

<div class="{{ $menuClass }}" style="{{ $menuStyle }}">
    <div class="dropdown-header">
        <i class="fas fa-user-circle me-2"></i>{{ $headerTitle }}
    </div>

    <div class="px-3 py-3">
        <div class="d-flex align-items-center">
            <div class="bg-primary rounded-circle p-3 me-3">
                <i class="{{ $userIcon }} text-white"></i>
            </div>
            <div>
                <div class="fw-bold">{{ $userInfo['name'] }}</div>
                <div class="{{ $infoTextClass }}">{{ $userInfo['role'] }}</div>
                @if($userInfo['email'])
                    <div class="{{ $infoTextClass }}">{{ $userInfo['email'] }}</div>
                @endif
                @if($userInfo['phone'])
                    <div class="{{ $infoTextClass }}">{{ $userInfo['phone'] }}</div>
                @endif
                @foreach($userInfo['additional_info'] as $label => $value)
                    @if($value)
                        <div class="{{ $infoTextClass }}">{{ $label }}: {{ $value }}</div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="dropdown-divider"></div>

    <a class="{{ $menuItemClass }}" href="{{ $linkProfile }}">
        <i class="fas fa-user me-2"></i>Detail Profil
    </a>
    <a class="{{ $menuItemClass }}" href="{{ $linkProfileSection }}">
        <i class="fas fa-edit me-2"></i>Edit Profil
    </a>
    <a class="{{ $menuItemClass }}" href="{{ $linkProfileSection }}">
        <i class="fas fa-cog me-2"></i>Pengaturan
    </a>
    <a class="{{ $menuItemClass }}" href="{{ $linkSecuritySection }}">
        <i class="fas fa-key me-2"></i>Ubah Password
    </a>

    <div class="dropdown-divider"></div>

    <a class="{{ $menuItemClass }} text-danger" href="#" onclick="event.preventDefault(); document.getElementById('{{ $logoutFormId }}').submit();">
        <i class="fas fa-sign-out-alt me-2"></i>Logout
    </a>
    <form id="{{ $logoutFormId }}" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</div>
