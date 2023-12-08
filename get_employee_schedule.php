<?php
// get_employee_schedule.php

// Your database connection code
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeesdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve the employee ID from the AJAX request
$employeeId = $_GET['employee_id'];

// Fetch the schedule information for the selected employee
$sql = "SELECT emp_id, name, picture_path, schedule FROM employees WHERE emp_id = $employeeId";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Schedule</title>
    <link rel="stylesheet" href="your-styles.css"> <!-- Include your common styles -->
</head>

<body>

<?php
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $employeeName = $row['name'];
    $picturePath = $row['picture_path'];
    $schedule = $row['schedule'];

    echo "<div class='employee-schedule-container'>";
    echo "<img src='$picturePath' alt='$employeeName' class='employee-picture'>";
    echo "<h2>$employeeName's Schedule</h2>";
    echo "<p>Schedule: $schedule</p>";
    echo "</div>";
} else {
    echo "No schedule information found for the selected employee.";
}

$conn->close();
?>

</body>

</html>
