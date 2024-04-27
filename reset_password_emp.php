<?php
session_start(); 

if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']); 
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeesdb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Default password and hash it
$defaultPassword = 'Employee123';
$hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT); // Hashing for security

if (!isset($_POST['emp_id']) || !is_numeric($_POST['emp_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid employee ID']); 
    exit;
}

$employeeId = intval($_POST['emp_id']); // Convert to integer

// Prepare the SQL query
$sql = "UPDATE emp_acc SET emp_pwd = ? WHERE emp_id = ?";
$stmt = $conn->prepare($sql);

// Check for SQL preparation errors
if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'SQL prepare error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("si", $hashedPassword, $employeeId);

if (!$stmt->execute()) { // If SQL execution failed
    echo json_encode(['status' => 'error', 'message' => 'SQL execution failed: ' . $stmt->error]);
    exit;
}

$stmt->close(); // Close the prepared statement
$conn->close(); // Close the database connection
