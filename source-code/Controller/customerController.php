<?php
define('PROJECT_DB3', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
include_once PROJECT_DB3 . "/Database/DBConnection.php";

class Customer
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function UpdateCustomer($CustomerID, $newName, $newContactNumber, $newAddress)
    {
        try {
            // Check if customer exists
            $stmt1 = $this->conn->prepare("SELECT id FROM customer WHERE id = :id LIMIT 1");
            $stmt1->execute(['id' => $CustomerID]);
            $customer = $stmt1->fetch(PDO::FETCH_ASSOC);

            if (!$customer) {
                error_log("Customer ID $CustomerID doesn't exist in the database.");
                return json_encode(["status" => "error", "message" => "Customer ID not found."]);
            }

            // Perform the update query
            $stmt2 = $this->conn->prepare("UPDATE customer SET name = :newName, contact_number = :newContactNumber, address = :newAddress WHERE id = :id");

            $stmt2->execute(['newName' => $newName,'newContactNumber' => $newContactNumber,'newAddress' => $newAddress,'id' => $CustomerID]);
            
            if ($stmt2->rowCount() > 0) {
                return json_encode(["status" => "success", "message" => "Customer updated successfully."]);
            } else {
                return json_encode(["status" => "error", "message" => "No changes were made."]);
            }

        } catch (Exception $e) {
            error_log("Error updating customer: " . $e->getMessage());
            return json_encode(["status" => "error", "message" => "An error occurred while updating customer data."]);
        }
    }


    public function DeleteCustomer($customerID)
    {
        try {
            $stmt1 = $this->conn->prepare("SELECT id FROM customer WHERE id = :id LIMIT 1");
            $stmt1->execute(['id' => $customerID]);
            $customer = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($customer) {
                $stmt2 = $this->conn->prepare("DELETE FROM customer WHERE id = :id");
                $stmt2->execute(['id' => $customerID]);
            } else {
                error_log("Customer Didn't Exist");
            }

        } catch (Exception $e) {
            error_log("Error deleting employee: " . $e->getMessage());
            throw new Exception("An error occurred while deleting customer data.");
        }
    }
}

class Feedback
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function FindOrCreateFeedback($appointmentID, $feedback) {
        try {
            // Check if the feedback already Exists
            $stmt1 = $this->conn->prepare("SELECT id, appointment_id FROM customer_feedback WHERE appointment_id = :appointment_id LIMIT 1");
            $stmt1->execute(['appointment_id' => $appointmentID]);
            $appointment = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($appointment) {
                // if the appointment was found and exist Send return the id;
                return $appointment['id'];
            } else {
                // if the appointment was not found and doesn't exist, create and entry.
                $stmt2 = $this->conn->prepare("INSERT INTO customer_feedback (appointment_id, feedback) VALUES (:appointment_id, :feedback)");
                $stmt2->execute(['appointment_id' => $appointmentID, 'feedback' => $feedback]);
            }

        } catch (Exception $e) {
            error_log("Error updating customer: " . $e->getMessage());
            return json_encode(["status" => "error", "message" => "An error occurred while updating customer_feedback data."]);
        }
    }

    public function UpdateFeedback($feedbackID, $feedback)
    {
        try {
            // Check if the feedback already Exists
            $stmt1 = $this->conn->prepare("SELECT id FROM customer_feedback WHERE id = :id LIMIT 1");
            $stmt1->execute(['id' => $feedbackID]);
            $appointment = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($appointment) {
                // Prepare to Update Value
                $stmt2 = $this->conn->prepare("UPDATE customer_feedback SET feedback = :newFeedback WHERE id = :id");
                $stmt2->execute(['newFeedback' => $feedback, 'id' => $feedbackID]);
            } else {
                error_log("Appointment ID or Feedback ID Doesn't Exist!");
            }

        } catch (Exception $e) {
            error_log("Error updating customer: " . $e->getMessage());
            return json_encode(["status" => "error", "message" => "An error occurred while updating customer data."]);
        }
    }

    public function DeleteFeedback($feedbackID)
    {
        try {
            $stmt1 = $this->conn->prepare("SELECT id FROM customer_feedback WHERE id = :id LIMIT 1");
            $stmt1->execute(['id' => $feedbackID]);
            $customer = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($customer) {
                $stmt2 = $this->conn->prepare("DELETE FROM customer_feedback WHERE id = :id");
                $stmt2->execute(['id' => $feedbackID]);
                return true;
            } else {
                error_log("Feedback Didn't Exist");
            }

        } catch (Exception $e) {
            error_log("Error deleting employee: " . $e->getMessage());
            throw new Exception("An error occurred while deleting customer data.");
        }
    }
}


