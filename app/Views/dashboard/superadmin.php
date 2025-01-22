<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Məktəb Məlumatları</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" id="saveChanges" style="display: none;">
                <i class="fas fa-save"></i> Yadda Saxla
            </button>
            <button class="btn btn-success" id="excelExport">
                <i class="fas fa-file-excel"></i> Excel Export
            </button>
        </div>
    </div>

    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger">
            Məlumatları yükləyərkən xəta baş verdi
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered excel-table m-0" id="dataTable">
                        <thead>
                            <tr>
                                <?php 
                                $cols = ['Məktəblər'];
                                foreach ($columns as $column) {
                                    $cols[] = $column['name'];
                                }
                                foreach ($cols as $col): ?>
                                    <th class="excel-header"><?php echo htmlspecialchars($col); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schools as $school): ?>
                            <tr>
                                <td class="excel-cell font-weight-bold"><?php echo htmlspecialchars($school['name']); ?></td>
                                <?php 
                                foreach ($columns as $column) {
                                    $value = '';
                                    foreach ($data as $item) {
                                        if ($item['school_id'] == $school['id'] && 
                                            $item['column_id'] == $column['id']) {
                                            $value = $item['value'];
                                            break;
                                        }
                                    }
                                    echo '<td class="excel-cell data-cell" 
                                             data-school="' . $school['id'] . '" 
                                             data-column="' . $column['id'] . '" 
                                             contenteditable="true">' . 
                                        htmlspecialchars($value) . 
                                    '</td>';
                                }
                                ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.excel-table {
    border: 1px solid #dee2e6;
    border-collapse: collapse;
    width: 100%;
}

.excel-header {
    background-color: #f8f9fa;
    font-weight: bold;
    padding: 12px;
    border: 1px solid #dee2e6;
    min-width: 150px;
    text-align: center;
}

.excel-cell {
    padding: 12px;
    border: 1px solid #dee2e6;
    position: relative;
}

.excel-cell:focus {
    outline: 2px solid #007bff;
    outline-offset: -2px;
}

.data-cell:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}

.font-weight-bold {
    font-weight: bold;
}
</style>

<script>
$(document).ready(function() {
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

    // Update table with new data
    function updateTable(data) {
        const $tbody = $('#dataTable tbody');
        $tbody.empty();

        data.schools.forEach(school => {
            const $row = $('<tr>');
            $row.append(`<td class="excel-cell font-weight-bold">${school.name}</td>`);
            
            data.columns.forEach(column => {
                let value = '';
                data.data.forEach(item => {
                    if (item.school_id == school.id && item.column_id == column.id) {
                        value = item.value;
                    }
                });
                
                $row.append(`
                    <td class="excel-cell data-cell" 
                        data-school="${school.id}" 
                        data-column="${column.id}" 
                        contenteditable="true">${value}</td>
                `);
            });
            
            $tbody.append($row);
        });

        // Reattach event handlers
        attachEventHandlers();
    }

    // Attach event handlers to cells
    function attachEventHandlers() {
        $('.excel-cell[contenteditable="true"]')
            .off()
            .on('focus', function() {
                $(this).addClass('selected');
            })
            .on('blur', function() {
                $(this).removeClass('selected');
            })
            .on('input', function() {
                hasChanges = true;
                $saveBtn.show();
            })
            .on('keydown', function(e) {
                const $current = $(this);
                const $row = $current.parent();
                const cellIndex = $current.index();
                
                switch(e.keyCode) {
                    case 13: // Enter
                        e.preventDefault();
                        const $nextRow = $row.next();
                        if ($nextRow.length) {
                            $nextRow.children().eq(cellIndex).focus();
                        }
                        break;
                    case 9: // Tab
                        e.preventDefault();
                        if (e.shiftKey) {
                            $current.prev('[contenteditable="true"]').focus();
                        } else {
                            $current.next('[contenteditable="true"]').focus();
                        }
                        break;
                }
            });
    }

    let hasChanges = false;
    const $saveBtn = $('#saveChanges');

    // Initial event handlers
    attachEventHandlers();

    // Save changes
    $saveBtn.click(function() {
        const changes = [];
        $('.excel-cell[contenteditable="true"]').each(function() {
            const $cell = $(this);
            changes.push({
                school_id: $cell.data('school'),
                column_id: $cell.data('column'),
                value: $cell.text().trim()
            });
        });

        // Disable save button while saving
        $saveBtn.prop('disabled', true)
               .html('<span class="spinner-border spinner-border-sm"></span> Saxlanılır...');

        $.ajax({
            url: '/api/data/bulk-update',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ changes }),
            success: function(response) {
                if (response.success) {
                    hasChanges = false;
                    $saveBtn.hide();
                    showNotification('Məlumatlar yadda saxlanıldı', 'success');
                    
                    // Update table with new data
                    updateTable(response);
                } else {
                    showNotification(response.error || 'Xəta baş verdi', 'danger');
                }
            },
            error: function(xhr) {
                showNotification(xhr.responseJSON?.error || 'Xəta baş verdi', 'danger');
            },
            complete: function() {
                // Re-enable save button
                $saveBtn.prop('disabled', false)
                       .html('<i class="fas fa-save"></i> Yadda Saxla');
            }
        });
    });

    // Excel export
    $('#excelExport').click(function() {
        window.location.href = '/api/export';
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>