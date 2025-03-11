class PendingCollectionForm {
  constructor(submitButtonId) {
    this.submitButton = document.getElementById(submitButtonId);

    if (!this.submitButton) {
        this.submitButton = document.getElementById("submitCollection");
      return;
    }

    this.submitButton.addEventListener("click", () => this.handleSubmit());
  }

  getFormData() {
    let pendingCollection_ID = document.getElementById(
      "selectCollection_ID"
    )?.value;
    let collectionStatus = document.getElementById("statusCollection")?.value;

    if (!pendingCollection_ID) {
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "warning");
      return null;
    }

    return JSON.stringify({
      collection_ID: pendingCollection_ID,
      collectionStatus: collectionStatus,
      action: "PendingCollection",
    });
  }

  async sendFormData(formData) {
    try {
      const response = await fetch("billing-statement.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: formData, // JSON string
      });

      const data = await response.text();
      console.log(formData);

      if (data.includes("success")) {
        this.submitButton.removeAttribute("loading");
        this.submitButton.setAttribute("variant", "success");
        setTimeout(() => {
          window.location.reload();
        }, 3000);
        clearAllInputs();
      }
    } catch (error) {
      console.error("Error fetching data:", error);
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "danger");
    }
  }

  handleSubmit() {
    console.log("Submit button clicked.");
    this.submitButton.setAttribute("loading", true);

    const formData = this.getFormData();
    if (formData) this.sendFormData(formData);
  }
}

// Initialize when the page loads
document.addEventListener("DOMContentLoaded", () => {
  new PendingCollectionForm("submitCollection");
  console.log("Collection JS Loaded!");

  fetch(
    "/Coolant/source-code/Controller/billingStatementController.php?fetch_pending_collection=true"
  )
    .then((response) => response.json())
    .then((data) => {
      const dropdown = document.getElementById("selectCollection_ID");
      dropdown.innerHTML =
        "<sl-option value=''>Select Collection ID</sl-option>";

      data.forEach((appointment) => {
        const option = document.createElement("sl-option");
        option.value = appointment.id;
        option.textContent = `${appointment.id} - ${appointment.name}`;
        dropdown.appendChild(option);
      });
    })
    .catch((error) => console.error("Error fetching appointments:", error));
});
