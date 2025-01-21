$(document).ready(function() {
    // Initialize DataTables
    $('#columnsTable, #schoolsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/az.json"
        }
    });

    $('#dataTable').DataTable({
        "scrollX": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/az.json"
        }
    });

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
        const schoolName = $('#schoolName').val();
        const username = $('#adminUsername').val();
        const password = $('#adminPassword').val();
        const email = $('#adminEmail').val();
        const phone = $('#adminPhone').val();

        const data = {
            school_name: schoolName,
            username: username,
            password: password,
            email: email,
            phone: phone
        };

        $.ajax({
            url: '/api/school-admin',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                $('#addSchoolAdminModal').modal('hide');
                showNotification('Məktəb admini uğurla əlavə edildi');
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'danger');
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
    $('.data-cell').click(function() {
        const cell = $(this);
        const currentValue = cell.text().trim();
        const columnId = cell.data('column');
        
        // Get column type and options
        $.get('/api/columns/' + columnId, function(column) {
            let input;
            
            if (column.type === 'select') {
                input = $('<select>').addClass('form-control');
                input.append($('<option>').val('').text('-'));
                column.options.forEach(option => {
                    input.append($('<option>').val(option).text(option));
                });
                input.val(currentValue === '-' ? '' : currentValue);
            } else {
                const inputType = column.type === 'number' ? 'number' : 
                                column.type === 'date' ? 'date' : 'text';
                input = $('<input>')
                    .attr('type', inputType)
                    .val(currentValue === '-' ? '' : currentValue)
                    .addClass('form-control');
            }

            cell.html(input);
            input.focus();

            const saveData = function() {
                const newValue = input.val().trim();
                const data = {
                    school_id: cell.data('school'),
                    column_id: columnId,
                    value: newValue
                };

                $.ajax({
                    url: '/api/data',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function() {
                        cell.html(newValue || '-');
                        showNotification('Məlumat uğurla yadda saxlanıldı');
                    },
                    error: function(xhr) {
                        showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'danger');
                        cell.html(currentValue || '-');
                    }
                });
            };

            input.blur(saveData);
            input.keypress(function(e) {
                if (e.which === 13) saveData();
            });
        });
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