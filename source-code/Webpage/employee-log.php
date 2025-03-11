<?php
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
define('BASE_URL_STYLE', '/Coolant/source-code');

include PROJECT_ROOT . "/Controller/employeeLogController.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/employee-log.css">
    <title>Employee Log</title>
</head>

<body>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/employee-log/employee-logFilter.js"></script>
    <div class="content">
        <div class="topNavigationBar">
            <div class="topNavigationBar1" style="width: 100%; display: flex; align-items: center;">
                <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
                <sl-breadcrumb class="topNavbar">
                    <sl-breadcrumb-item>
                        <sl-icon slot="prefix" name="substack"></sl-icon>
                        <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Employee Log</label>
                    </sl-breadcrumb-item>
                </sl-breadcrumb>
            </div>
        </div>
        <nav class="sl-theme-dark">
            <div style="width: 100vw; display: flex; justify-content: center;">
                <div style="display: flex; flex-direction: row; justify-content: flex-start; align-items: flex-end; 
                border: solid var(--sl-input-border-width) var(--sl-input-border-color); width: 85%; padding: 10px;
                border-radius: 10px;">
                    <sl-select id="dropdownOrderBy" class="column" label="Order By" size="small">
                        <sl-option value="appointment.date">Date</sl-option>
                        <sl-option value="employee_log.id">Log Number</sl-option>
                        <sl-option value="employee_log.employee_id">Employee ID</sl-option>
                        <sl-option value="appointment.id">Appointment ID</sl-option>
                    </sl-select>
                    <sl-select id="dropdownSortBy" class="column" label="Sort By" size="small">
                        <sl-option value="ASC">Ascending</sl-option>
                        <sl-option value="DESC">Descending</sl-option>
                    </sl-select>
                    <div class="column">
                        <sl-button id="filterApplyButton" variant="primary" size="small">Apply</sl-button>
                    </div>
                </div>
            </div>
            <div style="width: 100vw; display: flex; justify-content: center;">
                <div class="employeeLogTable">
                    <?= $table_content; ?>
                </div>
            </div>
        </nav>
    </div>
</body>

</html>