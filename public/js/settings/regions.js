console.log("Regions.js loaded");

// Region operations
function editRegion(id) {
  console.log("Edit region ID:", id);
  const url = regionConfig.urls.edit.replace(":id", id);

  $.ajax({
    url: url,
    type: "GET",
    dataType: "json",
    headers: {
      'accept': 'application/json',
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (response) {
      console.log("Response:", response);
      if (response.success) {
        const modal = $("#regionModal");
        const form = modal.find("form");

        form.find('[name="name"]').val(response.region.name);
        form.find('[name="phone"]').val(response.region.phone);
        form.attr("action", regionConfig.urls.update.replace(":id", id));
        form.find('input[name="_method"]').val("PUT");

        modal.modal("show");
      }
    },
    error: function (xhr, status, error) {
      console.log("Error:", error);
      Swal.fire({
        icon: "error",
        title: "Xəta!",
        text: "Məlumatları yükləmək Mümkün olmadı",
      });
    },
  });
}

function deleteRegion(id) {
  console.log("Delete region ID:", id);

  Swal.fire({
    title: "Əminsiniz?",
    text: "Bu regionu silmək istədiyinizə əminsiniz?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Bəli, sil!",
    cancelButtonText: "Xeyr, ləğv et",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      const url = regionConfig.urls.delete.replace(":id", id);

      $.ajax({
        url: url,
        type: "DELETE",
        dataType: "json",
        success: function (response) {
          console.log("Response:", response);
          if (response.success) {
            Swal.fire("Silindi!", "Region uğurla silindi.", "success");
            $(`button[data-id="${id}"]`).closest("tr").remove();
          }
        },
        error: function (xhr, status, error) {
          console.log("Error:", error);
          console.log("Response:", xhr.responseText);
          Swal.fire("Xəta!", "Silinmə zamanı xəta baş verdi", "error");
        },
      });
    }
  });
}

// Event Listeners
$(document).ready(function () {
  console.log("Document ready");

  $(".btn-edit-region").on("click", function () {
    const id = $(this).data("id");
    console.log("Edit button clicked for ID:", id);
    editRegion(id);
  });

  $(".btn-delete-region").on("click", function () {
    const id = $(this).data("id");
    console.log("Delete button clicked for ID:", id);
    deleteRegion(id);
  });

  // Region form submit
  $("#regionModal form").on("submit", function (e) {
    e.preventDefault();
    const form = $(this);

    $.ajax({
      url: form.attr("action"),
      type: form.attr("method"),
      data: form.serialize(),
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $("#regionModal").modal("hide");
          Swal.fire({
            icon: "success",
            title: "Uğurlu!",
            text: response.message,
            timer: 1500,
          }).then(() => {
            window.location.reload();
          });
        }
      },
      error: function (xhr) {
        console.log("Error:", xhr.responseText);
        Swal.fire({
          icon: "error",
          title: "Xəta!",
          text: xhr.responseJSON?.message || "Xəta baş verdi",
        });
      },
    });
  });
});
