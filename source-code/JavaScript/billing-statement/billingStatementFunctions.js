document.addEventListener("DOMContentLoaded", () => {
  console.log("billingStatementFunctions script loaded");

  const submitButton = document.getElementById("generateBillingReport");
  if (!submitButton) {
    console.error("Submit button not found");
    return;
  }

  fetch("/Coolant/source-code/Controller/billingStatementController.php?fetch_appointments=true")
    .then((response) => response.json())
    .then((data) => {
      const dropdown = document.getElementById("billingDetails_AppointmentID");
      if (!dropdown) {
        console.error("Dropdown element not found");
        return;
      }

      dropdown.innerHTML = "<sl-option value=''>Select Appointment</sl-option>";
      
      data.forEach((appointment) => {
        if (appointment.id && appointment.name) {
          const option = document.createElement("sl-option");
          option.value = appointment.id;
          option.textContent = `${appointment.id} - ${appointment.name}`;
          dropdown.appendChild(option);
        }
      });
    })
    .catch((error) => console.error("Error fetching appointments:", error));
    
  submitButton.addEventListener("click", async function () {
    submitButton.setAttribute("loading", true);

    let appointmentID = document.getElementById("billingDetails_AppointmentID")?.value || "";

    // Document Header
    let dHeader = {
      companyName: document.getElementById("billingHeader_CompanyName")?.value || "",
      companyAddress: document.getElementById("billingHeader_CompanyAddress")?.value || "",
      companyNumber: document.getElementById("billingHeader_CompanyNumber")?.value || "",
      companyEmail: document.getElementById("billingHeader_CompanyEmail")?.value || "",
    };

    // Document Body Information
    let dBodyInfo = {
      billingDate: document.getElementById("billingBody_Date")?.value || "",
      customerName: document.getElementById("billingBody_CustomerName")?.value || "",
      customerLocation: document.getElementById("billingBody_Location")?.value || "",
    };

    // Document Footer Information
    let dFooterInfo = {
      authorizedName: document.getElementById("billingFooter_AuthorizedName")?.value || "",
      authorizedRole: document.getElementById("billingFooter_AuthorizedRole")?.value || "",
      remarks: document.getElementById("billingFooter_Remarks")?.value || "",
    };

    if (!appointmentID) {
      submitButton.removeAttribute("loading");
      submitButton.setAttribute("variant", "warning");
      return;
    }

    let formData = {
      appointmentID: appointmentID,
      action: "billingStatementDATA",
      document: {
        header: dHeader,
        body: dBodyInfo,
        footer: dFooterInfo,
      },
    };
    sendFormData(formData, submitButton);
  });
});

async function sendFormData(formData, submitButton) {
  try {
    const response = await fetch(window.location.pathname, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(formData),
    });

    const data = await response.text();
    console.log(formData);

    if (data.includes("success")) {
      submitButton.removeAttribute("loading");
        submitButton.setAttribute("variant", "success");
        // setTimeout(() => {
        //   window.location.reload();
        // }, 3000);
        // clearAllInputs();
      }
  } catch (error) {
    console.error("Error fetching data:", error);
    submitButton.removeAttribute("loading");
    submitButton.setAttribute("variant", "danger");
  }
}

// Clear all input fields
function clearAllInputs() {
  document.querySelectorAll("input, textarea").forEach((input) => (input.value = ""));
}