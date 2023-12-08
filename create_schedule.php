<?php
// create_schedule.php

// Your database connection code
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeesdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve data from the AJAX request
$employeeId = $_POST['employeeId'];
$newSchedule = $_POST['newSchedule'];

// Update the schedule in the database
$sql = "UPDATE employees SET schedule = '$newSchedule' WHERE emp_id = $employeeId";

if ($conn->query($sql) === TRUE) {
    echo "Schedule created successfully";
} else {
    echo "Error creating schedule: " . $conn->error;
}

$conn->close();
?>
