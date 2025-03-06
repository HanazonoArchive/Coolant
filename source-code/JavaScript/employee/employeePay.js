class EmployeePayForm {
  constructor(submitButtonId) {
    this.submitButton = document.getElementById(submitButtonId);

    if (!this.submitButton) {
      console.error("Submit button not found.");
      return;
    }

    this.submitButton.addEventListener("click", () => this.handleSubmit());
  }

  getFormData() {
    let employeeID = document.getElementById("payEmployee_ID")?.value;
    let confirmationTEXT = document.getElementById("payEmployee_Confirmation")?.value;

    if (!employeeID || !confirmationTEXT) {
      this.submitButton.removeAttribute("loading");
      this.submitButton.setAttribute("variant", "warning");
      return null;
    }

    return {
      employee_ID: employeeID,
      confirmationTEXT: confirmationTEXT,
      action: "payingEmployee",
    };
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
        setTimeout(() => {
          window.location.reload();
        }, 3000);
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
    document.getElementById("payEmployee_ID").value = "";
    document.getElementById("payEmployee_Confirmation").value = "";
  }

  handleSubmit() {
    console.log("Submit button clicked.");
    this.submitButton.setAttribute("loading", true);

    const formData = this.getFormData();
    if (formData) this.sendFormData(formData);
  }
}

document.addEventListener("DOMContentLoaded", async () => {
  new EmployeePayForm("submitEmployeePay");
  console.log("Employee Pay JS Loaded!");

  fetch("/Coolant/source-code/Controller/employeeController_Employee.php?fetch_Employee=true")
    .then((response) => response.json())
    .then((data) => {
      const dropdown = document.getElementById("payEmployee_ID");
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
