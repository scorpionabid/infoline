import { showNotification } from './notifications.js';

export class CategoryManager {
    constructor() {
        console.log('CategoryManager initialized');
        this.categories = [];
        this.apiBaseUrl = '/api/categories';
        this.initializeDataTable();
        this.setupEventHandlers();
        this.loadCategories();
    }

    initializeDataTable() {
        console.log('Initializing DataTable');
        this.categoryTable = $('#categoryTable').DataTable({
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
            order: [[0, 'asc']], // Kateqoriya adına görə sıralama
            columnDefs: [
                {
                    targets: -1, // Son sütun (əməliyyatlar)
                    orderable: false // Sıralama olmasın
                }
            ]
        });
        console.log('DataTable initialized');
    }

    setupEventHandlers() {
        console.log('Setting up event handlers');
        
        // Save category handler
        $(document).on('click', '#saveCategory', (e) => {
            e.preventDefault();
            console.log('Save category button clicked');
            this.saveCategory();
        });

        // Delete category handler
        $(document).on('click', '.delete-category', (e) => {
            e.preventDefault();
            const id = $(e.currentTarget).data('id');
            console.log('Delete category button clicked for ID:', id);
            if (confirm('Bu kateqoriyanı silmək istədiyinizdən əminsiniz?')) {
                this.deleteCategory(id);
            }
        });
    }

    loadCategories() {
        console.log('Loading categories...');
        $.ajax({
            url: this.apiBaseUrl,
            method: 'GET',
            dataType: 'json',
            beforeSend: () => {
                console.log('Sending request to load categories...');
            }
        })
        .done((response) => {
            console.log('Categories load response:', response);
            if (response && response.success) {
                this.categories = Array.isArray(response.data) ? response.data : [];
                console.log('Categories loaded successfully:', this.categories);
                this.updateCategoryDropdowns();
                this.updateCategoryTable();
            } else {
                console.error('Error in categories response:', response);
                const errorMsg = response?.message || 'Kateqoriyaları yükləmək mümkün olmadı';
                showNotification(errorMsg, 'error');
            }
        })
        .fail((xhr, status, error) => {
            console.error('Failed to load categories:', {
                status: status,
                error: error,
                response: xhr.responseText,
                url: this.apiBaseUrl
            });
            
            let errorMessage = 'Kateqoriyaları yükləmək mümkün olmadı';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 404) {
                errorMessage = 'Kateqoriyalar API-si tapılmadı';
                console.error('API endpoint not found:', this.apiBaseUrl);
            } else if (xhr.status === 500) {
                errorMessage = 'Server xətası baş verdi';
            }
            
            showNotification(errorMessage, 'error');
        });
    }

    updateCategoryDropdowns() {
        console.log('Updating category dropdowns');
        const dropdowns = $('.category-dropdown');
        dropdowns.empty();
        
        if (this.categories && this.categories.length > 0) {
            console.log('Adding categories to dropdowns:', this.categories);
            
            // Default kateqoriyanı tap
            const defaultCategory = this.categories.find(cat => cat.is_default === 1);
            
            // Əvvəlcə default olmayan kateqoriyaları əlavə et
            this.categories
                .filter(cat => cat.is_default !== 1)
                .forEach(category => {
                    dropdowns.append(`<option value="${category.id}">${category.name}</option>`);
                });
            
            // Sonda default kateqoriyanı əlavə et
            if (defaultCategory) {
                dropdowns.append(`<option value="${defaultCategory.id}">${defaultCategory.name}</option>`);
            }
            
            // İlk kateqoriyanı seç
            const firstCategory = this.categories[0];
            if (firstCategory) {
                dropdowns.val(firstCategory.id);
            }
        } else {
            console.log('No categories to display');
            dropdowns.append('<option value="">Kateqoriya tapılmadı</option>');
        }
    }

    updateCategoryTable() {
        console.log('Updating category table');
        // Cədvəli təmizləyirik
        this.categoryTable.clear();
        
        // Yeni məlumatları əlavə edirik
        if (this.categories && this.categories.length > 0) {
            console.log('Adding categories to table:', this.categories);
            this.categories.forEach(category => {
                const deleteButton = `
                    <button class="btn btn-sm btn-danger delete-category" data-id="${category.id}" title="Sil">
                        <i class="fas fa-trash"></i>
                    </button>
                `;
                
                this.categoryTable.row.add([
                    category.name,
                    category.description || '',
                    deleteButton
                ]);
            });
        } else {
            console.log('No categories to display');
        }
        
        // Cədvəli yeniləyirik
        this.categoryTable.draw();
        console.log('Category table updated');
    }

    saveCategory() {
        console.log('Saving category');
        if (!this.validateCategoryForm()) {
            console.error('Form validation failed');
            return;
        }
        
        const categoryData = {
            name: $('#categoryName').val().trim(),
            description: $('#categoryDescription').val().trim()
        };
        console.log('Category data:', categoryData);

        if (!categoryData.name) {
            console.error('Category name is empty');
            showNotification('Kateqoriya adı tələb olunur', 'error');
            return;
        }

        $.ajax({
            url: '/api/categories',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(categoryData),
            success: (response) => {
                console.log('Save category response:', response);
                if (response.success) {
                    $('#categoryModal').modal('hide');
                    showNotification(response.message, 'success');
                    $('#categoryForm')[0].reset();
                    
                    // Yeni kateqoriyaları yeniləyirik
                    if (response.data) {
                        console.log('Updating categories with response data');
                        this.categories = response.data;
                        this.updateCategoryDropdowns();
                        this.updateCategoryTable();
                    } else {
                        console.log('No response data, loading categories');
                        this.loadCategories();
                    }
                } else {
                    console.error('Error saving category:', response.message);
                    showNotification(response.message || 'Xəta baş verdi', 'error');
                }
            },
            error: (xhr) => {
                console.error('Failed to save category:', xhr);
                showNotification(xhr.responseJSON?.message || 'Xəta baş verdi', 'error');
            }
        });
    }

    deleteCategory(id) {
        console.log('Deleting category:', id);
        if (!id) {
            console.error('Category ID is empty');
            showNotification('Kateqoriya ID-si tələb olunur', 'error');
            return;
        }

        $.ajax({
            url: `/api/categories/${id}`,
            method: 'DELETE',
            success: (response) => {
                console.log('Delete category response:', response);
                if (response.success) {
                    showNotification(response.message, 'success');
                    
                    // Yeni kateqoriyaları yeniləyirik
                    if (response.data) {
                        console.log('Updating categories with response data');
                        this.categories = response.data;
                        this.updateCategoryDropdowns();
                        this.updateCategoryTable();
                    } else {
                        console.log('No response data, loading categories');
                        this.loadCategories();
                    }
                } else {
                    console.error('Error deleting category:', response.message);
                    showNotification(response.message || 'Xəta baş verdi', 'error');
                }
            },
            error: (xhr) => {
                console.error('Failed to delete category:', xhr);
                showNotification(xhr.responseJSON?.message || 'Xəta baş verdi', 'error');
            }
        });
    }

    validateCategoryForm() {
        const form = document.getElementById('categoryForm');
        const isValid = form.checkValidity();
        console.log('Form validation result:', isValid);
        return isValid;
    }
}