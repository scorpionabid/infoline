<!DOCTYPE html>
<html lang="az">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <title>{{ config('app.name', 'İnfoLine') }}</title>

   <!-- Vendor CSS -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
   <link rel="stylesheet" href="{{ asset('css/app.css') }}">
   @yield('styles')
</head>
<body>
   @auth
       @include('partials.navbar')
   @endauth

   <div class="container-fluid mt-3">
       @yield('content')
   </div>

   <!-- Core JS -->
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

   <!-- Global Config -->
   <script>
       window.appConfig = {
           baseUrl: '{{ url('/') }}',
           csrfToken: '{{ csrf_token() }}'
       };

       $.ajaxSetup({
           headers: {
               'X-CSRF-TOKEN': window.appConfig.csrfToken
           }
       });
   </script>

   <!-- App JS -->
   <script src="{{ asset('js/app.js') }}"></script>
   
   <!-- Page Specific Scripts -->
   @stack('scripts')

   <!-- Flash Messages -->
   @if(session('success') || session('error'))
       <script>
           Swal.fire({
               icon: '{{ session('success') ? 'success' : 'error' }}',
               title: '{{ session('success') ? 'Uğurlu!' : 'Xəta!' }}',
               text: '{{ session('success') ?? session('error') }}',
               timer: 3000
           });
       </script>
   @endif
</body>
</html>