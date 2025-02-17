// public/js/settings/table.js

document.addEventListener("DOMContentLoaded", function () {
  initializeDataTables();
  setupEventListeners();
});

function initializeDataTables() {
  if (document.getElementById("columnsTable")) {
    $("#columnsTable").DataTable({
      language: {
        url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/az.json",
      },
    });
  }
}

function setupEventListeners() {
  const categoryForm = document.getElementById("addCategoryForm");
  const columnForm = document.getElementById("addColumnForm");

  if (categoryForm) {
    categoryForm.addEventListener("submit", handleCategorySubmit);
  }

  if (columnForm) {
    columnForm.addEventListener("submit", handleColumnSubmit);
  }
}

function handleCategorySubmit(e) {
  e.preventDefault();
  const formData = new FormData(e.target);

  axios
    .post("/settings/categories", formData)
    .then((response) => {
      if (response.data.success) {
        window.location.reload();
      }
    })
    .catch((error) => {
      showErrorAlert(error.response.data.message);
    });
}

function handleColumnSubmit(e) {
  e.preventDefault();
  const formData = new FormData(e.target);

  axios
    .post("/settings/columns", formData)
    .then((response) => {
      if (response.data.success) {
        window.location.reload();
      }
    })
    .catch((error) => {
      showErrorAlert(error.response.data.message);
    });
}

function showErrorAlert(message) {
  Swal.fire({
    title: "Xəta!",
    text: message,
    icon: "error",
  });
}
    