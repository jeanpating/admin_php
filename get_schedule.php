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
$sql = "SELECT emp_id, name, picture_path, schedule FROM employees";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        .schedule-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 20px;
        }

        .employee-list-item-container {
            width: ;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 10px;
            overflow: hidden;
            transition: transform 0.3s ease-in-out;
            cursor: pointer;
        }

        .employee-list-item-container:hover {
            transform: scale(1.05);
        }

        .employee-picture {
            width: 100%;
            height: auto;
            border-radius: 20px 20px 5px 5px;
        }

        .employee-details {
            padding: 10px;
            text-align: center;
        }

        .employee-name {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .employee-status {
            margin: 0;
            font-size: 14px;
            margin-top: 5px;
            font-weight: bold;
        }

        .status-late {
            color: red;
        }

        .status-early {
            color: green;
        }
    </style>
</head>

<body>

<?php
if ($result && $result->num_rows > 0) {
    echo "<div class='schedule-container'>";
    while ($row = $result->fetch_assoc()) {
        $employeeId = $row['emp_id'];
        $employeeName = $row['name'];
        $picturePath = $row['picture_path'];
        $schedule = $row['schedule'];

        echo "<div class='employee-list-item-container'>";
        echo "<img src='$picturePath' alt='$employeeName' class='employee-picture'>";
        echo "<div class='employee-details'>";
        echo "<p class='employee-name' data-employee-id='$employeeId'>$employeeName</p>";

        // Fetch the attendance status for the current employee from attendancedb
        $current_date = date("d_m_Y");
        $table_name = "attendance_table_" . $current_date;
        $sqlAttendance = "SELECT status FROM $table_name WHERE name = '$employeeName'";
        $resultAttendance = $connAttendance->query($sqlAttendance);

        if ($resultAttendance && $resultAttendance->num_rows > 0) {
            $rowAttendance = $resultAttendance->fetch_assoc();
            $attendanceStatus = $rowAttendance['status'];
            $statusClass = ($attendanceStatus === 'Late') ? 'status-late' : 'status-early';
            echo "<p class='employee-status $statusClass'>Status: $attendanceStatus</p>";
        } else {
            echo "<p class='employee-status'>Status: Not available</p>";
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
