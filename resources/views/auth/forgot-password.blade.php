@extends('layouts.auth')

@section('title', 'Şifrə Bərpası')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Şifrə Bərpası</h4>
                </div>
                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="text-muted mb-4">
                        E-poçt ünvanınızı daxil edin. Şifrə bərpası üçün link göndərəcəyik.
                    </p>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">E-poçt ünvanı</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Şifrə bərpası linkini göndər</button>
                            <a href="{{ route('login') }}" class="btn btn-link">Giriş səhifəsinə qayıt</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection