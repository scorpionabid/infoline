@extends('layouts.app')

@section('title', 'Sistem Tənzimləmələri')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Panel</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Tənzimləmələr</a></li>
                        <li class="breadcrumb-item active">Sistem</li>
                    </ol>
                </div>
                <h4 class="page-title">Sistem Tənzimləmələri</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Notifications Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Bildiriş Tənzimləmələri</h5>
                        <i class="fas fa-bell text-muted"></i>
                    </div>
                    <p class="text-muted">Email bildirişləri və sistem xəbərdarlıqlarını idarə edin</p>
                    <div class="mt-3">
                        <a href="{{ route('settings.system.notifications.index') }}" class="btn btn-primary">
                            <i class="fas fa-cog me-1"></i> Tənzimləmələr
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backups Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Backup İdarəetməsi</h5>
                        <i class="fas fa-database text-muted"></i>
                    </div>
                    <p class="text-muted">Sistem backup-larını yaradın və idarə edin</p>
                    <div class="mt-3">
                        <a href="{{ route('settings.system.backups.index') }}" class="btn btn-primary">
                            <i class="fas fa-archive me-1"></i> Backup-lar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logs Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Log İdarəetməsi</h5>
                        <i class="fas fa-clipboard-list text-muted"></i>
                    </div>
                    <p class="text-muted">Sistem log-larını izləyin və analiz edin</p>
                    <div class="mt-3">
                        <a href="{{ route('settings.system.logs.index') }}" class="btn btn-primary">
                            <i class="fas fa-list-alt me-1"></i> Log-lar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sistem Məlumatları</h5>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th style="width: 200px;">PHP Versiyası</th>
                                    <td>{{ phpversion() }}</td>
                                </tr>
                                <tr>
                                    <th>Laravel Versiyası</th>
                                    <td>{{ app()->version() }}</td>
                                </tr>
                                <tr>
                                    <th>Server</th>
                                    <td>{{ $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Disk İstifadəsi</th>
                                    <td>
                                        @php
                                            $totalSpace = disk_total_space('/');
                                            $freeSpace = disk_free_space('/');
                                            $usedSpace = $totalSpace - $freeSpace;
                                            $usedPercentage = round(($usedSpace / $totalSpace) * 100);
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $usedPercentage > 80 ? 'bg-danger' : 'bg-success' }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $usedPercentage }}%"
                                                 aria-valuenow="{{ $usedPercentage }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ $usedPercentage }}%
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            İstifadə: {{ formatBytes($usedSpace) }} / {{ formatBytes($totalSpace) }}
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Son Backup</th>
                                    <td>
                                        @if($lastBackup = getLastBackup())
                                            {{ $lastBackup->created_at->format('d.m.Y H:i') }}
                                            <span class="text-muted">({{ $lastBackup->created_at->diffForHumans() }})</span>
                                        @else
                                            Heç bir backup tapılmadı
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
</script>
@endpush