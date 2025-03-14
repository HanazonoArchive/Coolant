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
                            <sl-button id="submitCreateAppointment" variant="primary" outline>Submit</sl-button>
                            <br>
                            <br>
                            <sl-details summary="Help?">
                                <p style="font-weight: 300; font-size: 16px;"> Type in Customer <sl-tag size="small">Name</sl-tag>, <sl-tag size="small">Contact Number</sl-tag>, 
                                <sl-tag size="small">Address</sl-tag>, <sl-tag size="small">Date</sl-tag>, <sl-tag size="small">Category</sl-tag> and <sl-tag size="small">Priority</sl-tag> of the Appointment. <br> <br>

                                We have <sl-tag size="small">3</sl-tag> Category you choose from <sl-tag size="small">Installation</sl-tag>, <sl-tag size="small">Repair</sl-tag>, and <sl-tag size="small">Maintenance</sl-tag>. <br> <br>

                                Now for the <sl-tag size="small">Priority</sl-tag>, We have <sl-tag size="small" variant="success">Low</sl-tag>, <sl-tag size="small" variant="primary">Medium</sl-tag>, <sl-tag size="small" variant="warning">High</sl-tag>, and <sl-tag size="small" variant="danger">Urgent</sl-tag>. <br> <br>
                                </p>
                            </sl-details>

                            <sl-button id="createAppointment_Close" slot="footer" variant="primary">Close</sl-button>
                        </sl-dialog>
                        <sl-tooltip content="Yes... It's Create Appointment...">
                            <sl-button id="createAppointment_Open" variant="success" outline size="small">Create Appointment</sl-button>
                        </sl-tooltip>
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
                            <sl-button id="submitUpdateAppointment" variant="primary" outline>Submit</sl-button>
                            <br>
                            <br>
                            <sl-details summary="Help?">
                                <p style="font-weight: 300; font-size: 16px;"> Select <sl-tag size="small">Appointment ID</sl-tag> you want to Update. <br> <br>
                                <sl-icon name="exclamation-triangle" style="font-size: 16px; color: orange;"></sl-icon> Take Note that you can't Update an Appointment that is already <sl-tag size="small" variant="primary">Working</sl-tag> or <sl-tag size="small" variant="success">Completed</sl-tag>. <br> <br>
                                Only those are on <sl-tag size="small" variant="warning">Pending</sl-tag> status.</p>
                            </sl-details>

                            <sl-button id="updateAppointment_Close" slot="footer" variant="primary">Close</sl-button>
                        </sl-dialog>
                        <sl-tooltip content="Oh... It's Update Appointment...">
                            <sl-button id="updateAppointment_Open" variant="warning" outline size="small">Update Appointment</sl-button>
                        </sl-tooltip>
                    </div>
                    <div class="column">
                        <sl-dialog id="deleteAppointment_Dialog" label="Delete Appointment" class="dialog-deny-close">
                            <sl-select id="appointmentDelete_AppointmentID" label="Appointment" help-text="Select the Appointment you want Delete" size="small">
                                <sl-option value="">Loading...</sl-option>
                            </sl-select>
                            <br>
                            <sl-input id="appointmentDelete_Confirmation" label="Date" help-text='Type "DELETE" to confirm Deletion' size="small"></sl-input>
                            <sl-divider></sl-divider>
                            <sl-button id="submitDeleteAppointment" variant="primary" outline>Submit</sl-button>
                            <br>
                            <br>
                            <sl-details summary="Help?">
                                <p style="font-weight: 300; font-size: 16px;"> Select <sl-tag size="small">Appointment ID</sl-tag> you want to Delete. <br> <br>
                                <sl-icon name="exclamation-triangle" style="font-size: 16px; color: orange;"></sl-icon> Take Note that you can't Delete an Appointment that is already <sl-tag size="small" variant="primary">Working</sl-tag> or <sl-tag size="small" variant="success">Completed</sl-tag>. <br> <br>
                                Only those are on <sl-tag size="small" variant="warning">Pending</sl-tag> and <sl-tag size="small" variant="danger">Cancelled</sl-tag> status.</p>
                            </sl-details>
                            <sl-button id="deleteAppointment_Close" slot="footer" variant="primary">Close</sl-button>
                        </sl-dialog>
                        <sl-tooltip content="Oh... It's Delete Appointment...">
                            <sl-button id="deleteAppointment_Open" variant="danger" outline size="small">Delete Appointment</sl-button>
                        </sl-tooltip>
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