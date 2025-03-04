<?php
define('PROJECT_ROOT_DB', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
include PROJECT_ROOT_DB . "/Database/DBConnection.php";

class AppointmentManager
{
    private $conn;
    private $default_order = "ORDER BY appointment.id ASC"; // Define as a class property

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function fetchAppointments($order = null)
    {
        try {
            $order = $order ?? $this->default_order; // Use default order if not provided

            $stmt = $this->conn->prepare("SELECT 
                    appointment.id AS Ticket_Number, 
                    customer.name AS Customer_Name, 
                    appointment.date AS Appointment_Date, 
                    customer.address AS Address, 
                    appointment.category AS Category, 
                    appointment.priority AS Priority, 
                    appointment.status AS Status,
                    customer.id AS Customer_ID,
                    customer.contact_number AS Contact_Number
                FROM appointment
                JOIN customer ON appointment.customer_id = customer.id
                $order");

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) > 0) {
                echo "<table border='1' class='appointment-table'>";
                echo "<tr>";
                $headers = ['Ticket_Number', 'Customer_Name', 'Appointment_Date', 'Address', 'Category', 'Priority', 'Status'];
                foreach ($headers as $columnName) {
                    echo "<th>" . htmlspecialchars(str_replace("_", " ", $columnName)) . "</th>";
                }
                echo "</tr>";

                foreach ($results as $row) {
                    $rowData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                    echo "<tr onclick='updateDetails($rowData)'>";
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
                $query .= " ORDER BY appointment.id ASC";
            }
    
            $this->fetchAppointments($query);
            exit;
        }
    }  
}

// Initialize the database connection
$conn = Database::getInstance();
$appointmentManager = new AppointmentManager($conn);

// Handle POST request if any
$appointmentManager->handlePostRequest();

// Fetch appointments for the initial page load
ob_start();
$appointmentManager->fetchAppointments();
$table_content = ob_get_clean();
