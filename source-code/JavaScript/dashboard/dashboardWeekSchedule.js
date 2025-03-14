document.addEventListener("DOMContentLoaded", function () {
  let weekScheduleContainers = {
    Monday: document.getElementById("scheduleDay_Monday"),
    Tuesday: document.getElementById("scheduleDay_Tuesday"),
    Wednesday: document.getElementById("scheduleDay_Wednesday"),
    Thursday: document.getElementById("scheduleDay_Thursday"),
    Friday: document.getElementById("scheduleDay_Friday"),
    Saturday: document.getElementById("scheduleDay_Saturday"),
    Sunday: document.getElementById("scheduleDay_Sunday"),
  };

  function getWeekRange() {
    let currentDate = new Date();
    let dayOfWeek = currentDate.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday

    // Adjust to get Monday
    let monday = new Date(currentDate);
    monday.setDate(
      currentDate.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1)
    );

    // Adjust to get Sunday
    let sunday = new Date(monday);
    sunday.setDate(monday.getDate() + 6);

    // Format YYYY-MM-DD
    let formatDate = (date) => date.toISOString().split("T")[0];

    return {
      startofWeek: formatDate(monday),
      endofWeek: formatDate(sunday),
    };
  }

  // Get Monday & Sunday dates
  const weekRange = getWeekRange();

  // Create requestData
  const requestData = {
    action: "loadWeekSchedule",
    startofWeek: weekRange.startofWeek,
    endofWeek: weekRange.endofWeek,
  };

  console.log("Week Start:", weekRange.startofWeek);
  console.log("Week End:", weekRange.endofWeek);

  sendRequestData(requestData);

  async function sendRequestData(data) {
    try {
      const response = await fetch(window.location.pathname, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      console.log("Full JSON Response:", result); // Log the entire object

      if (result.status === "success" && Array.isArray(result.data)) {
        console.log("Data received successfully!");
        processScheduleData(result.data);
      } else {
        console.error("Error retrieving data:", result.message);
      }
    } catch (error) {
      console.error("Error sending data:", error);
    }
  }

  function processScheduleData(data) {
    let navContainer = document.createElement("nav");
    navContainer.classList.add("sl-theme-dark");
    document.body.appendChild(navContainer); // Ensure it's added to the document

    data.forEach((entry, index) => {
      let date = new Date(entry.date);
      let dayOfWeek = date.toLocaleDateString("en-US", { weekday: "long" });

      let targetDiv = document.getElementById(`scheduleDay_${dayOfWeek}`);
      if (!targetDiv) return; // Skip if the day doesn't exist

      // Create a button that will trigger the modal
      let button = document.createElement("sl-button");
      button.textContent = `${entry.name}`;
      button.setAttribute("variant", "primary"); // Change color to green
      button.setAttribute("size", "small"); // Change button size
      button.style.width = "100%";
      button.style.boxSizing = "border-box";
      button.style.cursor = "pointer";
      button.style.margin = "0px";
      button.style.paddingLeft = "5px";
      button.style.paddingRight = "5px";
      button.style.paddingBottom = "5px";

      // Create the modal inside <nav class="sl-theme-dark">
      let modal = document.createElement("sl-dialog");
      modal.setAttribute("label", `${entry.name} / ${entry.date}`);
      modal.classList.add(`dialog-entry-${index}`); // Unique class

      modal.innerHTML = `
      <div 
      style="border: 1px solid #E7E7D9; color: #E7E7D9; padding: 20px; border-radius: 10px; 
         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); font-size: 16px; line-height: 1.6;
         display: flex; flex-direction: column; gap: 10px; max-width: 500px;">
  
  <div style="display: flex; justify-content: space-between; align-items: center;">
    <strong style="min-width: 180px;">Appointment ID:</strong>
    <span>${entry.id}</span>
  </div>

  <div style="display: flex; justify-content: space-between; align-items: center;">
    <strong style="min-width: 180px;">Name:</strong>
    <span>${entry.name}</span>
  </div>

  <div style="display: flex; justify-content: space-between; align-items: center;">
    <strong style="min-width: 180px;">Contact Number:</strong>
    <span>${entry.contact_number}</span>
  </div>

  <div style="display: flex; justify-content: space-between; align-items: center;">
    <strong style="min-width: 180px;">Address:</strong>
    <span>${entry.address}</span>
  </div>

  <div style="display: flex; justify-content: space-between; align-items: center;">
    <strong style="min-width: 180px;">Category:</strong>
    <span>${entry.category}</span>
  </div>

  <div style="display: flex; justify-content: space-between; align-items: center;">
    <strong style="min-width: 180px;">Priority:</strong>
    <span 
      style="display: inline-block; padding: 5px 12px; border-radius: 8px; font-weight: 600; 
             text-transform: capitalize; border: 2px solid ${
               entry.priority.toLowerCase() === "low"
                 ? "#27BAFD"
                 : entry.priority.toLowerCase() === "medium"
                 ? "#F1C40F"
                 : entry.priority.toLowerCase() === "high"
                 ? "#E67E22"
                 : entry.priority.toLowerCase() === "urgent"
                 ? "#E74C3C"
                 : "#E7E7D9"
             };
             color: ${
               entry.priority.toLowerCase() === "low"
                 ? "#27BAFD"
                 : entry.priority.toLowerCase() === "medium"
                 ? "#F1C40F"
                 : entry.priority.toLowerCase() === "high"
                 ? "#E67E22"
                 : entry.priority.toLowerCase() === "urgent"
                 ? "#E74C3C"
                 : "#E7E7D9"
             }; padding: 5px 12px;">
      ${entry.priority}
      </span>
    </div>
  </div>
  <sl-button slot="footer" variant="primary" id="close-${index}">Close</sl-button>
            `;

      // Append elements
      targetDiv.appendChild(button);
      navContainer.appendChild(modal); // Append modal inside <nav class="sl-theme-dark">

      // Event Listeners
      button.addEventListener("click", () => modal.show());
      modal
        .querySelector(`#close-${index}`)
        .addEventListener("click", () => modal.hide());
    });
  }

  // Highlight the current day's label
  let currentDay = new Date().toLocaleDateString("en-US", { weekday: "long" });
  let currentLabel = document.getElementById(currentDay);

  if (currentLabel) {
    currentLabel.style.color = "#27BAFD"; // Change text color
    currentLabel.style.fontWeight = "600"; // Make text bold
  }
});
