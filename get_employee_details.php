<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            background-color: #f4f4f4;
        }

        .container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            margin: auto;
        }

        .card {
            flex: 1;
            padding: 20px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            margin: 20px;
        }

        .employee-details-container {
            text-align: center;
        }

        .employee-picture-container {
            text-align: center;
            margin-top: 20px;
        }

        .employee-picture {
            border-radius: 50%;
            max-width: 200px;
            border: 5px solid #fff;
        }

        .employee-details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .employee-name-border {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .employee-content-container {
            display: flex;
            align-items: center;
        }

        .change-picture-form {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .file-label,
        .change-picture-button {
            padding: 10px;
            background-color: #333;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .file-input {
            display: none;
        }

        .employee-details {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            border-collapse: collapse;
        }

        .employee-details th,
        .employee-details td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .attendance-table {
            width: 100%;
            max-width: 600px;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .attendance-table th,
        .attendance-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .button-container {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .back-button,
        .edit-button {
            padding: 10px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>

<div class="container">
<div class="card employee-info">
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeesdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handlePictureChange($conn);
}

function handlePictureChange($conn) {
    $newPicture = $_FILES['new_picture'];

    // Check if a new picture was uploaded
    if ($newPicture['error'] === UPLOAD_ERR_OK) {
        $tempFilePath = $newPicture['tmp_name'];
        $newPicturePath = "profilepics/" . $newPicture['name']; // Modify the path as needed

        // Move the uploaded file to the desired location
        move_uploaded_file($tempFilePath, $newPicturePath);

        // Update the picture path in the database
        $employeeId = $_GET['emp_id'];
        $sql = "UPDATE employees SET picture_path = '$newPicturePath' WHERE emp_id = $employeeId";

        // Perform the update
        if ($conn->query($sql) === TRUE) {
            // Reload the page to reflect the changes
            header("Location: {$_SERVER['PHP_SELF']}?emp_id=$employeeId");
            exit();
        } else {
            echo "Error updating picture: " . $conn->error;
        }
    }
}

// Fetch employee details
$employeeId = isset($_GET['emp_id']) ? $_GET['emp_id'] : null;
$employeeId = filter_var($employeeId, FILTER_VALIDATE_INT);

if ($employeeId === false) {
    die("Invalid employee ID");
}

$sql = "SELECT * FROM employees WHERE emp_id = $employeeId";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $employeeId = $row['emp_id'];
    $employeeName = htmlspecialchars($row['name']);
    $picturePath = $row['picture_path'];
    $department = htmlspecialchars($row['department']);
    $position = htmlspecialchars($row['position']);
    $address = htmlspecialchars($row['address']);
    $contactNumber = htmlspecialchars($row['contact_number']);
    $emailAddress = htmlspecialchars($row['email_address']);
    // $startingSchedule = htmlspecialchars($row['schedule']);
    // $finalSchedule = htmlspecialchars($row['final_schedule']);

    echo "<div class='employee-picture-container'>";
    echo "<img src='$picturePath' alt='$employeeName Profile Picture' class='employee-picture'>";
    echo "</div>";

    echo "<div class='employee-details-container'>";
    echo "<h1>$employeeName</h1>";
    echo "</div>";

    ?>

    <hr>

    <?php    
    // Display employee picture at the top right with border
    if ($picturePath) {
        echo "<div class='employee-details-header'>";
        
    
        // Create a container for both picture and form
        echo "<div class='employee-content-container'>";
        
        //Add a form for changing the picture
        echo "<form action='' method='post' enctype='multipart/form-data' class='change-picture-form'>";
        echo "<label class='file-label'>";
        echo "<input type='file' name='new_picture' accept='image/*' class='file-input'>";
        echo "Choose a File";
        echo "</label>";
        echo "<input type='submit' value='Change Picture' class='change-picture-button'>";
        echo "</form>";
    
        echo "</div>"; // Close the employee-content-container
        echo "</div>"; // Close the employee-details-header
    }
    
    // Add or update the following CSS styles
    echo "<style>";
    // echo ".employee-content-container { display: flex; justify-content: flex-end; align-items: center; }";
    // echo ".employee-picture-container { border: 1px solid #ddd; border-radius: 5px; margin-right: 20px; }";
    echo ".file-label { display: inline-block; margin-right: 20px; }";
    echo ".file-input { display: none; }";
    echo ".file-label, .change-picture-button { padding: 10px; background-color: #333; color: white; border-radius: 5px; cursor: pointer; }";
    echo "</style>";
    ?>
    
    <!--Display all employee details-->
    <table class="employee-details">
        <tr>
            <th>Employee ID</th>
            <td><?php echo $employeeId; ?></td>
        </tr>
        <tr>
            <th>Employee Name</th>
            <td><?php echo $employeeName; ?></td>
        </tr>
        <tr>
            <th>Department</th>
            <td><?php echo $department; ?></td>
        </tr>
        <tr>
            <th>Position</th>
            <td><?php echo $position; ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><?php echo $address; ?></td>
        </tr>
        <tr>
            <th>Contact Number</th>
            <td><?php echo $contactNumber; ?></td>
        </tr>
        <tr>
            <th>Email Address</th>
            <td><?php echo $emailAddress; ?></td>
        </tr>
    </table>
<?php
} else {
    echo "<p>No details found for the employee.</p>";
}



$conn->close();
?>

<hr>
    <!-- Back and Edit buttons -->
    <div class="button-container">
        <a href='admin.php' class='back-button'>
            Back
        </a>
        <a href='edit_employee_details.php?emp_id=<?php echo $employeeId; ?>' class='edit-button'>
            Edit Information
        </a>
    </div>

</div>
<div class="card employee-dtr">
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbnameEmployees = "employeesdb";
$dbnameAttendance = "attendancedb";

// Create connection to employeesdb
$connEmployees = new mysqli($servername, $username, $password, $dbnameEmployees);

// Create connection to attendancedb
$connAttendance = new mysqli($servername, $username, $password, $dbnameAttendance);

// Check connections
if ($connEmployees->connect_error || $connAttendance->connect_error) {
    die("Connection failed: " . $connEmployees->connect_error . " " . $connAttendance->connect_error);
}

// Fetch employee details
$employeeId = isset($_GET['emp_id']) ? $_GET['emp_id'] : null;
$employeeId = filter_var($employeeId, FILTER_VALIDATE_INT);

if ($employeeId === false) {
    die("Invalid employee ID");
}

// Fetch employee details
$sqlEmployee = "SELECT * FROM employees WHERE emp_id = $employeeId";
$resultEmployee = $connEmployees->query($sqlEmployee);

if ($resultEmployee && $resultEmployee->num_rows > 0) {
    $rowEmployee = $resultEmployee->fetch_assoc();

    echo "<h1 style='text-align: center;'>Daily Time Record</h1>";
    echo "<hr>";
    echo "<p>Name: <b>" . $rowEmployee['name'] . "</b></p>";
    echo "<p>Employee ID: <b>" . $rowEmployee['emp_id'] ."</b></p>";
    echo "<p>Department: <b>" . $rowEmployee['department'] . "</b></p>";

    $scheduleSql = "SELECT am_time_in, am_time_out, pm_time_in, pm_time_out FROM scheduledb.employee_schedule WHERE emp_id = $employeeId";
    $scheduleResult = $connEmployees->query($scheduleSql);

    if ($scheduleResult && $scheduleResult->num_rows > 0) {
        // Display the schedule in a table
        echo "<h3 style='text-align: center;'>Employee's Schedule</h3>";
        echo "<table border='1' style='text-align: center; margin-left: auto; margin-right: auto;'>";
        echo "<tr><th>AM Time In</th><th>AM Time Out</th><th>PM Time In</th><th>PM Time Out</th></tr>";

        while ($scheduleRow = $scheduleResult->fetch_assoc()) {
            echo "<td>{$scheduleRow['am_time_in']}</td>";
            echo "<td>{$scheduleRow['am_time_out']}</td>";
            echo "<td>{$scheduleRow['pm_time_in']}</td>";
            echo "<td>{$scheduleRow['pm_time_out']}</td>";
        }

        echo "</table>";
    } else {
        echo "<p>No schedule found for the employee.</p>";
    }

    // Fetch attendance records using the name column from attendancedb
    $employeeName = $rowEmployee['name'];
    $currentDate = date('Y-m-d');
    $firstDayOfMonth = date('Y-m-01', strtotime($currentDate));
    $lastDayOfMonth = date('Y-m-t', strtotime($currentDate));
    
    // Fetch attendance records using the name column from attendancedb
    $sqlAttendance = "SELECT * FROM attendance WHERE name = '$employeeName' AND date BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'";
    $resultAttendance = $connAttendance->query($sqlAttendance);

    if ($resultAttendance === false) {
        die("Error in SQL query: " . $connAttendance->error);
    }

    if ($resultAttendance && $resultAttendance->num_rows > 0) {
        // Initialize arrays for TIME-IN and TIME-OUT
        $amTimeIn = array_fill(1, date('t', strtotime($currentDate)), '');
        $pmTimeOut = array_fill(1, date('t', strtotime($currentDate)), '');
        $amTimeOut = array_fill(1, date('t', strtotime($currentDate)), '');
        $pmTimeIn = array_fill(1, date('t', strtotime($currentDate)), '');
        $underTimeHours = array_fill(1, date('t', strtotime($currentDate)), 0);
        $underTimeMinutes = array_fill(1, date('t', strtotime($currentDate)), 0);

        // Loop through the attendance records
        while ($rowAttendance = $resultAttendance->fetch_assoc()) {
            // Extract day of the month from the date
            $day = date('j', strtotime($rowAttendance['date']));

            // Determine the type of record based on the clock value
            $recordType = '';
            switch ($rowAttendance['clock']) {
                case 'AM-TIME-IN':
                    $recordType = 'amTimeIn';
                    break;
                case 'AM-TIME-OUT':
                    $recordType = 'amTimeOut';
                    break;
                case 'PM-TIME-IN':
                    $recordType = 'pmTimeIn';
                    break;
                case 'PM-TIME-OUT':
                    $recordType = 'pmTimeOut';
                    break;
                default:
                    // Handle unexpected clock values if needed
                    break;
            }

            // Store details in the corresponding array
            if (!empty($recordType)) {
                ${$recordType}[$day] = date('H:i:s', strtotime($rowAttendance['time']));
            }

            // Calculate Under Time
            if (!empty($amTimeIn[$day]) && !empty($amTimeOut[$day]) && !empty($pmTimeIn[$day]) && !empty($pmTimeOut[$day])) {
                $dateTimeAMIn = new DateTime($amTimeIn[$day]);
                $dateTimeAMOut = new DateTime($amTimeOut[$day]);
                $dateTimePMIn = new DateTime($pmTimeIn[$day]);
                $dateTimePMOut = new DateTime($pmTimeOut[$day]);

                $intervalAM = $dateTimeAMOut->diff($dateTimeAMIn);
                $intervalPM = $dateTimePMOut->diff($dateTimePMIn);

                // Calculate total hours and minutes for underTime
                $underTimeHours[$day] = $intervalAM->h + $intervalPM->h;
                $underTimeMinutes[$day] = $intervalAM->i + $intervalPM->i;

                // Adjust hours if minutes exceed 60
                if ($underTimeMinutes[$day] >= 60) {
                    $underTimeHours[$day] += floor($underTimeMinutes[$day] / 60);
                    $underTimeMinutes[$day] %= 60;
                }
            }
        }

        // Display attendance records in a table format
        echo "<h2 style='text-align: center;'>Attendance Records</h2>";
        echo "<table border='1'>";
        echo "<tr><th>DAY</th><th>AM TIME-IN</th><th>AM TIME-OUT</th><th>PM TIME-IN</th><th>PM TIME-OUT</th><th>UNDER TIME (HOURS)</th><th>UNDER TIME (MINUTES)</th></tr>";

        foreach (range(1, date('t', strtotime($currentDate))) as $day) {
            echo "<tr>";
            echo "<td>$day</td>";
            echo "<td>{$amTimeIn[$day]}</td>";
            echo "<td>{$amTimeOut[$day]}</td>";
            echo "<td>{$pmTimeIn[$day]}</td>";
            echo "<td>{$pmTimeOut[$day]}</td>";
            echo "<td>{$underTimeHours[$day]}</td>";
            echo "<td>{$underTimeMinutes[$day]}</td>";
            echo "</tr>";
        }

        echo "</table>";

    } else {
        echo "<p>No attendance records found for the employee in the specified date range.</p>";
    }
} else {
    echo "<p>No employee details found.</p>";
}

$connEmployees->close();
$connAttendance->close();
?>
<div class="button-container">
    <a href='employee_dtr.php?emp_id=<?php echo $employeeId; ?>' class='edit-button'>
        Download DTR
    </a>
</div>
</div>

</body>
</html>