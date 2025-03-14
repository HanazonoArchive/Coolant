<?php
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
define('BASE_URL_STYLE', '/Coolant/source-code');

include PROJECT_ROOT . "/Controller/employeeController.php";
include PROJECT_ROOT . "/Controller/employeeController_Employee.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/employee.css">
    <title>Employee</title>
</head>

<body>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/employee/employeeFunctions.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/employee/employeeFilter.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/employee/employeeCreate.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/employee/employeeUpdate.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/employee/employeeDelete.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/employee/employeePay.js"></script>
    <div class="content">
        <div class="topNavigationBar">
            <div class="topNavigationBar1" style="width: 100%; display: flex; align-items: center;">
                <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
                <sl-breadcrumb class="topNavbar">
                    <sl-breadcrumb-item>
                        <sl-icon slot="prefix" name="person-circle"></sl-icon>
                        <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Employee</label>
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
                        <sl-select id="dropdownOrderBy" label="Order by" size="small">
                            <sl-option value="employee.id">Employee ID</sl-option>
                            <sl-option value="pay">Pay</sl-option>
                            <sl-option value="days_of_work">Work Days</sl-option>
                        </sl-select>
                    </div>
                    <div class="column">
                        <sl-select id="dropdownStatusBy" label="Status by" size="small">
                            <sl-option value="Present">Present</sl-option>
                            <sl-option value="Absent">Absent</sl-option>
                            <sl-option value="On-Leave">On-Leave</sl-option>
                        </sl-select>
                    </div>
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
                        <sl-dialog id="createEmployee_Dialog" label="Create Employee" class="dialog-deny-close">
                            <sl-input id="employeeAdd_Name" label="Name"
                                help-text="Type in the Employee Name." size="small"></sl-input>
                            <br>
                            <sl-input id="employeeAdd_ContactNumber" label="Contact Number"
                                help-text="Type in the Employee Contact Number." size="small"></sl-input>
                            <br>
                            <sl-input id="employeeAdd_Role" label="Role"
                                help-text="Type in the Employee Role." size="small"></sl-input>
                            <br>
                            <sl-input id="employeeAdd_Pay" label="Pay"
                                help-text="Type in the Employee pay." size="small"></sl-input>

                            <sl-divider></sl-divider>

                            <sl-button id="submitEmployeeAdd" variant="primary" outline>Create</sl-button>
                            <br>
                            <br>
                            <sl-details summary="Help?">
                                <p style="font-weight: 300; font-size: 16px;"> Type in Employee <sl-tag size="small">Name</sl-tag>, <sl-tag size="small">Contact Number</sl-tag>, 
                                <sl-tag size="small">Address</sl-tag>, <sl-tag size="small">Role</sl-tag>, and <sl-tag size="small">Pay</sl-tag> of the Employee. <br> <br>
                                </p>
                            </sl-details>

                            <sl-button id="createEmployee_Close" slot="footer" variant="primary">Close</sl-button>
                        </sl-dialog>
                        <sl-tooltip content="Yes... It's Create Employee...">
                        <sl-button id="createEmployee_Open" size="small" variant="success" outline>Create</sl-button>
                        </sl-tooltip>
                    </div>
                    <div class="column">
                        <sl-dialog id="updateEmployee_Dialog" label="Update Employee" class="dialog-deny-close">
                            <sl-select id="UpdateEmployee_ID" label="Select EmployeeID"
                                help-text="Select the available employee." size="small">
                                <sl-option value="">Loading...</sl-option>
                            </sl-select>
                            <br>
                            <sl-input id="UpdateEmployee_NewName" label="Name"
                                help-text="Type in the New Employee Name." size="small"></sl-input>
                            <br>
                            <sl-input id="UpdateEmployee_NewContactNumber" label="Contact Number"
                                help-text="Type in the New Employee Contact Number." size="small"></sl-input>
                            <br>
                            <sl-input id="UpdateEmployee_NewRole" label="Role"
                                help-text="Type in the New Employee Role." size="small"></sl-input>
                            <br>
                            <sl-input id="UpdateEmployee_NewPay" label="Pay"
                                help-text="Type in the New Employee pay." size="small"></sl-input>
                            <br>
                            <sl-select id="UpdateEmploye_NewStatus" label="Employee Status"
                                help-text="Update Employee status." size="small">
                                <sl-option value="Present">Present</sl-option>
                                <sl-option value="Absent">Absent</sl-option>
                                <sl-option value="On-Leave">On-Leave</sl-option>
                            </sl-select>

                            <sl-divider></sl-divider>

                            <sl-button id="submitEmployeeUpdate" variant="primary" outline>Update</sl-button>
                            <br>
                            <br>
                            <sl-details summary="Help?">
                                <p style="font-weight: 300; font-size: 16px;"> Select <sl-tag size="small">Employee ID</sl-tag> you want to Update. <br> <br>
                                <sl-icon name="exclamation-triangle" style="font-size: 16px; color: orange;"></sl-icon> Take Note that you can also Update Employee Status, and leave the rest empty.</p>
                            </sl-details>

                            <sl-button id="updateEmployee_Close" slot="footer" variant="primary">Close</sl-button>
                        </sl-dialog>
                        <sl-tooltip content="Oh... It's Update Employee...">
                        <sl-button id="updateEmployee_Open" size="small" variant="warning" outline>Update</sl-button>
                        </sl-tooltip>
                    </div>
                    <div class="column">
                        <sl-dialog id="deleteEmployee_Dialog" label="Delete Employee" class="dialog-deny-close">
                            <sl-select id="DeleteEmployee_ID" label="Select EmployeeID"
                                help-text="Select the available employee." size="small">
                                <sl-option value="">Loading...</sl-option>
                            </sl-select>
                            <br>
                            <sl-input id="DeleteEmployee_Confirmation" label="Confirmation"
                                help-text='Type "DELETE" to confirm' size="small"></sl-input>

                            <sl-divider></sl-divider>

                            <sl-button id="submitEmployeeDelete" variant="primary" outline>Delete</sl-button>
                            <br>
                            <br>
                            <sl-details summary="Help?">
                                <p style="font-weight: 300; font-size: 16px;"> Select <sl-tag size="small">Employee ID</sl-tag> you want to Delete. <br> <br>
                                <sl-icon name="exclamation-triangle" style="font-size: 16px; color: orange;"></sl-icon> Take Note that you can't Delete an Employee that is already on <sl-tag size="small" variant="primary">Appointment</sl-tag>. <br><br>
                                Only those who aren't part of any <sl-tag size="small" variant="primary">Appointment</sl-tag> can be Deleted.</p>
                            </sl-details>

                            <sl-button id="deleteEmployee_Close" slot="footer" variant="primary">Close</sl-button>
                        </sl-dialog>

                        <sl-tooltip content="Oh... It's Delete Employee...">
                        <sl-button id="deleteEmployee_Open" size="small" variant="danger" outline>Delete</sl-button>
                        </sl-tooltip>
                    </div>
                    <div class="column">
                        <sl-dialog id="payEmployee_Dialog" label="Pay Employee" class="dialog-deny-close">
                            <sl-select id="payEmployee_ID" label="Select EmployeeID"
                                help-text="Select the available employee." size="small">
                                <sl-option value="">Loading...</sl-option>
                            </sl-select>
                            <br>
                            <sl-input id="payEmployee_Confirmation" label="Pay"
                                help-text='Type the "Total Pay" to confirm.' size="small"></sl-input>

                            <sl-divider></sl-divider>

                            <sl-button id="submitEmployeePay" variant="primary" outline>Pay</sl-button>
                            <br>
                            <br>
                            <sl-details summary="Help?">
                                <p style="font-weight: 300; font-size: 16px;"> Select <sl-tag size="small">Employee ID</sl-tag> you want to Pay. <br> <br>
                                <sl-icon name="exclamation-triangle" style="font-size: 16px; color: orange;"></sl-icon> Take Note that only pay them based on their <sl-tag size="small" variant="primary">Total Pay</sl-tag>. </p>
                            </sl-details>

                            <sl-button id="payEmployee_Close" slot="footer" variant="primary">Close</sl-button>
                        </sl-dialog>

                        <sl-tooltip content="Oh... It's Paying Employee...">
                        <sl-button id="payEmployee_Open" size="small" variant="primary" outline>Pay</sl-button>
                        </sl-tooltip>
                    </div>
                </div>
            </div>
            <div style="width: 100vw; display: flex; justify-content: center;">
                <div class="employeeTable">
                    <?= $table_content; ?>
                </div>
            </div>
        </nav>
    </div>
</body>

</html>