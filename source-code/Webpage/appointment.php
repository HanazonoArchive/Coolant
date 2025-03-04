<?php
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
define('BASE_URL_STYLE', '/Coolant/source-code');

include PROJECT_ROOT . "/Controller/appointmentController.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/appointment.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Appointment</title>
</head>

<body>
    <div class="content">
        <script src="<?= BASE_URL_STYLE ?>/JavaScript/appointment/appointmentFunctions.js"></script>
        <script src="<?= BASE_URL_STYLE ?>/JavaScript/appointment/appointmentAdd.js"></script>
        <script src="<?= BASE_URL_STYLE ?>/JavaScript/appointment/appointmentDelete.js"></script>
        <script src="<?= BASE_URL_STYLE ?>/JavaScript/appointment/appointmentUpdate.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <div class="topNavigationBar">
            <div class="topNavigationBar1" style="width: 100%; display: flex; align-items: center;">
                <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
                <sl-breadcrumb class="topNavbar">
                    <sl-breadcrumb-item>
                        <sl-icon slot="prefix" name="clipboard-check"></sl-icon>
                        <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Appoinment</label>
                    </sl-breadcrumb-item>
                </sl-breadcrumb>
            </div>
        </div>
        <nav class="sl-theme-dark">
            <div style="width: 100vw; display: flex; justify-content: center;">
                <div style="display: flex; flex-direction: row; justify-content: flex-start; align-items: flex-end; 
                border: solid var(--sl-input-border-width) var(--sl-input-border-color); width: 85%; padding: 10px;
                border-radius: 10px;">
                    <div class="column">
                        <sl-dialog id="createAppointment_Dialog" label="Create Appointment" class="dialog-deny-close">
                            <sl-input id="appointmentCreateCustomer_Name" label="Name" help-text="What is the Customer Name?" size="small"></sl-input>
                            <br>
                            <sl-input id="appointmentCreateCustomer_ContactNumber" label="Contact Number" help-text="What is the Customer Contact Number?" size="small"></sl-input>
                            <br>
                            <sl-input id="appointmentCreateCustomer_Address" label="Address" help-text="Where is this Appointment Located?" size="small"></sl-input>
                            <br>
                            <sl-input id="appointmentCreate_Date" config-id="date" label="Date" help-text="What Date this Appointment Should be Placed?" size="small"></sl-input>
                            <br>
                            <sl-select id="appointmentCreate_Category" label="Category" help-text="What Appointment Category is this?" size="small">
                                <sl-option value="Installation">Installation</sl-option>
                                <sl-option value="Repair">Repair</sl-option>
                                <sl-option value="Maintenance">Maintenance</sl-option>
                            </sl-select>
                            <br>
                            <sl-select id="appointmentCreate_Priority" label="Priority" help-text="How urgent is this Appointment?" size="small">
                                <sl-option value="Low">Low</sl-option>
                                <sl-option value="Medium">Medium</sl-option>
                                <sl-option value="High">High</sl-option>
                                <sl-option value="Urgent">Urgent</sl-option>
                            </sl-select>
                            <sl-divider></sl-divider>
                            <sl-button id="submitCreateAppointment" variant="primary">Submit</sl-button>

                            <sl-button id="createAppointment_Close" slot="footer" variant="primary">Close</sl-button>
                        </sl-dialog>

                        <sl-button id="createAppointment_Open">Create Appointment</sl-button>
                    </div>
                    <div class="column">
                        <sl-dialog id="updateAppointment_Dialog" label="Update Appointment" class="dialog-deny-close">
                            <sl-select id="appointmentUpdate_ID" label="Appointment" help-text="Select the Appointment you want Update" size="small">
                                <sl-option value="">Loading...</sl-option>
                            </sl-select>
                            <br>
                            <sl-select id="appointmentUpdate_Category" label="Category" help-text="Update the Appointment Category" size="small">
                                <sl-option value="Installation">Installation</sl-option>
                                <sl-option value="Repair">Repair</sl-option>
                                <sl-option value="Maintenance">Maintenance</sl-option>
                            </sl-select>
                            <br>
                            <sl-select id="appointmentUpdate_Priority" label="Priority" help-text="Update the Appointment Priority" size="small">
                                <sl-option value="Low">Low</sl-option>
                                <sl-option value="Medium">Medium</sl-option>
                                <sl-option value="High">High</sl-option>
                                <sl-option value="Urgent">Urgent</sl-option>
                            </sl-select>
                            <br>
                            <sl-input id="appointmentUpdate_Date" config-id="date" label="Date" help-text="Update the Appointment Date" size="small"></sl-input>
                            <sl-divider></sl-divider>
                            <sl-button id="submitUpdateAppointment" variant="primary">Submit</sl-button>

                            <sl-button id="updateAppointment_Close" slot="footer" variant="primary">Close</sl-button>
                        </sl-dialog>

                        <sl-button id="updateAppointment_Open">Update Appointment</sl-button>
                    </div>
                    <div class="column">
                        <sl-dialog id="deleteAppointment_Dialog" label="Delete Appointment" class="dialog-deny-close">
                            <sl-select id="appointmentDelete_AppointmentID" label="Appointment" help-text="Select the Appointment you want Delete" size="small">
                                <sl-option value="">Loading...</sl-option>
                            </sl-select>
                            <br>
                            <sl-input id="appointmentDelete_Confirmation" label="Date" help-text='Type "DELETE" to confirm Deletion' size="small"></sl-input>
                            <sl-divider></sl-divider>
                            <sl-button id="submitDeleteAppointment" variant="primary">Submit</sl-button>

                            <sl-button id="deleteAppointment_Close" slot="footer" variant="primary">Close</sl-button>
                        </sl-dialog>

                        <sl-button id="deleteAppointment_Open">Delete Appointment</sl-button>
                    </div>
                </div>
            </div>
            <div class="customerTable">
                <div class="customerTable1">
                    <?= $appointmentManager->fetchAppointments(); ?>
                </div>
            </div>
        </nav>
    </div>
    <script>
        document.querySelectorAll('[config-id="date"]').forEach((datePicker) => {
            flatpickr(datePicker, {
                minDate: "today"
            });
        });
    </script>
</body>

</html>