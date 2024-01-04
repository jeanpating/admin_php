<?php

$employeeId = isset($_GET['emp_id']) ? $_GET['emp_id'] : null;

if ($employeeId === null) {
    die("Employee ID is not set. Handle this case appropriately.");
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeesdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employee details
$employeeId = isset($_GET['emp_id']) ? $_GET['emp_id'] : null;
$employeeId = filter_var($employeeId, FILTER_VALIDATE_INT);

if ($employeeId === false) {
    die("Invalid employee ID");
}

// Delete employee from the database
$sqlDelete = "DELETE FROM employees WHERE emp_id = $employeeId";
if ($conn->query($sqlDelete) === TRUE) {
    // Output success message with styling
    echo "<div style='text-align: center; margin-top: 50px;'>";
    echo "<p style='color: green; font-size: 20px;'>Employee removed successfully</p>";
    echo "<a href='admin.php' style='text-decoration: none; padding: 10px; background-color: #CD8D7A; color: white; border-radius: 5px;'>Back to Admin Page</a>";
    echo "</div>";
} else {
    // Output error message with styling
    echo "<div style='text-align: center; margin-top: 50px;'>";
    echo "<p style='color: red; font-size: 20px;'>Error removing employee: " . $conn->error . "</p>";
    echo "<a href='admin.php' style='text-decoration: none; padding: 10px; background-color: #CD8D7A; color: white; border-radius: 5px;'>Back to Admin Page</a>";
    echo "</div>";
}

$conn->close();
?>
