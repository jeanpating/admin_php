<?php
// Start session (though this might not be needed if you're not using session variables)
session_start();

// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'employeesdb');

// Check for connection errors
if ($mysqli->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

// Default new password (hashed for security)
$new_password = 'Admin123';

// Warning: This will update the password for ALL users in the `users` table
$sql = "UPDATE users SET user_pwd = ?"; 
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'SQL preparation failed']);
    exit;
}

// Bind parameter and execute the statement
$stmt->bind_param('s', $new_password);
if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update password']);
    exit;
}

// Return success message
echo json_encode(['status' => 'success', 'message' => 'Password reset for all users successfully']);
