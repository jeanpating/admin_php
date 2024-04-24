<?php
    session_start(); 

    if (!isset($_SESSION['username1'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }
    if (isset($_GET['logout'])) {
        if(isset($_SESSION['username1'])){
        unset($_SESSION['username1']);
        session_destroy();
        header("location: login.php?out='1'");
        }/*elseif(isset($_SESSION['username2'])){
        unset($_SESSION['username2']);
        session_destroy();
        header("location: ../../login.php?out='1'");
        }*/
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/admin_styles.css">
    <link rel="stylesheet" href="icons/fontawesome-free-6.5.1-web/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
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
            background-color: #ff4444;
            border-radius: 8px;
            color: #fff;
        }
        .confirmation-box {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: white;
            border: 1px solid #ccc;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            display: none; /* Default hidden */
        }

        .confirmation-box button {
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }

        #confirmResetButton {
            background-color: #4CAF50; /* Green */
            color: white;
        }

        #cancelResetButton {
            background-color: #f44336; /* Red */
            color: white;
        }

    </style>

</head>

<body>
    
    <nav>
    <!-- <button id="colorButton">Toggle Background Color</button>     -->
    </nav>
    
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <img src="bg/bawalogo.png" alt="Bawa Elementary School logo">
            <h1 style="color: white;">BAWA</h1>
        </div>
        <hr>
        <a href="#" id="dashboardLink"  class="fa-solid fa-gauge"> Dashboard</a>
        <a href="#" id="attendanceLink" class="fa-solid fa-clipboard-user"> Attendance</a>
        <a href="#" id="employeesLink" class="fa-solid fa-users"> Employees</a>
        <a href="#" id="scheduleLink" class="fa-solid fa-calendar-days"> Schedule</a>
        <a href="#" id="summaryLink" class="fa-solid fa-check-to-slot"> Summary</a>
        <a href="#" id="graphLink" class="fa-solid fa-chart-simple"> Graph</a>
        <a href="notification.php"  class="fa-solid fa-bell"> Notification</a>

        <br><br>
        
        <a href="#" onclick="changePassword()" class="fa-solid fa-key"> Change Pass</a>
        <a href="#" onclick="resetPassword()" class="fa-solid fa-key"> Reset Pass</a>
        <a href="#" onclick="confirmLogout()" class="fa-solid fa-right-from-bracket"> LOGOUT</a>

    </div>

    <!-- Content area -->
    <div class="content" id="contentContainer">

        <div class ="container">
        <h1 style="font-family:;">Admin Dashboard</h1>
            <div class ="col"> 
                <div class="column cardDashboard employees-card">
                    <?php
                        // Database connection parameters
                        $servername = "localhost";
                        $username = "root";
                        $password = "";

                        // Create connection for employeesdb
                        $connEmployees = new mysqli($servername, $username, $password, "employeesdb");

                        // Create connection for attendancedb
                        $connAttendance = new mysqli($servername, $username, $password, "attendancedb");

                        // Check connections
                        if ($connEmployees->connect_error || $connAttendance->connect_error) {
                            die("Connection failed: " . $connEmployees->connect_error . " or " . $connAttendance->connect_error);
                        }
                        $current_date = date("Y_m_d");

                        // SQL query to fetch total number of rows in the 'employees' table
                        $sqlEmployees = "SELECT COUNT(*) as total_employees FROM employees";
                        $resultEmployees = $connEmployees->query($sqlEmployees);

                        if ($resultEmployees->num_rows > 0) {
                            $rowEmployees = $resultEmployees->fetch_assoc();
                            $totalEmployees = $rowEmployees['total_employees'];
                            echo "<strong>Employees</strong><br> " . $totalEmployees;
                        } else {
                            echo "<strong>Employees</strong><br> 0";
                        }

                        // Close the connection for employeesdb
                        $connEmployees->close();
                    ?>
                </div>

                <div class="column cardDashboard present-card">
                    <?php
                        // SQL query for counting present attendees in attendancedb
                        $sqlPresentCount = "SELECT COUNT(*) as total FROM attendance WHERE date = '$current_date' AND (clock ='AM-TIME-IN' OR clock ='PM-TIME-IN') AND status NOT IN ('Absent', 'On-Leave', 'On-Official Business')";
                        $resultPresentCount = $connAttendance->query($sqlPresentCount);

                        if ($resultPresentCount->num_rows > 0) {
                            while ($rowPresentCount = $resultPresentCount->fetch_assoc()) {
                                echo "<strong>Present</strong><br> " . $rowPresentCount['total'];
                            }
                        } else {
                            echo "<strong>Present</strong><br> 0";
                        }
                    ?>
                </div>

                <div class="column cardDashboard late-card">
                    <?php
                        // SQL query for attendance from attendancedb
                        $sqlLate = "SELECT COUNT(*) as total FROM attendance WHERE date = '$current_date' AND (clock = 'AM-TIME-IN' OR 'PM-TIME-IN') AND status = 'late'";
                        $resultLate = $connAttendance   ->query($sqlLate);

                        if ($resultLate->num_rows > 0) {
                            while ($rowLate = $resultLate->fetch_assoc()) {
                                echo "<strong>Late</strong><br> " . $rowLate['total'];
                            }
                        } else {
                            echo "<strong>Late</strong><br> 0";
                        }

                        // Close the connection for attendancedb
                        $connAttendance->close();
                    ?>
                </div>     
            </div>
        <div>

        <hr>

        <div id="current_date" 
        style="font-size: 24px; 
        color: #5e7a60; 
        font-weight: bold; 
        background-color: #C3E2C2; 
        text-align: center;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.5);
        "></div>

        <!-- Eto yung naka fix sa Asia/Manila, kahit palitan yung timezone ng computer na gamit hindi magbabago yung time -->

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script>
            // Function to refresh the page
            function refreshPage() {
                // Set the current date
                var currentDate = new Date();
                var options = { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true };
                var formattedDate = currentDate.toLocaleString('en-US', { ...options, timeZone: 'Asia/Manila' });
                document.getElementById("current_date").innerHTML = formattedDate;
                
                // Use jQuery AJAX to load only the necessary part of the page
                $.ajax({
                    url: 'partial-content.html',
                    success: function(data) {
                        $('#content-container').html($(data).find('#content-container').html());
                    }
                });
            }

            // Refresh the page every second
            setInterval(refreshPage, 1000);
        </script>


        
        <div class="attendance-table-card" style="float: left; width: 58%;">
        <h3 style="text-align: center; font-size: 25px; font-family: roboto;">Calendar</h3>
        <?php
            // // Database connection parameters
            // $servername = "localhost";
            // $username = "root";
            // $password = "";

            // // Create connection for attendancedb
            // $connAttendance = new mysqli($servername, $username, $password, "attendancedb");

            // // Check connection
            // if ($connAttendance->connect_error) {
            //     die("Connection failed: " . $connAttendance->connect_error);
            // }

            // // SQL query to fetch attendance data
            // $sqlAttendanceTable = "SELECT name, time, status FROM attendance WHERE date = '$current_date' AND clock='AM-TIME-IN'";
            // $resultAttendanceTable = $connAttendance->query($sqlAttendanceTable);

            // if ($resultAttendanceTable->num_rows > 0) {
            //     echo '<table class="attendance-table">';
            //     echo '<tr><th>Name</th><th>Time</th></tr>';

            //     while ($rowAttendanceTable = $resultAttendanceTable->fetch_assoc()) {
            //         // Check if the status is 'Absent', 'On-Leave', or 'On-Official Business'
            //         if (!in_array($rowAttendanceTable['status'], array('Absent', 'On-Leave', 'On-Official Business'))) {
            //             echo '<tr>';
            //             echo '<td>' . $rowAttendanceTable['name'] . '</td>';
            //             echo '<td>' . $rowAttendanceTable['time'] . '</td>';
            //             echo '</tr>';
            //         }
            //     }

            //     echo '</table>';
            // } else {
            //     echo '<p>No attendance records available.</p>';
            // }

            // // Close the connection for attendancedb
            // $connAttendance->close();

                
                
            // Function to generate a calendar for a specific month and year
            function generateCalendar($month, $year) {
                $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $firstDay = mktime(0, 0, 0, $month, 1, $year);
                $firstDayOfWeek = date('N', $firstDay);
            
                // Array of holidays (format: 'month-day' => 'Holiday Name')
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
                    '12-08' => 'Feast of the Immaculate Concepcion',
                    '12-24' => 'Christmas Eve',
                    '12-25' => 'Christmas Day',
                    '12-30' => 'Rizal Day',
                    '12-31' => 'New Year\'s Eve'
                    ];
                    $events = [
                        '07-13' => 'Nutrition Day',
                        '10-03' => 'Teacher\'s Day',
                        '08-31' => 'Araw ng Wika'
                    ];
            
                    echo '<div style="font-family: Arial, sans-serif;">';
                    echo '<table style="width: 100%; border-collapse: collapse; margin: 20px; background-color: #f0f0f0; border: 1px solid #ddd; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">';
                    echo '<caption style="font-size: 1.5em; padding: 10px; text-align: center; background-color: #3498db; color: #fff;">' . date('F Y', $firstDay) . '</caption>';
                    echo '<tr style="background-color: #3498db; color: #fff;">
                            <th style="padding: 10px;">Mon</th>
                            <th style="padding: 10px;">Tue</th>
                            <th style="padding: 10px;">Wed</th>
                            <th style="padding: 10px;">Thu</th>
                            <th style="padding: 10px;">Fri</th>
                            <th style="padding: 10px;">Sat</th>
                            <th style="padding: 10px;">Sun</th>
                        </tr>';
            
                    // Add empty cells for days before the first day of the month
                    echo '<tr>';
                    for ($i = 1; $i < $firstDayOfWeek; $i++) {
                        echo '<td></td>';
                    }
            
                    // Loop through the days of the month
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $currentDate = sprintf('%02d-%02d', $month, $day);
            
                        // Check if the current date is a holiday
                        $isHoliday = isset($holidays[$currentDate]);
                        $cellStyle = $isHoliday ? 'background-color: #ffc0cb;' : '';
            
                        $isEvent = isset($events[$currentDate]);
                        $cellStyleEvent = $isEvent ? 'background-color: #c0ebcc;' : '';
            
                        echo '<td style="padding: 10px; text-align: center; font-weight: ' . ($isHoliday ? 'bold' : 'normal') . ';' . $cellStyle . $cellStyleEvent .'">';
                        echo '<div style="font-size: 1.2em;">' . $day . '</div>';
            
                        if ($isHoliday) {
                            echo '<div style="font-size: 0.8em; color: #e44d26;">' . $holidays[$currentDate] . '</div>';
                        }
                        if ($isEvent) {
                            echo '<div style="font-size: 0.8em; color: green;">' . $events[$currentDate] . '</div>';
                        }
                        echo '</td>';
            
                        // Start a new row every 7 days (a week)
                        if (($day + $firstDayOfWeek - 1) % 7 == 0) {
                            echo '</tr><tr>';
                        }
                    }
            
                    // Add empty cells for remaining days in the last week
                    for ($i = ($day + $firstDayOfWeek - 1) % 7; $i < 7; $i++) {
                        echo '<td></td>';
                    }
            
                    echo '</tr></table>';
                    echo '</div>';
                }
            
                // Process the form submission
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $selectedMonth = (int)$_POST['month'];
                    $selectedYear = (int)$_POST['year'];
                } else {
                    // Default to the current month and year
                    $selectedMonth = date('n');
                    $selectedYear = date('Y');
                }
            
            // Display the form with a dropdown for month and year
            echo '<form method="post" style="text-align: center; margin: 20px;">';
            echo '  <label for="month" style="margin-right: 10px;">Select Month:</label>';
            echo '  <select name="month" id="month" style="padding: 5px;">';
            for ($i = 1; $i <= 12; $i++) {
                echo '    <option value="' . $i . '" ' . ($selectedMonth == $i ? 'selected' : '') . '>' . date('F', mktime(0, 0, 0, $i, 1)) . '</option>';
            }
            echo '  </select>';
            
            echo '  <label for="year" style="margin-right: 10px;">Select Year:</label>';
            echo '  <select name="year" id="year" style="padding: 5px;">';
            for ($i = date('Y') - 10; $i <= date('Y') + 10; $i++) {
                echo '    <option value="' . $i . '" ' . ($selectedYear == $i ? 'selected' : '') . '>' . $i . '</option>';
            }
            echo '  </select>';
            
            echo '  <input type="submit" value="Show Calendar" style="padding: 5px; background-color: #3498db; color: #fff; border: none; cursor: pointer;">';
            echo '</form>';
            
            // Display the calendar for the selected month and year
            generateCalendar($selectedMonth, $selectedYear);
            ?>
        </div>

        <div class="attendance-table-card" style="float: right; width: 37%;">
        <?php
            // Function to get current month's holidays
            function getCurrentMonthEvents() {
                // Array of holidays (format: 'month-day' => 'Holiday Name')
                $holidays = [
                    '01-01' => 'New Year\'s Day',
                    '02-09' => 'Lunar New Year Holiday',
                    '02-10' => 'Lunar New Year\'s Day',
                    '02-25' => 'People Power Anniversary',
                    '03-11' => 'Ramadan Start',
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

                // Array of events (format: 'month-day' => 'Event Name')
                $events = [
                    '07-13' => 'Nutrition Day',
                    '10-03' => 'Teacher\'s Day',
                    '08-31' => 'Araw ng Wika'
                ];

                // Get current month and year
                $currentMonth = date('n');
                $currentYear = date('Y');

                // Merge holidays and events
                $currentMonthEvents = array_merge($holidays, $events);

                // Filter events for the current month
                $currentMonthEvents = array_filter($currentMonthEvents, function ($key) use ($currentMonth) {
                    return explode('-', $key)[0] == $currentMonth;
                }, ARRAY_FILTER_USE_KEY);

                return $currentMonthEvents;
            }

            // Get current month's holidays and events
            $currentMonthEvents = getCurrentMonthEvents();

            // Display holidays and events in a card
            if (!empty($currentMonthEvents)) {
                echo '<div style="border: 1px solid #ddd; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; margin: 20px; background-color: #fff; border-radius: 8px; height: 430px;">';
                echo '<h2 style="color: #3498db; text-align: center; font-family: roboto;">Upcoming Holidays and Events</h2>';
                echo'<hr>';
                echo '<ul>';
                foreach ($currentMonthEvents as $date => $event) {
                    echo "<li style='font-size: 18; padding: 5px;'>($date) $event</li>";
                }
                echo '</ul>';
                echo '</div>';
            } else {
                echo '<p style="margin: 20px;">There are no holidays or events this month.</p>';
            }
            ?>
        </div>

    </div>
    <script src="script/admin_script.js"></script>
    
    <!--Log out-->
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

    <div id="confirmationBox" class="confirmation-box">
        <h2>Confirm Logout</h2>
        <p>Are you sure you want to logout?</p>
        <button id="confirmButton">Logout</button>
        <button class="cancel" id="cancelButton">Cancel</button>
    </div>
   
    <!--Change pass-->
    <div class="confirmation-box" id="passwordChangeBox">
            <h2>Change Password</h2>
            <form id="passwordChangeForm">
                <label for="newPassword">New Password:</label><br>
                <input type="password" id="newPassword" name="newPassword"><br><br>
                <label for="confirmPassword">Confirm Password:</label><br>
                <input type="password" id="confirmPassword" name="confirmPassword"><br><br>
                <button type="button" id="confirmButton" onclick="submitPasswordChange()">Change</button>
                <button type="button" id="cancelButton" onclick="hidePasswordChangeBox()">Cancel</button>
            </form>
        </div>
        <script>
        function changePassword() {
            document.getElementById("passwordChangeBox").style.display = "block";
        }

        function hidePasswordChangeBox() {
            document.getElementById("passwordChangeBox").style.display = "none";
        }

        function submitPasswordChange() {
            var newPassword = document.getElementById("newPassword").value;
            var confirmPassword = document.getElementById("confirmPassword").value;

            // Check if the passwords match
            if (newPassword !== confirmPassword) {
                alert("Passwords do not match. Please try again.");
                return;
            }

            // Perform AJAX request to update password
            $.ajax({
                type: "POST",
                url: "change_password.php",
                data: { newPassword: newPassword },
                success: function(response) {
                    // Handle success response
                    var data = JSON.parse(response);
                    if (data.status === "success") {
                        alert("Password changed successfully!");
                        hidePasswordChangeBox();
                    } else {
                        alert("Failed to change password: " + data.message);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error
                    alert("Error occurred while changing password: " + error);
                }
            });
        }
        function resetPassword() {
            var confirmationBox = document.getElementById("resetConfirmationBox");
            confirmationBox.style.display = "block"; // Show the confirmation box
            
            var confirmResetButton = document.getElementById("confirmResetButton");
            var cancelResetButton = document.getElementById("cancelResetButton");
            
            confirmResetButton.onclick = function () {
                $.ajax({
                    type: "POST",
                    url: "reset_password.php",
                    success: function (response) {
                        var result = JSON.parse(response);
                        if (result.status === "success") {
                            alert("Password has been reset.");
                        } else {
                            alert("Failed to reset password: " + result.message);
                        }
                        confirmationBox.style.display = "none"; // Hide the confirmation box
                    },
                    error: function () {
                        alert("An error occurred while resetting the password.");
                        confirmationBox.style.display = "none"; // Hide the confirmation box
                    }
                });
            };
            
            cancelResetButton.onclick = function () {
                confirmationBox.style.display = "none"; // Hide the confirmation box
            };
        }
    </script>
    <div id="resetConfirmationBox" class="confirmation-box" style="display: none;">
        <h2>Reset Password</h2>
        <p>Are you sure you want to reset the password?</p>
        <button id="confirmResetButton">Reset</button>
        <button id="cancelResetButton">Cancel</button>
    </div>

</body>
</html>
