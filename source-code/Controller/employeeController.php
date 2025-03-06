<?php
define('PROJECT_DB', $_SERVER['DOCUMENT_ROOT'] . '/Coolant/source-code');
include_once PROJECT_DB . "/Database/DBConnection.php";

class Employee
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function FindOrCreateEmployee($name, $contact_number, $role, $status, $pay, $dayWorks)
    {
        try {
            $stmt1 = $this->conn->prepare("SELECT id FROM employee WHERE name = :name AND contact_number =:contact_number LIMIT 1");
            $stmt1->execute(['name' => $name, 'contact_number' => $contact_number]);
            $employee = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($employee) {
                return $employee['id'];
            } else {
                $stmt2 = $this->conn->prepare("INSERT INTO employee (name, contact_number, role, status, pay, days_of_work) VALUES (:name, :contact_number, :role, :status, :pay, :dayWorks)");
                $stmt2->execute(['name' => $name, 'contact_number' => $contact_number, 'role' => $role, 'status' => $status, 'pay' => $pay, 'dayWorks' => $dayWorks]);
                return $this->conn->lastInsertId();
            }
        } catch (Exception $e) {
            error_log("Error handling customer: " . $e->getMessage());
            throw new Exception("An error occurred while processing customer data.");
        }
    }
    public function UpdateEmployee($employeeID, $newName, $newContactNumber, $newRole, $newPay, $newStatus)
    {
        try {
            $stmt1 = $this->conn->prepare("SELECT id FROM employee WHERE id = :id LIMIT 1");
            $stmt1->execute(['id' => $employeeID]);
            $employee = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($employee) {
                $stmt2 = $this->conn->prepare("UPDATE employee SET name = :newName, contact_number = :newContactNumber, role = :newRole, pay = :newPay, status = :newStatus WHERE id = :id");
                $stmt2->execute(['newName' => $newName, 'newContactNumber' => $newContactNumber, 'newRole' => $newRole, 'newPay' => $newPay, 'newStatus' => $newStatus, 'id' => $employeeID]);
            } else {
                error_log("Employee ID $employeeID doesn't exist in the database.");
            }
        } catch (Exception $e) {
            error_log("Error updating employee: " . $e->getMessage());
            throw new Exception("An error occurred while updating employee data.");
        }
    }

    public function UpdateEmployeeStatus($employeeID, $status)
    {
        try {
            $stmt1 = $this->conn->prepare("SELECT id FROM employee WHERE id = :id LIMIT 1");
            $stmt1->execute(['id' => $employeeID]);
            $employee = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($employee) {
                $stmt2 = $this->conn->prepare("UPDATE employee SET status = :status WHERE id = :id");
                $stmt2->execute(['status' => $status, 'id' => $employeeID]);
            } else {
                error_log("Employee ID $employeeID doesn't exist in the database.");
            }
        } catch (Exception $e) {
            error_log("Error updating employee status: " . $e->getMessage());
            throw new Exception("An error occurred while updating employee status.");
        }
    }

    public function DeleteEmployee($employeeID)
    {
        try {

            $stmt1 = $this->conn->prepare("SELECT id FROM employee WHERE id = :id LIMIT 1");
            $stmt1->execute(['id' => $employeeID]);
            $employee = $stmt1->fetch(PDO::FETCH_ASSOC);

            if ($employee) {
                $stmt2 = $this->conn->prepare("DELETE FROM employee WHERE id = :id");
                $stmt2->execute(['id' => $employeeID]);
            } else {
                error_log("Employee Didn't Exist");
            }

        } catch (Exception $e) {
            error_log("Error deleting employee: " . $e->getMessage());
            throw new Exception("An error occurred while deleting customer data.");
        }
    }

    public function EmployeePay($employeeID, $employeeAmmount) {
        try {
            // Fetch employee details in a single query
            $stmt = $this->conn->prepare("SELECT id, days_of_work, pay FROM employee WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $employeeID]);
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($employee) {
                $calculatedAmount = floatval($employee['pay']) * intval($employee['days_of_work']);
                if ($employeeAmmount == $calculatedAmount) {
                    $stmtUpdate = $this->conn->prepare("UPDATE employee SET days_of_work = 0 WHERE id = :id");
                    $stmtUpdate->execute(['id' => $employeeID]);
                    return "Employee payment processed successfully.";
                } else {
                    error_log("Error: Incorrect verification number. Expected: $calculatedAmount, Received: $employeeAmmount.");
                    return "Error: Incorrect verification number.";
                }
            } else {
                error_log("Error: Employee not found.");
                return "Error: Employee not found.";
            }
    
        } catch (Exception $e) {
            error_log("Error processing payment: " . $e->getMessage());
            return "Error processing payment.";
        }
    }
}

