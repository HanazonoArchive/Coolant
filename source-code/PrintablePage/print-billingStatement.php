<?php
session_start();
define('PROJECT_DB', $_SERVER['DOCUMENT_ROOT'] . '/CSE7PHPWebsite/public/');
include_once PROJECT_DB . "db/DBConnection.php";

$conn = Database::getInstance();

$quotationID = $_SESSION['QuotationID_BS'] ?? null;
$quotationAmount = $_SESSION['QuotationAmount_BS'] ?? null;
$serviceReportID = $_SESSION['ServiceReportID_BS'] ?? null;
$serviceReportAmount = $_SESSION['ServiceReportAmount_BS'] ?? null;
$billingStatementAmount = $_SESSION['Amount_BS'] ?? null;

$items_SR = [];
$items_QUO = [];

// Check if Service Report ID exists
if ($serviceReportID) {
    $stmt1 = $conn->prepare("SELECT data FROM service_report_data WHERE service_report_id = :serviceReportID");
    $stmt1->execute(['serviceReportID' => $serviceReportID]);
    $serviceReportData = $stmt1->fetch(PDO::FETCH_ASSOC);

    if ($serviceReportData) {
        $serviceReportDataDecoded = json_decode($serviceReportData['data'], true);

        if ($serviceReportDataDecoded !== null && json_last_error() === JSON_ERROR_NONE) {
            $items_SR = $serviceReportDataDecoded['items'];
        } else {
            error_log("Error decoding Service Report JSON: " . json_last_error_msg());
        }
    } else {
        error_log("Service Report Data not found for ID: " . htmlspecialchars($serviceReportID));
    }
} else {
    error_log("Service Report ID is missing in session.");
}

// Check if Quotation ID exists
if ($quotationID) {
    $stmt2 = $conn->prepare("SELECT data FROM quotation_data WHERE quotation_id = :quotationID");
    $stmt2->execute(['quotationID' => $quotationID]);
    $quotationData = $stmt2->fetch(PDO::FETCH_ASSOC);

    if ($quotationData) {
        $quotationDataDecoded = json_decode($quotationData['data'], true);

        if ($quotationDataDecoded !== null && json_last_error() === JSON_ERROR_NONE) {
            $items_QUO = $quotationDataDecoded['items'];
        } else {
            error_log("Error decoding Quotation JSON: " . json_last_error_msg());
        }
    } else {
        error_log("Quotation Data not found for ID: " . htmlspecialchars($quotationID));
    }
} else {
    error_log("Quotation ID is missing in session.");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Billing Statement Print</title>
    <link rel="stylesheet" href="print-billingStatement.css">
</head>

<body>
    <div class="content">
        <div class="header">
            <p class="companyName"><?php echo $_SESSION['dHeader_BS']['companyName'] ?? "No data found"; ?></p>
            <p class="CompanyDetails"><?php echo $_SESSION['dHeader_BS']['companyAddress'] ?? "No data found"; ?></p>
            <p class="CompanyDetails"><?php echo $_SESSION['dHeader_BS']['companyNumber'] ?? "No data found"; ?></p>
            <p class="CompanyDetails"><?php echo $_SESSION['dHeader_BS']['companyEmail'] ?? "No data found"; ?></p>
        </div>
        <hr class="HorizontalLine">
        <p class="documentTITLE">Billing Statement</p>
        <div class="body">
            <p class="documentBody"><strong>Date:</strong>
                <?php echo $_SESSION['dBody_BS']['billingDate'] ?? "No data found"; ?></p>
            <p class="documentBody"><strong>Customer Name:</strong>
                <?php echo $_SESSION['dBody_BS']['customerName'] ?? "No data found"; ?></p>
            <p class="documentBody"><strong>Location:</strong>
                <?php echo $_SESSION['dBody_BS']['customerLocation'] ?? "No data found"; ?></p>
            <p class="documentBody"><strong>Billing Statement #:</strong>
                <?php echo $_SESSION['BillingStatementID'] ?? "No data found"; ?></p>
        </div>
        <div class="table">
            <table border="1" cellspacing="0" cellpadding="5">
                <tr>
                    <th>Unit/Item</th>
                    <th>Activity Performed/Description</th>
                    <th>Qty.</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>

                <!-- Items from $items_QUO -->
                <?php if (!empty($items_QUO) && is_array($items_QUO)): ?>
                    <?php foreach ($items_QUO as $item): ?>
                        <tr>
                            <td class="TD1"><?= $item['item'] ?></td>
                            <td class="TD2"><?= $item['description'] ?></td>
                            <td class="TD3"><?= $item['quantity'] ?></td>
                            <td class="TD4"><?= $item['price'] ?></td>
                            <td><?= $item['total'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Items from $items_SR -->
                <?php if (!empty($items_SR) && is_array($items_SR)): ?>
                    <?php foreach ($items_SR as $item): ?>
                        <tr>
                            <td class="TD1"><?= $item['item'] ?></td>
                            <td class="TD2"><?= $item['description'] ?></td>
                            <td class="TD3"><?= $item['quantity'] ?></td>
                            <td class="TD4"><?= $item['price'] ?></td>
                            <td><?= $item['total'] ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Final Amount Row -->
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><strong>Final Price =></strong></td>
                        <td><strong><?php echo $billingStatementAmount ?></strong></td>
                    </tr>
                <?php endif; ?>

                <!-- If both tables are empty, show a message -->
                <?php if (empty($items_QUO) && empty($items_SR)): ?>
                    <tr>
                        <td colspan="5">No items found in session.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>

        <div class="body1">
            <p class="TechInfoText">
                <strong><?php echo $_SESSION['dFooter_BS']['remarks'] ?? "No data found"; ?></strong></p>
        </div>
        <div class="techInfo">
            <div class="columnSR">
                <p class="TechInfoText">
                    <strong><?php echo $_SESSION['dFooter_BS']['authorizedName'] ?? "No data found"; ?></strong></p>
                <p class="TechInfoText">Authorized
                    <?php echo $_SESSION['dFooter_BS']['authorizedRole'] ?? "No data found"; ?> Signature over Printed
                    Name:</p>
            </div>
        </div>
    </div>
</body>

</html>