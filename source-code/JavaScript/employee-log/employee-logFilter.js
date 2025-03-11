document.addEventListener("DOMContentLoaded", () => {
  console.log("EmployeeLog Filter JS Loaded");

  const orderBy = document.getElementById("dropdownOrderBy");
  const sortBy = document.getElementById("dropdownSortBy");
  const applyButton = document.getElementById("filterApplyButton");

  applyButton.addEventListener("click", () => {
    applyButton.setAttribute("loading", true);
    const orderValue = orderBy.value;
    const sortValue = sortBy.value;

    let orderQuery = `ORDER BY ${orderValue} ${sortValue}`;

    fetchFilteredData(orderQuery);
  });

  async function fetchFilteredData(orderQuery) {
    try {
      const response = await fetch(window.location.pathname, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "sql_query=" + encodeURIComponent(orderQuery),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.text();
      document.querySelector(".appointment-table").innerHTML = data;
      applyButton.removeAttribute("loading");
      applyButton.setAttribute("variant", "success");
    } catch (error) {
      console.error("Error fetching data:", error);
      applyButton.removeAttribute("loading");
      applyButton.setAttribute("variant", "danger");
    }
  }
});
