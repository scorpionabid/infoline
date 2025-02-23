{{-- resources/views/pages/dashboard/school-admin/components/column-input.blade.php --}}
<div class="column-input" data-column-id="{{ $column->id }}">
    @switch($column->data_type)
        @case('text')
            <input type="text" 
                   class="form-control form-control-sm" 
                   value="{{ $value }}" 
                   disabled 
                   data-original-value="{{ $value }}">
            @break

        @case('number')
            <input type="number" 
                   class="form-control form-control-sm" 
                   value="{{ $value }}" 
                   disabled 
                   data-original-value="{{ $value }}">
            @break

        @case('date')
            <input type="date" 
                   class="form-control form-control-sm" 
                   value="{{ $value }}" 
                   disabled 
                   data-original-value="{{ $value }}">
            @break

        @case('select')
            <select class="form-select form-select-sm" disabled>
                <option value="">Seçin</option>
                @foreach($column->options as $option)
                    <option value="{{ $option }}" {{ $value == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
            @break

        @default
            <input type="text" 
                   class="form-control form-control-sm" 
                   value="{{ $value }}" 
                   disabled 
                   data-original-value="{{ $value }}">
    @endswitch
</div>