// Notification System
export const NotificationSystem = {
    types: {
        success: { icon: '✓', className: 'alert-success' },
        error: { icon: '✕', className: 'alert-danger' },
        warning: { icon: '⚠', className: 'alert-warning' },
        info: { icon: 'ℹ', className: 'alert-info' }
    },

    show(message, type = 'success', duration = 5000) {
        const config = this.types[type];
        const alertDiv = $('<div>')
            .addClass(`alert ${config.className} alert-dismissible fade show`)
            .html(`
                <span class="alert-icon">${config.icon}</span>
                <span class="alert-message">${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `);
        
        $('.notification-container').append(alertDiv);
        
        if (duration > 0) {
            setTimeout(() => {
                alertDiv.alert('close');
            }, duration);
        }

        return alertDiv;
    }
};

// Helper function for showing notifications
export function showNotification(message, type = 'success') {
    return NotificationSystem.show(message, type);
}