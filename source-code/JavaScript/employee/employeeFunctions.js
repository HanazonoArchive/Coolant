document.addEventListener("DOMContentLoaded", () => {
  console.log("employeeFunctions.js loaded");

  const createDialog = document.getElementById("createEmployee_Dialog");
  const createOpenButton = document.getElementById("createEmployee_Open");
  const createCloseButton = document.getElementById("createEmployee_Close");

  createOpenButton.addEventListener("click", () => createDialog.show());
  createCloseButton.addEventListener("click", () => createDialog.hide());

  // Prevent the dialog from closing when the user clicks on the overlay
  createDialog.addEventListener("sl-request-close", (event) => {
    if (event.detail.source === "overlay") {
      event.preventDefault();
    }
  });

  const updateDialog = document.getElementById("updateEmployee_Dialog");
  const updateOpenButton = document.getElementById("updateEmployee_Open");
  const updateCloseButton = document.getElementById("updateEmployee_Close");

  updateOpenButton.addEventListener("click", () => updateDialog.show());
  updateCloseButton.addEventListener("click", () => updateDialog.hide());

  // Prevent the dialog from closing when the user clicks on the overlay
  updateDialog.addEventListener("sl-request-close", (event) => {
    if (event.detail.source === "overlay") {
      event.preventDefault();
    }
  });

  const deleteDialog = document.getElementById("deleteEmployee_Dialog");
  const deleteOpenButton = document.getElementById("deleteEmployee_Open");
  const deleteCloseButton = document.getElementById("deleteEmployee_Close");

  deleteOpenButton.addEventListener("click", () => deleteDialog.show());
  deleteCloseButton.addEventListener("click", () => deleteDialog.hide());

  // Prevent the dialog from closing when the user clicks on the overlay
  deleteDialog.addEventListener("sl-request-close", (event) => {
    if (event.detail.source === "overlay") {
      event.preventDefault();
    }
  });

  const payDialog = document.getElementById("payEmployee_Dialog");
  const payOpenButton = document.getElementById("payEmployee_Open");
  const payCloseButton = document.getElementById("payEmployee_Close");

  payOpenButton.addEventListener("click", () => payDialog.show());
  payCloseButton.addEventListener("click", () => payDialog.hide());

  // Prevent the dialog from closing when the user clicks on the overlay
  payDialog.addEventListener("sl-request-close", (event) => {
    if (event.detail.source === "overlay") {
      event.preventDefault();
    }
  });
});
