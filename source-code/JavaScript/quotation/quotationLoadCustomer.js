document.addEventListener("DOMContentLoaded", function () {
  const appointmentID = document.getElementById("quotationDetails_AppointmentID");
  const customerName = document.getElementById("qoutationBody_CustomerName");
  const customerAddress = document.getElementById("qoutationBody_Location");
  const quotationDetails = document.getElementById("qoutationBody_Details");

  const loadButton = document.getElementById("loadCustomerInformation");

  loadButton.addEventListener("click", function () {
    const requestData = {
      action: "quotationDataLoader",
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
          quotationDetails.value = result.details || "";
        } else {
          console.error("Error retrieving data:", result.message);
        }
      } catch (error) {
        console.error("Error sending data:", error);
      }
    }
  });
});
