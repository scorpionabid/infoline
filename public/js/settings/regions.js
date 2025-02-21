const RegionManager = {
    config: {
        endpoints: {
            base: '/settings/personal/regions',
            create: '/settings/personal/regions/create',
            update: '/settings/personal/regions/:id',
            delete: '/settings/personal/regions/:id',
            assignAdmin: '/settings/personal/regions/:id/assign-admin'
        },
        selectors: {
            form: '#regionForm',
            adminForm: '#regionAdminForm',
            modal: '#regionModal',
            adminModal: '#regionAdminModal',
            createButton: '#createRegionBtn',
            editButton: '.edit-region-btn',
            deleteButton: '.delete-region-btn',
            assignAdminButton: '.assign-admin-btn',
            submitButton: '#regionSubmitBtn',
            adminSubmitButton: '#adminSubmitBtn',
            nameInput: '[name="name"]',
            descriptionInput: '[name="description"]',
            errorContainer: '#regionFormErrors',
            adminErrorContainer: '#adminFormErrors'
        },
        validation: {
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 255
                },
                code: {
                    required: true,
                    maxlength: 50
                },
                phone: {
                    required: true,
                    maxlength: 20
                }
            },
            messages: {
                name: {
                    required: 'Region adı daxil edilməlidir',
                    minlength: 'Region adı ən az 2 simvol olmalıdır',
                    maxlength: 'Region adı ən çox 255 simvol ola bilər'
                },
                code: {
                    required: 'Region kodu daxil edilməlidir',
                    maxlength: 'Region kodu ən çox 50 simvol ola bilər'
                },
                phone: {
                    required: 'Telefon nömrəsi daxil edilməlidir',
                    maxlength: 'Telefon nömrəsi ən çox 20 simvol ola bilər'
                }
            }
        }
    },

    init() {
        this.setupValidation();
        this.setupAdminValidation();
        this.setupEventListeners();
        this.initializeDataTable();
    },

    setupValidation() {
        $(this.config.selectors.form).validate({
            rules: this.config.validation.rules,
            messages: this.config.validation.messages,
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            highlight: (element) => $(element).addClass('is-invalid'),
            unhighlight: (element) => $(element).removeClass('is-invalid'),
            submitHandler: (form, event) => {
                event.preventDefault();
                this.handleSubmit(event);
            }
        });
    },

    setupAdminValidation() {
        $(this.config.selectors.adminForm).validate({
            rules: {
                full_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 255
                },
                email: {
                    required: true,
                    email: true
                },
                phone: {
                    required: true,
                    maxlength: 20
                }
            },
            messages: {
                full_name: {
                    required: 'Ad Soyad daxil edilməlidir',
                    minlength: 'Ad Soyad ən az 3 simvol olmalıdır',
                    maxlength: 'Ad Soyad ən çox 255 simvol ola bilər'
                },
                email: {
                    required: 'Email daxil edilməlidir',
                    email: 'Düzgün email formatı daxil edin'
                },
                phone: {
                    required: 'Telefon nömrəsi daxil edilməlidir',
                    maxlength: 'Telefon nömrəsi ən çox 20 simvol ola bilər'
                }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            highlight: (element) => $(element).addClass('is-invalid'),
            unhighlight: (element) => $(element).removeClass('is-invalid'),
            submitHandler: (form, event) => {
                event.preventDefault();
                this.handleAdminSubmit(event);
            }
        });
    },

    setupEventListeners() {
        $(this.config.selectors.createButton).on('click', () => this.showModal());
        $(document).on('click', this.config.selectors.editButton, (e) => {
            const regionId = $(e.currentTarget).data('region-id');
            this.showModal(regionId);
        });
        $(document).on('click', this.config.selectors.deleteButton, (e) => {
            const regionId = $(e.currentTarget).data('region-id');
            this.handleDelete(regionId);
        });
        $(document).on('click', this.config.selectors.assignAdminButton, (e) => {
            const regionId = $(e.currentTarget).data('region-id');
            this.showAdminModal(regionId);
        });
    },

    initializeDataTable() {
        $('#regions-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: this.config.endpoints.base + '/data',
                data: (d) => {
                    d.status = $('#status-filter').val();
                }
            },
            columns: [
                {data: 'name', name: 'name'},
                {data: 'code', name: 'code'},
                {data: 'sectors_count', name: 'sectors_count'},
                {data: 'schools_count', name: 'schools_count'},
                {
                    data: 'admin',
                    name: 'admin.full_name',
                    render: (data) => {
                        if (data) {
                            return `<span class="badge bg-success">${data.full_name}</span>`;
                        }
                        return '<span class="badge bg-warning">Təyin edilməyib</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: (data) => {
                        let buttons = `
                            <a href="${this.config.endpoints.base}/${data.id}/edit" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-region-btn" data-region-id="${data.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        `;
                        
                        if (!data.admin) {
                            buttons += `
                                <button class="btn btn-sm btn-success assign-admin-btn" data-region-id="${data.id}">
                                    <i class="fas fa-user-shield"></i>
                                </button>
                            `;
                        }
                        
                        return buttons;
                    }
                }
            ],
            order: [[0, 'asc']],
            language: {
                url: '/assets/libs/datatables/i18n/az.json'
            }
        });
    },

    handleSubmit(event) {
        const form = $(event.target);
        const formData = new FormData(form[0]);
        const regionId = form.data('region-id');
        
        const url = regionId ? 
            this.config.endpoints.update.replace(':id', regionId) : 
            this.config.endpoints.create;
        
        this.sendRequest(url, formData, regionId ? 'PUT' : 'POST')
            .then(response => {
                if (response.success) {
                    this.handleSuccess(response);
                }
            })
            .catch(error => this.handleError(error));
    },

    handleAdminSubmit(event) {
        const form = $(event.target);
        const formData = new FormData(form[0]);
        const regionId = form.data('region-id');
        
        this.sendRequest(
            this.config.endpoints.assignAdmin.replace(':id', regionId),
            formData
        ).then(response => {
            if (response.success) {
                this.handleSuccess(response);
                $(this.config.selectors.adminModal).modal('hide');
            }
        }).catch(error => this.handleError(error));
    },

    handleDelete(regionId) {
        Swal.fire({
            title: 'Əminsiniz?',
            text: 'Bu region silinəcək!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Bəli, sil!',
            cancelButtonText: 'Xeyr'
        }).then((result) => {
            if (result.isConfirmed) {
                this.sendRequest(
                    this.config.endpoints.delete.replace(':id', regionId),
                    {},
                    'DELETE'
                ).then(response => {
                    if (response.success) {
                        this.handleSuccess(response);
                    }
                }).catch(error => this.handleError(error));
            }
        });
    },

    showAdminModal(regionId) {
        const modal = $(this.config.selectors.adminModal);
        const form = modal.find(this.config.selectors.adminForm);
        
        form.data('region-id', regionId);
        form[0].reset();
        $(this.config.selectors.adminErrorContainer).empty();
        
        modal.modal('show');
    },

    sendRequest(url, formData, method = 'POST') {
        return fetch(url, {
            method: method,
            body: formData,
            headers: this.getHeaders(),
            credentials: 'same-origin'
        }).then(response => response.json());
    },

    handleSuccess(response) {
        Swal.fire({
            icon: 'success',
            title: 'Uğurlu!',
            text: response.message,
            timer: 2000
        });
        
        this.resetForm();
        $('#regions-table').DataTable().ajax.reload();
        $(this.config.selectors.modal).modal('hide');
    },

    handleError(error) {
        if (error.status === 422) {
            this.showValidationErrors(error.responseJSON.errors);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Xəta!',
                text: error.responseJSON?.message || 'Xəta baş verdi'
            });
        }
    },

    showValidationErrors(errors) {
        const errorContainer = $(this.config.selectors.errorContainer);
        errorContainer.empty();
        
        Object.keys(errors).forEach(field => {
            errors[field].forEach(message => {
                errorContainer.append(`
                    <div class="alert alert-danger">
                        ${message}
                    </div>
                `);
            });
        });
    },

    getHeaders() {
        return {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        };
    }
};

$(document).ready(() => RegionManager.init());