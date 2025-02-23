// Sector management configuration and constants
const SectorManager = {
    config: {
        endpoints: {
            base: '/settings/personal/sectors',
            data: '/settings/personal/sectors/data',
            cleanupDeleted: '/settings/personal/sectors/cleanup-deleted'
        },
        selectors: {
            form: '#sectorForm',
            modal: '#sectorModal',
            modalTitle: '#sectorModalLabel',
            submitBtn: '#submitBtn',
            nameInput: '#name',
            regionSelect: '#region_id',
            idInput: '#sectorId'
        }
    },

    init() {
        this.initializeComponents();
        this.setupEventListeners();
        this.loadSectors();
        this.cleanupDeletedSectors();
    },

    initializeComponents() {
        this.form = $(this.config.selectors.form);
        this.modal = $(this.config.selectors.modal);
        this.modalTitle = $(this.config.selectors.modalTitle);
        this.submitBtn = $(this.config.selectors.submitBtn);
        this.nameInput = $(this.config.selectors.nameInput);
        this.regionSelect = $(this.config.selectors.regionSelect);
        this.idInput = $(this.config.selectors.idInput);
        
        // Initialize Select2
        if ($.fn.select2) {
            this.regionSelect.select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: this.modal,
                placeholder: 'Region seçin'
            }).on('select2:open', () => {
                // Select2 açılanda invalid class-ı sil
                this.regionSelect.removeClass('is-invalid');
            });
        }
    },

    async cleanupDeletedSectors() {
        try {
            const response = await fetch(this.config.endpoints.cleanupDeleted, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            
            if (data.success && data.message) {
                console.log('Cleanup result:', data.message);
            }
        } catch (error) {
            console.error('Error cleaning up deleted sectors:', error);
        }
    },

    setupEventListeners() {
        // Form submit
        this.form.on('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });

        // Modal events
        this.modal.on('show.bs.modal', (e) => {
            const button = $(e.relatedTarget);
            const sectorId = button.data('sector-id');
            
            if (sectorId) {
                this.loadSectorData(sectorId);
            } else {
                this.resetForm();
            }
        });

        this.modal.on('hidden.bs.modal', () => {
            this.resetForm();
        });

        // Edit button click
        $(document).on('click', '.edit-sector-btn', (e) => {
            e.preventDefault();
            const sectorId = $(e.currentTarget).data('sector-id');
            this.loadSectorData(sectorId);
            this.modal.modal('show');
        });

        // Delete button click
        $(document).on('click', '.delete-sector-btn', (e) => {
            e.preventDefault();
            const sectorId = $(e.currentTarget).data('sector-id');
            this.deleteSector(sectorId);
        });

        // Region select change
        this.regionSelect.on('change', () => {
            this.regionSelect.removeClass('is-invalid');
            this.regionSelect.siblings('.invalid-feedback').text('');
        });

        // Name input change
        this.nameInput.on('input', () => {
            this.nameInput.removeClass('is-invalid');
            this.nameInput.siblings('.invalid-feedback').text('');
        });
    },

    async loadSectorData(sectorId) {
        try {
            const response = await fetch(`${this.config.endpoints.base}/${sectorId}/edit`);
            const data = await response.json();
            
            if (data.success) {
                this.fillForm(data.sector);
                this.modalTitle.text('Sektoru Redaktə Et');
            } else {
                toastr.error(data.message || 'Məlumatlar yüklənərkən xəta baş verdi');
            }
        } catch (error) {
            console.error('Error loading sector data:', error);
            toastr.error('Məlumatlar yüklənərkən xəta baş verdi');
        }
    },

    fillForm(sector) {
        this.idInput.val(sector.id);
        this.nameInput.val(sector.name);
        
        // Region seçimini yenilə və disabled et
        if (this.regionSelect.find(`option[value='${sector.region_id}']`).length) {
            this.regionSelect
                .val(sector.region_id)
                .trigger('change')
                .prop('disabled', true);
        }
        
        // Modal başlığını yenilə
        this.modalTitle.text('Sektoru Redaktə Et');
    },

    resetForm() {
        this.form[0].reset();
        this.idInput.val('');
        this.modalTitle.text('Yeni Sektor');
        
        // Region seçimini sıfırla və enable et
        this.regionSelect
            .val('')
            .trigger('change')
            .prop('disabled', false);
            
        this.form.find('.is-invalid').removeClass('is-invalid');
        this.form.find('.invalid-feedback').text('');
    },

    async handleSubmit() {
        const submitBtn = this.submitBtn;
        const originalText = submitBtn.html();
        
        try {
            // Form validation
            this.form.find('.is-invalid').removeClass('is-invalid');
            this.form.find('.invalid-feedback').text('');
            
            const sectorId = this.idInput.val();
            const regionId = this.regionSelect.val();
            const name = this.nameInput.val().trim();
            let hasError = false;
            
            // Yeni sektor yaradılanda region seçimini yoxla
            if (!sectorId && !regionId) {
                this.regionSelect.addClass('is-invalid');
                this.regionSelect.siblings('.invalid-feedback').text('Region seçilməlidir');
                hasError = true;
            }
            
            if (!name) {
                this.nameInput.addClass('is-invalid');
                this.nameInput.siblings('.invalid-feedback').text('Sektor adı daxil edilməlidir');
                hasError = true;
            }
            
            if (hasError) {
                return;
            }
            
            submitBtn
                .prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin me-1"></i> Gözləyin...');

            const formData = new FormData(this.form[0]);
            
            let url = this.config.endpoints.base;
            let method = 'POST';
            
            if (sectorId) {
                url = `${url}/${sectorId}`;
                method = 'PUT';
                formData.append('_method', 'PUT');
            }

            const response = await fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const data = await response.json();
            
            if (!response.ok) {
                throw data;
            }

            toastr.success(data.message);
            this.modal.modal('hide');
            this.loadSectors();
            
        } catch (error) {
            console.error('Error:', error);
            
            if (error.errors) {
                Object.entries(error.errors).forEach(([field, messages]) => {
                    const input = this.form.find(`[name="${field}"]`);
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').text(messages[0]);
                });
            } else {
                toastr.error(error.message || 'Xəta baş verdi');
            }
        } finally {
            submitBtn.prop('disabled', false).html(originalText);
        }
    },

    async deleteSector(sectorId) {
        const result = await Swal.fire({
            title: 'Əminsiniz?',
            text: 'Bu sektoru birdəfəlik silmək istədiyinizdən əminsiniz?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Bəli, sil',
            cancelButtonText: 'Ləğv et',
            confirmButtonColor: '#dc3545'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`${this.config.endpoints.base}/${sectorId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                const data = await response.json();
                
                if (!response.ok) {
                    throw data;
                }

                toastr.success(data.message);
                this.loadSectors();
                
            } catch (error) {
                console.error('Error:', error);
                toastr.error(error.message || 'Sektor silinərkən xəta baş verdi');
            }
        }
    },

    async loadSectors() {
        try {
            const response = await fetch(this.config.endpoints.data);
            const data = await response.json();
            
            if (!data.data) {
                throw new Error('Invalid data format');
            }

            // Group sectors by region
            const sectors = data.data;
            const regionSectors = {};
            
            sectors.forEach(sector => {
                if (!regionSectors[sector.region_id]) {
                    regionSectors[sector.region_id] = [];
                }
                regionSectors[sector.region_id].push(sector);
            });

            // Update each region's sectors
            $('.region-sectors').each(function() {
                const regionId = $(this).data('region-id');
                const sectorsList = regionSectors[regionId] || [];
                
                // Clear existing sectors
                $(this).empty();
                
                if (sectorsList.length === 0) {
                    $(this).append(`
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Bu regionda sektor yoxdur
                            </td>
                        </tr>
                    `);
                } else {
                    sectorsList.forEach(sector => {
                        $(this).append(`
                            <tr>
                                <td>${sector.name}</td>
                                <td>${sector.admin_name}</td>
                                <td>${sector.info}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary btn-sm edit-sector-btn" data-sector-id="${sector.id}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-sector-btn" data-sector-id="${sector.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `);
                    });
                }
            });
            
        } catch (error) {
            console.error('Error loading sectors:', error);
            toastr.error('Sektorlar yüklənərkən xəta baş verdi');
        }
    }
};

// Initialize when document is ready
$(document).ready(() => {
    if (typeof $ !== 'undefined') {
        SectorManager.init();
    }
});
