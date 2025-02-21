@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $school->name }} - Məlumatlar</h3>
                </div>
                <div class="card-body">
                    @foreach($categories as $category)
                        <div class="category-section mb-4">
                            <h4>{{ $category->name }}</h4>
                            <p class="text-muted">{{ $category->description }}</p>

                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sütun</th>
                                            <th>Dəyər</th>
                                            <th>Son yenilənmə</th>
                                            <th>Əməliyyatlar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($category->columns->where('status', true) as $column)
                                            <tr>
                                                <td>
                                                    {{ $column->name }}
                                                    @if($column->required)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                    @if($column->description)
                                                        <br>
                                                        <small class="text-muted">{{ $column->description }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $dataValue = $column->dataValues->first();
                                                        $value = $dataValue ? $dataValue->value : null;
                                                    @endphp
                                                    
                                                    @if($column->type === 'select')
                                                        <select class="form-control data-input" 
                                                                data-column-id="{{ $column->id }}"
                                                                @if($column->end_date && now()->greaterThan($column->end_date)) disabled @endif>
                                                            <option value="">Seçin</option>
                                                            @foreach(json_decode($column->options, true) as $key => $option)
                                                                <option value="{{ $key }}" @if($value === $key) selected @endif>
                                                                    {{ $option }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    @elseif($column->type === 'textarea')
                                                        <textarea class="form-control data-input" 
                                                                data-column-id="{{ $column->id }}"
                                                                @if($column->end_date && now()->greaterThan($column->end_date)) disabled @endif>{{ $value }}</textarea>
                                                    @else
                                                        <input type="{{ $column->type }}" 
                                                               class="form-control data-input" 
                                                               data-column-id="{{ $column->id }}"
                                                               value="{{ $value }}"
                                                               @if($column->end_date && now()->greaterThan($column->end_date)) disabled @endif>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($dataValue)
                                                        {{ $dataValue->updated_at->format('d.m.Y H:i') }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($dataValue)
                                                        <button class="btn btn-sm btn-danger delete-data"
                                                                data-column-id="{{ $column->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Məlumatların saxlanılması
    $('.data-input').on('change', function() {
        const columnId = $(this).data('column-id');
        const value = $(this).val();

        $.ajax({
            url: "{{ route('settings.table.school.data.store', $school) }}",
            method: 'POST',
            data: {
                values: [{
                    column_id: columnId,
                    value: value
                }],
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                toastr.success(response.message);
                location.reload();
            },
            error: function(xhr) {
                const errors = xhr.responseJSON.errors;
                Object.values(errors).forEach(error => {
                    toastr.error(error[0]);
                });
            }
        });
    });

    // Məlumatların silinməsi
    $('.delete-data').on('click', function() {
        const columnId = $(this).data('column-id');

        if (confirm('Bu məlumatı silmək istədiyinizə əminsiniz?')) {
            $.ajax({
                url: "{{ route('settings.table.school.data.destroy', ['school' => $school->id, 'column' => ':columnId']) }}".replace(':columnId', columnId),
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    toastr.success(response.message);
                    location.reload();
                },
                error: function(xhr) {
                    toastr.error('Xəta baş verdi');
                }
            });
        }
    });
});
</script>
@endpush