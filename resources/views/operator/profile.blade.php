@extends('operator.layout')

@section('title', 'Profil Operator')

@section('content')
<div class="container-fluid">
    <x-breadcrumb :items="[
        ['label' => 'Beranda', 'url' => route('operator.dashboard')],
        ['label' => 'Profil', 'active' => true],
    ]" />

    <x-page-header title="Profil Operator" subtitle="UNIVERSITAS UDAYANA" description="Kelola data akun operator/pimpinan PT." />

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <x-ui-card title="Informasi Akun & Keamanan" icon="fa-user-cog">
                <form action="{{ route('operator.profile.update') }}" method="POST" id="operatorProfileForm">
                    <div id="profile-section"></div>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                class="form-control @error('name') is-invalid @enderror" required maxlength="255">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" value="{{ $user->email }}" class="form-control" disabled>
                            <small class="text-muted">Email login tidak dapat diubah dari halaman ini.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" value="{{ ucfirst(str_replace('_', ' ', $user->role)) }}" class="form-control" disabled>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="form-control @error('phone') is-invalid @enderror" required maxlength="15">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3" id="security-section"><i class="fas fa-key me-2"></i>Ubah Password (Opsional)</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="current_password" class="form-label">Password Saat Ini</label>
                            <input type="password" id="current_password" name="current_password"
                                class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" id="new_password" name="new_password"
                                class="form-control @error('new_password') is-invalid @enderror">
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary" id="btnSubmitOperatorProfile">
                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('operator.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </form>
            </x-ui-card>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('operatorProfileForm')?.addEventListener('submit', function () {
        const submitBtn = document.getElementById('btnSubmitOperatorProfile');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
    });
</script>
@endsection
