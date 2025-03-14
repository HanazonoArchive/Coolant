<?php
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
define('BASE_URL_STYLE', '/Coolant/source-code');

include PROJECT_ROOT . "/Controller/serviceReportController.php";
include PROJECT_ROOT . "/Controller/dataRetrieval.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/quotation.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Services Report</title>
</head>

<body>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/serviceReport/serviceReportFunctions.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/serviceReport/serviceReportTableFunctions.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/serviceReport/serviceReportSettings.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/serviceReport/serviceReportLoadCustomer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <div class="content">
        <div class="topNavigationBar">
            <div class="topNavigationBar1" style="width: 100%; display: flex; align-items: center;">
                <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
                <sl-breadcrumb class="topNavbar">
                    <sl-breadcrumb-item>
                        <sl-icon slot="prefix" name="clipboard2-data"></sl-icon>
                        <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Services Report</label>
                    </sl-breadcrumb-item>
                </sl-breadcrumb>
            </div>
        </div>
        <nav class="sl-theme-dark">
            <div style="width: 100vw; display: flex; justify-content: center;">
                <div style="display: flex; flex-direction: row; justify-content: flex-start; 
                align-items: flex-end; width: 85%;">
                    <div style="width: 100%; margin-right: 0.5%; padding: 10px; 
                        border: solid var(--sl-input-border-width) var(--sl-input-border-color); border-radius: 10px; max-height: 500px; overflow-y: auto;">
                        <sl-tab-group placement="start">
                            <sl-tab slot="nav" panel="employeeSelection">Step 1 - Select Appointment</sl-tab>
                            <sl-tab slot="nav" panel="documentHeader">Step 2 - Docs. Header </sl-tab>
                            <sl-tab slot="nav" panel="documentInformation">Step 3 - Docs. Info.</sl-tab>
                            <sl-tab slot="nav" panel="documentTable">Step 4 - Serv. Table</sl-tab>
                            <sl-tab slot="nav" panel="documentInformationFooter">Step 5 - Services Info.</sl-tab>
                            <sl-tab slot="nav" panel="documentPreparerInformation">Step 6 - Technician Info.</sl-tab>
                            <sl-tab slot="nav" panel="submitTheData">Output / Load</sl-tab>

                            <sl-tab-panel name="employeeSelection">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Select
                                    Appointment</label>
                                <sl-select id="serviceReportDetails_AppointmentID" class="column" label="Select Appointment"
                                    size="small">
                                    <sl-option value="">Loading..</sl-option>
                                </sl-select>
                            </sl-tab-panel>


                            <sl-tab-panel name="documentHeader">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Documents
                                    Header</label>
                                <sl-input id="serviceReportHeader_CompanyName" class="column" label="Company Name"
                                    placeholder="Ex. Aircool JV" size="small" data-save></sl-input>
                                <sl-input id="serviceReportHeader_CompanyAddress" class="column" label="Company Address"
                                    placeholder="Ex. Santa Rosa St." size="small" data-save></sl-input>

                                <sl-input id="serviceReportHeader_CompanyNumber" class="column" label="Contact Number"
                                    placeholder="Ex. (123) 456-789" size="small" data-save></sl-input>
                                <sl-input id="serviceReportHeader_CompanyEmail" class="column" label="Email Address"
                                    placeholder="Ex. example@email.com" size="small" data-save></sl-input>
                            </sl-tab-panel>


                            <sl-tab-panel name="documentInformation"><label
                                    style="font-weight: 600; font-size: 16px; color: #27BAFD;">Documents
                                    Information</label>
                                <sl-input id="serviceReportBody_CustomerName" class="column" label="Customer Name"
                                    size="small"></sl-input>
                                <sl-input id="serviceReportBody_Location" class="column" label="Customer Address"
                                    size="small"></sl-input>
                                <sl-input config-id="date" id="serviceReportBody_Date" class="column" label="Date"
                                    placeholder="Ex. 20XX-12-25" size="small"></sl-input>
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
                                    Service Information</label>
                                <sl-input id="serviceReportFooter_Complaint" class="column" label="Complaint"
                                    placeholder="Ex. Not Working Aircon" size="small" data-save></sl-input>
                                <sl-input id="serviceReportFooter_Diagnosed" class="column" label="Diagnosed"
                                    placeholder="Ex. Dust Buildup and Repair" size="small" data-save></sl-input>
                                <sl-input id="serviceReportFooter_ActivityPerformed" class="column" label="Activity Performed"
                                    placeholder="Ex. General Cleaning and Repair"
                                    size="small" data-save></sl-input>
                                <sl-input id="serviceReportFooter_Recommendation" class="column" label="Recommendation"
                                    placeholder="Ex. Preventive Maintenance every 6 Months"
                                    size="small" data-save></sl-input>
                            </sl-tab-panel>


                            <sl-tab-panel name="documentPreparerInformation">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Document
                                    Technician Information</label>
                                <sl-input id="serviceReportFooter_PreparerName" class="column"
                                    label="Technician Name" placeholder="Ex. John Doe" size="small" data-save></sl-input>
                                <sl-input id="serviceReportFooter_PreparerPosition" class="column"
                                    label="Position" placeholder="Ex. Technician" size="small" data-save></sl-input>
                                <sl-input id="serviceReportFooter_ManagerName" class="column"
                                    label="Customer Name" placeholder="Ex. Jane Doe"
                                    size="small"></sl-input>
                            </sl-tab-panel>


                            <sl-tab-panel name="submitTheData">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">
                                    Output</label>
                                <br>
                                <br>
                                <sl-tooltip content="Generate a Customer Service Report" placement="top">
                                <sl-button id="generateServiceReport" variant="primary" size="small">Generate</sl-button>
                                </sl-tooltip>
                                <sl-tooltip content="When Generate Service Report is done, 'Click' this to redirect to Printable Page" placement="right">
                                <sl-button variant="primary" size="small" href="<?= BASE_URL_STYLE ?>/PrintablePage/print-serviceReport.php">
                                    Visit Print</sl-button>
                                </sl-tooltip>

                                <div style="width: 300px;display: flex;padding-top: 20px;
                                flex-direction: column;align-items: stretch;flex-wrap: nowrap;">
                                    <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">
                                        Load Created Settings</label>
                                    <sl-input id="settingName" class="column" label="Settings Name"
                                        placeholder="Ex. AirCool Company Settings" size="small"></sl-input>
                                    <sl-select id="settingsLoad" class="column" label="Select Settings to Load"
                                        size="small">
                                        <sl-option value="">None...</sl-option>
                                    </sl-select>
                                    <br>
                                    <sl-button-group label="Alignment">
                                        <sl-button id="saveSettings" variant="success" size="small"
                                            outline>Save</sl-button>
                                        <sl-button id="loadSettings" variant="success" size="small"
                                            outline>Load</sl-button>
                                        <sl-button id="updateSettings" variant="warning" size="small"
                                            outline>Update</sl-button>
                                        <sl-button id="deleteSettings" variant="danger" size="small"
                                            outline>Delete</sl-button>
                                    </sl-button-group>
                                </div>
                                <br> <br>
                                    <sl-details summary="Help?">
                                        <p style="font-weight: 300; font-size: 16px;"><sl-tag size="small">Service Report</sl-tag> Page, is where you can generate a Service Report by filling those information. <br><br>
                                        In the <sl-tag variant="primary" size="small">Ouput</sl-tag> There's <sl-tag size="small">2</sl-tag> Button <sl-tag variant="primary" size="small">Gemerate</sl-tag> and <sl-tag variant="primary" size="small">Visit Print</sl-tag>. <br> <br>
                                        What <sl-tag variant="primary" size="small">Generate</sl-tag> do is take all the input from <sl-tag size="small">Step 1</sl-tag> to <sl-tag size="small">Step 6</sl-tag>, and send it to the Database to Stored at the same time,
                                        a copy of it, will be sent to <sl-tag variant="primary" size="small">Visit Print</sl-tag> to be displayed and print. <br> <br>

                                        <sl-icon name="exclamation-triangle" style="font-size: 16px; color: orange;"></sl-icon> Take Note that any if you click <sl-tag variant="primary" size="small">Visit Print</sl-tag> before <sl-tag variant="primary" size="small">Gemerate</sl-tag>, it will display the previous <sl-tag size="small">Service Report</sl-tag>.
                                        </p>
                                        <sl-divider></sl-divider>
                                        <p style="font-weight: 300; font-size: 16px;">In the <sl-tag size="small" variant="primary">Load Created Settings</sl-tag> Section, you can save the current settings you have, and load it back when you need it. <br> <br>
                                        So what this Load Created Settings do?.. It take the current input and save in the local Storage, where you can load it back, this is to prevent repeative input of the same information. <br> <br>
                                        </p>
                                    </sl-details>
                            </sl-tab-panel>

                            <sl-tab-panel name="documentTable">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Services Report Table</label>
                                <br>
                                <br>
                                <div class="titleContent">
                                    <table id="serviceReportTable" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Unit</th>
                                                <th>Activity Performed</th>
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
                                        <sl-button variant="primary" size="small" class="submitButton" onclick="addRow()">Add Row</button>
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