class TableSettings {
    constructor() {
        this.config = {
            selectors: {
                // Category Management
                addCategoryModal: '#addCategoryModal',
                addCategoryForm: '#addCategoryForm',
                editCategoryModal: '#editCategoryModal',
                editCategoryForm: '#editCategoryForm',
                deleteCategoryModal: '#deleteCategoryModal',
                deleteCategoryForm: '#deleteCategoryForm',
                
                // Column Management
                addColumnModal: '#addColumnModal',
                addColumnForm: '#addColumnForm',
                editColumnModal: '#editColumnModal',
                editColumnForm: '#editColumnForm',
                deleteColumnModal: '#deleteColumnModal',
                deleteColumnForm: '#deleteColumnForm',
                
                // Common
                formErrors: '.form-errors'
            },
            routes: {
                category: {
                    store: '/settings/table/category',
                    update: '/settings/table/category/',
                    delete: '/settings/table/category/',
                    edit: '/settings/table/category/'
                },
                column: {
                    store: '/settings/table/column',
                    update: '/settings/table/column/',
                    delete: '/settings/table/column/',
                    edit: '/settings/table/column/',
                    options: '/settings/table/column/options/'
                }
            }
        };

        // Event listener-ləri bind edirik
        this.bindEvents();
    }

    bindEvents() {
        // Mövcud event listener-lər
        $(document).on('submit', this.config.selectors.addColumnForm, this.handleColumnSubmit.bind(this));
        $(document).on('submit', this.config.selectors.addCategoryForm, this.handleCategorySubmit.bind(this));
        $(document).on('click', '[data-action="delete-category"]', this.handleCategoryDelete.bind(this));
        
        // Yeni event listener-lər
        $(document).on('click', '[data-action="delete-column"]', this.handleColumnDelete.bind(this));
        $(document).on('click', '[data-action="edit-column"]', this.handleColumnEdit.bind(this));

        // Category Management
        $(document).on('click', '[data-action="edit-category"]', (e) => {
            const categoryId = $(e.currentTarget).data('category-id');
            this.handleCategoryEdit(categoryId);
        });

        $(document).on('click', '[data-action="delete-category"]', (e) => {
            const categoryId = $(e.currentTarget).data('category-id');
            this.handleCategoryDelete(categoryId);
        });

        $(this.config.selectors.addCategoryForm).on('submit', (e) => this.handleCategorySubmit(e));
        $(this.config.selectors.editCategoryForm).on('submit', (e) => this.handleCategoryUpdate(e));

        // Column Management
        $(document).on('click', '[data-action="edit-column"]', (e) => {
            const columnId = $(e.currentTarget).data('column-id');
            this.handleColumnEdit(columnId);
        });

        $(document).on('click', '[data-action="delete-column"]', (e) => {
            const columnId = $(e.currentTarget).data('column-id');
            this.handleColumnDelete(columnId);
        });

        $(this.config.selectors.addColumnForm).on('submit', (e) => this.handleColumnSubmit(e));
        $(this.config.selectors.editColumnForm).on('submit', (e) => this.handleColumnUpdate(e));
    }

    // Utility Methods
    getHeaders() {
        return {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        };
    }

    showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Uğurlu!',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    }

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Xəta!',
            text: message
        });
    }

    showFormErrors(errors) {
        const errorDiv = $(this.config.selectors.formErrors);
        errorDiv.html('');
        
        if (typeof errors === 'object') {
            const ul = $('<ul class="mb-0"></ul>');
            Object.values(errors).forEach(error => {
                ul.append(`<li>${error}</li>`);
            });
            errorDiv.append(ul);
        } else {
            errorDiv.text(errors);
        }
        
        errorDiv.show();
    }

    // Category Management Methods
    async handleCategoryEdit(categoryId) {
        try {
            const response = await fetch(`${this.config.routes.category.edit}${categoryId}`, {
                method: 'GET',
                headers: this.getHeaders()
            });
            
            const data = await response.json();
            
            if (response.ok) {
                // Form elementlərini doldur
                const form = $(this.config.selectors.editCategoryForm);
                form.attr('action', `${this.config.routes.category.update}${categoryId}`);
                $('#editCategoryId').val(categoryId);
                $('#editCategoryName').val(data.category.name);
                $('#editCategoryDescription').val(data.category.description);
                
                // Modalı göstər
                $(this.config.selectors.editCategoryModal).modal('show');
            } else {
                this.showError(data.message || 'Kateqoriya məlumatları alınarkən xəta baş verdi');
            }
        } catch (error) {
            console.error('Category edit error:', error);
            this.showError('Sistem xətası baş verdi');
        }
    }

    async handleCategoryDelete(categoryId) {
        try {
            const result = await Swal.fire({
                title: 'Əminsiniz?',
                text: 'Bu kateqoriya və ona aid bütün sütunlar silinəcək!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Bəli, sil!',
                cancelButtonText: 'Ləğv et'
            });

            if (result.isConfirmed) {
                const response = await fetch(`${this.config.routes.category.delete}${categoryId}`, {
                    method: 'DELETE',
                    headers: this.getHeaders()
                });

                const data = await response.json();

                if (response.ok) {
                    this.showSuccess(data.message);
                    window.location.href = '/settings/table';
                } else {
                    this.showError(data.message || 'Kateqoriya silinərkən xəta baş verdi');
                }
            }
        } catch (error) {
            console.error('Category delete error:', error);
            this.showError('Sistem xətası baş verdi');
        }
    }

    async handleCategorySubmit(e) {
        e.preventDefault();
        const form = $(e.currentTarget);
        
        try {
            const response = await fetch(form.attr('action'), {
                method: form.attr('method'),
                headers: this.getHeaders(),
                body: new FormData(form[0])
            });
            
            const data = await response.json();
            
            if (response.ok) {
                this.showSuccess(data.message);
                $(this.config.selectors.addCategoryModal).modal('hide');
                window.location.reload();
            } else {
                if (data.errors) {
                    this.showFormErrors(data.errors);
                } else {
                    this.showError(data.message || 'Xəta baş verdi');
                }
            }
        } catch (error) {
            console.error('Category submit error:', error);
            this.showError('Sistem xətası baş verdi');
        }
    }

    async handleCategoryUpdate(e) {
        e.preventDefault();
        const form = $(e.currentTarget);
        
        try {
            const response = await fetch(form.attr('action'), {
                method: form.attr('method'),
                headers: this.getHeaders(),
                body: new FormData(form[0])
            });
            
            const data = await response.json();
            
            if (response.ok) {
                this.showSuccess(data.message);
                $(this.config.selectors.editCategoryModal).modal('hide');
                window.location.reload();
            } else {
                if (data.errors) {
                    this.showFormErrors(data.errors);
                } else {
                    this.showError(data.message || 'Xəta baş verdi');
                }
            }
        } catch (error) {
            console.error('Category update error:', error);
            this.showError('Sistem xətası baş verdi');
        }
    }

    async handleColumnSubmit(e) {
        e.preventDefault();
        const form = $(e.currentTarget);
        
        try {
            // Form məlumatlarını yoxla
            const formData = new FormData(form[0]);
            const dataType = formData.get('data_type');
            
            // Select tipi üçün options məcburidir
            if (dataType === 'select') {
                const options = formData.getAll('options[]').filter(opt => opt.trim());
                if (!options.length) {
                    this.showError('Seçim tipi üçün ən azı bir variant daxil edilməlidir');
                    return;
                }
                
                // Boş options-ları sil
                formData.delete('options[]');
                options.forEach(opt => formData.append('options[]', opt));
            }

            // Form URL-ini yoxla
            const url = '/settings/table/column';
            console.log('Form URL:', url);
            console.log('Form Data:', Object.fromEntries(formData));

            // AJAX request
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    console.log('Success response:', response);
                    if (response.success) {
                        this.showSuccess(response.message);
                        $(this.config.selectors.addColumnModal).modal('hide');
                        
                        // Sütunlar cədvəlini yeniləmək üçün
                        const currentUrl = new URL(window.location.href);
                        const categoryId = currentUrl.searchParams.get('category');
                        
                        if (categoryId) {
                            window.location.href = `/settings/table?category=${categoryId}`;
                        } else {
                            window.location.reload();
                        }
                    } else {
                        this.showError(response.message || 'Xəta baş verdi');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('XHR Error:', {xhr, status, error});
                    console.error('Response Text:', xhr.responseText);
                    
                    if (xhr.status === 422 && xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            const errorMessages = Object.values(xhr.responseJSON.errors).flat();
                            this.showFormErrors(errorMessages);
                        } else {
                            this.showError(xhr.responseJSON.message || 'Validasiya xətası baş verdi');
                        }
                    } else {
                        this.showError('Sistem xətası baş verdi');
                    }
                }
            });
        } catch (error) {
            console.error('Column submit error:', error);
            this.showError('Sistem xətası baş verdi');
        }
    }

    async handleColumnDelete(e) {
        const button = $(e.currentTarget);
        const columnId = button.data('column-id');
        
        if (!columnId) {
            this.showError('Sütun ID-si tapılmadı');
            return;
        }

        if (!confirm('Bu sütunu silmək istədiyinizə əminsiniz?')) {
            return;
        }

        try {
            const response = await $.ajax({
                url: `/settings/table/column/${columnId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            if (response.success) {
                this.showSuccess(response.message);
                // Cədvəli yenilə
                const currentUrl = new URL(window.location.href);
                const categoryId = currentUrl.searchParams.get('category');
                if (categoryId) {
                    window.location.href = `/settings/table?category=${categoryId}`;
                } else {
                    window.location.reload();
                }
            } else {
                this.showError(response.message || 'Sütun silinərkən xəta baş verdi');
            }
        } catch (error) {
            console.error('Column delete error:', error);
            this.showError('Sistem xətası baş verdi');
        }
    }

    async handleColumnEdit(e) {
        const button = $(e.currentTarget);
        const columnId = button.data('column-id');
        
        if (!columnId) {
            this.showError('Sütun ID-si tapılmadı');
            return;
        }

        try {
            // Sütun məlumatlarını al
            const response = await $.ajax({
                url: `/settings/table/column/${columnId}`,
                method: 'GET'
            });

            if (response.success && response.column) {
                const column = response.column;
                const modal = $(this.config.selectors.addColumnModal);
                
                // Form elementlərini doldur
                modal.find('[name="name"]').val(column.name);
                modal.find('[name="data_type"]').val(column.data_type);
                modal.find('[name="description"]').val(column.description);
                modal.find('[name="is_required"]').prop('checked', column.is_required);
                modal.find('[name="input_limit"]').val(column.input_limit);
                
                if (column.end_date) {
                    modal.find('[name="end_date"]').val(column.end_date.split('T')[0]);
                }

                // Select tipi üçün options
                if (column.data_type === 'select' && column.options) {
                    const optionsWrapper = modal.find('#optionsWrapper');
                    const optionsList = optionsWrapper.find('#optionsList');
                    
                    // Mövcud options-ları təmizlə
                    optionsList.empty();
                    
                    // Options-ları əlavə et
                    column.options.forEach(option => {
                        const optionHtml = `
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="options[]" 
                                       value="${option}" maxlength="255">
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="$(this).closest('.input-group').remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                        optionsList.append(optionHtml);
                    });
                    
                    optionsWrapper.show();
                }

                // Form action-u yenilə
                const form = modal.find('form');
                form.attr('action', `/settings/table/column/${columnId}`);
                form.append('<input type="hidden" name="_method" value="PUT">');

                // Modal-ı göstər
                modal.modal('show');
            } else {
                this.showError(response.message || 'Sütun məlumatları alınarkən xəta baş verdi');
            }
        } catch (error) {
            console.error('Column edit error:', error);
            this.showError('Sistem xətası baş verdi');
        }
    }
}

// Global instance yaradırıq
window.tableSettings = new TableSettings();
