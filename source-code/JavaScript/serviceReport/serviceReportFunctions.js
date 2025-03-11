document.addEventListener("DOMContentLoaded", () => {
    console.log("serviceReportFunctions script loaded");
  
    const submitButton = document.getElementById("generateServiceReport");
  
    const fetchData = (url, dropdownId, defaultText) => {
      fetch(url)
        .then((response) => response.json())
        .then((data) => {
          const dropdown = document.getElementById(dropdownId);
          dropdown.innerHTML = `<sl-option value=''>${defaultText}</sl-option>`;
          data.forEach((item) => {
            const option = document.createElement("sl-option");
            option.value = item.id; 
            option.textContent = `${item.id} - ${item.name}`;
            dropdown.appendChild(option);
          });
        })
        .catch((error) => console.error(`Error fetching data from ${url}:`, error));
    };
  
    fetchData("/Coolant/source-code/Controller/serviceReportController.php?fetch_appointments=true", "serviceReportDetails_AppointmentID", "Select Appointment");

    submitButton.addEventListener("click", async function () {
      submitButton.setAttribute("loading", true);
  
      let appointmentID = document.getElementById("serviceReportDetails_AppointmentID")?.value;
      let totalAmount = document.getElementById("grandTotalInput")?.innerText || "0";
  
      let dHeader = {
        companyName: document.getElementById("serviceReportHeader_CompanyName")?.value,
        companyAddress: document.getElementById("serviceReportHeader_CompanyAddress")?.value,
        companyNumber: document.getElementById("serviceReportHeader_CompanyNumber")?.value,
        companyEmail: document.getElementById("serviceReportHeader_CompanyEmail")?.value,
      };
  
      let dBodyInfo = {
        serviceReportDate: document.getElementById("serviceReportBody_Date")?.value,
        customerName: document.getElementById("serviceReportBody_CustomerName")?.value,
        customerLocation: document.getElementById("serviceReportBody_Location")?.value,
        tableTotalAmount:document.getElementById("grandTotalInput")?.innerText || "0",
      };
  
      let dFooterInfo = {
        complaint: document.getElementById("serviceReportFooter_Complaint")?.value,
        diagnosed: document.getElementById("serviceReportFooter_Diagnosed")?.value,
        activityPerformed: document.getElementById("serviceReportFooter_ActivityPerformed")?.value,
        recommendation: document.getElementById("serviceReportFooter_Recommendation")?.value,
      };
  
      let dTechnicianInfo = {
        preparerName: document.getElementById("serviceReportFooter_PreparerName")?.value,
        preparerPosition: document.getElementById("serviceReportFooter_PreparerPosition")?.value,
        managerName: document.getElementById("serviceReportFooter_ManagerName")?.value,
      };
  
      if (!appointmentID) {
        submitButton.removeAttribute("loading");
        submitButton.setAttribute("variant", "warning");
        return;
      }
  
      let formData = {
        appointmentID,
        totalAmount,
        status: "Completed",
        action: "serviceReportDATA",
        document: { header: dHeader, body: dBodyInfo, footer: dFooterInfo, technicianInfo: dTechnicianInfo },
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
        setTimeout(() => {
          window.location.reload();
        }, 3000);
        clearAllInputs();
      }
    } catch (error) {
      console.error("Error fetching data:", error);
      submitButton.removeAttribute("loading");
      submitButton.setAttribute("variant", "warning");
    }
  }
  
  function clearAllInputs() {
    document.getElementById("serviceReportDetails_AppointmentID").value = "";
    document.getElementById("serviceReportHeader_CompanyName").value = "";
    document.getElementById("serviceReportHeader_CompanyAddress").value = "";
    document.getElementById("serviceReportHeader_CompanyNumber").value = "";
    document.getElementById("serviceReportHeader_CompanyEmail").value = "";
    document.getElementById("serviceReportBody_Date").value = "";
    document.getElementById("serviceReportBody_CustomerName").value = "";
    document.getElementById("serviceReportBody_Location").value = "";
    document.getElementById("serviceReportFooter_Complaint").value = "";
    document.getElementById("serviceReportFooter_Diagnosed").value = "";
    document.getElementById("serviceReportFooter_ActivityPerformed").value = "";
    document.getElementById("serviceReportFooter_Recommendation").value = "";
    document.getElementById("serviceReportFooter_PreparerName").value = "";
    document.getElementById("serviceReportFooter_PreparerPosition").value = "";
    document.getElementById("serviceReportFooter_ManagerName").value = "";
  }
  