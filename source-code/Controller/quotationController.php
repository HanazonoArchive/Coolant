<?php
session_start();
define('PROJECT_DB', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
include_once PROJECT_DB . "/Database/DBConnection.php";

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
            // Check if a quotation already exists for this appointment
            $stmt1 = $this->conn->prepare("SELECT id FROM quotation WHERE appointment_id = :id LIMIT 1");
            $stmt1->execute(['id' => $appointmentID]);
            $quotation = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($quotation) {
                error_log("Quotation for Appointment ID $appointmentID already exists.");
            } else {
                error_log("Creating new quotation for Appointment ID: $appointmentID");

                // Insert the new quotation
                $stmt2 = $this->conn->prepare("INSERT INTO quotation (appointment_id, amount) VALUES (:appointment_id, :totalAmount)");
                $stmt2->execute(['appointment_id' => $appointmentID, 'totalAmount' => $totalAmount]);
                $quotationID = $this->conn->lastInsertId();

                $_SESSION['quotationID_QUO'] = $quotationID; // Store the quotation ID in session

                if ($quotationID) {
                    // Update appointment status
                    $stmt3 = $this->conn->prepare("UPDATE appointment SET status = :newStatus WHERE id = :appointmentID");
                    if ($stmt3->execute(['newStatus' => $newStatus, 'appointmentID' => $appointmentID])) {
                        error_log("Appointment ID $appointmentID updated successfully.");

                        if (isset($_SESSION['data_QUO'])) {
                            error_log("Data successfully stored in session.");
                            $data = $_SESSION['data_QUO'];
                            $this->createQuotationTableData($data);

                            // Prepare insert statement for employee_log
                            $stmt4 = $this->conn->prepare("INSERT INTO employee_log (employee_id, appointment_id) VALUES (:employee_id, :appointment_id)");

                            // Get unique employee IDs
                            $uniqueEmployees = array_unique([$employeeID1, $employeeID2, $employeeID3]);

                            // Insert only employees who are not already logged for this appointment
                            foreach ($uniqueEmployees as $employeeID) {
                                $stmtCheck = $this->conn->prepare("SELECT COUNT(*) FROM employee_log WHERE employee_id = :employee_id AND appointment_id = :appointment_id");
                                $stmtCheck->execute(['employee_id' => $employeeID, 'appointment_id' => $appointmentID]);
                                $exists = $stmtCheck->fetchColumn();

                                if ($exists == 0) {
                                    // Only insert if the employee is not already logged for this appointment
                                    $stmt4->execute(['employee_id' => $employeeID, 'appointment_id' => $appointmentID]);
                                }
                            }
                        } else {
                            error_log("Failed to store data in session.");
                            throw new Exception("Failed to store data in session.");
                        }
                    } else {
                        error_log("Failed to update appointment status.");
                    }
                } else {
                    error_log("Failed to insert quotation.");
                }
            }
        } catch (Exception $e) {
            error_log("Error creating quotation: " . $e->getMessage());
            throw new Exception("An error occurred while creating the quotation.");
        }
    }


    public function createQuotationTableData($data)
    {
        try {
            // 1st: Check if the Quotation ID exists
            $quotationID = $_SESSION['quotationID_QUO'];
            if (!$quotationID) {
                error_log("Quotation ID is not found.");
                throw new Exception("Quotation ID is required.");
            } else {
                error_log("Quotation ID found: $quotationID");

                // 2nd: Check if the data for Quotation exists
                $stmt1 = $this->conn->prepare("SELECT id FROM quotation_data WHERE quotation_id = :id LIMIT 1");
                $stmt1->execute(['id' => $quotationID]);
                $quotationdataID = $stmt1->fetch(PDO::FETCH_ASSOC);

                if ($quotationdataID !== false) {
                    error_log("Quotation data already exists.");
                } else {
                    // 3rd: Create a new quotation data
                    error_log("Creating new quotation data for quotation ID: $quotationID");

                    $data = json_encode($data); // Convert the data to JSON
                    $stmt2 = $this->conn->prepare("INSERT INTO quotation_data (quotation_id, data) VALUES (:quotation_id, :jsonData)");
                    if ($stmt2->execute(['quotation_id' => $quotationID, 'jsonData' => $data])) {
                        error_log("Quotation data ID created successfully.");
                    } else {
                        error_log("Failed to insert quotation data.");
                    }
                    if ($stmt2->rowCount() > 0) {
                        error_log("Quotation data ID $quotationdataID created successfully.");
                    } else {
                        error_log("Failed to insert quotation data.");
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Error creating quotation table data: " . $e->getMessage());
            throw new Exception("An error occurred while creating the quotation table data.");
        }
    }
}


// Process the request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (isset($data["action"]) && $data["action"] === "quotationDATA") {
        try {
            $conn = Database::getInstance();
            $conn->beginTransaction();

            $quotationHandler = new Quotation($conn);

            // Database DATA INFORMATION
            $appointmentID = trim($data["appointmentID"] ?? "");
            $employees = $data["employees"] ?? [];
            $totalAmount = trim($data["totalAmount"] ?? "");
            $newStatus = trim($data["status"] ?? "");

            $documentData = $data["document"] ?? []; // Get the document data safely
            $_SESSION['dHeader'] = $documentData["header"] ?? [];
            $_SESSION['dBody'] = $documentData["body"] ?? [];
            $_SESSION['dFooter'] = $documentData["footer"] ?? [];
            $_SESSION['dTechnicianInfo'] = $documentData["technicianInfo"] ?? [];

            // Ensure at least one employee ID is provided
            if (empty($employees)) {
                throw new Exception("At least one employee ID is required.");
            }

            // Call the method with employee IDs dynamically
            $quotationHandler->createQuotation(
                $employees[0] ?? "",
                $employees[1] ?? "",
                $employees[2] ?? "",
                $appointmentID,
                $totalAmount,
                $newStatus
            );

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Quotation created successfully"]);
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                "status" => "error",
                "message" => "Failed to process the request.",
                "error" => $e->getMessage()
            ]);
        }
    } elseif (isset($data["action"]) && $data["action"] === "quotationTABLE") {
        try {
            $conn = Database::getInstance();

            $items = $data["items"] ?? [];

            if (ob_get_contents()) {
                ob_end_clean();
            }

            // Store items separately
            $_SESSION['items'] = $items;
            $_SESSION['data_QUO'] = $data;

            header('Content-Type: application/json');
            echo json_encode([
                "status" => "success",
                "message" => "Quotation items processed successfully",
                "items" => $items
            ]);
            exit;
        } catch (Exception $e) {
            if (ob_get_length()) {
                ob_clean();
            }

            header('Content-Type: application/json');
            echo json_encode([
                "status" => "error",
                "message" => "Failed to process the request.",
                "error" => $e->getMessage()
            ]);
            exit;
        }
    } elseif (isset($data["action"]) && $data["action"] === "cancel") {
        try {
            // Extract appointment_ID from received data
            $appointmentID = $data["appointment_ID"] ?? null;
    
            // Validate the received appointment ID
            if (!$appointmentID) {
                echo json_encode(["status" => "error", "message" => "Invalid appointment ID."]);
                exit;
            }
    
            $conn = Database::getInstance();
    
            // Execute a DELETE or UPDATE query depending on the cancellation logic
            $stmt = $conn->prepare("UPDATE appointment SET status = 'Cancelled' WHERE id = :appointmentID");
            $stmt->execute(['appointmentID' => $appointmentID]);
    
            // Check if any row was affected
            if ($stmt->rowCount() > 0) {
                echo json_encode(["status" => "success", "message" => "Appointment cancelled successfully.", "reload" => true]);
            } else {
                echo json_encode(["status" => "error", "message" => "No matching appointment found.", "reload" => false]);
            }            
        } catch (Exception $e) {
            error_log("Error cancelling the appointment: " . $e->getMessage());
            echo json_encode(["status" => "error", "message" => "Error cancelling appointment.", "error" => $e->getMessage()]);
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
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error fetching appointment IDs: " . $e->getMessage()]);
        }
    }

    public function fetchEmployeeIDs()
    {
        try {
            $stmt = $this->conn->prepare("
            SELECT employee.id, employee.name 
            FROM employee ORDER BY employee.id ASC
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
