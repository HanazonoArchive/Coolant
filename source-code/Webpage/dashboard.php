<?php
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
define('BASE_URL_STYLE', '/Coolant/source-code');

include PROJECT_ROOT . "/Controller/dashboardController.php";
include PROJECT_ROOT . "/Controller/WeeklyScheduleController.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= BASE_URL_STYLE ?>/StyleSheet/dashboard.css">
    <title>Dashboard</title>
</head>

<body>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/dashboard/dashboardFunctions.js"></script>
    <script src="<?= BASE_URL_STYLE ?>/JavaScript/dashboard/dashboardWeekSchedule.js"></script>
    <div class="content">
        <div class="topNavigationBar">
            <div class="topNavigationBar1" style="width: 100%; display: flex; align-items: center;">
                <?php require PROJECT_ROOT . "/Components/sidebar.php"; ?>
                <sl-breadcrumb class="topNavbar">
                    <sl-breadcrumb-item>
                        <sl-icon slot="prefix" name="columns-gap"></sl-icon>
                        <label style="padding-left: 10px; font-size: 18px; font-weight: 600;">| Dashboard</label>
                    </sl-breadcrumb-item>
                </sl-breadcrumb>
            </div>
        </div>
        <nav class="sl-theme-dark">
            <div style="width: 98vw; display: flex; justify-content: center;">
                <div style="display: flex; flex-direction: row; justify-content: flex-start; align-items: flex-end; width: 86%; height: 85vh;">
                    <div style="display: flex;width: 50%;flex-direction: column; flex-wrap: nowrap;justify-content: center;align-items: center; height: 100%; margin-right: 5px;">
                        <div style="padding: 10px; display: flex;border: solid var(--sl-input-border-width) var(--sl-input-border-color);border-radius: 10px;width: 100%;height: 69.5%;margin-bottom: 5px;flex-direction: column;align-items: flex-start;">
                            <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Schedule this Week</label>
                            <div class="scheduleTable">
                                <?= $scheduleWeekTable; ?>
                            </div>
                        </div>
                        <div style="padding: 10px; display: flex;border: solid var(--sl-input-border-width) var(--sl-input-border-color);border-radius: 10px;width: 100%;height: 29.5%;margin-top: 5px;flex-direction: column;align-items: flex-start;">
                            <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Pending Collection</label>
                            <div class="pendingTable">
                                <?= $pendingCollectionTable; ?>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex;width: 50%;flex-direction: column; flex-wrap: nowrap;justify-content: center;align-items: center; height: 100%; margin-left: 5px;">
                        <div style="display: flex; width: 100%; height: 59.5%; margin-bottom: 5px;">
                            <div style="display: flex;border: solid var(--sl-input-border-width) var(--sl-input-border-color);border-radius: 10px;width: 49.5%;height: 100%;margin-right: 5px;flex-direction: column;flex-wrap: nowrap;justify-content: center;align-items: center;">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Employee Available</label>
                                <br>
                                <sl-progress-ring id="employeeProgress" value="0" class="progress-ring-values" style="--track-width: 16px; --indicator-width: 24px; --size: 200px;">
                                    0/0
                                </sl-progress-ring>
                            </div>
                            <div style="display: flex;border: solid var(--sl-input-border-width) var(--sl-input-border-color);border-radius: 10px;width: 49.5%;height: 100%;margin-left: 5px;flex-direction: column;flex-wrap: nowrap;justify-content: center;align-items: center;">
                                <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Completed Appointment</label>
                                <br>
                                <sl-progress-ring id="appointmentProgress" value="0" class="progress-ring-values" style="--track-width: 16px; --indicator-width: 24px; --size: 200px;">
                                    0/0
                                </sl-progress-ring>
                            </div>
                        </div>
                        <div style=" padding: 10px; display: flex;border: solid var(--sl-input-border-width) var(--sl-input-border-color);border-radius: 10px;width: 100%;height: 39.5%;margin-top: 5px;flex-direction: column;flex-wrap: nowrap;align-items: flex-start;">
                            <label style="font-weight: 600; font-size: 16px; color: #27BAFD;">Employee</label>
                            <div class="employeeTable">
                                <?= $employeeTable; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="width: 98vw; display: flex; justify-content: center;">
                <div style="display: flex; flex-direction: row; justify-content: flex-start; align-items: stretch; 
                border: solid var(--sl-input-border-width) var(--sl-input-border-color); width: 86%; padding: 10px;
                border-radius: 10px; margin-top: 10px; margin-bottom: 10px;">
                    <div style="display: flex; flex-direction: column; justify-content: flex-start; align-items: stretch;
                    border: solid var(--sl-input-border-width) var(--sl-input-border-color); 
                    width: 20%; height: auto;; border-radius: 10px; margin: 5px;">
                        <label style="padding-left: 10px; padding-top: 5px;" id="Monday">Monday</label>
                        <div style="width: 100%;">
                            <sl-divider style="width: 100%; --spacing: 5px;"></sl-divider>
                        </div>
                        <div id="scheduleDay_Monday">
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: flex-start; align-items: stretch;
                    border: solid var(--sl-input-border-width) var(--sl-input-border-color); 
                    width: 20%; height: auto;; border-radius: 10px; margin: 5px;">
                        <label style="padding-left: 10px; padding-top: 5px;" id="Tuesday">Tuesday</label>
                        <div style="width: 100%;">
                            <sl-divider style="width: 100%; --spacing: 5px;"></sl-divider>
                        </div>
                        <div id="scheduleDay_Tuesday">
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: flex-start; align-items: stretch;
                    border: solid var(--sl-input-border-width) var(--sl-input-border-color); 
                    width: 20%; height: auto;; border-radius: 10px; margin: 5px;">
                        <label style="padding-left: 10px; padding-top: 5px;" id="Wednesday">Wednesday</label>
                        <div style="width: 100%;">
                            <sl-divider style="width: 100%; --spacing: 5px;"></sl-divider>
                        </div>
                        <div id="scheduleDay_Wednesday">
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: flex-start; align-items: stretch;
                    border: solid var(--sl-input-border-width) var(--sl-input-border-color); 
                    width: 20%; height: auto;; border-radius: 10px; margin: 5px;">
                        <label style="padding-left: 10px; padding-top: 5px;" id="Thursday">Thursday</label>
                        <div style="width: 100%;">
                            <sl-divider style="width: 100%; --spacing: 5px;"></sl-divider>
                        </div>
                        <div id="scheduleDay_Thursday">
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: flex-start; align-items: stretch;
                    border: solid var(--sl-input-border-width) var(--sl-input-border-color); 
                    width: 20%; height: auto;; border-radius: 10px; margin: 5px;">
                        <label style="padding-left: 10px; padding-top: 5px;" id="Friday">Friday</label>
                        <div style="width: 100%;">
                            <sl-divider style="width: 100%; --spacing: 5px;"></sl-divider>
                        </div>
                        <div id="scheduleDay_Friday">
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: flex-start; align-items: stretch;
                    border: solid var(--sl-input-border-width) var(--sl-input-border-color); 
                    width: 20%; height: auto;; border-radius: 10px; margin: 5px;">
                        <label style="padding-left: 10px; padding-top: 5px;" id="Saturday">Saturday</label>
                        <div style="width: 100%;">
                            <sl-divider style="width: 100%; --spacing: 5px;"></sl-divider>
                        </div>
                        <div id="scheduleDay_Saturday">
                        </div>
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: flex-start; align-items: stretch;
                    border: solid var(--sl-input-border-width) var(--sl-input-border-color); 
                    width: 20%; height: auto;; border-radius: 10px; margin: 5px;">
                        <label style="padding-left: 10px; padding-top: 5px;" id="Sunday">Sunday</label>
                        <div style="width: 100%;">
                            <sl-divider style="width: 100%; --spacing: 5px;"></sl-divider>
                        </div>
                        <div id="scheduleDay_Sunday">
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</body>

</html>