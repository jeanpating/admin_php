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

// Fetch employee details
$employeeId = isset($_GET['emp_id']) ? $_GET['emp_id'] : null;
$employeeId = filter_var($employeeId, FILTER_VALIDATE_INT);

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles/get_employee_details.css">
    <title>Employee Dashboard</title>
    <style>
        .schedule-table {
            border-collapse: collapse;
            width: 100%;
        }
        .schedule-table th {
            text-align: center;
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        .schedule-table td {
            text-align: center;
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>

<body>

<div class="container">
<div class="card employee-info">
    
<?php
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
    
    echo "<div class='employee-picture-container'>";
    echo "<img src='$picturePath' alt='$employeeName Profile Picture' class='employee-picture'>";
    echo "</div>";

    echo "<div class='employee-details-container'>";
    echo "<h1>$employeeName</h1>";
    echo "</div>";

    ?>

    <hr>

    <?php    
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
    
    echo "<style>";
    // echo ".employee-content-container { display: flex; justify-content: flex-end; align-items: center; }";
    // echo ".employee-picture-container { border: 1px solid #ddd; border-radius: 5px; margin-right: 20px; }";
    echo ".file-label { display: inline-block; margin-right: 20px; }";
    echo ".file-input { display: none; }";
    echo ".file-label, .change-picture-button { padding: 10px; color: white; border-radius: 5px; cursor: pointer; }";
    echo "</style>";
    ?>
    
    <!--Display all employee details-->
    <table class="employee-details">
        <tr>
            <th>Employee ID</th>
            <td><?php echo $employeeId; ?></td>
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
<h2>Schedule</h2>

<table class="schedule-table">
    <thead>
        <tr>
            <th>Time</th>
            <th>Subject</th>
            <th>Classroom</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Connect to the database
        $servername = "localhost";
        $username = "root";
        $password = ""; 
        $database = "scheduledb"; 

        $conn = new mysqli($servername, $username, $password, $database);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Select data from table
        $sql = "SELECT time, subject, classroom FROM schedule";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>".$row["time"]."</td><td>".$row["subject"]."</td><td>".$row["classroom"]."</td></tr>";
            }
        } else {
            echo "0 results";
        }
        $conn->close();
        ?>
    </tbody>
</table>
</div>

</script>
<div class="card employee-dtr">
<form method="post" action="employee_selected_dtr.php">
    <label for="month">Month:</label>
    <select id="month" name="month">
        <option value="01">January</option>
        <option value="02">February</option>
        <option value="03">March</option>
        <option value="04">April</option>
        <option value="05">May</option>
        <option value="06">June</option>
        <option value="07">July</option>
        <option value="08">August</option>
        <option value="09">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
    </select>

    <label for="year">Year:</label>
    <select id="year" name="year">
        <?php
            $currentYear = date("Y");
            for ($i = $currentYear; $i >= $currentYear - 10; $i--) {
                echo "<option value=\"$i\">$i</option>";
            }
        ?>
    </select>

    <!-- Add a hidden input field to capture the employee ID -->
    <input type="hidden" name="emp_id" value="<?php echo htmlspecialchars($_GET['emp_id']); ?>">

    <input type="submit" value="View DTR">
