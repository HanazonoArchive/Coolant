class UpdateAppointmentForm {
  constructor(submitButtonId) {
    this.submitButton = document.getElementById(submitButtonId);

    if (!this.submitButton) {
      console.error("Submit button not found.");
      return;
    }

    this.submitButton.addEventListener("click", () => this.handleSubmit());
  }

  getFormData() {
    let formData = {
      update_AppointmentID: this.getValue("appointmentUpdate_ID"),
      update_Category: this.getValue("appointmentUpdate_Category"),
      update_Priority: this.getValue("appointmentUpdate_Priority"),
      update_Date: this.getValue("appointmentUpdate_Date"),
      action: "update",
    };

    // Check for empty fields
    if (Object.values(formData).some((value) => !value.trim())) {
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "warning");
      return null;
    }

    return formData;
  }

  getValue(id) {
    return document.getElementById(id)?.value.trim() || "";
  }

  async sendFormData(formData) {
    try {
      const response = await fetch("appointment.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(formData).toString(),
      });

      const data = await response.text();

      if (data.includes("success")) {
        setTimeout(() => location.reload(), 2000);
        return;
      }

      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "success");
      this.clearFields();
    } catch (error) {
      console.error("Error fetching data:", error);
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "danger");
    }
  }

  clearFields() {
    document.getElementById("appointmentUpdate_ID").value = "";
    document.getElementById("appointmentUpdate_Category").value = "";
    document.getElementById("appointmentUpdate_Priority").value = "";
    document.getElementById("appointmentUpdate_Date").value = "";
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
  new UpdateAppointmentForm("submitUpdateAppointment");
  console.log("Appointment Update JS Loaded!");

  fetch(
    "/Coolant/source-code/Controller/quotationController.php?fetch_appointments=true"
  )
    .then((response) => response.json())
    .then((data) => {
      const dropdown = document.getElementById("appointmentUpdate_ID");
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
