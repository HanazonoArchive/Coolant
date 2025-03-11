class CancelAppointmentForm {
  constructor(submitButtonId) {
    this.submitButton = document.getElementById(submitButtonId);

    if (!this.submitButton) {
      console.error("Submit button not found.");
      return;
    }

    this.submitButton.addEventListener("click", () => this.handleSubmit());
  }

  getFormData() {
    let appointmentID = document.getElementById("cancelAppointment_ID")?.value;

    if (!appointmentID) {
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "warning");
      return null;
    }

    return JSON.stringify({
      appointment_ID: appointmentID,
      action: "cancel",
    });
  }

  async sendFormData(formData) {
    try {
      const response = await fetch(window.location.pathname, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: formData,
      });

      const data = await response.text();

      if (data.includes("success")) {
        this.submitButton.removeAttribute("loading");
        this.submitButton.setAttribute("variant", "success");
        setTimeout(() => {
          window.location.reload();
        }, 3000); // Reload after 3 seconds
      }
    } catch (error) {
      console.error("Error fetching data:", error);
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "danger")
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
  new CancelAppointmentForm("submitCancelAppointment");
  console.log("Quotation Cancel JS Loaded!");

  fetch(
    "/Coolant/source-code/Controller/quotationController.php?fetch_appointments=true"
  )
    .then((response) => response.json())
    .then((data) => {
      const dropdown = document.getElementById("cancelAppointment_ID");
      dropdown.innerHTML = "<sl-option value=''>Select Appointment</sl-option>";

      data.forEach((appointment) => {
        const option = document.createElement("sl-option");
        option.value = appointment.id;
        option.textContent = `${appointment.id} - ${appointment.name}`;
        dropdown.appendChild(option);
      });
    })
    .catch((error) => console.error("Error fetching appointments:", error));
});
