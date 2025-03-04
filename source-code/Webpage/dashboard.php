<?php
define('PROJECT_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
define('BASE_URL_STYLE', '/Coolant/source-code');

//include PROJECT_ROOT . "/Controller/scheduleController.php";
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
            <div style="width: 100vw; display: flex; justify-content: center;">
                <div style="display: flex; flex-direction: row; justify-content: flex-start; align-items: flex-end; 
                border: solid var(--sl-input-border-width) var(--sl-input-border-color); width: 85%; padding: 10px;
                border-radius: 10px;">
                </div>
            </div>
        </nav>
    </div>
</body>

</html>