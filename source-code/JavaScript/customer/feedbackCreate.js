class CreateFeedbackForm {
    constructor(submitButtonId, queryStatusId) {
        this.submitButton = document.getElementById(submitButtonId);

        if (!this.submitButton) {
            console.error("Submit button not found.");
            return;
        }

        this.submitButton.addEventListener("click", () => this.handleSubmit());
    }

    getFormData() {
        let formData = {
            appointment_ID: this.getValue("AddFeedback_AppointmentID"),
            feedback_comment: this.getValue("AddFeedback_Comment"),
            action: "feedbackCreate"
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
        document.getElementById("AddFeedback_AppointmentID").value = "";
        document.getElementById("AddFeedback_Comment").value = "";
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

    handleSubmit() {
        console.log("Submit button clicked.");
        this.submitButton.setAttribute("loading", true);

        const formData = this.getFormData();
        if (formData) this.sendFormData(formData);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    new CreateFeedbackForm("submitFeedbackAdd");
    console.log("Feedback Create JS Loaded!");

    fetch("/Coolant/source-code/Controller/customerController_Feedback.php?fetch_Appointment=true")
        .then((response) => response.json())
        .then((data) => {
          const dropdown = document.getElementById("AddFeedback_AppointmentID");
          dropdown.innerHTML = "<sl-option value=''>Select Feedback ID</sl-option>";
    
          data.forEach((appointment) => {
            const option = document.createElement("sl-option");
            option.value = appointment.id;
            option.textContent = `${appointment.id} - ${appointment.name}`;
            dropdown.appendChild(option);
          });
        })
        .catch((error) => console.error("Error fetching appointments:", error));
});
