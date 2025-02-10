// Globals
const API_URL = "/api/v1";
let currentCategoryId = null;
let currentColumnId = null;

// Event Listeners
document.addEventListener("DOMContentLoaded", () => {
  // Form validations
  initializeFormValidations();

  // Data type change handler
  initializeDataTypeHandler();

  // Initialize tooltips
  initializeTooltips();

  // Handle notifications
  handleNotifications();
});

// Initialize Bootstrap validations
function initializeFormValidations() {
  const forms = document.querySelectorAll(".needs-validation");
  forms.forEach((form) => {
    form.addEventListener("submit", (event) => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add("was-validated");
    });
  });
}

// Data type change handler
function initializeDataTypeHandler() {
  const dataTypeSelect = document.getElementById("dataType");
  if (dataTypeSelect) {
    dataTypeSelect.addEventListener("change", () => {
      toggleChoicesSection();
      updateTypeSpecificValidation();
    });
  }
}

// Initialize Bootstrap tooltips
function initializeTooltips() {
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
}

// Handle success/error notifications
function handleNotifications() {
  const notification = document.getElementById("notification");
  if (notification) {
    setTimeout(() => {
      notification.classList.add("fade");
      setTimeout(() => notification.remove(), 150);
    }, 3000);
  }
}

// Category operations
async function createCategory(formData) {
  try {
    const response = await fetch(`${API_URL}/categories`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
          .content,
      },
      body: JSON.stringify(Object.fromEntries(formData)),
    });

    if (!response.ok) throw new Error("Server error");

    const result = await response.json();
    window.location.reload();
  } catch (error) {
    console.error("Error creating category:", error);
    showError("Kateqoriya yaradılarkən xəta baş verdi");
  }
}

async function editCategory(id) {
  try {
    const response = await fetch(`${API_URL}/categories/${id}`);
    if (!response.ok) throw new Error("Server error");

    const category = await response.json();
    populateCategoryForm(category);

    currentCategoryId = id;
    openModal("categoryModal");
  } catch (error) {
    console.error("Error fetching category:", error);
    showError("Kateqoriya məlumatları alınarkən xəta baş verdi");
  }
}

async function deleteCategory(id) {
  if (!confirm("Bu kateqoriyanı silmək istədiyinizə əminsiniz?")) return;

  try {
    const response = await fetch(`${API_URL}/categories/${id}`, {
      method: "DELETE",
      headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
          .content,
      },
    });

    if (!response.ok) throw new Error("Server error");
    window.location.reload();
  } catch (error) {
    console.error("Error deleting category:", error);
    showError("Kateqoriya silinərkən xəta baş verdi");
  }
}

// Column operations
async function createColumn(formData) {
  try {
    const response = await fetch(`${API_URL}/columns`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
          .content,
      },
      body: JSON.stringify(Object.fromEntries(formData)),
    });

    if (!response.ok) throw new Error("Server error");

    const result = await response.json();
    window.location.reload();
  } catch (error) {
    console.error("Error creating column:", error);
    showError("Sütun yaradılarkən xəta baş verdi");
  }
}

async function editColumn(id) {
  try {
    const response = await fetch(`${API_URL}/columns/${id}`);
    if (!response.ok) throw new Error("Server error");

    const column = await response.json();
    populateColumnForm(column);

    currentColumnId = id;
    openModal("columnModal");
  } catch (error) {
    console.error("Error fetching column:", error);
    showError("Sütun məlumatları alınarkən xəta baş verdi");
  }
}

async function deleteColumn(id) {
  if (!confirm("Bu sütunu silmək istədiyinizə əminsiniz?")) return;

  try {
    const response = await fetch(`${API_URL}/columns/${id}`, {
      method: "DELETE",
      headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
          .content,
      },
    });

    if (!response.ok) throw new Error("Server error");
    window.location.reload();
  } catch (error) {
    console.error("Error deleting column:", error);
    showError("Sütun silinərkən xəta baş verdi");
  }
}

// Form helpers
function populateCategoryForm(category) {
  const form = document.getElementById("categoryForm");
  form.querySelector('[name="name"]').value = category.name;
  form.querySelector('[name="description"]').value = category.description || "";
}

function populateColumnForm(column) {
  const form = document.getElementById("columnForm");
  form.querySelector('[name="name"]').value = column.name;
  form.querySelector('[name="data_type"]').value = column.data_type;
  form.querySelector('[name="end_date"]').value = column.end_date || "";
  form.querySelector('[name="input_limit"]').value = column.input_limit || "";

  if (column.validation_rules) {
    Object.entries(column.validation_rules).forEach(([key, value]) => {
      const input = form.querySelector(`[name="validation_rules[${key}]"]`);
      if (input) input.checked = value;
    });
  }

  if (column.choices && column.choices.length > 0) {
    document.getElementById("choicesList").innerHTML = "";
    column.choices.forEach((choice) => addChoice(choice.value));
  }

  toggleChoicesSection();
  updateTypeSpecificValidation();
}

// UI helpers
function openModal(modalId) {
  const modal = new bootstrap.Modal(document.getElementById(modalId));
  modal.show();
}

function showError(message) {
  const alertHtml = `
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
  document
    .querySelector(".container-fluid")
    .insertAdjacentHTML("afterbegin", alertHtml);
}

function toggleChoicesSection() {
  const dataType = document.getElementById("dataType").value;
  const choicesSection = document.getElementById("choicesSection");

  if (["select", "multiselect"].includes(dataType)) {
    choicesSection.classList.remove("d-none");
  } else {
    choicesSection.classList.add("d-none");
  }
}

function updateTypeSpecificValidation() {
  const dataType = document.getElementById("dataType").value;
  const container = document.getElementById("typeSpecificValidation");
  let html = "";

  switch (dataType) {
    case "text":
      html = getTextValidationFields();
      break;
    case "number":
      html = getNumberValidationFields();
      break;
    case "file":
      html = getFileValidationFields();
      break;
  }

  container.innerHTML = html;
}

function getTextValidationFields() {
  return `
        <div class="row mt-3">
            <div class="col-md-6">
                <label class="form-label">Minimum uzunluq</label>
                <input type="number" class="form-control" name="validation_rules[min_length]" min="0">
            </div>
            <div class="col-md-6">
                <label class="form-label">Maximum uzunluq</label>
                <input type="number" class="form-control" name="validation_rules[max_length]" min="1">
            </div>
        </div>
    `;
}

function getNumberValidationFields() {
  return `
        <div class="row mt-3">
            <div class="col-md-6">
                <label class="form-label">Minimum dəyər</label>
                <input type="number" class="form-control" name="validation_rules[min]">
            </div>
            <div class="col-md-6">
                <label class="form-label">Maximum dəyər</label>
                <input type="number" class="form-control" name="validation_rules[max]">
            </div>
        </div>
    `;
}

function getFileValidationFields() {
  return `
        <div class="row mt-3">
            <div class="col-md-6">
                <label class="form-label">Fayl növləri</label>
                <input type="text" class="form-control" name="validation_rules[allowed_types]" 
                       placeholder="pdf,doc,docx">
            </div>
            <div class="col-md-6">
                <label class="form-label">Maximum həcm (MB)</label>
                <input type="number" class="form-control" name="validation_rules[max_size]" min="1">
            </div>
        </div>
    `;
}
