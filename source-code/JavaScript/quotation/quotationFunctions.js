document.addEventListener("DOMContentLoaded", () => {
    console.log("quotationFunctions script loaded");

    //Appointment - Done
    fetch("/Coolant/source-code/Controller/quotationController.php?fetch_appointments=true")
      .then((response) => response.json())
      .then((data) => {
        const dropdown = document.getElementById(
          "quotationDetails_AppointmentID"
        );
        dropdown.innerHTML = "<sl-option value=''>Select Appointment</sl-option>";
  
        data.forEach((appointment) => {
          const option = document.createElement("sl-option");
          option.value = appointment.id;
          option.textContent = `${appointment.id} - ${appointment.name}`;
          dropdown.appendChild(option);
        });
      })
      .catch((error) => console.error("Error fetching appointments:", error));
  
      // Employee ID 1 - Done
      fetch("/Coolant/source-code/Controller/quotationController.php?fetch_Employee=true")
      .then((response) => response.json())
      .then((data) => {
        const dropdown = document.getElementById(
          "quotationDetails_EmployeeID1"
        );
        dropdown.innerHTML = "<sl-option value=''>Select Employee - NONE</sl-option>";
  
        data.forEach((appointment) => {
          const option = document.createElement("sl-option");
          option.value = appointment.id;
          option.textContent = `${appointment.id} - ${appointment.name}`;
          dropdown.appendChild(option);
        });
      })
      .catch((error) => console.error("Error fetching appointments:", error));
  
      //Employee ID 2 - Done
      fetch("/Coolant/source-code/Controller/quotationController.php?fetch_Employee=true")
      .then((response) => response.json())
      .then((data) => {
        const dropdown = document.getElementById(
          "quotationDetails_EmployeeID2"
        );
        dropdown.innerHTML = "<sl-option value=''>Select Employee - NONE</sl-option>";
  
        data.forEach((appointment) => {
          const option = document.createElement("sl-option");
          option.value = appointment.id;
          option.textContent = `${appointment.id} - ${appointment.name}`;
          dropdown.appendChild(option);
        });
      })
      .catch((error) => console.error("Error fetching appointments:", error));
  
      //Employee ID 3 - Done
      fetch("/Coolant/source-code/Controller/quotationController.php?fetch_Employee=true")
      .then((response) => response.json())
      .then((data) => {
        const dropdown = document.getElementById(
          "quotationDetails_EmployeeID3"
        );
        dropdown.innerHTML = "<sl-option value=''>Select Employee - NONE</sl-option>";
  
        data.forEach((appointment) => {
          const option = document.createElement("sl-option");
          option.value = appointment.id;
          option.textContent = `${appointment.id} - ${appointment.name}`;
          dropdown.appendChild(option);
        });
      })
      .catch((error) => console.error("Error fetching appointments:", error));

      const submitButton = document.getElementById("generateQoutation");

      submitButton.addEventListener("click", async function () {
        submitButton.setAttribute("loading", true);

        let appointmentID = document.getElementById("quotationDetails_AppointmentID")?.value; // Important
        let employeeID1 = document.getElementById("quotationDetails_EmployeeID1")?.value; // Important
        let employeeID2 = document.getElementById("quotationDetails_EmployeeID2")?.value; // Important
        let employeeID3 = document.getElementById("quotationDetails_EmployeeID3")?.value; // Important
        let totalAmount =document.getElementById("grandTotalInput")?.innerText || "0"; //Auto Created
  
        // Document Header
        let dHeader = {
          companyName: document.getElementById("qoutationHeader_CompanyName")?.value,
          companyAddress: document.getElementById("qoutationHeader_CompanyAddress")?.value,
          companyNumber: document.getElementById("qoutationHeader_CompanyNumber")?.value,
          companyEmail: document.getElementById("qoutationHeader_CompanyEmail")?.value,
        };
  
        // Document Body Information
        let dBodyInfo = {
          quotationDate: document.getElementById("qoutationBody_Date")?.value,
          customerName: document.getElementById("qoutationBody_CustomerName")?.value,
          customerLocation: document.getElementById("qoutationBody_Location")?.value,
          customerDetails: document.getElementById("qoutationBody_Details")?.value,
          tableTotalAmmount:document.getElementById("grandTotalInput")?.innerText || "0",
        };
  
        // Document Footer Information
        let dFooterInfo = {
          details1: document.getElementById("qoutationFooter_Details1")?.value,
          details2: document.getElementById("qoutationFooter_Details2")?.value,
          details3: document.getElementById("qoutationFooter_Details3")?.value,
          details4: document.getElementById("qoutationFooter_Details4")?.value,
        };
  
        // Document Technician Information
        let dTechnicianInfo = {
          namePreparer: document.getElementById("qoutationFooter_TechnicianNamePreparer")?.value,
          positionPreparer: document.getElementById("qoutationFooter_TechnicianPositionPreparer")?.value,
          nameManager: document.getElementById("qoutationFooter_TechnicianNameManager")?.value,
          positionManager: document.getElementById("qoutationFooter_TechnicianPositionManager")?.value,
        };
  
        if (!appointmentID) {
          submitButton.removeAttribute("loading");
          submitButton.setAttribute("variant", "warning");
          return;
        }
  
        if (!employeeID1 && !employeeID2 && !employeeID3) {
          submitButton.removeAttribute("loading");
          submitButton.setAttribute("variant", "warning");
          return;
        }
  
        let formData = {
          appointmentID: appointmentID,
          employees: [employeeID1, employeeID2, employeeID3].filter(Boolean),
          totalAmount: totalAmount,
          status: "Working",
          action: "quotationDATA",
          document: {
            header: dHeader,
            body: dBodyInfo,
            footer: dFooterInfo,
            technicianInfo: dTechnicianInfo,
          },
        };
  
        this.disabled = true; // Disable button while processing
        try {
          await sendFormData(formData);
        } finally {
          this.disabled = false; // Ensure button is re-enabled even if request fails
        }
      });
  });
  
  // Send form data and handle response
  async function sendFormData(formData) {
    try {
      const response = await fetch("quotation.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData),
      });
  
      const data = await response.text();
      console.log(formData);
  
      if (data.includes("success")) {
        submitButton.removeAttribute("loading");
        submitButton.setAttribute("variant", "success");
        clearAllInputs();
      }
    } catch (error) {
      console.error("Error fetching data:", error);
      submitButton.removeAttribute("loading");
      submitButton.setAttribute("variant", "success");
    }
  }
  
  // Clear all input fields
  function clearAllInputs() {
    document.getElementById("quotationDetails_AppointmentID").value == "";
    document.getElementById("quotationDetails_EmployeeID1").value == "";
    document.getElementById("quotationDetails_EmployeeID2").value == "";
    document.getElementById("quotationDetails_EmployeeID3").value == "";
    document.getElementById("qoutationHeader_CompanyName").value == "";
    document.getElementById("qoutationHeader_CompanyAddress").value == "";
    document.getElementById("qoutationHeader_CompanyNumber").value == "";
    document.getElementById("qoutationHeader_CompanyEmail").value == "";
    document.getElementById("qoutationBody_Date").value == "";
    document.getElementById("qoutationBody_CustomerName").value == "";
    document.getElementById("qoutationBody_Location").value == "";
    document.getElementById("qoutationBody_Details").value == "";
    document.getElementById("qoutationFooter_Details1").value == "";
    document.getElementById("qoutationFooter_Details2").value == "";
    document.getElementById("qoutationFooter_Details3").value == "";
    document.getElementById("qoutationFooter_Details4").value == "";
    document.getElementById("qoutationFooter_TechnicianNamePreparer").value == "";
    document.getElementById("qoutationFooter_TechnicianPositionPreparer").value == "";
    document.getElementById("qoutationFooter_TechnicianNameManager").value == "";
    document.getElementById("qoutationFooter_TechnicianPositionManager").value == "";
  }
  