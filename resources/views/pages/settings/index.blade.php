@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @include('partials.navigation-tabs')

    <div class="row mt-4">
        <div class="col-12">
            <!-- İstifadəçinin roluna əsasən müəssisə adı -->
            <div class="alert alert-info">
                @if(auth()->user()->isSuperAdmin())
                    <h4 class="mb-0">{{ auth()->user()->region->name ?? 'Region müəyyən edilməyib' }}</h4>
                @elseif(auth()->user()->isSectorAdmin())
                    <h4 class="mb-0">{{ auth()->user()->sector->name ?? 'Sektor müəyyən edilməyib' }}</h4>
                @else
                    <h4 class="mb-0">{{ auth()->user()->school->name ?? 'Məktəb müəyyən edilməyib' }}</h4>
                @endif
            </div>

            <!-- Statistika Kartları -->
            <div class="row mb-4">
                <!-- Kateqoriyalar -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary h-100 py-2">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Kateqoriyalar
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $stats['categories'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-folder fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aktiv Sütunlar -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success h-100 py-2">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Aktiv Sütunlar
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $stats['activeColumns'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-columns fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Məlumat Sayı -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info h-100 py-2">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Ümumi Məlumatlar
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $stats['dataValues'] ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-database fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- İstifadəçilər -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning h-100 py-2">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        İstifadəçilər
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $stats['users'] ?? 0 }}
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

            <!-- Sürətli Əməliyyatlar -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Sürətli Əməliyyatlar</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Məlumat İdarəetməsi</h5>
                            <a href="{{ route('settings.table') }}" class="btn btn-primary btn-block mb-2">
                                <i class="fas fa-table"></i> Cədvəl Görünüşü
                            </a>
                            <a href="{{ route('settings.import') }}" class="btn btn-success btn-block mb-2">
                                <i class="fas fa-file-import"></i> Məlumat İdxalı
                            </a>
                            <a href="{{ route('settings.export') }}" class="btn btn-info btn-block">
                                <i class="fas fa-file-export"></i> Məlumat İxracı
                            </a>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Personal İdarəetməsi</h5>
                            <a href="{{ route('settings.personal') }}" class="btn btn-warning btn-block mb-2">
                                <i class="fas fa-users"></i> Personal Siyahısı
                            </a>
                            <a href="{{ route('settings.permissions') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-key"></i> İcazələr
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection