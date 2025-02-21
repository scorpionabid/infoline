@extends('layouts.app')

@section('title', 'Backup İdarəetməsi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Panel</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Tənzimləmələr</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('settings.system.index') }}">Sistem</a></li>
                        <li class="breadcrumb-item active">Backup-lar</li>
                    </ol>
                </div>
                <h4 class="page-title">Backup İdarəetməsi</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Backup Info Card -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Backup Məlumatları</h5>
                        <button type="button" class="btn btn-primary" id="createBackupBtn">
                            <i class="fas fa-plus me-1"></i> Yeni Backup
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h6>Son Backup</h6>
                                <p class="mb-0">
                                    @if($lastBackup = $backups->first())
                                        {{ $lastBackup['created_at'] }}
                                        <br>
                                        <small class="text-muted">
                                            Ölçü: {{ formatBytes($lastBackup['size']) }}
                                        </small>
                                    @else
                                        <span class="text-muted">Heç bir backup tapılmadı</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h6>Ümumi Backup-lar</h6>
                                <p class="mb-0">
                                    {{ count($backups) }} backup
                                    <br>
                                    <small class="text-muted">
                                        Ümumi ölçü: {{ formatBytes($backups->sum('size')) }}
                                    </small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h6>Backup Tezliyi</h6>
                                <p class="mb-0">
                                    Hər gün 00:00-da
                                    <br>
                                    <small class="text-muted">Avtomatik backup</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Backup List Card -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Backup Siyahısı</h5>

                    <div class="table-responsive">
                        <table class="table table-centered mb-0">
                            <thead>
                                <tr>
                                    <th>Fayl Adı</th>
                                    <th>Ölçü</th>
                                    <th>Tarix</th>
                                    <th class="text-end">Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody id="backupList">
                                @forelse($backups as $backup)
                                    <tr id="backup-{{ $backup['name'] }}">
                                        <td>{{ $backup['name'] }}</td>
                                        <td>{{ formatBytes($backup['size']) }}</td>
                                        <td>{{ $backup['created_at'] }}</td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary download-backup"
                                                        data-backup="{{ $backup['name'] }}"
                                                        title="Yüklə">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-backup"
                                                        data-backup="{{ $backup['name'] }}"
                                                        title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            Heç bir backup tapılmadı
                                        </td>
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

@push('scripts')
<script src="{{ asset('js/settings/system/backups.js') }}"></script>
@endpush