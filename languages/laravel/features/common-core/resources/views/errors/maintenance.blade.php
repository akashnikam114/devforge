<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode - {{ $appSetting::getBusinessInfo('app_name') }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/admin/images/favicons/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('assets/admin/images/favicons/favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Nunito', sans-serif; }
        :root {
            --app-primary: {{ config('app-ui.primary_color', '#049C9C') }};
            --app-secondary: {{ config('app-ui.secondary_color', '#037a7a') }};
        }
        .theme-border { border-color: var(--app-primary); }
        .theme-bg { background-color: var(--app-primary); }
        .theme-bg-soft { background: color-mix(in srgb, var(--app-primary) 8%, white); }
        .theme-text { color: var(--app-primary); }
        .theme-shadow { box-shadow: 0 4px 14px color-mix(in srgb, var(--app-primary) 20%, transparent); }
        .theme-stroke { color: var(--app-primary); }
        @keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        .animate-spin-slow { animation: spin-slow 12s linear infinite; }
    </style>
</head>
<body class="bg-slate-50 flex flex-col items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-lg bg-white rounded-[1.5rem] md:rounded-[2rem] p-8 md:p-12 shadow-xl shadow-slate-200 text-center border-t-4 theme-border">
        <div class="float-icon mb-6 inline-flex p-5 rounded-full bg-white shadow-sm border border-slate-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 theme-stroke" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
            </svg>
        </div>

        <h1 class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight mb-2">Scheduled Maintenance</h1>
        <p class="text-slate-500 text-base md:text-lg mb-8 max-w-sm mx-auto leading-relaxed">
            We're currently updating our system to serve you better. We'll be back online shortly.
        </p>

        <div class="grid grid-cols-3 gap-4 mb-10">
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 shadow-sm">
                <span id="hours" class="block text-2xl font-black theme-text">24</span>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Hours</span>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 shadow-sm">
                <span id="minutes" class="block text-2xl font-black theme-text">00</span>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Minutes</span>
            </div>
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 shadow-sm">
                <span id="seconds" class="block text-2xl font-black theme-text">00</span>
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Seconds</span>
            </div>
        </div>
        
        <div class="flex flex-col items-center gap-5">
            <a href="mailto:{{ $appSetting::getBusinessInfo('app_email') }}" class="inline-block w-full sm:w-auto px-8 py-3 theme-bg text-white font-bold rounded-xl hover:opacity-90 transition-all shadow-md theme-shadow active:scale-95 text-sm">
                Contact Support
            </a>
            
            <p class="text-xs md:text-sm text-slate-400">
                If you have an urgent inquiry, please contact our 
                <a href="mailto:{{ $appSetting::getBusinessInfo('app_email') }}" class="theme-text font-bold hover:underline transition-all">Support Team</a>.
            </p>
        </div>
    </div>

    <footer class="mt-6 text-slate-400 text-xs font-semibold tracking-wide text-center leading-loose">
        © {{ date('Y') }} {{ $appSetting::getBusinessInfo('app_name') }}. All rights reserved.
    </footer>

    <script>
        let duration = 24 * 60 * 60;
        const hDisplay = document.getElementById('hours');
        const mDisplay = document.getElementById('minutes');
        const sDisplay = document.getElementById('seconds');

        setInterval(() => {
            const h = Math.floor(duration / 3600);
            const m = Math.floor((duration % 3600) / 60);
            const s = Math.floor(duration % 60);

            hDisplay.textContent = h.toString().padStart(2, '0');
            mDisplay.textContent = m.toString().padStart(2, '0');
            sDisplay.textContent = s.toString().padStart(2, '0');

            if (duration > 0) duration--;
        }, 1000);
    </script>
</body>
</html>
