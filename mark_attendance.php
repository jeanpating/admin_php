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
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : null;
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($employeeId !== null && $status !== null) {
        // Use TRIM to remove leading and trailing whitespaces from the status
        $status = trim($status);

        if ($status === 'Absent') {
            // For marking absent on the current date
            markAbsent($conn, $employeeName, $status);
        } else if ($startDate !== null && $endDate !== null) {
            // For a range of dates
            markAttendanceRange($conn, $employeeName, $status, $startDate, $endDate);
        } else {
            echo 'Invalid data received';
        }
    } else {
        echo 'Invalid data received';
    }
}

$conn->close();

function markAbsent($conn, $employeeName, $status) {
    $currentTime = date('h:i:s A');
    $stmt = $conn->prepare("INSERT INTO attendance (date, name, time, status, clock) VALUES (NOW(), ?, '0:00:00', ?, 'AM-TIME-IN')");
    $stmt->bind_param("ss", $employeeName, $status);

    if ($stmt->execute()) {
        echo 'Attendance marked successfully';
    } else {
        echo 'Error marking attendance: ' . $stmt->error;
    }

    $stmt->close();
}

function markAttendanceRange($conn, $employeeName, $status, $startDate, $endDate) {
    $status = trim($status);
    $startDate = date_create($startDate)->format('Y-m-d');
    $endDate = date_create($endDate)->format('Y-m-d');

    $currentDate = date_create($startDate);
    $endDateObj = date_create($endDate);

    while ($currentDate <= $endDateObj) {
        $currentDateString = $currentDate->format('Y-m-d');

        $currentTime = date('h:i:s A');
        $stmt = $conn->prepare("INSERT INTO attendance (date, name, time, status, clock) VALUES (?, ?, '0:00:00', ?, 'AM-TIME-IN')");
        $stmt->bind_param("sss", $currentDateString, $employeeName, $status);

        if ($stmt->execute()) {
            // Insert successful
        } else {
            echo 'Error marking attendance: ' . $stmt->error;
        }

        $stmt->close();
        $currentDate->modify('+1 day');
    }

    echo 'Attendance marked successfully';
}
?>
