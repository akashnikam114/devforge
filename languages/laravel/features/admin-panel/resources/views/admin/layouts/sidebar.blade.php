<aside class="w-64 bg-white border-r border-slate-200 min-h-screen p-5">
    <div class="font-extrabold text-lg">{{ config('app-ui.app_name', config('app.name')) }}</div>
    <nav class="mt-8 space-y-2">
        <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Dashboard</a>
        @if(\Illuminate\Support\Facades\Route::has('admin.users'))
            <a href="{{ route('admin.users') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Users</a>
        @endif
        @if(\Illuminate\Support\Facades\Route::has('admin.activity_logs'))
            <a href="{{ route('admin.activity_logs') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Activity Logs</a>
        @endif
        @if(\Illuminate\Support\Facades\Route::has('admin.banners') || \Illuminate\Support\Facades\Route::has('admin.app_releases'))
            <div class="pt-3">
                <div class="px-3 text-xs font-bold uppercase tracking-wide text-slate-400">Content Hub</div>
                <div class="mt-2 space-y-2">
                    @if(\Illuminate\Support\Facades\Route::has('admin.banners'))
                        <a href="{{ route('admin.banners') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Banner</a>
                    @endif
                    @if(\Illuminate\Support\Facades\Route::has('admin.app_releases'))
                        <a href="{{ route('admin.app_releases') }}" class="block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">App Release</a>
                    @endif
                </div>
            </div>
        @endif
        @if(\Illuminate\Support\Facades\Route::has('admin.notification'))
            <div class="pt-3">
                <div class="px-3 text-xs font-bold uppercase tracking-wide text-slate-400">Notification Center</div>
                <a href="{{ route('admin.notification') }}" class="mt-2 block rounded-lg px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">Push Notification</a>
            </div>
        @endif
    </nav>
</aside>
