console.log("📡 Sectors.js yükləndi");

function editSector(id) {
  console.log(`🔍 Sektor düzəlişi üçün ID: ${id}`);
  const url = sectorConfig.urls.edit.replace(":id", id);

  $.ajax({
    url: url,
    type: "GET",
    dataType: "json",
    success: function (response) {
      console.log("✅ Sektor məlumatları uğurla gətirildi:", response);

      const modal = $("#sectorModal");
      const form = modal.find("form");

      form.find('[name="name"]').val(response.sector.name);
      form.find('[name="phone"]').val(response.sector.phone);
      form.find('[name="region_id"]').val(response.sector.region_id);
      form.attr("action", sectorConfig.urls.update.replace(":id", id));
      form.find('input[name="_method"]').val("PUT");

      console.log("📝 Form düzəlişə hazırlandı");
      modal.modal("show");
    },
    error: function (xhr) {
      console.error("❌ Sektor məlumatları gətirilə bilmədi:", xhr);

      Swal.fire({
        icon: "error",
        title: "Xəta!",
        text: "Məlumatları yükləmək mümkün olmadı",
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
  }).then((result) => {
    if (result.isConfirmed) {
      const url = sectorConfig.urls.delete.replace(":id", id);

      console.log(`🔗 Silmə URL-i: ${url}`);

      $.ajax({
        url: url,
        type: "DELETE",
        dataType: "json",
        success: function (response) {
          console.log("✅ Sektor uğurla silindi:", response);

          Swal.fire("Silindi!", "Sektor uğurla silindi", "success");
          $(`button[onclick="deleteSector(${id})"]`).closest("tr").remove();
        },
        error: function (xhr) {
          console.error("❌ Sektor silinə bilmədi:", xhr);

          Swal.fire("Xəta!", "Silinmə zamanı xəta baş verdi", "error");
        },
      });
    }
  });
}

$(document).ready(function () {
  console.log("🚀 Sektor əməliyyatları üçün event listener-lər qurulur");

  // Sektor form submit
  $("#sectorModal form").on("submit", function (e) {
    e.preventDefault();
    const form = $(this);

    console.log("📤 Sektor formu göndərilir");
    console.log("🔗 Form URL-i:", form.attr("action"));
    console.log("📋 Form məlumatları:", form.serialize());

    $.ajax({
      url: form.attr("action"),
      type: form.attr("method"),
      data: form.serialize(),
      dataType: "json",
      success: function (response) {
        console.log("✅ Sektor uğurla əlavə/yeniləndi:", response);

        $("#sectorModal").modal("hide");
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
  });

  // Sektor admininin təyinatı
  $("#sectorAdminModal form").on("submit", function (e) {
    e.preventDefault();
    const form = $(this);
    const sectorId = form.find('input[name="sector_id"]').val();

    console.log(`👤 Sektor admini təyin edilir. Sektor ID: ${sectorId}`);

    $.ajax({
      url: form.attr("action").replace(":sectorId", sectorId),
      type: "POST",
      data: form.serialize(),
      dataType: "json",
      success: function (response) {
        console.log("✅ Sektor admini uğurla təyin edildi:", response);

        $("#sectorAdminModal").modal("hide");
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
  });
});
