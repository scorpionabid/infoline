import { showNotification } from './notifications.js';

export class ColumnManager {
    constructor() {
        this.hasChanges = false;
        this.changedData = [];
        this.categories = [];
        this.setupEventHandlers();
        this.loadCategories();
    }

    setupEventHandlers() {
        // Save column handler
        $('#saveColumn').click(() => this.saveColumn());

        // Data cell input handler
        $('.data-cell').on('input', (e) => this.handleCellInput(e));

        // Save changes button handler
        $('#saveChanges').click(() => this.saveChanges());

        // Modal açılanda kateqoriyaları yenilə
        $('#columnModal').on('show.bs.modal', () => {
            this.updateCategoryDropdown();
        });
    }

    loadTableData() {
        $.get('/api/columns', (response) => {
            if (response.success && Array.isArray(response.data)) {
                const grouped = this.groupColumnsByCategory(response.data);
                this.renderGroupedColumns(grouped);
            } else {
                console.error('Invalid response format:', response);
                showNotification('Məlumatları yükləmək mümkün olmadı', 'error');
            }
        }).fail((xhr) => {
            console.error('Failed to load data:', xhr);
            showNotification('Məlumatları yükləmək mümkün olmadı', 'error');
        });
    }

    loadCategories() {
        console.log('Loading categories...');
        $.ajax({
            url: '/api/categories',
            method: 'GET',
            success: (response) => {
                if (response.success) {
                    this.categories = response.data;
                    console.log('Categories loaded:', this.categories);
                    this.updateCategoryDropdown();
                } else {
                    console.error('Failed to load categories:', response);
                    showNotification('Kateqoriyaları yükləmək mümkün olmadı', 'error');
                }
            },
            error: (xhr) => {
                console.error('Error loading categories:', xhr);
                showNotification('Kateqoriyaları yükləmək mümkün olmadı', 'error');
            }
        });
    }

    groupColumnsByCategory(columns) {
        if (!Array.isArray(columns)) {
            console.error('Columns is not an array:', columns);
            return {};
        }
        
        const grouped = {};
        columns.forEach(col => {
            const catName = col.category_name || 'Digər';
            if (!grouped[catName]) {
                grouped[catName] = [];
            }
            grouped[catName].push(col);
        });
        return grouped;
    }

