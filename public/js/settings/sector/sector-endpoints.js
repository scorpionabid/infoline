// sector-endpoints.js
import { ENDPOINTS } from "./sector-config.js";

export class SectorEndpoints {
  static async fetchSectors() {
    try {
      const response = await fetch(ENDPOINTS.data);
      const data = await response.json();

      if (!data.data) {
        throw new Error("Invalid data format");
      }

      return data.data;
    } catch (error) {
      console.error("Error loading sectors:", error);
      throw error;
    }
  }

  static async deleteSector(sectorId) {
    try {
      const response = await fetch(`${ENDPOINTS.base}/${sectorId}`, {
        method: "DELETE",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
      });

      return await response.json();
    } catch (error) {
      console.error("Error deleting sector:", error);
      throw error;
    }
  }

  static async assignAdmin(sectorId, userId) {
    try {
      const url = ENDPOINTS.assignAdmin.replace("{id}", sectorId);
      const response = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({ user_id: userId }),
      });

      return await response.json();
    } catch (error) {
      console.error("Error assigning admin:", error);
      throw error;
    }
  }
}

// Example usage:
// const sectorEndpoints = new SectorEndpoints();
// sectorEndpoints.fetchSectors().then(sectors => {
//     console.log(sectors);
// });  