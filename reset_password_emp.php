<?php
session_start(); // If using session management

// Check if the user is authorized to reset passwords (like an admin check, if needed)
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); 
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeesdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Default password
$defaultPassword = 'Employee123';
$hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT); // Hashing for security

// Check if employee ID is provided
if (!isset($_POST['emp_id']) || !is_numeric($_POST['emp_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid employee ID']); // Added more info
    exit;
}

$employeeId = intval($_POST['emp_id']); // Sanitize employee ID

// Prepare the SQL statement
$sql = "UPDATE emp_acc SET emp_pwd = ? WHERE emp_id = ?";
$stmt = $conn->prepare($sql);

// Check if the preparation was successful
if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'SQL prepare error: ' . $conn->error]); 
    exit;
}

// Bind parameters and execute the query
$stmt->bind_param("si", $hashedPassword, $employeeId);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Password reset to default']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Password reset failed: ' . $stmt->error]); // More error details
}

$stmt->close(); // Close the prepared statement
$conn->close(); // Close the connection
?>
