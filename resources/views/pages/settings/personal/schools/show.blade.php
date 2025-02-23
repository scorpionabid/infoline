@extends('layouts.app')

@section('title', $school->name)

@section('content')
<div class="content-wrapper">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
        <div class="container-fluid">
            <h5 class="navbar-brand mb-0">{{ $school->name }}</h5>
            <div class="ms-auto">
                <a href="{{ route('settings.personal.schools.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Geri
                </a>
                <a href="{{ route('settings.personal.schools.edit', $school) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Redaktə et
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- School Details -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Məktəb məlumatları</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">UTİS kodu:</dt>
                                    <dd class="col-sm-8">{{ $school->utis_code }}</dd>

                                    <dt class="col-sm-4">Region:</dt>
                                    <dd class="col-sm-8">{{ $school->sector->region->name }}</dd>

                                    <dt class="col-sm-4">Sektor:</dt>
                                    <dd class="col-sm-8">{{ $school->sector->name }}</dd>

                                    <dt class="col-sm-4">Məktəb tipi:</dt>
                                    <dd class="col-sm-8">{{ config('enums.school_types')[$school->type] ?? '-' }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Telefon:</dt>
                                    <dd class="col-sm-8">{{ $school->phone ?: '-' }}</dd>

                                    <dt class="col-sm-4">Email:</dt>
                                    <dd class="col-sm-8">{{ $school->email ?: '-' }}</dd>

                                    <dt class="col-sm-4">Ünvan:</dt>
                                    <dd class="col-sm-8">{{ $school->address ?: '-' }}</dd>

                                    <dt class="col-sm-4">Status:</dt>
                                    <dd class="col-sm-8">
                                        @if($school->status)
                                            <span class="badge bg-success">Aktiv</span>
                                        @else
                                            <span class="badge bg-danger">Deaktiv</span>
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Completion -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Məlumat tamamlanması</h5>
                    </div>
                    <div class="card-body">
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar" role="progressbar" 
                                style="width: {{ $school->data_completion_percentage }}%;"
                                aria-valuenow="{{ $school->data_completion_percentage }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                {{ $school->data_completion_percentage }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admins -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Məktəb adminləri</h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignAdminModal">
                            <i class="fas fa-plus me-1"></i>Admin əlavə et
                        </button>
                    </div>
                    <div class="card-body">
                        @if($school->admins->isEmpty())
                            <p class="text-muted mb-0">Heç bir admin təyin edilməyib.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($school->admins as $admin)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">{{ $admin->full_name }}</h6>
                                            <small class="text-muted">{{ $admin->email }}</small>
                                        </div>
                                        @if($admin->id === $school->admin_id)
                                            <span class="badge bg-primary">Əsas admin</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assign Admin Modal -->
<div class="modal fade" id="assignAdminModal" tabindex="-1" aria-labelledby="assignAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignAdminModalLabel">Admin təyin et</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignAdminForm" action="{{ route('settings.personal.schools.assign-admin', $school) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="admin_id" class="form-label">Admin seçin <span class="text-danger">*</span></label>
                        <select class="form-select" id="admin_id" name="admin_id" required>
                            <option value="">Seçin</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                <button type="submit" class="btn btn-primary" form="assignAdminForm">Təyin et</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('js/settings/school.js') }}"></script>
@endpush
