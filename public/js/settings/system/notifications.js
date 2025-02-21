// Notification Settings Management
const NotificationSettings = {
    config: {
        selectors: {
            form: '#notificationSettingsForm',
            emailNotifications: '#emailNotifications',
            deadlineReminders: '#deadlineReminders',
            systemAlerts: '#systemAlerts',
            reminderDays: '#reminderDays',
            submitButton: '#saveNotificationSettings'
        },
        endpoints: {
            update: '/settings/system/notifications'
        }
    },

    init() {
        this.setupEventListeners();
    },

    setupEventListeners() {
        const { selectors } = this.config;
        
        $(selectors.form).on('submit', (e) => this.handleSubmit(e));
        
        // Enable/disable reminder days input based on deadline reminders checkbox
        $(selectors.deadlineReminders).on('change', (e) => {
            $(selectors.reminderDays).prop('disabled', !e.target.checked);
        });
    },

    async handleSubmit(e) {
        e.preventDefault();
        const form = $(e.currentTarget);
        const submitButton = $(this.config.selectors.submitButton);
        
        // Disable submit button and show loading state
        submitButton.prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin me-2"></i>Yadda saxlanılır...');
        
        try {
            const response = await this.sendRequest(form);
            
            if (response.success) {
                this.showSuccess(response.message);
            } else {
                throw new Error(response.message || 'Xəta baş verdi');
            }
        } catch (error) {
            this.showError(error.message);
        } finally {
            // Re-enable submit button and restore original text
            submitButton.prop('disabled', false)
                .html('Yadda saxla');
        }
    },

    async sendRequest(form) {
        const response = await fetch(this.config.endpoints.update, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({
                email_notifications: $(this.config.selectors.emailNotifications).is(':checked'),
                deadline_reminders: $(this.config.selectors.deadlineReminders).is(':checked'),
                system_alerts: $(this.config.selectors.systemAlerts).is(':checked'),
                reminder_days: parseInt($(this.config.selectors.reminderDays).val())
            })
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Server xətası');
        }

        return response.json();
    },

    showSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: 'Uğurlu!',
            text: message,
            timer: 1500,
            showConfirmButton: false
        });
    },

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Xəta!',
            text: message
        });
    }
};

// Initialize when document is ready
$(document).ready(() => {
    NotificationSettings.init();
});