    renderGroupedColumns(groupedData) {
        const container = $('#columnsContainer');
        container.empty();

        Object.entries(groupedData).forEach(([category, columns]) => {
            const categoryHtml = `
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">${category || 'Kateqoriyasız'}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Sütun Adı</th>
                                        <th>Məlumat Tipi</th>
                                        <th>Son Tarix</th>
                                        <th>Status</th>
                                        <th>Əməliyyatlar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${columns.map(column => this.createColumnRow(column)).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            container.append(categoryHtml);
        });

        this.attachColumnEventHandlers();
    }

    createColumnRow(column) {
        return `
            <tr>
                <td>${this.escapeHtml(column.name)}</td>
                <td>${this.escapeHtml(column.type)}</td>
                <td>${column.deadline ? this.formatDate(column.deadline) : '-'}</td>
                <td>
                    <span class="badge bg-${column.is_active ? 'success' : 'danger'}">
                        ${column.is_active ? 'Aktiv' : 'Deaktiv'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary edit-column" data-id="${column.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-column" data-id="${column.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    attachColumnEventHandlers() {
        // Edit column handler
        $('.edit-column').on('click', (e) => {
            const id = $(e.currentTarget).data('id');
            this.editColumn(id);
        });

        // Delete column handler
        $('.delete-column').on('click', (e) => {
            const id = $(e.currentTarget).data('id');
            this.deleteColumn(id);
        });
    }

    editColumn(id) {
        $.get(`/api/columns/${id}`, (response) => {
            if (response.success) {
                const column = response.data;
                $('#columnForm input[name="id"]').val(column.id);
                $('#columnName').val(column.name);
                $('#columnType').val(column.type);
                $('#deadline').val(column.deadline);
                $('#isRequired').prop('checked', column.is_required);
                $('#columnModal').modal('show');
            }
        });
    }

    deleteColumn(id) {
        if (confirm('Bu sütunu silmək istədiyinizdən əminsiniz?')) {
            $.ajax({
                url: `/api/columns/${id}`,
                method: 'DELETE',
                success: (response) => {
                    if (response.success) {
                        showNotification('Sütun uğurla silindi', 'success');
                        this.loadTableData();
                    } else {
                        showNotification(response.message || 'Xəta baş verdi', 'error');
                    }
                },
                error: (xhr) => {
                    showNotification(xhr.responseJSON?.message || 'Xəta baş verdi', 'error');
                }
            });
        }
    }

    saveColumn() {
        const columnData = {
            name: $('#columnName').val(),
            type: $('#columnType').val(),
            deadline: $('#columnDeadline').val(),
            is_active: $('#columnStatus').is(':checked'),
            category_id: $('#columnCategory').val()
        };

        // Validasiya
        if (!columnData.name) {
            showNotification('Sütun adı daxil edilməlidir', 'error');
            return;
        }

        if (!columnData.type) {
            showNotification('Məlumat tipi seçilməlidir', 'error');
            return;
        }

        $.ajax({
            url: '/settings/addColumn',
            method: 'POST',
            data: columnData,
            success: (response) => {
                if (response.success) {
                    $('#columnModal').modal('hide');
                    showNotification(response.message || 'Sütun uğurla əlavə edildi', 'success');
                    this.loadTableData();
                } else {
                    showNotification(response.error || 'Xəta baş verdi', 'error');
                }
            },
            error: (xhr) => {
                console.error('Save column error:', xhr);
                showNotification(xhr.responseJSON?.error || 'Sistem xətası baş verdi', 'error');
            }
        });
    }

    handleCellInput(e) {
        const cell = $(e.currentTarget);
        const columnId = cell.data('column');
        const value = cell.text().trim();

        // Add to changed data array
        this.changedData = this.changedData.filter(item => item.column_id !== columnId);
        this.changedData.push({
            column_id: columnId,
            value: value
        });

        // Show save button
        this.hasChanges = true;
        $('#saveChanges').show();
    }

    saveChanges() {
        const $btn = $('#saveChanges');
        const originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Gözləyin...');

        // Get school ID from meta tag
        const schoolId = $('meta[name="school-id"]').attr('content');
        
        // Prepare promises array
        const promises = this.changedData.map(data => 
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
                this.hasChanges = false;
                this.changedData = [];
                $('#saveChanges').hide();

                // Refresh the page after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            })
            .catch(error => {
                showNotification(error.responseJSON?.error || 'Xəta baş verdi', 'error');
            })
            .finally(() => {
                $btn.prop('disabled', false).html(originalText);
            });
    }

    updateCell(schoolId, columnId, value) {
        const cell = $(`#cell_${schoolId}_${columnId}`);
        if (cell.length) {
            const oldValue = cell.text();
            cell.text(value);
            
            // Visual feedback
            cell.addClass('bg-success-light');
            setTimeout(() => {
                cell.removeClass('bg-success-light');
            }, 1000);

            // Log change
            this.logChange(schoolId, columnId, oldValue, value);
        }
    }

    logChange(schoolId, columnId, oldValue, newValue) {
        const log = {
            school_id: schoolId,
            column_id: columnId,
            old_value: oldValue,
            new_value: newValue,
            timestamp: new Date().toISOString()
        };

        // Send to server
        $.post('/api/log-change', log)
            .fail((xhr) => {
                console.error('Failed to log change:', xhr.responseText);
            });
    }

    validateColumnForm() {
        const form = document.getElementById('columnForm');
        return form.checkValidity();
    }

    escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('az-AZ', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    hasUnsavedChanges() {
        return this.hasChanges;
    }

    updateCategoryDropdown() {
        const dropdown = $('#columnCategory');
        dropdown.empty();
        
        if (this.categories && this.categories.length > 0) {
            // Default olmayan kateqoriyaları əlavə et
            this.categories
                .filter(cat => !cat.is_default)
                .forEach(category => {
                    dropdown.append(`<option value="${category.id}">${category.name}</option>`);
                });
            
            // Default kateqoriyanı sona əlavə et
            const defaultCategory = this.categories.find(cat => cat.is_default);
            if (defaultCategory) {
                dropdown.append(`<option value="${defaultCategory.id}">${defaultCategory.name}</option>`);
            }
        } else {
            dropdown.append('<option value="">Kateqoriya tapılmadı</option>');
        }
    }
}