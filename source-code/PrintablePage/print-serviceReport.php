<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Service Report Print</title>
    <link rel="stylesheet" href="print-serviceReport.css">
</head>

<body>
    <div class="content">
        <div class="header">
            <p class="companyName"><?php echo $_SESSION['dHeader_SR']['companyName'] ?? "No data found"; ?></p>
            <p class="CompanyDetails"><?php echo $_SESSION['dHeader_SR']['companyAddress'] ?? "No data found"; ?></p>
            <p class="CompanyDetails"><?php echo $_SESSION['dHeader_SR']['companyNumber'] ?? "No data found"; ?></p>
            <p class="CompanyDetails"><?php echo $_SESSION['dHeader_SR']['companyEmail'] ?? "No data found"; ?></p>
        </div>
        <hr class="HorizontalLine">
        <p class="documentTITLE">Service Report Call</p>
        <div class="body">
            <p class="documentBody"><strong>Date:</strong> <?php echo $_SESSION['dBody_SR']['serviceReportDate'] ?? "No data found"; ?></p>
            <p class="documentBody"><strong>Customer Name:</strong> <?php echo $_SESSION['dBody_SR']['customerName'] ?? "No data found"; ?></p>
            <p class="documentBody"><strong>Location:</strong> <?php echo $_SESSION['dBody_SR']['customerLocation'] ?? "No data found"; ?></p>
        </div>
        <div class="footer">
            <table border="1" cellspacing="0" cellpadding="5">
                <tr>
                    <th>Complain:</th>
                    <th>Diagnosed:</th>
                    <th>Activity Performed:</th>
                    <th>Recommendation:</th>
                </tr>
                <tr>
                    <td><?php echo $_SESSION['dFooter_SR']['complaint'] ?? "No data found"; ?></td>
                    <td><?php echo $_SESSION['dFooter_SR']['diagnosed'] ?? "No data found"; ?></td>
                    <td><?php echo $_SESSION['dFooter_SR']['activityPerformed'] ?? "No data found"; ?></td>
                    <td><?php echo $_SESSION['dFooter_SR']['recommendation'] ?? "No data found"; ?></td>
                </tr>
            </table>
        </div>
        <div class="table">
            <?php if (!empty($_SESSION['itemsSR']) && is_array($_SESSION['itemsSR'])) : ?>
                <table border="1" cellspacing="0" cellpadding="5">
                    <tr>
                        <th>Unit</th>
                        <th>Activity Performed</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                    <?php foreach ($_SESSION['itemsSR'] as $item) : ?>
                        <tr>
                            <td><?= $item['item'] ?></td>
                            <td><?= $item['description'] ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= number_format((float)$item['price'], 2, '.', '') ?></td>
                            <td><?= number_format((float)$item['total'], 2, '.', '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><strong>Total Cost =></strong></td>
                    <td><strong><?php echo number_format((float)($_SESSION['dBody_SR']['tableTotalAmount'] ?? 0), 2, '.', ''); ?></strong></td>
                </table>
            <?php else : ?>
                <p>No items found in session.</p>
            <?php endif; ?>
        </div>
        <div class="techInfo">
            <div class="columnSR">
                <p class="TechInfoText"><strong><?php echo $_SESSION['dTechnicianInfo_SR']['preparerName'] ?? "No data found"; ?></strong></p>
                <p class="TechInfoText"><?php echo $_SESSION['dTechnicianInfo_SR']['preparerPosition'] ?? "No data found"; ?> Signature over Printed Name:</p>
            </div>
            <div class="columnSR">
                <p class="TechInfoText"><strong><?php echo $_SESSION['dTechnicianInfo_SR']['managerName'] ?? "No data found"; ?></strong></p>
                <p class="TechInfoText">Customer Signature over Printed Name:</p>
            </div>
        </div>
    </div>
</body>

</html>