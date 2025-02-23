{{-- resources/views/pages/dashboard/school-admin/components/data-entry-form.blade.php --}}
<div class="tab-content mt-4" id="categoryTabsContent">
    @foreach($categories as $category)
    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
         id="category-{{ $category->id }}" 
         role="tabpanel">
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Sütun adı</th>
                        <th>Tip</th>
                        <th>Dəyər</th>
                        <th>Son tarix</th>
                        <th>Status</th>
                        <th>Əməliyyatlar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($category->columns as $column)
                    <tr data-column-id="{{ $column->id }}" 
                        class="{{ $column->is_required ? 'table-warning' : '' }} {{ $column->created_at->gt(now()->subDays(7)) ? 'new-column' : '' }}">
                        <td>
                            {{ $column->name }}
                            @if($column->is_required)
                            <span class="text-danger">*</span>
                            @endif
                            @if($column->created_at->gt(now()->subDays(7)))
                            <span class="badge bg-info">Yeni</span>
                            @endif
                        </td>
                        <td>{{ $column->data_type }}</td>
                        <td>
                            @include('pages.dashboard.school-admin.components.column-input', [
                                'column' => $column,
                                'value' => $column->dataValues->first()?->value
                            ])
                        </td>
                        <td>
                            @if($column->end_date)
                            <span class="{{ $column->end_date->isPast() ? 'text-danger' : ($column->end_date->diffInDays(now()) <= 3 ? 'text-warning' : '') }}">
                                {{ $column->end_date->format('d.m.Y') }}
                            </span>
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            @if($column->dataValues->first())
                            <span class="badge bg-success">Doldurulub</span>
                            @else
                            <span class="badge bg-danger">Boş</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" 
                                        class="btn btn-primary edit-value" 
                                        data-column-id="{{ $column->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-success save-value d-none" 
                                        data-column-id="{{ $column->id }}">
                                    <i class="fas fa-save"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>