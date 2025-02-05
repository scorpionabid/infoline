@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-body">
                <!-- Logo və başlıq -->
                <div class="text-center mb-4">
                    <h3 class="card-title">
                        <i class="fas fa-school text-primary"></i>
                        İnfoLine
                    </h3>
                    <p class="text-muted">Məktəb İdarəetmə Sistemi</p>
                </div>

                <!-- Xəta mesajları -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Login formu -->
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Şifrə
                        </label>
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="remember" 
                               name="remember">
                        <label class="form-check-label" for="remember">
                            Məni xatırla
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="loginButton">
                        <i class="fas fa-sign-in-alt"></i> Daxil ol
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Form təqdim edilərkən düymənin deaktiv edilməsi
    $('#loginForm').on('submit', function() {
        $('#loginButton').prop('disabled', true)
                        .html('<i class="fas fa-spinner fa-spin"></i> Yüklənir...');
    });
});
</script>
@endpush