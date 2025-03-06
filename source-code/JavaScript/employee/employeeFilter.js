document.addEventListener("DOMContentLoaded", () => {
  console.log("Employee Filter JS Loaded!");

  const orderBy = document.getElementById("dropdownOrderBy");
  const statusBy = document.getElementById("dropdownStatusBy");
  const sortBy = document.getElementById("dropdownSortBy");
  const applyButton = document.getElementById("filterApplyButton");

  applyButton.addEventListener("click", () => {
    applyButton.setAttribute("loading", true);
    const orderValue = orderBy.value;
    const statusValue = statusBy.value;
    const sortValue = sortBy.value;

    if (orderValue === "" || statusValue === "" || sortValue === "") {
      applyButton.removeAttribute("loading");
      applyButton.setAttribute("variant", "warning");
      return;
    }

    let query = `WHERE employee.status = '${statusValue}' ORDER BY ${orderValue} ${sortValue}`;
    fetchFilteredData(query);
  });

  async function fetchFilteredData(query) {
    console.log("Query Sent:", query);

    try {
      const response = await fetch("employee.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "sql_query=" + encodeURIComponent(query),
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
