<?php
define('PROJECT', $_SERVER['DOCUMENT_ROOT'] . '/CSE7PHPWebsite/public');
include PROJECT . "/db/DBConnection.php";

class EmployeeLogManager
{
    private $conn;
    private $default_order = "ORDER BY employee_log.id ASC"; // Define as a class property

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function fetchEmployeeLog($order = null)
    {
        try {
            $order = $order ?? $this->default_order; // Use default order if not provided

            $stmt = $this->conn->prepare("SELECT 
                employee_log.id AS Log_Number,
                employee_log.employee_id AS Employee_ID,
                employee.name AS Employee_Name,
                employee_log.appointment_id AS Appointment_ID,
                appointment.date AS Appointment_Date,
                appointment.status AS Appointment_Status
                FROM employee_log
                JOIN employee ON employee_log.employee_id = employee.id
                LEFT JOIN appointment ON employee_log.appointment_id = appointment.id
                $order");

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) > 0) {
                echo "<table border='1' class='appointment-table'>";
                echo "<tr>";
                $headers = ['Log_Number', 'Employee_ID', 'Employee_Name', 'Appointment_ID', 'Appointment_Date', 'Appointment_Status'];
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

    public function handlePostRequest() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sql_query'])) {
            $query = trim($_POST['sql_query']);
    
            // Ensure the query always has a valid ORDER BY
            if (!str_contains($query, "ORDER BY")) {
                $query .= " ORDER BY employee_log.id ASC";
            }
    
            $this->fetchEmployeeLog($query);
            exit;
        }
    }
}

// Initialize the database connection
$conn = Database::getInstance();
$employeeLogManager = new EmployeeLogManager($conn);

// Handle POST request if any
$employeeLogManager->handlePostRequest();

// Fetch appointments for the initial page load
ob_start();
$employeeLogManager->fetchEmployeeLog();
$table_content = ob_get_clean();
