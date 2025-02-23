@extends('layouts.app')

@section('title', 'Region Redaktə Et')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Region Redaktə Et</h3>
                    <div class="card-tools">
                        <a href="{{ route('settings.personal.regions.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Geri
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="regionForm" data-region-id="{{ $region->id }}">
                        @csrf
                        @method('PUT')
                        <div id="regionFormErrors"></div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Region Adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $region->name }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="code" class="form-label">Region Kodu <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="code" name="code" value="{{ $region->code }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $region->phone }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Təsvir</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ $region->description }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Yadda Saxla</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('js/settings/regions.js') }}"></script>
@endpush