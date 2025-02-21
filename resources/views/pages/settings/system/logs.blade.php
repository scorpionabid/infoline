@extends('layouts.app')

@section('title', 'Log İdarəetməsi')

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
                        <li class="breadcrumb-item active">Log-lar</li>
                    </ol>
                </div>
                <h4 class="page-title">Log İdarəetməsi</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <!-- Log Stats Card -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h6>Ümumi Log-lar</h6>
                                <p class="mb-0">
                                    {{ count($logs) }} fayl
                                    <br>
                                    <small class="text-muted">
                                        Ümumi ölçü: {{ formatBytes($logs->sum('size')) }}
                                    </small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h6>Bu günkü Log-lar</h6>
                                <p class="mb-0">
                                    {{ $logs->where('updated_at', '>=', today())->count() }} fayl
                                    <br>
                                    <small class="text-muted">
                                        {{ now()->format('d.m.Y') }}
                                    </small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h6>Xəta Log-ları</h6>
                                <p class="mb-0">
                                    {{ $errorCount ?? 0 }} xəta
                                    <br>
                                    <small class="text-muted">Son 24 saat</small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded p-3">
                                <h6>Log Saxlama</h6>
                                <p class="mb-0">
                                    30 gün
                                    <br>
                                    <small class="text-muted">Avtomatik təmizləmə</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Log List Card -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Log Siyahısı</h5>
                        
                        <div class="d-flex gap-2">
                            <!-- Search -->
                            <div class="search-box">
                                <input type="text" class="form-control" id="logSearch" 
                                       placeholder="Log axtar...">
                                <i class="fas fa-search search-icon"></i>
                            </div>

                            <!-- Date Filter -->
                            <input type="date" class="form-control" id="logDateFilter"
                                   value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered mb-0">
                            <thead>
                                <tr>
                                    <th>Fayl Adı</th>
                                    <th>Ölçü</th>
                                    <th>Son Dəyişiklik</th>
                                    <th class="text-end">Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody id="logList">
                                @forelse($logs as $log)
                                    <tr id="log-{{ $log['name'] }}">
                                        <td>{{ $log['name'] }}</td>
                                        <td>{{ formatBytes($log['size']) }}</td>
                                        <td>{{ $log['updated_at'] }}</td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info view-log"
                                                        data-log="{{ $log['name'] }}"
                                                        title="Bax">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger delete-log"
                                                        data-log="{{ $log['name'] }}"
                                                        title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            Heç bir log faylı tapılmadı
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

<!-- Log Viewer Modal -->
<div class="modal fade" id="logViewer" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Log Məzmunu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="log-content" id="logContent">
                    <!-- Log content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .search-box {
        position: relative;
    }
    .search-box .search-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #98a6ad;
    }
    .log-content {
        font-family: monospace;
        white-space: pre-wrap;
        max-height: 500px;
        overflow-y: auto;
    }
    .log-content .log-line {
        padding: 2px 0;
        display: flex;
    }
    .log-content .line-number {
        color: #666;
        padding-right: 10px;
        user-select: none;
        min-width: 40px;
        text-align: right;
    }
    .log-content .line-content {
        flex: 1;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/settings/system/logs.js') }}"></script>
@endpush