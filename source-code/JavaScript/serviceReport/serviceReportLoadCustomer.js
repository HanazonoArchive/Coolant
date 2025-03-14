document.addEventListener("DOMContentLoaded", function () {
  const appointmentID = document.getElementById("serviceReportDetails_AppointmentID");
  const customerName = document.getElementById("serviceReportBody_CustomerName");
  const customerAddress = document.getElementById("serviceReportBody_Location");
  const footerCustomerName = document.getElementById("serviceReportFooter_ManagerName");

  const loadButton = document.getElementById("loadCustomerInformation");

  loadButton.addEventListener("click", function () {
    loadButton.setAttribute("loading", true);
    const requestData = {
      action: "serviceReportLoadCustomer",
      appointmentID: appointmentID.value,
    };

    sendRequestData(requestData);

    async function sendRequestData(data) {
      try {
        const response = await fetch(window.location.pathname, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(data),
        });

        const result = await response.json();

        console.log("Response Data:", result);

        if (result.status === "success") {
          console.log("Data received successfully!");
          customerName.value = result.name || "";
          customerAddress.value = result.address || "";
          footerCustomerName.value = result.name || "";
          loadButton.removeAttribute("loading");
          loadButton.setAttribute("variant", "success");
        } else {
          console.error("Error retrieving data:", result.message);
          loadButton.removeAttribute("loading");
          loadButton.setAttribute("variant", "warning");
        }
      } catch (error) {
        console.error("Error sending data:", error);
        loadButton.removeAttribute("loading");
        loadButton.setAttribute("variant", "danger");
      }
    }
  });
});
