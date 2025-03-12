document.addEventListener("DOMContentLoaded", () => {
  console.log("quotationFunctions script loaded");

  const submitButton = document.getElementById("generateQoutation");

  const fetchDataAppointment = (url, dropdownId, defaultText) => {
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

  const fetchDataEmployee = (url, dropdownId, defaultText) => {
    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        const dropdown = document.getElementById(dropdownId);
        dropdown.innerHTML = `<sl-option value=''>${defaultText}</sl-option>`;
        data.forEach((item) => {
          const option = document.createElement("sl-option");
          option.value = item.id;
          option.textContent = `${item.id} - ${item.name} (${item.role})`;
          dropdown.appendChild(option);
        });
      })
      .catch((error) => console.error(`Error fetching data from ${url}:`, error));
  };

  fetchDataAppointment("/Coolant/source-code/Controller/quotationController.php?fetch_appointments=true", "quotationDetails_AppointmentID", "Select Appointment");
  fetchDataEmployee("/Coolant/source-code/Controller/quotationController.php?fetch_Employee=true", "quotationDetails_EmployeeID1", "Select Employee - NONE");
  fetchDataEmployee("/Coolant/source-code/Controller/quotationController.php?fetch_Employee=true", "quotationDetails_EmployeeID2", "Select Employee - NONE");
  fetchDataEmployee("/Coolant/source-code/Controller/quotationController.php?fetch_Employee=true", "quotationDetails_EmployeeID3", "Select Employee - NONE");

  submitButton.addEventListener("click", async function () {
    submitButton.setAttribute("loading", true);

    let appointmentID = document.getElementById("quotationDetails_AppointmentID")?.value;
    let employeeID1 = document.getElementById("quotationDetails_EmployeeID1")?.value;
    let employeeID2 = document.getElementById("quotationDetails_EmployeeID2")?.value;
    let employeeID3 = document.getElementById("quotationDetails_EmployeeID3")?.value;
    let totalAmount = document.getElementById("grandTotalInput")?.innerText || "0";

    let dHeader = {
      companyName: document.getElementById("qoutationHeader_CompanyName")?.value,
      companyAddress: document.getElementById("qoutationHeader_CompanyAddress")?.value,
      companyNumber: document.getElementById("qoutationHeader_CompanyNumber")?.value,
      companyEmail: document.getElementById("qoutationHeader_CompanyEmail")?.value,
    };

    let dBodyInfo = {
      quotationDate: document.getElementById("qoutationBody_Date")?.value,
      customerName: document.getElementById("qoutationBody_CustomerName")?.value,
      customerLocation: document.getElementById("qoutationBody_Location")?.value,
      customerDetails: document.getElementById("qoutationBody_Details")?.value,
      tableTotalAmmount: totalAmount,
    };

    let dFooterInfo = {
      details1: document.getElementById("qoutationFooter_Details1")?.value,
      details2: document.getElementById("qoutationFooter_Details2")?.value,
      details3: document.getElementById("qoutationFooter_Details3")?.value,
      details4: document.getElementById("qoutationFooter_Details4")?.value,
    };

    let dTechnicianInfo = {
      namePreparer: document.getElementById("qoutationFooter_TechnicianNamePreparer")?.value,
      positionPreparer: document.getElementById("qoutationFooter_TechnicianPositionPreparer")?.value,
      nameManager: document.getElementById("qoutationFooter_TechnicianNameManager")?.value,
      positionManager: document.getElementById("qoutationFooter_TechnicianPositionManager")?.value,
    };

    if (!appointmentID || (!employeeID1 && !employeeID2 && !employeeID3)) {
      submitButton.removeAttribute("loading");
      submitButton.setAttribute("variant", "warning");
      return;
    }

    let formData = {
      appointmentID,
      employees: [employeeID1, employeeID2, employeeID3].filter(Boolean),
      totalAmount,
      status: "Working",
      action: "quotationDATA",
      document: { header: dHeader, body: dBodyInfo, footer: dFooterInfo, technicianInfo: dTechnicianInfo },
    };
    sendFormData(formData, submitButton);
  });
});

async function sendFormData(formData, submitButton) {
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
  document.getElementById("quotationDetails_AppointmentID").value = "";
  document.getElementById("quotationDetails_EmployeeID1").value = "";
  document.getElementById("quotationDetails_EmployeeID2").value = "";
  document.getElementById("quotationDetails_EmployeeID3").value = "";
  document.getElementById("qoutationHeader_CompanyName").value = "";
  document.getElementById("qoutationHeader_CompanyAddress").value = "";
  document.getElementById("qoutationHeader_CompanyNumber").value = "";
  document.getElementById("qoutationHeader_CompanyEmail").value = "";
  document.getElementById("qoutationBody_Date").value = "";
  document.getElementById("qoutationBody_CustomerName").value = "";
  document.getElementById("qoutationBody_Location").value = "";
  document.getElementById("qoutationBody_Details").value = "";
  document.getElementById("qoutationFooter_Details1").value = "";
  document.getElementById("qoutationFooter_Details2").value = "";
  document.getElementById("qoutationFooter_Details3").value = "";
  document.getElementById("qoutationFooter_Details4").value = "";
  document.getElementById("qoutationFooter_TechnicianNamePreparer").value = "";
  document.getElementById("qoutationFooter_TechnicianPositionPreparer").value = "";
  document.getElementById("qoutationFooter_TechnicianNameManager").value = "";
  document.getElementById("qoutationFooter_TechnicianPositionManager").value = "";
}
