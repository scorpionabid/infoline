@extends('layouts.app')

@section('title', 'Məktəb Məlumatları')

@section('content')
<div class="container-fluid">
    <!-- Başlıq -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $school->name }}</h1>
            <p class="mb-0 text-muted">
                {{ $school->sector->region->name }} / {{ $school->sector->name }}
            </p>
        </div>
        <div>
            <a href="{{ route('settings.personal.schools.edit', $school) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Redaktə Et
            </a>
            <a href="{{ route('settings.personal.schools.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Məlumat Kartları -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Tamamlanma
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $dataCompletion }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Son Yeniləmə
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $school->updated_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Kateqoriyalar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $categories->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Status
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $school->status ? 'Aktiv' : 'Deaktiv' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Məlumat Cədvəli -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Məlumatlar</h6>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addDataModal">
                <i class="fas fa-plus"></i> Yeni Məlumat
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kateqoriya</th>
                            <th>Məlumat</th>
                            <th>Tarix</th>
                            <th>Status</th>
                            <th>Əməliyyatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr>
                                <td>{{ $item->category->name }}</td>
                                <td>
                                    @if(strlen($item->content) > 100)
                                        {{ Str::limit($item->content, 100) }}
                                        <a href="#" class="show-more" data-content="{{ $item->content }}">
                                            daha çox
                                        </a>
                                    @else
                                        {{ $item->content }}
                                    @endif
                                </td>
                                <td>{{ $item->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-{{ $item->status ? 'success' : 'warning' }}">
                                        {{ $item->status ? 'Təsdiqlənib' : 'Gözləmədə' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" 
                                                class="btn btn-sm btn-primary edit-data"
                                                data-id="{{ $item->id }}"
                                                data-category="{{ $item->category_id }}"
                                                data-content="{{ $item->content }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger delete-data"
                                                data-id="{{ $item->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Məlumat tapılmadı</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Səhifələmə -->
            <div class="mt-3">
                {{ $data->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Yeni Məlumat Modal -->
<div class="modal fade" id="addDataModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Məlumat</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addDataForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="category_id">Kateqoriya</label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Seçin</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="content">Məlumat</label>
                        <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Bağla</button>
                    <button type="submit" class="btn btn-primary">Əlavə Et</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Məlumat Redaktə Modal -->
<div class="modal fade" id="editDataModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Məlumatı Redaktə Et</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editDataForm">
                <input type="hidden" id="edit_data_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_category_id">Kateqoriya</label>
                        <select class="form-control" id="edit_category_id" name="category_id" required>
                            <option value="">Seçin</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_content">Məlumat</label>
                        <textarea class="form-control" id="edit_content" name="content" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Bağla</button>
                    <button type="submit" class="btn btn-primary">Yadda Saxla</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Select2 inteqrasiyası
    $('#category_id, #edit_category_id').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Yeni məlumat əlavə etmə
    $('#addDataForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: "{{ route('settings.personal.schools.data.store', $school) }}",
            type: 'POST',
            data: {
                category_id: $('#category_id').val(),
                content: $('#content').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#addDataModal').modal('hide');
                toastr.success(response.message);
                location.reload();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message || 'Xəta baş verdi');
            }
        });
    });

    // Məlumat redaktə etmə
    $('.edit-data').click(function() {
        const id = $(this).data('id');
        const categoryId = $(this).data('category');
        const content = $(this).data('content');

        $('#edit_data_id').val(id);
        $('#edit_category_id').val(categoryId).trigger('change');
        $('#edit_content').val(content);

        $('#editDataModal').modal('show');
    });

    $('#editDataForm').submit(function(e) {
        e.preventDefault();
        const id = $('#edit_data_id').val();
        
        $.ajax({
            url: "{{ route('settings.personal.schools.data.update', ['id' => ':id']) }}".replace(':id', id),
            type: 'PUT',
            data: {
                category_id: $('#edit_category_id').val(),
                content: $('#edit_content').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#editDataModal').modal('hide');
                toastr.success(response.message);
                location.reload();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message || 'Xəta baş verdi');
            }
        });
    });

    // Məlumat silmə
    $('.delete-data').click(function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Əminsiniz?',
            text: "Bu məlumatı silmək istədiyinizə əminsiniz?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Bəli, sil!',
            cancelButtonText: 'Xeyr'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('settings.personal.schools.data.destroy', ['id' => ':id']) }}".replace(':id', id),
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        toastr.success(response.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message || 'Xəta baş verdi');
                    }
                });
            }
        });
    });

    // Məlumatın tam mətnini göstərmə
    $('.show-more').click(function(e) {
        e.preventDefault();
        const content = $(this).data('content');
        
        Swal.fire({
            title: 'Tam Mətn',
            text: content,
            confirmButtonText: 'Bağla'
        });
    });
});
</script>
@endpush