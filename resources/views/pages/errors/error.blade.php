{{-- resources/views/errors/error.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-circle text-danger display-1"></i>
                    </div>
                    
                    <h1 class="h3 mb-4">{{ $title ?? 'Xəta baş verdi' }}</h1>
                    
                    <p class="text-muted mb-4">
                        {{ $message ?? 'Gözlənilməz bir xəta baş verdi. Zəhmət olmasa bir az sonra yenidən cəhd edin.' }}
                    </p>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Geri qayıt
                        </a>

                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>
                            Ana səhifə
                        </a>
                    </div>
                </div>
            </div>

            @if(config('app.debug') && isset($exception))
            <div class="card mt-4">
                <div class="card-header">
                    Xəta detalları
                </div>
                <div class="card-body">
                    <pre class="mb-0"><code>{{ $exception }}</code></pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection