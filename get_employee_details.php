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
    if ($newPicture['error'] === UPLOAD_ERR_OK) {
        $tempFilePath = $newPicture['tmp_name'];
        $newPicturePath = "profilepics/" . $newPicture['name'];

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
        echo "<div class='employee-content-container'>";
        
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

            showCustomConfirm('Are you sure you want to mark attendance as ' + status + '?', function () {
                markAttendance(status);
            });

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

            if (status === 'Absent') {
                showCustomConfirm('Are you sure you want to mark attendance as ' + status + '?', function () {
                    markAttendance(status);
               });
            } else if (OBStartDate && OBEndDate) {
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
                },
                error: function (error) {
                    console.error('Error marking attendance: ' + error);
                }
            });
        }
    });
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
        $currentMonth = date('m');  // Current month as a number
        $currentYear = date('Y');  // Current year
        
        $holidays = [
            '01-01' => 'New Year\'s Day',
            '02-09' => 'Lunar New Year Holiday',
            '02-10' => 'Lunar New Year\'s Day',
            '02-25' => 'People Power Anniversary',
            '03-11' => 'Ramadans Start',
            '03-28' => 'Maundy Thursday',
            '03-29' => 'Good Friday',
            '03-30' => 'Black Saturday',
            '03-31' => 'Easter Sunday',
            '04-09' => 'The Day of Valor',
            '05-01' => 'Labour Day',
            '06-12' => 'Independence Day',
            '08-21' => 'Ninoy Aquino Day',
            '08-26' => 'National Heroes Day',
            '11-01' => 'All Saint\'s Day',
            '11-02' => 'All Souls Day',
            '11-30' => 'Bonifacio Day',
            '12-08' => 'Feast of the Immaculate Conception',
            '12-24' => 'Christmas Eve',
            '12-25' => 'Christmas Day',
            '12-30' => 'Rizal Day',
            '12-31' => 'New Year\'s Eve'
        ];

        echo "<h2 style='text-align: center;'>Attendance Records ($currentMonth, $currentYear)</h2>";
        echo "<table border='1' style='border-collapse: collapse; text-align: left;'>";

        // Main header 
        echo "<tr>";
        echo "<th rowspan='2'>Day</th>";  
        echo "<th colspan='2'>AM</th>";   
        echo "<th rowspan='2'>AM-STATUS</th>";  
        echo "<th colspan='2'>PM</th>";   
        echo "<th rowspan='2'>PM-STATUS</th>";  

        echo "</tr>";

        // Sub-header row for Time-in and Time-out under "AM" and "PM"
        echo "<tr>";
        echo "<th>Time-in</th>";  // AM Time-in
        echo "<th>Time-out</th>";  // AM Time-out
        echo "<th>Time-in</th>";  // PM Time-in
        echo "<th>Time-out</th>";  // PM Time-out
        echo "</tr>";
        
        $firstDayOfMonth = date('N', strtotime("$currentYear-$currentMonth-01"));

        // Loop through each day of the month
        foreach (range(1, date('t', strtotime("$currentYear-$currentMonth-01"))) as $day) {
            $formattedDate = sprintf("%02d-%02d", $currentMonth, $day); // 'MM-DD'
            $weekdayName = date('D', strtotime("$currentYear-$currentMonth-$day")); // "Mon", "Tue", etc.
            $displayDay = "$day $weekdayName"; 
            
            // Check if it's a holiday
            $isHoliday = isset($holidays[$formattedDate]);
            $holidayName = $isHoliday ? $holidays[$formattedDate] : ''; // Get the holiday name

            // Output the table row
            echo "<tr>";
            echo "<td>$displayDay"; 

            // Display holiday name if it's a holiday
            if ($isHoliday) {
                echo " - <b>$holidayName</b>";
            }

            echo "</td>";

            // AM Time-in
            echo "<td>" . (($amTimeIn[$day] != '00:00:00') ? $amTimeIn[$day] : '') . "</td>";
            // AM Time-out
            echo "<td>" . (isset($amTimeOut[$day]) ? $amTimeOut[$day] : '') . "</td>";

            // AM-STATUS
            echo "<td>";
            if ($isHoliday) {
                echo isset($amStatus[$day]) ? $amStatus[$day] : '';
            } 
            // Changed On-Official Business into OOB because its too long
            if (isset($amStatus[$day]) && $amStatus[$day] === 'On-Official Business') {
                echo 'OOB';
            } else {
                echo (isset($amStatus[$day]) ? $amStatus[$day] : '');
            }
            echo "</td>";

            // PM Time-in
            echo "<td>" . (isset($pmTimeIn[$day]) ? $pmTimeIn[$day] : '') . "</td>";
            // PM Time-out
            echo "<td>" . (isset($pmTimeOut[$day]) ? $pmTimeOut[$day] : '') . "</td>";

            // PM-STATUS
            echo "<td>";
            if ($isHoliday) {
                echo isset($pmStatus[$day]) ? $pmStatus[$day] : '';
            
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