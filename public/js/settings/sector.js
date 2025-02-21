// Sector management configuration and constants
const SectorManager = {
  config: {
    endpoints: {
      base: '/settings/personal/sectors',
      create: '/settings/personal/sectors/create',
      update: '/settings/personal/sectors/:id',
      delete: '/settings/personal/sectors/:id',
      assignAdmin: '/settings/personal/sectors/:id/assign-admin'
    },
    selectors: {
      form: '#sectorForm',
      adminForm: '#sectorAdminForm',
      modal: '#sectorModal',
      adminModal: '#sectorAdminModal',
      createButton: '#createSectorBtn',
      editButton: '.edit-sector-btn',
      deleteButton: '.delete-sector-btn',
      assignAdminButton: '.assign-admin-btn',
      submitButton: '#sectorSubmitBtn',
      adminSubmitButton: '#adminSubmitBtn',
      nameInput: '[name="name"]',
      descriptionInput: '[name="description"]',
      errorContainer: '#sectorFormErrors',
      adminErrorContainer: '#adminFormErrors'
    },
    validation: {
      rules: {
        name: {
          required: true,
          minlength: 2,
          maxlength: 255
        },
        description: {
          required: true,
          maxlength: 1000
        }
      },
      messages: {
        name: {
          required: 'Sektor adı daxil edilməlidir',
          minlength: 'Sektor adı ən az 2 simvol olmalıdır',
          maxlength: 'Sektor adı ən çox 255 simvol ola bilər'
        },
        description: {
          required: 'Sektor təsviri daxil edilməlidir',
          maxlength: 'Sektor təsviri ən çox 1000 simvol ola bilər'
        }
      }
    }
  },
  init() {
    console.log('Initializing Sector Manager...');
    this.form = $(this.config.selectors.form);
    this.modal = $(this.config.selectors.modal);
    this.adminForm = $(this.config.selectors.adminForm);
    this.adminModal = $(this.config.selectors.adminModal);
    this.setupValidation();
    this.setupAdminValidation();
    this.setupEventListeners();
    console.log('Sector Manager initialized successfully');
  },

  setupValidation() {
    console.log('Setting up sector form validation...');
    if (!$.validator) {
      console.error('jQuery Validator plugin not found');
      return;
    }

    this.form.validate({
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
    console.log('Sector form validation setup completed');
  },

  setupAdminValidation() {
    console.log('Setting up admin form validation...');
    if (!$.validator) {
      console.error('jQuery Validator plugin not found');
      return;
    }

    this.adminForm.validate({
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
    console.log('Setting up sector event listeners...');
    
    // Create button
    $(this.config.selectors.createButton).on('click', () => {
      console.log('Create sector button clicked');
      this.showModal();
    });

    // Edit button
    $(document).on('click', this.config.selectors.editButton, (e) => {
      const sectorId = $(e.currentTarget).data('sector-id');
      console.log(`Edit sector button clicked for sector ID: ${sectorId}`);
      this.showModal(sectorId);
    });

    // Delete button
    $(document).on('click', this.config.selectors.deleteButton, (e) => {
      const sectorId = $(e.currentTarget).data('sector-id');
      console.log(`Delete sector button clicked for sector ID: ${sectorId}`);
      this.handleDelete(sectorId);
    });

    // Assign Admin button
    $(document).on('click', this.config.selectors.assignAdminButton, (e) => {
      const sectorId = $(e.currentTarget).data('sector-id');
      console.log(`Assign Admin button clicked for sector ID: ${sectorId}`);
      this.showAdminModal(sectorId);
    });

    // Modal events
    this.modal.on('hidden.bs.modal', () => {
      console.log('Sector modal hidden - resetting form');
      this.resetForm();
    });

    this.adminModal.on('hidden.bs.modal', () => {
      console.log('Admin modal hidden - resetting form');
      this.resetAdminForm();
    });

    console.log('Sector event listeners setup completed');
  },

  async handleSubmit(event) {
    event.preventDefault();
    console.log('Handling sector form submission...');

    if (!this.form.valid()) {
      console.log('Sector form validation failed');
      return;
    }

    const submitButton = $(this.config.selectors.submitButton);
    const originalText = submitButton.html();
    const sectorId = this.form.data('sector-id');
    const isEdit = !!sectorId;

    try {
      submitButton
        .prop('disabled', true)
        .html('<i class="fas fa-spinner fa-spin"></i> Gözləyin...');

      const formData = this.form.serialize();
      const url = isEdit 
        ? this.config.endpoints.update.replace(':id', sectorId)
        : this.config.endpoints.create;

      console.log(`Sending ${isEdit ? 'update' : 'create'} request to: ${url}`);
      const response = await this.sendRequest(url, formData, isEdit ? 'PUT' : 'POST');
      
      await this.handleSuccess(response);
    } catch (error) {
      this.handleError(error);
    } finally {
      submitButton.prop('disabled', false).html(originalText);
    }
  },

  async handleAdminSubmit(event) {
    const sectorId = $(this.config.selectors.adminForm).data('sector-id');
    const formData = new FormData($(this.config.selectors.adminForm)[0]);
    
    this.sendRequest(
      this.config.endpoints.assignAdmin.replace(':id', sectorId),
      formData
    ).then(response => {
      if (response.success) {
        this.handleAdminSuccess(response);
      } else {
        this.handleError(response);
      }
    }).catch(error => {
      this.handleError(error);
    });
  },

  async sendRequest(url, formData, method = 'POST') {
    console.log(`Sending ${method} request to ${url}`);
    
    const response = await $.ajax({
      url: url,
      method: method,
      data: formData,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept': 'application/json',
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    });

    if (!response.success) {
      console.error('Request failed:', response);
      throw new Error(response.message || 'Xəta baş verdi');
    }

    console.log('Request successful:', response);
    return response;
  },

  async handleSuccess(response) {
    console.log('Operation successful:', response);
    
    await Swal.fire({
      icon: 'success',
      title: 'Uğurlu!',
      text: response.message,
      timer: 1500,
      showConfirmButton: false
    });

    this.modal.modal('hide');
    window.location.reload();
  },

  async handleAdminSuccess(response) {
    $(this.config.selectors.adminModal).modal('hide');
    this.resetAdminForm();
    
    // Yeniləmə mesajını göstər
    Swal.fire({
      icon: 'success',
      title: 'Uğurlu!',
      text: response.message || 'Admin uğurla təyin edildi',
      timer: 2000
    });

    // DataTable-ı yenilə
    $('#sectors-table').DataTable().ajax.reload();
  },

  handleError(error) {
    console.error('Error occurred:', error);

    if (error.responseJSON?.errors) {
      this.showValidationErrors(error.responseJSON.errors);
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Xəta!',
        text: error.responseJSON?.message || 'Sistem xətası baş verdi',
        confirmButtonText: 'Bağla'
      });
    }
  },

  showValidationErrors(errors) {
    console.log('Showing validation errors:', errors);
    
    Object.entries(errors).forEach(([field, [message]]) => {
      const input = this.form.find(`[name="${field}"]`);
      input.addClass('is-invalid').next('.invalid-feedback').text(message);
    });

    const firstError = this.form.find('.is-invalid').first();
    if (firstError.length) {
      firstError[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  },

  async showModal(sectorId = null) {
    console.log(`Showing sector modal ${sectorId ? 'for editing' : 'for creation'}`);
    
    this.resetForm();
    
    if (sectorId) {
      try {
        const response = await this.sendRequest(
          this.config.endpoints.base + '/' + sectorId,
          null,
          'GET'
        );
        this.fillForm(response.data);
        this.form.data('sector-id', sectorId);
        $(this.config.selectors.modal + 'Label').text('Sektoru Redaktə Et');
      } catch (error) {
        console.error('Error fetching sector data:', error);
        return;
      }
    } else {
      $(this.config.selectors.modal + 'Label').text('Yeni Sektor');
    }

    this.modal.modal('show');
    modal.show();
  },

  showAdminModal(sectorId) {
    $(this.config.selectors.adminForm).data('sector-id', sectorId);
    $(this.config.selectors.adminModal).modal('show');
  },

  resetForm() {
    this.form[0].reset();
    this.form.find(".is-invalid").removeClass("is-invalid");
    this.form.find(".invalid-feedback").text("");
    this.form
      .find(this.config.selectors.errorContainer)
      .addClass("d-none")
      .html("");
  },

  resetAdminForm() {
    $(this.config.selectors.adminForm)[0].reset();
    $(this.config.selectors.adminForm).find('.is-invalid').removeClass('is-invalid');
    $(this.config.selectors.adminErrorContainer).empty();
  },

  async handleDelete(sectorId) {
    const result = await Swal.fire({
      title: 'Əminsiniz?',
      text: 'Bu sektoru silmək istədiyinizə əminsiniz?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Bəli, sil!',
      cancelButtonText: 'Xeyr',
      reverseButtons: true
    });

    if (!result.isConfirmed) return;

    try {
      const url = this.config.endpoints.delete.replace(':id', sectorId);
      const response = await this.sendRequest(url, {}, 'DELETE');
      
      await this.handleSuccess(response);
      $(`${this.config.selectors.deleteButton}[data-sector-id="${sectorId}"]`)
        .closest('tr')
        .fadeOut(() => $(this).remove());
    } catch (error) {
      this.handleError(error);
    }
  }
};

// Initialize when document is ready
$(document).ready(() => SectorManager.init());

$(document).ready(function() {
    // Initialize DataTable
    var table = $('#sectors-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/settings/personal/sectors/data',
            data: function(d) {
                d.region = $('#region-filter').val();
                d.admin_status = $('#admin-status-filter').val();
            }
        },
        columns: [
            {data: 'name', name: 'name'},
            {data: 'region.name', name: 'region.name'},
            {data: 'schools_count', name: 'schools_count'},
            {data: 'admin.full_name', name: 'admin.full_name', defaultContent: 'Təyin edilməyib'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false}
        ],
        order: [[0, 'asc']],
        language: {
            url: '/assets/libs/datatables/i18n/az.json'
        },
        responsive: true
    });

    // Filter handling
    $('#region-filter, #admin-status-filter').change(function() {
        table.draw();
    });

    // Delete sector handling
    $(document).on('click', '.delete-sector', function(e) {
        e.preventDefault();
        var button = $(this);
        var sectorId = button.data('id');

        Swal.fire({
            title: 'Əminsiniz?',
            text: 'Bu sektoru silmək istədiyinizə əminsiniz?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Bəli, sil!',
            cancelButtonText: 'Ləğv et'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/settings/personal/sectors/${sectorId}`,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire(
                            'Silindi!',
                            response.message,
                            'success'
                        );
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Xəta!',
                            xhr.responseJSON.error || 'Sektor silinərkən xəta baş verdi.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    // Form validation
    if ($('#sectorForm').length) {
        $('#sectorForm').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3
                },
                region_id: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: 'Sektor adını daxil edin',
                    minlength: 'Sektor adı minimum 3 simvol olmalıdır'
                },
                region_id: {
                    required: 'Region seçin'
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    }
});
