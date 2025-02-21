<div class="btn-group">
    <a href="{{ route('settings.personal.regions.edit', $region->id) }}" 
       class="btn btn-sm btn-primary edit-region-btn"
       data-region-id="{{ $region->id }}"
       title="Redaktə et">
        <i class="fas fa-edit"></i>
    </a>
    
    @if($region->sectors_count == 0)
        <button type="button"
                class="btn btn-sm btn-danger delete-region-btn"
                data-region-id="{{ $region->id }}"
                title="Sil">
            <i class="fas fa-trash"></i>
        </button>
    @endif
    
    @if(!$region->admin)
        <button type="button"
                class="btn btn-sm btn-success assign-admin-btn"
                data-region-id="{{ $region->id }}"
                title="Admin təyin et">
            <i class="fas fa-user-shield"></i>
        </button>
    @endif
</div>