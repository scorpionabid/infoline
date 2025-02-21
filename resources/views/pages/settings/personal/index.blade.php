@extends('layouts.app')

@section('title', 'Şəxsi İdarəetmə')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Şəxsi İdarəetmə</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Regionlar -->
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-1 mt-1"><span data-plugin="counterup">{{ $regions_count }}</span></h4>
                            <p class="text-muted mb-0">Regionlar</p>
                        </div>
                        <div class="avatar-sm rounded-circle bg-primary align-self-center">
                            <span class="avatar-title rounded-circle bg-primary">
                                <i class="fas fa-map-marker-alt font-size-24"></i>
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('settings.personal.regions.index') }}" class="btn btn-primary btn-sm mt-3 w-100">
                        <i class="fas fa-cog me-1"></i> İdarə et
                    </a>
                </div>
            </div>
        </div>

        <!-- Sektorlar -->
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-1 mt-1"><span data-plugin="counterup">{{ $sectors_count }}</span></h4>
                            <p class="text-muted mb-0">Sektorlar</p>
                        </div>
                        <div class="avatar-sm rounded-circle bg-success align-self-center">
                            <span class="avatar-title rounded-circle bg-success">
                                <i class="fas fa-sitemap font-size-24"></i>
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('settings.personal.sectors.index') }}" class="btn btn-success btn-sm mt-3 w-100">
                        <i class="fas fa-cog me-1"></i> İdarə et
                    </a>
                </div>
            </div>
        </div>

        <!-- Məktəblər -->
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-1 mt-1"><span data-plugin="counterup">{{ $schools_count }}</span></h4>
                            <p class="text-muted mb-0">Məktəblər</p>
                        </div>
                        <div class="avatar-sm rounded-circle bg-info align-self-center">
                            <span class="avatar-title rounded-circle bg-info">
                                <i class="fas fa-school font-size-24"></i>
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('settings.personal.schools.index') }}" class="btn btn-info btn-sm mt-3 w-100">
                        <i class="fas fa-cog me-1"></i> İdarə et
                    </a>
                </div>
            </div>
        </div>

        <!-- İstifadəçilər -->
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-1 mt-1"><span data-plugin="counterup">{{ $users_count }}</span></h4>
                            <p class="text-muted mb-0">İstifadəçilər</p>
                        </div>
                        <div class="avatar-sm rounded-circle bg-warning align-self-center">
                            <span class="avatar-title rounded-circle bg-warning">
                                <i class="fas fa-users font-size-24"></i>
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('settings.personal.users.index') }}" class="btn btn-warning btn-sm mt-3 w-100">
                        <i class="fas fa-cog me-1"></i> İdarə et
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Son Fəaliyyətlər -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Son Fəaliyyətlər</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tarix</th>
                                    <th>İstifadəçi</th>
                                    <th>Əməliyyat</th>
                                    <th>Detallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                <tr>
                                    <td>{{ $activity->created_at->format('d.m.Y H:i') }}</td>
                                    <td>{{ $activity->causer->full_name ?? '-' }}</td>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ $activity->properties['details'] ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Son fəaliyyət tapılmadı</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="{{ asset('assets/libs/counter/counter.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('js')
<script src="{{ asset('assets/libs/counter/counter.min.js') }}"></script>
<script>
    $("[data-plugin='counterup']").each(function(index, el) {
        $(this).counterUp({
            delay: 10,
            time: 1000
        });
    });
</script>
@endpush