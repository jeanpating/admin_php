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

// Fetch employee details and attendance count
$employeeId = $_GET['employee_id'];
$sql = "SELECT name, COUNT(*) as total_attendances 
        FROM employees 
        LEFT JOIN attendance_table ON employees.id = attendance_table.employee_id 
        WHERE employees.id = $employeeId 
        GROUP BY employees.id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $employeeName = $row['name'];
    $totalAttendances = $row['total_attendances'];
    echo "<h2>Total Attendances for $employeeName: $totalAttendances</h2>";
} else {
    echo "<p>No attendances found for the employee.</p>";
}

// Close the connection
$conn->close();
?>
