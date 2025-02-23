@extends('layouts.app')

@section('title', 'İstifadəçilər')

@section('content')
<div class="container-fluid">
    <!-- Page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">İstifadəçilər</h4>
            </div>
        </div>
    </div>

    <!-- Filters and actions -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <a href="{{ route('settings.personal.users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Yeni İstifadəçi
                            </a>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <input type="text" class="form-control w-auto me-2" id="searchInput" placeholder="Axtar...">
                                <select class="form-select w-auto" id="userTypeFilter">
                                    <option value="">Bütün tiplər</option>
                                    @foreach($user_types as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Ad Soyad</th>
                                    <th>Email</th>
                                    <th>Tip</th>
                                    <th>Region</th>
                                    <th>Sektor</th>
                                    <th>Məktəb</th>
                                    <th>Status</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->full_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user_types[$user->user_type->value] ?? $user->user_type->value }}</td>
                                    <td>{{ $user->region->name ?? '-' }}</td>
                                    <td>{{ $user->sector->name ?? '-' }}</td>
                                    <td>{{ $user->school->name ?? '-' }}</td>
                                    <td>
                                        @if($user->status === 'active')
                                            <span class="badge bg-success">Aktiv</span>
                                        @else
                                            <span class="badge bg-danger">Deaktiv</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{ route('settings.personal.users.edit', $user->id) }}" class="dropdown-item">
                                                    <i class="fas fa-edit text-primary"></i> Redaktə et
                                                </a>
                                                <a href="javascript:void(0);" class="dropdown-item toggle-status" 
                                                   data-id="{{ $user->id }}" 
                                                   data-status="{{ $user->status }}">
                                                    @if($user->status === 'active')
                                                        <i class="fas fa-ban text-danger"></i> Deaktiv et
                                                    @else
                                                        <i class="fas fa-check text-success"></i> Aktiv et
                                                    @endif
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">İstifadəçi tapılmadı</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('js')
<script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Select2 initialization
    $('.form-select').select2();

    // Search functionality
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // User type filter
    $('#userTypeFilter').on('change', function() {
        var value = $(this).val().toLowerCase();
        if (value) {
            $("table tbody tr").filter(function() {
                $(this).toggle($(this).find("td:eq(2)").text().toLowerCase().indexOf(value) > -1)
            });
        } else {
            $("table tbody tr").show();
        }
    });

    // Status toggle
    $('.toggle-status').on('click', function() {
        var userId = $(this).data('id');
        var currentStatus = $(this).data('status');
        var newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        var $button = $(this);

        $.ajax({
            url: '/settings/personal/users/' + userId + '/toggle-status',
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                // Reload page to show updated status
                location.reload();
            },
            error: function(xhr) {
                alert('Xəta baş verdi!');
            }
        });
    });
});
</script>
@endpush