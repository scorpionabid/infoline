// WebSocket handling
import { WebSocketClient } from './modules/websocket.js';
import { NotificationSystem, showNotification } from './modules/notifications.js';
import { CategoryManager } from './modules/categories.js';
import { ColumnManager } from './modules/columns.js';
import { SchoolManager } from './modules/schools.js';
import { MobileUI } from './modules/mobile.js';
import { Utils } from './modules/utils.js';

// Initialize managers
const categoryManager = new CategoryManager();
const columnManager = new ColumnManager();
const schoolManager = new SchoolManager();

// Initialize WebSocket
const wsClient = new WebSocketClient(`ws://${window.location.hostname}:${window.appConfig.wsPort}`);

wsClient.on('connect', () => {
    showNotification('Real-time bağlantı quruldu', 'success');
});

wsClient.on('update', (data) => {
    columnManager.updateCell(data.school_id, data.column_id, data.value);
});

// Document ready handler
$(document).ready(function() {
    // Initialize UI components
    MobileUI.init();
    
    // Load initial data
    categoryManager.loadCategories();
    columnManager.loadTableData();
    
    // Attach event handlers
    attachEventHandlers();
});

// Attach global event handlers
function attachEventHandlers() {
    // Generate random password
    $('#generatePassword').click(function() {
        const password = Utils.generatePassword();
        $('#adminPassword').val(password);
    });

    // Column type change handler
    $('#columnType').change(function() {
        const type = $(this).val();
        $('#optionsDiv').toggle(type === 'select');
    });
    
    // Clear form inputs when modal is closed
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#optionsDiv').hide();
    });

    // Prevent leaving page with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (columnManager.hasUnsavedChanges()) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
}