@extends('layouts.app')

@section('title', 'Log Məzmunu')

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
                        <li class="breadcrumb-item"><a href="{{ route('settings.system.logs.index') }}">Log-lar</a></li>
                        <li class="breadcrumb-item active">{{ $logName }}</li>
                    </ol>
                </div>
                <h4 class="page-title">Log Məzmunu: {{ $logName }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Log Info -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h6>Fayl Məlumatı</h6>
                                <p class="mb-0">
                                    Ölçü: {{ formatBytes($logSize) }}
                                    <br>
                                    <small class="text-muted">
                                        Son dəyişiklik: {{ $lastModified }}
                                    </small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h6>Log Səviyyələri</h6>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($logLevels as $level => $count)
                                        <span class="badge bg-{{ $level === 'ERROR' ? 'danger' : 
                                                                ($level === 'WARNING' ? 'warning' : 
                                                                ($level === 'INFO' ? 'info' : 'secondary')) }}">
                                            {{ $level }}: {{ $count }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <h6>Əməliyyatlar</h6>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-primary" id="downloadLog">
                                        <i class="fas fa-download me-1"></i> Yüklə
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" id="deleteLog">
                                        <i class="fas fa-trash me-1"></i> Sil
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Log Content -->
                    <div class="log-content border rounded">
                        <div class="p-2 border-bottom d-flex justify-content-between align-items-center bg-light">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-light" id="wrapLines">
                                    <i class="fas fa-wrap-text"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light" id="copyContent">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>

                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-light filter-level active" data-level="all">
                                    Hamısı
                                </button>
                                @foreach($logLevels as $level => $count)
                                    <button type="button" class="btn btn-sm btn-light filter-level" 
                                            data-level="{{ strtolower($level) }}">
                                        {{ $level }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="log-lines p-2" id="logContent">
                            @foreach($logContent as $line)
                                <div class="log-line" data-level="{{ strtolower($line['level']) }}">
                                    <span class="line-number">{{ $loop->iteration }}</span>
                                    <span class="line-content">
                                        @if($line['level'])
                                            <span class="badge bg-{{ $line['level'] === 'ERROR' ? 'danger' : 
                                                                    ($line['level'] === 'WARNING' ? 'warning' : 
                                                                    ($line['level'] === 'INFO' ? 'info' : 'secondary')) }}">
                                                {{ $line['level'] }}
                                            </span>
                                        @endif
                                        {{ $line['content'] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .log-content {
        font-family: monospace;
    }
    .log-lines {
        white-space: pre;
        overflow-x: auto;
    }
    .log-lines.wrap {
        white-space: pre-wrap;
    }
    .log-line {
        display: flex;
        padding: 2px 0;
    }
    .line-number {
        color: #666;
        padding-right: 10px;
        user-select: none;
        min-width: 40px;
        text-align: right;
    }
    .line-content {
        flex: 1;
    }
    .filter-level.active {
        background-color: #e9ecef;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle line wrapping
    $('#wrapLines').on('click', function() {
        $('.log-lines').toggleClass('wrap');
        $(this).toggleClass('active');
    });

    // Copy log content
    $('#copyContent').on('click', function() {
        const content = Array.from($('.log-line')).map(line => 
            $(line).find('.line-content').text().trim()
        ).join('\n');

        navigator.clipboard.writeText(content).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Uğurlu!',
                text: 'Log məzmunu kopyalandı',
                timer: 1500,
                showConfirmButton: false
            });
        });
    });

    // Filter log levels
    $('.filter-level').on('click', function() {
        const level = $(this).data('level');
        
        $('.filter-level').removeClass('active');
        $(this).addClass('active');
        
        if (level === 'all') {
            $('.log-line').show();
        } else {
            $('.log-line').hide();
            $(`.log-line[data-level="${level}"]`).show();
        }
    });

    // Download log
    $('#downloadLog').on('click', function() {
        window.location.href = '{{ route("settings.system.logs.download", $logName) }}';
    });

    // Delete log
    $('#deleteLog').on('click', function() {
        Swal.fire({
            title: 'Əminsiniz?',
            text: 'Bu log faylını silmək istədiyinizə əminsiniz?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Bəli, sil!',
            cancelButtonText: 'Xeyr',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("settings.system.logs.delete", $logName) }}',
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Uğurlu!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = '{{ route("settings.system.logs.index") }}';
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Xəta!',
                            text: xhr.responseJSON?.message || 'Xəta baş verdi'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush