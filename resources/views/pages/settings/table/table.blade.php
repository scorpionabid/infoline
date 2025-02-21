@extends('layouts.app')

@section('title', 'Cədvəl Ayarları')

@section('content')
<div class="container-fluid px-4 py-5">
    @include('partials.alerts')

    <!-- Başlıq -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-0 text-gray-800">Cədvəl Ayarları</h4>
            <p class="text-muted mb-0">Məlumat toplama cədvəllərinin idarə edilməsi</p>
        </div>
        <div class="d-flex gap-2">
            @if($selectedCategory)
                <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cloneCategoryModal">
                    <i class="fas fa-copy me-2"></i>Kateqoriyanı Kopyala
                </button>
            @endif
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus me-2"></i>Yeni Kateqoriya
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Kateqoriyalar -->
        <div class="col-md-3">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Kateqoriyalar</h5>
                    <span class="badge bg-light text-primary">{{ $categories->count() }}</span>
                </div>
                <div class="list-group list-group-flush" id="categoriesList">
                    @forelse($categories as $category)
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('settings.table', ['category' => $category->id]) }}" 
                                   class="text-decoration-none text-dark flex-grow-1 {{ $selectedCategory && $selectedCategory->id === $category->id ? 'fw-bold' : '' }}">
                                    {{ $category->name }}
                                    <span class="badge bg-secondary rounded-pill ms-2">{{ $category->columns_count }}</span>
                                </a>
                                <div class="form-check form-switch ms-2">
                                    <input class="form-check-input category-status" type="checkbox" 
                                           data-category-id="{{ $category->id }}"
                                           {{ $category->status ? 'checked' : '' }}>
                                </div>
                            </div>
                            @if($category->description)
                                <small class="text-muted d-block mt-1">{{ $category->description }}</small>
                            @endif
                        </div>
                    @empty
                        <div class="list-group-item text-muted text-center">
                            Kateqoriya tapılmadı
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sütunlar -->
        <div class="col-md-9">
            <div class="card shadow h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">
                            @if($selectedCategory)
                                {{ $selectedCategory->name }} - Sütunlar
                                <span class="badge bg-primary ms-2">{{ $columns->count() }}</span>
                            @else
                                Sütunlar
                            @endif
                        </h5>
                        @if($selectedCategory && $selectedCategory->description)
                            <small class="text-muted">{{ $selectedCategory->description }}</small>
                        @endif
                    </div>
                    @if($selectedCategory)
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addColumnModal">
                            <i class="fas fa-plus me-2"></i>Yeni Sütun
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($selectedCategory)
                        @if($columns->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover" id="columnsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px">#</th>
                                            <th>Ad</th>
                                            <th>Tip</th>
                                            <th>Son Tarix</th>
                                            <th>Limit</th>
                                            <th>Status</th>
                                            <th style="width: 150px">Əməliyyatlar</th>
                                        </tr>
                                    </thead>
                                    <tbody class="sortable">
                                        @foreach($columns as $column)
                                            <tr data-column-id="{{ $column->id }}">
                                                <td>
                                                    <i class="fas fa-grip-vertical text-muted cursor-move"></i>
                                                </td>
                                                <td>
                                                    <div class="fw-bold">{{ $column->name }}</div>
                                                    @if($column->description)
                                                        <small class="text-muted">{{ $column->description }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $column->type }}</span>
                                                    @if($column->required)
                                                        <span class="badge bg-danger ms-1">Məcburi</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($column->end_date)
                                                            <span class="me-2 {{ $column->isExpired() ? 'text-danger' : 'text-success' }}">
                                                                {{ $column->end_date->format('d.m.Y') }}
                                                            </span>
                                                            <button class="btn btn-sm btn-outline-secondary" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#updateDeadlineModal"
                                                                    data-column-id="{{ $column->id }}">
                                                                <i class="fas fa-clock"></i>
                                                            </button>
                                                        @else
                                                            <span class="text-muted">--</span>
                                                            <button class="btn btn-sm btn-outline-secondary ms-2" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#updateDeadlineModal"
                                                                    data-column-id="{{ $column->id }}">
                                                                <i class="fas fa-clock"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($column->input_limit)
                                                            <span class="me-2">{{ $column->input_limit }}</span>
                                                            <button class="btn btn-sm btn-outline-secondary" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#updateLimitModal"
                                                                    data-column-id="{{ $column->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        @else
                                                            <span class="text-muted">--</span>
                                                            <button class="btn btn-sm btn-outline-secondary ms-2" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#updateLimitModal"
                                                                    data-column-id="{{ $column->id }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input column-status" type="checkbox" 
                                                               data-column-id="{{ $column->id }}"
                                                               {{ $column->status ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-sm btn-info" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editColumnModal"
                                                                data-column-id="{{ $column->id }}">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger delete-column" 
                                                                data-column-id="{{ $column->id }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <img src="{{ asset('images/empty-table.svg') }}" alt="Empty Table" class="mb-3" style="width: 150px">
                                <h5>Sütun tapılmadı</h5>
                                <p class="text-muted">Bu kateqoriyaya aid heç bir sütun yoxdur</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addColumnModal">
                                    <i class="fas fa-plus me-2"></i>Yeni Sütun Əlavə Et
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('images/select-category.svg') }}" alt="Select Category" class="mb-3" style="width: 150px">
                            <h5>Kateqoriya Seçin</h5>
                            <p class="text-muted">Sütunları görmək üçün sol tərəfdən bir kateqoriya seçin</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.settings.table.modals.category')
