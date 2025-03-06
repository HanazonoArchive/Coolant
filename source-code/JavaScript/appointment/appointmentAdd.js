class CreateAppointmentForm {
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
      customer_name: this.getValue("appointmentCreateCustomer_Name"),
      customer_number: this.getValue("appointmentCreateCustomer_ContactNumber"),
      customer_address: this.getValue("appointmentCreateCustomer_Address"),
      appointment_date: this.getValue("appointmentCreate_Date"),
      appointment_category: this.getValue("appointmentCreate_Category"),
      appointment_priority: this.getValue("appointmentCreate_Priority"),
      appointment_status: "Pending",
      action: "create",
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
      
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "success");
      this.clearFields();

      if (data.includes("success")) {
        setTimeout(() => location.reload(), 2000);
        return;
      }

    } catch (error) {
      console.error("Error fetching data:", error);
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "danger");
    }
  }

  clearFields() {
    document.getElementById("appointmentCreateCustomer_Name").value = "";
    document.getElementById("appointmentCreateCustomer_ContactNumber").value = "";
    document.getElementById("appointmentCreateCustomer_Address").value = "";
    document.getElementById("appointmentCreate_Date").value = "";
    document.getElementById("appointmentCreate_Category").value = "";
    document.getElementById("appointmentCreate_Priority").value = "";
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
  new CreateAppointmentForm("submitCreateAppointment");
  console.log("Appointment Create JS Loaded!");
});
