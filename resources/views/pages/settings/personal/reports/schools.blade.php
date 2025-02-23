@extends('layouts.app')

@section('title', 'Məktəblər üzrə Hesabat')

@section('content')
<div class="container-fluid">
    <!-- Başlıq -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Məktəblər üzrə Hesabat</h1>
        <div>
            <button class="btn btn-success" id="exportExcel">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button class="btn btn-danger" id="exportPdf">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
        </div>
    </div>

    <!-- Filtrlər -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtrlər</h6>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row">
                <div class="col-md-3 mb-3">
                    <label for="region_id">Region</label>
                    <select class="form-control" id="region_id" name="region_id">
                        <option value="">Hamısı</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="sector_id">Sektor</label>
                    <select class="form-control" id="sector_id" name="sector_id">
                        <option value="">Hamısı</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="">Hamısı</option>
                        <option value="1">Aktiv</option>
                        <option value="0">Deaktiv</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-filter"></i> Filtr
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistika -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Ümumi Məktəb
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $schools->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Aktiv Məktəblər
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $schools->where('status', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Orta Məlumat Sayı
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ round($schools->avg('data_count')) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Ümumi Admin
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $schools->sum('admins_count') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cədvəl -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="schoolsTable">
                    <thead>
                        <tr>
                            <th>Məktəb</th>
                            <th>Region</th>
                            <th>Sektor</th>
                            <th>Admin</th>
                            <th>Məlumat Sayı</th>
                            <th>Status</th>
                            <th>Əməliyyatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schools as $school)
                            <tr>
                                <td>{{ $school->name }}</td>
                                <td>{{ $school->sector->region->name }}</td>
                                <td>{{ $school->sector->name }}</td>
                                <td>{{ $school->admin?->name ?? 'Təyin edilməyib' }}</td>
                                <td>{{ $school->data_count }}</td>
                                <td>
                                    <span class="badge badge-{{ $school->status ? 'success' : 'warning' }}">
                                        {{ $school->status ? 'Aktiv' : 'Deaktiv' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('settings.personal.schools.show.data', $school) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // DataTables inteqrasiyası
    const table = $('#schoolsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Azerbaijan.json'
        }
    });

    // Region seçildikdə sektorları yükləmə
    $('#region_id').change(function() {
        const regionId = $(this).val();
        
        if (!regionId) {
            $('#sector_id').html('<option value="">Hamısı</option>');
            return;
        }

        $.get(`/api/regions/${regionId}/sectors`, function(sectors) {
            let options = '<option value="">Hamısı</option>';
            sectors.forEach(sector => {
                options += `<option value="${sector.id}">${sector.name}</option>`;
            });
            $('#sector_id').html(options);
        });
    });

    // Excel export
    $('#exportExcel').click(function() {
        window.location.href = '{{ route("settings.personal.reports.schools.excel") }}' + 
            '?' + $('#filterForm').serialize();
    });

    // PDF export
    $('#exportPdf').click(function() {
        window.location.href = '{{ route("settings.personal.reports.schools.pdf") }}' + 
            '?' + $('#filterForm').serialize();
    });

    // Filtr formu
    $('#filterForm').submit(function(e) {
        e.preventDefault();
        
        table.ajax.url('{{ route("settings.personal.reports.schools.data") }}' + 
            '?' + $(this).serialize()).load();
    });
});
</script>
@endpush