$(document).ready(function() {
    // WebSocket Connection
    const wsUrl = `ws://${window.location.hostname}:${window.appConfig.wsPort}`;
    const ws = new WebSocket(wsUrl);
    
    ws.onmessage = function(event) {
        const data = JSON.parse(event.data);
        if (data.type === 'update') {
            updateCell(data.school_id, data.column_id, data.value);
        }
    };

    // Notification function
    function showNotification(message, type = 'success') {
        const alertDiv = $('<div>')
            .addClass(`alert alert-${type} alert-dismissible fade show`)
            .html(`
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `);
        
        $('.container-fluid').first().prepend(alertDiv);
        
        setTimeout(() => {
            alertDiv.alert('close');
        }, 5000);
    }

    // Generate random password
    $('#generatePassword').click(function() {
        const length = 12;
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
        let password = "";
        for (let i = 0; i < length; i++) {
            password += charset.charAt(Math.floor(Math.random() * charset.length));
        }
        $('#adminPassword').val(password);
    });

    // Show/hide options textarea based on column type
    $('#columnType').change(function() {
        if ($(this).val() === 'select') {
            $('#optionsDiv').show();
        } else {
            $('#optionsDiv').hide();
        }
    });

    // Column operations
    $('#saveColumn').click(function() {
        const columnName = $('#columnName').val();
        const columnType = $('#columnType').val();
        const deadline = $('#deadline').val();
        const isRequired = $('#isRequired').is(':checked');
        const options = $('#options').val();

        const data = {
            name: columnName,
            type: columnType,
            deadline: deadline,
            is_required: isRequired ? 1 : 0,
            options: columnType === 'select' ? options.split('\n').filter(o => o.trim()) : null
        };

        $.ajax({
            url: '/api/columns',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                $('#addColumnModal').modal('hide');
                showNotification('Sütun uğurla əlavə edildi');
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'danger');
            }
        });
    });

    // School Admin operations
    $('#saveSchoolAdmin').click(function() {
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
            showNotification('Məktəb adı, istifadəçi adı və şifrə mütləq doldurulmalıdır', 'danger');
            return;
        }

        // Disable button while processing
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Gözləyin...');

        $.ajax({
            url: '/api/school-admin',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
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
            error: function(xhr) {
                const message = xhr.responseJSON?.error || 'Xəta baş verdi';
                showNotification(message, 'danger');
            },
            complete: function() {
                // Re-enable button
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Add/Edit School
    $('#saveSchool').click(function() {
        const schoolName = $('#schoolName').val();
        if (!schoolName) {
            showNotification('Məktəb adı daxil edilməlidir', 'danger');
            return;
        }

        // Disable button while processing
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Gözləyin...');

        $.ajax({
            url: '/api/schools',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ name: schoolName }),
            success: function(response) {
                $('#schoolModal').modal('hide');
                showNotification('Məktəb uğurla əlavə edildi');
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'danger');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Toggle column status
    $('.toggle-column').change(function() {
        const id = $(this).data('id');
        const isActive = $(this).is(':checked');

        $.ajax({
            url: '/api/columns/toggle',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id, is_active: isActive ? 1 : 0 }),
            success: function() {
                showNotification('Status uğurla dəyişdirildi');
            },
            error: function(xhr) {
                showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'danger');
                // Revert toggle if error occurs
                $(this).prop('checked', !isActive);
            }
        });
    });

    // Toggle school status
    $('.toggle-school').change(function() {
        const id = $(this).data('id');
        const isActive = $(this).is(':checked');

        $.ajax({
            url: '/api/schools/toggle',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id, is_active: isActive ? 1 : 0 }),
            success: function() {
                showNotification('Status uğurla dəyişdirildi');
            },
            error: function(xhr) {
                showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'danger');
                // Revert toggle if error occurs
                $(this).prop('checked', !isActive);
            }
        });
    });

    // Delete operations
    $('.delete-column').click(function() {
        const id = $(this).data('id');
        if (!confirm('Bu sütunu silmək istədiyinizə əminsiniz?')) return;

        $.ajax({
            url: '/api/columns/' + id,
            method: 'DELETE',
            success: function() {
                showNotification('Sütun uğurla silindi');
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'danger');
            }
        });
    });

    $('.delete-school').click(function() {
        const id = $(this).data('id');
        if (!confirm('Bu məktəbi silmək istədiyinizə əminsiniz?')) return;

        $.ajax({
            url: '/api/schools/' + id,
            method: 'DELETE',
            success: function() {
                showNotification('Məktəb uğurla silindi');
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'danger');
            }
        });
    });

    // Reset password
    $('.reset-password').click(function() {
        const id = $(this).data('id');
        if (!confirm('Bu məktəb admininin şifrəsini sıfırlamaq istədiyinizə əminsiniz?')) return;

        $.ajax({
            url: '/api/school-admin/reset-password/' + id,
            method: 'POST',
            success: function(response) {
                showNotification('Şifrə uğurla sıfırlandı. Yeni şifrə: ' + response.password);
            },
            error: function(xhr) {
                showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'danger');
            }
        });
    });

    // Data cell operations
    let hasChanges = false;
    let changedData = [];

    // For school admin view
    $('.data-cell').on('input', function() {
        const cell = $(this);
        const columnId = cell.data('column');
        const value = cell.text().trim();

        // Add to changed data array
        changedData = changedData.filter(item => item.column_id !== columnId);
        changedData.push({
            column_id: columnId,
            value: value
        });

        // Show save button
        hasChanges = true;
        $('#saveChanges').show();
    });

    // Save changes
    $('#saveChanges').click(function() {
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Gözləyin...');

        // Get school ID from meta tag
        const schoolId = $('meta[name="school-id"]').attr('content');
        
        // Prepare promises array
        const promises = changedData.map(data => 
            $.ajax({
                url: '/api/data/update',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    ...data,
                    school_id: schoolId
                })
            })
        );

        Promise.all(promises)
            .then(() => {
                showNotification('Məlumatlar uğurla yadda saxlanıldı');
                hasChanges = false;
                changedData = [];
                $('#saveChanges').hide();

                // Refresh the page after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            })
            .catch(error => {
                showNotification(error.responseJSON?.error || 'Xəta baş verdi', 'danger');
            })
            .finally(() => {
                $btn.prop('disabled', false).html(originalText);
            });
    });

    // Prevent leaving page with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (hasChanges) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Excel export
    $('#exportExcel').click(function() {
        window.location.href = '/api/export';
    });

    // Clear form inputs when modal is closed
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#optionsDiv').hide();
    });

    // Helper function to update cell value
    function updateCell(schoolId, columnId, value) {
        $(`.data-cell[data-school="${schoolId}"][data-column="${columnId}"]`).text(value || '-');
    }
});