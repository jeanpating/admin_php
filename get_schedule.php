<?php
// Your employees database connection code
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeesdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Your attendance database connection code
$attendanceDbName = "attendancedb";

$connAttendance = new mysqli($servername, $username, $password, $attendanceDbName);

if ($connAttendance->connect_error) {
    die("Connection to attendance database failed: " . $connAttendance->connect_error);
}

// Fetch name, picture, and schedule information
$sql = "SELECT emp_id, name, picture_path FROM employees";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles/get_schedule.css">
    <title>Schedule</title>
</head>

<body>

<?php
if ($result && $result->num_rows > 0) {
    echo "<div class='schedule-container'>";
    while ($row = $result->fetch_assoc()) {
        $employeeId = $row['emp_id'];
        $employeeName = $row['name'];
        $picturePath = $row['picture_path'];

        // Fetch schedule details from scheduledb
        $scheduleSql = "SELECT am_time_in, am_time_out, pm_time_in, pm_time_out FROM scheduledb.employee_schedule WHERE emp_id = $employeeId";
        $scheduleResult = $conn->query($scheduleSql);

        echo "<div class='employee-list-item-container'>";
        // echo "<img src='$picturePath' alt='$employeeName' class='employee-picture'>";
        echo "<div class='employee-details'>";
        echo "<p class='employee-name' data-employee-id='$employeeId'>$employeeName</p>";
        echo "<p>Schedule</p>"
        ?>
        <hr>
        <?php

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
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "No schedule information found.";
}

$conn->close();
$connAttendance->close();
?>
</body>

</html>
