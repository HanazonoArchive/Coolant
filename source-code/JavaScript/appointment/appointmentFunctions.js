document.addEventListener("DOMContentLoaded", () => {
  console.log("appointmentFunctions.js loaded");

  const createDialog = document.getElementById("createAppointment_Dialog");
  const createOpenButton = document.getElementById("createAppointment_Open");
  const createCloseButton = document.getElementById("createAppointment_Close");

  createOpenButton.addEventListener("click", () => createDialog.show());
  createCloseButton.addEventListener("click", () => createDialog.hide());

  // Prevent the dialog from closing when the user clicks on the overlay
  createDialog.addEventListener("sl-request-close", (event) => {
    if (event.detail.source === "overlay") {
      event.preventDefault();
    }
  });

  const updateDialog = document.getElementById("updateAppointment_Dialog");
  const updateOpenButton = document.getElementById("updateAppointment_Open");
  const updateCloseButton = document.getElementById("updateAppointment_Close");

  updateOpenButton.addEventListener("click", () => updateDialog.show());
  updateCloseButton.addEventListener("click", () => updateDialog.hide());

  // Prevent the dialog from closing when the user clicks on the overlay
  updateDialog.addEventListener("sl-request-close", (event) => {
    if (event.detail.source === "overlay") {
      event.preventDefault();
    }
  });

  const deleteDialog = document.getElementById("deleteAppointment_Dialog");
  const deleteOpenButton = document.getElementById("deleteAppointment_Open");
  const deleteCloseButton = document.getElementById("deleteAppointment_Close");

  deleteOpenButton.addEventListener("click", () => deleteDialog.show());
  deleteCloseButton.addEventListener("click", () => deleteDialog.hide());

  // Prevent the dialog from closing when the user clicks on the overlay
  deleteDialog.addEventListener("sl-request-close", (event) => {
    if (event.detail.source === "overlay") {
      event.preventDefault();
    }
  });
});
