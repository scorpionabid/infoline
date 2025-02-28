{{-- resources/views/pages/dashboard/school-admin/components/column-input.blade.php --}}
<div class="column-input" data-column-id="{{ $column->id }}">
    @switch($column->type)
        @case('text')
            <input type="text" 
                   class="form-control form-control-sm" 
                   value="{{ $value }}"
                   data-original-value="{{ $value }}">
            @break

        @case('number')
            <input type="number" 
                   class="form-control form-control-sm" 
                   value="{{ $value }}"
                   data-original-value="{{ $value }}">
            @break
            
        <!-- Digər input tipləri -->

        @default
            <input type="text" 
                   class="form-control form-control-sm" 
                   value="{{ $value }}"
                   data-original-value="{{ $value }}">
    @endswitch
</div>