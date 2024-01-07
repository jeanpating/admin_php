<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendancedb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$employeeId = isset($_POST['emp_id']) ? $_POST['emp_id'] : null;
$status = isset($_POST['status']) ? $_POST['status'] : null;
$employeeName = isset($_POST['employee_name']) ? $_POST['employee_name'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = isset($_POST['emp_id']) ? $_POST['emp_id'] : null;
    $status = isset($_POST['status']) ? $_POST['status'] : null;
    $employeeName = isset($_POST['employee_name']) ? $_POST['employee_name'] : null;

    if ($employeeId !== null && $status !== null) {
        // Use TRIM to remove leading and trailing whitespaces from the status
        $status = trim($status);

        $currentTime = date('h:i:s A');
        $stmt = $conn->prepare("INSERT INTO attendance (date, name, time, status, clock) VALUES (NOW(), ?, '0:00:00', ?, 'AM-TIME-IN')");
        $stmt->bind_param("ss", $employeeName, $status);

        if ($stmt->execute()) {
            echo 'Attendance marked successfully';
        } else {
            echo 'Error marking attendance: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        echo 'Invalid data received';
    }
}


$conn->close();
?>
