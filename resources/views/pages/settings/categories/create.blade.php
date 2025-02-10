@extends('layouts.app')

@section('title', 'Yeni Kateqoriya Əlavə Etmə')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Yeni Kateqoriya Əlavə Etmə</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('settings.categories.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="name">Kateqoriya Adı</label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   placeholder="Kateqoriya adını daxil edin" 
                                   value="{{ old('name') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Açıqlama</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Kateqoriya haqqında ətraflı məlumat">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Kateqoriya Əlavə Et
                        </button>
                        <a href="{{ route('settings.categories') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i> Geri Qayıt
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection