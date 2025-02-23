<div class="btn-group">
    <a href="{{ route('settings.personal.regions.edit', $region->id) }}" 
       class="btn btn-sm btn-primary"
       title="Redaktə et">
        <i class="fas fa-edit"></i>
    </a>
    
    @if($region->sectors_count == 0)
        <form action="{{ route('settings.personal.regions.destroy', $region->id) }}" 
              method="POST" 
              class="d-inline"
              onsubmit="return confirm('Bu regionu silmək istədiyinizdən əminsiniz?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    @endif
    
    @if(!$region->admin)
        <a href="{{ route('settings.personal.regions.assign-admin', $region->id) }}" 
           class="btn btn-sm btn-success"
           title="Admin təyin et">
            <i class="fas fa-user-shield"></i>
        </a>
    @endif
</div>