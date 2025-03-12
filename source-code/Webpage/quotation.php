<?php
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
define('BASE_URL_STYLE', '/Coolant/source-code');

include PROJECT_ROOT . "/Controller/quotationController.php";
include PROJECT_ROOT . "/Controller/dataRetrieval.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/quotation.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Quotation</title>
</head>

<body>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/quotation/quotationFunctions.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/quotation/quotationCancel.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/quotation/quotationTableFunctions.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/quotation/quotationLoadCustomer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <div class="content">
        <div class="topNavigationBar">
            <div class="topNavigationBar1" style="width: 100%; display: flex; align-items: center;">
                <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
                <sl-breadcrumb class="topNavbar">
                    <sl-breadcrumb-item>
                        <sl-icon slot="prefix" name="layout-text-sidebar-reverse"></sl-icon>
                        <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Quotation</label>
                    </sl-breadcrumb-item>
                </sl-breadcrumb>
            </div>
        </div>
        <nav class="sl-theme-dark">
            <div style="width: 100vw; display: flex; justify-content: center;">
                <div style="display: flex; flex-direction: row; justify-content: flex-start; 
                align-items: flex-end; width: 85%;">
                    <div
                        style="width: 100%; margin-right: 0.5%; padding: 10px; 
                        border: solid var(--sl-input-border-width) var(--sl-input-border-color); border-radius: 10px; max-height: 500px; overflow-y: auto;">
                        <sl-tab-group placement="start">
                            <sl-tab slot="nav" panel="employeeSelection">Step 1 - Select Employee</sl-tab>
                            <sl-tab slot="nav" panel="documentHeader">Step 2 - Docs. Header </sl-tab>
                            <sl-tab slot="nav" panel="documentInformation">Step 3 - Docs. Info.</sl-tab>
                            <sl-tab slot="nav" panel="documentTable">Step 4 - Quota. Table</sl-tab>
                            <sl-tab slot="nav" panel="documentInformationFooter">Step 5 - Docs. Footer</sl-tab>
                            <sl-tab slot="nav" panel="documentPreparerInformation">Step 6 - Preparer Info.</sl-tab>
                            <sl-tab slot="nav" panel="submitTheData">Output / Load</sl-tab>
                            <sl-tab slot="nav" panel="Cancel">Cancel</sl-tab>

                            <sl-tab-panel name="employeeSelection">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Select
                                    Employee</label>
                                <sl-select id="quotationDetails_AppointmentID" class="column" label="Select Appointment"
                                    size="small">
                                    <sl-option value="">Loading..</sl-option>
                                </sl-select>
                                <sl-select id="quotationDetails_EmployeeID1" class="column" label="Technician 1"
                                    size="small">
                                    <sl-option value="">Loading..</sl-option>
                                </sl-select>
                                <sl-select id="quotationDetails_EmployeeID2" class="column" label="Technician 2"
                                    size="small">
                                    <sl-option value="">Loading..</sl-option>
                                </sl-select>
                                <sl-select id="quotationDetails_EmployeeID3" class="column" label="Technician 3"
                                    size="small">
                                    <sl-option value="">Loading..</sl-option>
                                </sl-select>
                            </sl-tab-panel>


                            <sl-tab-panel name="documentHeader">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Documents
                                    Header</label>
                                <sl-input id="qoutationHeader_CompanyName" class="column" label="Company Name"
                                    placeholder="Ex. Aircool JV" size="small"></sl-input>
                                <sl-input id="qoutationHeader_CompanyAddress" class="column" label="Company Address"
                                    placeholder="Ex. Santa Rosa St." size="small"></sl-input>

                                <sl-input id="qoutationHeader_CompanyNumber" class="column" label="Contact Number"
                                    placeholder="Ex. (123) 456-789" size="small"></sl-input>
                                <sl-input id="qoutationHeader_CompanyEmail" class="column" label="Email Address"
                                    placeholder="Ex. example@email.com" size="small"></sl-input>
                            </sl-tab-panel>


                            <sl-tab-panel name="documentInformation"><label
                                    style="font-weight: 600; font-size: 16px; color: #27BAFD;">Documents
                                    Information</label>
                                <sl-input id="qoutationBody_CustomerName" class="column" label="Customer Name"
                                    size="small"></sl-input>
                                <sl-input id="qoutationBody_Location" class="column" label="Customer Address"
                                    size="small"></sl-input>
                                <sl-input config-id="date" id="qoutationBody_Date" class="column" label="Date"
                                    placeholder="Ex. 20XX-12-25" size="small"></sl-input>
                                <sl-input id="qoutationBody_Details" class="column" label="Quotation for"
                                    placeholder="Ex. Installation & Repair" size="small"></sl-input>
                                <br>
                                <div style="display: flex; align-items: flex-start; justify-content: flex-start;
                                flex-direction: column; flex-wrap: nowrap;">
                                    <label>Load Customer Information</label>
                                    <br>
                                    <sl-button id="loadCustomerInformation" variant="primary"
                                        size="small">Load</sl-button>
                                </div>
                            </sl-tab-panel>


                            <sl-tab-panel name="documentInformationFooter">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Documents
                                    Information Footer</label>
                                <sl-input id="qoutationFooter_Details1" class="column" label="Warranty Details"
                                    placeholder="Ex. Repair Warranty" size="small"></sl-input>
                                <sl-input id="qoutationFooter_Details2" class="column" label="Warranty Duration"
                                    placeholder="Ex. 1 Year upon Completion" size="small"></sl-input>
                                <sl-input id="qoutationFooter_Details3" class="column" label="Quotation Message"
                                    placeholder="Ex. Thank you for letting us submit out quotation."
                                    size="small"></sl-input>
                                <sl-input id="qoutationFooter_Details4" class="column" label="Quotation Message"
                                    placeholder="Ex. If you have any concern regarding to this matter, give us a call!"
                                    size="small"></sl-input>
                            </sl-tab-panel>


                            <sl-tab-panel name="documentPreparerInformation">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Document
                                    Preparer Information</label>
                                <sl-input id="qoutationFooter_TechnicianNamePreparer" class="column"
                                    label="Preparer Name" placeholder="Ex. John Doe" size="small"></sl-input>
                                <sl-input id="qoutationFooter_TechnicianPositionPreparer" class="column"
                                    label="Preparer Position" placeholder="Ex. Technician" size="small"></sl-input>
                                <sl-input id="qoutationFooter_TechnicianNameManager" class="column"
                                    label="Preparer Contact Number" placeholder="Ex. (123) 456-789"
                                    size="small"></sl-input>
                                <sl-input id="qoutationFooter_TechnicianPositionManager" class="column"
                                    label="Preparer Email Address" placeholder="Ex. example@email.com"
                                    size="small"></sl-input>
                            </sl-tab-panel>


                            <sl-tab-panel name="submitTheData">
                                <br>
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">
                                        Output</label>
                                <sl-button id="generateQoutation" variant="primary" size="small">Generate</sl-button>
                                <sl-button variant="primary" size="small"
                                    href="<?= BASE_URL_STYLE ?>/PrintablePage/print-quotation.php">
                                    Visit Print</sl-button>

                                <div style="width: 300px;display: flex;padding-top: 20px;
                                flex-direction: column;align-items: stretch;flex-wrap: nowrap;">
                                    <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">
                                        Load Created Settings</label>
                                    <sl-input id="settingName" class="column" label="Settings Name"
                                        placeholder="Ex. AirCool Company Settings" size="small"></sl-input>
                                    <sl-select id="settingsLoad" class="column" label="Select Settings to Load"
                                        size="small">
                                        <sl-option value="">Loading..</sl-option>
                                    </sl-select>
                                    <br>
                                    <div>
                                        <sl-button id="loadSettings" variant="success" size="small"
                                            outline>Load</sl-button>
                                        <sl-button id="updateSettings" variant="warning" size="small"
                                            outline>Update</sl-button>
                                        <sl-button id="deleteSettings" variant="danger" size="small"
                                            outline>Delete</sl-button>
                                    </div>
                                </div>

                            </sl-tab-panel>

                            <sl-tab-panel name="Cancel">
                                <br>
                                <sl-select id="cancelAppointment_ID" label="Appointment"
                                    help-text="Select the Appointment you want to Cancel" size="small">
                                    <sl-option value="">Loading...</sl-option>
                                </sl-select>
                                <br>
                                <sl-button id="submitCancelAppointment" variant="primary"
                                    size="small">Cancel</sl-button>
                            </sl-tab-panel>

                            <sl-tab-panel name="documentTable">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Quotation
                                    Table</label>
                                <br>
                                <br>
                                <div class="titleContent">
                                    <table id="quotationTable" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Description</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Total Amount</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr class="total-row">
                                                <td colspan="4">Grand Total</td>
                                                <td id="grandTotalInput">0.00</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    <div class="column">
                                        <sl-button variant="primary" size="small" class="submitButton"
                                            onclick="addRow()">Add Row</button>
                                    </div>
                                </div>
                            </sl-tab-panel>
                        </sl-tab-group>
                    </div>
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