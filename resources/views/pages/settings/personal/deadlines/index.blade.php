@extends('layouts.app')

@section('title', 'Deadline İdarəetməsi')

@section('content')
<div class="container-fluid">
    <!-- Başlıq -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Deadline İdarəetməsi</h1>
        <button class="btn btn-primary" data-toggle="modal" data-target="#addDeadlineModal">
            <i class="fas fa-plus"></i> Yeni Deadline
        </button>
    </div>

    <!-- Deadline Cədvəli -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Başlıq</th>
                            <th>Kateqoriya</th>
                            <th>Son Tarix</th>
                            <th>Prioritet</th>
                            <th>Təyin edilib</th>
                            <th>Status</th>
                            <th>Əməliyyatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deadlines as $deadline)
                            <tr>
                                <td>{{ $deadline->title }}</td>
                                <td>{{ $deadline->category->name }}</td>
                                <td>
                                    <span class="@if($deadline->isOverdue()) text-danger @endif">
                                        {{ $deadline->due_date->format('d.m.Y H:i') }}
                                    </span>
                                </td>
                                <td>
                                    @switch($deadline->priority)
                                        @case('high')
                                            <span class="badge badge-danger">Yüksək</span>
                                            @break
                                        @case('medium')
                                            <span class="badge badge-warning">Orta</span>
                                            @break
                                        @default
                                            <span class="badge badge-info">Aşağı</span>
                                    @endswitch
                                </td>
                                <td>{{ $deadline->assignee->name }}</td>
                                <td>
                                    <span class="badge badge-{{ $deadline->status ? 'success' : 'warning' }}">
                                        {{ $deadline->status ? 'Tamamlanıb' : 'Davam edir' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" 
                                                class="btn btn-sm btn-primary edit-deadline"
                                                data-deadline="{{ $deadline->id }}"
                                                data-title="{{ $deadline->title }}"
                                                data-description="{{ $deadline->description }}"
                                                data-due-date="{{ $deadline->due_date->format('Y-m-d\TH:i') }}"
                                                data-category="{{ $deadline->category_id }}"
                                                data-priority="{{ $deadline->priority }}"
                                                data-assigned="{{ $deadline->assigned_to }}"
                                                data-status="{{ $deadline->status }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger delete-deadline"
                                                data-deadline="{{ $deadline->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Deadline tapılmadı</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Səhifələmə -->
            <div class="mt-3">
                {{ $deadlines->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Yeni Deadline Modal -->
<div class="modal fade" id="addDeadlineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Deadline</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addDeadlineForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title">Başlıq</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Təsvir</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="due_date">Son Tarix</label>
                        <input type="datetime-local" class="form-control" id="due_date" name="due_date" required>
                    </div>
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
                        <label for="priority">Prioritet</label>
                        <select class="form-control" id="priority" name="priority" required>
                            <option value="low">Aşağı</option>
                            <option value="medium">Orta</option>
                            <option value="high">Yüksək</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="assigned_to">Təyin ediləcək</label>
                        <select class="form-control" id="assigned_to" name="assigned_to" required>
                            <option value="">Seçin</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
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

<!-- Deadline Redaktə Modal -->
<div class="modal fade" id="editDeadlineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deadline Redaktə Et</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editDeadlineForm">
                <input type="hidden" id="edit_deadline_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_title">Başlıq</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Təsvir</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_due_date">Son Tarix</label>
                        <input type="datetime-local" class="form-control" id="edit_due_date" name="due_date" required>
                    </div>
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
                        <label for="edit_priority">Prioritet</label>
                        <select class="form-control" id="edit_priority" name="priority" required>
                            <option value="low">Aşağı</option>
                            <option value="medium">Orta</option>
                            <option value="high">Yüksək</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_assigned_to">Təyin ediləcək</label>
                        <select class="form-control" id="edit_assigned_to" name="assigned_to" required>
                            <option value="">Seçin</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_status" name="status">
                            <label class="custom-control-label" for="edit_status">Tamamlanıb</label>
                        </div>
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
    $('#category_id, #edit_category_id, #assigned_to, #edit_assigned_to').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Yeni deadline əlavə etmə
    $('#addDeadlineForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: "{{ route('settings.personal.deadlines.store') }}",
            type: 'POST',
            data: {
                title: $('#title').val(),
                description: $('#description').val(),
                due_date: $('#due_date').val(),
                category_id: $('#category_id').val(),
                priority: $('#priority').val(),
                assigned_to: $('#assigned_to').val(),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#addDeadlineModal').modal('hide');
                toastr.success(response.message);
                location.reload();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message || 'Xəta baş verdi');
            }
        });
    });

    // Deadline redaktə etmə
    $('.edit-deadline').click(function() {
        const deadline = $(this).data();
        
        $('#edit_deadline_id').val(deadline.deadline);
        $('#edit_title').val(deadline.title);
        $('#edit_description').val(deadline.description);
        $('#edit_due_date').val(deadline.dueDate);
        $('#edit_category_id').val(deadline.category).trigger('change');
        $('#edit_priority').val(deadline.priority);
        $('#edit_assigned_to').val(deadline.assigned).trigger('change');
        $('#edit_status').prop('checked', deadline.status);

        $('#editDeadlineModal').modal('show');
    });

    $('#editDeadlineForm').submit(function(e) {
        e.preventDefault();
        const id = $('#edit_deadline_id').val();
        
        $.ajax({
            url: `/settings/personal/deadlines/${id}`,
            type: 'PUT',
            data: {
                title: $('#edit_title').val(),
                description: $('#edit_description').val(),
                due_date: $('#edit_due_date').val(),
                category_id: $('#edit_category_id').val(),
                priority: $('#edit_priority').val(),
                assigned_to: $('#edit_assigned_to').val(),
                status: $('#edit_status').is(':checked'),
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#editDeadlineModal').modal('hide');
                toastr.success(response.message);
                location.reload();
            },
            error: function(xhr) {
                toastr.error(xhr.responseJSON.message || 'Xəta baş verdi');
            }
        });
    });

    // Deadline silmə
    $('.delete-deadline').click(function() {
        const id = $(this).data('deadline');
        
        Swal.fire({
            title: 'Əminsiniz?',
            text: "Bu deadline-ı silmək istədiyinizə əminsiniz?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Bəli, sil!',
            cancelButtonText: 'Xeyr'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/settings/personal/deadlines/${id}`,
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
});
</script>
@endpush