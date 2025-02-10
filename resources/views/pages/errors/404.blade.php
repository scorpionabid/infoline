@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <h1 class="display-1 text-muted">404</h1>
                    <h2 class="mb-4">Səhifə tapılmadı</h2>
                    <p class="mb-4">Axtardığınız səhifə tapılmadı və ya silinmiş ola bilər.</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Ana Səhifəyə Qayıt
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
