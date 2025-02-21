@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Yeni İstifadəçi</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('settings.personal.users.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Ad</label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                               value="{{ old('first_name') }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Soyad</label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                               value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Şifrə</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">İstifadəçi tipi</label>
                        <select name="user_type" id="user_type" 
                                class="form-select @error('user_type') is-invalid @enderror" required>
                            <option value="">Seçin</option>
                            @foreach($user_types as $value => $label)
                                <option value="{{ $value }}" @selected(old('user_type') == $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Region</label>
                        <select name="region_id" id="region_id" 
                                class="form-select @error('region_id') is-invalid @enderror">
                            <option value="">Seçin</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" @selected(old('region_id') == $region->id)>
                                    {{ $region->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('region_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6" id="sector_container" style="display: none;">
                        <label class="form-label">Sektor</label>
                        <select name="sector_id" id="sector_id" 
                                class="form-select @error('sector_id') is-invalid @enderror">
                            <option value="">Seçin</option>
                            @foreach($sectors as $sector)
                                <option value="{{ $sector->id }}" @selected(old('sector_id') == $sector->id)>
                                    {{ $sector->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('sector_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6" id="school_container" style="display: none;">
                        <label class="form-label">Məktəb</label>
                        <select name="school_id" id="school_id" 
                                class="form-select @error('school_id') is-invalid @enderror">
                            <option value="">Seçin</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" @selected(old('school_id') == $school->id)>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('school_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Yadda saxla
                        </button>
                        <a href="{{ route('settings.personal.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Ləğv et
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('js')
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Select2 initialization
    $('.form-select').select2();

    // User type change handler
    $('#user_type').on('change', function() {
        var userType = $(this).val();
        
        // Hide all containers first
        $('#sector_container, #school_container').hide();
        
        if (userType === 'sectoradmin') {
            $('#sector_container').show();
        } else if (userType === 'schooladmin') {
            $('#sector_container, #school_container').show();
        }
    });

    // Region change handler
    $('#region_id').on('change', function() {
        var regionId = $(this).val();
        var sectorSelect = $('#sector_id');
        
        sectorSelect.empty().append('<option value="">Seçin</option>');
        $('#school_id').empty().append('<option value="">Seçin</option>');
        
        if (regionId) {
            $.get('/api/regions/' + regionId + '/sectors', function(sectors) {
                sectors.forEach(function(sector) {
                    sectorSelect.append(new Option(sector.name, sector.id));
                });
            });
        }
    });

    // Sector change handler
    $('#sector_id').on('change', function() {
        var sectorId = $(this).val();
        var schoolSelect = $('#school_id');
        
        schoolSelect.empty().append('<option value="">Seçin</option>');
        
        if (sectorId) {
            $.get('/api/sectors/' + sectorId + '/schools', function(schools) {
                schools.forEach(function(school) {
                    schoolSelect.append(new Option(school.name, school.id));
                });
            });
        }
    });

    // Trigger user type change if there's a selected value
    if ($('#user_type').val()) {
        $('#user_type').trigger('change');
    }
});
</script>
@endpush