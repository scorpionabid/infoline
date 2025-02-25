// school-utils.js
export class SchoolUtils {
  /**
   * Form submit düyməsinin vəziyyətini dəyişir
   * @param {jQuery} submitBtn - Submit düyməsi
   * @param {boolean} isLoading - Yükləmə vəziyyəti
   */
  static toggleSubmitButton(submitBtn, isLoading) {
    submitBtn.prop("disabled", isLoading);

    if (isLoading) {
      submitBtn.html(`
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Gözləyin...
            `);
    } else {
      submitBtn.html(submitBtn.data("original-text") || "Yadda Saxla");
    }
  }

  /**
   * AJAX əməliyyatının nəticəsini göstərir
   * @param {Object} response - Cavab
   * @param {jQuery} form - Form elementi
   * @param {jQuery} modal - Modal elementi
   */
  static handleAjaxResponse(response, form, modal) {
    if (response.success) {
      toastr.success(response.message);

      if (modal) modal.modal("hide");
      if (form) form[0].reset();

      window.location.reload();
    } else {
      toastr.error(response.message);
    }
  }

  /**
   * Xəta mesajlarını göstərir
   * @param {Object} xhr - XMLHttpRequest
   */
  static displayErrorMessages(xhr) {
    const errors = xhr.responseJSON?.errors || {};

    Object.values(errors)
      .flat()
      .forEach((error) => {
        toastr.error(error);
      });
  }

  /**
   * Select2 üçün ajax konfiguratorunu hazırlayır
   * @param {string} url - Endpoint URL
   * @param {Function} processResults - Nəticələri emal edən funksiya
   * @returns {Object} Select2 ajax konfigurasiyası
   */
  static createSelect2AjaxConfig(url, processResults) {
    return {
      url: url,
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          search: params.term,
          page: params.page || 1,
        };
      },
      processResults:
        processResults ||
        function (data) {
          return {
            results: data.results,
            pagination: {
              more: data.has_more || false,
            },
          };
        },
      cache: true,
    };
  }
}
