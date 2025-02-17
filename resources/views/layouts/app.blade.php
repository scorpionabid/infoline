<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'İnfoLine') }}</title>

    <!-- Vendor CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Application CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    @auth
        @include('partials.navbar')
    @endauth

    <!-- Main Content -->
    <div class="container-fluid mt-3">
        @yield('content')
    </div>

    <!-- Vendor Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>

    <!-- Global Configurations -->
    <script>
        window.appConfig = {
            baseUrl: '{{ url('/') }}',
            csrfToken: '{{ csrf_token() }}'
        };

        // Configure AJAX defaults
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': window.appConfig.csrfToken
            }
        });
    </script>

    <!-- Application Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/settings/table.js') }}"></script>
    <script src="{{ asset('js/settings/regions.js') }}"></script>
    <script src="{{ asset('js/settings/sector.js') }}"></script>

    <!-- Page Specific Scripts -->
    @stack('scripts')

    <!-- Flash Messages -->
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Uğurlu!',
                text: '{{ session('success') }}',
                timer: 3000
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Xəta!',
                text: '{{ session('error') }}',
                timer: 3000
            });
        </script>
    @endif
</body>
</html>