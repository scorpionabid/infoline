// Global konfiqurasiya
const sectorConfig = {
  urls: {
    edit: `${window.appConfig.baseUrl}/api/v1/sectors/:id/edit`,
    update: `${window.appConfig.baseUrl}/api/v1/sectors/:id`,
    delete: `${window.appConfig.baseUrl}/api/v1/sectors/:id`,
    assignAdmin: `${window.appConfig.baseUrl}/settings/personal/sectors/:sectorId/admin`,
  },
};

// Modal instances
let sectorModal = null;
let sectorAdminModal = null;

// Utility functions
function initializeModals() {
  console.log("🔧 Modallar initiallaşdırılır");

  const sectorModalElement = document.getElementById("sectorModal");
  const sectorAdminModalElement = document.getElementById("sectorAdminModal");

  if (sectorModalElement) {
    sectorModal = new bootstrap.Modal(sectorModalElement, {
      backdrop: true,
      keyboard: true,
    });
  }

  if (sectorAdminModalElement) {
    sectorAdminModal = new bootstrap.Modal(sectorAdminModalElement, {
      backdrop: true,
      keyboard: true,
    });
  }
}

// CRUD Operations
function editSector(sectorId) {
  console.log(`🔍 Sektor düzəlişi üçün ID: ${sectorId}`);

  if (!sectorModal) {
    console.error("❌ Sektor modalı tapılmadı");
    return;
  }

  $.ajax({
    url: sectorConfig.urls.edit.replace(":id", sectorId),
    type: "GET",
    dataType: "json",
    success: function (response) {
      console.log("✅ Sektor məlumatları uğurla gətirildi:", response);

      const form = $("#sectorModal form");

      // Form məlumatlarının doldurulması
      form.find('[name="name"]').val(response.sector.name);
      form.find('[name="phone"]').val(response.sector.phone);
      form.find('[name="region_id"]').val(response.sector.region_id);

      // Form atributlarının yenilənməsi
      form.attr("action", sectorConfig.urls.update.replace(":id", sectorId));
      form.find('input[name="_method"]').val("PUT");

      console.log("📝 Form düzəlişə hazırlandı");
      sectorModal.show();
    },
    error: function (xhr) {
      console.error("❌ Sektor məlumatları gətirilə bilmədi:", xhr);
      Swal.fire({
        icon: "error",
        title: "Xəta!",
        text: xhr.responseJSON?.message || "Məlumatları yükləmək mümkün olmadı",
      });
    },
  });
}

function deleteSector(id) {
  console.log(`🗑️ Sektor silinməsi üçün ID: ${id}`);

  Swal.fire({
    title: "Əminsiniz?",
    text: "Bu sektoru silmək istədiyinizə əminsiniz?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Bəli, sil!",
    cancelButtonText: "Xeyr, ləğv et",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: sectorConfig.urls.delete.replace(":id", id),
        type: "DELETE",
        dataType: "json",
        success: function (response) {
          console.log("✅ Sektor uğurla silindi:", response);
          Swal.fire({
            icon: "success",
            title: "Silindi!",
            text: "Sektor uğurla silindi",
            timer: 1500,
          }).then(() => {
            window.location.reload();
          });
        },
        error: function (xhr) {
          console.error("❌ Sektor silinə bilmədi:", xhr);
          Swal.fire({
            icon: "error",
            title: "Xəta!",
            text: xhr.responseJSON?.message || "Silinmə zamanı xəta baş verdi",
          });
        },
      });
    }
  });
}

// Event handlers
function handleSectorFormSubmit(e) {
  e.preventDefault();
  const form = $(this);

  console.log("📤 Sektor formu göndərilir");
  console.log("🔗 Form URL-i:", form.attr("action"));

  $.ajax({
    url: form.attr("action"),
    type: form.attr("method"),
    data: form.serialize(),
    dataType: "json",
    success: function (response) {
      console.log("✅ Sektor uğurla əlavə/yeniləndi:", response);
      sectorModal.hide();

      Swal.fire({
        icon: "success",
        title: "Uğurlu!",
        text: response.message,
        timer: 1500,
      }).then(() => {
        window.location.reload();
      });
    },
    error: function (xhr) {
      console.error("❌ Sektor əməliyyatı uğursuz oldu:", xhr);
      Swal.fire({
        icon: "error",
        title: "Xəta!",
        text: xhr.responseJSON?.message || "Xəta baş verdi",
      });
    },
  });
}

