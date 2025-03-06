class CreateEmployeeForm {
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
            employee_name: this.getValue("employeeAdd_Name"),
            employee_contactNumber: this.getValue("employeeAdd_ContactNumber"),
            employee_role: this.getValue("employeeAdd_Role"),
            employee_status: "Present",
            employee_pay: this.getValue("employeeAdd_Pay"),
            employee_workDays: "0",
            action: "create"
        };

        if (Object.entries(formData).some(([key, value]) => typeof value === "string" && !value.trim())) {
            this.submitButton.removeAttribute("loading");
            this.submitButton.setAttribute("variant", "warning");
            return null;
        }

        return formData;
    }

    getValue(id) {
        return document.getElementById(id)?.value.trim() || "";
    }

    clearInputField() {
        document.getElementById("employeeAdd_Name").value = "";
        document.getElementById("employeeAdd_ContactNumber").value = "";
        document.getElementById("employeeAdd_Role").value = "";
        document.getElementById("employeeAdd_Pay").value = "";
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

    handleSubmit() {
        console.log("Submit button clicked.");
        this.submitButton.setAttribute("loading", true);

        const formData = this.getFormData();
        if (formData) this.sendFormData(formData);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    new CreateEmployeeForm("submitEmployeeAdd");
    console.log("Employee Create JS Loaded!");
});
