<script>
    $(document).ready(function () {
        @if (session('success'))
            NioApp.Toast('<h5>Success</h5><p>{{ session('success') }}</p>', 'success');
        @endif

        @if (session('error'))
            NioApp.Toast('<h5>Error</h5><p>{{ session('error') }}</p>', 'error');
        @endif

        @if (session('warning'))
            NioApp.Toast('<h5>Warning</h5><p>{{ session('warning') }}</p>', 'warning');
        @endif
    });
</script>