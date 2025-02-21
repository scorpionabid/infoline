// Backup Management
const BackupManager = {
    config: {
        selectors: {
            createButton: '#createBackupBtn',
            backupList: '#backupList',
            downloadButton: '.download-backup',
            deleteButton: '.delete-backup'
        },
        endpoints: {
            create: '/settings/system/backups',
            download: '/settings/system/backups/',
            delete: '/settings/system/backups/'
        },
        templates: {
            backupItem: (backup) => `
                <tr id="backup-${backup.name}">
                    <td>${backup.name}</td>
                    <td>${this.formatSize(backup.size)}</td>
                    <td>${backup.created_at}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-primary download-backup" 
                                    data-backup="${backup.name}">
                                <i class="fas fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-backup" 
                                    data-backup="${backup.name}">
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
    },

    setupEventListeners() {
        const { selectors } = this.config;
        
        $(selectors.createButton).on('click', () => this.handleCreate());
        $(document).on('click', selectors.downloadButton, (e) => this.handleDownload(e));
        $(document).on('click', selectors.deleteButton, (e) => this.handleDelete(e));
    },

    async handleCreate() {
        const button = $(this.config.selectors.createButton);
        const originalText = button.html();
        
        try {
            button.prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin me-2"></i>Yaradılır...');
            
            const response = await this.sendRequest(this.config.endpoints.create, {
                method: 'POST'
            });
            
            if (response.success) {
                this.showSuccess(response.message);
                this.reloadBackupList();
            } else {
                throw new Error(response.message || 'Backup yaradılarkən xəta baş verdi');
            }
        } catch (error) {
            this.showError(error.message);
        } finally {
            button.prop('disabled', false).html(originalText);
        }
    },

    handleDownload(e) {
        const button = $(e.currentTarget);
        const backup = button.data('backup');
        const url = this.config.endpoints.download + backup;
        
        // Create temporary link and trigger download
        const link = document.createElement('a');
        link.href = url;
        link.download = backup;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    },

    async handleDelete(e) {
        const button = $(e.currentTarget);
        const backup = button.data('backup');
        
        const result = await Swal.fire({
            title: 'Əminsiniz?',
            text: 'Bu backup-ı silmək istədiyinizə əminsiniz?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Bəli, sil!',
            cancelButtonText: 'Xeyr',
            reverseButtons: true
        });
        
        if (!result.isConfirmed) return;
        
        try {
            const response = await this.sendRequest(this.config.endpoints.delete + backup, {
                method: 'DELETE'
            });
            
            if (response.success) {
                this.showSuccess(response.message);
                $(`#backup-${backup}`).fadeOut(() => {
                    $(this).remove();
                });
            } else {
                throw new Error(response.message || 'Backup silinərkən xəta baş verdi');
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

    async reloadBackupList() {
        try {
            const response = await this.sendRequest(this.config.endpoints.create);
            
            if (response.success && response.backups) {
                const backupList = $(this.config.selectors.backupList);
                backupList.empty();
                
                response.backups.forEach(backup => {
                    backupList.append(this.config.templates.backupItem(backup));
                });
            }
        } catch (error) {
            console.error('Backup siyahısı yenilənərkən xəta:', error);
        }
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
    BackupManager.init();
});