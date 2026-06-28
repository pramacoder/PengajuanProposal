<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Sistem Pengajuan Proposal</title>
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
        
        /* Reset Password Card Effects */
        .reset-card {
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
        
        .reset-card:hover {
            transform: translateY(-5px) scale(1.01);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 
                        0 0 0 1px rgba(255, 255, 255, 0.2), 
                        0 0 0 0 rgba(209, 62, 62, 0.2),
                        0 0 30px rgba(209, 62, 62, 0.1);
        }
        
        .reset-card {
            position: relative;
        }
        
        /* Glow effect */
        .reset-card::before {
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
        
        .reset-card:hover::before {
            opacity: 1;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <!-- Mobile Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center mb-4">
                    <img src="{{ asset('UNUDLOGO.png') }}" alt="UNUD Logo" class="w-16 h-16 object-contain">
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Reset Password</h1>
                <p class="text-maroon-100">Sistem Pengajuan Proposal PKM</p>
            </div>

            <!-- Reset Password Card -->
            <div class="reset-card bg-white rounded-2xl shadow-2xl overflow-hidden transform hover:scale-105 transition-all duration-300">
                <!-- Header -->
                <div class="bg-gradient-to-r from-maroon-600 to-maroon-700 px-8 py-8 text-center">
                    <div class="inline-flex items-center justify-center mb-4">
                        <img src="{{ asset('UNUDLOGO.png') }}" alt="UNUD Logo" class="w-12 h-12 object-contain">
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">Reset Password</h2>
                    <p class="text-maroon-100">Masukkan data untuk reset password</p>
                </div>

                <!-- Reset Form -->
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

                    <form method="POST" action="{{ route('password.reset') }}" x-data="{ isLoading: false }">
                        @csrf

                        <!-- Email Field -->
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Terdaftar</label>
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
                                       placeholder="Masukkan email yang terdaftar"
                                       required>
                            </div>
                        </div>

                        <!-- NIM Field -->
                        <div class="mb-6">
                            <label for="nim" class="block text-sm font-medium text-gray-700 mb-2">NIM</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                    </svg>
                                </div>
                                <input type="text" 
                                       id="nim" 
                                       name="nim" 
                                       value="{{ old('nim') }}"
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                       placeholder="Masukkan NIM Anda"
                                       required>
                            </div>
                        </div>

                        <!-- Gmail Field -->
                        <div class="mb-6">
                            <label for="gmail" class="block text-sm font-medium text-gray-700 mb-2">Gmail untuk Menerima Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input type="email" 
                                       id="gmail" 
                                       name="gmail" 
                                       value="{{ old('gmail') }}"
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-maroon-500 focus:border-maroon-500 transition-colors duration-200"
                                       placeholder="Masukkan Gmail untuk menerima password"
                                       required>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                x-bind:disabled="isLoading"
                                @click="isLoading = true"
                                class="w-full bg-gradient-to-r from-maroon-600 to-maroon-700 text-white py-3 px-4 rounded-lg font-medium hover:from-maroon-700 hover:to-maroon-800 focus:ring-4 focus:ring-maroon-200 transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="flex items-center justify-center" x-show="!isLoading">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                </svg>
                                Kirim Password ke Gmail
                            </span>
                            <span class="flex items-center justify-center" x-show="isLoading">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mengirim...
                            </span>
                        </button>
                    </form>

                    <!-- Back to Login Link -->
                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center text-sm text-gray-600 hover:text-maroon-600 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali ke Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
