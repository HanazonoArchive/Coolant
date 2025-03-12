<?php
define('PROJECT_ROOT_DB', $_SERVER['DOCUMENT_ROOT'] . '/CSE7PHPWebsite/public');
include PROJECT_ROOT_DB . "/db/DBConnection.php";

class ScheduleManager
{
    private $conn;
    private $default_order = "ORDER BY appointment.id ASC"; // Define as a class property

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function fetchSchedule($order = null)
    {
        try {
            $order = $order ?? $this->default_order; // Use default order if not provided

            // Get the start (Monday) and end (Sunday) of the current week
            $startOfWeek = date('Y-m-d', strtotime('monday this week'));
            $endOfWeek = date('Y-m-d', strtotime('sunday this week'));

            // Prepare SQL query with date filtering
            $stmt = $this->conn->prepare("SELECT 
                customer.name AS Name, 
                appointment.date AS Date, 
                customer.address AS Address, 
                appointment.category AS Category
            FROM appointment
            JOIN customer ON appointment.customer_id = customer.id
            WHERE appointment.date BETWEEN :startOfWeek AND :endOfWeek
            $order");

            // Bind date parameters
            $stmt->bindParam(':startOfWeek', $startOfWeek);
            $stmt->bindParam(':endOfWeek', $endOfWeek);

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) > 0) {
                echo "<table border='1' class='appointment-table'>";
                echo "<tr>";
                $headers = ['Name', 'Date', 'Address', 'Category'];
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
                echo "No records found for this week.";
            }
        } catch (PDOException $e) {
            echo "Error fetching data: " . $e->getMessage();
        }
    }

    public function pendingCollection()
    {
        try {
            // Prepare SQL query
            $stmt = $this->conn->prepare("SELECT
            customer.name AS Name,
            customer.address AS Address,
            billing_statement.amount AS Amount,
            pending_collection.status AS `Status`
        FROM appointment
        JOIN customer ON appointment.customer_id = customer.id
        LEFT JOIN quotation ON quotation.appointment_id = appointment.id
        LEFT JOIN billing_statement ON billing_statement.quotation_id = quotation.id
        LEFT JOIN pending_collection ON pending_collection.billing_statement_id = billing_statement.id
        WHERE appointment.status = 'Completed'
        ORDER BY 
            CASE 
                WHEN pending_collection.status = 'Pending' THEN 0 
                WHEN pending_collection.status = 'Paid' THEN 1 
                ELSE 2 
            END,  
            customer.name ASC");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) > 0) {
                echo "<table border='1' class='appointment-table'>";
                echo "<tr>";
                $headers = ['Name', 'Address', 'Amount', 'Status']; // Fixed headers
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

    public function employeeTable()
    {
        try {
            // Prepare SQL query
            $stmt = $this->conn->prepare("SELECT
            name AS Name,
            status AS Status,
            role AS Role,
            (pay * days_of_work) AS `Total Pay`
        FROM employee
        ORDER BY status DESC, name ASC");

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) > 0) {
                echo "<table border='1' class='employee-table'>";
                echo "<tr>";
                $headers = ['Name', 'Status', 'Role', 'Total Pay']; // Table headers
                foreach ($headers as $columnName) {
                    echo "<th>" . htmlspecialchars($columnName) . "</th>";
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
                echo "No employee records found.";
            }
        } catch (PDOException $e) {
            echo "Error fetching data: " . $e->getMessage();
        }
    }
    public function getEmployeeStats()
    {
        try {
            $stmt = $this->conn->prepare("SELECT 
            COUNT(*) AS totalEmployees, 
            SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS availableEmployees
        FROM employee");

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode($result);
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function getAppointmentStats()
    {
        try {
            $stmt = $this->conn->prepare("SELECT 
            COUNT(*) AS totalAppointments, 
            SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completedAppointments
        FROM appointment");

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            echo json_encode($result);
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}

// Initialize the database connection
$conn = Database::getInstance();
$appointmentManager = new ScheduleManager($conn);

// Handle API requests first
if (isset($_GET['employeeStats'])) {
    header('Content-Type: application/json');
    $appointmentManager->getEmployeeStats();
    exit;
}

if (isset($_GET['appointmentStats'])) {
    header('Content-Type: application/json');
    $appointmentManager->getAppointmentStats();
    exit;
}

// Fetch appointments for the initial page load
ob_start();
$appointmentManager->fetchSchedule();
$scheduleWeekTable = ob_get_clean();

ob_start();
$appointmentManager->pendingCollection();
$pendingCollectionTable = ob_get_clean();

ob_start();
$appointmentManager->employeeTable();
$employeeTable = ob_get_clean();

