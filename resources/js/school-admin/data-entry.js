// resources/js/school-admin/data-entry.js

document.addEventListener("DOMContentLoaded", function () {
  // Edit düyməsinə click
  document.querySelectorAll(".edit-value").forEach((button) => {
    button.addEventListener("click", function () {
      const columnId = this.dataset.columnId;
      const row = document.querySelector(`tr[data-column-id="${columnId}"]`);
      const input = row.querySelector(
        ".column-input input, .column-input select"
      );

      // Input-u enable et
      input.disabled = false;
      input.focus();

      // Düymələri dəyiş
      this.classList.add("d-none");
      row.querySelector(".save-value").classList.remove("d-none");
    });
  });

  // Save düyməsinə click
  document.querySelectorAll(".save-value").forEach((button) => {
    button.addEventListener("click", function () {
      const columnId = this.dataset.columnId;
      const row = document.querySelector(`tr[data-column-id="${columnId}"]`);
      const input = row.querySelector(
        ".column-input input, .column-input select"
      );
      const originalValue = input.dataset.originalValue;
      const newValue = input.value;

      if (originalValue !== newValue) {
        saveColumnValue(columnId, newValue, row);
      }

      // Input-u disable et
      input.disabled = true;

      // Düymələri dəyiş
      this.classList.add("d-none");
      row.querySelector(".edit-value").classList.remove("d-none");
    });
  });

  // Filter funksionallığı
  document.querySelectorAll("[data-filter]").forEach((filter) => {
    filter.addEventListener("click", function (e) {
      e.preventDefault();
      const filterType = this.dataset.filter;
      filterRows(filterType);
    });
  });

  // Axtarış funksionallığı
  const searchInput = document.getElementById("searchInput");
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      filterRows("search", this.value);
    });
  }
});

// Məlumatı yadda saxlama
async function saveColumnValue(columnId, value, row) {
  try {
    const response = await fetch("/api/v1/data-values", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
          .content,
      },
      body: JSON.stringify({
        column_id: columnId,
        value: value,
      }),
    });

    if (!response.ok) throw new Error("Network response was not ok");

    const data = await response.json();

    // Status update
    row.querySelector(".badge").textContent = "Doldurulub";
    row.querySelector(".badge").classList.remove("bg-danger");
    row.querySelector(".badge").classList.add("bg-success");

    // Show success notification
    showNotification("Məlumat uğurla yadda saxlanıldı", "success");
  } catch (error) {
    console.error("Error:", error);
    showNotification("Xəta baş verdi", "error");
  }
}

// Filter funksiyası
function filterRows(filterType, searchValue = "") {
  const rows = document.querySelectorAll("tbody tr");

  rows.forEach((row) => {
    let show = true;

    switch (filterType) {
      case "empty":
        show = row.querySelector(".badge").textContent === "Boş";
        break;
      case "filled":
        show = row.querySelector(".badge").textContent === "Doldurulub";
        break;
      case "required":
        show = row.classList.contains("table-warning");
        break;
      case "search":
        const text = row.textContent.toLowerCase();
        show = text.includes(searchValue.toLowerCase());
        break;
      default:
        show = true;
    }

    row.style.display = show ? "" : "none";
  });
}

// Bildiriş göstərmə
function showNotification(message, type) {
  // Burada mövcud notification sisteminizi istifadə edə bilərsiniz
  // və ya yeni bir notification komponenti əlavə edə bilərsiniz
}

// resources/js/school-admin/data-entry.js

class DataEntryManager {
    constructor() {
        this.init();
        this.autoSaveTimeout = null;
    }

    init() {
        this.initEventListeners();
        this.initAutoSave();
        this.initValidation();
    }

