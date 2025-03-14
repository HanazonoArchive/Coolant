<?php
session_start();
define('PROJECT_DB', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
include_once PROJECT_DB . "/Database/DBConnection.php";

class BillingStatement
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createBillingStatement($appointmentID)
    {
        try {
            // Get the ID and amount from the quotation using appointment ID
            $stmt1 = $this->conn->prepare("SELECT id, amount FROM quotation WHERE appointment_id = :appointmentID LIMIT 1");
            $stmt1->execute(['appointmentID' => $appointmentID]);
            $quotation = $stmt1->fetch(PDO::FETCH_ASSOC);

            if (!$quotation) {
                throw new Exception("No quotation found for this appointment.");
            }

            $quotationID = $quotation['id'];
            $quotationAmount = $quotation['amount'] ?? 0;

            // Get the ID and amount from the service report using quotation ID
            $stmt2 = $this->conn->prepare("SELECT id, amount FROM service_report WHERE quotation_id = :quotationID LIMIT 1");
            $stmt2->execute(['quotationID' => $quotationID]);
            $serviceReport = $stmt2->fetch(PDO::FETCH_ASSOC);

            if (!$serviceReport) {
                throw new Exception("No service report found for this quotation.");
            }

            $serviceReportID = $serviceReport['id'];
            $serviceReportAmount = $serviceReport['amount'] ?? 0;

            // Check if the billing statement already exists
            $stmt3 = $this->conn->prepare("SELECT id, amount FROM billing_statement WHERE quotation_id = :quotationID AND service_report_id = :serviceReportID LIMIT 1");
            $stmt3->execute(['quotationID' => $quotationID, 'serviceReportID' => $serviceReportID]);
            $billingStatement = $stmt3->fetch(PDO::FETCH_ASSOC);

            if ($billingStatement) {
                error_log("Billing Statement already exists!");

                // Store existing billing statement details in session
                $_SESSION['BillingStatementID'] = $billingStatement['id'];
                $_SESSION['Amount_BS'] = $billingStatement['amount'];
                $_SESSION['QuotationID_BS'] = $quotationID;
                $_SESSION['QuotationAmount_BS'] = $quotationAmount;
                $_SESSION['ServiceReportID_BS'] = $serviceReportID;
                $_SESSION['ServiceReportAmount_BS'] = $serviceReportAmount;

                error_log("Existing billing statement info has been sessioned.");
                return;
            }

            // Calculate new total amount
            $newTotalAmount = $quotationAmount + $serviceReportAmount;

            // Insert into billing statement
            $stmt4 = $this->conn->prepare("INSERT INTO billing_statement (quotation_id, service_report_id, amount) VALUES (:quotationID, :serviceReportID, :newAmount)");
            $stmt4->execute([
                'quotationID' => $quotationID,
                'serviceReportID' => $serviceReportID,
                'newAmount' => $newTotalAmount
            ]);

            $billingStatementID = $this->conn->lastInsertId();
            $defaultStatus = "Pending";

            // Store new billing statement details in session
            $_SESSION['BillingStatementID'] = $billingStatementID;
            $_SESSION['Amount_BS'] = $newTotalAmount;
            $_SESSION['QuotationID_BS'] = $quotationID;
            $_SESSION['QuotationAmount_BS'] = $quotationAmount;
            $_SESSION['ServiceReportID_BS'] = $serviceReportID;
            $_SESSION['ServiceReportAmount_BS'] = $serviceReportAmount;

            error_log("All the info has been sessioned! (New Billing Statement)");

            // Insert into pending collection if amount is greater than 0
            if ($newTotalAmount > 0) {
                $stmt5 = $this->conn->prepare("INSERT INTO pending_collection (billing_statement_id, amount, status) VALUES (:billingStatementID, :newAmount, :newStatus)");
                $stmt5->execute([
                    'billingStatementID' => $billingStatementID,
                    'newAmount' => $newTotalAmount,
                    'newStatus' => $defaultStatus
                ]);
            }

            // Update Days
            $stmtUpdateDays = $this->conn->prepare("UPDATE employee SET days_of_work = days_of_work + 1 WHERE id IN (SELECT employee_id FROM employee_log WHERE appointment_id = :appointmentID)");
            $stmtUpdateDays->execute(['appointmentID' => $appointmentID]);
        } catch (Exception $e) {
            error_log("Error creating service report: " . $e->getMessage());
            throw new Exception("An error occurred while creating the service report.");
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (isset($data["action"]) && $data["action"] === "billingStatementDATA") {
        try {
            $conn = Database::getInstance();
            $conn->beginTransaction();

            $billingStatementHandler = new BillingStatement($conn);

            // Database DATA INFORMATION
            $appointmentID = trim($data["appointmentID"] ?? "");

            $documentData = $data["document"] ?? []; // Get the document data safely
            $_SESSION['dHeader_BS'] = $documentData["header"] ?? [];
            $_SESSION['dBody_BS'] = $documentData["body"] ?? [];
            $_SESSION['dFooter_BS'] = $documentData["footer"] ?? [];

            $billingStatementHandler->createBillingStatement($appointmentID);

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Quotation created successfully"]);
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request."]);
            exit;
        }
    } elseif (isset($data["action"]) && $data["action"] === "PendingCollection") {
        try {

            $pendingCollectionID = $data["collection_ID"] ?? null;
            $pendingCollectionStatus = $data["collectionStatus"] ?? null;

            if (!$pendingCollectionID) {
                echo json_encode(["status" => "error", "message" => "Invalid collection ID."]);
                exit;
            }

            $conn = Database::getInstance();

            $stmt = $conn->prepare("UPDATE pending_collection SET status = :newStatus WHERE id = :collectionID");
            $stmt->execute(['newStatus' => $pendingCollectionStatus, 'collectionID' => $pendingCollectionID]);

            if (!$stmt->rowCount() > 0) {
                echo json_encode(["status" => "error", "message" => "No matching collection found.", "reload" => false]);
            }

            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Appointment cancelled successfully.", "reload" => true]);
            exit;
        } catch (Exception $e) {
            error_log("Error cancelling the appointment: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Error cancelling appointment."]);
            exit;
        }
    }
}

class AppointmentManager
{
    private $conn;
    private $default_order = "ORDER BY appointment.id ASC";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function fetchAppointments($order = null)
    {
        try {
            $order = $order ?? $this->default_order;

            $stmt = $this->conn->prepare("SELECT
                customer.name AS Customer_Name,
                customer.address AS Customer_Address,
                appointment.id AS Appointment_ID,
                appointment.category AS Appointment_Category,
                appointment.date AS Appointment_Date,
                appointment.status AS Appointment_Status,
                (SELECT COUNT(*) FROM billing_statement 
                 WHERE billing_statement.quotation_id IN 
                      (SELECT id FROM quotation WHERE quotation.appointment_id = appointment.id) 
                   OR billing_statement.service_report_id IN 
                      (SELECT id FROM service_report WHERE service_report.quotation_id IN 
                          (SELECT id FROM quotation WHERE quotation.appointment_id = appointment.id))
                ) AS Billing_Statement_Count
            FROM appointment
            JOIN customer ON appointment.customer_id = customer.id 
            WHERE appointment.status = 'Completed'
            $order");

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($results) {
                echo "<table border='1' class='appointment-table'>";
                echo "<th>Customer Name</th><th>Address</th><th>Appointment ID</th><th>Category</th><th>Date</th><th>Status</th><th>Has Billing Statement</th></tr>";

                foreach ($results as $row) {
                    $hasBillingStatement = ($row['Billing_Statement_Count'] > 0) ? 'Yes' : 'No';

                    echo "<tr onclick='updateDetails(" . htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') . ")'>";
                    echo "<td>{$row['Customer_Name']}</td><td>{$row['Customer_Address']}</td><td>{$row['Appointment_ID']}</td><td>{$row['Appointment_Category']}</td><td>{$row['Appointment_Date']}</td><td>{$row['Appointment_Status']}</td><td>{$hasBillingStatement}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No Completed Work Orders found.";
            }
        } catch (PDOException $e) {
            echo "Error fetching data: " . $e->getMessage();
        }
    }

    public function fetchPendingCollection($order = null)
    {
        try {
            $order = $order ?? $this->default_order;

            $stmt = $this->conn->prepare("SELECT
            customer.name AS Name,
            customer.address AS Address,
            appointment.id AS `App. ID`,
            appointment.category AS Category,
            appointment.date AS Date,
            appointment.status AS Status,
            billing_statement.id AS `Statement ID`,
            billing_statement.amount AS Amt,
            pending_collection.status AS `Collection Status`
        FROM appointment
        JOIN customer ON appointment.customer_id = customer.id
        LEFT JOIN quotation ON quotation.appointment_id = appointment.id
        LEFT JOIN billing_statement ON billing_statement.quotation_id = quotation.id
        LEFT JOIN pending_collection ON pending_collection.billing_statement_id = billing_statement.id
        WHERE appointment.status = 'Completed' $order");

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($results) {
                echo "<table border='1' class='appointment-table'>";
                echo "<tr><th>Name</th><th>Address</th><th>App. ID</th><th>Category</th><th>Date</th><th>Status</th><th>Statement ID</th><th>Amt.</th><th>Collection Status</th></tr>";

                foreach ($results as $row) {
                    echo "<tr onclick='updateDetails(" . htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') . ")'>";
                    echo "<td>{$row['Name']}</td><td>{$row['Address']}</td><td>{$row['App. ID']}</td><td>{$row['Category']}</td><td>{$row['Date']}</td><td>{$row['Status']}</td><td>{$row['Statement ID']}</td><td>{$row['Amt']}</td><td>{$row['Collection Status']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No Completed Work Orders found.";
            }
        } catch (PDOException $e) {
            echo "Error fetching data: " . $e->getMessage();
        }
    }

    public function fetchAppointmentIDs()
    {
        try {
            $stmt = $this->conn->prepare("
            SELECT appointment.id, customer.name 
            FROM appointment
            JOIN customer ON appointment.customer_id = customer.id
            WHERE appointment.status = 'Completed'
            ORDER BY appointment.id ASC
        ");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($results);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error fetching appointment IDs: " . $e->getMessage()]);
        }
    }

    public function fetchPendingCollectionIDs()
    {
        try {
            $stmt = $this->conn->prepare("
            SELECT pending_collection.id, customer.name 
            FROM pending_collection
            JOIN billing_statement ON pending_collection.billing_statement_id = billing_statement.id
            JOIN quotation ON billing_statement.quotation_id = quotation.id
            JOIN appointment ON quotation.appointment_id = appointment.id
            JOIN customer ON appointment.customer_id = customer.id
            ORDER BY pending_collection.id ASC
        ");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($results);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error fetching appointment IDs: " . $e->getMessage()]);
        }
    }
}

if (isset($_GET['fetch_pending_collection'])) {
    $conn = Database::getInstance();
    $appointmentManager = new AppointmentManager($conn);
    $appointmentManager->fetchPendingCollectionIDs(); // Calls the function to output JSON
    exit; // Stop further execution
}


// Check if request is made to fetch appointment IDs
if (isset($_GET['fetch_appointments'])) {
    $conn = Database::getInstance();
    $appointmentManager = new AppointmentManager($conn);
    $appointmentManager->fetchAppointmentIDs(); // Calls the function to output JSON
    exit; // Stop further execution
}

// Initialize database connection
$conn = Database::getInstance();
$appointmentManager = new AppointmentManager($conn);
