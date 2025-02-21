@extends('layouts.app')

@section('title', 'Yeni Məktəb')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Yeni Məktəb</h6>
            <a href="{{ route('settings.personal.schools.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri
            </a>
        </div>
        <div class="card-body">
            <form id="createSchoolForm" method="POST" action="{{ route('settings.personal.schools.store') }}">
                @csrf
                
                <div class="row">
                    <!-- Əsas Məlumatlar -->
                    <div class="col-md-6">
                        <h5 class="mb-3">Əsas Məlumatlar</h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Məktəbin Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="utis_code" class="form-label">UTİS Kodu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('utis_code') is-invalid @enderror" 
                                   id="utis_code" name="utis_code" value="{{ old('utis_code') }}" required>
                            @error('utis_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Məktəb Tipi <span class="text-danger">*</span></label>
                            <select class="form-control @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                <option value="">Seçin</option>
                                @foreach($schoolTypes as $type)
                                    <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sector_id" class="form-label">Sektor <span class="text-danger">*</span></label>
                            <select class="form-control @error('sector_id') is-invalid @enderror" 
                                    id="sector_id" name="sector_id" required>
                                <option value="">Seçin</option>
                                @foreach($sectors as $sector)
                                    <option value="{{ $sector->id }}" {{ old('sector_id') == $sector->id ? 'selected' : '' }}>
                                        {{ $sector->region->name }} - {{ $sector->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('sector_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Əlaqə Məlumatları -->
                    <div class="col-md-6">
                        <h5 class="mb-3">Əlaqə Məlumatları</h5>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="website" class="form-label">Vebsayt</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                   id="website" name="website" value="{{ old('website') }}">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Ünvan</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Əlavə Məlumatlar -->
                    <div class="col-12 mt-4">
                        <h5 class="mb-3">Əlavə Məlumatlar</h5>

                        <div class="mb-3">
                            <label for="description" class="form-label">Təsvir</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" 
                                       id="status" name="status" value="1" 
                                       {{ old('status', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="status">Aktiv</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Yadda Saxla
                    </button>
                    <a href="{{ route('settings.personal.schools.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Ləğv Et
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Form submit
    $('#createSchoolForm').submit(function() {
        $(this).find('button[type="submit"]').prop('disabled', true);
    });

    // Select2 inteqrasiyası
    $('#sector_id, #type').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Telefon nömrəsi formatı
    $('#phone').inputmask('+\\9\\94 (99) 999-99-99');
});
</script>
@endpush