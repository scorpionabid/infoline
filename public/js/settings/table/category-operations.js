/**
 * Kateqoriya əməliyyatları üçün JavaScript funksiyaları
 * Bu fayl kateqoriyaların yaradılması, redaktəsi və silinməsi üçün lazım olan bütün funksiyaları təmin edir
 */

const CategoryOperations = {
  /**
   * Hadisə dinləyicilərini qeydə alır
   */
  initEventListeners: function () {
    console.log("Kateqoriya əməliyyatları üçün hadisə dinləyiciləri qeydə alınır...");
    
    // Kateqoriya formu təqdim ediləndə
    const categoryForm = document.getElementById("categoryForm");
    if (categoryForm) {
      categoryForm.addEventListener("submit", this.handleFormSubmit.bind(this));
    }
    
    // Kateqoriya tipini dəyişəndə
    const categoryType = document.getElementById("categoryType");
    if (categoryType) {
      categoryType.addEventListener("change", this.handleTypeChange.bind(this));
    }
    
    console.log("Kateqoriya əməliyyatları üçün hadisə dinləyiciləri uğurla qeydə alındı");
  },

  /**
   * Yeni kateqoriya yaratmaq üçün modal pəncərəni açır
   */
  openCreateModal: function() {
    console.log('Yeni kateqoriya modalı açılır');
    
    // Formu sıfırla
    TableUtils.resetForm('categoryForm');
    
    // Modal başlığını yenilə
    document.getElementById('categoryModalTitle').textContent = 'Yeni Kateqoriya';
    
    // Metodu POST olaraq təyin et
    document.getElementById('categoryMethod').value = 'POST';
    
    // Formu yönləndir
    document.getElementById('categoryForm').action = TableUtils.routes.categories.store;
    
    // Təyinat tipini sıfırla
    document.getElementById('assignmentAll').checked = true;
    this.handleAssignmentTypeChange('all');
    
    console.log('Yeni kateqoriya modalı hazırlandı');
  },
  
  /**
   * Mövcud kateqoriyanı redaktə etmək üçün modal pəncərəni açır
   * @param {number} categoryId - Kateqoriya ID
   */
  openEditModal: function(categoryId) {
    console.log(`Kateqoriya redaktə modalı açılır: ID=${categoryId}`);
    
    if (!categoryId) {
      console.error('Kateqoriya ID təyin edilməyib');
      TableUtils.showErrorMessage('Kateqoriya ID təyin edilməyib');
      return;
    }
    
    // Yükləmə göstəricisini göstər
    TableUtils.showLoadingOverlay('Kateqoriya məlumatları yüklənir...');
    
    // Kateqoriya məlumatlarını əldə et
    axios.get(TableUtils.routes.categories.show(categoryId))
      .then(response => {
        // Yükləmə göstəricisini gizlət
        TableUtils.hideLoadingOverlay();
        
        if (!response.data.category) {
          TableUtils.showErrorMessage('Kateqoriya tapılmadı');
          return;
        }
        
        const category = response.data.category;
        
        // Formu sıfırla
        TableUtils.resetForm('categoryForm');
        
        // Modal başlığını yenilə
        document.getElementById('categoryModalTitle').textContent = 'Kateqoriyanı Redaktə Et';
        
        // Metodu PUT olaraq təyin et
        document.getElementById('categoryMethod').value = 'PUT';
        
        // Kateqoriya ID-ni təyin et
        document.getElementById('categoryId').value = category.id;
        
        // Formu yönləndir
        document.getElementById('categoryForm').action = TableUtils.routes.categories.update(category.id);
        
        // Formu doldur
        document.getElementById('categoryName').value = category.name;
        document.getElementById('categoryDescription').value = category.description || '';
        
        // Təyinat tipini təyin et
        const assignmentType = category.assigned_type || 'all';
        document.querySelector(`input[name="assigned_type"][value="${assignmentType}"]`).checked = true;
        
        // Təyinat seçimlərini göstər/gizlət
        this.handleAssignmentTypeChange(assignmentType);
        
        // Təyin edilmiş sektorları və məktəbləri seç
        if (assignmentType === 'sector' && category.assigned_sectors) {
          const sectorSelect = document.getElementById('assignedSectors');
          if (sectorSelect && $.fn.select2) {
            $(sectorSelect).val(category.assigned_sectors).trigger('change');
          }
        } else if (assignmentType === 'school' && category.assigned_schools) {
          const schoolSelect = document.getElementById('assignedSchools');
          if (schoolSelect && $.fn.select2) {
            $(schoolSelect).val(category.assigned_schools).trigger('change');
          }
        }
        
        console.log(`Kateqoriya redaktə modalı hazırlandı: ID=${categoryId}`);
      })
      .catch(error => {
        TableUtils.handleError(error, 'Kateqoriya məlumatları yüklənərkən xəta baş verdi');
      });
  },

  /**
   * Kateqoriya formu təqdim ediləndə işləyir
   * @param {Event} event - Form təqdim hadisəsi
   */
  handleFormSubmit: function (event) {
    event.preventDefault();
    console.log("Kateqoriya formu təqdim edilir...");

    const form = event.target;
    const formData = new FormData(form);
    const method = formData.get("_method") || "POST";
    const url = form.action;

    // Clear previous errors
    TableUtils.clearFormErrors();

    // Disable submit button
    const submitButton = form.querySelector('button[type="submit"]');
    if (submitButton) {
      submitButton.disabled = true;
      submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gözləyin...';
    }

    // Send request
    axios({
      method: method === "PUT" ? "post" : method.toLowerCase(),
      url: url,
      data: formData,
    })
      .then((response) => {
        if (response.data.success) {
          // Close modal
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("categoryModal")
          );
          if (modal) {
            modal.hide();
          }

          // Show success message
          TableUtils.showSuccessMessage(
            response.data.message || "Kateqoriya uğurla yadda saxlanıldı"
          );

          // Reload page after a short delay
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        }
      })
      .catch((error) => {
        TableUtils.handleError(
          error,
          "Kateqoriya yadda saxlanılarkən xəta baş verdi"
        );
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
   * Kateqoriya tipini dəyişəndə işləyir
   * @param {Event} event - Tip dəyişmə hadisəsi
   */
  handleTypeChange: function (event) {
    const type = event.target.value;
    console.log("Kateqoriya tipi dəyişdirildi:", type);
    
    // Show/hide fields based on type
    const assignmentsSection = document.getElementById("assignmentsSection");
    if (assignmentsSection) {
      assignmentsSection.style.display = type === "custom" ? "none" : "block";
    }
  },

  /**
   * Təyinat növü dəyişdirildikdə müvafiq sahələri göstərir/gizlədir
   * @param {string} type - Təyinat növü
   */
  handleAssignmentTypeChange: function(type) {
    console.log(`Təyinat növü dəyişdirilir: ${type}`);
    
    // Bütün seçim bölmələrini gizlət
    document.getElementById('sectorSelection').style.display = 'none';
    document.getElementById('schoolSelection').style.display = 'none';
    
    // Seçilmiş növə görə müvafiq bölməni göstər
    if (type === 'sector') {
      document.getElementById('sectorSelection').style.display = 'block';
    } else if (type === 'school') {
      document.getElementById('schoolSelection').style.display = 'block';
    }
  },

  /**
   * Kateqoriya təyinatlarını sıfırlayır
   */
  resetAssignments: function () {
    const assignmentsList = document.getElementById("assignmentsList");
    if (assignmentsList) {
      assignmentsList.innerHTML = "";
    }
  },

  /**
   * Kateqoriya təyinatlarını gətirir
   * @param {number} categoryId - Kateqoriya ID-si
   */
  fetchCategoryAssignments: function (categoryId) {
    if (!categoryId) return;
    
    console.log("Kateqoriya təyinatları gətirilir, ID:", categoryId);

    axios
      .get(TableUtils.routes.categories.assignments(categoryId))
      .then((response) => {
        if (response.data.success) {
          this.renderAssignments(response.data.assignments || []);
        }
      })
      .catch((error) => {
        console.error("Təyinatlar alınarkən xəta baş verdi:", error);
      });
  },

  /**
   * Kateqoriya təyinatlarını render edir
   * @param {Array} assignments - Təyinatlar siyahısı
   */
  renderAssignments: function (assignments) {
    const assignmentsList = document.getElementById("assignmentsList");
    if (!assignmentsList) return;

    assignmentsList.innerHTML = "";

    if (assignments.length === 0) {
      assignmentsList.innerHTML =
        '<div class="alert alert-info">Bu kateqoriya üçün heç bir təyinat yoxdur.</div>';
      return;
    }

    assignments.forEach((assignment) => {
      const item = document.createElement("div");
      item.className = "form-check mb-2";
      item.innerHTML = `
        <input class="form-check-input" type="checkbox" name="assignments[]" value="${assignment.id}" id="assignment${assignment.id}" ${assignment.assigned ? "checked" : ""}>
        <label class="form-check-label" for="assignment${assignment.id}">
          ${assignment.name}
        </label>
      `;
      assignmentsList.appendChild(item);
    });
  },

  /**
   * Kateqoriya silmə əməliyyatını təsdiqləyir
   * @param {number} categoryId - Kateqoriya ID
   */
  confirmDelete: function(categoryId) {
    console.log(`Kateqoriya silmə təsdiqi: ID=${categoryId}`);
    
    if (!categoryId) {
      console.error('Kateqoriya ID təyin edilməyib');
      TableUtils.showErrorMessage('Kateqoriya ID təyin edilməyib');
      return;
    }
    
    // Kateqoriya adını əldə et
    const categoryName = document.querySelector(`.category-item[data-category-id="${categoryId}"] .category-name`);
    const name = categoryName ? categoryName.textContent.trim() : 'Bu kateqoriya';
    
    // Təsdiq dialoqu göstər
    if (confirm(`${name} kateqoriyasını silmək istədiyinizə əminsiniz? Bu əməliyyat geri qaytarıla bilməz.`)) {
      this.deleteCategory(categoryId);
    }
  },
  
  /**
   * Kateqoriya silir
   * @param {number} categoryId - Kateqoriya ID
   */
  deleteCategory: function(categoryId) {
    console.log(`Kateqoriya silinir: ID=${categoryId}`);
    
    if (!categoryId) {
      console.error('Kateqoriya ID təyin edilməyib');
      TableUtils.showErrorMessage('Kateqoriya ID təyin edilməyib');
      return;
    }
    
    // Yükləmə göstəricisini göstər
    TableUtils.showLoadingOverlay('Kateqoriya silinir...');
    
    // Silmə sorğusu göndər
    axios({
      method: 'DELETE',
      url: TableUtils.routes.categories.destroy(categoryId)
    })
      .then(response => {
        // Yükləmə göstəricisini gizlət
        TableUtils.hideLoadingOverlay();
        
        if (response.data.success) {
          TableUtils.showSuccessMessage(response.data.message || 'Kateqoriya uğurla silindi');
          
          // Kateqoriya elementini DOM-dan sil
          const categoryItem = document.querySelector(`.category-item[data-category-id="${categoryId}"]`);
          if (categoryItem) {
            categoryItem.remove();
          }
          
          // Əgər səhifədə kateqoriya qalmayıbsa, səhifəni yenilə
          const remainingCategories = document.querySelectorAll('.category-item');
          if (remainingCategories.length === 0) {
            setTimeout(() => {
              window.location.reload();
            }, 1000);
          }
        } else {
          TableUtils.showErrorMessage(response.data.message || 'Kateqoriya silinərkən xəta baş verdi');
        }
      })
      .catch(error => {
        TableUtils.handleError(error, 'Kateqoriya silinərkən xəta baş verdi');
      });
  },
  
  /**
   * Kateqoriya statusunu dəyişdirir
   * @param {number} categoryId - Kateqoriya ID
   * @param {boolean} isActive - Yeni status
   */
  toggleStatus: function(categoryId, isActive) {
    console.log(`Kateqoriya statusu dəyişdirilir: ID=${categoryId}, Status=${isActive}`);
    
    if (!categoryId) {
      console.error('Kateqoriya ID təyin edilməyib');
      TableUtils.showErrorMessage('Kateqoriya ID təyin edilməyib');
      return;
    }
    
    // Status dəyişdirmə sorğusu göndər
    axios({
      method: 'PATCH',
      url: TableUtils.routes.categories.status(categoryId),
      data: { status: isActive ? 1 : 0 }
    })
      .then(response => {
        if (response.data.success) {
          TableUtils.showSuccessMessage(response.data.message || 'Kateqoriya statusu uğurla dəyişdirildi');
        } else {
          TableUtils.showErrorMessage(response.data.message || 'Kateqoriya statusu dəyişdirilirkən xəta baş verdi');
          
          // Statusu əvvəlki vəziyyətinə qaytar
          const checkbox = document.querySelector(`.category-status[data-category-id="${categoryId}"]`);
          if (checkbox) {
            checkbox.checked = !isActive;
          }
        }
      })
      .catch(error => {
        TableUtils.handleError(error, 'Kateqoriya statusu dəyişdirilirkən xəta baş verdi');
        
        // Statusu əvvəlki vəziyyətinə qaytar
        const checkbox = document.querySelector(`.category-status[data-category-id="${categoryId}"]`);
        if (checkbox) {
          checkbox.checked = !isActive;
        }
      });
  },
  
  /**
   * Kateqoriya formunu təqdim edir
   * @param {HTMLFormElement} form - Form elementi
   */
  submitForm: function(form) {
    console.log('Kateqoriya formu təqdim edilir');
    
    // Yükləmə göstəricisini göstər
    TableUtils.showLoadingOverlay('Kateqoriya yadda saxlanılır...');
    
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
          TableUtils.showSuccessMessage(response.data.message || 'Kateqoriya uğurla yadda saxlanıldı');
          
          // Modalı bağla
          const categoryModal = bootstrap.Modal.getInstance(document.getElementById('categoryModal'));
          if (categoryModal) {
            categoryModal.hide();
          }
          
          // Səhifəni yenilə
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        } else {
          TableUtils.showErrorMessage(response.data.message || 'Kateqoriya yadda saxlanılarkən xəta baş verdi');
        }
      })
      .catch(error => {
        TableUtils.handleError(error, 'Kateqoriya yadda saxlanılarkən xəta baş verdi');
      });
  },
};

// Initialize event listeners when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  CategoryOperations.initEventListeners();
});

// Export globally
window.CategoryOperations = CategoryOperations;
