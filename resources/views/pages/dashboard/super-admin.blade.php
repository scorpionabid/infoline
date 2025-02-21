@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="fas fa-tachometer-alt"></i> SuperAdmin Dashboard</h2>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Regionlar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $regionCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marked-alt fa-2x text-gray-300"></i>
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
                                Sektorlar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $sectorCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
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
                                Məktəblər</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $schoolCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
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
                                İstifadəçilər</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userCount ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Son Fəaliyyətlər</h6>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        @forelse($recentActivities ?? [] as $activity)
                        <div class="activity-item">
                            <div class="activity-content">
                                <div class="activity-header">
                                    <i class="fas {{ $activity->icon ?? 'fa-circle' }} text-{{ $activity->type ?? 'primary' }}"></i>
                                    <span class="activity-time">{{ $activity->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="activity-description">
                                    {{ $activity->description }}
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted">Heç bir fəaliyyət tapılmadı.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Sürətli Keçidlər</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('settings.personal.regions.index') }}" class="card h-100 border-left-primary">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Region İdarəetməsi
                                    </div>
                                    <div class="small">Regionları və sektor adminlərini idarə edin</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('settings.personal.sectors.index') }}" class="card h-100 border-left-success">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Sektor İdarəetməsi
                                    </div>
                                    <div class="small">Sektorları və məktəb adminlərini idarə edin</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('settings.table.category.index') }}" class="card h-100 border-left-info">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Cədvəl Ayarları
                                    </div>
                                    <div class="small">Kateqoriya və sütunları idarə edin</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('settings.system.index') }}" class="card h-100 border-left-warning">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Sistem Ayarları
                                    </div>
                                    <div class="small">Sistem parametrlərini tənzimləyin</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection