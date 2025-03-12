<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Quotation Print</title>
    <link rel="stylesheet" href="print-quotation.css">
</head>

<body>
    <div class="content">
        <div class="header">
            <p class="companyName"><?php echo $_SESSION['dHeader']['companyName'] ?? "No data found"; ?></p>
            <p class="CompanyDetails"><?php echo $_SESSION['dHeader']['companyAddress'] ?? "No data found"; ?></p>
            <p class="CompanyDetails"><?php echo $_SESSION['dHeader']['companyNumber'] ?? "No data found"; ?></p>
            <p class="CompanyDetails"><?php echo $_SESSION['dHeader']['companyEmail'] ?? "No data found"; ?></p>
        </div>
        <hr class="HorizontalLine">
        <p class="documentTITLE">Quotation</p>
        <div class="body">
            <p class="documentBody"><strong>Date:</strong> <?php echo $_SESSION['dBody']['quotationDate'] ?? "No data found"; ?></p>
            <p class="documentBody"><strong>Customer Name:</strong> <?php echo $_SESSION['dBody']['customerName'] ?? "No data found"; ?></p>
            <p class="documentBody"><strong>Location:</strong> <?php echo $_SESSION['dBody']['customerLocation'] ?? "No data found"; ?></p>
            <p class="documentBody"><Strong>Quotation for:</Strong> <?php echo $_SESSION['dBody']['customerDetails'] ?? "No data found"; ?></p>
        </div>
        <div class="table">
            <?php if (!empty($_SESSION['items']) && is_array($_SESSION['items'])) : ?>
                <table border="1" cellspacing="0" cellpadding="5">
                    <tr>
                        <th>Item</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                    <?php foreach ($_SESSION['items'] as $item) : ?>
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
                    <td><strong><?php echo number_format((float)($_SESSION['dBody']['tableTotalAmmount'] ?? 0), 2, '.', ''); ?></strong></td>
                </table>
            <?php else : ?>
                <p>No items found in session.</p>
            <?php endif; ?>
        </div>
        <div class="footer">
            <p class="Details"><?php echo $_SESSION['dFooter']['details1'] ?? "No data found"; ?></p>
            <p class="Details"><?php echo $_SESSION['dFooter']['details2'] ?? "No data found"; ?></p>
            <p class="Details"><?php echo $_SESSION['dFooter']['details3'] ?? "No data found"; ?></p>
            <p class="Details"><?php echo $_SESSION['dFooter']['details4'] ?? "No data found"; ?></p>
        </div>
        <div class="techInfo">
            <div style="width: 100%; display: flex; justify-content: space-between; 
            flex-direction: column; align-items: center; flex-wrap: nowrap;">
                <p class="TechInfoText">Prepared by:</p>
                <p class="TechInfoText"><strong><?php echo $_SESSION['dTechnicianInfo']['namePreparer'] ?? "No data found"; ?></strong></p>
                <p class="TechInfoText"><strong><?php echo $_SESSION['dTechnicianInfo']['positionPreparer'] ?? "No data found"; ?></strong></p>
                <p class="TechInfoText"><strong><?php echo $_SESSION['dTechnicianInfo']['nameManager'] ?? "No data found"; ?></strong></p>
                <p class="TechInfoText"><strong><?php echo $_SESSION['dTechnicianInfo']['positionManager'] ?? "No data found"; ?></strong></p>
            </div>
        </div>
    </div>
</body>

</html>