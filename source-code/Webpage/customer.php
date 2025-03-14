<?php
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
define('BASE_URL_STYLE', '/Coolant/source-code');

include PROJECT_ROOT . "/Controller/customerController_Customer.php";
include PROJECT_ROOT . "/Controller/customerController_Feedback.php";
include PROJECT_ROOT . "/Controller/customerController.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/customer.css">
    <title>Customer</title>
</head>

<body>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/customer/customerFunctions.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/customer/customerFilter.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/customer/customerUpdate.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/customer/customerDelete.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/customer/feedbackCreate.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/customer/feedbackUpdate.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/customer/feedbackDelete.js"></script>
    <div class="content">
        <div class="topNavigationBar">
            <div class="topNavigationBar1" style="width: 100%; display: flex; align-items: center;">
                <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
                <sl-breadcrumb class="topNavbar">
                    <sl-breadcrumb-item>
                        <sl-icon slot="prefix" name="file-person"></sl-icon>
                        <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Customer</label>
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
                        <sl-select id="dropdownSortBy" label="Sort by" size="small">
                            <sl-option value="ASC">Ascending</sl-option>
                            <sl-option value="DESC">Descending</sl-option>
                        </sl-select>
                    </div>
                    <div class="column">
                        <sl-button id="filterApplyButton" variant="primary" size="small">Apply</sl-button>
                    </div>
                    <div class="column">
                        <sl-dialog id="updateCustomer_Dialog" label="Update Customer" class="dialog-deny-close">
                            <sl-select id="UpdateCustomer_ID" label="Select CustomerID"
                                help-text="Select the available customer." size="small">
                                <sl-option value="">Loading...</sl-option>
                            </sl-select>
                            <br>
                            <sl-input id="UpdateCustomer_NewName" label="Customer Name"
                                help-text="Type in the new Customer Name." size="small"></sl-input>
                            <br>
                            <sl-input id="UpdateCustomer_NewContactNumber" label="Contact Number"
                                help-text="Type in the new Customer Contact Number." size="small"></sl-input>
                            <br>
                            <sl-input id="UpdateCustomer_NewAddress" label="Address"
                                help-text="Type in the new Customer Address." size="small"></sl-input>
                            <br>
                            <sl-divider></sl-divider>
                            <sl-button id="submitCustomerUpdate" variant="primary" outline>Update</sl-button>

                            <sl-button id="updateCustomer_Close" slot="footer" variant="primary">Close</sl-button>
                            <br>
                            <br>
                            <sl-details summary="Help?">
                                <p style="font-weight: 300; font-size: 16px;"> Select <sl-tag size="small">Customer ID</sl-tag> you want to Update. <br> <br>
                                <sl-icon name="exclamation-triangle" style="font-size: 16px; color: orange;"></sl-icon> Take Note that if you want to update the you must filled everything, in order for it to update.</p>
                            </sl-details>
                        </sl-dialog>

                        <sl-tooltip content="Oh... It's Update Customer...">
                        <sl-button id="updateCustomer_Open" size="small" variant="warning" outline>Update
                            Customer</sl-button>
                        </sl-tooltip>
                    </div>
                    <div class="column">
                        <sl-dialog id="deleteCustomer_Dialog" label="Delete Customer" class="dialog-deny-close">
                            <sl-select id="DeleteCustomer_ID" label="Select CustomerID"
                                help-text="Select the available customer." size="small">
                                <sl-option value="">Loading...</sl-option>
                            </sl-select>
                            <br>
                            <sl-input id="DeleteCustomer_Confirmation" label="Confirmation"
                                help-text='Type "DELETE" to confirm deletion.' size="small"></sl-input>
                            <sl-divider></sl-divider>
                            <sl-button id="submitCustomerDelete" variant="primary" outline>Delete</sl-button>

                            <sl-button id="deleteCustomer_Close" slot="footer" variant="primary">Close</sl-button>
                            <br>
                            <br>
                            <sl-details summary="Help?">
                                <p style="font-weight: 300; font-size: 16px;"> Select <sl-tag size="small">Customer ID</sl-tag> you want to Delete. <br> <br>
                                <sl-icon name="exclamation-triangle" style="font-size: 16px; color: orange;"></sl-icon> Take Note that you can't Delete an Customer if has already Appointment. <br> <br>
                                Only those are no appointment customer can be deleted.</p>
                            </sl-details>
                        </sl-dialog>

                        <sl-tooltip content="Oh... It's Delete Customer...">
                        <sl-button id="deleteCustomer_Open" size="small" variant="danger" outline>Delete
                            Customer</sl-button>
                        </sl-tooltip>
                    </div>
                    <div style="padding-left: 30px; padding-right: 10px;">
                        <sl-dialog id="createFeedback_Dialog" label="Create Feedback" class="dialog-deny-close">
                            <sl-select id="AddFeedback_AppointmentID" label="Select CustomerID"
                                help-text="Select the available customer." size="small">
                                <sl-option value="">Loading...</sl-option>
                            </sl-select>
                            <br>
                            <sl-input id="AddFeedback_Comment" label="Comment" help-text="Customer Feedback Comment."
                                size="small"></sl-input>
                            <sl-divider></sl-divider>
                            <sl-button id="submitFeedbackAdd" variant="primary" outline>Create</sl-button>
                            <br>
                            <br>
                            <sl-details summary="Help?">
                                <p style="font-weight: 300; font-size: 16px;">Select <sl-tag size="small">Appointment ID</sl-tag> you want to Add Feedback. <br> <br>
                                <sl-icon name="exclamation-triangle" style="font-size: 16px; color: orange;"></sl-icon> Take Note that you must filled comment, in order for it to create. Only the Appointment with status of Completed and created a billing statement cant only be used.</p>
                            </sl-details>

                            <sl-button id="createFeedback_Close" slot="footer" variant="primary">Close</sl-button>
                        </sl-dialog>

                        <sl-tooltip content="Yes... It's Create Feedback...">
                        <sl-button id="createFeedback_Open" size="small" variant="success" outline>Create
                            Feedback</sl-button>
                        </sl-tooltip>
                    </div>
                    <div class="column">
                        <sl-dialog id="updateFeedback_Dialog" label="Update Feedback" class="dialog-deny-close">
                            <sl-select id="UpdateFeedback_ID" label="Select CustomerID"
                                help-text="Select the available customer." size="small">
                                <sl-option value="">Loading...</sl-option>
                            </sl-select>
                            <br>
                            <sl-input id="UpdateFeedback_NewComment" label="Comment"
                                help-text="New Customer Feedback Comment." size="small"></sl-input>
                            <sl-divider></sl-divider>
                            <sl-button id="submitFeedbackUpdate" variant="primary" outline>Update</sl-button>
                            <br>
                            <br>
                            <sl-details summary="Help?">
                                <p style="font-weight: 300; font-size: 16px;">Select <sl-tag size="small">Feedback ID</sl-tag> you want to Update. <br> <br>
                                <sl-icon name="exclamation-triangle" style="font-size: 16px; color: orange;"></sl-icon> Take Note that you need to input the new comment to be updated.</p>
                            </sl-details>

                            <sl-button id="updateFeedback_Close" slot="footer" variant="primary">Close</sl-button>
                        </sl-dialog>

                        <sl-tooltip content="Oh... It's Update Feedback...">
                        <sl-button id="updateFeedback_Open" size="small" variant="warning" outline>Update
                            Feedback</sl-button>
                        </sl-tooltip>
                    </div>
                    <div class="column">
                        <sl-dialog id="deleteFeedback_Dialog" label="Delete Feedback" class="dialog-deny-close">
                            <sl-select id="DeleteFeedback_ID" label="Select CustomerID"
                                help-text="Select the available customer." size="small">
                                <sl-option value="">Loading...</sl-option>
                            </sl-select>
                            <br>
                            <sl-input id="DeleteFeedback_Confirmation" label="Confirmation"
                                help-text='Type "DELETE" to cofirm deletion' size="small"></sl-input>
                            <sl-divider></sl-divider>
                            <sl-button id="submitFeedbackDelete" variant="primary" outline>Delete</sl-button>
                            <br>
                            <br>
                            <sl-details summary="Help?">
                                <p style="font-weight: 300; font-size: 16px;">Select <sl-tag size="small">Feedback ID</sl-tag> you want to Delete.</p>
                            </sl-details>

                            <sl-button id="deleteFeedback_Close" slot="footer" variant="primary">Close</sl-button>
                        </sl-dialog>

                        <sl-tooltip content="Oh... It's Delete Feedback...">
                        <sl-button id="deleteFeedback_Open" size="small" variant="danger" outline>Delete
                            Feedback</sl-button>
                        </sl-tooltip>
                    </div>
                </div>
            </div>
            <div style="width: 100%;display: flex;justify-content: center;flex-direction: column;align-items: center;flex-wrap: nowrap;">
                <div style="width: 100%; display: flex; justify-content: center;">
                    <div class="customerTable">
                        <?= $table_content; ?>
                    </div>
                </div>
                <div style="width: 100%; display: flex; justify-content: center;">
                    <div class="customerFeedbackTable">
                        <?= $table_content_feedback; ?>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</body>

</html>