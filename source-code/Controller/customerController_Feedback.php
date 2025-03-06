<?php
define('PROJECT_DB2', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
include_once PROJECT_DB2 . "/Database/DBConnection.php";

class CustomerFeedbackManager
{
    private $conn;
    private $default_order = "ORDER BY customer_feedback.id ASC"; // Define as a class property

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function fetchCustomerFeedback($order = null)
    {
        try {
            $order = $order ?? $this->default_order; // Use default order if not provided

            $stmt = $this->conn->prepare("SELECT 
                customer_feedback.id AS Feedback_ID,
                customer.name AS Customer_Name,
                customer_feedback.appointment_id AS Appointment_ID, 
                customer_feedback.feedback AS Feedback
                FROM customer_feedback
                JOIN appointment ON customer_feedback.appointment_id = appointment.id
                JOIN customer ON appointment.customer_id = customer.id
                $order");

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) > 0) {
                echo "<table border='1' class='appointment-table'>";
                echo "<tr>";
                $headers = ['Feedback_ID', 'Customer_Name', 'Appointment_ID', 'Feedback'];
                foreach ($headers as $columnName) {
                    echo "<th>" . htmlspecialchars(str_replace("_", " ", $columnName)) . "</th>";
                }
                echo "</tr>";

                foreach ($results as $row) {
                    foreach ($headers as $column) {
                        echo "<td>" . htmlspecialchars($row[$column]) . "</td>";
                    }
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

    public function handlePostRequest()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sql_query'])) {
            $query = trim($_POST['sql_query']);

            // Ensure the query always has a valid ORDER BY
            if (!str_contains($query, "ORDER BY")) {
                $query .= " ORDER BY customer_feedback.id ASC";
            }

            $this->fetchCustomerFeedback($query);
            exit;
        }
    }

    public function fetchCustomerIDs()
    {
        try {
            $stmt = $this->conn->prepare("
            SELECT customer.id, customer.name 
            FROM customer
            ORDER BY customer.id ASC
        ");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($results);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error fetching Customer IDs: " . $e->getMessage()]);
        }
    }

    public function fetchAppointmentsIDs()
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
            echo json_encode(["error" => "Error fetching Customer IDs: " . $e->getMessage()]);
        }
    }

    public function fetchFeedbackIDs()
    {
        try {
            $stmt = $this->conn->prepare("
            SELECT customer_feedback.id, customer.name 
            FROM customer_feedback
            JOIN appointment ON customer_feedback.appointment_id = appointment.id
            JOIN customer ON appointment.customer_id = customer.id
        ");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json');
            echo json_encode($results);
        } catch (PDOException $e) {
            echo json_encode(["error" => "Error fetching Customer IDs: " . $e->getMessage()]);
        }
    }
}

if (isset($_GET['fetch_Customer'])) {
    $conn = Database::getInstance();
    $feedbackManager = new CustomerFeedbackManager($conn);
    $feedbackManager->fetchCustomerIDs(); // Calls the function to output JSON
    exit; // Stop further execution
}

if (isset($_GET['fetch_Appointment'])) {
    $conn = Database::getInstance();
    $feedbackManager = new CustomerFeedbackManager($conn);
    $feedbackManager->fetchAppointmentsIDs(); // Calls the function to output JSON
    exit; // Stop further execution
}

if (isset($_GET['fetch_Feedback'])) {
    $conn = Database::getInstance();
    $feedbackManager = new CustomerFeedbackManager($conn);
    $feedbackManager->fetchFeedbackIDs(); // Calls the function to output JSON
    exit; // Stop further execution
}


// Initialize the database connection
$conn = Database::getInstance();
$customerFeedbackManager = new CustomerFeedbackManager($conn);

// Handle POST request if any
$customerFeedbackManager->handlePostRequest();

// Fetch appointments for the initial page load
ob_start();
$customerFeedbackManager->fetchCustomerFeedback();
$table_content_feedback = ob_get_clean();
