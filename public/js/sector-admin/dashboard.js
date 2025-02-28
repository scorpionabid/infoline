// resources/js/sector-admin/dashboard.js

class SectorDashboard {
  constructor() {
    this.initFilters();
    this.initModals();
    this.initForms();
  }

  initFilters() {
    // Axtarış
    $("#schoolSearch").on("input", () => this.filterSchools());

    // Status filter
    $("#statusFilter").on("change", () => this.filterSchools());

    // Doldurulma filter
    $("#completionFilter").on("change", () => this.filterSchools());
  }

  filterSchools() {
    const searchText = $("#schoolSearch").val().toLowerCase();
    const statusFilter = $("#statusFilter").val();
    const completionFilter = $("#completionFilter").val();

    $("tbody tr").each((i, row) => {
      const $row = $(row);
      let show = true;

      // Axtarış
      if (searchText) {
        const text = $row.text().toLowerCase();
        show = show && text.includes(searchText);
      }

      // Status
      if (statusFilter) {
        const status = $row.find(".badge").text().toLowerCase();
        show = show && status.includes(statusFilter);
      }

      // Doldurulma
      if (completionFilter) {
        const completion = parseInt($row.find(".progress-bar").text());
        const [min, max] = completionFilter.split("-").map(Number);
        show = show && completion >= min && completion <= max;
      }

      $row.toggle(show);
    });
  }

  initModals() {
    // Admin tipini dəyişdikdə
    $("#adminType").on("change", function () {
      const isNew = $(this).val() === "new";
      $("#newAdminSection").toggleClass("d-none", !isNew);
      $("#existingAdminSection").toggleClass("d-none", isNew);
    });
  }

  initForms() {
    // Yeni məktəb əlavə etmə
    $("#addSchoolForm").on("submit", async (e) => {
      e.preventDefault();
      await this.handleAddSchool(e.target);
    });

    // Admin təyin etmə
    $("#assignAdminForm").on("submit", async (e) => {
      e.preventDefault();
      await this.handleAssignAdmin(e.target);
    });
  }

  async handleAddSchool(form) {
    try {
      const formData = new FormData(form);
      const response = await fetch("/api/schools", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
            .content,
        },
        body: formData,
      });

      if (!response.ok) throw new Error("Network response was not ok");

      const result = await response.json();

      // Uğurlu əməliyyat
      this.showNotification("Məktəb uğurla əlavə edildi", "success");
      $("#addSchoolModal").modal("hide");
      location.reload(); // və ya cədvəli yenilə
    } catch (error) {
      console.error("Error:", error);
      this.showNotification("Xəta baş verdi", "error");
    }
  }

  async handleAssignAdmin(form) {
    try {
      const formData = new FormData(form);
      const response = await fetch("/api/schools/assign-admin", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
            .content,
        },
        body: formData,
      });

      if (!response.ok) throw new Error("Network response was not ok");

      const result = await response.json();

      // Uğurlu əməliyyat
      this.showNotification("Admin uğurla təyin edildi", "success");
      $("#assignAdminModal").modal("hide");
      location.reload(); // və ya cədvəli yenilə
    } catch (error) {
      console.error("Error:", error);
      this.showNotification("Xəta baş verdi", "error");
    }
  }

  showNotification(message, type = "info") {
    // Bootstrap toast və ya digər notification sistemi
  }
}

// Initialize when document is ready
document.addEventListener("DOMContentLoaded", () => {
  new SectorDashboard();
});
