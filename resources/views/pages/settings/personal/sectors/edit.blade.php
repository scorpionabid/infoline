@extends('layouts.app')

@section('title', 'Sektor Redaktəsi')

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
                        <li class="breadcrumb-item active">Sektor Redaktəsi</li>
                    </ol>
                </div>
                <h4 class="page-title">Sektor Redaktəsi</h4>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('settings.personal.sectors.update', $sector->id) }}" method="POST" id="sectorForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Sektor Adı <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $sector->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="region_id" class="form-label">Region <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('region_id') is-invalid @enderror" 
                                            id="region_id" name="region_id" required>
                                        <option value="">Seçin</option>
                                        @foreach($regions as $region)
                                            <option value="{{ $region->id }}" 
                                                {{ old('region_id', $sector->region_id) == $region->id ? 'selected' : '' }}>
                                                {{ $region->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('region_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Təsvir</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description', $sector->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="{{ route('settings.personal.sectors.index') }}" class="btn btn-secondary me-2">Ləğv Et</a>
                            <button type="submit" class="btn btn-primary">Yadda Saxla</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(!$sector->admin)
    <!-- Assign Admin Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Sektor Admini Təyin Et</h5>
                    <form action="{{ route('settings.personal.sectors.assign-admin', $sector->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                           id="full_name" name="full_name" required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telefon <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">Admin Təyin Et</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Sektor Admin Modal -->
    <div class="modal fade" id="sectorAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sektor Admini Təyin Et</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="sectorAdminForm">
                        @csrf
                        <div id="adminFormErrors"></div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Ad Soyad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefon <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="send_credentials" name="send_credentials">
                                <label class="form-check-label" for="send_credentials">
                                    Giriş məlumatlarını email ilə göndər
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv Et</button>
                    <button type="submit" class="btn btn-primary" form="sectorAdminForm">Təyin Et</button>
                </div>
            </div>
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
        $('.select2').select2();
    });
</script>
<script>
    // Admin təyin etmə düyməsinin hadisəsini əlavə et
    $(document).on('click', '.assign-admin-btn', function(e) {
        e.preventDefault();
        const sectorId = $(this).data('sector-id');
        SectorManager.showAdminModal(sectorId);
    });
</script>
@endpush