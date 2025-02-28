/**
 * Son tarix əməliyyatları üçün JavaScript funksiyaları
 */

const DeadlineOperations = {
    /**
     * Son tarix modalını açır
     * @param {number} columnId - Sütun ID-si
     */
    openModal: function(columnId) {
        // Modal elementlərini əldə edirik
        const modal = document.getElementById('deadlineModal');
        const columnIdInput = document.getElementById('deadlineColumnId');
        const dateInput = document.getElementById('deadlineDate');
        
        // Sütun ID-sini təyin edirik
        columnIdInput.value = columnId;
        
        // Mövcud son tarixi əldə etmək üçün sütun elementini tapırıq
        const columnElement = document.querySelector(`.column-item[data-column-id="${columnId}"]`);
        if (columnElement) {
            const deadlineElement = columnElement.querySelector('.column-deadline');
            if (deadlineElement && deadlineElement.dataset.date) {
                // Əgər tarix varsa, input-a təyin edirik
                dateInput.value = deadlineElement.dataset.date;
            } else {
                // Əgər tarix yoxdursa, bugünkü tarixi təyin edirik
                const today = new Date();
                const formattedDate = today.toISOString().split('T')[0];
                dateInput.value = formattedDate;
            }
        }
    },
    
    /**
     * Son tarix formu göndərilir
     * @param {Event} event - Form göndərilmə hadisəsi
     */
    submitForm: function(event) {
        event.preventDefault();
        
        // Form elementlərini əldə edirik
        const form = event.target;
        const columnId = document.getElementById('deadlineColumnId').value;
        const date = document.getElementById('deadlineDate').value;
        const formErrors = form.querySelector('.form-errors');
        
        // Yüklənmə göstəricisini aktivləşdiririk
        TableUtils.showLoadingOverlay();
        
        // API sorğusu göndəririk
        axios.patch(`/settings/table/columns/${columnId}/deadline`, {
            end_date: date
        })
        .then(response => {
            if (response.data.success) {
                // Uğurlu olduqda modalı bağlayırıq və səhifəni yeniləyirik
                const modal = bootstrap.Modal.getInstance(document.getElementById('deadlineModal'));
                modal.hide();
                
                // Səhifəni yeniləyirik
                window.location.reload();
            } else {
                // Xəta baş verdikdə göstəririk
                formErrors.textContent = response.data.message || 'Xəta baş verdi';
                formErrors.style.display = 'block';
            }
        })
        .catch(error => {
            // Xəta baş verdikdə göstəririk
            let errorMessage = 'Son tarix yenilənərkən xəta baş verdi';
            
            if (error.response && error.response.data) {
                if (error.response.data.errors && error.response.data.errors.end_date) {
                    errorMessage = error.response.data.errors.end_date[0];
                } else if (error.response.data.message) {
                    errorMessage = error.response.data.message;
                }
            }
            
            formErrors.textContent = errorMessage;
            formErrors.style.display = 'block';
        })
        .finally(() => {
            // Yüklənmə göstəricisini deaktivləşdiririk
            TableUtils.hideLoadingOverlay();
        });
    }
};

// Global obyektə əlavə edirik
window.DeadlineOperations = DeadlineOperations;
