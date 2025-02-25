@extends('layouts.app')

@section('title', 'Yeni Admin Təyin Et')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Panel</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('settings.personal.sectors.index') }}">Sektorlar</a></li>
                        <li class="breadcrumb-item active">Yeni Admin Təyin Et</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ $sector->name }} Sektoru üçün Yeni Admin</h4>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Create Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('settings.personal.sectors.admin.store', $sector) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">Ad <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="{{ old('first_name') }}" 
                                           required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Soyad <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="{{ old('last_name') }}" 
                                           required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Şifrə <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           minlength="6">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Şifrə Təkrarı <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           required 
                                           minlength="6">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <a href="{{ route('settings.personal.sectors.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left"></i> Geri
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Yadda Saxla
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('title', 'Admin Yarat')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ $sector->name }} sektoru üçün admin təyin et</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('settings.personal.sectors.admin.store', $sector) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="first_name" class="form-label">Ad</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                   value="{{ old('first_name') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Soyad</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                   value="{{ old('last_name') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="{{ old('email') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Şifrə</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   required minlength="6">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Şifrə təkrarı</label>
                            <input type="password" class="form-control" id="password_confirmation" 
                                   name="password_confirmation" required minlength="6">
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('settings.personal.sectors.index') }}" class="btn btn-secondary">
                                Geri
                            </a>
                            <button type="submit" class="btn btn-primary">Yadda saxla</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection