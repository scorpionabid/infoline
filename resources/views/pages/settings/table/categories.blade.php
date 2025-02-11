@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-th-list"></i> Kateqoriyalar və Sütunlar</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
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
                                    <td>{{ $category->columns_count }}</td>
                                    <td>{{ $category->updated_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $category->columns_count > 0 ? 'success' : 'warning' }}">
                                            {{ $category->columns_count > 0 ? 'Aktiv' : 'Boş' }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-info" onclick="showColumns({{ $category->id }})">
                                            <i class="fas fa-columns"></i> Sütunlar
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="editCategory({{ $category->id }})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteCategory({{ $category->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
</div>

<!-- Yeni Kateqoriya Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Kateqoriya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCategoryForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kateqoriya Adı</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                    <button type="submit" class="btn btn-primary">Yadda saxla</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sütunlar Modal -->
<div class="modal fade" id="columnsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kateqoriya Sütunları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <button type="button" class="btn btn-primary mb-3" onclick="showAddColumn()">
                    <i class="fas fa-plus"></i> Yeni Sütun
                </button>
                
                <div class="table-responsive">
                    <table class="table" id="columnsTable">
                        <thead>
                            <tr>
                                <th>Sütun Adı</th>
                                <th>Tip</th>
                                <th>Son Tarix</th>
                                <th>Status</th>
                                <th>Əməliyyatlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- JavaScript ilə doldurulacaq -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Sütun Əlavə/Redaktə Modal -->
<div class="modal fade" id="columnModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="columnModalTitle">Yeni Sütun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="columnForm">
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="columnCategoryId">
                    <input type="hidden" name="column_id" id="columnId">

                    <!-- Əsas Məlumatlar -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Sütun Adı</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Məlumat Tipi</label>
                            <select class="form-select" name="data_type" id="dataType" required>
                                <option value="text">Mətn</option>
                                <option value="number">Rəqəm</option>
                                <option value="date">Tarix</option>
                                <option value="select">Seçim</option>
                                <option value="multiselect">Çoxlu Seçim</option>
                                <option value="file">Fayl</option>
                            </select>
                        </div>
                    </div>

                    <!-- Sütun Parametrləri -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Son Tarix</label>
                            <input type="date" class="form-control" name="end_date">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Daxiletmə Limiti</label>
                            <input type="number" class="form-control" name="input_limit" 
                                   placeholder="Limitsiz üçün boş buraxın">
                        </div>
                    </div>

                    <!-- Seçim Variantları (select və multiselect üçün) -->
                    <div id="choicesSection" class="d-none">
                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Seçim Variantları</h6>
                                    <button type="button" class="btn btn-sm btn-primary" onclick="addChoice()">
                                        <i class="fas fa-plus"></i> Variant Əlavə Et
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="choicesList">
                                    <!-- JavaScript ilə doldurulacaq -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Validasiya Parametrləri -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Validasiya Parametrləri</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" 
                                               name="is_required" id="isRequired">
                                        <label class="form-check-label" for="isRequired">
                                            Məcburidir
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" 
                                               name="is_unique" id="isUnique">
                                        <label class="form-check-label" for="isUnique">
                                            Unikal olmalıdır
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Tip-specific validasiya parametrləri -->
                            <div id="typeSpecificValidation">
                                <!-- JavaScript ilə doldurulacaq -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                    <button type="submit" class="btn btn-primary">Yadda saxla</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sütun tipinin dəyişməsinə reaksiya
    const dataType = document.getElementById('dataType');
    dataType.addEventListener('change', function() {
        toggleChoicesSection();
        updateTypeSpecificValidation();
    });

    // Sütun formu
    const columnForm = document.getElementById('columnForm');
    columnForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const columnId = document.getElementById('columnId').value;
        
        try {
            const url = columnId 
                ? `/api/v1/columns/${columnId}` 
                : '/api/v1/columns';
                
            const response = await fetch(url, {
                method: columnId ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            if (response.ok) {
                window.location.reload();
            } else {
                const error = await response.json();
                throw new Error(error.message);
            }
        } catch (error) {
            alert('Xəta baş verdi: ' + error.message);
        }
    });
});

// Seçim variantları bölməsini göstər/gizlət
function toggleChoicesSection() {
    const type = document.getElementById('dataType').value;
    const choicesSection = document.getElementById('choicesSection');
    
    if (type === 'select' || type === 'multiselect') {
        choicesSection.classList.remove('d-none');
    } else {
        choicesSection.classList.add('d-none');
    }
}

// Seçim variantı əlavə et
function addChoice() {
    const choicesList = document.getElementById('choicesList');
    const choiceId = Date.now();
    
    const choiceHtml = `
        <div class="input-group mb-2" id="choice_${choiceId}">
            <input type="text" class="form-control" name="choices[]" required>
            <button type="button" class="btn btn-danger" onclick="removeChoice(${choiceId})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    choicesList.insertAdjacentHTML('beforeend', choiceHtml);
}

// Seçim variantını sil
function removeChoice(choiceId) {
    document.getElementById(`choice_${choiceId}`).remove();
}

// Tip-ə xas validasiya parametrlərini yenilə
function updateTypeSpecificValidation() {
    const type = document.getElementById('dataType').value;
    const container = document.getElementById('typeSpecificValidation');
    
    let html = '';
    
    switch(type) {
        case 'number':
            html = `
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Minimum Dəyər</label>
                        <input type="number" class="form-control" name="min_value">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Maximum Dəyər</label>
                        <input type="number" class="form-control" name="max_value">
                    </div>
                </div>
            `;
            break;
            
        case 'text':
            html = `
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Minimum Uzunluq</label>
                        <input type="number" class="form-control" name="min_length">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Maximum Uzunluq</label>
                        <input type="number" class="form-control" name="max_length">
                    </div>
                </div>
            `;
            break;
            
        case 'file':
            html = `
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label class="form-label">İcazə Verilən Fayl Tipləri</label>
                        <input type="text" class="form-control" name="allowed_types" 
                               placeholder="pdf,doc,docx">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Maximum Fayl Ölçüsü (MB)</label>
                        <input type="number" class="form-control" name="max_file_size">
                    </div>
                </div>
            `;
            break;
    }
    
    container.innerHTML = html;
}
</script>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kateqoriya əlavə etmə
    const addCategoryForm = document.getElementById('addCategoryForm');
    addCategoryForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        try {
            const response = await fetch('/api/v1/categories', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: this.querySelector('[name="name"]').value
                })
            });
            
            if (response.ok) {
                window.location.reload();
            } else {
                throw new Error('Xəta baş verdi');
            }
        } catch (error) {
            alert('Kateqoriya əlavə edilərkən xəta baş verdi');
        }
    });

    // Sütunları göstərmək
    window.showColumns = async function(categoryId) {
        try {
            const response = await fetch(`/api/v1/categories/${categoryId}/columns`);
            const columns = await response.json();
            
            const tbody = document.querySelector('#columnsTable tbody');
            tbody.innerHTML = columns.data.map(column => `
                <tr>
                    <td>${column.name}</td>
                    <td>${column.data_type}</td>
                    <td>${column.end_date || '-'}</td>
                    <td>
                        <span class="badge bg-${column.is_active ? 'success' : 'danger'}">
                            ${column.is_active ? 'Aktiv' : 'Deaktiv'}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editColumn(${column.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteColumn(${column.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            
            $('#columnsModal').modal('show');
        } catch (error) {
            alert('Sütunlar yüklənərkən xəta baş verdi');
        }
    };
});
</script>
@endpush
@endsection