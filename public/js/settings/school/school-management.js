// school-management.js
const SELECTORS = {
    form: "#schoolForm",
    modal: "#adminModal",
    adminForm: "#adminForm",
    adminSelect: "#adminSelect",
    createModal: "#createModal",
    addDataModal: "#addDataModal",
    editDataModal: "#editDataModal",
    selectAllCheckbox: "#select-all",
    schoolCheckbox: ".school-checkbox",
};

const ENDPOINTS = {
    base: "/settings/personal/schools",
    assignAdmin: "/settings/personal/schools/{id}/admin/assign",
    removeAdmin: "/settings/personal/schools/{id}/admin/remove",
    availableAdmins: "/api/users/available-admins",
    deleteData: "/settings/personal/schools/data/{id}",
};

const CONFIG = {
    select2Options: {
        theme: "bootstrap-5",
        width: "100%",
        dropdownParent: $(SELECTORS.modal)
    },
};

const SchoolUtils = {
    handleAjaxResponse: function(response, form = null, modal = null) {
        if (response.success) {
            toastr.success(response.message);
            if (modal) modal.modal('hide');
            if (form) form[0].reset();
            window.location.reload();
        } else {
            toastr.error(response.message || 'Xəta baş verdi');
        }
    },

    displayErrorMessages: function(xhr) {
        const errors = xhr.responseJSON?.errors || {};
        Object.values(errors).forEach(messages => {
            messages.forEach(message => toastr.error(message));
        });
    },

    toggleSubmitButton: function(button, disabled) {
        button.prop('disabled', disabled);
        const spinner = button.find('.spinner-border');
        if (disabled) {
            button.data('original-text', button.html());
            button.html('<span class="spinner-border spinner-border-sm" role="status"></span> Gözləyin...');
        } else {
            button.html(button.data('original-text'));
        }
    }
};

class SchoolManager {
    constructor() {
        this.initializeComponents();
        this.setupEventListeners();
    }

    initializeComponents() {
        // Initialize select2 for admin select
        $(SELECTORS.adminSelect).select2({
            ...CONFIG.select2Options,
            ajax: {
                url: ENDPOINTS.availableAdmins,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results.map(user => ({
                            id: user.id,
                            text: user.text
                        }))
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            placeholder: 'Admin axtar...'
        });
    }

    setupEventListeners() {
        // Handle admin form submission
        $(SELECTORS.adminForm).on('submit', (e) => {
            e.preventDefault();
            const form = $(e.currentTarget);
            const schoolId = form.find('[name="school_id"]').val();
            const userId = $(SELECTORS.adminSelect).val();

            if (!userId) {
                toastr.error('Zəhmət olmasa admin seçin');
                return;
            }

            this.assignAdmin(schoolId, userId);
        });

        // Handle remove admin button click
        $('.remove-admin-btn').on('click', (e) => {
            const schoolId = $(e.currentTarget).data('school-id');
            this.removeAdmin(schoolId);
        });
    }

    async assignAdmin(schoolId, userId) {
        try {
            const response = await $.ajax({
                url: ENDPOINTS.assignAdmin.replace('{id}', schoolId),
                method: 'POST',
                data: {
                    user_id: userId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }
            });

            SchoolUtils.handleAjaxResponse(response, null, $(SELECTORS.modal));
        } catch (error) {
            toastr.error(error.responseJSON?.message || 'Xəta baş verdi');
        }
    }

    async removeAdmin(schoolId) {
        if (!confirm('Admini silmək istədiyinizə əminsiniz?')) {
            return;
        }

        try {
            const response = await $.ajax({
                url: ENDPOINTS.removeAdmin.replace('{id}', schoolId),
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            SchoolUtils.handleAjaxResponse(response);
        } catch (error) {
            toastr.error(error.responseJSON?.message || 'Xəta baş verdi');
        }
    }
}

// Global namespace
window.SchoolManager = {
    init: function() {
        const manager = new SchoolManager();
        return manager;
    },

    openAdminModal: function(schoolId) {
        // Reset form and select2
        $(SELECTORS.adminForm)[0].reset();
        $(SELECTORS.adminSelect).val(null).trigger('change');

        // Set school ID
        $(SELECTORS.adminForm).find('[name="school_id"]').val(schoolId);

        // Show modal
        $(SELECTORS.modal).modal('show');
    },

    removeAdmin: function(schoolId) {
        const manager = new SchoolManager();
        manager.removeAdmin(schoolId);
    }
};

// Initialize when document is ready
$(function () {
    window.schoolManager = SchoolManager.init();
});

