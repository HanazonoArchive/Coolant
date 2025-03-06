<?php
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
define('BASE_URL_STYLE', '/Coolant/source-code');

include PROJECT_ROOT . "/Controller/scheduleController.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/schedule.css">
    <title>Schedule</title>
</head>

<body>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/schedule/scheduleFilter.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/schedule/scheduleDetails.js"></script>
    <div class="content">
        <div class="topNavigationBar">
            <div class="topNavigationBar1" style="width: 100%; display: flex; align-items: center;">
                <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
                <sl-breadcrumb class="topNavbar">
                    <sl-breadcrumb-item>
                        <sl-icon slot="prefix" name="calendar-date"></sl-icon>
                        <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Schedule</label>
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
                        <sl-option value="appointment.id">Ticket</sl-option>
                        <sl-option value="Priority">Priority</sl-option>
                        <sl-option value="Date">Date</sl-option>
                    </sl-select>
                    <sl-select id="dropdownFilterBy" class="column" label="Filter By" size="small">
                        <sl-option value="Pending">Pending</sl-option>
                        <sl-option value="Working">Working</sl-option>
                        <sl-option value="Completed">Completed</sl-option>
                        <sl-option value="Cancelled">Cancelled</sl-option>
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
            <div style="width: 100vw; display: flex; justify-content: center; margin-top: 10px;">
                <div class="scheduleTable">
                    <?= $table_content; ?>
                </div>
                <div style="display: flex; flex-direction: column;   justify-content: flex-start; align-items: flex-start; 
                border: solid var(--sl-input-border-width) var(--sl-input-border-color); width: 19%; padding: 10px;
                border-radius: 10px; margin-left: 10px;">
                    <p class="detailsHeader">Customer Information</p>
                    <div class="detailsRow">
                        <p class=detailsTitle>Customer ID</p>
                        <p class="detialsContent" id="customer_id">-</p>
                    </div>
                    <div class="detailsRow">
                        <p class=detailsTitle>Customer Name</p>
                        <p class="detialsContent" id="customer_name">-</p>
                    </div>
                    <div class="detailsRow">
                        <p class=detailsTitle>Contact Number</p>
                        <p class="detialsContent" id="customer_contact-number">-</p>
                    </div>
                    <div class="detailsRow">
                        <p class=detailsTitle>Address</p>
                        <p class="detialsContent" id="customer_address">-</p>
                    </div>
                    <hr style="width: 235px;">
                    <p class="detailsHeader">Appoinment Information</p>
                    <div class="detailsRow">
                        <p class=detailsTitle>Appointment ID</p>
                        <p class="detialsContent" id="appointment_id">-</p>
                    </div>
                    <div class="detailsRow">
                        <p class=detailsTitle>Date</p>
                        <p class="detialsContent" id="appointment_date">-</p>
                    </div>
                    <div class="detailsRow">
                        <p class=detailsTitle>Category</p>
                        <p class="detialsContent" id="appointment_category">-</p>
                    </div>
                    <div class="detailsRow">
                        <p class=detailsTitle>Priority</p>
                        <p class="detialsContent" id="appointment_priority">-</p>
                    </div>
                    <div class="detailsRow">
                        <p class=detailsTitle>Status</p>
                        <p class="detialsContent" id="appointment_status">-</p>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</body>

</html>