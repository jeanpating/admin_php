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

    echo "<div class='employee-schedule-container'>";
    echo "<img src='$picturePath' alt='$employeeName' class='employee-picture'>";
    echo "<h2>$employeeName's Schedule</h2>"; 
    echo "<hr>";

    // Fetch the additional schedule details (am_time_in, am_time_out, pm_time_in, pm_time_out)
    $scheduleSql = "SELECT am_time_in, am_time_out, pm_time_in, pm_time_out FROM scheduledb.employee_schedule WHERE emp_id = $employeeId";
    $scheduleResult = $conn->query($scheduleSql);

    // Debugging statement
    if (!$scheduleResult) {
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
