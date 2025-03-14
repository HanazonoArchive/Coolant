<?php
session_start();
define('PROJECT_DB', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
include_once PROJECT_DB . "/Database/DBConnection.php";

class ServiceReport
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createServiceReport($appointmentID, $totalAmount, $newStatus)
    {
        try {
            $stmt1 = $this->conn->prepare("SELECT id FROM quotation WHERE appointment_id = :appointmentID LIMIT 1");
            $stmt1->execute(['appointmentID' => $appointmentID]);
            $quotation = $stmt1->fetch(PDO::FETCH_ASSOC);

            if (!$quotation) {
                throw new Exception("Quotation not found for this appointment.");
            }

            $quotationID = $quotation['id'];

            $stmt2 = $this->conn->prepare("SELECT id FROM service_report WHERE quotation_id = :quotationID LIMIT 1");
            $stmt2->execute(['quotationID' => $quotationID]);
            $serviceReport = $stmt2->fetch(PDO::FETCH_ASSOC);

            if ($serviceReport) {
                throw new Exception("A service report already exists for this appointment.");
            }

            $stmt3 = $this->conn->prepare("INSERT INTO service_report (quotation_id, amount) VALUES (:quotationID, :totalAmount)");
            $stmt3->execute(['quotationID' => $quotationID, 'totalAmount' => $totalAmount]);
            $serviceReportID = $this->conn->lastInsertId();
            $_SESSION['serviceReportID'] = $serviceReportID;

            if ($serviceReportID) {
                $stmt4 = $this->conn->prepare("UPDATE appointment SET status = :newStatus WHERE id = :appointmentID");
                if ($stmt4->execute(['newStatus' => $newStatus, 'appointmentID' => $appointmentID])) {
                    error_log("Service report created successfully for appointment ID: $appointmentID");
                } else {
                    throw new Exception("Failed to update appointment status.");
                }
            }
        } catch (Exception $e) {
            throw new Exception("An error occurred while creating the service report: " . $e->getMessage());
        }
    }

    public function createServiceReportTableData($data)
    {
        try {
            if (!isset($_SESSION['serviceReportID'])) {
                throw new Exception("Service report ID not found in the session.");
            }

            $serviceReportID = $_SESSION['serviceReportID'];
            $jsonData = json_encode($data);

            $stmt1 = $this->conn->prepare("SELECT id FROM service_report_data WHERE service_report_id = :serviceReportID LIMIT 1");
            $stmt1->execute(['serviceReportID' => $serviceReportID]);
            $serviceReportData = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($serviceReportData) {
                $stmt2 = $this->conn->prepare("UPDATE service_report_data SET data = :data WHERE service_report_id = :serviceReportID");
            } else {
                $stmt2 = $this->conn->prepare("INSERT INTO service_report_data (service_report_id, data) VALUES (:serviceReportID, :data)");
            }
            $stmt2->execute(['serviceReportID' => $serviceReportID, 'data' => $jsonData]);
        } catch (Exception $e) {
            throw new Exception("An error occurred while creating the service report table data: " . $e->getMessage());
        }
    }
}

// Process the request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (isset($data["action"]) && $data["action"] === "serviceReportDATA") {
        try {
            $conn = Database::getInstance();
            $conn->beginTransaction();

            $serviceReportHandler = new ServiceReport($conn);

            // Database DATA INFORMATION
            $appointmentID = trim(string: $data["appointmentID"] ?? "");
            $totalAmount = trim($data["totalAmount"] ?? "");
            $newStatus = trim($data["status"] ?? "");

            $documentData = $data["document"] ?? []; // Get the document data safely
            $_SESSION['dHeader_SR'] = $documentData["header"] ?? [];
            $_SESSION['dBody_SR'] = $documentData["body"] ?? [];
            $_SESSION['dFooter_SR'] = $documentData["footer"] ?? [];
            $_SESSION['dTechnicianInfo_SR'] = $documentData["technicianInfo"] ?? [];

            // Call the method with employee IDs dynamically
            $serviceReportHandler->createServiceReport(
                $appointmentID,
                $totalAmount,
                $newStatus
            );

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
    } elseif (isset($data["action"]) && $data["action"] === "serviceReportTABLE") {
        try {
            $conn = Database::getInstance();
            $conn->beginTransaction();

            $serviceReportHandler = new ServiceReport($conn);

            $items = $data["items"] ?? [];

            // Store items separately
            $_SESSION['itemsSR'] = $items;
            $_SESSION['data_SR'] = $data;

            if (isset($data)) {
                $serviceReportHandler->createServiceReportTableData($data);
            } else {
                throw new Exception("Failed to store data in session.");
            }

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Quotation items processed successfully", "items" => $items]);
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request."]);
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
                    appointment.status AS Appointment_Status
                FROM appointment
                JOIN customer ON appointment.customer_id = customer.id WHERE appointment.status = 'Working'
                $order");

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($results) {
                echo "<table border='1' class='appointment-table'>";
                echo "<th>Customer Name</th><th>Address</th><th>Appointment ID</th><th>Category</th><th>Date</th><th>Status</th></tr>";

                foreach ($results as $row) {
                    echo "<tr onclick='updateDetails(" . htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') . ")'>";
                    echo "<td>{$row['Customer_Name']}</td><td>{$row['Customer_Address']}</td><td>{$row['Appointment_ID']}</td><td>{$row['Appointment_Category']}</td><td>{$row['Appointment_Date']}</td><td>{$row['Appointment_Status']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No Working Orders found.";
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
            WHERE appointment.status = 'Working'
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
