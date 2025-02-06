export function showAlert(message, type = "info") {
  // Bootstrap Toast istifadə edirik
  const toast = document.createElement("div");
  toast.className = `toast align-items-center text-white bg-${getAlertClass(
    type
  )} border-0`;
  toast.setAttribute("role", "alert");
  toast.setAttribute("aria-live", "assertive");
  toast.setAttribute("aria-atomic", "true");

  toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${getAlertIcon(type)} ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

  // Toast container
  let container = document.querySelector(".toast-container");
  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container position-fixed bottom-0 end-0 p-3";
    document.body.appendChild(container);
  }

  container.appendChild(toast);

  const bsToast = new bootstrap.Toast(toast);
  bsToast.show();

  // Auto remove after hide
  toast.addEventListener("hidden.bs.toast", () => {
    toast.remove();
  });
}

function getAlertClass(type) {
  switch (type) {
    case "success":
      return "success";
    case "error":
      return "danger";
    case "warning":
      return "warning";
    case "info":
      return "info";
    default:
      return "primary";
  }
}

function getAlertIcon(type) {
  switch (type) {
    case "success":
      return '<i class="fas fa-check-circle"></i>';
    case "error":
      return '<i class="fas fa-exclamation-circle"></i>';
    case "warning":
      return '<i class="fas fa-exclamation-triangle"></i>';
    case "info":
      return '<i class="fas fa-info-circle"></i>';
    default:
      return '<i class="fas fa-bell"></i>';
  }
}
