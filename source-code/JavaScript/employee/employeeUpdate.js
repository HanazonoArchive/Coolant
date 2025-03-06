class UpdateEmployeeForm {
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
      update_EmployeeID: this.getValue("UpdateEmployee_ID"),
      update_EmployeeName: this.getValue("UpdateEmployee_NewName"),
      update_EmployeeContactNumber: this.getValue(
        "UpdateEmployee_NewContactNumber"
      ),
      update_EmployeeRole: this.getValue("UpdateEmployee_NewRole"),
      update_EmployeePay: this.getValue("UpdateEmployee_NewPay"),
      update_EmployeeStatus: this.getValue("UpdateEmploye_NewStatus"),
      action: "update",
    };

    // Extract individual values for validation
    const {
      update_EmployeeID,
      update_EmployeeName,
      update_EmployeeContactNumber,
      update_EmployeeRole,
      update_EmployeePay,
      update_EmployeeStatus,
    } = formData;

    // Condition 1: Only EmployeeID and EmployeeStatus are filled
    const onlyRequiredFilled =
      update_EmployeeID &&
      update_EmployeeStatus &&
      !update_EmployeeName &&
      !update_EmployeeContactNumber &&
      !update_EmployeeRole &&
      !update_EmployeePay;

    // Condition 2: All fields are filled
    const allFieldsFilled =
      update_EmployeeID &&
      update_EmployeeStatus &&
      update_EmployeeName &&
      update_EmployeeContactNumber &&
      update_EmployeeRole &&
      update_EmployeePay;

    // Proceed only if one of the conditions is met
    if (onlyRequiredFilled || allFieldsFilled) {
      return formData;
    } else {
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "warning");
    }
    return null;
  }

  getValue(id) {
    return document.getElementById(id)?.value.trim() || "";
  }

  async sendFormData(formData) {
    try {
      const response = await fetch("employee.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams(formData).toString(),
      });

      const data = await response.text();
      if (data.includes("success")) {
        this.submitButton.removeAttribute("loading");
        this.submitButton.setAttribute("variant", "success");
        this.clearInputField();
        setTimeout(() => {window.location.reload();}, 3000);
      } else {
        this.submitButton.removeAttribute("loading");
        this.submitButton.setAttribute("variant", "warning");
      }
    } catch (error) {
      console.error("Error fetching data:", error);
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "danger");
    }
  }

  clearInputField() {
    document.getElementById("UpdateEmployee_ID").value = "";
    document.getElementById("UpdateEmployee_NewName").value = "";
    document.getElementById("UpdateEmployee_NewContactNumber").value = "";
    document.getElementById("UpdateEmployee_NewRole").value = "";
    document.getElementById("UpdateEmployee_NewPay").value = "";
    document.getElementById("UpdateEmploye_NewStatus").value = "";
  }

  handleSubmit() {
    console.log("Submit button clicked.");
    this.submitButton.setAttribute("loading", true);

    const formData = this.getFormData();
    if (formData) this.sendFormData(formData);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  new UpdateEmployeeForm("submitEmployeeUpdate");
  console.log("Employee Update JS Loaded!");

  fetch("/Coolant/source-code/Controller/employeeController_Employee.php?fetch_Employee=true")
    .then((response) => response.json())
    .then((data) => {
      const dropdown = document.getElementById("UpdateEmployee_ID");
      dropdown.innerHTML = "<sl-option value=''>Select Employee</sl-option>";

      data.forEach((appointment) => {
        const option = document.createElement("sl-option");
        option.value = appointment.id;
        option.textContent = `${appointment.id} - ${appointment.name}`;
        dropdown.appendChild(option);
      });
    })
    .catch((error) => console.error("Error fetching appointments:", error));
});
