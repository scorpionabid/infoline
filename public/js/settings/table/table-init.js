// public/js/settings/table/table-init.js

/**
 * Bu fayl cədvəl ayarları səhifəsindəki bütün düymələri və funksionallığı işə salır
 * Bütün hadisə dinləyiciləri burada mərkəzləşdirilmişdir
 */

const TableInit = {
    /**
     * Səhifə yükləndikdə bütün funksionallığı işə salır
     */
    init: function() {
        console.log('Cədvəl ayarları səhifəsi yüklənir...');
        
        this.registerCategoryEvents();
        this.registerColumnEvents();
        this.initSortableTable();
        this.initSelect2();
        
        console.log('Cədvəl ayarları səhifəsi uğurla işə salındı');
    },
    
    /**
     * Kateqoriya əməliyyatları üçün hadisələri qeydiyyata alır
     */
    registerCategoryEvents: function() {
        console.log('Kateqoriya hadisələri qeydiyyata alınır');
        
        // Yeni kateqoriya əlavə et düyməsi
        const addCategoryButtons = document.querySelectorAll('.add-category');
        addCategoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                console.log('Yeni kateqoriya düyməsinə kliklədi');
                CategoryOperations.openCreateModal();
            });
        });
        
        // Kateqoriya redaktə et düyməsi
        const editCategoryButtons = document.querySelectorAll('.edit-category');
        editCategoryButtons.forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.dataset.categoryId;
                console.log(`Kateqoriya redaktə düyməsinə kliklədi: ID=${categoryId}`);
                CategoryOperations.openEditModal(categoryId);
            });
        });
        
        // Kateqoriya sil düyməsi
        const deleteCategoryButtons = document.querySelectorAll('.delete-category');
        deleteCategoryButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const categoryId = this.dataset.categoryId;
                console.log(`Kateqoriya sil düyməsinə kliklədi: ID=${categoryId}`);
                CategoryOperations.confirmDelete(categoryId);
            });
        });
        
        // Kateqoriya statusunu dəyişdirmək üçün switch
        const categoryStatusSwitches = document.querySelectorAll('.category-status');
        categoryStatusSwitches.forEach(switchEl => {
            switchEl.addEventListener('change', function() {
                const categoryId = this.dataset.categoryId;
                const isActive = this.checked;
                console.log(`Kateqoriya statusu dəyişdirildi: ID=${categoryId}, Status=${isActive}`);
                CategoryOperations.toggleStatus(categoryId, isActive);
            });
        });
        
        // Kateqoriya formu təqdim edildi
        const categoryForm = document.getElementById('categoryForm');
        if (categoryForm) {
            categoryForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Kateqoriya formu təqdim edildi');
                CategoryOperations.submitForm(this);
            });
        }
        
        // Təyinat növü dəyişdirildi
        const assignmentTypeRadios = document.querySelectorAll('input[name="assigned_type"]');
        assignmentTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                console.log(`Təyinat növü dəyişdirildi: ${this.value}`);
                CategoryOperations.handleAssignmentTypeChange(this.value);
            });
        });
    },
    
    /**
     * Sütun əməliyyatları üçün hadisələri qeydiyyata alır
     */
    registerColumnEvents: function() {
        console.log('Sütun hadisələri qeydiyyata alınır');
        
        // Yeni sütun əlavə et düyməsi
        const addColumnButtons = document.querySelectorAll('.add-column');
        addColumnButtons.forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.dataset.categoryId;
                console.log(`Yeni sütun düyməsinə kliklədi: Kateqoriya ID=${categoryId}`);
                ColumnOperations.openCreateModal(categoryId);
            });
        });
        
        // Sütun redaktə et düyməsi
        const editColumnButtons = document.querySelectorAll('.edit-column');
        editColumnButtons.forEach(button => {
            button.addEventListener('click', function() {
                const columnId = this.dataset.columnId;
                console.log(`Sütun redaktə düyməsinə kliklədi: ID=${columnId}`);
                ColumnOperations.openEditModal(columnId);
            });
        });
        
        // Sütun sil düyməsi
        const deleteColumnButtons = document.querySelectorAll('.delete-column');
        deleteColumnButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const columnId = this.dataset.columnId;
                console.log(`Sütun sil düyməsinə kliklədi: ID=${columnId}`);
                ColumnOperations.confirmDelete(columnId);
            });
        });
        
        // Son tarix düyməsi
        const deadlineButtons = document.querySelectorAll('.set-deadline');
        deadlineButtons.forEach(button => {
            button.addEventListener('click', function() {
                const columnId = this.dataset.columnId;
                console.log(`Son tarix düyməsinə kliklədi: ID=${columnId}`);
                DeadlineOperations.openModal(columnId);
            });
        });
        
        // Sütun statusunu dəyişdirmək üçün switch
        const columnStatusSwitches = document.querySelectorAll('.column-status');
        columnStatusSwitches.forEach(switchEl => {
            switchEl.addEventListener('change', function() {
                const columnId = this.dataset.columnId;
                const isActive = this.checked;
                console.log(`Sütun statusu dəyişdirildi: ID=${columnId}, Status=${isActive}`);
                ColumnOperations.toggleStatus(columnId, isActive);
            });
        });
        
        // Sütun formu təqdim edildi
        const columnForm = document.getElementById('columnForm');
        if (columnForm) {
            columnForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Sütun formu təqdim edildi');
                ColumnOperations.submitForm(this);
            });
        }
        
        // Son tarix formu təqdim edildi
        const deadlineForm = document.getElementById('deadlineForm');
        if (deadlineForm) {
            deadlineForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Son tarix formu təqdim edildi');
                ColumnOperations.submitDeadlineForm(this);
            });
        }
        
        // Sütun növü dəyişdirildi
        const columnTypeSelects = document.querySelectorAll('select[name="type"]');
        columnTypeSelects.forEach(select => {
            select.addEventListener('change', function() {
                console.log(`Sütun növü dəyişdirildi: ${this.value}`);
                ColumnOperations.toggleTypeFields(this.value);
            });
        });
        
        // Seçim əlavə et düyməsi
        const addOptionButton = document.getElementById('addOptionButton');
        if (addOptionButton) {
            addOptionButton.addEventListener('click', function() {
                console.log('Seçim əlavə et düyməsinə kliklədi');
                ColumnOperations.addOption();
            });
        }
    },
    
    /**
     * Sortable cədvəli inisializasiya edir
     */
    initSortableTable: function() {
        const sortableTable = document.querySelector('.sortable');
        if (!sortableTable) return;
        
        const categoryId = document.querySelector('[data-category-id]')?.dataset.categoryId;
        if (!categoryId) return;
        
        console.log(`Sortable cədvəl inisializasiya edilir: Kateqoriya ID=${categoryId}`);
        TableUtils.initSortable('.sortable', categoryId);
    },
    
    /**
     * Select2 inisializasiya edir
     */
    initSelect2: function() {
        if (typeof $.fn.select2 === 'undefined') {
            console.warn('Select2 kitabxanası tapılmadı');
            return;
        }
        
        console.log('Select2 inisializasiya edilir');
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
    }
};

// Səhifə yükləndikdə işə sal
document.addEventListener('DOMContentLoaded', function() {
    TableInit.init();
});
