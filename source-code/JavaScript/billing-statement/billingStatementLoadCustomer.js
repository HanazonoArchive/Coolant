document.addEventListener("DOMContentLoaded", function () {
    const appointmentID = document.getElementById("billingDetails_AppointmentID");
    const customerName = document.getElementById("billingBody_CustomerName");
    const customerAddress = document.getElementById("billingBody_Location");
  
    const loadButton = document.getElementById("loadCustomerInformation");
  
    loadButton.addEventListener("click", function () {
      const requestData = {
        action: "billingStatementLoadCustomer",
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
          } else {
            console.error("Error retrieving data:", result.message);
          }
        } catch (error) {
          console.error("Error sending data:", error);
        }
      }
    });
  });
  