</form>
<hr>
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
    // echo "<hr>";
    // echo "<p>Name: <b>" . $rowEmployee['name'] . "</b></p>";
    // echo "<p>Employee ID: <b>" . $rowEmployee['emp_id'] ."</b></p>";
    // echo "<p>Department: <b>" . $rowEmployee['department'] . "</b></p>";

    $scheduleSql = "SELECT am_time_in, am_time_out, pm_time_in, pm_time_out FROM scheduledb.employee_schedule WHERE emp_id = $employeeId";
    $scheduleResult = $connEmployees->query($scheduleSql);

    // if ($scheduleResult && $scheduleResult->num_rows > 0) {
    //     // Display the schedule in a table
    //     echo "<h3 style='text-align: center;'>Employee's Schedule</h3>";
    //     echo "<table border='1' style='text-align: center; margin-left: auto; margin-right: auto;'>";
    //     echo "<tr><th>AM Time In</th><th>AM Time Out</th><th>PM Time In</th><th>PM Time Out</th></tr>";

    //     while ($scheduleRow = $scheduleResult->fetch_assoc()) {
    //         echo "<td>{$scheduleRow['am_time_in']}</td>";
    //         echo "<td>{$scheduleRow['am_time_out']}</td>";
    //         echo "<td>{$scheduleRow['pm_time_in']}</td>";
    //         echo "<td>{$scheduleRow['pm_time_out']}</td>";
    //     }

    //     echo "</table>";
    // } else {
    //     echo "<p>No schedule found for the employee.</p>";
    // }

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
        $amStatus = array_fill(1, date('t', strtotime($currentDate)), '');
        $pmStatus = array_fill(1, date('t', strtotime($currentDate)), '');

        // Loop through the attendance records
        while ($rowAttendance = $resultAttendance->fetch_assoc()) {
            // Extract day of the month from the date
            $day = date('j', strtotime($rowAttendance['date']));

            // Determine the type of record based on the clock value
            $recordType = '';
            switch ($rowAttendance['clock']) {
                case 'AM-TIME-IN':
                    $recordType = 'amTimeIn';
                    $amStatus[$day] = $rowAttendance['status'];
                    break;
                case 'AM-TIME-OUT':
                    $recordType = 'amTimeOut';
                    break;
                case 'PM-TIME-IN':
                    $recordType = 'pmTimeIn';
                    $pmStatus[$day] = $rowAttendance['status'];
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

                $status[$day] = ($rowAttendance['clock'] === 'AM-TIME-IN' || $rowAttendance['clock'] === 'AM-TIME-OUT') ? $amStatus[$day] : $pmStatus[$day];
    
            }
        }

        // Display attendance records in a table format
        $currentMonth = date("F");
        $currentYear = date("Y");
        
        echo "<h2 style='text-align: center;'>Attendance Records ($currentMonth, $currentYear)</h2>";
        echo "<table border='1'>";
        echo "<tr><th>DAY</th><th>AM TIME-IN</th><th>AM TIME-OUT</th><th>AM-STATUS</th><th>PM TIME-IN</th><th>PM TIME-OUT</th><th>PM-STATUS</th></tr>";
        
        $firstDayOfMonth = date('N', strtotime("$currentYear-$currentMonth-01"));

        foreach (range(1, date('t', strtotime("$currentYear-$currentMonth-01"))) as $day) {
            $currentDayOfWeek = ($firstDayOfMonth + $day - 1) % 7; // Calculate the day of the week
        
            echo "<tr>";
            echo "<td>$day</td>";
            echo "<td>" . (isset($amTimeIn[$day]) ? $amTimeIn[$day] : '') . "</td>";
            echo "<td>" . (isset($amTimeOut[$day]) ? $amTimeOut[$day] : '') . "</td>";
            echo "<td>";
        
            // Check if it's Saturday (0) or Sunday (6)
            if ($currentDayOfWeek == 0) {
                echo '<b>'."Sunday".'</b>';
            } elseif ($currentDayOfWeek == 6) {
                echo '<b>'."Saturday".'</b>';
            } else {
                // Show the regular status
                echo (isset($amStatus[$day]) ? $amStatus[$day] : '');
            }
        
            echo "</td>";
            echo "<td>" . (isset($pmTimeIn[$day]) ? $pmTimeIn[$day] : '') . "</td>";
            echo "<td>" . (isset($pmTimeOut[$day]) ? $pmTimeOut[$day] : '') . "</td>";
            echo "<td>";
        
            // Check if it's Saturday (0) or Sunday (6)
            if ($currentDayOfWeek == 0) {
                echo '<b>'."Sunday".'</b>';
            } elseif ($currentDayOfWeek == 6) {
                echo '<b>'."Saturday".'</b>';
            } else {
                // Show the regular status
                echo (isset($pmStatus[$day]) ? $pmStatus[$day] : '');
            }
        
            echo "</td>";
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