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
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            /* display: flex; */
            justify-content: center;
            align-items: center;
            height: 100vh;

        }

        .employee-schedule-container {
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 40%;
        }

        .employee-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        h2 {
            color: #333333;
        }

        p {
            color: #555555;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .edit-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }

        .edit-button:hover {
            background-color: #2980b9;
        }

        .error-message {
            color: #ff0000;
        }

    </style>
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
    ?><hr><?php
    echo "<p>Current Schedule: $schedule</p>";
    

    $scheduleSql = "SELECT subject, time, classroom FROM scheduledb.employee_schedule WHERE emp_id = $employeeId";
    $scheduleResult = $conn->query($scheduleSql);
    // Display schedule details using a table
    if ($scheduleResult && $scheduleResult->num_rows > 0) {
        echo "<table border='1' style='width: 100%; margin-top: 10px;'>";
        echo "<tr><th>Subject</th><th>Time</th><th>Classroom</th></tr>";
        while ($scheduleRow = $scheduleResult->fetch_assoc()) {
            $subject = $scheduleRow['subject'];
            $time = $scheduleRow['time'];
            $classroom = $scheduleRow['classroom'];
            echo "<tr><td>$subject</td><td>$time</td><td>$classroom</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='employee-schedule'>Schedule not available</p>";
    }
    // Add an "Edit" button that links to edit_employee_schedule.php
    echo "<a href='edit_employee_schedule.php?employee_id=$employeeId' class='edit-button'>Edit</a>";
    
    echo "</div>";
} else {
    echo "No schedule information found for the selected employee.";
}

$conn->close();
?>

</body>

</html>
