document.addEventListener("DOMContentLoaded", () => {
    console.log("customerFilter.js loaded");
    const sortBy = document.getElementById("dropdownSortBy");
    const applyButton = document.getElementById("filterApplyButton");
  
    applyButton.addEventListener("click", () => {
      applyButton.setAttribute("loading", true);
      const sortValue = sortBy.value;
  
      if (sortValue === "") {
        applyButton.removeAttribute("loading");
        applyButton.setAttribute("variant", "warning");
      } else {
        let query = `ORDER BY customer.id ${sortValue}`;
        fetchFilteredData(query);
      }
    });
  
    async function fetchFilteredData(query) {
      console.log("Query Sent:", query);
  
      try {
        const response = await fetch("customer.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "sql_query=" + encodeURIComponent(query),
        });
  
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
  
        const data = await response.text();
        document.querySelector(".appointment-table").innerHTML = data;
        console.log("Schedule Filter Query Sent Successfully!");
        applyButton.removeAttribute("loading");
        applyButton.setAttribute("variant", "success");
      } catch (error) {
        console.error("Error fetching data:", error);
        applyButton.removeAttribute("loading");
        applyButton.setAttribute("variant", "danger");
      }
    }
  });
  