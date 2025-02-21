@extends('layouts.app')

@section('title', 'Cədvəl')

@section('content')
<div class="container-fluid px-4 py-5">
    <!-- Bildirişlər -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Başlıq -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 text-gray-800">Cədvəl Ayarları</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus me-2"></i>Yeni Kateqoriya
        </button>
    </div>

    <div class="row">
        <!-- Kateqoriyalar -->
        <div class="col-md-3">
            <div class="card shadow h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Kateqoriyalar</h5>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($categories as $category)
                        <div class="list-group-item d-flex justify-content-between align-items-center
                              {{ $selectedCategory && $selectedCategory->id === $category->id ? 'active' : '' }}">
                            <a href="{{ route('settings.table.index', ['category' => $category->id]) }}" 
                               class="text-decoration-none {{ $selectedCategory && $selectedCategory->id === $category->id ? 'text-white' : 'text-dark' }}">
                                {{ $category->name }}
                                <span class="badge {{ $selectedCategory && $selectedCategory->id === $category->id ? 'bg-white text-primary' : 'bg-secondary' }} rounded-pill ms-2">
                                    {{ $category->columns_count }}
                                </span>
                            </a>
                            <div class="btn-group">
                                <button type="button" 
                                        class="btn btn-sm {{ $selectedCategory && $selectedCategory->id === $category->id ? 'btn-light' : 'btn-outline-primary' }}"
                                        data-action="edit-category"
                                        data-category-id="{{ $category->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-sm {{ $selectedCategory && $selectedCategory->id === $category->id ? 'btn-light' : 'btn-outline-danger' }}"
                                        data-action="delete-category"
                                        data-category-id="{{ $category->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
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
                    <h5 class="mb-0">
                        @if($selectedCategory)
                            {{ $selectedCategory->name }} - Sütunlar
                        @else
                            Sütunlar
                        @endif
                    </h5>
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
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Ad</th>
                                            <th>Tip</th>
                                            <th>Son Tarix</th>
                                            <th>Limit</th>
                                            <th>Status</th>
                                            <th>Əməliyyatlar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($columns as $column)
                                            <tr>
                                                <td>{{ $column->name }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $column->data_type }}</span>
                                                </td>
                                                <td>
                                                    @if($column->end_date)
                                                        {{ $column->end_date->format('d.m.Y') }}
                                                    @else
                                                        <span class="text-muted">--</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($column->input_limit)
                                                        {{ $column->input_limit }}
                                                    @else
                                                        <span class="text-muted">--</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($column->end_date && $column->end_date->isPast())
                                                        <span class="badge bg-danger">Bitmişdir</span>
                                                    @else
                                                        <span class="badge bg-success">Aktivdir</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary me-1" 
                                                            data-action="edit-column"
                                                            data-column-id="{{ $column->id }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger"
                                                            data-action="delete-column"
                                                            data-column-id="{{ $column->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <p>Bu kateqoriyada hələ heç bir sütun yoxdur.</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-arrow-left fa-2x mb-3"></i>
                            <p>Zəhmət olmasa, kateqoriya seçin.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modallar -->
@include('partials.category-modal')
@include('partials.column-modal')

@endsection

@push('scripts')
<script src="{{ asset('js/settings/table.js') }}"></script>
@endpush