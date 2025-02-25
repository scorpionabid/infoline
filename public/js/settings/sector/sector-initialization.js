// sector-initialization.js
import { SELECTORS, CONFIG } from "./sector-config.js";
import { SectorEndpoints } from "./sector-endpoints.js";
import { SectorFormHandler } from "./sector-form-handler.js";
import { SectorAdminManager } from "./sector-admin-management.js";

class SectorManager {
  constructor() {
    this.formHandler = new SectorFormHandler();
    this.adminManager = new SectorAdminManager(() => this.loadSectors());
  }

  init() {
    this.initializeComponents();
    this.setupEventListeners();
    this.loadSectors();
    this.cleanupDeletedSectors();
    this.adminManager.init();
  }

  initializeComponents() {
    // Select2 initialization
    if ($.fn.select2) {
      $(SELECTORS.regionSelect).select2({
        ...CONFIG.selectOptions,
        dropdownParent: $(SELECTORS.modal),
        placeholder: "Region seçin",
      });
    }
  }

  setupEventListeners() {
    this.formHandler.initEventListeners();

    // Digər event listeners
    $(document).on(
      "click",
      ".assign-admin-btn",
      this.openAdminModal.bind(this)
    );
    $(document).on("click", ".remove-admin-btn", this.removeAdmin.bind(this));
    $(document).on("click", ".delete-sector-btn", this.deleteSector.bind(this));
  }

  async cleanupDeletedSectors() {
    try {
      const response = await fetch(ENDPOINTS.cleanupDeleted, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          Accept: "application/json",
        },
      });

      const data = await response.json();

      if (data.success && data.message) {
        console.log("Cleanup result:", data.message);
      }
    } catch (error) {
      console.error("Error cleaning up deleted sectors:", error);
    }
  }

  async loadSectors() {
    try {
      const sectors = await SectorEndpoints.fetchSectors();
      this.renderSectors(sectors);
    } catch (error) {
      toastr.error("Sektorlar yüklənərkən xəta baş verdi");
    }
  }

  renderSectors(sectors) {
    const regionSectors = {};

    sectors.forEach((sector) => {
      if (!regionSectors[sector.region_id]) {
        regionSectors[sector.region_id] = [];
      }
      regionSectors[sector.region_id].push(sector);
    });

    $(".region-sectors").each(function () {
      const regionId = $(this).data("region-id");
      const sectorsList = regionSectors[regionId] || [];

      $(this).empty();

      if (sectorsList.length === 0) {
        $(this).append(`
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            Bu regionda sektor yoxdur
                        </td>
                    </tr>
                `);
      } else {
        sectorsList.forEach((sector) => {
          $(this).append(`
                        <tr>
                            <td>${sector.name}</td>
                            <td>${sector.admin_name}</td>
                            <td>${sector.info}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-primary btn-sm edit-sector-btn" data-sector-id="${sector.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm delete-sector-btn" data-sector-id="${sector.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `);
        });
      }
    });
  }

  // Digər köməkçi metodlar...
}

// Initialize when document is ready
console.log('Initializing sector manager...');
document.addEventListener('DOMContentLoaded', () => {
  console.log('Document ready...');
  const sectorManager = new SectorManager();
  console.log('SectorManager instance created...');
  sectorManager.init();
});

export default SectorManager;
