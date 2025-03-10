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
            <button class="btn btn-primary add-category" data-bs-toggle="modal" data-bs-target="#categoryModal">
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
                                <a href="{{ route('settings.table.index', ['category' => $category->id]) }}" 
                                   class="text-decoration-none text-dark flex-grow-1 {{ $selectedCategory && $selectedCategory->id === $category->id ? 'fw-bold' : '' }}">
                                    {{ $category->name }}
                                    <span class="badge bg-secondary rounded-pill ms-2">{{ $category->columns_count }}</span>
                                </a>
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-switch ms-2">
                                        <input class="form-check-input category-status" type="checkbox" 
                                               data-category-id="{{ $category->id }}"
                                               {{ $category->status ? 'checked' : '' }}>
                                    </div>
                                    <div class="dropdown ms-2">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <button class="dropdown-item edit-category" data-category-id="{{ $category->id }}" data-bs-toggle="modal" data-bs-target="#categoryModal">
                                                    <i class="fas fa-edit me-2"></i>Redaktə et
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item delete-category" data-category-id="{{ $category->id }}">
                                                    <i class="fas fa-trash me-2"></i>Sil
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
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
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sütunlar</h5>
                    @if($selectedCategory)
                        <div>
                            <span class="badge bg-light text-primary me-2">{{ $columns->count() }}</span>
                            <button class="btn btn-light btn-sm add-column" data-category-id="{{ $selectedCategory->id }}" data-bs-toggle="modal" data-bs-target="#columnModal">
                                <i class="fas fa-plus me-1"></i>Yeni Sütun
                            </button>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if(!$selectedCategory)
                        <div class="alert alert-info text-center">
                            Sütunları görmək üçün soldakı siyahıdan bir kateqoriya seçin.
                        </div>
                    @elseif($columns->isEmpty())
                        <div class="alert alert-info text-center">
                            Bu kateqoriyada hələ heç bir sütun yoxdur.
                            <button class="btn btn-primary btn-sm ms-2 add-column" data-category-id="{{ $selectedCategory->id }}" data-bs-toggle="modal" data-bs-target="#columnModal">
                                <i class="fas fa-plus me-1"></i>Sütun əlavə et
                            </button>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;">#</th>
                                        <th>Sütun</th>
                                        <th style="width: 120px;">Növ</th>
                                        <th style="width: 150px;">Son tarix</th>
                                        <th style="width: 100px;">Limit</th>
                                        <th style="width: 100px;">Status</th>
                                        <th style="width: 120px;">Əməliyyatlar</th>
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
                                                        <button class="btn btn-sm btn-outline-secondary set-deadline" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deadlineModal"
                                                                data-column-id="{{ $column->id }}">
                                                            <i class="fas fa-clock"></i>
                                                        </button>
                                                    @else
                                                        <span class="text-muted">--</span>
                                                        <button class="btn btn-sm btn-outline-secondary ms-2 set-deadline" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deadlineModal"
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
                                                    @else
                                                        <span class="text-muted me-2">∞</span>
                                                    @endif
                                                    <button class="btn btn-sm btn-outline-secondary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#updateLimitModal"
                                                            data-column-id="{{ $column->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input column-status" type="checkbox" 
                                                           data-column-id="{{ $column->id }}"
                                                           {{ $column->is_active ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        {{ $column->is_active ? 'Aktiv' : 'Deaktiv' }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-warning edit-column" data-column-id="{{ $column->id }}" data-bs-toggle="modal" data-bs-target="#columnModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-column" data-column-id="{{ $column->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.settings.table.modals.category-modal')
@include('partials.settings.table.modals.column')
@include('partials.settings.table.modals.deadline')
@include('partials.settings.table.modals.limit')

@endsection

@push('scripts')
<script src="{{ asset('js/settings/table/table-utils.js') }}"></script>
<script src="{{ asset('js/settings/table/category-operations.js') }}"></script>
<script src="{{ asset('js/settings/table/column-operations.js') }}"></script>
<script src="{{ asset('js/settings/table/deadline-operations.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script src="{{ asset('js/settings/table/table-init.js') }}"></script>
@endpush