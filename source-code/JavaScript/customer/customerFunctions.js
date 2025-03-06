document.addEventListener("DOMContentLoaded", () => {
  console.log("customerFunctions.js loaded");

  const updateDialog = document.getElementById("updateCustomer_Dialog");
  const updateOpenButton = document.getElementById("updateCustomer_Open");
  const updateCloseButton = document.getElementById("updateCustomer_Close");

  updateOpenButton.addEventListener("click", () => updateDialog.show());
  updateCloseButton.addEventListener("click", () => updateDialog.hide());

  // Prevent the dialog from closing when the user clicks on the overlay
  updateDialog.addEventListener("sl-request-close", (event) => {
    if (event.detail.source === "overlay") {
      event.preventDefault();
    }
  });

  const deleteDialog = document.getElementById("deleteCustomer_Dialog");
  const deleteOpenButton = document.getElementById("deleteCustomer_Open");
  const deleteCloseButton = document.getElementById("deleteCustomer_Close");

  deleteOpenButton.addEventListener("click", () => deleteDialog.show());
  deleteCloseButton.addEventListener("click", () => deleteDialog.hide());

  // Prevent the dialog from closing when the user clicks on the overlay
  deleteDialog.addEventListener("sl-request-close", (event) => {
    if (event.detail.source === "overlay") {
      event.preventDefault();
    }
  });

  const createDialogF = document.getElementById("createFeedback_Dialog");
  const createOpenButtonF = document.getElementById("createFeedback_Open");
  const createCloseButtonF = document.getElementById("createFeedback_Close");

  createOpenButtonF.addEventListener("click", () => createDialogF.show());
  createCloseButtonF.addEventListener("click", () => createDialogF.hide());

  // Prevent the dialog from closing when the user clicks on the overlay
  createDialogF.addEventListener("sl-request-close", (event) => {
    if (event.detail.source === "overlay") {
      event.preventDefault();
    }
  });

  const updateDialogF = document.getElementById("updateFeedback_Dialog");
  const updateOpenButtonF = document.getElementById("updateFeedback_Open");
  const updateCloseButtonF = document.getElementById("updateFeedback_Close");

  updateOpenButtonF.addEventListener("click", () => updateDialogF.show());
  updateCloseButtonF.addEventListener("click", () => updateDialogF.hide());

  // Prevent the dialog from closing when the user clicks on the overlay
  updateDialogF.addEventListener("sl-request-close", (event) => {
    if (event.detail.source === "overlay") {
      event.preventDefault();
    }
  });

  const deleteDialogF = document.getElementById("deleteFeedback_Dialog");
  const deleteOpenButtonF = document.getElementById("deleteFeedback_Open");
  const deleteCloseButtonF = document.getElementById("deleteFeedback_Close");

  deleteOpenButtonF.addEventListener("click", () => deleteDialogF.show());
  deleteCloseButtonF.addEventListener("click", () => deleteDialogF.hide());

  // Prevent the dialog from closing when the user clicks on the overlay
  deleteDialogF.addEventListener("sl-request-close", (event) => {
    if (event.detail.source === "overlay") {
      event.preventDefault();
    }
  });
});
