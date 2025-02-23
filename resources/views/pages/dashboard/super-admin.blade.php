{{-- resources/views/pages/dashboard/super-admin.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Regionlar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['regionCount'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marked-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Similar cards for sectors, schools, users --}}
    </div>

    {{-- Error Message if exists --}}
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    {{-- Filters --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Məlumat Filtirləri</h6>
        </div>
        <div class="card-body">
            <form id="filterForm" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Sektor</label>
                        <select name="sector_id" class="form-control">
                            <option value="">Bütün sektorlar</option>
                            @foreach($sectors as $sector)
                                <option value="{{ $sector->id }}" {{ request('sector_id') == $sector->id ? 'selected' : '' }}>
                                    {{ $sector->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Kateqoriya</label>
                        <select name="category_id" class="form-control">
                            <option value="">Bütün kateqoriyalar</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">Bütün</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Gözləyir</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Tamamlanıb</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Vaxtı keçib</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filtrlə
                            </button>
                            
                            <button type="submit" name="export" value="1" class="btn btn-success">
                                <i class="fas fa-download"></i> Export
                            </button>

                            <a href="{{ route('dashboard.super-admin') }}" class="btn btn-secondary">
                                <i class="fas fa-redo"></i> Sıfırla
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>Məktəb</th>
                            <th>Sektor</th>
                            <th>Kateqoriya</th>
                            <th>Son yenilənmə</th>
                            <th>Status</th>
                            <th>Tamamlanma</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schoolData as $data)
                            <tr>
                                <td>{{ $data->school->name }}</td>
                                <td>{{ $data->school->sector->name }}</td>
                                <td>{{ $data->category->name }}</td>
                                <td>{{ $data->updated_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-{{ $data->getStatusColor() }}">
                                        {{ $data->getStatusText() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" 
                                            style="width: {{ $data->completion_percentage }}%">
                                            {{ $data->completion_percentage }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Məlumat tapılmadı</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $schoolData->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // DataTables initialization
    $('#dataTable').DataTable({
        "pageLength": 15,
        "ordering": true,
        "info": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Azerbaijan.json"
        }
    });
});
</script>
@endpush