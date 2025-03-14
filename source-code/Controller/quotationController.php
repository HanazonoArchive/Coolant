<?php
session_start();
define('PROJECT_DATABASE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
include_once PROJECT_DATABASE_PATH . '/Database/DBConnection.php';

class Quotation
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createQuotation($employeeID1, $employeeID2, $employeeID3, $appointmentID, $totalAmount, $newStatus)
    {
        try {
            $stmt1 = $this->conn->prepare("SELECT id FROM quotation WHERE appointment_id = :appointmentID LIMIT 1");
            $stmt1->execute(['appointmentID' => $appointmentID]);
            $quotation = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($quotation) {
                throw new Exception("[quotationController]: Quotation already exists for this appointment");
            } else {
                $stmt2 = $this->conn->prepare("INSERT INTO quotation (appointment_id, amount) VALUES (:appointmentID, :totalAmount)");
                $stmt2->execute(['appointmentID' => $appointmentID, 'totalAmount' => $totalAmount]);
                $quotationID = $this->conn->lastInsertId();

                $_SESSION['quotationID_QUO'] = $quotationID;
            }

            if (isset($quotationID) && $quotationID) {
                $stmt3 = $this->conn->prepare("UPDATE appointment SET status = :newStatus WHERE id = :appointmentID");
                $stmt3->execute(['newStatus' => $newStatus, 'appointmentID' => $appointmentID]);
            } else {
                throw new Exception("[quotationController]: An error occurred while creating Quotation - Quotation ID not found");
            }

            $stmt4 = $this->conn->prepare("INSERT INTO employee_log (employee_id, appointment_id) VALUES (:employee_id, :appointment_id)");
            $uniqueEmployees = array_unique([$employeeID1, $employeeID2, $employeeID3]);

            foreach ($uniqueEmployees as $employeeID) {
                $stmtCheck = $this->conn->prepare("SELECT COUNT(*) FROM employee_log WHERE employee_id = :employee_id AND appointment_id = :appointment_id");
                $stmtCheck->execute(['employee_id' => $employeeID, 'appointment_id' => $appointmentID]);
                $exist = $stmtCheck->fetchColumn();

                if ($exist == 0) {
                    $stmt4->execute(['employee_id' => $employeeID, 'appointment_id' => $appointmentID]);
                } else {
                    throw new Exception("[quotationController]: Employee already assigned to this appointment");
                }
            }
            return true;
        } catch (Exception $e) {
            error_log("Error creating Quotation: " . $e->getMessage());
            throw new Exception("[quotationController]: An error occurred while creating Quotation");
        }
    }

    public function createQuotationTableData($data)
    {
        try {
            $quotationID = $_SESSION['quotationID_QUO'];

            if (!$quotationID) {
                throw new Exception("[quotationController]: Quotation ID not found");
            } else {
                $stmt1 = $this->conn->prepare("SELECT id FROM quotation_data WHERE quotation_id = :id LIMIT 1");
                $stmt1->execute(['id' => $quotationID]);
                $quotationDataID = $stmt1->fetch(PDO::FETCH_ASSOC);
            }

            if ($quotationDataID !== false) {
                throw new Exception("[quotationController]: Quotation Data already exists for this Quotation");
            } else {
                $data_encoded = json_encode($data);
                $stmt2 = $this->conn->prepare("INSERT INTO quotation_data (quotation_id, data) VALUES (:quotationID, :jsonData)");
                $stmt2->execute(['quotationID' => $quotationID, 'jsonData' => $data_encoded]);
                $stmt2Check = $stmt2->rowCount() > 0;

                if ($stmt2Check) {
                    return true;
                } else {
                    throw new Exception("[quotationController]: An error occurred while creating Quotation Data");
                }
            }
        } catch (Exception $e) {
            error_log("Error creating Quotation Data: " . $e->getMessage());
            throw new Exception("[quotationController]: An error occurred while creating Quotation Data");
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (isset($data["action"]) && $data["action"] === "quotationDATA") {
        try {
            $conn = Database::getInstance();
            $conn->beginTransaction();

            $quotationHandler = new Quotation($conn);

            // Database DATA INFORMATION
            $appointmentID = $data['appointmentID'];
            $employees = $data['employees'];
            $totalAmount = $data['totalAmount'];
            $newStatus = $data['status'];

            $documentData = $data['document'];
            $_SESSION['dHeader'] = $documentData['header'];
            $_SESSION['dBody'] = $documentData['body'];
            $_SESSION['dFooter'] = $documentData['footer'];
            $_SESSION['dTechnicianInfo'] = $documentData['technicianInfo'];

            if (empty($employees)) {
                throw new Exception("[quotationController]: Employees not found");
            }

            $quotationHandler->createQuotation(
                $employees[0],
                $employees[1],
                $employees[2],
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
            error_log("Error creating Quotation: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "An error occurred while creating Quotation"]);
            exit;
        }
    } elseif (isset($data["action"]) && $data["action"] === "quotationTABLE") {
        try {
            $conn = Database::getInstance();
            $conn->beginTransaction();

            $quotationHandler = new Quotation($conn);

            $items = $data["items"];

            $_SESSION['items'] = $items;
            $_SESSION['data_QUO'] = $data;

            if (isset($data)) {
                $quotationHandler->createQuotationTableData($data);
            } else {
                throw new Exception("[quotationTable]: An error occurred while creating Quotation - Data not found");
            }

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Quotation items processed successfully", "items" => $items]);
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Error creating Quotation Table Data: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "An error occurred while creating Quotation Table Data"]);
            exit;
        }
    } elseif (isset($data["action"]) && $data["action"] === "cancel") {
        try {
            $conn = Database::getInstance();
            $conn->beginTransaction();

            $appointmentID = $data['appointment_ID'];

            $stmt = $conn->prepare("UPDATE appointment SET status = 'Cancelled' WHERE id = :appointmentID");
            $stmt->execute(['appointmentID' => $appointmentID]);

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Quotation cancelled successfully"]);
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Error cancelling Quotation: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "An error occurred while cancelling Quotation"]);
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
                JOIN customer ON appointment.customer_id = customer.id WHERE appointment.status = 'Pending'
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
                echo "No Pending Work Orders found.";
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
            WHERE appointment.status = 'Pending'
            ORDER BY appointment.id ASC
        ");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($results);
            exit;
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "Error fetching appointment IDs: " . $e->getMessage()]);
            exit;
        }
    }

    public function fetchEmployeeIDs()
    {
        try {
            $stmt = $this->conn->prepare("
            SELECT employee.id, employee.name, employee.role
            FROM employee ORDER BY employee.id ASC
        ");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($results);
            exit;
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            exit;
            echo json_encode(["error" => "Error fetching appointment IDs: " . $e->getMessage()]);
        }
    }
}

// Check if request is made to fetch appointment IDs
if (isset($_GET['fetch_Employee'])) {
    $conn = Database::getInstance();
    $appointmentManager = new AppointmentManager($conn);
    $appointmentManager->fetchEmployeeIDs(); // Calls the function to output JSON
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
