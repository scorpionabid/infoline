// Log Management
const LogManager = {
    config: {
        selectors: {
            logList: '#logList',
            viewButton: '.view-log',
            deleteButton: '.delete-log',
            searchInput: '#logSearch',
            dateFilter: '#logDateFilter',
            logViewer: '#logViewer',
            logContent: '#logContent'
        },
        endpoints: {
            view: '/settings/system/logs/',
            delete: '/settings/system/logs/'
        },
        templates: {
            logItem: (log) => `
                <tr id="log-${log.name}">
                    <td>${log.name}</td>
                    <td>${this.formatSize(log.size)}</td>
                    <td>${log.updated_at}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-info view-log" 
                                    data-log="${log.name}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-log" 
                                    data-log="${log.name}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `
        }
    },

    init() {
        this.setupEventListeners();
        this.setupFilters();
    },

    setupEventListeners() {
        const { selectors } = this.config;
        
        $(document).on('click', selectors.viewButton, (e) => this.handleView(e));
        $(document).on('click', selectors.deleteButton, (e) => this.handleDelete(e));
        
        // Close log viewer when clicking outside
        $(selectors.logViewer).on('click', '.close', () => {
            $(selectors.logViewer).modal('hide');
        });
    },

    setupFilters() {
        const { selectors } = this.config;
        
        // Setup search filter
        $(selectors.searchInput).on('keyup', (e) => {
            const searchText = e.target.value.toLowerCase();
            
            $(`${selectors.logList} tr`).each((i, row) => {
                const logName = $(row).find('td:first').text().toLowerCase();
                $(row).toggle(logName.includes(searchText));
            });
        });

        // Setup date filter
        $(selectors.dateFilter).on('change', (e) => {
            const selectedDate = e.target.value;
            
            if (!selectedDate) {
                $(`${selectors.logList} tr`).show();
                return;
            }

            $(`${selectors.logList} tr`).each((i, row) => {
                const logDate = $(row).find('td:nth-child(3)').text().split(' ')[0];
                $(row).toggle(logDate === selectedDate);
            });
        });
    },

    async handleView(e) {
        const button = $(e.currentTarget);
        const log = button.data('log');
        
        try {
            const response = await this.sendRequest(this.config.endpoints.view + log);
            
            if (response.success) {
                $(this.config.selectors.logContent).html(this.formatLogContent(response.content));
                $(this.config.selectors.logViewer).modal('show');
            } else {
                throw new Error(response.message || 'Log məzmunu yüklənərkən xəta baş verdi');
            }
        } catch (error) {
            this.showError(error.message);
        }
    },

    async handleDelete(e) {
        const button = $(e.currentTarget);
        const log = button.data('log');
        
        const result = await Swal.fire({
            title: 'Əminsiniz?',
            text: 'Bu log faylını silmək istədiyinizə əminsiniz?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Bəli, sil!',
            cancelButtonText: 'Xeyr',
            reverseButtons: true
        });
        
        if (!result.isConfirmed) return;
        
        try {
            const response = await this.sendRequest(this.config.endpoints.delete + log, {
                method: 'DELETE'
            });
            
            if (response.success) {
                this.showSuccess(response.message);
                $(`#log-${log}`).fadeOut(() => {
                    $(this).remove();
                });
            } else {
                throw new Error(response.message || 'Log silinərkən xəta baş verdi');
            }
        } catch (error) {
            this.showError(error.message);
        }
    },

    async sendRequest(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                ...options.headers
            }
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Server xətası');
        }

        return response.json();
    },

    formatLogContent(content) {
        // Convert log levels to badges
        content = content.replace(/\b(ERROR|CRITICAL)\b/g, '<span class="badge bg-danger">$1</span>');
        content = content.replace(/\b(WARNING)\b/g, '<span class="badge bg-warning">$1</span>');
        content = content.replace(/\b(INFO)\b/g, '<span class="badge bg-info">$1</span>');
        content = content.replace(/\b(DEBUG)\b/g, '<span class="badge bg-secondary">$1</span>');
        
        // Convert URLs to links
        content = content.replace(
            /(https?:\/\/[^\s<]+)/g,
            '<a href="$1" target="_blank">$1</a>'
        );
        
        // Add line numbers
        const lines = content.split('\n');
        return lines.map((line, i) => 
            `<div class="log-line">
                <span class="line-number">${i + 1}</span>
                <span class="line-content">${line}</span>
            </div>`
        ).join('');
    },

    formatSize(bytes) {
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if (bytes === 0) return '0 Byte';
        const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
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
    LogManager.init();
});