@include('partials.settings.table.modals.column')
@include('partials.settings.table.modals.deadline')
@include('partials.settings.table.modals.limit')
@include('partials.settings.table.modals.clone-category')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    // Sütunların sırasını dəyişmək üçün
    new Sortable(document.querySelector('.sortable'), {
        handle: '.cursor-move',
        animation: 150,
        onEnd: function(evt) {
            const columns = [];
            document.querySelectorAll('[data-column-id]').forEach((el, index) => {
                columns.push({
                    id: el.dataset.columnId,
                    order: index
                });
            });

            // Sıranı yeniləmək üçün API sorğusu
            axios.post('{{ route("settings.table.columns.order") }}', { columns })
                .then(response => {
                    toastr.success(response.data.message);
                })
                .catch(error => {
                    toastr.error('Xəta baş verdi');
                    console.error(error);
                });
        }
    });

    // Kateqoriya statusunu dəyişmək
    document.querySelectorAll('.category-status').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const categoryId = this.dataset.categoryId;
            axios.patch(`/settings/table/categories/${categoryId}/status`, {
                status: this.checked
            })
            .then(response => {
                toastr.success(response.data.message);
            })
            .catch(error => {
                this.checked = !this.checked;
                toastr.error('Xəta baş verdi');
                console.error(error);
            });
        });
    });

    // Sütun statusunu dəyişmək
    document.querySelectorAll('.column-status').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const columnId = this.dataset.columnId;
            axios.patch(`/settings/table/columns/${columnId}/status`, {
                status: this.checked
            })
            .then(response => {
                toastr.success(response.data.message);
            })
            .catch(error => {
                this.checked = !this.checked;
                toastr.error('Xəta baş verdi');
                console.error(error);
            });
        });
    });

    // Sütunu silmək
    document.querySelectorAll('.delete-column').forEach(button => {
        button.addEventListener('click', function() {
            const columnId = this.dataset.columnId;
            if (confirm('Bu sütunu silmək istədiyinizə əminsiniz?')) {
                axios.delete(`/settings/table/columns/${columnId}`)
                    .then(response => {
                        toastr.success(response.data.message);
                        location.reload();
                    })
                    .catch(error => {
                        toastr.error('Xəta baş verdi');
                        console.error(error);
                    });
            }
        });
    });
</script>
@endpush