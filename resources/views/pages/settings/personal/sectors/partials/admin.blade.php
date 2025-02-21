@if($sector->admin)
    <div class="d-flex align-items-center">
        <div>
            <div class="d-flex align-items-center">
                {{ $sector->admin->full_name }}
                <span class="badge bg-success ms-2">Aktiv</span>
            </div>
            <small class="text-muted">{{ $sector->admin->email }}</small>
        </div>
        <div class="dropdown ms-2">
            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                <i class="fas fa-ellipsis-v"></i>
            </button>
            <div class="dropdown-menu">
                <a href="#" class="dropdown-item edit-admin" data-id="{{ $sector->id }}">
                    <i class="fas fa-edit me-1"></i> Redaktə et
                </a>
                <a href="#" class="dropdown-item text-danger remove-admin" data-id="{{ $sector->id }}">
                    <i class="fas fa-trash me-1"></i> Sil
                </a>
            </div>
        </div>
    </div>
@else
    <button class="btn btn-sm btn-outline-primary assign-admin" data-id="{{ $sector->id }}">
        <i class="fas fa-user-plus me-1"></i> Admin təyin et
    </button>
@endif