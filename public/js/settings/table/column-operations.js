/**
 * Sütun əməliyyatları üçün JavaScript funksiyaları
 * Bu fayl sütunların yaradılması, redaktəsi və silinməsi üçün lazım olan bütün funksiyaları təmin edir
 */

const ColumnOperations = {
  /**
   * Hadisə dinləyicilərini qeydə alır
   */
  initEventListeners: function() {
    console.log("Sütun əməliyyatları üçün hadisə dinləyiciləri qeydə alınır...");
    
    // Sütun formu təqdim ediləndə
    const columnForm = document.getElementById('columnForm');
    if (columnForm) {
      columnForm.addEventListener('submit', this.handleFormSubmit.bind(this));
    }
    
    // Sütun tipi dəyişəndə
    const columnType = document.getElementById('columnDataType');
    if (columnType) {
      columnType.addEventListener('change', function() {
        ColumnOperations.toggleTypeFields(this.value);
      });
    }
    
    // Seçim əlavə et düyməsi
    const addOptionButton = document.getElementById('addOptionButton');
    if (addOptionButton) {
      addOptionButton.addEventListener('click', function(e) {
        e.preventDefault();
        ColumnOperations.addOption();
      });
    }
    
    console.log("Sütun əməliyyatları üçün hadisə dinləyiciləri uğurla qeydə alındı");
  },
  
  /**
   * Yeni sütun yaratmaq üçün modal pəncərəni açır
   * @param {number} categoryId - Kateqoriya ID
   */
  openCreateModal: function(categoryId) {
    console.log(`Yeni sütun modalı açılır: Kateqoriya ID=${categoryId}`);
    
    if (!categoryId) {
      console.error('Kateqoriya ID təyin edilməyib');
      TableUtils.showErrorMessage('Kateqoriya ID təyin edilməyib');
      return;
    }
    
    // Formu sıfırla
    TableUtils.resetForm('columnForm');
    
    // Modal başlığını yenilə
    document.getElementById('columnModalTitle').textContent = 'Yeni Sütun';
    
    // Metodu POST olaraq təyin et
    document.getElementById('columnMethod').value = 'POST';
    
    // Kateqoriya ID-ni təyin et
    document.getElementById('columnCategoryId').value = categoryId;
    
    // Formu yönləndir
    document.getElementById('columnForm').action = TableUtils.routes.columns.store;
    
    // Sütun növünü sıfırla və müvafiq sahələri göstər/gizlət
    const columnTypeSelect = document.getElementById('columnDataType');
    if (columnTypeSelect) {
      columnTypeSelect.value = 'text';
      this.toggleTypeFields('text');
    }
    
    console.log('Yeni sütun modalı hazırlandı');
  },
  
  /**
   * Mövcud sütunu redaktə etmək üçün modal pəncərəni açır
   * @param {number} columnId - Sütun ID
   */
  openEditModal: function(columnId) {
    console.log(`Sütun redaktə modalı açılır: ID=${columnId}`);
    
    if (!columnId) {
      console.error('Sütun ID təyin edilməyib');
      TableUtils.showErrorMessage('Sütun ID təyin edilməyib');
      return;
    }
    
    // Yükləmə göstəricisini göstər
    TableUtils.showLoadingOverlay('Sütun məlumatları yüklənir...');
    
    // Sütun məlumatlarını əldə et
    axios.get(TableUtils.routes.columns.show(columnId))
      .then(response => {
        // Yükləmə göstəricisini gizlət
        TableUtils.hideLoadingOverlay();
        
        if (!response.data.column) {
          TableUtils.showErrorMessage('Sütun tapılmadı');
          return;
        }
        
        const column = response.data.column;
        
        // Formu sıfırla
        TableUtils.resetForm('columnForm');
        
        // Modal başlığını yenilə
        document.getElementById('columnModalTitle').textContent = 'Sütunu Redaktə Et';
        
        // Metodu PUT olaraq təyin et
        document.getElementById('columnMethod').value = 'PUT';
        
        // Sütun ID-ni və kateqoriya ID-ni təyin et
        document.getElementById('columnId').value = column.id;
        document.getElementById('columnCategoryId').value = column.category_id;
        
        // Formu yönləndir
        document.getElementById('columnForm').action = TableUtils.routes.columns.update(column.id);
        
        // Formu doldur
        document.getElementById('columnName').value = column.name;
        document.getElementById('columnDescription').value = column.description || '';
        document.getElementById('columnDataType').value = column.data_type || 'text';
        document.getElementById('columnIsRequired').checked = column.is_required || false;
        
        // Sütun növünə görə müvafiq sahələri göstər/gizlət
        this.toggleTypeFields(column.data_type || 'text');
        
        // Növə görə əlavə sahələri doldur
        if (column.data_type === 'date' && column.date_format) {
          document.getElementById('columnDateFormat').value = column.date_format;
        } else if (column.data_type === 'file' && column.file_types) {
          const fileTypesSelect = document.getElementById('columnFileTypes');
          if (fileTypesSelect) {
            const fileTypes = column.file_types.split(',');
            for (let i = 0; i < fileTypesSelect.options.length; i++) {
              fileTypesSelect.options[i].selected = fileTypes.includes(fileTypesSelect.options[i].value);
            }
          }
        }
        
        // Limit dəyərini təyin et
        if (column.input_limit) {
          document.getElementById('columnInputLimit').value = column.input_limit;
        }
        
        console.log(`Sütun redaktə modalı hazırlandı: ID=${columnId}`);
      })
      .catch(error => {
        TableUtils.handleError(error, 'Sütun məlumatları yüklənərkən xəta baş verdi');
      });
  },
  
  /**
   * Sütun formu təqdim ediləndə işləyir
   * @param {Event} event - Form təqdim hadisəsi
   */
  handleFormSubmit: function(event) {
    event.preventDefault();
    console.log("Sütun formu təqdim edilir...");
    
    const form = event.target;
    const formData = new FormData(form);
    const method = formData.get('_method') || 'POST';
    const url = form.action;
    
    // Clear previous errors
    TableUtils.clearFormErrors();
    
    // Disable submit button
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
      submitButton.disabled = true;
      submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gözləyin...';
    }
    
    // Handle options for select type
    if (formData.get('type') === 'select') {
      const options = [];
      const optionRows = document.querySelectorAll('.option-row');
      
      optionRows.forEach((row, index) => {
        const valueInput = row.querySelector('.option-value');
        const labelInput = row.querySelector('.option-label');
        const defaultCheck = row.querySelector('.option-default');
        
        if (valueInput && labelInput) {
          const value = valueInput.value.trim();
          const label = labelInput.value.trim();
          
          if (value && label) {
            options.push({
              value: value,
              label: label,
              is_default: defaultCheck ? defaultCheck.checked : false,
              order: index
            });
          }
        }
      });
      
      // Add options as JSON string
      formData.set('options', JSON.stringify(options));
    }
    
    // Send request
    axios({
      method: method === 'PUT' ? 'post' : method.toLowerCase(),
      url: url,
      data: formData
    })
    .then(response => {
      if (response.data.success) {
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('columnModal'));
        if (modal) {
          modal.hide();
        }
        
        // Show success message
        TableUtils.showSuccessMessage(response.data.message || 'Sütun uğurla yadda saxlanıldı');
        
        // Reload page after a short delay
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      }
    })
    .catch(error => {
      TableUtils.handleError(error, 'Sütun yadda saxlanılarkən xəta baş verdi');
    })
    .finally(() => {
      // Re-enable submit button
      if (submitButton) {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Yadda saxla';
      }
    });
  },
  
  /**
   * Sütun tipinə görə sahələri göstərir/gizlədir
   * @param {string} type - Sütun tipi
   */
  toggleTypeFields: function(type) {
    console.log(`Sütun tipi dəyişdirildi: ${type}`);
    
    // Bütün növ-spesifik bölmələri gizlət
    document.querySelectorAll('.type-specific-section').forEach(section => {
      section.style.display = 'none';
    });
    
    // Seçilmiş növə görə müvafiq bölməni göstər
    const typeSection = document.getElementById(`${type}Section`);
    if (typeSection) {
      typeSection.style.display = 'block';
    }
    
    // Xüsusi hallar üçün əlavə tənzimləmələr
    if (type === 'select') {
      document.getElementById('optionsSection').style.display = 'block';
    } else if (type === 'date') {
      document.getElementById('dateFormatSection').style.display = 'block';
    } else if (type === 'file') {
      document.getElementById('fileTypesSection').style.display = 'block';
    }
  },
  
  /**
   * Sütun formunu təqdim edir
   * @param {HTMLFormElement} form - Form elementi
   */
  submitForm: function(form) {
    console.log('Sütun formu təqdim edilir');
    
    // Yükləmə göstəricisini göstər
    TableUtils.showLoadingOverlay('Sütun yadda saxlanılır...');
    
    // Form məlumatlarını topla
    const formData = new FormData(form);
    
    // Sorğu metodunu təyin et
    const method = formData.get('_method') || 'POST';
    
    // Sorğunu göndər
    axios({
      method: method === 'PUT' ? 'post' : method.toLowerCase(),
      url: form.action,
      data: formData,
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    .then(response => {
      // Yükləmə göstəricisini gizlət
      TableUtils.hideLoadingOverlay();
      
      if (response.data.success) {
        TableUtils.showSuccessMessage(response.data.message || 'Sütun uğurla yadda saxlanıldı');
        
        // Modalı bağla
        const columnModal = bootstrap.Modal.getInstance(document.getElementById('columnModal'));
        if (columnModal) {
          columnModal.hide();
        }
        
        // Səhifəni yenilə
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        TableUtils.showErrorMessage(response.data.message || 'Sütun yadda saxlanılarkən xəta baş verdi');
      }
    })
    .catch(error => {
      TableUtils.handleError(error, 'Sütun yadda saxlanılarkən xəta baş verdi');
    });
  },
  
  /**
   * Seçim siyahısına yeni seçim əlavə edir
   * @param {string} value - Seçim dəyəri
   * @param {string} label - Seçim etiketi
   * @param {boolean} isDefault - Varsayılan seçimdir?
   */
  addOption: function(value = '', label = '', isDefault = false) {
    console.log('Yeni seçim əlavə edilir');
    
    const optionsList = document.getElementById('optionsList');
    if (!optionsList) return;
    
    const optionId = Date.now(); // Unikal ID
    
    const optionHtml = `
      <div class="row mb-2 option-item" data-option-id="${optionId}">
        <div class="col-5">
          <input type="text" class="form-control" name="option_values[]" placeholder="Dəyər" value="${value}" required>
        </div>
        <div class="col-5">
          <input type="text" class="form-control" name="option_labels[]" placeholder="Etiket" value="${label}" required>
        </div>
        <div class="col-1">
          <div class="form-check mt-2">
            <input class="form-check-input" type="radio" name="default_option" value="${optionId}" ${isDefault ? 'checked' : ''}>
          </div>
        </div>
        <div class="col-1">
          <button type="button" class="btn btn-sm btn-outline-danger" onclick="ColumnOperations.removeOption(${optionId})">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
    `;
    
    optionsList.insertAdjacentHTML('beforeend', optionHtml);
    
    console.log(`Yeni seçim əlavə edildi: ID=${optionId}`);
  },
  
  /**
   * Seçim siyahısından seçimi silir
   * @param {number} optionId - Seçim ID
   */
  removeOption: function(optionId) {
    console.log(`Seçim silinir: ID=${optionId}`);
    
    const optionItem = document.querySelector(`.option-item[data-option-id="${optionId}"]`);
    if (optionItem) {
      optionItem.remove();
      console.log(`Seçim silindi: ID=${optionId}`);
    }
  },
  
  /**
   * Sütun silmə əməliyyatını təsdiqləyir
   * @param {number} columnId - Sütun ID
   */
  confirmDelete: function(columnId) {
    console.log(`Sütun silmə təsdiqi: ID=${columnId}`);
    
    if (!columnId) {
      console.error('Sütun ID təyin edilməyib');
      TableUtils.showErrorMessage('Sütun ID təyin edilməyib');
      return;
    }
    
    // Sütun adını əldə et
    const columnName = document.querySelector(`.column-item[data-column-id="${columnId}"] .column-name`);
    const name = columnName ? columnName.textContent.trim() : 'Bu sütun';
    
    // Təsdiq dialoqu göstər
    if (confirm(`${name} sütununu silmək istədiyinizə əminsiniz? Bu əməliyyat geri qaytarıla bilməz.`)) {
      this.deleteColumn(columnId);
    }
  },
  
  /**
   * Sütun silir
   * @param {number} columnId - Sütun ID
   */
  deleteColumn: function(columnId) {
    console.log(`Sütun silinir: ID=${columnId}`);
    
    if (!columnId) {
      console.error('Sütun ID təyin edilməyib');
      TableUtils.showErrorMessage('Sütun ID təyin edilməyib');
      return;
    }
    
    // Yükləmə göstəricisini göstər
    TableUtils.showLoadingOverlay('Sütun silinir...');
    
    // Silmə sorğusu göndər
    axios({
      method: 'DELETE',
      url: TableUtils.routes.columns.destroy(columnId)
    })
      .then(response => {
        // Yükləmə göstəricisini gizlət
        TableUtils.hideLoadingOverlay();
        
        if (response.data.success) {
          TableUtils.showSuccessMessage(response.data.message || 'Sütun uğurla silindi');
          
          // Sütun elementini DOM-dan sil
          const columnItem = document.querySelector(`.column-item[data-column-id="${columnId}"]`);
          if (columnItem) {
            columnItem.remove();
          }
          
          // Əgər səhifədə sütun qalmayıbsa, səhifəni yenilə
          const remainingColumns = document.querySelectorAll('.column-item');
          if (remainingColumns.length === 0) {
            setTimeout(() => {
              window.location.reload();
            }, 1000);
          }
        } else {
          TableUtils.showErrorMessage(response.data.message || 'Sütun silinərkən xəta baş verdi');
        }
      })
      .catch(error => {
        TableUtils.handleError(error, 'Sütun silinərkən xəta baş verdi');
      });
  },
  
  /**
   * Sütun statusunu dəyişdirir
   * @param {number} columnId - Sütun ID-si
   * @param {boolean} isActive - Yeni status
   */
  toggleStatus: function(columnId, isActive) {
    console.log(`Sütun statusu dəyişdirilir: ID=${columnId}, Status=${isActive}`);
    
    if (!columnId) {
      console.error('Sütun ID təyin edilməyib');
      TableUtils.showErrorMessage('Sütun ID təyin edilməyib');
      return;
    }
    
    // Status dəyişdirmə sorğusu göndər
    axios({
      method: 'PATCH',
      url: TableUtils.routes.columns.status(columnId),
      data: { status: isActive ? 1 : 0 }
    })
      .then(response => {
        if (response.data.success) {
          TableUtils.showSuccessMessage(response.data.message || 'Sütun statusu uğurla dəyişdirildi');
        } else {
          TableUtils.showErrorMessage(response.data.message || 'Sütun statusu dəyişdirilirkən xəta baş verdi');
          
          // Statusu əvvəlki vəziyyətinə qaytar
          const checkbox = document.querySelector(`.column-status[data-column-id="${columnId}"]`);
          if (checkbox) {
            checkbox.checked = !isActive;
          }
        }
      })
      .catch(error => {
        TableUtils.handleError(error, 'Sütun statusu dəyişdirilirkən xəta baş verdi');
        
        // Statusu əvvəlki vəziyyətinə qaytar
        const checkbox = document.querySelector(`.column-status[data-column-id="${columnId}"]`);
        if (checkbox) {
          checkbox.checked = !isActive;
        }
      });
  },
  
  /**
   * Son tarix modalını açır
   * @param {number} columnId - Sütun ID
   */
  openDeadlineModal: function(columnId) {
    console.log(`Son tarix modalı açılır: ID=${columnId}`);
    
    if (!columnId) {
      console.error('Sütun ID təyin edilməyib');
      TableUtils.showErrorMessage('Sütun ID təyin edilməyib');
      return;
    }
    
    // Sütun ID-ni təyin et
    document.getElementById('deadlineColumnId').value = columnId;
    
    // Formu yönləndir
    document.getElementById('deadlineForm').action = TableUtils.routes.columns.deadline(columnId);
    
    // Modalı göstər
    const deadlineModal = new bootstrap.Modal(document.getElementById('deadlineModal'));
    deadlineModal.show();
    
    console.log(`Son tarix modalı hazırlandı: ID=${columnId}`);
  },
  
  /**
   * Son tarix formunu təqdim edir
   * @param {HTMLFormElement} form - Form elementi
   */
  submitDeadlineForm: function(form) {
    console.log('Son tarix formu təqdim edilir');
    
    // Yükləmə göstəricisini göstər
    TableUtils.showLoadingOverlay('Son tarix yenilənir...');
    
    // Form məlumatlarını topla
    const formData = new FormData(form);
    
    // Sorğunu göndər
    axios({
      method: 'PATCH',
      url: form.action,
      data: formData
    })
      .then(response => {
        // Yükləmə göstəricisini gizlət
        TableUtils.hideLoadingOverlay();
        
        if (response.data.success) {
          TableUtils.showSuccessMessage(response.data.message || 'Son tarix uğurla yeniləndi');
          
          // Modalı bağla
          const deadlineModal = bootstrap.Modal.getInstance(document.getElementById('deadlineModal'));
          if (deadlineModal) {
            deadlineModal.hide();
          }
          
          // Səhifəni yenilə
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          TableUtils.showErrorMessage(response.data.message || 'Son tarix yenilənərkən xəta baş verdi');
        }
      })
      .catch(error => {
        TableUtils.handleError(error, 'Son tarix yenilənərkən xəta baş verdi');
      });
  }
};

// Initialize event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  ColumnOperations.initEventListeners();
});

// Export globally
window.ColumnOperations = ColumnOperations;