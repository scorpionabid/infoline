import { showNotification } from './notifications.js';

export class SettingsManager {
    constructor() {
        this.initializeEventHandlers();
        this.initializeDataTable();
    }

    initializeDataTable() {
        this.columnsTable = $('#columnsTable').DataTable({
            language: {
                "emptyTable": "Cədvəldə heç bir məlumat yoxdur",
                "info": "_TOTAL_ Nəticədən _START_ - _END_ Arası Nəticələr",
                "infoEmpty": "Nəticə Yoxdur",
                "infoFiltered": "(_MAX_ Nəticə İçindən Tapılanlar)",
                "lengthMenu": "Səhifədə _MENU_ Nəticə Göstər",
                "loadingRecords": "Yüklənir...",
                "processing": "Gözləyin...",
                "search": "Axtarış:",
                "zeroRecords": "Nəticə Tapılmadı.",
                "paginate": {
                    "first": "İlk",
                    "last": "Axırıncı",
                    "next": "Sonrakı",
                    "previous": "Öncəki"
                }
            },
            order: [[0, 'asc']]
        });
    }

    initializeEventHandlers() {
        // Sütun əlavə etmə
        $('#addColumnForm').on('submit', (e) => {
            e.preventDefault();
            this.addColumn();
        });

        // Sütun silmə
        $(document).on('click', '.delete-column', (e) => {
            e.preventDefault();
            const columnId = $(e.currentTarget).data('id');
            
            if (confirm('Bu sütunu silmək istədiyinizdən əminsiniz?')) {
                this.deleteColumn(columnId);
            }
        });

        // Sütun redaktə
        $(document).on('click', '.edit-column', (e) => {
            e.preventDefault();
            const columnId = $(e.currentTarget).data('id');
            this.loadColumnForEdit(columnId);
        });

        // Redaktə formu
        $('#editColumnForm').on('submit', (e) => {
            e.preventDefault();
            this.updateColumn();
        });
    }

    deleteColumn(id) {
        $.ajax({
            url: '/settings/deleteColumn',
            method: 'POST',
            data: { id: id },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    showNotification(response.message, 'success');
                    // Cədvəli yeniləyirik
                    window.location.reload();
                } else {
                    showNotification(response.error, 'error');
                }
            },
            error: (xhr) => {
                showNotification('Sistem xətası baş verdi', 'error');
                console.error('Delete error:', xhr);
            }
        });
    }

    loadColumnForEdit(id) {
        $.ajax({
            url: '/settings/getColumn',
            method: 'GET',
            data: { id: id },
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    // Modal-ı doldururuq
                    $('#editColumnModal').modal('show');
                    $('#editColumnId').val(response.data.id);
                    $('#editColumnName').val(response.data.name);
                    $('#editColumnType').val(response.data.type);
                    $('#editColumnDeadline').val(response.data.deadline);
                    $('#editColumnActive').prop('checked', response.data.is_active);
                } else {
                    showNotification(response.error, 'error');
                }
            },
            error: (xhr) => {
                showNotification('Sistem xətası baş verdi', 'error');
                console.error('Load error:', xhr);
            }
        });
    }

    updateColumn() {
        const formData = {
            id: $('#editColumnId').val(),
            name: $('#editColumnName').val(),
            type: $('#editColumnType').val(),
            deadline: $('#editColumnDeadline').val(),
            is_active: $('#editColumnActive').is(':checked')
        };

        $.ajax({
            url: '/settings/updateColumn',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    $('#editColumnModal').modal('hide');
                    showNotification(response.message, 'success');
                    // Cədvəli yeniləyirik
                    window.location.reload();
                } else {
                    showNotification(response.error, 'error');
                }
            },
            error: (xhr) => {
                showNotification('Sistem xətası baş verdi', 'error');
                console.error('Update error:', xhr);
            }
        });
    }

    addColumn() {
        const formData = {
            name: $('#columnName').val(),
            type: $('#columnType').val(),
            deadline: $('#columnDeadline').val(),
            is_active: $('#columnActive').is(':checked'),
            category_id: $('#columnCategory').val()
        };

        $.ajax({
            url: '/settings/addColumn',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: (response) => {
                if (response.success) {
                    showNotification(response.message, 'success');
                    $('#addColumnModal').modal('hide');
                    window.location.reload();
                } else {
                    showNotification(response.error, 'error');
                }
            },
            error: (xhr) => {
                showNotification('Sistem xətası baş verdi', 'error');
                console.error('Add error:', xhr);
            }
        });
    }
}