<?php
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
define('BASE_URL_STYLE', '/Coolant/source-code');

include PROJECT_ROOT . "/Controller/billingStatementController.php";
include PROJECT_ROOT . "/Controller/dataRetrieval.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/billing-statement.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Billing Statement</title>
</head>

<body>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/billing-statement/billingStatementCollection.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/billing-statement/billingStatementFunctions.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/billing-statement/billingStatementSettings.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/billing-statement/billingStatementLoadCustomer.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <div class="content">
        <div class="topNavigationBar">
            <div class="topNavigationBar1" style="width: 100%; display: flex; align-items: center;">
                <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
                <sl-breadcrumb class="topNavbar">
                    <sl-breadcrumb-item>
                        <sl-icon slot="prefix" name="receipt-cutoff"></sl-icon>
                        <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Billing Statement</label>
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
                            <sl-tab slot="nav" panel="documentInformation">Step 4 - Docs. Info.</sl-tab>
                            <sl-tab slot="nav" panel="documentInformationFooter">Step 5 - Billing Info.</sl-tab>
                            <sl-tab slot="nav" panel="submitTheData">Output / Load</sl-tab>
                            <sl-tab slot="nav" panel="collectionStatement">Collection</sl-tab>

                            <sl-tab-panel name="employeeSelection">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Select
                                    Appointment</label>
                                <sl-select id="billingDetails_AppointmentID" class="column" label="Select Appointment"
                                    size="small">
                                    <sl-option value="">Loading..</sl-option>
                                </sl-select>
                                <div class="appointmentTable">
                                    <p class="titleHeader">Customer Table</p>
                                    <?php
                                    $appointmentManager->fetchAppointments();
                                    ?>
                                </div>
                            </sl-tab-panel>

                            <sl-tab-panel name="documentHeader">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Documents
                                    Header</label>
                                <sl-input id="billingHeader_CompanyName" class="column" label="Company Name"
                                    placeholder="Ex. Aircool JV" size="small" data-save></sl-input>
                                <sl-input id="billingHeader_CompanyAddress" class="column" label="Company Address"
                                    placeholder="Ex. Santa Rosa St." size="small" data-save></sl-input>

                                <sl-input id="billingHeader_CompanyNumber" class="column" label="Contact Number"
                                    placeholder="Ex. (123) 456-789" size="small" data-save></sl-input>
                                <sl-input id="billingHeader_CompanyEmail" class="column" label="Email Address"
                                    placeholder="Ex. example@email.com" size="small" data-save></sl-input>
                            </sl-tab-panel>


                            <sl-tab-panel name="documentInformation"><label
                                    style="font-weight: 600; font-size: 16px; color: #27BAFD;">Documents
                                    Information</label>
                                <sl-input id="billingBody_CustomerName" class="column" label="Customer Name"
                                    size="small"></sl-input>
                                <sl-input id="billingBody_Location" class="column" label="Customer Address"
                                    size="small"></sl-input>
                                <sl-input config-id="date" id="billingBody_Date" class="column" label="Date"
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
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Document
                                    Billing Information</label>
                                <sl-input id="billingFooter_AuthorizedName" class="column"
                                    label="Authorized by" placeholder="Ex. John Doe" size="small" data-save></sl-input>
                                <sl-input id="billingFooter_AuthorizedRole" class="column"
                                    label="Position" placeholder="Ex. Manager" size="small" data-save></sl-input>
                                <sl-input id="billingFooter_Remarks" class="column"
                                    label="Message" placeholder="Ex. Thank you for doing business with us."
                                    size="small" data-save></sl-input>
                            </sl-tab-panel>

                            <sl-tab-panel name="submitTheData">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">
                                    Output</label>
                                <br>
                                <br>
                                <sl-button id="generateBillingReport" variant="primary" size="small">Generate</sl-button>
                                <sl-button variant="primary" size="small" href="<?= BASE_URL_STYLE ?>/PrintablePage/print-billingStatement.php">
                                    Visit Print</sl-button>


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
                            </sl-tab-panel>

                            <sl-tab-panel name="collectionStatement">
                                <sl-select id="selectCollection_ID" class="column" label="Select Collection ID"
                                    size="small">
                                    <sl-option value="">Loading..</sl-option>
                                </sl-select>
                                <sl-select id="statusCollection" class="column" label="Select Collection ID"
                                    size="small">
                                    <sl-option value="Paid">Paid</sl-option>
                                    <sl-option value="Pending">Pending</sl-option>
                                </sl-select>
                                <br>
                                <sl-button id="submitCollection" variant="primary"
                                    size="small">Submit</sl-button>
                                <div class="pendingCollectionTable">
                                    <p class="titleHeader">Pending Collection Table</p>
                                    <?php
                                    $appointmentManager->fetchPendingCollection();
                                    ?>
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