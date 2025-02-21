@extends('layouts.app')

@section('title', 'Məktəb Redaktəsi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Məktəb Məlumatları -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Məktəb Məlumatları</h6>
                    <a href="{{ route('settings.personal.schools.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Geri
                    </a>
                </div>
                <div class="card-body">
                    <form id="editSchoolForm" method="POST" action="{{ route('settings.personal.schools.update', $school) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Əsas Məlumatlar -->
                            <div class="col-md-6">
                                <h5 class="mb-3">Əsas Məlumatlar</h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label">Məktəbin Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $school->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="utis_code" class="form-label">UTİS Kodu <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('utis_code') is-invalid @enderror" 
                                           id="utis_code" name="utis_code" value="{{ old('utis_code', $school->utis_code) }}" required>
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
                                            <option value="{{ $type }}" {{ old('type', $school->type) == $type ? 'selected' : '' }}>
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
                                            <option value="{{ $sector->id }}" {{ old('sector_id', $school->sector_id) == $sector->id ? 'selected' : '' }}>
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
                                           id="phone" name="phone" value="{{ old('phone', $school->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $school->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="website" class="form-label">Vebsayt</label>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                           id="website" name="website" value="{{ old('website', $school->website) }}">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Ünvan</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3">{{ old('address', $school->address) }}</textarea>
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
                                              id="description" name="description" rows="3">{{ old('description', $school->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="status" name="status" value="1" 
                                               {{ old('status', $school->status) ? 'checked' : '' }}>
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

        <!-- Sağ Panel -->
        <div class="col-md-4">
            <!-- Məktəb Statistikası -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Məktəb Statistikası</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h4 class="small font-weight-bold">Məlumat Tamamlanması 
                            <span class="float-right">{{ $dataCompletion }}%</span>
                        </h4>
                        <div class="progress">
                            <div class="progress-bar bg-{{ $dataCompletion < 50 ? 'danger' : ($dataCompletion < 80 ? 'warning' : 'success') }}" 
                                 role="progressbar" style="width: {{ $dataCompletion }}%"></div>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('settings.personal.schools.data', $school) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-database"></i> Məlumatları İdarə Et
                        </a>
                    </div>
                </div>
            </div>

            <!-- Məktəb Administratoru -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Məktəb Administratoru</h6>
                </div>
                <div class="card-body">
                    @if($school->admin)
                        <div class="text-center mb-3">
                            <img src="{{ $school->admin->avatar_url }}" 
                                 alt="{{ $school->admin->name }}" 
                                 class="img-profile rounded-circle" 
                                 style="width: 100px; height: 100px;">
                        </div>
                        <h5 class="text-center mb-3">{{ $school->admin->name }}</h5>
                        <p class="text-center mb-2">
                            <i class="fas fa-envelope"></i> {{ $school->admin->email }}
                        </p>
                        <p class="text-center">
                            <i class="fas fa-phone"></i> {{ $school->admin->phone }}
                        </p>
                    @else
                        <div class="text-center">
                            <p class="mb-3">Administrator təyin edilməyib</p>
                        </div>
                    @endif

                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#assignAdminModal">
                            <i class="fas fa-user-plus"></i> Administrator Təyin Et
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Administrator Təyin Etmə Modal -->
<div class="modal fade" id="assignAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Administrator Təyin Et</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="assignAdminForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="admin_id">Administrator</label>
                        <select class="form-control" id="admin_id" name="admin_id" required>
                            <option value="">Seçin</option>
                            @foreach($availableAdmins as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Bağla</button>
                    <button type="submit" class="btn btn-primary">Təyin Et</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Form submit
    $('#editSchoolForm').submit(function() {
        $(this).find('button[type="submit"]').prop('disabled', true);
    });

    // Select2 inteqrasiyası
    $('#sector_id, #type, #admin_id').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Telefon nömrəsi formatı
    $('#phone').inputmask('+\\9\\94 (99) 999-99-99');

    // Administrator təyin etmə
    $('#assignAdminForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: "{{ route('settings.personal.schools.assign-admin', $school) }}",
            type: 'POST',
            data: {
                admin_id: $('#admin_id').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#assignAdminModal').modal('hide');
                toastr.success(response.message);
                location.reload();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message || 'Xəta baş verdi');
            }
        });
    });
});
</script>
@endpush