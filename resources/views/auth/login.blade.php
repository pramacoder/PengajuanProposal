<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pengajuan Proposal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    <style>
        body {
            background: linear-gradient(135deg, 
                #420e0e 0%, 
                #7a1f1f 15%, 
                #942424 30%, 
                #b32b2b 45%, 
                #d13e3e 60%, 
                #b32b2b 75%, 
                #942424 90%, 
                #7a1f1f 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        
        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        /* Overlay untuk efek yang lebih halus */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 80%, rgba(255,255,255,0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(255,255,255,0.05) 0%, transparent 50%),
                        radial-gradient(circle at 40% 40%, rgba(255,255,255,0.03) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }
        
        .min-h-screen {
            position: relative;
            z-index: 1;
        }
        
        /* Login Card Pop Up Effects */
        .login-card {
            animation: slideInUp 0.8s ease-out;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .login-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.3), 
                        0 0 0 1px rgba(255, 255, 255, 0.2), 
                        0 0 0 0 rgba(209, 62, 62, 0.2),
                        0 0 50px rgba(209, 62, 62, 0.1);
        }
        
        /* Login card animation */
        .login-card {
            animation: slideInUp 0.8s ease-out;
        }
        
        /* Glow effect */
        .login-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, 
                rgba(209, 62, 62, 0.3), 
                rgba(255, 255, 255, 0.1), 
                rgba(209, 62, 62, 0.3));
            border-radius: 24px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .login-card:hover::before {
            opacity: 1;
        }
        
        /* Pulse effect */
        .login-card {
            position: relative;
        }
        
        .login-card::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(209, 62, 62, 0.1) 0%, transparent 70%);
            border-radius: 24px;
            transform: translate(-50%, -50%) scale(0);
            z-index: -1;
            animation: pulse 4s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: translate(-50%, -50%) scale(0);
                opacity: 1;
            }
            50% {
                transform: translate(-50%, -50%) scale(1.2);
                opacity: 0.3;
            }
            100% {
                transform: translate(-50%, -50%) scale(1.5);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="min-h-screen flex">
        <!-- Left Side - Information Panel -->
        <div class="hidden lg:flex lg:w-1/2">
            
            <!-- Content -->
            <div class="flex flex-col justify-center px-12 py-16 text-white">
                <!-- Logo & Title -->
                <div class="mb-12">
                    <div class="inline-flex items-center justify-center mb-8">
                        <img src="{{ asset('UNUDLOGO.png') }}" alt="UNUD Logo" class="w-30 h-30 object-contain">
                    </div>
                    <h1 class="text-5xl font-bold mb-6 leading-tight">Sistem Pengajuan<br>Proposal PKM</h1>
                    <p class="text-xl text-maroon-100 leading-relaxed">Platform terintegrasi untuk pengelolaan proposal Program Kreativitas Mahasiswa Universitas Udayana</p>
                </div>

                <!-- Features -->
                <div class="space-y-6 mb-12">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Proses Terintegrasi</h3>
                            <p class="text-maroon-100">Dari pengajuan hingga penilaian dalam satu platform</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Multi-Role Access</h3>
                            <p class="text-maroon-100">Akses khusus untuk Mahasiswa, Dosen, Reviewer, dan Operator</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-white/20 rounded-full flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Review Terstruktur</h3>
                            <p class="text-maroon-100">Sistem review administratif dan substantif yang komprehensif</p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-white/20 pt-6">
                    <p class="text-maroon-100 text-sm">© 2024 Universitas Udayana. All rights reserved.</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-8 py-12">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center justify-center mb-4">
                        <img src="{{ asset('UNUDLOGO.png') }}" alt="UNUD Logo" class="w-14 h-14 object-contain">
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2">Sistem Pengajuan Proposal</h1>
                    <p class="text-maroon-100">Universitas Udayana</p>
                </div>

                <!-- Login Card -->
                <div class="login-card bg-white rounded-2xl shadow-2xl overflow-hidden transform hover:scale-105 transition-all duration-300 hover:shadow-3xl" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1), 0 0 0 0 rgba(209, 62, 62, 0.1);">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-maroon-600 to-maroon-700 px-8 py-8 text-center">
                        <div class="inline-flex items-center justify-center mb-4">
                            <img src="{{ asset('UNUDLOGO.png') }}" alt="UNUD Logo" class="w-14 h-14 object-contain">
                        </div>
                        <h2 class="text-2xl font-bold text-white mb-2">Selamat Datang Kembali</h2>
                        <p class="text-maroon-100">Silakan login untuk mengakses sistem</p>
                    </div>

                    <!-- Login Form -->
                    <div class="px-8 pb-8">
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

                        @if (session('success'))
                            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-green-400 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="text-sm text-green-700">{{ session('success') }}</div>
                                </div>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" x-data="{ showPassword: false }">
                            @csrf

                            <!-- Email Field -->
                            <div class="mb-6">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                        </svg>
                                    </div>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                           placeholder="Masukkan email Anda"
                                           required>
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div class="mb-6">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                        </svg>
                                    </div>
                                    <input :type="showPassword ? 'text' : 'password'"
                                           id="password" 
                                           name="password"
                                           class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                           placeholder="Masukkan password Anda"
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

                            <!-- Login Button -->
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-maroon-600 to-maroon-700 text-white py-3 px-4 rounded-lg font-medium hover:from-maroon-700 hover:to-maroon-800 focus:ring-4 focus:ring-maroon-200 transition-all duration-200 transform hover:scale-[1.02]">
                                <span class="flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    Masuk ke Sistem
                                </span>
                            </button>
                        </form>

                        <!-- Forgot Password Link -->
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600 mb-3">Lupa password Anda?</p>
                            <a href="{{ route('password.forgot') }}" 
                               class="inline-flex items-center px-4 py-2 border border-maroon-300 text-maroon-700 bg-white rounded-lg hover:bg-maroon-50 hover:border-maroon-400 focus:ring-2 focus:ring-maroon-200 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                Reset Password
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>