<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pengajuan Proposal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        html, body { height: 100%; }
        
        /* Ensure main container fills viewport */
        .main-container {
            height: 100vh;
            min-height: 100vh;
            display: flex;
        }
        
        /* Left side background with image */
        .left-side {
            background-image: url('{{ asset('Backgroundlogin.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            min-height: 100vh;
            height: 100vh;
        }
        
        /* Dark overlay for better text readability */
        .left-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            height: 100vh;
            background: linear-gradient(135deg, rgba(20, 30, 48, 0.95) 0%, rgba(36, 59, 85, 0.9) 100%);
            z-index: 1;
        }
        
        .left-side-content {
            position: relative;
            z-index: 2;
        }
        
        /* Right side - clean white */
        .right-side {
            background: #ffffff;
            min-height: 100vh;
            height: 100vh;
        }
        
        /* Form animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-container {
            animation: fadeInUp 0.6s ease-out;
        }
        
        /* Input focus effects */
        .input-field:focus {
            outline: none;
            border-color: #1a1a1a;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
        }
        
        /* Button hover effect */
        .btn-login {
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        
        .btn-google {
            transition: all 0.3s ease;
        }
        
        .btn-google:hover {
            background-color: #f8f9fa;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        /* Logo animation */
        .logo-container {
            animation: fadeInUp 0.8s ease-out;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex main-container h-full">
        <!-- Left Side - Branding Panel -->
        <div class="hidden lg:flex lg:w-1/2 left-side">
            <!-- Content -->
            <div class="flex flex-col justify-center px-8 py-8 text-white left-side-content w-full">
                <!-- Logo & Title -->
                <div class="logo-container mb-6">
                    <div class="flex items-center mb-4">
                        <img src="{{ asset('UNUDLOGO.png') }}" alt="UNUD Logo" class="w-10 h-10 object-contain mr-3">
                        <span class="text-lg font-bold">Universitas Udayana</span>
                    </div>
                    <h1 class="text-3xl font-bold mb-4 leading-tight">
                        Kelola Proposal Lebih Cepat.<br>
                        Ekspor Lebih Mudah.<br>
                        Buat Dimana Saja.
                    </h1>
                    <p class="text-sm text-gray-300 leading-relaxed max-w-md">
                        Dari pengajuan proposal hingga penilaian akhir, platform kami membantu Anda mengelola seluruh proses dengan mudah dan efisien.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-4 py-6 right-side">
            <div class="w-full max-w-sm form-container">
                <!-- Welcome Text -->
                <div class="mb-4">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1">Selamat Datang!</h2>
                    <p class="text-xs text-gray-600">Masuk untuk mulai membuat proposal yang menakjubkan dengan mudah.</p>
                </div>

                <!-- Error Messages -->
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

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" x-data="{ showPassword: false, rememberMe: false }">
                            @csrf

                    <!-- Email Field -->
                    <div class="mb-2">
                        <label for="email" class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="input-field block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 transition-all duration-200"
                               placeholder="masukan email anda"
                               required>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-2">
                        <label for="password" class="block text-xs font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'"
                                   id="password" 
                                   name="password"
                                   class="input-field block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 transition-all duration-200"
                                   placeholder="masukan password anda"
                                   required>
                                    <button type="button" 
                                            @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                    <!-- Login Button -->
                    <button type="submit" 
                            class="btn-login w-full bg-gray-900 text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-2 mb-3">
                        Log In
                    </button>
                        </form>

                <!-- Request Credentials Section -->
                <div class="mt-3 text-center">
                    <p class="text-xs text-gray-600 mb-1">Butuh kredensial login?</p>
                    <a href="{{ route('register') }}" 
                       class="btn-login inline-flex items-center justify-center w-full px-3 py-2 bg-gray-700 text-white rounded-lg text-sm font-medium hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-700 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        Cari Akun
                    </a>
                </div>
                            
                <!-- Forgot Password Section -->
                <div class="mt-2 text-center">
                    <p class="text-xs text-gray-600 mb-1">Lupa password Anda?</p>
                    <a href="{{ route('password.forgot') }}" 
                       class="btn-login inline-flex items-center justify-center w-full px-3 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:ring-offset-2 transition-all duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        Reset Password
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
