// resources/js/sector-admin/reports.js içinə əlavə edəcəyimiz funksiyalar

function sortTable(criterion) {
  const table = document.getElementById("schoolsComparisonTable");
  const tbody = table.querySelector("tbody");
  const rows = Array.from(tbody.querySelectorAll("tr"));

  rows.sort((a, b) => {
    switch (criterion) {
      case "name":
        return a.cells[0].textContent.localeCompare(b.cells[0].textContent);

      case "completion":
        const aCompletion = parseFloat(a.cells[1].textContent);
        const bCompletion = parseFloat(b.cells[1].textContent);
        return bCompletion - aCompletion;

      case "date":
        const aDate = parseInt(a.cells[3].dataset.sort);
        const bDate = parseInt(b.cells[3].dataset.sort);
        return bDate - aDate;

      default:
        return 0;
    }
  });

  // Sıralanmış sətirləri yenidən əlavə et
  rows.forEach((row) => tbody.appendChild(row));
}

// Cədvəl üzərində hover effekti
document.querySelectorAll("#schoolsComparisonTable tbody tr").forEach((row) => {
  row.addEventListener("mouseenter", () => {
    row.style.backgroundColor = "#f8f9fa";
  });

  row.addEventListener("mouseleave", () => {
    row.style.backgroundColor = "";
  });
});
