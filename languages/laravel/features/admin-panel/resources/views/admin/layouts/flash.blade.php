@if (session('success'))
    <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{{ session('success') }}</div>
@endif

@if (session('error'))
    <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ session('error') }}</div>
@endif
