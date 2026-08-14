<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | {{ config('app-ui.app_name', '__PROJECT_NAME__') }}</title>
    <link rel="manifest" href="{{ asset('pwa/manifest.json') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --admin-primary: {{ config('app-ui.primary_color', '#049C9C') }};
            --admin-secondary: {{ config('app-ui.secondary_color', '#037a7a') }};
        }
        body {
            font-family: Nunito, ui-sans-serif, system-ui, sans-serif;
        }
        .bg-app { background-color: var(--admin-primary); }
        .theme-focus:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--admin-primary) 12%, transparent);
        }
        .text-app { color: var(--admin-primary); }
        .login-gradient {
            background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%);
        }
        .login-visual-card {
            border: 1px solid rgba(255, 255, 255, 0.22);
            box-shadow: 0 24px 70px rgba(0, 0, 0, 0.16);
        }
    </style>
</head>
<body class="bg-white">
    <div class="flex min-h-screen">
        <div class="flex items-center justify-center w-full lg:w-[45%] px-6 sm:px-12">
            <div class="w-full max-w-sm">
                <div class="mb-10">
                    <div class="mb-6">
                        <img src="{{ asset('assets/admin/images/app-logo.png') }}" alt="{{ config('app-ui.app_name', '__PROJECT_NAME__') }} Logo" class="h-14 w-auto">
                    </div>
                    <h1 class="text-3xl font-extrabold text-gray-900">{{ config('app-ui.app_name', '__PROJECT_NAME__') }}</h1>
                    <p class="mt-2 text-gray-500 font-medium">Enter your admin credentials to access the management panel.</p>
                </div>

                <form method="POST" action="{{ route('admin.login') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-3.5 border border-gray-200 rounded-lg bg-gray-50 focus:bg-white theme-focus outline-none" placeholder="admin@example.com">
                        @error('email')<p class="mt-1.5 text-xs text-red-500 font-bold">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Password</label>
                        <input type="password" name="password" class="w-full px-4 py-3.5 border border-gray-200 rounded-lg bg-gray-50 focus:bg-white theme-focus outline-none" placeholder="Password">
                        @error('password')<p class="mt-1.5 text-xs text-red-500 font-bold">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="w-full py-4 bg-app text-white font-bold rounded-lg shadow hover:opacity-95 transition">Sign In to Panel</button>
                </form>
            </div>
        </div>

        <div class="hidden lg:flex w-[55%] login-gradient relative items-center justify-center overflow-hidden px-10 xl:px-16">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-black/5 rounded-full -ml-20 -mb-20"></div>

            <div class="relative z-10 w-full max-w-3xl text-center">
                <div class="login-visual-card rounded-2xl bg-white/15 p-5 xl:p-6 ring-1 ring-white/20 backdrop-blur-sm">
                    <img src="{{ asset('assets/admin/images/admin-login-illustration.png') }}" alt="Admin dashboard illustration" class="mx-auto w-full max-w-2xl rounded-xl">
                </div>
                <div class="max-w-xl mx-auto mt-8 text-white">
                    <h2 class="text-3xl font-extrabold mb-3">{{ config('app-ui.panel_title', 'Admin Panel') }}</h2>
                    <p class="text-white/72 text-base font-medium leading-relaxed">{{ config('app-ui.panel_description', 'Manage users, settings, reports, and application operations from one place.') }}</p>
                </div>
            </div>

            <div class="absolute bottom-0 w-full leading-[0]">
                <svg viewBox="0 0 1440 320" xmlns="http://www.w3.org/2000/svg" class="fill-white/5">
                    <path d="M0,160L48,176C96,192,192,224,288,213.3C384,203,480,149,576,149.3C672,149,768,203,864,202.7C960,203,1056,149,1152,117.3C1248,85,1344,75,1392,69.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                </svg>
            </div>
        </div>
    </div>
</body>
</html>
