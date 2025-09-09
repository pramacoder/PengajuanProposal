<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Pengajuan Proposal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: {
                            50: '#fdf2f2',
                            100: '#fce7e7',
                            200: '#f9d5d5',
                            300: '#f4b3b3',
                            400: '#ed8a8a',
                            500: '#e25c5c',
                            600: '#d13e3e',
                            700: '#b32b2b',
                            800: '#942424',
                            900: '#7a1f1f',
                            950: '#420e0e',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-maroon-600 rounded-full mb-6 shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Registrasi Akun Baru</h1>
                <p class="text-xl text-gray-600">Lengkapi data diri Anda untuk membuat akun baru</p>
            </div>

            <!-- Register Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Role Header -->
                <div class="bg-gradient-to-r from-maroon-600 to-maroon-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white" x-text="getRoleDisplay()">Registrasi</h2>
                                <p class="text-maroon-100">Silakan isi data diri Anda</p>
                            </div>
                        </div>
                        <div class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium">
                            <span x-text="getRoleDisplay()"></span>
                        </div>
                    </div>
                </div>

                <!-- Register Form -->
                <div class="px-8 py-8">
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex">
                                <svg class="w-5 h-5 text-red-400 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc pl-5 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}" x-data="registerForm()" x-init="showPassword = false; showConfirmPassword = false;" @submit="console.log('Form submit event triggered')">
                        @csrf
                        <input type="hidden" name="user_type" x-bind:value="userType">
                        
                        <!-- Personal Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-maroon-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Informasi Pribadi
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap *</label>
                                    <input type="text" 
                                           id="nama" 
                                           name="nama" 
                                           value="{{ old('nama') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                           placeholder="Masukkan nama lengkap"
                                           required>
                                </div>
                                
                                <div x-show="userType === 'mahasiswa'">
                                    <label for="nim" class="block text-sm font-medium text-gray-700 mb-2">NIM *</label>
                                    <input type="text" 
                                           id="nim" 
                                           name="nim" 
                                           value="{{ old('nim') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                           placeholder="Masukkan NIM"
                                           x-bind:required="userType === 'mahasiswa'">
                                </div>

                                <div x-show="userType === 'dosen'">
                                    <label for="nuptk" class="block text-sm font-medium text-gray-700 mb-2">NUPTK *</label>
                                    <input type="text" 
                                           id="nuptk" 
                                           name="nuptk" 
                                           value="{{ old('nuptk') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                           placeholder="Masukkan NUPTK"
                                           x-bind:required="userType === 'dosen'">
                                </div>

                                <div x-show="userType === 'reviewer'">
                                    <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">NIP *</label>
                                    <input type="text" 
                                           id="nip" 
                                           name="nip" 
                                           value="{{ old('nip') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                           placeholder="Masukkan NIP"
                                           x-bind:required="userType === 'reviewer'">
                                </div>

                                <div x-show="userType === 'operator'">
                                    <label for="nip" class="block text-sm font-medium text-gray-700 mb-2">NIP *</label>
                                    <input type="text" 
                                           id="nip" 
                                           name="nip" 
                                           value="{{ old('nip') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                           placeholder="Masukkan NIP"
                                           x-bind:required="userType === 'operator'">
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-maroon-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Informasi Kontak
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                           placeholder="Masukkan email"
                                           required>
                                </div>
                                
                                <div>
                                    <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">No. HP *</label>
                                    <input type="text" 
                                           id="no_hp" 
                                           name="no_hp" 
                                           value="{{ old('no_hp') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                           placeholder="Masukkan nomor HP"
                                           required>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information - Only for Mahasiswa -->
                        <div class="mb-8" x-show="userType === 'mahasiswa'">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-maroon-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Informasi Akademik
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="fakultas" class="block text-sm font-medium text-gray-700 mb-2">Fakultas *</label>
                                    <select id="fakultas" 
                                            name="fakultas" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                            x-bind:required="userType === 'mahasiswa'">
                                        <option value="">Pilih Fakultas</option>
                                        @foreach($fakultas as $f)
                                            <option value="{{ $f->id_fakultas }}" {{ old('fakultas') == $f->id_fakultas ? 'selected' : '' }}>
                                                {{ $f->nama_fakultas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="prodi" class="block text-sm font-medium text-gray-700 mb-2">Program Studi *</label>
                                    <select id="prodi" 
                                            name="prodi" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                            x-bind:required="userType === 'mahasiswa'">
                                        <option value="">Pilih Program Studi</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Security Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 text-maroon-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Keamanan Akun
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                                    <div class="relative">
                                        <input :type="showPassword ? 'text' : 'password'"
                                               id="password" 
                                               name="password"
                                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                               placeholder="Masukkan password"
                                               required>
                                        <button type="button" 
                                                @click="showPassword = !showPassword"
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <svg x-show="!showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <svg x-show="showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password *</label>
                                    <div class="relative">
                                        <input :type="showConfirmPassword ? 'text' : 'password'"
                                               id="password_confirmation" 
                                               name="password_confirmation"
                                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                               placeholder="Konfirmasi password"
                                               required>
                                        <button type="button" 
                                                @click="showConfirmPassword = !showConfirmPassword"
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <svg x-show="!showConfirmPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            <svg x-show="showConfirmPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between">
                            <button type="submit" 
                                    class="bg-gradient-to-r from-maroon-600 to-maroon-700 text-white py-3 px-8 rounded-lg font-medium hover:from-maroon-700 hover:to-maroon-800 focus:ring-4 focus:ring-maroon-200 transition-all duration-200 transform hover:scale-[1.02]"
                                    @click="console.log('Form submitted', {userType: userType})">
                                <span class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    Daftar Sekarang
                                </span>
                            </button>
                            
                            <a href="#" 
                               @click.prevent="goToLogin()"
                               class="text-maroon-600 hover:text-maroon-700 font-medium transition-colors duration-200">
                                Sudah punya akun? Login di sini
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Back to Get Started -->
            <div class="text-center mt-6">
                <a href="{{ route('getstarted') }}" 
                   class="inline-flex items-center text-gray-500 hover:text-maroon-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke pilihan role
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('registerForm', () => ({
                userType: 'mahasiswa',
                
                init() {
                    // Ambil user_type dari query string
                    const urlParams = new URLSearchParams(window.location.search);
                    const userTypeParam = urlParams.get('user_type');
                    if (userTypeParam) {
                        this.userType = userTypeParam;
                    }
                },

                getRoleDisplay() {
                    const roleMap = {
                        'mahasiswa': 'Mahasiswa',
                        'dosen': 'Dosen',
                        'reviewer': 'Reviewer',
                        'operator': 'Operator'
                    };
                    return roleMap[this.userType] || 'Pengguna';
                },

                goToLogin() {
                    window.location.href = '/login?user_type=' + this.userType;
                }
            }));
        });

        $(document).ready(function() {
            // Ketika fakultas dipilih
            $('#fakultas').change(function() {
                var id_fakultas = $(this).val();
                var prodiSelect = $('#prodi');
                
                // Reset prodi dropdown
                prodiSelect.html('<option value="">Pilih Program Studi</option>');
                
                if (id_fakultas) {
                    // Ambil data prodi berdasarkan fakultas
                    $.ajax({
                        url: '/get-prodi/' + id_fakultas,
                        type: 'GET',
                        success: function(data) {
                            $.each(data, function(key, value) {
                                prodiSelect.append('<option value="' + value.id_prodi + '">' + value.nama_prodi + '</option>');
                            });
                        },
                        error: function() {
                            alert('Terjadi kesalahan saat mengambil data program studi.');
                        }
                    });
                }
            });

            // Jika ada old value untuk fakultas, trigger change event
            @if(old('fakultas'))
                $('#fakultas').trigger('change');
                // Set prodi value setelah dropdown terisi
                setTimeout(function() {
                    $('#prodi').val('{{ old("prodi") }}');
                }, 500);
            @endif
        });
    </script>
</body>
</html> 