<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Access Forbidden</title>
    <link rel="shortcut icon" href="{{ asset('assets/admin/img/favicons/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('assets/admin/img/favicons/favicon.ico') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Nunito', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex flex-col items-center justify-center min-h-screen p-4">
    <div class="w-full max-w-lg bg-white rounded-[1.5rem] md:rounded-[2rem] p-8 md:p-12 shadow-xl shadow-slate-200 text-center border-t-4 border-red-600">
        
        <div class="inline-block p-4 bg-red-50 rounded-2xl mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 md:w-12 md:h-12" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>

        <h1 class="text-4xl md:text-5xl font-black text-red-600 tracking-tight mb-1">403</h1>
        <h2 class="text-xl md:text-2xl font-bold text-slate-800 tracking-tight mb-3">Access Forbidden</h2>
        
        <p class="text-slate-500 text-base md:text-lg mb-8 max-w-sm mx-auto leading-relaxed">
            You don't have permission to access this resource.
        </p>
        
        <div class="flex flex-col items-center gap-5">
            <a href="mailto:{{ $appSetting::getBusinessInfo('app_email') }}" class="inline-block w-full sm:w-auto px-8 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition-all shadow-md shadow-red-200 active:scale-95 text-sm">
                Contact Support
            </a>
            
            <p class="text-xs md:text-sm text-slate-400">
                If you believe this is an error, please contact our 
                <a href="mailto:{{ $appSetting::getBusinessInfo('app_email') }}" class="text-red-600 font-bold hover:underline transition-all">Support Team</a>.
            </p>
        </div>
    </div>

    <footer class="mt-6 text-slate-400 text-xs font-semibold tracking-wide">
        © {{ date('Y') }} {{ $appSetting::getBusinessInfo('app_name') }}. All rights reserved.
    </footer>
</body>
</html>