    initEventListeners() {
        // Edit düymələri
        document.querySelectorAll('.edit-value').forEach(button => {
            button.addEventListener('click', (e) => this.handleEdit(e));
        });

        // Save düymələri
        document.querySelectorAll('.save-value').forEach(button => {
            button.addEventListener('click', (e) => this.handleSave(e));
        });

        // Input dəyişiklikləri
        document.querySelectorAll('.column-input input, .column-input select').forEach(input => {
            input.addEventListener('input', (e) => this.handleInputChange(e));
        });

        // Filterlər
        document.querySelectorAll('[data-filter]').forEach(filter => {
            filter.addEventListener('click', (e) => this.handleFilter(e));
        });

        // Axtarış
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.handleSearch(e));
        }
    }

    handleEdit(event) {
        const button = event.currentTarget;
        const columnId = button.dataset.columnId;
        const row = this.getRowByColumnId(columnId);
        const input = this.getInputElement(row);

        this.enableEditing(input, button, row);
    }

    async handleSave(event) {
        const button = event.currentTarget;
        const columnId = button.dataset.columnId;
        const row = this.getRowByColumnId(columnId);
        const input = this.getInputElement(row);

        if (await this.validateInput(input)) {
            await this.saveValue(columnId, input.value, row);
            this.disableEditing(input, button, row);
        }
    }

    handleInputChange(event) {
        const input = event.target;
        const row = input.closest('tr');
        const columnId = row.dataset.columnId;

        // Real-time validasiya
        this.validateInput(input);

        // Autosave
        this.scheduleAutoSave(columnId, input.value, row);
    }

    scheduleAutoSave(columnId, value, row) {
        if (this.autoSaveTimeout) {
            clearTimeout(this.autoSaveTimeout);
        }

        this.autoSaveTimeout = setTimeout(async () => {
            await this.saveValue(columnId, value, row);
        }, 2000); // 2 saniyə gözlə
    }

    async validateInput(input) {
        const row = input.closest('tr');
        const column = this.getColumnData(row);
        let isValid = true;
        let errorMessage = '';

        switch (column.dataType) {
            case 'number':
                isValid = !isNaN(input.value) && input.value !== '';
                errorMessage = 'Rəqəm daxil edin';
                break;

            case 'date':
                isValid = !isNaN(Date.parse(input.value));
                errorMessage = 'Düzgün tarix formatı daxil edin';
                break;

            case 'select':
                isValid = input.value !== '';
                errorMessage = 'Seçim edin';
                break;

            default:
                isValid = input.value.trim() !== '';
                errorMessage = 'Bu xana boş ola bilməz';
        }

        this.toggleValidationUI(input, isValid, errorMessage);
        return isValid;
    }

    toggleValidationUI(input, isValid, errorMessage) {
        const wrapper = input.closest('.column-input');
        
        // Validation classes
        input.classList.toggle('is-invalid', !isValid);
        input.classList.toggle('is-valid', isValid);

        // Error message
        let errorDiv = wrapper.querySelector('.invalid-feedback');
        if (!errorDiv && !isValid) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            wrapper.appendChild(errorDiv);
        }
        if (errorDiv) {
            errorDiv.textContent = errorMessage;
        }
    }

    async saveValue(columnId, value, row) {
        try {
            const response = await fetch('/api/v1/data-values', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    column_id: columnId,
                    value: value
                })
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            this.updateRowStatus(row, true);
            this.showNotification('Məlumat yadda saxlanıldı', 'success');

        } catch (error) {
            console.error('Error:', error);
            this.showNotification('Xəta baş verdi', 'error');
        }
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification`;
        notification.textContent = message;

        document.body.appendChild(notification);

        // 3 saniyədən sonra notification-ı sil
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    handleFilter(event) {
        event.preventDefault();
        const filterType = event.currentTarget.dataset.filter;
        this.filterRows(filterType);
    }

    handleSearch(event) {
        const searchText = event.target.value.toLowerCase();
        this.filterRows('search', searchText);
    }

    filterRows(filterType, searchText = '') {
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const shouldShow = this.shouldShowRow(row, filterType, searchText);
            row.style.display = shouldShow ? '' : 'none';
        });
    }

    shouldShowRow(row, filterType, searchText) {
        switch (filterType) {
            case 'empty':
                return row.querySelector('.badge').textContent === 'Boş';
            
            case 'filled':
                return row.querySelector('.badge').textContent === 'Doldurulub';
            
            case 'required':
                return row.classList.contains('table-warning');
            
            case 'search':
                return row.textContent.toLowerCase().includes(searchText);
            
            default:
                return true;
        }
    }

    // Helper methods
    getRowByColumnId(columnId) {
        return document.querySelector(`tr[data-column-id="${columnId}"]`);
    }

    getInputElement(row) {
        return row.querySelector('.column-input input, .column-input select');
    }

    getColumnData(row) {
        return {
            id: row.dataset.columnId,
            dataType: row.querySelector('td:nth-child(2)').textContent.trim()
        };
    }

    enableEditing(input, editButton, row) {
        input.disabled = false;
        input.focus();
        editButton.classList.add('d-none');
        row.querySelector('.save-value').classList.remove('d-none');
    }

    disableEditing(input, saveButton, row) {
        input.disabled = true;
        saveButton.classList.add('d-none');
        row.querySelector('.edit-value').classList.remove('d-none');
    }

    updateRowStatus(row, isSaved) {
        const badge = row.querySelector('.badge');
        badge.textContent = isSaved ? 'Doldurulub' : 'Boş';
        badge.className = `badge ${isSaved ? 'bg-success' : 'bg-danger'}`;
    }
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', () => {
    new DataEntryManager();
});