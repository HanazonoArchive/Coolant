<?php

class WeeklyScheduleController
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function retrieveData($startOfWeek, $endOfWeek)
    {
        try {
            if (empty($startOfWeek) || empty($endOfWeek)) {
                throw new Exception("Start or End date not set");
            }

            $this->conn->beginTransaction();
            
            $stmt = $this->conn->prepare("
                SELECT
                customer.name,
                customer.contact_number,
                customer.address,
                appointment.id,
                appointment.date,
                appointment.category,
                appointment.priority,
                appointment.status
                FROM appointment 
                JOIN customer ON appointment.customer_id = customer.id
                WHERE appointment.date BETWEEN :startOfWeek AND :endOfWeek 
                AND appointment.status = 'Pending'
            ");
            
            $stmt->execute([
                'startOfWeek' => $startOfWeek,
                'endOfWeek' => $endOfWeek
            ]);

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch as an array


            $this->conn->commit();

            header("Content-Type: application/json");
            echo json_encode(["status" => "success", "message" => "Data Retrieved Successfully", "data" => $result]);
            exit;
        } catch (Exception $e) {
            $this->conn->rollBack(); // Rollback on failure
            error_log("Retrieval error: " . $e->getMessage());
            header("Content-Type: application/json");
            echo json_encode(["status" => "error", "message" => "An error occurred while retrieving data.", "error" => $e->getMessage()]);
            exit;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    if (isset($data["action"]) && $data["action"] === "loadWeekSchedule") {
        try {
            error_log("Retrieve request received");

            $conn = Database::getInstance();
            $retrievalHandler = new WeeklyScheduleController($conn);

            $startOfWeek = $data['startofWeek'];
            $endOfWeek = $data['endofWeek'];

            $retrievalHandler->retrieveData($startOfWeek, $endOfWeek);
        } catch (Exception $e) {
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Failed to process the request.", "error" => $e->getMessage()]);
            exit;
        }
    }
}
