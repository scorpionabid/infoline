{{-- resources/views/pages/dashboard/school-admin/components/category-tabs.blade.php --}}
<ul class="nav nav-tabs" id="categoryTabs" role="tablist">
    @foreach($categories as $category)
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                id="category-{{ $category->id }}-tab" 
                data-bs-toggle="tab"
                data-bs-target="#category-{{ $category->id }}" 
                type="button" 
                role="tab">
            {{ $category->name }}
            @if($category->columns->whereNull('data_values')->where('is_required', true)->count() > 0)
            <span class="badge bg-danger">!</span>
            @endif
        </button>
    </li>
    @endforeach
</ul>