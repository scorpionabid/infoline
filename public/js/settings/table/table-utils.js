// public/js/settings/table/table-utils.js

/**
 * Cədvəl əməliyyatları üçün ümumi funksiyalar
 * Bu fayl kateqoriya və sütun əməliyyatları üçün ortaq funksiyaları təmin edir
 */

const TableUtils = {
  // API Routes
  routes: {
    categories: {
      index: "/settings/table/categories",
      show: (id) => `/settings/table/categories/${id}`,
      store: "/settings/table/categories",
      update: (id) => `/settings/table/categories/${id}`,
      destroy: (id) => `/settings/table/categories/${id}`,
      assignments: (id) => `/settings/table/categories/${id}/assignments`,
      clone: (id) => `/settings/table/categories/${id}/clone`,
      status: (id) => `/settings/table/categories/${id}/status`,
      toggleStatus: (id) => `/settings/table/categories/${id}/status`,
    },
    columns: {
      index: "/settings/table/columns",
      byCategory: (categoryId) =>
        `/settings/table/columns/category/${categoryId}`,
      show: (id) => `/settings/table/columns/${id}`,
      store: "/settings/table/columns",
      update: (id) => `/settings/table/columns/${id}`,
      destroy: (id) => `/settings/table/columns/${id}`,
      status: (id) => `/settings/table/columns/${id}/status`,
      deadline: (id) => `/settings/table/columns/${id}/deadline`,
      limit: (id) => `/settings/table/columns/${id}/limit`,
      order: (categoryId) =>
        `/settings/table/columns/category/${categoryId}/order`,
      toggleStatus: (id) => `/settings/table/columns/${id}/toggle-status`,
      choices: {
        index: (id) => `/settings/table/columns/${id}/choices`,
        store: (id) => `/settings/table/columns/${id}/choices`,
        update: (id, choiceId) =>
          `/settings/table/columns/${id}/choices/${choiceId}`,
        destroy: (id, choiceId) =>
          `/settings/table/columns/${id}/choices/${choiceId}`,
      },
    },
  },

  // Error handling
  handleError: function (error, defaultMessage = "Xəta baş verdi") {
    console.error(error);

    let errorMessage = defaultMessage;
    let validationErrors = null;

    if (error.response) {
      if (error.response.data && error.response.data.message) {
        errorMessage = error.response.data.message;
      }

      if (error.response.data && error.response.data.errors) {
        validationErrors = error.response.data.errors;
      }
    } else if (error.request) {
      errorMessage =
        "Server cavab vermir, zəhmət olmasa bir az sonra cəhd edin";
    }

    if (validationErrors) {
      this.showValidationErrors(validationErrors);
    } else {
      this.showErrorMessage(errorMessage);
    }
  },

  showValidationErrors: function (errors) {
    const errorContainer = document.querySelector(".form-errors");
    if (!errorContainer) return;

    // Clear previous errors
    errorContainer.innerHTML = "";
    errorContainer.style.display = "block";

    // Add each error message
    Object.keys(errors).forEach((field) => {
      errors[field].forEach((message) => {
        const errorDiv = document.createElement("div");
        errorDiv.textContent = message;
        errorContainer.appendChild(errorDiv);

        // Highlight field if possible
        const fieldElement = document.querySelector(`[name="${field}"]`);
        if (fieldElement) {
          fieldElement.classList.add("is-invalid");

          // Add error message next to field
          const feedback =
            fieldElement.parentNode.querySelector(".invalid-feedback");
          if (feedback) {
            feedback.textContent = errors[field][0];
          }
        }
      });
    });
  },

  showErrorMessage: function (message) {
    // Use toastr if available
    if (typeof toastr !== "undefined") {
      toastr.error(message);
    } else {
      // Fallback to alert
      alert(message);
    }
  },

  showSuccessMessage: function (message) {
    // Use toastr if available
    if (typeof toastr !== "undefined") {
      toastr.success(message);
    } else {
      // Fallback to alert
      alert(message);
    }
  },

  // Form helpers
  resetForm: function (formId) {
    const form = document.getElementById(formId);
    if (form) {
      form.reset();

      // Clear validation errors
      form.querySelectorAll(".is-invalid").forEach((el) => {
        el.classList.remove("is-invalid");
      });

      const errorContainer = form.querySelector(".form-errors");
      if (errorContainer) {
        errorContainer.style.display = "none";
        errorContainer.innerHTML = "";
      }
    }
  },

  clearFormErrors: function () {
    const errorContainers = document.querySelectorAll(".form-errors");
    errorContainers.forEach((container) => {
      container.innerHTML = "";
      container.style.display = "none";
    });

    // Remove any error classes from form fields
    document.querySelectorAll(".is-invalid").forEach((field) => {
      field.classList.remove("is-invalid");
    });

    // Remove any error feedback elements
    document.querySelectorAll(".invalid-feedback").forEach((feedback) => {
      feedback.remove();
    });
  },

  // Initialization functions
  initDataTables: function () {
    if (
      typeof $.fn.DataTable !== "undefined" &&
      document.getElementById("columnsTable")
    ) {
      $("#columnsTable").DataTable({
        language: {
          url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/az.json",
        },
      });
    }
  },

  initSelect2: function () {
    if (typeof $.fn.select2 !== "undefined") {
      $(".select2").select2({
        placeholder: "Seçin...",
        allowClear: true,
        width: "100%",
      });
    }
  },

  initSortable: function (selector, categoryId) {
    if (typeof Sortable === "undefined") {
      console.error("Sortable kitabxanası tapılmadı");
      return;
    }

    const sortableEl = document.querySelector(selector);
    if (!sortableEl) {
      console.error(`"${selector}" elementi tapılmadı`);
      return;
    }

    // Sortable obyektini yaradaq
    const sortable = new Sortable(sortableEl, {
      handle: ".fa-grip-vertical", // Sürüşdürmə ikonası
      animation: 150,
      ghostClass: "bg-light", // Sürüşdürmə zamanı arxa fon
      onEnd: (evt) => {
        // Yeni sıralamaya görə ID-ləri toplayaq
        const columnIds = Array.from(
          sortableEl.querySelectorAll("[data-column-id]")
        ).map((item) => item.dataset.columnId);

        // Sıralamaya görə yeniləmə sorğusu göndərək
        axios
          .post(this.routes.columns.order(categoryId), { column_ids: columnIds })
          .then((response) => {
            if (response.data.success) {
              this.showSuccessMessage(
                response.data.message || "Sütunlar yenidən sıralandı"
              );
            }
          })
          .catch((error) => {
            this.handleError(
              error,
              "Sütunların sıralaması yenilənərkən xəta baş verdi"
            );
          });
      },
    });

    return sortable;
  },

  /**
   * Yükləmə göstəricisini göstərir
   * @param {string} message - Göstəriləcək mesaj
   */
  showLoadingOverlay: function (message = "Yüklənir...") {
    console.log(`Yükləmə göstəricisi göstərilir: ${message}`);

    // Əgər mövcud overlay varsa, onu gizlət
    this.hideLoadingOverlay();

    // Overlay yaradılır
    const overlay = document.createElement("div");
    overlay.id = "loadingOverlay";
    overlay.className = "loading-overlay";
    overlay.innerHTML = `
      <div class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Yüklənir...</span>
        </div>
        <div class="loading-message mt-2">${message}</div>
      </div>
    `;

    // Overlay-ə stil əlavə et
    const style = document.createElement("style");
    style.textContent = `
      .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
      }

      .loading-spinner {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        text-align: center;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
      }

      .loading-message {
        color: #333;
        font-weight: bold;
      }
    `;

    // DOM-a əlavə et
    document.head.appendChild(style);
    document.body.appendChild(overlay);

    console.log("Yükləmə göstəricisi göstərildi");
  },

  /**
   * Yükləmə göstəricisini gizlədir
   */
  hideLoadingOverlay: function () {
    console.log("Yükləmə göstəricisi gizlədilir");

    // Overlay-i tap və sil
    const overlay = document.getElementById("loadingOverlay");
    if (overlay) {
      overlay.remove();
      console.log("Yükləmə göstəricisi gizlədildi");
    }
  },
};

// Export globally
window.TableUtils = TableUtils;
