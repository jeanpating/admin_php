<?php
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
$sql = "SELECT emp_id, name, picture_path FROM employees WHERE emp_id = $employeeId";
$result = $conn->query($sql);

// Debugging statement
if (!$result) {
    echo "Error: " . $conn->error;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Schedule</title>
    <link rel="stylesheet" href="styles/get_employee_schedule.css">
</head>

<body>

<?php
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $employeeName = $row['name'];
    $picturePath = $row['picture_path'];

    echo "<div class='employee-schedule-container'>";
    echo "<img src='$picturePath' alt='$employeeName' class='employee-picture'>";
    echo "<h2>$employeeName's Schedule</h2>"; 
    echo "<hr>";

    // Fetch the additional schedule details (am_time_in, am_time_out, pm_time_in, pm_time_out)
    $scheduleSql = "SELECT am_time_in, am_time_out, pm_time_in, pm_time_out FROM scheduledb.employee_schedule WHERE emp_id = $employeeId";
    $scheduleResult = $conn->query($scheduleSql);

    // Fetch the additional columns (time, subject, classroom) from the schedule table
    $scheduleDetailsSql = "SELECT time, subject, classroom FROM scheduledb.schedule WHERE emp_id = $employeeId";
    $scheduleDetailsResult = $conn->query($scheduleDetailsSql);

    // Debugging statement
    if (!$scheduleResult || !$scheduleDetailsResult) {
        echo "Error: " . $conn->error;
    }

    // Display schedule details using a table
    if ($scheduleResult && $scheduleResult->num_rows > 0) {
        echo "<table border='1' style='width: 100%; margin-top: 10px;'>";
        echo "<tr><th>AM Time In</th><th>AM Time Out</th><th>PM Time In</th><th>PM Time Out</th></tr>";
        while ($scheduleRow = $scheduleResult->fetch_assoc()) {
            $amTimeIn = $scheduleRow['am_time_in'];
            $amTimeOut = $scheduleRow['am_time_out'];
            $pmTimeIn = $scheduleRow['pm_time_in'];
            $pmTimeOut = $scheduleRow['pm_time_out'];
        
            echo "<tr><td>$amTimeIn</td><td>$amTimeOut</td><td>$pmTimeIn</td><td>$pmTimeOut</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='employee-schedule'>Schedule not available</p>";
    }

    // Display additional schedule details (time, subject, classroom) using a table
    if ($scheduleDetailsResult && $scheduleDetailsResult->num_rows > 0) {
        echo "<table border='1' style='width: 100%; margin-top: 10px;'>";
        echo "<tr><th>Time</th><th>Subject</th><th>Classroom</th></tr>";
        while ($scheduleDetailsRow = $scheduleDetailsResult->fetch_assoc()) {
            $time = $scheduleDetailsRow['time'];
            $subject = $scheduleDetailsRow['subject'];
            $classroom = $scheduleDetailsRow['classroom'];
        
            echo "<tr><td>$time</td><td>$subject</td><td>$classroom</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='employee-schedule'>Additional schedule details not available</p>";
    }
    
    echo "<a href='edit_employee_schedule.php?employee_id=$employeeId' class='edit-button'>Edit</a>";
    echo "</div>";
} else {
    echo "No schedule information found for the selected employee.";
}

$conn->close();
?>

</body>
</html>
