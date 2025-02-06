import { showAlert } from "../components/Alert";
import { validateForm } from "../utils/validation";

document.addEventListener("DOMContentLoaded", function () {
  // Elements
  const form = document.getElementById("loginForm");
  const loginInput = document.getElementById("login");
  const loginLabel = document.getElementById("loginLabel");
  const emailLogin = document.getElementById("emailLogin");
  const usernameLogin = document.getElementById("usernameLogin");
  const schoolSearch = document.getElementById("schoolSearch");
  const schoolResults = document.getElementById("schoolResults");
  const togglePassword = document.getElementById("togglePassword");
  const password = document.getElementById("password");

  if (!form) return; // Login səhifəsində deyiliksə

  // Login növünü dəyiş
  function updateLoginType(isEmail) {
    loginInput.type = isEmail ? "email" : "text";
    loginInput.name = isEmail ? "email" : "username";
    loginInput.placeholder = isEmail
      ? "Email daxil edin"
      : "İstifadəçi adını daxil edin";
    loginLabel.innerHTML = `<i class="fas fa-${
      isEmail ? "envelope" : "user"
    }"></i> ${isEmail ? "Email" : "İstifadəçi adı"}`;
  }

  // Event listeners
  emailLogin?.addEventListener("change", () => updateLoginType(true));
  usernameLogin?.addEventListener("change", () => updateLoginType(false));

  // Şifrəni göstər/gizlət
  togglePassword?.addEventListener("click", function () {
    const type = password.type === "password" ? "text" : "password";
    password.type = type;
    this.innerHTML = `<i class="fas fa-eye${
      type === "password" ? "" : "-slash"
    }"></i>`;
  });

  // Məktəb axtarışı
  let searchTimeout;
  schoolSearch?.addEventListener("input", function () {
    clearTimeout(searchTimeout);
    if (this.value.length < 3) {
      schoolResults.innerHTML = "";
      return;
    }

    searchTimeout = setTimeout(async () => {
      try {
        const response = await fetch(`/api/schools/search?query=${this.value}`);
        const schools = await response.json();

        schoolResults.innerHTML = schools.length
          ? schools
              .map(
                (school) => `
                    <button type="button" 
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                            data-username="${school.admin_username}">
                        <span>${school.name}</span>
                        <small class="text-muted">${school.admin_username}</small>
                    </button>
                `
              )
              .join("")
          : '<div class="list-group-item text-muted">Nəticə tapılmadı</div>';
      } catch (error) {
        console.error("Axtarış xətası:", error);
        showAlert("Axtarış zamanı xəta baş verdi", "error");
      }
    }, 300);
  });

  // Məktəb seçimi
  schoolResults?.addEventListener("click", function (e) {
    const button = e.target.closest("button");
    if (button) {
      const username = button.dataset.username;
      usernameLogin.checked = true;
      updateLoginType(false);
      loginInput.value = username;
      schoolResults.innerHTML = "";
      schoolSearch.value = button.querySelector("span").textContent;
    }
  });

  // Click-dən kənar bağla
  document.addEventListener("click", function (e) {
    if (
      schoolResults &&
      !schoolSearch?.contains(e.target) &&
      !schoolResults.contains(e.target)
    ) {
      schoolResults.innerHTML = "";
    }
  });

  // Form submit
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Validation
    if (!validateForm(this)) {
      return;
    }

    const button = this.querySelector('button[type="submit"]');
    button.disabled = true;
    button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${window.APP.messages.alerts.loading}`;

    // Form submit
    this.submit();
  });
});
