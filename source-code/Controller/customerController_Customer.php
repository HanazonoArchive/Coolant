<?php
define('PROJECT_ROOT_DB', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
include PROJECT_ROOT_DB . "/Database/DBConnection.php";

class CustomerManager
{
    private $conn;
    private $default_order = "ORDER BY customer.id ASC"; // Define as a class property

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function fetchCustomer($order = null)
    {
        try {
            $order = $order ?? $this->default_order; // Use default order if not provided

            $stmt = $this->conn->prepare("SELECT 
                    customer.id AS Customer_ID, 
                    customer.name AS Customer_Name, 
                    customer.contact_number AS Contact_Number, 
                    customer.address AS Address
                FROM customer $order");

            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($results) > 0) {
                echo "<table border='1' class='appointment-table'>";
                echo "<tr>";
                $headers = ['Customer_ID', 'Customer_Name', 'Contact_Number', 'Address'];
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
                $query .= " ORDER BY customer.id ASC";
            }
    
            $this->fetchCustomer($query);
            exit;
        }
    }
}

// Initialize the database connection
$conn = Database::getInstance();
$customerManager = new CustomerManager($conn);

// Handle POST request if any
$customerManager->handlePostRequest();

// Fetch appointments for the initial page load
ob_start();
$customerManager->fetchCustomer();
$table_content = ob_get_clean();
