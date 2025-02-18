const sectorConfig = {
  urls: {
    edit: `${window.appConfig.baseUrl}/api/v1/sectors/:id/edit`,
    update: `${window.appConfig.baseUrl}/api/v1/sectors/:id`,
    delete: `${window.appConfig.baseUrl}/api/v1/sectors/:id`,
    assignAdmin: `${window.appConfig.baseUrl}/settings/personal/sectors/:id/admin`,
    getAdmin: `${window.appConfig.baseUrl}/api/v1/sectors/:id/admin`,
  },
};

const sectorAdminForm = {
  init() {
    this.form = $("#sectorAdminForm");
    this.setupValidation();
    this.handleSubmit();
  },

  setupValidation() {
    this.form.validate({
      rules: {
        first_name: { required: true, maxlength: 255 },
        last_name: { required: true, maxlength: 255 },
        email: {
          required: true,
          email: true,
          pattern: /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/,
        },
        username: { required: true, maxlength: 50 },
        password: { required: true, minlength: 8 },
        utis_code: {
          required: true,
          pattern: /^[0-9]{7}$/,
        },
      },
      messages: {
        first_name: "Ad daxil edilməlidir",
        last_name: "Soyad daxil edilməlidir",
        email: "Düzgün email formatı daxil edin",
        username: "İstifadəçi adı tələb olunur",
        password: "Şifrə minimum 8 simvol olmalıdır",
        utis_code: "UTİS kodu 7 rəqəmdən ibarət olmalıdır",
      },
      errorElement: "span",
      errorClass: "invalid-feedback",
      highlight: (element) => $(element).addClass("is-invalid"),
      unhighlight: (element) => $(element).removeClass("is-invalid"),
    });
  },

  handleSubmit() {
    this.form.on("submit", this.submitForm.bind(this));
  },

  async submitForm(e) {
    e.preventDefault();
    if (!this.form.valid()) return;

    const sectorId = this.form.find("#sectorIdInput").val();
    try {
      const response = await $.ajax({
        url: sectorConfig.urls.assignAdmin.replace(":id", sectorId),
        type: "POST",
        data: this.form.serialize(),
        headers: { "X-CSRF-TOKEN": window.appConfig.csrfToken },
      });

      await this.handleSuccess(response);
    } catch (error) {
      this.handleError(error);
    }
  },

  async handleSuccess(response) {
    await Swal.fire({
      icon: "success",
      title: "Uğurlu!",
      text: response.message,
      timer: 1500,
    });
    window.location.reload();
  },

  handleError(error) {
    const errors = error.responseJSON?.errors || {};
    Object.entries(errors).forEach(([field, [message]]) => {
      const input = this.form.find(`[name="${field}"]`);
      input.addClass("is-invalid").next(".invalid-feedback").text(message);
    });

    Swal.fire({
      icon: "error",
      title: "Xəta!",
      text: error.responseJSON?.message || "Xəta baş verdi",
    });
  },
};

async function showSectorAdminModal(sectorId) {
  try {
    const response = await $.get(
      sectorConfig.urls.getAdmin.replace(":id", sectorId)
    );
    const form = $("#sectorAdminForm");
    form.find("#sectorIdInput").val(sectorId);

    if (response.admin) {
      fillAdminForm(response.admin);
    } else {
      form[0].reset();
    }

    const modal = new bootstrap.Modal("#sectorAdminModal");
    modal.show();
  } catch (error) {
    console.error("Admin məlumatları alına bilmədi:", error);
  }
}

function fillAdminForm(admin) {
  const form = $("#sectorAdminForm");
  ["first_name", "last_name", "email", "username", "utis_code"].forEach(
    (field) => {
      form.find(`[name="${field}"]`).val(admin[field]);
    }
  );
}

$(document).ready(() => {
  sectorAdminForm.init();
  $(".assign-admin-btn").on("click", function () {
    const sectorId = $(this).data("sector-id");
    showSectorAdminModal(sectorId);
  });
});