// Process request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "create") {
        try {
            $conn = Database::getInstance(); // Get database connection

            if (!$conn) {
                error_log("Database connection failed.");
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "Database connection failed."]);
                exit;
            }

            $conn->beginTransaction();
            $employeeHandler = new Employee($conn);

            // Retrieve and sanitize input data
            $employee_name = htmlspecialchars(trim($_POST["employee_name"] ?? ""));
            $employee_contactNumber = htmlspecialchars(trim($_POST["employee_contactNumber"] ?? ""));
            $employee_role = htmlspecialchars(trim($_POST["employee_role"] ?? ""));
            $employee_status = htmlspecialchars(trim($_POST["employee_status"] ?? ""));
            $employee_pay = htmlspecialchars(trim($_POST["employee_pay"] ?? ""));
            $employee_workDays = htmlspecialchars(string: trim($_POST["employee_workDays"] ?? ""));

            if (!$employee_name || !$employee_contactNumber || !$employee_role || !$employee_pay) {
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "All fields are required"]);
                exit;
            }

            // Create or find employee
            $employee_id = $employeeHandler->FindOrCreateEmployee($employee_name, $employee_contactNumber, $employee_role, $employee_status, $employee_pay, $employee_workDays);

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Employee created successfully"]);
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

            $employeeHandler = new Employee($conn);

            $employeeID = trim($_POST["update_EmployeeID"] ?? "");
            $employeeName = trim($_POST["update_EmployeeName"] ?? "");
            $employeeContactNumber = trim($_POST["update_EmployeeContactNumber"] ?? "");
            $employeeRole = trim($_POST["update_EmployeeRole"] ?? "");
            $employeePay = trim($_POST["update_EmployeePay"] ?? "");
            $employeeStatus = trim($_POST["update_EmployeeStatus"] ?? "");

            // Validation Logic
            $onlyStatusUpdate = !empty($employeeID) && !empty($employeeStatus) &&
                empty($employeeName) && empty($employeeContactNumber) &&
                empty($employeeRole) && empty($employeePay);

            $fullUpdate = !empty($employeeID) && !empty($employeeStatus) &&
                !empty($employeeName) && !empty($employeeContactNumber) &&
                !empty($employeeRole) && !empty($employeePay);

            if ($onlyStatusUpdate) {
                $employeeHandler->UpdateEmployeeStatus($employeeID, $employeeStatus);
            } elseif ($fullUpdate) {
                $employeeHandler->UpdateEmployee($employeeID, $employeeName, $employeeContactNumber, $employeeRole, $employeePay, $employeeStatus);
            } else {
                throw new Exception("Invalid input: Fill only Employee ID & Status OR fill all fields.");
            }

            $conn->commit();
            header('Content-Type: application/json');
            echo json_encode(["status" => "success", "message" => "Update successful"]);
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Transaction failed: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
    } elseif ($action === "delete") {
        try {
            $conn = Database::getInstance();
            if (!$conn) {
                error_log("Database connection failed.");
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "Database connection failed."]);
                exit;
            }

            $conn->beginTransaction();
            $employeeHandler = new Employee($conn);

            $employeeID = trim($_POST["employee_ID"] ?? "");

            if (!empty($employeeID)) {
                $employeeHandler->deleteEmployee($employeeID);
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
    } elseif ($action === "payingEmployee") {
        try {
            $conn = Database::getInstance();
            if (!$conn) {
                error_log("Database connection failed.");
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "Database connection failed."]);
                exit;
            }

            $conn->beginTransaction();
            $employeeHandler = new Employee($conn);

            $employeeID = trim($_POST["employee_ID"] ?? "");
            $employeeConfirmationTEXT = trim($_POST["confirmationTEXT"] ?? "");

            if (!empty($employeeID)) {
                $employeeHandler->EmployeePay($employeeID, $employeeConfirmationTEXT);
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
?>