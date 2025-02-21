<div class="btn-group">
    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-cog"></i> <i class="mdi mdi-chevron-down"></i>
    </button>
    <div class="dropdown-menu">
        <a class="dropdown-item edit-sector" href="javascript:void(0);" data-id="{{ $sector->id }}">
            <i class="fas fa-edit text-primary"></i> Redaktə et
        </a>
        @if($sector->admin)
            <a class="dropdown-item remove-admin" href="javascript:void(0);" data-id="{{ $sector->id }}">
                <i class="fas fa-user-minus text-warning"></i> Admini sil
            </a>
        @else
            <a class="dropdown-item assign-admin" href="javascript:void(0);" data-id="{{ $sector->id }}">
                <i class="fas fa-user-plus text-success"></i> Admin təyin et
            </a>
        @endif
        @if($sector->schools->isEmpty())
            <a class="dropdown-item delete-sector" href="javascript:void(0);" data-id="{{ $sector->id }}">
                <i class="fas fa-trash-alt text-danger"></i> Sil
            </a>
        @endif
    </div>
</div>