<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST["status"];
    $employeeName = $_POST["employeeName"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $attendanceDatabase = "attendancedb";

    $connAttendance = new mysqli($servername, $username, $password, $attendanceDatabase);

    if ($connAttendance->connect_error) {
        die("Connection to attendance database failed: " . $connAttendance->connect_error);
    }

    // Use prepared statements to prevent SQL injection
    $stmt = $connAttendance->prepare("UPDATE attendance SET status = ? WHERE name = ?");
    $stmt->bind_param("ss", $status, $employeeName);
    
    if ($stmt->execute()) {
        echo "Attendance updated successfully";
    } else {
        echo "Error updating attendance: " . $stmt->error;
    }

    $stmt->close();
    $connAttendance->close();
} else {
    // Handle invalid requests
    echo "Invalid request";
}
?>
