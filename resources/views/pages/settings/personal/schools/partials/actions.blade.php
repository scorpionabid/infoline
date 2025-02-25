<div class="btn-group">
    <a href="{{ route('settings.personal.schools.edit', $school) }}" class="btn btn-sm btn-primary" title="Redaktə et">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button" class="btn btn-sm btn-danger delete-school" data-id="{{ $school->id }}" title="Sil">
        <i class="fas fa-trash"></i>
    </button>
    <div class="btn-group">
        <button type="button" class="btn btn-sm btn-info assign-admin" data-id="{{ $school->id }}" data-bs-toggle="modal" data-bs-target="#assignAdminModal" title="Mövcud istifadəçini admin təyin et">
            <i class="fas fa-user-shield"></i>
        </button>
        <a href="{{ route('settings.personal.schools.admin.create', $school) }}" class="btn btn-sm btn-success" title="Yeni admin yarat">
            <i class="fas fa-user-plus"></i>
        </a>
    </div>
    <a href="{{ route('settings.personal.schools.show', $school) }}" class="btn btn-sm btn-success" title="Ətraflı bax">
        <i class="fas fa-eye"></i>
    </a>
</div>
