<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
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

        .notification {
            display: inline-block;
            margin-right: 20px;
            padding: 14px 16px;
            text-decoration: none;
            background-color: #333;
            color: white;
        }

        .employee-list-item-container {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            max-width: fit-content;
        }

        .employee-picture {
            max-width: 100px;
            max-height: 100px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .employee-details {
            /* Add any additional styling for the employee details */
        }

        /* Style for the Download Summary button */
        #downloadSummaryButton {
            display: block;
            margin: 10px 0;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }

        #downloadSummaryButton:hover {
            background-color: #ddd;
            color: black;
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
        <button id="downloadSummaryButton">Download Summary</button>
    </div>

    <!-- Content area -->
    <div class="content" id="contentContainer">
        <!-- The content will be dynamically updated here -->
    </div>

    <!-- Download Summary button -->

    <script>
        document.getElementById('attendanceLink').addEventListener('click', function () {
            changeTitleAndLoadAttendance();
        });

        document.getElementById('employeesLink').addEventListener('click', function () {
            changeTitle('Employees');
            loadEmployees();
        });

        document.getElementById('scheduleLink').addEventListener('click', function () {
            changeTitle('Schedule');
            // functionality
        });

        document.getElementById('graphLink').addEventListener('click', function () {
            changeTitle('Graph');
            // functionality
        });

        function changeTitleAndLoadAttendance() {
            changeTitle('Attendance');
            loadAttendanceTables();
        }

        function changeTitle(title) {
            var currentDate = new Date();
            var daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            var dayOfWeek = daysOfWeek[currentDate.getDay()];
            var formattedDate = currentDate.toISOString().split('T')[0];

            var additionalText = '';

            if (title === 'Attendance') {
                additionalText = 'Here is the list of attendances for today. ' + "(" + dayOfWeek + ", " + formattedDate + ")";
            }

            document.getElementById('contentContainer').innerHTML = '<h1>' + title + '</h1>' + '<p>' + additionalText + '</p>';
        }

        function loadAttendanceTables() {
            var xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
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
            // Set the title explicitly
            changeTitle('Employees');

            // Fetch and display employee data using AJAX
            var xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    // Display employee data
                    document.getElementById('contentContainer').innerHTML = this.responseText;

                    // Add click event listener for each employee name
                    var employeeNames = document.getElementsByClassName('employee-name');
                    Array.from(employeeNames).forEach(function (element) {
                        element.addEventListener('click', function () {
                            loadEmployeeDetails(element.dataset.employeeId);
                        });
                    });
                }
            };

            xhttp.open("GET", "get_employees.php", true);
            xhttp.send();
        }


        function loadEmployeeDetails(employeeId) {
            var xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    var contentContainer = document.getElementById('contentContainer');
                    contentContainer.innerHTML = this.responseText;
                }
            };

            xhttp.open("GET", "get_employee_details.php?employee_id=" + employeeId, true);
            xhttp.send();
        }

        document.getElementById('downloadSummaryButton').addEventListener('click', function () {
            window.location.href = 'monthly_summary.php';
        });

        function uploadProfilePicture(employeeId) {
            var fileInput = document.getElementById('profilePicture_' + employeeId);
            var file = fileInput.files[0];

            var formData = new FormData();
            formData.append('profile_picture', file);

            var xhr = new XMLHttpRequest();

            xhr.open('POST', 'upload_profile_picture.php?employee_id=' + employeeId, true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log('Upload successful');
                } else {
                    console.error('Upload failed');
                }
            };

            xhr.onerror = function () {
                console.error('Error connecting to the server');
            };

            xhr.send(formData);
        }
    </script>

</body>

</html>
