@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-chart-pie"></i> Sektor İdarəetmə Paneli</h2>
        </div>
        <div class="col-md-6 text-end">
            @php
                $user = auth()->user();
                $sector = $user->sector;
                $region = $user->region;
            @endphp
            <p class="text-muted">
                <i class="fas fa-map-marker-alt"></i> Region: {{ $region->name ?? 'Təyin edilməyib' }} / 
                <i class="fas fa-building"></i> Sektor: {{ $sector->name ?? 'Təyin edilməyib' }}
            </p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Ümumi Məktəb Sayı</h5>
                    <h2>{{ $sector->schools->count() ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Aktiv Məktəblər</h5>
                    <h2>{{ $sector->schools->where('deleted_at', null)->count() ?? 0 }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Məktəb Adminləri</h5>
                    <h2>{{ $sector->schools->sum(function($school) { 
                        return $school->admins->count(); 
                    }) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Schools List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Məktəblər</h3>
            <div class="card-tools">
                <input type="text" class="form-control" id="schoolSearch" placeholder="Məktəb axtar...">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Məktəb Adı</th>
                            <th>UTIS Kod</th>
                            <th>Əlaqə Nömrəsi</th>
                            <th>Email</th>
                            <th>Admin</th>
                            <th>Status</th>
                            <th>Əməliyyatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($sector->schools ?? [] as $school)
                        <tr>
                            <td>{{ $school->name }}</td>
                            <td>{{ $school->utis_code }}</td>
                            <td>{{ $school->phone }}</td>
                            <td>{{ $school->email }}</td>
                            <td>
                                @foreach($school->admins as $admin)
                                    <span class="badge bg-info">{{ $admin->username }}</span>
                                @endforeach
                            </td>
                            <td>
                                @if($school->deleted_at)
                                    <span class="badge bg-danger">Deaktiv</span>
                                @else
                                    <span class="badge bg-success">Aktiv</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info view-school" data-id="{{ $school->id }}">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning edit-school" data-id="{{ $school->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Məktəb tapılmadı</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Məktəb axtarışı
    $("#schoolSearch").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // Məktəb detallı baxış
    $(".view-school").click(function() {
        var schoolId = $(this).data('id');
        // Ajax sorğu və ya modal açılışı
    });

    // Məktəb redaktə
    $(".edit-school").click(function() {
        var schoolId = $(this).data('id');
        // Ajax sorğu və ya redaktə səhifəsinə yönləndirmə
    });
});
</script>
@endpush
@endsection