@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-th-list"></i> Kateqoriyalar və Sütunlar</h2>
        <button type="button" class="btn btn-primary" onclick="showAddCategory()">
            <i class="fas fa-plus"></i> Yeni Kateqoriya
        </button>
    </div>

    <!-- Kateqoriyalar Siyahısı -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="categoriesTable">
                            <thead>
                                <tr>
                                    <th>Kateqoriya</th>
                                    <th>Təsvir</th>
                                    <th>Növ</th>
                                    <th>Sütun Sayı</th>
                                    <th>Son Dəyişiklik</th>
                                    <th>Status</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                <tr data-category-id="{{ $category->id }}">
                                    <td>{{ $category->name }}</td>
                                    <td>{{ Str::limit($category->description, 50) }}</td>
                                    <td>
                                        @switch($category->type)
                                            @case('standard')
                                                <span class="badge bg-primary">Standart</span>
                                                @break
                                            @case('dynamic')
                                                <span class="badge bg-success">Dinamik</span>
                                                @break
                                            @case('report')
                                                <span class="badge bg-info">Hesabat</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $category->columns_count }}</td>
                                    <td>{{ $category->updated_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   onchange="updateCategoryStatus({{ $category->id }}, this.checked)"
                                                   {{ $category->status ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info" 
                                                    onclick="showColumns({{ $category->id }})">
                                                <i class="fas fa-columns"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="showEditCategory({{ json_encode($category) }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="showCloneCategory({{ $category->id }}, '{{ $category->name }}')">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="deleteCategory({{ $category->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sütunlar Siyahısı -->
    <div class="row d-none" id="columnsSection">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary" id="columnsSectionTitle"></h6>
                    <button type="button" class="btn btn-primary btn-sm" onclick="showAddColumn()" id="addColumnBtn" disabled>
                        <i class="fas fa-plus"></i> Yeni Sütun
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="columnsTable">
                            <thead>
                                <tr>
                                    <th>Sıra</th>
                                    <th>Sütun</th>
                                    <th>Təsvir</th>
                                    <th>Növ</th>
                                    <th>Məcburi</th>
                                    <th>Son Tarix</th>
                                    <th>Limit</th>
                                    <th>Status</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal-ları daxil edirik -->
@include('partials.settings.table.modals.category')
@include('partials.settings.table.modals.column')
@include('partials.settings.table.modals.deadline')
@include('partials.settings.table.modals.limit')
@include('partials.settings.table.modals.clone-category')

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script src="{{ asset('js/settings/table.js') }}"></script>

<script>
// Sütunların sırasını dəyişmək üçün
let currentCategoryId = null;
let sortable = null;

function showColumns(categoryId) {
    currentCategoryId = categoryId;
    
    // Sütunları gətir
    axios.get(`/settings/categories/${categoryId}/columns`)
        .then(response => {
            const columns = response.data.columns;
            const category = response.data.category;
            
            // Başlığı yenilə
            document.getElementById('columnsSectionTitle').textContent = `${category.name} - Sütunlar`;
            
            // Sütunlar cədvəlini doldur
            const tbody = document.getElementById('columnsTable').querySelector('tbody');
            tbody.innerHTML = columns.map((column, index) => `
                <tr data-column-id="${column.id}">
                    <td class="handle"><i class="fas fa-grip-vertical"></i> ${index + 1}</td>
                    <td>${column.name}</td>
                    <td>${column.description || '-'}</td>
                    <td>${getColumnTypeLabel(column.type)}</td>
                    <td>${column.required ? '<i class="fas fa-check text-success"></i>' : '-'}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                onclick="showDeadlineModal(${column.id}, '${column.end_date || ''}')">
                            ${column.end_date || 'Təyin et'}
                        </button>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                onclick="showLimitModal(${column.id}, ${column.input_limit || ''})">
                            ${column.input_limit || 'Təyin et'}
                        </button>
                    </td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   onchange="updateColumnStatus(${column.id}, this.checked)"
                                   ${column.status ? 'checked' : ''}>
                        </div>
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-primary" 
                                    onclick="showEditColumn(${JSON.stringify(column)})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" 
                                    onclick="deleteColumn(${column.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
            
            // Sütunlar bölməsini göstər
            document.getElementById('columnsSection').classList.remove('d-none');
            document.getElementById('addColumnBtn').disabled = false;
            document.getElementById('addColumnBtn').onclick = () => showAddColumn(categoryId);
            
            // Sıralama funksionallığını aktiv et
            if (sortable) {
                sortable.destroy();
            }
            
            sortable = new Sortable(tbody, {
                handle: '.handle',
                animation: 150,
                onEnd: function() {
                    const newOrder = Array.from(tbody.children).map(tr => tr.dataset.columnId);
                    updateColumnsOrder(newOrder);
                }
            });
        })
        .catch(error => {
            showErrorMessage(error.response?.data?.message || 'Xəta baş verdi');
        });
}

function getColumnTypeLabel(type) {
    const labels = {
        'text': 'Mətn',
        'number': 'Rəqəm',
        'date': 'Tarix',
        'select': 'Seçim',
        'textarea': 'Uzun Mətn'
    };
    return labels[type] || type;
}

function updateColumnsOrder(newOrder) {
    axios.put(`/settings/categories/${currentCategoryId}/columns/reorder`, { order: newOrder })
        .then(response => {
            showSuccessMessage(response.data.message);
        })
        .catch(error => {
            showErrorMessage(error.response?.data?.message || 'Xəta baş verdi');
            showColumns(currentCategoryId); // Sıralanmanı yenidən yüklə
        });
}

function updateCategoryStatus(categoryId, status) {
    axios.put(`/settings/categories/${categoryId}/status`, { status })
        .then(response => {
            showSuccessMessage(response.data.message);
        })
        .catch(error => {
            showErrorMessage(error.response?.data?.message || 'Xəta baş verdi');
            location.reload(); // Səhifəni yenilə
        });
}

function updateColumnStatus(columnId, status) {
    axios.put(`/settings/columns/${columnId}/status`, { status })
        .then(response => {
            showSuccessMessage(response.data.message);
        })
        .catch(error => {
            showErrorMessage(error.response?.data?.message || 'Xəta baş verdi');
            showColumns(currentCategoryId); // Sütunları yenidən yüklə
        });
}
</script>
@endpush