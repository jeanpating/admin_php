<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        nav {
            background-color: #333;
            overflow: hidden;
            text-align: right;
            padding-right: 20px;
        }

        nav a {
            display: inline-block;
            color: white;
            padding: 14px 16px;
            text-decoration: none;
        }

        nav a:hover {
            background-color: #ddd;
            color: black;
        }

        /* Sidebar styles */
        .sidebar {
            height: 100%;
            width: 200px;
            position: fixed;
            background-color: #111;
            padding-top: 20px;
            text-align: center;
        }

        .sidebar a {
            padding: 10px;
            text-decoration: none;
            font-size: 18px;
            color: white;
            display: block;
            margin-bottom: 15px;
        }

        .sidebar a:hover {
            background-color: #ddd;
            color: black;
        }

        /* Content area styles */
        .content {
            margin-left: 220px;
            padding: 16px;
        }

        /* Notification styles */
        .notification {
            display: inline-block;
            margin-right: 20px; 
            padding: 14px 16px;
            text-decoration: none;
            background-color: #333; 
            color: white;
        }
    </style>
</head>

<body>

    <nav>
        <a href="#admin" style="float: right;">Admin Profile</a>
        <a href="notification.php" class="notification">Notification</a>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="#" id="attendanceLink">Attendance</a>
        <a href="#" id="employeesLink">Employees</a>
        <a href="#" id="scheduleLink">Schedule</a>
        <a href="#" id="graphLink">Graph</a>
    </div>

    <!-- Content area -->
    <div class="content" id="contentContainer">
        <!-- The content will be dynamically updated here -->
    </div>

    <script>
        document.getElementById('attendanceLink').addEventListener('click', function() {
            changeTitleAndLoadAttendance();
        });

        document.getElementById('employeesLink').addEventListener('click', function() {
            changeTitle('Employees');
            loadEmployees();
        });

        document.getElementById('scheduleLink').addEventListener('click', function() {
            changeTitle('Schedule');
            // functionality
        });

        document.getElementById('graphLink').addEventListener('click', function() {
            changeTitle('Graph');
            // functionality
        });

        function changeTitleAndLoadAttendance() {
            changeTitle('Attendance');
            loadAttendanceTables();
        }

        function changeTitle(title) {
            // Get the current date
            var currentDate = new Date();
            var daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            // Extract the day of the week and format the date
            var dayOfWeek = daysOfWeek[currentDate.getDay()];
            var formattedDate = currentDate.toISOString().split('T')[0];

            var additionalText = '';

            if (title === 'Attendance') {
                additionalText = 'Here is the list of attendances for today. ' + "(" + dayOfWeek + ", " + formattedDate + ")";
            }

            // Page's title
            document.getElementById('contentContainer').innerHTML = '<h1>' + title + '</h1>' + '<p>' + additionalText + '</p>';
        }

        // LOAD ATTENDANCES
        function loadAttendanceTables() {
            // Fetch and display attendance tables using AJAX
            var xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        // Append the table inside the existing container
                        var contentContainer = document.getElementById('contentContainer');
                        contentContainer.innerHTML += this.responseText;
                    } else {
                        console.error("AJAX Error:", this.status, this.statusText);
                    }
                }
            };

            xhttp.open("GET", "get_attendance.php", true);
            xhttp.send();
        }

        function loadEmployees() {
            // Fetch and display employee data using AJAX
            var xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById('contentContainer').innerHTML = this.responseText;
                }
            };

            xhttp.open("GET", "get_employees.php", true);
            xhttp.send();
        }
    </script>

</body>

</html>
