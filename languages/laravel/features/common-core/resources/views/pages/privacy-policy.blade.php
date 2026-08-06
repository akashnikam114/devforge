<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy | {{ $appSetting::getBusinessInfo('app_name') }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/admin/img/favicons/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('assets/admin/img/favicons/favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .dynamic-content h1 { font-size: 2rem; font-weight: 700; margin-bottom: 1rem; color: #0f172a; }
        .dynamic-content h2 { font-size: 1.5rem; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem; color: #0f172a; }
        .dynamic-content h3 { font-size: 1.25rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.75rem; color: #0f172a; }
        .dynamic-content p { margin-bottom: 1.25rem; line-height: 1.75; color: #475569; }
        .dynamic-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.25rem; color: #475569; }
        .dynamic-content ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1.25rem; color: #475569; }
        .dynamic-content li { margin-bottom: 0.5rem; }
        .dynamic-content strong { color: #1e293b; font-weight: 600; }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 leading-normal">
    <div class="relative py-16 sm:py-24 overflow-hidden">
        <div
            class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-full bg-gradient-to-r from-teal-50/50 via-sky-50/50 to-indigo-50/50 blur-3xl -z-10">
        </div>
        <div class="max-w-7xl mx-auto px-6 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-100 mb-6">
                <span class="relative flex h-2 w-2">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                </span>
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-teal-700">Legal Document</span>
            </div>

            <h1 class="text-5xl md:text-7xl font-black tracking-tight text-slate-900 mb-6">
                Privacy <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-sky-600">Policy</span>
            </h1>

            <p class="text-slate-500 text-lg font-medium max-w-xl mx-auto leading-relaxed">
                Your trust is our priority. Below you’ll find a clear breakdown of how we protect your data at <span
                    class="text-slate-900 font-bold">{{ $appSetting::getBusinessInfo('app_name') }}.</span>
            </p>

            <div class="mt-10 flex justify-center items-center gap-4">
                <div class="h-[1px] w-12 bg-slate-200"></div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-widest">Effective {{ date('M Y') }}</span>
                <div class="h-[1px] w-12 bg-slate-200"></div>
            </div>
        </div>
    </div>

    <main class="max-w-4xl mx-auto px-6 pb-24">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 md:p-12">
            <div class="dynamic-content">
                {!! $appSetting::getBusinessInfo('privacy_policy') !!}
            </div>
        </div>

        <div class="mt-8 text-center">
            <p class="text-slate-400 text-sm">
                © {{ date('Y') }} {{ $appSetting::getBusinessInfo('app_name') }}. All rights reserved.
            </p>
        </div>
    </main>
</body>
</html>
