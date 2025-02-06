// resources/js/bootstrap.js

// Axios HTTP client quraşdırması
import axios from "axios";
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// CSRF token quraşdırması
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
  window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
}

// Bootstrap JS komponentləri
import "bootstrap";

// SweetAlert2 quraşdırması
import Swal from "sweetalert2";
window.Swal = Swal;

// Qlobal event bus
window.EventBus = new (class {
  constructor() {
    this.listeners = {};
  }

  on(event, callback) {
    if (!this.listeners[event]) {
      this.listeners[event] = [];
    }
    this.listeners[event].push(callback);
  }

  emit(event, data) {
    if (this.listeners[event]) {
      this.listeners[event].forEach((callback) => callback(data));
    }
  }
})();

// Error handling
window.addEventListener("error", function (e) {
  console.error("Global error:", e);
});

// Auth state
window.Auth = {
  check() {
    return document.body.classList.contains("user-logged-in");
  },
};
