@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h1 class="h3">İstifadəçi Düzəliş</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('settings.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Ad</label>
                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" 
                               value="{{ old('first_name', $user->first_name) }}" required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Soyad</label>
                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" 
                               value="{{ old('last_name', $user->last_name) }}" required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">İstifadəçi adı</label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                               value="{{ old('username', $user->username) }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Şifrə (Dəyişmək üçün doldurun)</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">UTIS Kodu</label>
                        <input type="text" name="utis_code" class="form-control @error('utis_code') is-invalid @enderror" 
                               value="{{ old('utis_code', $user->utis_code) }}" required>
                        @error('utis_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">İstifadəçi Tipi</label>
                        <select name="user_type" class="form-select @error('user_type') is-invalid @enderror" 
                                onchange="handleUserTypeChange(this.value)" 
                                {{ $user->isSuperAdmin() ? 'disabled' : 'required' }}>
                            <option value="">Seçin</option>
                            <option value="sectoradmin" @selected(old('user_type', $user->user_type) == 'sectoradmin')>
                                Sektor Admin
                            </option>
                            <option value="schooladmin" @selected(old('user_type', $user->user_type) == 'schooladmin')>
                                Məktəb Admin
                            </option>
                        </select>
                        @error('user_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Region</label>
                        <select name="region_id" id="region_id" 
                                class="form-select @error('region_id') is-invalid @enderror"
                                onchange="loadSectors(this.value)">
                            <option value="">Seçin</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}" 
                                        @selected(old('region_id', $user->region_id) == $region->id)>
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
                    <div class="col-md-6" id="sector_container">
                        <label class="form-label">Sektor</label>
                        <select name="sector_id" id="sector_id" 
                                class="form-select @error('sector_id') is-invalid @enderror"
                                onchange="loadSchools(this.value)">
                            <option value="">Seçin</option>
                            @foreach($sectors as $sector)
                                <option value="{{ $sector->id }}" 
                                        @selected(old('sector_id', $user->sector_id) == $sector->id)>
                                    {{ $sector->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('sector_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6" id="school_container">
                        <label class="form-label">Məktəb</label>
                        <select name="school_id" id="school_id" 
                                class="form-select @error('school_id') is-invalid @enderror">
                            <option value="">Seçin</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" 
                                        @selected(old('school_id', $user->school_id) == $school->id)>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('school_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">Rollar</label>
                        <div class="row">
                            @foreach($roles as $role)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="roles[]" 
                                               value="{{ $role->id }}"
                                               class="form-check-input @error('roles') is-invalid @enderror"
                                               @checked(in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())))>
                                        <label class="form-check-label">{{ $role->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                            @error('roles')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Yadda Saxla
                        </button>
                        <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Ləğv Et
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function handleUserTypeChange(type) {
    const sectorContainer = document.getElementById('sector_container');
    const schoolContainer = document.getElementById('school_container');
    const regionSelect = document.getElementById('region_id');
    const sectorSelect = document.getElementById('sector_id');
    const schoolSelect = document.getElementById('school_id');

    if (type === 'sectoradmin') {
        sectorContainer.style.display = 'block';
        schoolContainer.style.display = 'none';
        schoolSelect.value = '';
        regionSelect.required = true;
        sectorSelect.required = false;
        schoolSelect.required = false;
    } else if (type === 'schooladmin') {
        sectorContainer.style.display = 'block';
        schoolContainer.style.display = 'block';
        regionSelect.required = true;
        sectorSelect.required = true;
        schoolSelect.required = true;
    } else {
        sectorContainer.style.display = 'none';
        schoolContainer.style.display = 'none';
        regionSelect.required = false;
        sectorSelect.required = false;
        schoolSelect.required = false;
    }
}

function loadSectors(regionId) {
    if (!regionId) return;

    fetch(`/api/v1/regions/${regionId}/sectors`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('sector_id');
            select.innerHTML = '<option value="">Seçin</option>';
            
            data.data.forEach(sector => {
                const option = document.createElement('option');
                option.value = sector.id;
                option.textContent = sector.name;
                if (sector.id == '{{ old('sector_id', $user->sector_id) }}') {
                    option.selected = true;
                }
                select.appendChild(option);
            });

            // Məktəbləri yükləyək əgər seçilmiş sektor varsa
            if (select.value) {
                loadSchools(select.value);
            }
        });
}

function loadSchools(sectorId) {
    if (!sectorId) return;

    fetch(`/api/v1/sectors/${sectorId}/schools`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('school_id');
            select.innerHTML = '<option value="">Seçin</option>';
            
            data.data.forEach(school => {
                const option = document.createElement('option');
                option.value = school.id;
                option.textContent = school.name;
                if (school.id == '{{ old('school_id', $user->school_id) }}') {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        });
}

// Initialize form based on selected type
handleUserTypeChange(document.querySelector('select[name="user_type"]').value);
</script>
@endpush
@endsection
