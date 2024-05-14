    <?php
    // Start session to access employee ID
    
    session_start(); 


    if (!isset($_SESSION['username'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }
    if (isset($_GET['logout'])) {
        if(isset($_SESSION['username'])){
        unset($_SESSION['username']);
        session_destroy();
        header("location: login.php?out='1'");
        }/*elseif(isset($_SESSION['username2'])){
        unset($_SESSION['username2']);
        session_destroy();
        header("location: ../../login.php?out='1'");
        }*/
    }

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
        if(isset($_FILES['new_picture'])) {
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
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($newPassword === $confirmPassword) {
                // Hash the password before storing it in the database
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update the password in the database
                $sql = "UPDATE emp_acc SET emp_pwd = '$hashedPassword' WHERE emp_id = $employeeId";

                if ($conn->query($sql) === TRUE) {
                    echo "<script>alert('Password updated successfully');</script>";
                } else {
                    echo "<script>alert('Error updating password');</script>";
                }
            } else {
                echo "<script>alert('Passwords do not match');</script>";
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
            .logout {
                text-decoration: none;
            }
            .confirmation-box {
                display: none;
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                padding: 20px;
                background-color: #fff;
                border: 1px solid #ccc;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                z-index: 1000;
            }

            .confirmation-box h2 {
                margin-top: 0;
            }

            .confirmation-box button {
                padding: 10px 15px;
                border: none;
                cursor: pointer;
                margin-right: 10px;
            }

            .confirmation-box button#confirmButton {
                background-color: #4CAF50;
                border-radius: 8px;
                color: #fff;
            }

            .confirmation-box button#cancelButton {
                border-radius: 8px;
                background-color: #ff4444;
                color: #fff;
            }
            .changepass-box {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #ffffff;
            border: 1px solid #ccc;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            width: 300px;
            font-family: Arial, sans-serif;
        }

        .changepass-box h2 {
            margin: 0 0 20px;
            font-size: 1.2em;
        }

        .changepass-box label {
            display: block;
            margin-bottom: 10px;
        }

        .changepass-box input[type="password"] {
            width: calc(100% - 22px);
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .changepass-box button {
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            margin-right: 10px;
            background-color: #4CAF50;
            color: #ffffff;
            border-radius: 4px;
        }

        .changepass-box button.cancel {
            background-color: #ff4444;
        }
        .changepass,
        .logout {
            width: 21%;
            text-align: center;
            font-size: 13px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #789461;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }

        .changepass:hover,
        .logout:hover {
            background-color: #45a049;
        }

        /* Unique class for the confirmation box used in reset password */
        .reset-confirmation-box {
            display: none; /* Initially hidden */
            position: fixed; /* Position relative to the viewport */
            top: 50%; /* Centered vertically */
            left: 50%; /* Centered horizontally */
            transform: translate(-50%, -50%); /* Center precisely */
            padding: 20px; /* Padding for spacing */
            background-color: #fff; /* White background for visibility */
            border: 1px solid #ccc; /* Light border */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3); /* Drop shadow for depth */
            z-index: 1000; /* High z-index to appear above other elements */
            border-radius: 10px; /* Rounded corners */
            text-align: center; /* Center-align text */
            max-width: 300px; /* Maximum width */
            font-family: Arial, sans-serif; /* Consistent font style */
        }

        /* Header styling within the confirmation box */
        .reset-confirmation-box h2 {
            margin: 0 0 10px; /* Remove default margins and add spacing */
            font-size: 1.2em; /* Larger font for emphasis */
        }

        /* Button styling within the reset confirmation box */
        .reset-confirmation-box button {
            padding: 10px 15px; /* Padding for comfort */
            border: none; /* No border on buttons */
            cursor: pointer; /* Pointer cursor on hover */
            margin-right: 10px; /* Spacing between buttons */
            border-radius: 5px; /* Rounded corners */
            transition: background-color 0.3s; /* Smooth transition on hover */
        }

        /* Specific styles for 'confirm' and 'cancel' buttons */
        .reset-confirmation-box button#resetConfirmButton {
            background-color: #4CAF50; /* Green for confirmation */
            color: #fff; /* White text for visibility */
        }

        .reset-confirmation-box button#resetConfirmButton:hover {
            background-color: #45a049; /* Darker green on hover */
        }

        .reset-confirmation-box button#resetCancelButton {
            background-color: #ff4444; /* Red for cancellation */
            color: #fff; /* White text for visibility */
        }

        .reset-confirmation-box button#resetCancelButton:hover {
            background-color: #e53935; /* Darker red on hover */
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
            
            //form for changing the picture
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


        <?php
            // Retrieve emp_id from URL parameter
            if(isset($_GET['emp_id'])) {
                $emp_id = $_GET['emp_id'];
            } else {
                echo "Employee ID not provided.";
                exit;
            }

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

            // Query to retrieve employee's schedule based on emp_id
            $sql = "SELECT time, subject, classroom FROM schedule WHERE emp_id = $emp_id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Display the schedule in a table
                echo "<table class='schedule-table'>
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Subject</th>
                                <th>Classroom</th>
                            </tr>
                        </thead>
                        <tbody>";
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>".$row["time"]."</td><td>".$row["subject"]."</td><td>".$row["classroom"]."</td></tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "No schedule found for the employee.";
            }

            $conn->close();
        ?>
       
    <hr>
    <!--Logout-->
    <a href="#" onclick="confirmLogout()" class="logout">Logout</a>
    <div id="confirmationBox" class="confirmation-box">
        <h2>Confirm Logout</h2>
        <p>Are you sure you want to logout?</p>
        <button id="confirmButton">Logout</button>
        <button class="cancel" id="cancelButton">Cancel</button>
    </div>

    <script>
    function confirmLogout() {
        var confirmationBox = document.getElementById("confirmationBox");
        confirmationBox.style.display = "block";

        var confirmButton = document.getElementById("confirmButton");
        var cancelButton = document.getElementById("cancelButton");

        confirmButton.onclick = function () {
            window.location.href = "admin.php?logout=1";
        };

        cancelButton.onclick = function () {
            confirmationBox.style.display = "none";
        };
    }
    </script>
    
    <!--change password-->
    <a href="#" onclick="changePassword()" class="changepass">Change Pass</a>

    <div id="changePasswordForm" class="changepass-box">
        <h2>Change Password</h2>
        <form method="post">
            <label for="newPassword">New Password:</label>
            <input type="password" id="newPassword" name="new_password" required>
            <br>
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirm_password" required>
            <br>
            <button type="submit" id="changePasswordButton">Change Password</button>
            <button type="button" class="cancel" id="cancelChangePassword">Cancel</button>
        </form>
    </div>

    <script>
        function changePassword() {
            var changePasswordForm = document.getElementById("changePasswordForm");
            changePasswordForm.style.display = "block";

            var cancelButton = document.getElementById("cancelChangePassword");
            cancelButton.onclick = function () {
                changePasswordForm.style.display = "none";
            };
        }
    </script>
    
    <!-- HTML structure with unique ID and class -->
    <div id="resetConfirmationBox" class="reset-confirmation-box">
        <h2>Reset Password Confirmation</h2>
        <p>Are you sure you want to reset the password to default?</p>
        <button id="resetConfirmButton">Confirm</button>
        <button id="resetCancelButton">Cancel</button>
    </div>

    <!--Reset Password-->
    <?php
    
    $emp_id = isset($_GET['emp_id']) ? intval($_GET['emp_id']) : null;

    echo "<script>var employeeId = " . json_encode($emp_id) . ";</script>";
    ?>

    <!-- Trigger to show the reset confirmation box -->
    <a href="#" onclick="showResetConfirmationBox()" class="changepass">Reset Pass</a>

    <script>
    function showResetConfirmationBox() {
        var box = document.getElementById("resetConfirmationBox");
        box.style.display = "block"; 
    }

    function hideResetConfirmationBox() {
        var box = document.getElementById("resetConfirmationBox");
        box.style.display = "none"; 
    }

    document.getElementById("resetConfirmButton").onclick = function () {
        if (!employeeId) {
            console.error("Employee ID is undefined."); 
            alert("Employee ID not available.");
            return; 
        }

        console.log("Resetting password for Employee ID:", employeeId); // Debugging
        var xhr = new XMLHttpRequest(); 
        xhr.open("POST", "reset_password_emp.php", true); 
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) { 
                if (xhr.status === 200) {
                    console.log("Password reset response:", xhr.responseText); // Debugging
                    alert("Password reset to default."); // Notify the user
                } else {
                    console.error("Error resetting password:", xhr.responseText); // Log error details
                    alert("Error resetting password.");
                }
            }
        };

        // send post request
        xhr.send("emp_id=" + employeeId + "&action=reset_password&new_password=Employee123"); 
    };

    document.getElementById("resetCancelButton").onclick = function () {
        hideResetConfirmationBox();
    };
    </script>

    </div>

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

    $currentDate1 = date('Y-m-d');
    $firstDay = date('Y-m-01', strtotime($currentDate1));
    $lastDay = date('Y-m-t', strtotime($currentDate1));
    $monthRange = $firstDay . ' ~ ' . $lastDay;
    
    // Fetch employee details
    $sqlEmployee = "SELECT * FROM employees WHERE emp_id = $employeeId";
    $resultEmployee = $connEmployees->query($sqlEmployee);
    
    if ($resultEmployee && $resultEmployee->num_rows > 0) {
        $rowEmployee = $resultEmployee->fetch_assoc();
    
        echo "<h1 style='text-align: center;'>Daily Time Record</h1>";
        // Data
        $department = "BAWA Elementary School";
        
        // Output as a table
        echo "<table border='1' cellspacing='0' cellpadding='5' style='width: 100%;'>";
    
        // First row with two columns
        echo "<tr>";
        echo "<td><b>Department: </b> $department</td>";
        echo "<td><b>Name: </b>" . $rowEmployee['name'] . "</td>";           
        echo "</tr>";
    
        // Second row with two columns
        echo "<tr>";
        echo "<td><b>Date: </b> $monthRange</td>";      
        echo "<td><b>Employee ID: </b>" . $rowEmployee['emp_id'] . "</td>";
        echo "</tr>";
    
        echo "</table>";
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
            
            $sqlAttendance = "SELECT * FROM attendance 
            WHERE name = '$employeeName' 
            AND MONTH(date) = $currentMonth 
            AND YEAR(date) = $currentYear";
    
            $resultAttendance = $connAttendance->query($sqlAttendance);
    
            if ($resultAttendance === false) {
                die("Error in SQL query: " . $connAttendance->error);
            }
    
            $totalAbsence = 0;
            $totalLeave = 0;
            $totalTrip = 0;
            $totalWork = 0;
    
            // Array to store dates already counted for work
            $workDates = [];
    
            if ($resultAttendance->num_rows > 0) {
                while ($rowAttendance = $resultAttendance->fetch_assoc()) {
                    $status = $rowAttendance['status'];
                    $date = $rowAttendance['date'];
    
            // Increment totals based on the status
            switch ($status) {
                case 'Absent':
                    $totalAbsence++;
                break;
                case 'On-Leave':
                    $totalLeave++;
                break;
                case 'On-Official Business':
                    $totalTrip++;
                break;
                case 'On-Time':
                case 'Late':
                case 'Early':
                case 'Asynchronous':
            
            if (!in_array($date, $workDates)) {
                $totalWork++;
                $workDates[] = $date;
            }
                break;
                default:
                // Ignore other statuses
                    break;
                    }
                }
            }
    
            echo "<table border='1'>";
            echo "<tr><th>Absence (Day)</th><th>Leave (Day)</th><th>Trip (Day)</th><th>Work (Day)</th>
            <th>Overtime Normal</th><th>Overtime Special</th><th>Late (Time)</th><th>Late (Minute)</th>
            <th>Early (Time)</th><th>Early (Minute)</th></tr>";
            echo "<tr><td>$totalAbsence</td><td>$totalLeave</td><td>$totalTrip</td><td>$totalWork</td>
            <td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>";
            echo "</table>";

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

            // echo "<h2 style='text-align: center;'>Attendance Records ($currentMonth, $currentYear)</h2>";
            echo "<h2 style='text-align: center;'>Time Card</h2>";
            echo "<table border='1' style='border-collapse: collapse; text-align: left; width: 100%;'>";

            // Main header 
            echo "<tr>";
            echo "<th rowspan='2'>Day</th>";  
            echo "<th colspan='2'>AM</th>";     
            echo "<th colspan='2'>PM</th>";   

            echo "</tr>";

            // Sub-header row for Time-in and Time-out under "AM" and "PM"
            echo "<tr>";
            echo "<th>Time-in</th>";  // AM Time-in
            echo "<th>Time-out</th>";  // AM Time-out
            echo "<th>Time-in</th>";  // PM Time-in
            echo "<th>Time-out</th>";  // PM Time-out
            echo "</tr>";
            
            // Define the first day of the current month
            $firstDayOfMonth = date('N', strtotime("$currentYear-$currentMonth-01"));

            // Loop through each day of the current month
            foreach (range(1, date('t', strtotime("$currentYear-$currentMonth-01"))) as $day) {
                $formattedDate = sprintf("%02d-%02d", $currentMonth, $day); // 'MM-DD'
                $weekdayName = date('D', strtotime("$currentYear-$currentMonth-$day")); // "Mon", "Tue", etc.
                $displayDay = "$day $weekdayName"; 

                // Check if it's a holiday
                $isHoliday = isset($holidays[$formattedDate]);
                $holidayName = $isHoliday ? $holidays[$formattedDate] : ''; // Get the holiday name

                // Check for specific statuses
                $specialStatus = ''; // Default empty status

                if (isset($amStatus[$day])) {
                    $status = $amStatus[$day];
                } elseif (isset($pmStatus[$day])) {
                    $status = $pmStatus[$day];
                }

                if (isset($status)) {
                    switch ($status) {
                        case 'On-Leave':
                        case 'Absent':
                        case 'On-Official Business':
                        case 'Asynchronous':
                            if ($status === 'On-Official Business') {
                                $specialStatus = 'OOB'; // Abbreviation
                            } elseif ($status === 'Asynchronous') {
                                $specialStatus = 'Async'; // Abbreviation
                            } else {
                                $specialStatus = $status;
                            }
                            break;
                        default:
                            // Ignore other statuses
                            break;
                    }
                }

                // Output the table row
                echo "<tr>";
                echo "<td>$displayDay"; 

                // Display holiday or special status
                if ($isHoliday) {
                    echo " - <b>$holidayName</b>";
                }
                if ($specialStatus !== '') {
                    echo " - <b>$specialStatus</b>"; // Display special status
                }

                echo "</td>";

                // Display AM Time-in and AM Time-out
                echo "<td>" . (($amTimeIn[$day] != '00:00:00') ? $amTimeIn[$day] : '') . "</td>";
                echo "<td>" . (isset($amTimeOut[$day]) ? $amTimeOut[$day] : '') . "</td>";

                // Display PM Time-in and PM Time-out
                echo "<td>" . (isset($pmTimeIn[$day]) ? $pmTimeIn[$day] : '') . "</td>";
                echo "<td>" . (isset($pmTimeOut[$day]) ? $pmTimeOut[$day] : '') . "</td>";

                echo "</tr>";
            }

            // End the table
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