// Process request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "customerUpdate") {
        try {
            $conn = Database::getInstance();
            $conn->beginTransaction();

            $customerHandler = new Customer($conn);

            $customerID = trim($_POST["update_CustomerID"] ?? "");
            $customerName = trim($_POST["update_CustomerName"] ?? "");
            $customerContactNumber = trim($_POST["update_CustomerContactNumber"] ?? "");
            $customerAddress = trim($_POST["update_CustomerAddress"] ?? "");

            if (!empty($customerID)) {
                $customerHandler->UpdateCustomer($customerID, $customerName, $customerContactNumber, $customerAddress);
                echo json_encode(["status" => "success", "message" => "Customer updated successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Invalid input! Customer ID is required."]);
                exit; // Stop further execution
            }

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Update successful"]);
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(value: ["status" => "error", "message" => $e->getMessage()]);
        }
    } elseif ($action === "customerDelete") {
        try {
            $conn = Database::getInstance();
            if (!$conn) {
                error_log("Database connection failed.");
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "Database connection failed."]);
                exit;
            }

            $conn->beginTransaction();
            $customerHandler = new Customer($conn);

            $customerID = trim($_POST["customer_ID"] ?? "");

            if (!empty($customerID)) {
                $customerHandler->DeleteCustomer($customerID);
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
    } elseif ($action === "feedbackCreate") {
        try {
            $conn = Database::getInstance(); // Get database connection

            if (!$conn) {
                error_log("Database connection failed.");
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "Database connection failed."]);
                exit;
            }

            $conn->beginTransaction();
            $feedbackHandler = new Feedback($conn);

            // Retrieve and sanitize input data
            $appointmentID = htmlspecialchars(trim($_POST["appointment_ID"] ?? ""));
            $feedback = htmlspecialchars(trim($_POST["feedback_comment"] ?? ""));

            if (!$appointmentID || !$feedback) {
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "All fields are required"]);
                exit;
            }

            $employee_id = $feedbackHandler->FindOrCreateFeedback($appointmentID, $feedback);

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "feedback created successfully"]);
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request. Please try again later."]);
        }
    } elseif ($action === "feedbackUpdate") {
        try {
            $conn = Database::getInstance(); // Get database connection

            if (!$conn) {
                error_log("Database connection failed.");
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "Database connection failed."]);
                exit;
            }

            $conn->beginTransaction();
            $feedbackHandler = new Feedback($conn);

            // Retrieve and sanitize input data
            $feedbackID = htmlspecialchars(trim($_POST["feedback_ID"] ?? ""));
            $feedback = htmlspecialchars(trim($_POST["feedback_comment"] ?? ""));

            if (!$feedbackID || !$feedback) {
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "All fields are required"]);
                exit;
            }

            $employee_id = $feedbackHandler->UpdateFeedback($feedbackID, $feedback);

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "feedback created successfully"]);
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request. Please try again later."]);
        }
    } elseif ($action === "feedbackDelete") {
        try {
            $conn = Database::getInstance(); // Get database connection

            if (!$conn) {
                error_log("Database connection failed.");
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "Database connection failed."]);
                exit;
            }

            $conn->beginTransaction();
            $feedbackHandler = new Feedback($conn);

            // Retrieve and sanitize input data
            $feedbackID = htmlspecialchars(trim($_POST["feedback_ID"] ?? ""));

            if (!$feedbackID) {
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "All fields are required"]);
                exit;
            }

            $employee_id = $feedbackHandler->DeleteFeedback($feedbackID);

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "feedback created successfully", "feedback_ID" => $employee_id, "reload" => true]);
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request. Please try again later."]);
        }
    }
}
?>