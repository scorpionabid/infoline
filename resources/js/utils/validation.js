import { showAlert } from "../components/Alert";

export function validateForm(form) {
  const inputs = form.querySelectorAll("input[required]");
  let isValid = true;

  inputs.forEach((input) => {
    if (!input.value.trim()) {
      isValid = false;
      input.classList.add("is-invalid");

      const label = form.querySelector(`label[for="${input.id}"]`);
      const fieldName = label ? label.textContent.trim() : input.name;

      showAlert(`${fieldName} daxil edilməlidir`, "warning");
    } else {
      input.classList.remove("is-invalid");

      // Email validation
      if (input.type === "email" && !validateEmail(input.value)) {
        isValid = false;
        input.classList.add("is-invalid");
        showAlert("Düzgün email formatı daxil edin", "warning");
      }
    }
  });

  return isValid;
}

export function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}
