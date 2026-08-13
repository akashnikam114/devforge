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
        body { font-family: Nunito, ui-sans-serif, system-ui, sans-serif; }
        .bg-app { background-color: var(--admin-primary); }
        .theme-focus:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--admin-primary) 12%, transparent);
        }
        .login-gradient { background: linear-gradient(135deg, var(--admin-primary) 0%, var(--admin-secondary) 100%); }
    </style>
</head>
<body class="bg-white">
    <div class="flex min-h-screen">
        <div class="flex items-center justify-center w-full lg:w-[45%] px-6 sm:px-12">
            <div class="w-full max-w-sm">
                <div class="mb-10">
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

        <div class="hidden lg:flex w-[55%] login-gradient items-center justify-center px-16 text-white">
            <div class="max-w-lg">
                <h2 class="text-4xl font-extrabold mb-4">{{ config('app-ui.panel_title', 'Admin Panel') }}</h2>
                <p class="text-white/85 text-lg">{{ config('app-ui.panel_description', 'Manage users, settings, reports, and application operations from one place.') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
