// sectorAdmin.js
const SectorAdmin = {
  config: {
    endpoints: {
      base: "/settings/personal/sectors",
      assignAdmin: "/settings/personal/sectors/:id/assign-admin",
      updateAdmin: "/settings/personal/sectors/:id/update-admin",
      removeAdmin: "/settings/personal/sectors/:id/remove-admin",
      checkUsername: "/api/check-username",
    },
    selectors: {
      form: "#sectorAdminForm",
      modal: "#sectorAdminModal",
      createButton: ".assign-admin-btn",
      editButton: ".edit-admin-btn",
      removeButton: ".remove-admin-btn",
      submitButton: "#sectorAdminSubmitBtn",
      inputs: {
        sectorId: "#sectorIdInput",
        firstName: '[name="first_name"]',
        lastName: '[name="last_name"]',
        email: '[name="email"]',
        username: '[name="username"]',
        password: '[name="password"]',
        utisCode: '[name="utis_code"]',
      },
      errorContainer: "#formErrors",
    },
  },

  init() {
    console.log("SectorAdmin: Initializing...");
    this.initializeElements();
    this.setupValidation();
    this.setupEventListeners();
    console.log("SectorAdmin: Initialized successfully");
  },

  initializeElements() {
    const { selectors } = this.config;
    this.form = $(selectors.form);
    this.modal = $(selectors.modal);
    this.errorContainer = $(selectors.errorContainer);
    this.submitButton = $(selectors.submitButton);
  },

  setupValidation() {
    console.log("SectorAdmin: Setting up form validation");

    if (!$.validator) {
      console.error("SectorAdmin: jQuery Validator plugin not found");
      return;
    }

    this.form.validate({
      rules: {
        first_name: {
          required: true,
          minlength: 2,
          maxlength: 255,
        },
        last_name: {
          required: true,
          minlength: 2,
          maxlength: 255,
        },
        email: {
          required: true,
          email: true,
          maxlength: 255,
        },
        username: {
          required: true,
          minlength: 4,
          maxlength: 50,
          pattern: /^[a-zA-Z0-9_]+$/,
        },
        password: {
          required: function () {
            return !$("#adminId").val();
          },
          minlength: 8,
          pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/,
        },
        utis_code: {
          required: true,
          digits: true,
          minlength: 7,
          maxlength: 7,
        },
      },
      messages: {
        first_name: {
          required: "Ad tələb olunur",
          minlength: "Ad minimum 2 simvol olmalıdır",
          maxlength: "Ad maksimum 255 simvol ola bilər",
        },
        last_name: {
          required: "Soyad tələb olunur",
          minlength: "Soyad minimum 2 simvol olmalıdır",
          maxlength: "Soyad maksimum 255 simvol ola bilər",
        },
        email: {
          required: "E-poçt tələb olunur",
          email: "Düzgün e-poçt ünvanı daxil edin",
          maxlength: "E-poçt maksimum 255 simvol ola bilər",
        },
        username: {
          required: "İstifadəçi adı tələb olunur",
          minlength: "İstifadəçi adı minimum 4 simvol olmalıdır",
          maxlength: "İstifadəçi adı maksimum 50 simvol ola bilər",
          pattern:
            "İstifadəçi adı yalnız hərflər, rəqəmlər və alt xətt ola bilər",
        },
        password: {
          required: "Şifrə tələb olunur",
          minlength: "Şifrə minimum 8 simvol olmalıdır",
          pattern:
            "Şifrə ən azı bir böyük hərf, bir kiçik hərf və bir rəqəm ehtiva etməlidir",
        },
        utis_code: {
          required: "UTIS kodu tələb olunur",
          digits: "UTIS kodu yalnız rəqəmlərdən ibarət olmalıdır",
          minlength: "UTIS kodu 7 rəqəm olmalıdır",
          maxlength: "UTIS kodu 7 rəqəm olmalıdır",
        },
      },
      errorElement: "div",
      errorClass: "invalid-feedback",
      highlight: (element) => $(element).addClass("is-invalid"),
      unhighlight: (element) => $(element).removeClass("is-invalid"),
      submitHandler: (form, event) => {
        event.preventDefault();
        this.handleSubmit(event);
      },
    });
  },

  setupEventListeners() {
    console.log("SectorAdmin: Setting up event listeners");

    // Modal events
    this.modal.on("show.bs.modal", this.handleModalShow.bind(this));
    this.modal.on("hidden.bs.modal", this.handleModalHide.bind(this));

    // Action buttons
    $(this.config.selectors.createButton).on("click", (e) =>
      this.handleCreate(e)
    );
    $(this.config.selectors.editButton).on("click", (e) => this.handleEdit(e));
    $(this.config.selectors.removeButton).on("click", (e) =>
      this.handleRemove(e)
    );

    // Real-time validation
    this.setupRealTimeValidation();
  },

  setupRealTimeValidation() {
    const { selectors } = this.config;

    // Username availability check
    $(selectors.inputs.username).on(
      "blur",
      this.checkUsernameAvailability.bind(this)
    );

    // Password strength
    $(selectors.inputs.password).on(
      "input",
      this.updatePasswordStrength.bind(this)
    );

    // UTIS code format
    $(selectors.inputs.utisCode).on("input", (e) => {
      $(e.target).val($(e.target).val().replace(/\D/g, ""));
    });
  },

  async handleSubmit(e) {
    e.preventDefault();
    console.log("Form submit başladı");

    if (!this.form.valid()) {
      console.log("Form validasiyası uğursuz oldu");
      return;
    }

    // Get sector_id from hidden input
    const sectorId = this.form.find('input[name="sector_id"]').val();
    console.log("Göndəriləcək Sektor ID:", sectorId);

    if (!sectorId) {
      console.error("Sektor ID tapılmadı");
      toastr.error("Sektor ID tapılmadı");
      return;
    }

    this.startLoading();

    try {
      const formData = new FormData(this.form[0]);

      // Double-check sector_id is in FormData
      if (!formData.get("sector_id")) {
        formData.set("sector_id", sectorId);
      }

      // Log form data for debugging
      console.log("Form data being sent:");
      for (let pair of formData.entries()) {
        console.log(pair[0] + ": " + pair[1]);
      }

      const response = await fetch(this.form.attr("action"), {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: formData,
      });

      const data = await response.json();
      console.log("Server response:", data);

      if (!response.ok) {
        throw {
          message: data.message || "Xəta baş verdi",
          errors: data.errors || {},
        };
      }

      // Success handling
      this.handleSuccess(data);
    } catch (error) {
      this.handleFormErrors(error.errors || {});
      console.error("Error submitting form:", error);
      toastr.error(error.message || "Xəta baş verdi");
    } finally {
      this.stopLoading();
    }
  },

  handleFormErrors(errors) {
    // Clear previous errors
    this.form.find(".is-invalid").removeClass("is-invalid");
    this.form.find(".invalid-feedback").remove();

    // Show new errors
    Object.entries(errors).forEach(([field, messages]) => {
      const $input = this.form.find(`[name="${field}"]`);
      const $formGroup = $input.closest(".form-group");

      $input.addClass("is-invalid");
      $formGroup.append(
        `<div class="invalid-feedback">${
          Array.isArray(messages) ? messages.join(", ") : messages
        }</div>`
      );
    });
  },

  async handleCreate(e) {
    const sectorId = $(e.currentTarget).data("sector-id");
    console.log("SectorAdmin: Opening create modal for sector:", sectorId);

    this.resetForm();
    $(this.config.selectors.inputs.sectorId).val(sectorId);
    this.modal.modal("show");
  },

  async handleEdit(e) {
    const sectorId = $(e.currentTarget).data("sector-id");
    const adminId = $(e.currentTarget).data("admin-id");
    console.log("SectorAdmin: Loading admin data for editing:", {
      sectorId,
      adminId,
    });

    try {
      const response = await this.sendRequest(
        `${this.config.endpoints.base}/${sectorId}/admin/${adminId}`,
        null,
        "GET"
      );

      this.fillForm(response.data);
      $("#adminId").val(adminId);
      this.modal.modal("show");
    } catch (error) {
      this.handleError(error);
    }
  },

  async handleRemove(e) {
    const sectorId = $(e.currentTarget).data("sector-id");
    const adminName = $(e.currentTarget).data("admin-name");

    console.log("SectorAdmin: Attempting to remove admin:", {
      sectorId,
      adminName,
    });

    const result = await Swal.fire({
      title: "Əminsiniz?",
      text: `${adminName} adlı admini silmək istədiyinizə əminsiniz?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Bəli, sil",
      cancelButtonText: "Xeyr",
    });

    if (result.isConfirmed) {
      try {
        const response = await this.sendRequest(
          this.config.endpoints.removeAdmin.replace(":id", sectorId),
          null,
          "DELETE"
        );
        await this.handleSuccess(response);
      } catch (error) {
        this.handleError(error);
      }
    }
  },

  async sendRequest(url, data = null, method = "POST") {
    console.log("SectorAdmin: Sending request:", { url, method });

    const options = {
      method,
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        Accept: "application/json",
      },
    };

    if (data) {
      if (data instanceof FormData) {
        options.body = data;
      } else {
        options.headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(data);
      }
    }

    const response = await fetch(url, options);
    const responseData = await response.json();

    if (!response.ok) {
      console.error("SectorAdmin: Request failed:", responseData);
      throw new Error(responseData.message || "Xəta baş verdi");
    }

    console.log("SectorAdmin: Request successful:", responseData);
    return responseData;
  },

  startLoading() {
    this.submitButton
      .prop("disabled", true)
      .html('<i class="fas fa-spinner fa-spin me-1"></i> Gözləyin...');
  },

  stopLoading() {
    this.submitButton.prop("disabled", false).html("Təyin et");
  },

  async handleSuccess(data) {
    await Swal.fire({
      icon: "success",
      title: "Uğurlu!",
      text: data.message,
      showConfirmButton: false,
      timer: 1500,
    });

    // Səhifəni yeniləmədən əvvəl gözlə
    setTimeout(() => {
      window.location.reload();
    }, 1500);
  },

  handleError(error) {
    console.error("SectorAdmin: Error occurred:", error);

    Swal.fire({
      icon: "error",
      title: "Xəta!",
      text: error.message || "Sistem xətası baş verdi",
      confirmButtonText: "Bağla",
    });
  },

  fillForm(data) {
    console.log("SectorAdmin: Filling form with data:", data);

    Object.entries(this.config.selectors.inputs).forEach(
      ([field, selector]) => {
        const value = data[field.toLowerCase()];
        if (value) {
          $(selector).val(value);
        }
      }
    );
  },

  resetForm() {
    console.log("SectorAdmin: Resetting form");

    this.form[0].reset();
    this.form.find(".is-invalid").removeClass("is-invalid");
    this.form.find(".is-valid").removeClass("is-valid");
    this.errorContainer.addClass("d-none").html("");
    $("#adminId").val("");
    $("#passwordStrength").remove();
  },

  handleModalShow(e) {
    const button = $(e.relatedTarget);
    const sectorId = button.data("sector-id");
    console.log("Modal açıldı, Sektor ID:", sectorId);

    if (!sectorId) {
      console.error("Sektor ID tapılmadı");
      toastr.error("Sektor ID tapılmadı");
      return false;
    }

    // Reset form first
    this.resetForm();

    // Set form action for assign-admin endpoint
    const action = `${this.config.endpoints.base}/${sectorId}/assign-admin`;
    this.form.attr("action", action);

    // Set sector_id in hidden input
    const hiddenInput = this.form.find('input[name="sector_id"]');
    if (hiddenInput.length === 0) {
      this.form.append(
        `<input type="hidden" name="sector_id" value="${sectorId}">`
      );
    } else {
      hiddenInput.val(sectorId);
    }
  },
  // sector-admin.js
  handleSubmit: function (e) {
    e.preventDefault();
    const sectorId = this.modal.find("[data-sector-id]").data("sector-id");

    if (!sectorId) {
      toastr.error("Zəhmət olmasa sektor seçin!");
      return;
    }

    // Forma məlumatlarını yığ və göndər
    const formData = new FormData(this.form[0]);
    formData.set("sector_id", sectorId);

    $.ajax({
      url: `/settings/personal/sectors/${sectorId}/assign-admin`,
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: (response) => {
        if (response.success) window.location.reload();
      },
      error: (xhr) => {
        this.handleFormErrors(xhr.responseJSON.errors);
      },
    });
  },
};  