<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="{{ $appSetting::getBusinessInfo('app_name') }} - Modern application management platform.">
    <title>Login | {{ $appSetting::getBusinessInfo('app_name') }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/admin/img/favicons/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('assets/admin/img/favicons/favicon.ico') }}" type="image/x-icon">
    <link rel="manifest" href="{{ asset('pwa/manifest.json') }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Nunito', sans-serif;
            overflow-x: hidden;
        }

        .bg-app {
            background-color: {{ config('devforge-ui.primary_color', '#049C9C') }};
        }

        .text-app {
            color: {{ config('devforge-ui.primary_color', '#049C9C') }};
        }

        .login-gradient {
            background: linear-gradient(135deg, {{ config('devforge-ui.primary_color', '#049C9C') }} 0%, {{ config('devforge-ui.secondary_color', '#037a7a') }} 100%);
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-12px); }
        }

        .animate-float {
            animation: float 5s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-white">
    <div class="flex min-h-screen">
        <div class="flex items-center justify-center w-full lg:w-[45%] px-6 sm:px-12 md:px-16 lg:px-10 xl:px-16">
            <div class="w-full max-w-sm">
                <div class="mb-10 text-center lg:text-left">
                    <div class="mb-6 flex justify-center lg:justify-start">
                        <img src="{{ asset('assets/admin/img/app_logo.png') }}" alt="{{ $appSetting::getBusinessInfo('app_name') }} Logo" class="h-14 w-auto">
                    </div>
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Welcome Back</h2>
                    <p class="mt-2 text-gray-500 font-medium">Please enter your admin credentials to securely access the management panel.</p>
                </div>

                <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('Email Address') }}</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#049C9C] transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                                </svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full pl-11 pr-4 py-3.5 border border-gray-200 rounded-xl bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-[#049C9C]/10 focus:border-[#049C9C] transition-all duration-200 outline-none placeholder:text-gray-400"
                                placeholder="e.g. abc@example.com">
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-500 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">{{ __('Password') }}</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#049C9C] transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password-field" type="password" name="password"
                                class="w-full pl-11 pr-12 py-3.5 border border-gray-200 rounded-xl bg-gray-50/50 focus:bg-white focus:ring-4 focus:ring-[#049C9C]/10 focus:border-[#049C9C] transition-all duration-200 outline-none placeholder:text-gray-400"
                                placeholder="••••••••">

                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-[#049C9C] transition-colors focus:outline-none">
                                <svg id="eye-icon" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg id="eye-off-icon" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-500 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full py-4 bg-app text-white font-bold rounded-xl shadow-lg shadow-app/30 hover:shadow-app/40 hover:-translate-y-0.5 active:scale-95 transition-all duration-200">
                        Sign In to Panel
                    </button>
                </form>

                <div class="mt-10 text-center lg:text-left">
                    <footer class="mt-6 text-slate-400 text-xs font-semibold tracking-wide">
                        © {{ date('Y') }} {{ $appSetting::getBusinessInfo('app_name') }}. All rights reserved.
                    </footer>
                </div>
            </div>
        </div>

        <div class="hidden lg:flex w-[55%] login-gradient relative items-center justify-center overflow-hidden">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-black/5 rounded-full -ml-20 -mb-20"></div>

            <div class="relative z-10 text-center px-12 xl:px-24">
                <div class="animate-float mb-6">
                    <img src="{{ asset('assets/admin/img/login_illustration.png') }}"
                        alt="{{ $appSetting::getBusinessInfo('app_name') }} admin illustration"
                        class="w-full max-w-md xl:max-w-lg mx-auto drop-shadow-2xl">
                </div>

                <div class="max-w-md mx-auto text-white">
                    <h2 class="text-3xl font-extrabold mb-3">Welcome to {{ $appSetting::getBusinessInfo('app_name') }}!</h2>
                    <h4 class="text-md font-extrabold mb-3">{{ config('devforge-ui.panel_title', 'Admin Panel') }}</h4>
                    <p class="text-white/80 text-sm md:text-base font-medium leading-relaxed">
                        {{ config('devforge-ui.panel_description', 'Securely manage users, settings, content, and application operations from one dashboard.') }}
                    </p>
                </div>
            </div>

            <div class="absolute bottom-0 w-full leading-[0]">
                <svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg" class="fill-white/5">
                    <path d="M0,160L48,176C96,192,192,224,288,213.3C384,203,480,149,576,149.3C672,149,768,203,864,202.7C960,203,1056,149,1152,117.3C1248,85,1344,75,1392,69.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                </svg>
            </div>
        </div>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register("{{ asset('pwa/service-worker.js') }}");
        }

        function togglePassword() {
            const passwordField = document.getElementById('password-field');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