function handleAdminAssignment(e) {
  e.preventDefault();
  const form = $(this);
  const sectorId = form.find('input[name="sector_id"]').val();

  console.log(`👤 Sektor admini təyin edilir. Sektor ID: ${sectorId}`);

  $.ajax({
    url: sectorConfig.urls.assignAdmin.replace(":sectorId", sectorId),
    type: "POST",
    data: form.serialize(),
    dataType: "json",
    success: function (response) {
      console.log("✅ Sektor admini uğurla təyin edildi:", response);
      sectorAdminModal.hide();

      Swal.fire({
        icon: "success",
        title: "Uğurlu!",
        text: response.message,
        timer: 1500,
      }).then(() => {
        window.location.reload();
      });
    },
    error: function (xhr) {
      console.error("❌ Sektor admini təyin edilə bilmədi:", xhr);
      Swal.fire({
        icon: "error",
        title: "Xəta!",
        text: xhr.responseJSON?.message || "Xəta baş verdi",
      });
    },
  });
}

// Document ready
$(document).ready(function () {
  console.log("🚀 Sektor əməliyyatları üçün event listener-lər qurulur");

  // Modalların inizializasiyası
  initializeModals();

  // Event listener-lərin quraşdırılması
  $("#sectorModal form").on("submit", handleSectorFormSubmit);
  $("#sectorAdminModal form").attr(
      'action',
      "{{ route('personal.sectors.admin', ':id') }}".replace(':id', sectorId)
  );
  console.log("✅ Sektor əməliyyatları hazırdır");
});
// ... digər kodlar eyni qalır ...

function showSectorAdminModal(sectorId) {
    if (!sectorAdminModal) {
        console.error("❌ Admin modalı tapılmadı");
        return;
    }

    // Form action və sector_id-ni təyin et
    const form = $("#sectorAdminModal form");
    form.find('#sectorIdInput').val(sectorId);
    
    console.log("👤 Sektor admin modalı açılır:", sectorId);
    sectorAdminModal.show();
}

function handleAdminAssignment(e) {
    e.preventDefault();
    const form = $(this);
    const sectorId = form.find('#sectorIdInput').val();

    console.log(`👤 Sektor admini təyin edilir. Sektor ID: ${sectorId}`);

    $.ajax({
        url: `/settings/personal/sectors/${sectorId}/admin`,
        type: "POST",
        data: form.serialize(),
        headers: {
            'X-CSRF-TOKEN': window.appConfig.csrfToken
        },
        success: function (response) {
            console.log("✅ Sektor admini uğurla təyin edildi:", response);
            sectorAdminModal.hide();

            Swal.fire({
                icon: "success",
                title: "Uğurlu!",
                text: response.message,
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        },
        error: function (xhr) {
            console.error("❌ Sektor admini təyin edilə bilmədi:", xhr);
            Swal.fire({
                icon: "error",
                title: "Xəta!",
                text: xhr.responseJSON?.message || "Xəta baş verdi"
            });
        }
    });
}

// Document ready
$(document).ready(function () {
    console.log("🚀 Sektor əməliyyatları üçün event listener-lər qurulur");

    // Modalların inizializasiyası
    initializeModals();

    // Event listener-lərin quraşdırılması
    $("#sectorModal form").on("submit", handleSectorFormSubmit);
    $("#sectorAdminModal form").on("submit", handleAdminAssignment);

    // Admin təyin etmə düyməsi üçün listener
    $('.assign-admin-btn').on('click', function() {
        const sectorId = $(this).data('sector-id');
        showSectorAdminModal(sectorId);
    });

    console.log("✅ Sektor əməliyyatları hazırdır");
});