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

    public function createQuotation($employees, $appointmentID, $totalAmount, $newStatus)
    {
        try {
            $stmt1 = $this->conn->prepare("SELECT id FROM quotation WHERE appointment_id = :appointmentID LIMIT 1");
            $stmt1->execute(['appointmentID' => $appointmentID]);
            $quotation = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($quotation) {
                throw new Exception("[quotationController]: Quotation already exists for this appointment");
            }

            $stmt2 = $this->conn->prepare("INSERT INTO quotation (appointment_id, amount) VALUES (:appointmentID, :totalAmount)");
            $stmt2->execute(['appointmentID' => $appointmentID, 'totalAmount' => $totalAmount]);
            $quotationID = $this->conn->lastInsertId();
            $_SESSION['quotationID_QUO'] = $quotationID;

            $stmt3 = $this->conn->prepare("UPDATE appointment SET status = :newStatus WHERE id = :appointmentID");
            $stmt3->execute(['newStatus' => $newStatus, 'appointmentID' => $appointmentID]);

            if (isset($_SESSION['data_QUO'])) {
                $this->createQuotationTableData($_SESSION['data_QUO']);
                $this->logEmployees($employees, $appointmentID);
            } else {
                throw new Exception("[quotationController]: Data not found");
            }
            return true;
        } catch (Exception $e) {
            error_log("Error creating Quotation: " . $e->getMessage());
            throw new Exception("[quotationController]: An error occurred while creating Quotation");
        }
    }

    private function createQuotationTableData($data)
    {
        try {
            $quotationID = $_SESSION['quotationID_QUO'] ?? null;
            if (!$quotationID) {
                throw new Exception("[quotationController]: Quotation ID not found");
            }

            $stmt1 = $this->conn->prepare("SELECT id FROM quotation_data WHERE quotation_id = :id LIMIT 1");
            $stmt1->execute(['id' => $quotationID]);
            if ($stmt1->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("[quotationController]: Quotation Data already exists");
            }

            $stmt2 = $this->conn->prepare("INSERT INTO quotation_data (quotation_id, data) VALUES (:quotationID, :jsonData)");
            $stmt2->execute(['quotationID' => $quotationID, 'jsonData' => json_encode($data)]);
        } catch (Exception $e) {
            error_log("Error creating Quotation Data: " . $e->getMessage());
            throw new Exception("[quotationController]: An error occurred while creating Quotation Data");
        }
    }

    private function logEmployees($employees, $appointmentID)
    {
        try {
            $stmt = $this->conn->prepare("INSERT INTO employee_log (employee_id, appointment_id) VALUES (:employee_id, :appointment_id)");
            foreach (array_unique($employees) as $employeeID) {
                $stmtCheck = $this->conn->prepare("SELECT COUNT(*) FROM employee_log WHERE employee_id = :employee_id AND appointment_id = :appointment_id");
                $stmtCheck->execute(['employee_id' => $employeeID, 'appointment_id' => $appointmentID]);
                if ($stmtCheck->fetchColumn() == 0) {
                    $stmt->execute(['employee_id' => $employeeID, 'appointment_id' => $appointmentID]);
                }
            }
        } catch (Exception $e) {
            error_log("Error logging employees: " . $e->getMessage());
            throw new Exception("[quotationController]: An error occurred while logging employees");
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    try {
        $conn = Database::getInstance();

        if ($data["action"] === "quotationDATA") {
            $conn->beginTransaction();
            $quotationHandler = new Quotation($conn);

            $_SESSION['dHeader'] = $data['document']['header'];
            $_SESSION['dBody'] = $data['document']['body'];
            $_SESSION['dFooter'] = $data['document']['footer'];
            $_SESSION['dTechnicianInfo'] = $data['document']['technicianInfo'];

            if (empty($data['employees'])) {
                throw new Exception("[quotationController]: Employees not found");
            }

            $quotationHandler->createQuotation(
                $data['employees'],
                $data['appointmentID'],
                $data['totalAmount'],
                $data['status']
            );
            $conn->commit();
            echo json_encode(["status" => "success", "message" => "Quotation created successfully"]);
            exit;
        }

        if ($data["action"] === "quotationTABLE") {
            $_SESSION['items'] = $data["items"];
            $_SESSION['data_QUO'] = $data;
            echo json_encode(["status" => "success", "message" => "Quotation items processed successfully", "items" => $data["items"]]);
            exit;
        }

        if ($data["action"] === "cancel") {
            $conn->beginTransaction();
            $stmt = $conn->prepare("UPDATE appointment SET status = 'Cancelled' WHERE id = :appointmentID");
            $stmt->execute(['appointmentID' => $data['appointment_ID']]);
            $conn->commit();
            echo json_encode(["status" => "success", "message" => "Quotation cancelled successfully"]);
            exit;
        }
    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        error_log("Error: " . $e->getMessage());
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        exit;
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
