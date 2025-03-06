class UpdateCustomerForms {
  constructor(submitButtonId) {
    this.submitButton = document.getElementById(submitButtonId);

    if (!this.submitButton) {
      console.error("Submit button not found.");
      return;
    }

    this.submitButton.addEventListener("click", () => this.handleSubmit());
  }

  getFormData() {
    let customerID = document.getElementById("UpdateCustomer_ID")?.value;
    let customerName = document.getElementById("UpdateCustomer_NewName")?.value;
    let customerContactNumber = document.getElementById("UpdateCustomer_NewContactNumber")?.value;
    let customerAddress = document.getElementById("UpdateCustomer_NewAddress")?.value;

    if (customerID === "" && customerName === "" && customerContactNumber === "" && customerAddress === "") {
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "warning");
      return null;
    }

    let formData = {
      update_CustomerID: customerID,
      update_CustomerName: customerName,
      update_CustomerContactNumber: customerContactNumber,
      update_CustomerAddress: customerAddress,
      action: "customerUpdate",
    };

    return formData;
  }

  getValue(id) {
    return document.getElementById(id)?.value.trim() || "";
  }

  async sendFormData(formData) {
    try {
      const response = await fetch("customer.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(formData).toString(),
      });

      const data = await response.text();

      if (data.includes("success")) {
        this.clearInputFields();
        this.submitButton.removeAttribute("loading");
        this.submitButton.setAttribute("variant", "success");
        setTimeout(() => {
          window.location.reload();
        }, 3000);
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
    if (formData) {
      this.sendFormData(formData);
    }
  }

  clearInputFields() {
    document.getElementById("UpdateCustomer_ID").value = "";
    document.getElementById("UpdateCustomer_NewName").value = "";
    document.getElementById("UpdateCustomer_NewContactNumber").value = "";
    document.getElementById("UpdateCustomer_NewAddress").value = "";
  }
}

// Initialize when the page loads
document.addEventListener("DOMContentLoaded", () => {
  new UpdateCustomerForms("submitCustomerUpdate");
  console.log("Customer Update JS Loaded!");

  fetch("/Coolant/source-code/Controller/customerController_Feedback.php?fetch_Customer=true")
    .then((response) => response.json())
    .then((data) => {
      const dropdown = document.getElementById("UpdateCustomer_ID");
      dropdown.innerHTML = "<sl-option value=''>Select Customer</sl-option>";

      data.forEach((appointment) => {
        const option = document.createElement("sl-option");
        option.value = appointment.id;
        option.textContent = `${appointment.id} - ${appointment.name}`;
        dropdown.appendChild(option);
      });
    })
    .catch((error) => console.error("Error fetching appointments:", error));
});
