<?php
define('PROJECT_DB', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
include PROJECT_DB . "/Database/DBConnection.php";

class Customer
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function findOrCreateCustomer($name, $contact_number, $address)
    {
        try {
            // Check if the customer exists based on name and contact number only
            $stmt = $this->conn->prepare("SELECT id FROM customer WHERE name = :name AND contact_number = :contact_number LIMIT 1");
            $stmt->execute(['name' => $name, 'contact_number' => $contact_number]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($customer) {
                return $customer['id'];
            } else {
                $stmt = $this->conn->prepare("INSERT INTO customer (name, contact_number, address) VALUES (:name, :contact_number, :address)");
                $stmt->execute(['name' => $name, 'contact_number' => $contact_number, 'address' => $address]);
                return $this->conn->lastInsertId();
            }
        } catch (Exception $e) {
            error_log("Error handling customer: " . $e->getMessage());
            throw new Exception("An error occurred while processing customer data.");
        }
    }
}

class Appointment
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function createAppointment($customer_id, $date, $category, $priority, $status)
    {
        try {
            $stmt = $this->conn->prepare("INSERT INTO appointment (customer_id, date, category, priority, status) VALUES (:customer_id, :date, :category, :priority, :status)");
            $stmt->execute([
                'customer_id' => $customer_id,
                'date' => $date,
                'category' => $category,
                'priority' => $priority,
                'status' => $status
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Error creating appointment: " . $e->getMessage());
            throw new Exception("An error occurred while creating the appointment.");
        }
    }

    public function updateAppointment($appointmentID, $appointment_date, $appointment_category, $appointment_priority)
    {
        try {
            $stmt = $this->conn->prepare("SELECT id FROM appointment WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $appointmentID]);
            $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($appointment) {
                $stmt = $this->conn->prepare("UPDATE appointment SET date = :date, category = :category, priority = :priority WHERE id = :id");
                $stmt->execute(['id' => $appointmentID, 'date' => $appointment_date, 'category' => $appointment_category, 'priority' => $appointment_priority]);
            } else {
                error_log("Appointment ID $appointmentID doesn't exist in the database.");
            }
        } catch (Exception $e) {
            error_log("Error updating appointment: " . $e->getMessage());
            throw new Exception("An error occurred while updating appointment data.");
        }
    }

    public function deleteAppointment($appointmentID)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM appointment WHERE id = :id");
            $stmt->execute(['id' => $appointmentID]);
        } catch (Exception $e) {
            error_log("Error deleting appointment: " . $e->getMessage());
            throw new Exception("An error occurred while deleting appointment data.");
        }
    }
}

// Process request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "create") {
        try {
            $conn = Database::getInstance(); // Get database connection
            $conn->beginTransaction();

            $customerHandler = new Customer($conn);
            $appointmentHandler = new Appointment($conn);

            // Retrieve and sanitize input data
            $customer_name = trim($_POST["customer_name"] ?? "");
            $customer_number = trim($_POST["customer_number"] ?? "");
            $customer_address = trim($_POST["customer_address"] ?? "");
            $appointment_date = trim($_POST["appointment_date"] ?? "");
            $appointment_category = trim($_POST["appointment_category"] ?? "");
            $appointment_priority = trim($_POST["appointment_priority"] ?? "");
            $appointment_status = "Pending"; // Default status

            if (!$customer_name || !$customer_number || !$customer_address || !$appointment_date || !$appointment_category || !$appointment_priority) {
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "All fields are required"]);
                exit;
            }

            // Create or find customer
            $customer_id = $customerHandler->findOrCreateCustomer($customer_name, $customer_number, $customer_address);

            // Create appointment
            $appointmentHandler->createAppointment($customer_id, $appointment_date, $appointment_category, $appointment_priority, $appointment_status);

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Appointment created successfully", "customer_id" => $customer_id]);
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request. Please try again later."]);
        }
    } elseif ($action === "update") {
        try {
            $conn = Database::getInstance();
            $conn->beginTransaction();

            $customerHandler = new Customer($conn);
            $appointmentHandler = new Appointment($conn);

            $appointmentID = trim($_POST["update_AppointmentID"] ?? "");
            $appointment_date = trim($_POST["update_Date"] ?? "");
            $appointment_category = trim($_POST["update_Category"] ?? "");
            $appointment_priority = trim($_POST["update_Priority"] ?? "");

            if (!empty($appointmentID) && !empty($appointment_date) && !empty($appointment_category) && !empty($appointment_priority)) {
                $appointmentHandler->updateAppointment($appointmentID, $appointment_date, $appointment_category, $appointment_priority);
            }

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Update successful"]);
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to update. Please try again."]);
        }
    } elseif ($action === "delete") {
        try {
            $conn = Database::getInstance();
            $conn->beginTransaction();

            $customerHandler = new Customer($conn);
            $appointmentHandler = new Appointment($conn);

            $appointmentID = trim($_POST["appointment_ID"] ?? "");

            if (!empty($appointmentID)) {
                $appointmentHandler->deleteAppointment($appointmentID);
            }

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Update successful"]);
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to Delete. Please try again."]);
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
                    customer.id AS Customer_ID,
                    customer.name AS Customer_Name,
                    appointment.id AS Appointment_ID,
                    appointment.category AS Appointment_Category
                FROM appointment
                JOIN customer ON appointment.customer_id = customer.id
                WHERE appointment.status = 'Pending'
                $order");

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($results) {
                echo "<table border='1' class='appointment-table'>";
                echo "<tr><th>Customer ID</th><th>Customer Name</th><th>Appointment ID</th><th>Category</th></tr>";

                foreach ($results as $row) {
                    echo "<tr onclick='updateDetails(" . htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') . ")'>";
                    echo "<td>{$row['Customer_ID']}</td><td>{$row['Customer_Name']}</td><td>{$row['Appointment_ID']}</td><td>{$row['Appointment_Category']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No records found.";
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
