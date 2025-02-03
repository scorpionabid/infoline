import { showNotification } from './notifications.js';

export class SchoolManager {
    constructor() {
        this.setupEventHandlers();
    }

    setupEventHandlers() {
        // Save school admin handler
        $('#saveSchoolAdmin').click(() => this.saveSchoolAdmin());

        // Save school handler
        $('#saveSchool').click(() => this.saveSchool());

        // Reset password handler
        $('.reset-password').click((e) => {
            const id = $(e.currentTarget).data('id');
            this.resetPassword(id);
        });

        // Toggle school status handler
        $('.toggle-school').change((e) => {
            const id = $(e.currentTarget).data('id');
            const isActive = $(e.currentTarget).is(':checked');
            this.toggleSchoolStatus(id, isActive);
        });

        // Delete school handler
        $('.delete-school').click((e) => {
            const id = $(e.currentTarget).data('id');
            this.deleteSchool(id);
        });
    }

    saveSchoolAdmin() {
        const data = {
            school_name: $('#schoolName').val(),
            username: $('#adminUsername').val(),
            password: $('#adminPassword').val(),
            email: $('#adminEmail').val() || null,
            phone: $('#adminPhone').val() || null,
            is_active: $('#adminIsActive').is(':checked') ? 1 : 0
        };

        // Form validation
        if (!data.school_name || !data.username || !data.password) {
            showNotification('Məktəb adı, istifadəçi adı və şifrə mütləq doldurulmalıdır', 'error');
            return;
        }

        // Disable button while processing
        const $btn = $('#saveSchoolAdmin');
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Gözləyin...');

        $.ajax({
            url: '/api/school-admin',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: (response) => {
                showNotification('Məktəb admini uğurla əlavə edildi', 'success');
                $('#addSchoolAdminModal').modal('hide');
                
                // Clear form
                $('#addSchoolAdminForm')[0].reset();
                
                // Refresh table if exists
                if ($.fn.DataTable.isDataTable('#schoolsTable')) {
                    $('#schoolsTable').DataTable().ajax.reload();
                } else {
                    // If no DataTable, just reload the page
                    setTimeout(() => location.reload(), 1000);
                }
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.error || 'Xəta baş verdi';
                showNotification(message, 'error');
            },
            complete: () => {
                // Re-enable button
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }

    saveSchool() {
        const schoolName = $('#schoolName').val();
        if (!schoolName) {
            showNotification('Məktəb adı daxil edilməlidir', 'error');
            return;
        }

        // Disable button while processing
        const $btn = $('#saveSchool');
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Gözləyin...');

        $.ajax({
            url: '/api/schools',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ name: schoolName }),
            success: (response) => {
                $('#schoolModal').modal('hide');
                showNotification('Məktəb uğurla əlavə edildi');
                setTimeout(() => location.reload(), 1000);
            },
            error: (xhr) => {
                showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'error');
            },
            complete: () => {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    }

    resetPassword(id) {
        if (!confirm('Bu məktəb admininin şifrəsini sıfırlamaq istədiyinizə əminsiniz?')) return;

        $.ajax({
            url: '/api/school-admin/reset-password/' + id,
            method: 'POST',
            success: (response) => {
                showNotification('Şifrə uğurla sıfırlandı. Yeni şifrə: ' + response.password);
            },
            error: (xhr) => {
                showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'error');
            }
        });
    }

    toggleSchoolStatus(id, isActive) {
        $.ajax({
            url: '/api/schools/toggle',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id, is_active: isActive ? 1 : 0 }),
            success: () => {
                showNotification('Status uğurla dəyişdirildi');
            },
            error: (xhr) => {
                showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'error');
                // Revert toggle if error occurs
                $(this).prop('checked', !isActive);
            }
        });
    }

    deleteSchool(id) {
        if (!confirm('Bu məktəbi silmək istədiyinizə əminsiniz?')) return;

        $.ajax({
            url: '/api/schools/' + id,
            method: 'DELETE',
            success: () => {
                showNotification('Məktəb uğurla silindi');
                setTimeout(() => location.reload(), 1000);
            },
            error: (xhr) => {
                showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'error');
            }
        });
    }
}