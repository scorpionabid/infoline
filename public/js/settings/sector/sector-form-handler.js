// sector-form-handler.js
import { SELECTORS } from "./sector-config.js";
import { SectorEndpoints } from "./sector-endpoints.js";

export class SectorFormHandler {
  constructor() {
    this.form = document.querySelector(SELECTORS.form);
    this.modal = document.querySelector(SELECTORS.modal);
    this.submitBtn = document.querySelector(SELECTORS.submitBtn);
    this.nameInput = document.querySelector(SELECTORS.nameInput);
    this.regionSelect = document.querySelector(SELECTORS.regionSelect);
    this.idInput = document.querySelector(SELECTORS.idInput);
    this.modalTitle = document.querySelector(SELECTORS.modalTitle);
  }

  initEventListeners() {
    this.form.addEventListener("submit", this.handleSubmit.bind(this));
    this.regionSelect.addEventListener(
      "change",
      this.clearValidationError.bind(this)
    );
    this.nameInput.addEventListener(
      "input",
      this.clearValidationError.bind(this)
    );
  }

  clearValidationError(event) {
    const input = event.target;
    input.classList.remove("is-invalid");
    const errorElement = input.nextElementSibling;
    if (errorElement && errorElement.classList.contains("invalid-feedback")) {
      errorElement.textContent = "";
    }
  }

  async handleSubmit(event) {
    event.preventDefault();

    // Validasiya əməliyyatları
    const isValid = this.validateForm();
    if (!isValid) return;

    const submitBtn = this.submitBtn;
    const originalText = submitBtn.innerHTML;

    try {
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<i class="fas fa-spinner fa-spin me-1"></i> Gözləyin...';

      const formData = new FormData(this.form);
      const sectorId = this.idInput.value;

      const url = sectorId ? `${ENDPOINTS.base}/${sectorId}` : ENDPOINTS.base;

      const method = sectorId ? "PUT" : "POST";

      if (sectorId) {
        formData.append("_method", "PUT");
      }

      const response = await fetch(url, {
        method: method,
        body: formData,
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
      });

      const data = await response.json();

      if (!response.ok) {
        throw data;
      }

      toastr.success(data.message);
      $(this.modal).modal("hide");

      // Sektorları yenilə
      await SectorManager.loadSectors();
    } catch (error) {
      console.error("Error:", error);

      if (error.errors) {
        Object.entries(error.errors).forEach(([field, messages]) => {
          const input = this.form.querySelector(`[name="${field}"]`);
          input.classList.add("is-invalid");
          input.nextElementSibling.textContent = messages[0];
        });
      } else {
        toastr.error(error.message || "Xəta baş verdi");
      }
    } finally {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  }

  validateForm() {
    let hasError = false;
    const regionId = this.regionSelect.value;
    const name = this.nameInput.value.trim();

    if (!regionId) {
      this.regionSelect.classList.add("is-invalid");
      this.regionSelect.nextElementSibling.textContent = "Region seçilməlidir";
      hasError = true;
    }

    if (!name) {
      this.nameInput.classList.add("is-invalid");
      this.nameInput.nextElementSibling.textContent =
        "Sektor adı daxil edilməlidir";
      hasError = true;
    }

    return !hasError;
  }
}

const sectorFormHandler = new SectorFormHandler();
sectorFormHandler.initEventListeners();
