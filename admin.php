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

    </style>
</head>

<body>

    <nav>
          
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

        <br><br><br><br>
        
        <a href="notification.php"  class="fa-solid fa-bell"> Notification</a>
        <a href="admin.php?logout='1'" class="fa-solid fa-right-from-bracket"> LOGOUT</a>
    </div>

    <!-- Content area -->
    <div class="content" id="contentContainer">

        <div class ="container">
        <h1>Admin Dashboard</h1>
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

        <script>
            // Function to refresh the page
            function refreshPage() {
                // Set the current date
                var currentDate = new Date();
                document.getElementById("current_date").innerHTML = currentDate;
                
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

        <h3>Recent Time-ins</h3>
        <div class="attendance-table-card" style="float: left; width: 50%;">
        <?php
            // Database connection parameters
            $servername = "localhost";
            $username = "root";
            $password = "";

            // Create connection for attendancedb
            $connAttendance = new mysqli($servername, $username, $password, "attendancedb");

            // Check connection
            if ($connAttendance->connect_error) {
                die("Connection failed: " . $connAttendance->connect_error);
            }

            // SQL query to fetch attendance data
            $sqlAttendanceTable = "SELECT name, time, status FROM attendance WHERE date = '$current_date' AND clock='AM-TIME-IN'";
            $resultAttendanceTable = $connAttendance->query($sqlAttendanceTable);

            if ($resultAttendanceTable->num_rows > 0) {
                echo '<table class="attendance-table">';
                echo '<tr><th>Name</th><th>Time</th></tr>';

                while ($rowAttendanceTable = $resultAttendanceTable->fetch_assoc()) {
                    // Check if the status is 'Absent', 'On-Leave', or 'On-Official Business'
                    if (!in_array($rowAttendanceTable['status'], array('Absent', 'On-Leave', 'On-Official Business'))) {
                        echo '<tr>';
                        echo '<td>' . $rowAttendanceTable['name'] . '</td>';
                        echo '<td>' . $rowAttendanceTable['time'] . '</td>';
                        echo '</tr>';
                    }
                }

                echo '</table>';
            } else {
                echo '<p>No attendance records available.</p>';
            }

            // Close the connection for attendancedb
            $connAttendance->close();
            ?>
        </div>
    </div>
    <script src="script/admin_script.js"></script>
</body>
</html>
