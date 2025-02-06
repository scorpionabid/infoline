// Bootstrap
import "./bootstrap";

// Auth components
import "./auth/login";
import "./auth/forgot";

// Global app configuration
window.APP = {
  locale: "az",
  messages: {
    errors: {
      default: "Xəta baş verdi",
      server: "Server xətası baş verdi",
      validation: "Məlumatları düzgün daxil edin",
      network: "Şəbəkə xətası",
    },
    alerts: {
      loading: "Yüklənir...",
      processing: "Əməliyyat icra olunur...",
      success: "Əməliyyat uğurla tamamlandı",
      warning: "Diqqət",
      info: "Məlumat",
    },
  },
};

// Global error handler
window.onerror = function (msg, url, line) {
  console.error("JS Error:", msg, "Line:", line);
};

// DataTable initialization
$(document).ready(function () {
  // DataTable initialization
  new DataTable(dataTable, {
    language: {
      url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/az.json",
    },
  });
});
