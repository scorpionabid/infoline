// Global App Configuration
const App = {
    init() {
        this.setupAjax();
        this.setupSelect2();
        this.setupValidation();
        this.setupAlerts();
    },

    setupAjax() {
        // Setup global Ajax error handling
        $(document).ajaxError((event, jqXHR, settings, error) => {
            console.error('Ajax Error:', error);
            if (jqXHR.status === 422) {
                const errors = jqXHR.responseJSON.errors;
                Object.keys(errors).forEach(field => {
                    const input = $(`[name="${field}"]`);
                    input.addClass('is-invalid');
                    input.next('.invalid-feedback').text(errors[field][0]);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Xəta!',
                    text: 'Əməliyyat zamanı xəta baş verdi.'
                });
            }
        });

        // Add loading state to buttons during Ajax
        $(document).ajaxStart(() => {
            $('button[type="submit"]').prop('disabled', true).append('<span class="spinner-border spinner-border-sm ms-2"></span>');
        }).ajaxComplete(() => {
            $('button[type="submit"]').prop('disabled', false).find('.spinner-border').remove();
        });
    },

    setupSelect2() {
        // Initialize Select2 for all select elements
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }
    },

    setupValidation() {
        // Add custom validation methods
        if ($.validator) {
            $.validator.addMethod('utisCode', function(value, element) {
                return this.optional(element) || /^[0-9]{7}$/.test(value);
            }, 'UTİS kodu 7 rəqəmdən ibarət olmalıdır');
        }
    },

    setupAlerts() {
        // Setup SweetAlert2 defaults
        if (typeof Swal !== 'undefined') {
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-primary ms-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            });
        }
    },

    showSuccessMessage(message) {
        Swal.fire({
            icon: 'success',
            title: 'Uğurlu!',
            text: message,
            timer: 2000,
            showConfirmButton: false
        });
    },

    showErrorMessage(message) {
        Swal.fire({
            icon: 'error',
            title: 'Xəta!',
            text: message
        });
    },

    showConfirmDialog(options) {
        return Swal.fire({
            title: options.title || 'Əminsiniz?',
            text: options.text || 'Bu əməliyyatı geri qaytarmaq mümkün olmayacaq!',
            icon: options.icon || 'warning',
            showCancelButton: true,
            confirmButtonText: options.confirmButtonText || 'Bəli',
            cancelButtonText: options.cancelButtonText || 'Xeyr'
        });
    },

    resetForm(form) {
        form[0].reset();
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');
        if ($.fn.select2) {
            form.find('select').val(null).trigger('change');
        }
    }
};

// Initialize App when document is ready
$(document).ready(() => {
    App.init();
});
