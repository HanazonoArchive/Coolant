<?php
define('PROJECT_ROOT_DB', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
include_once PROJECT_ROOT_DB . "/Database/DBConnection.php";

class EmployeeManager
{
    private $conn;
    private $default_order = "ORDER BY employee.id ASC"; // Define as a class property

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function fetchEmployee($order = null)
    {
        try {
            $order = $order ?? $this->default_order; // Use default order if not provided

            $stmt = $this->conn->prepare("SELECT
                employee.id AS Employee_ID,
                employee.name AS Employee_Name,
                employee.contact_number AS Contact_Number,
                employee.role AS Role,
                employee.status AS Status,
                employee.pay AS Pay,
                employee.days_of_work AS Work_Days,
                (employee.pay * employee.days_of_work) AS Total_Pay
            FROM employee $order");

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) > 0) {
                echo "<table border='1' class='appointment-table'>";
                echo "<tr>";
                $headers = ['Employee_ID', 'Employee_Name', 'Contact_Number', 'Role', 'Status', 'Pay', 'Work_Days', 'Total_Pay'];
                foreach ($headers as $columnName) {
                    echo "<th>" . htmlspecialchars(str_replace("_", " ", $columnName)) . "</th>";
                }
                echo "</tr>";

                foreach ($results as $row) {
                    echo "<tr>";
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
            if (!str_contains($query, needle: "ORDER BY")) {
                $query .= " ORDER BY employee.id ASC";
            }

            $this->fetchEmployee($query);
            exit;
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
    $employeeManager = new EmployeeManager($conn);
    $employeeManager->fetchEmployeeIDs(); // Calls the function to output JSON
    exit; // Stop further execution
}

// Initialize the database connection
$conn = Database::getInstance();
$employeeManager = new EmployeeManager($conn);

// Handle POST request if any
$employeeManager->handlePostRequest();

// Fetch appointments for the initial page load
ob_start();
$employeeManager->fetchEmployee();
$table_content = ob_get_clean();
