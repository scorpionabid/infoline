@extends('layouts.app')

@section('title', 'Bildiriş Tənzimləmələri')

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
                        <li class="breadcrumb-item active">Bildirişlər</li>
                    </ol>
                </div>
                <h4 class="page-title">Bildiriş Tənzimləmələri</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="notificationSettingsForm">
                        @csrf
                        
                        <!-- Email Notifications -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Email Bildirişləri</h5>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="emailNotifications" 
                                           name="email_notifications" {{ $settings['email_notifications'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="emailNotifications"></label>
                                </div>
                            </div>
                            <p class="text-muted">Sistem bildirişlərini email vasitəsilə almaq</p>
                        </div>

                        <!-- Deadline Reminders -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Son Tarix Xatırlatmaları</h5>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="deadlineReminders" 
                                           name="deadline_reminders" {{ $settings['deadline_reminders'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="deadlineReminders"></label>
                                </div>
                            </div>
                            <p class="text-muted">Yaxınlaşan son tarixlər haqqında xatırlatmalar</p>
                            
                            <div class="mt-2" id="reminderDaysWrapper" 
                                 style="{{ !$settings['deadline_reminders'] ? 'display: none;' : '' }}">
                                <label for="reminderDays" class="form-label">Xatırlatma günləri</label>
                                <div class="input-group" style="width: 200px;">
                                    <input type="number" class="form-control" id="reminderDays" 
                                           name="reminder_days" value="{{ $settings['reminder_days'] }}"
                                           min="1" max="30">
                                    <span class="input-group-text">gün əvvəl</span>
                                </div>
                                <small class="text-muted">Son tarixdən neçə gün əvvəl xatırlatma göndərilsin</small>
                            </div>
                        </div>

                        <!-- System Alerts -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0">Sistem Xəbərdarlıqları</h5>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="systemAlerts" 
                                           name="system_alerts" {{ $settings['system_alerts'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="systemAlerts"></label>
                                </div>
                            </div>
                            <p class="text-muted">Sistem xətaları və vacib yeniləmələr haqqında bildirişlər</p>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" id="saveNotificationSettings">
                                <i class="fas fa-save me-1"></i> Yadda saxla
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Test Card -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Test Bildirişi</h5>
                    <p class="text-muted">Bildiriş tənzimləmələrini yoxlamaq üçün test bildirişi göndərin</p>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-info" id="sendTestNotification">
                            <i class="fas fa-paper-plane me-1"></i> Test Bildirişi Göndər
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/settings/system/notifications.js') }}"></script>
<script>
    // Toggle reminder days input based on deadline reminders checkbox
    $('#deadlineReminders').on('change', function() {
        $('#reminderDaysWrapper').slideToggle(this.checked);
    });

    // Send test notification
    $('#sendTestNotification').on('click', function() {
        const button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Göndərilir...');

        $.ajax({
            url: '{{ route("settings.system.notifications.test") }}',
            type: 'POST',
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
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Xəta!',
                    text: xhr.responseJSON?.message || 'Xəta baş verdi'
                });
            },
            complete: function() {
                button.prop('disabled', false)
                    .html('<i class="fas fa-paper-plane me-1"></i> Test Bildirişi Göndər');
            }
        });
    });
</script>
@endpush