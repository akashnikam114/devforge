<aside class="w-64 bg-white border-r border-slate-200 min-h-screen p-5">
    <div class="font-extrabold text-lg">{{ config('app-ui.app_name', config('app.name')) }}</div>
    <nav class="mt-8 space-y-2">
        <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Dashboard</a>
    </nav>
</aside>
