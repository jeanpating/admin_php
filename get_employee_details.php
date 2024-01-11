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
    <title>Employee Details</title>
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

<div id="custom-confirm-modal">
    <p id="confirm-message"></p>
    <button id="confirm-yes">Yes</button>
    <button id="confirm-no">No</button>
</div>

<p>Mark employee as</p>
<!-- mark attendances -->
<div class="button-container">
    <div id="onOfficialBusinessDates">
        <button class='markOnOfficialBusiness' onclick="showDateFields('onOfficialBusinessDates')">
            On-Official Business
        </button>
        <!-- Date fields for On-Official Business -->
        <label for="onOfficialBusinessStartDate">From:</label>
        <input type="date" id="onOfficialBusinessStartDate" name="onOfficialBusinessStartDate">

        <label for="onOfficialBusinessEndDate">Until:</label>
        <input type="date" id="onOfficialBusinessEndDate" name="onOfficialBusinessEndDate">
    </div>
</div>
<div class="button-container">
    <!-- Date fields for On-Leave -->
    <div id="onLeaveDates">
        <button class='markOnLeave' onclick="showDateFields('onLeaveDates')">
            On-Leave
        </button>
        <label for="onLeaveStartDate">From:</label>
        <input type="date" id="onLeaveStartDate" name="onLeaveStartDate">

        <label for="onLeaveEndDate">Until:</label>
        <input type="date" id="onLeaveEndDate" name="onLeaveEndDate">
    </div>
</div>
<div class="button-container">
    <button class='markAbsent'>
        Absent
    </button>
</div>


<hr>
    <!-- Back and Edit buttons -->
    <div class="button-container">
        <a href='admin.php' class='back-button'>
            Back
        </a>
        <a href='edit_employee_details.php?emp_id=<?php echo $employeeId; ?>' class='edit-button'>
            Edit Information
        </a>
        <button class='remove-button' onclick='confirmRemoveEmployee()'>
            Remove Employee
        </button>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    function showDateFields(containerId) {
        $('#' + containerId).show();
    }
    $(document).ready(function () {
        $('.markOnOfficialBusiness, .markOnLeave, .markAbsent').on('click', function () {
            var status = $(this).text();
            var startDate, endDate;

            if (status === 'On-Official Business') {
                OBStartDate = $('#onOfficialBusinessStartDate').val();
                OBEndDate = $('#onOfficialBusinessEndDate').val();
            } 

            if (status === 'On-Leave') {
                LStartDate = $('#onLeaveStartDate').val();
                LEndDate = $('#onLeaveEndDate').val();
            }
            
            OBStartDate = $('#onOfficialBusinessStartDate').val();
            OBEndDate = $('#onOfficialBusinessEndDate').val();

            LStartDate = $('#onLeaveStartDate').val();
            LEndDate = $('#onLeaveEndDate').val();

            console.log('Status:', status);
            console.log('Start Date:', OBStartDate);
            console.log('End Date:', OBEndDate);

            console.log('Start Date:', LStartDate);
            console.log('End Date:', LEndDate);


            if (OBStartDate && OBEndDate) {
                showCustomConfirm('Are you sure you want to mark attendance as ' + status + ' from ' + OBStartDate + ' to ' + OBEndDate + '?', function () {
                    markAttendance(status, OBStartDate, OBEndDate);
                });
            } else {
                console.error('Invalid date values');
            }
            if (LStartDate && LEndDate) {
                showCustomConfirm('Are you sure you want to mark attendance as ' + status + ' from ' + LStartDate + ' to ' + LEndDate + '?', function () {
                    markAttendance(status, LStartDate, LEndDate);
                });
            } else {
                console.error('Invalid date values');
            }
        });

        function showCustomConfirm(message, callback) {
            $('#confirm-message').text(message);
            $('#custom-confirm-modal').show();

            $('#confirm-yes').on('click', function () {
                callback();
                $('#custom-confirm-modal').hide();
            });

            $('#confirm-no').on('click', function () {
                console.log('Attendance marking canceled.');
                $('#custom-confirm-modal').hide();
            });
        }

        function markAttendance(status, startDate, endDate) {
            var empId = <?php echo $employeeId; ?>;
            var employeeName = <?php echo json_encode($employeeName); ?>;
            var url = 'mark_attendance.php';

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    emp_id: empId,
                    status: status,
                    employee_name: employeeName,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function (response) {
                    console.log(response);
                    // Handle success response if needed
                },
                error: function (error) {
                    console.error('Error marking attendance: ' + error);
                }
            });
        }
    });
</script>

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

        foreach (range(1, date('t', strtotime($currentDate))) as $day) {
            echo "<tr>";
            echo "<td>$day</td>";
            echo "<td>" . (isset($amTimeIn[$day]) ? $amTimeIn[$day] : '') . "</td>";
            echo "<td>" . (isset($amTimeOut[$day]) ? $amTimeOut[$day] : '') . "</td>";
            echo "<td>" . (isset($amStatus[$day]) ? $amStatus[$day] : '') . "</td>";
            echo "<td>" . (isset($pmTimeIn[$day]) ? $pmTimeIn[$day] : '') . "</td>";
            echo "<td>" . (isset($pmTimeOut[$day]) ? $pmTimeOut[$day] : '') . "</td>";
            echo "<td>" . (isset($pmStatus[$day]) ? $pmStatus[$day] : '') . "</td>";
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
<div id="customConfirm" class="custom-confirm">
    <p id="confirmMessage" class="confirm-message"></p>
    <div class="confirm-buttons">
        <button id="confirmButton" class="confirm-button">Confirm</button>
        <button id="cancelButton" class="confirm-button cancel">Cancel</button>
    </div>
</div>
<input type="hidden" id="employeeId" value="<?php echo $employeeId; ?>">

<script>
    function confirmRemoveEmployee() {
        var customConfirm = document.getElementById('customConfirm');
        var result;

        // Show the custom modal
        customConfirm.style.display = 'block';

        // You can customize the message here
        document.getElementById('confirmMessage').innerHTML = "Are you sure you want to remove this employee?";

        // Set up event listeners for buttons
        document.getElementById('confirmButton').addEventListener('click', function () {
            result = true;
            customConfirm.style.display = 'none';
            handleConfirmation(result);
        });

        document.getElementById('cancelButton').addEventListener('click', function () {
            result = false;
            customConfirm.style.display = 'none';
            handleConfirmation(result);
        });
    }

    function handleConfirmation(result) {
        if (result) {
            // If the user confirms, redirect to a PHP script to handle the removal
            var employeeId = document.getElementById('employeeId').value;
            console.log("Employee ID:", employeeId); // Debugging statement
            window.location.href = 'remove_employee.php?emp_id=' + employeeId;
        }
    }
</script>
</body>
</html>