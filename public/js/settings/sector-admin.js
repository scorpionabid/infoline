document.addEventListener("DOMContentLoaded", function () {
  const sectorAdminModal = new bootstrap.Modal(
    document.getElementById("sectorAdminModal")
  );
  const sectorAdminForm = document.getElementById("sectorAdminForm");
  const sectorAdminTable = document.getElementById("sectorAdminTable");

  // Sektor admininin təyin edilməsi
  function assignSectorAdmin(sectorId) {
    const formData = new FormData(sectorAdminForm);
    formData.append("sector_id", sectorId);

    axios
      .post(`/api/v1/sectors/${sectorId}/admin`, formData)
      .then((response) => {
        toastr.success("Sektor admini uğurla təyin edildi");
        sectorAdminModal.hide();
        loadSectorAdmins(sectorId);
      })
      .catch((error) => {
        const errorMessage = error.response?.data?.message || "Xəta baş verdi";
        toastr.error(errorMessage);
      });
  }

  // Sektor adminlərinin yüklənməsi
  function loadSectorAdmins(sectorId) {
    axios
      .get(`/api/v1/sectors/${sectorId}/admins`)
      .then((response) => {
        const admins = response.data.data;
        updateSectorAdminTable(admins);
      })
      .catch((error) => {
        toastr.error("Adminləri yükləmək mümkün olmadı");
      });
  }

  // Sektor adminləri cədvəlinin yenilənməsi
  function updateSectorAdminTable(admins) {
    if (sectorAdminTable) {
      const tbody = sectorAdminTable.querySelector("tbody");
      tbody.innerHTML = "";

      admins.forEach((admin) => {
        const row = `
                    <tr>
                        <td>${admin.name}</td>
                        <td>${admin.username}</td>
                        <td>
                            <button class="btn btn-sm btn-danger remove-admin" data-admin-id="${admin.id}">Sil</button>
                        </td>
                    </tr>
                `;
        tbody.insertAdjacentHTML("beforeend", row);
      });
    }
  }

  // Modal açıldıqda
  document
    .querySelectorAll('[data-bs-toggle="modal"][data-sector-id]')
    .forEach((trigger) => {
      trigger.addEventListener("click", function () {
        const sectorId = this.getAttribute("data-sector-id");
        sectorAdminForm.setAttribute("data-sector-id", sectorId);
        loadSectorAdmins(sectorId);
      });
    });

  // Form submit
  sectorAdminForm.addEventListener("submit", function (e) {
    e.preventDefault();
    const sectorId = this.getAttribute("data-sector-id");
    assignSectorAdmin(sectorId);
  });

  // Admin silmə
  sectorAdminTable?.addEventListener("click", function (e) {
    if (e.target.classList.contains("remove-admin")) {
      const adminId = e.target.getAttribute("data-admin-id");
      const sectorId = sectorAdminForm.getAttribute("data-sector-id");

      // Admin silmə əməliyyatı üçün axios sorğusu
      axios
        .delete(`/api/v1/sectors/${sectorId}/admins/${adminId}`)
        .then((response) => {
          toastr.success("Admin uğurla silindi");
          loadSectorAdmins(sectorId);
        })
        .catch((error) => {
          toastr.error("Admini silmək mümkün olmadı");
        });
    }
  });
});
