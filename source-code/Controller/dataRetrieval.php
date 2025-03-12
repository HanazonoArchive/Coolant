<?php

class DataRetrieval
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function retrieveQoutationData($appointmentID)
    {
        try {
            if (!isset($appointmentID)) {
                throw new Exception("Appointment ID not set");
            }

            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
            SELECT customer.name, customer.address, appointment.category
            FROM customer JOIN appointment ON customer.id = appointment.customer_id
            WHERE appointment.id = :appointment_id
            ");
            $stmt->execute(['appointment_id' => $appointmentID]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $name = $result['name'];
            $address = $result['address'];
            $details = $result['category'];

            $this->conn->commit();
            header("Content-Type: application/json");
            echo json_encode(["status" => "success", "message" => "Data Retrieve Succesfully", "name" => $name, "address" => $address, "details" => $details]);
            exit;
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode(["status" => "error", "message" => "An error occurred while processing Retrieval."]);
            exit;
        }
    }

    public function retrieveData($appointmentID)
    {
        try {
            if (!isset($appointmentID)) {
                throw new Exception("Appointment ID not set");
            }

            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("
            SELECT customer.name, customer.address
            FROM customer JOIN appointment ON customer.id = appointment.customer_id
            WHERE appointment.id = :appointment_id
            ");
            $stmt->execute(['appointment_id' => $appointmentID]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $name = $result['name'];
            $address = $result['address'];

            $this->conn->commit();
            header("Content-Type: application/json");
            echo json_encode(["status" => "success", "message" => "Data Retrieve Succesfully", "name" => $name, "address" => $address]);
            exit;
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode(["status" => "error", "message" => "An error occurred while processing Retrieval."]);
            exit;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (isset($data["action"]) && $data["action"] === "quotationDataLoader") {
        try {
            error_log("Retrieve request received");

            $conn = Database::getInstance();
            $retrievalHandler = new DataRetrieval($conn);

            $appointment_id = $data['appointmentID'];

            $retrievalHandler->retrieveQoutationData($appointment_id);
        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    } elseif (isset($data["action"]) && $data["action"] === "serviceReportLoadCustomer") {
        try {
            error_log("Retrieve request received");

            $conn = Database::getInstance();
            $retrievalHandler = new DataRetrieval($conn);

            $appointment_id = $data['appointmentID'];

            $retrievalHandler->retrieveData($appointment_id);
        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    } elseif (isset($data["action"]) && $data["action"] === "billingStatementLoadCustomer") {
        try {
            error_log("Retrieve request received");

            $conn = Database::getInstance();
            $retrievalHandler = new DataRetrieval($conn);

            $appointment_id = $data['appointmentID'];

            $retrievalHandler->retrieveData($appointment_id);
        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    }
}
?>