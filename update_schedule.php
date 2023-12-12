<?php
// update_schedule.php

// Your database connection code
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scheduledb";
$dbemployee = "employeesdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$employeeId = $_POST['employee_id'];
$subject = $_POST['subject'];
$time = $_POST['time'];
$classroom = $_POST['classroom'];

// Validate form data (add your validation logic here)

// Check if emp_id exists in the employee_schedule table
$checkEmpIdSql = "SELECT emp_id FROM employee_schedule WHERE emp_id = ?";
$stmtCheck = $conn->prepare($checkEmpIdSql);
$stmtCheck->bind_param("i", $employeeId);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    // Emp_id exists in the employee_schedule table, proceed with the update

    // Update the schedule in the database
    $updateSql = "UPDATE employee_schedule SET subject=?, time=?, classroom=? WHERE emp_id=?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sssi", $subject, $time, $classroom, $employeeId);

    if ($stmt->execute()) {
        echo "Schedule updated successfully";
    } else {
        echo "Error updating schedule: " . $stmt->error;
    }

    $stmt->close();
} else {
    // Emp_id does not exist in the employee_schedule table, insert a new row

    // Insert into the employee_schedule table
    $insertSql = "INSERT INTO employee_schedule (emp_id, subject, time, classroom) VALUES (?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($insertSql);
    $stmtInsert->bind_param("isss", $employeeId, $subject, $time, $classroom);

    if ($stmtInsert->execute()) {
        echo "New schedule inserted successfully";
    } else {
        echo "Error inserting into employee_schedule: " . $stmtInsert->error;
    }

    $stmtInsert->close();
}

$stmtCheck->close();
$conn->close();
?>
