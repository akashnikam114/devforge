<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app-ui.app_name', config('app.name')))</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="min-h-screen flex">
        @include('admin.layouts.sidebar')
        <main class="flex-1">
            @include('admin.layouts.header')
            <section class="p-6">
                @include('admin.layouts.flash')
                @yield('content')
            </section>
            @include('admin.layouts.footer')
        </main>
    </div>
</body>
</html>
