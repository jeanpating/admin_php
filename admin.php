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

        /* Notification styles */
        .notification {
            display: inline-block;
            margin-right: 20px;
            padding: 14px 16px;
            text-decoration: none;
            background-color: #333;
            color: white;
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

            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById('contentContainer').innerHTML = this.responseText;
                }
            };

            xhttp.open("GET", "get_employees.php", true);
            xhttp.send();
        }

        // Add this function to your existing JavaScript code
        function loadMonthlySummary() {
            var xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function () {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        generatePdf(JSON.parse(this.responseText));
                    } else {
                        console.error("AJAX Error:", this.status, this.statusText);
                    }
                }
            };

            xhttp.open("GET", "monthly_summary.php", true);
            xhttp.send();
        }

        function displayMonthlySummary(summary) {
            // Display the summary in the content container
            var contentContainer = document.getElementById('contentContainer');
            contentContainer.innerHTML = '<h1>Monthly Summary</h1>';

            // Create a table to display the summary
            var table = '<table border="1">';
            table += '<tr><th>Status</th><th>Total</th></tr>';
            for (var status in summary) {
                table += '<tr>';
                table += '<td>' + status.charAt(0).toUpperCase() + status.slice(1) + '</td>';
                table += '<td>' + summary[status] + '</td>';
                table += '</tr>';
            }
            table += '</table>';

            contentContainer.innerHTML += table;
        }

        // Add this function to your existing JavaScript code
        function downloadMonthlySummary() {
            loadMonthlySummary();
        }

        function generatePdf(summary) {
            // Create a new jsPDF instance
            var pdf = new jsPDF();

            // Add content to the PDF
            pdf.text('Monthly Summary', 20, 20);

            // Create a table to display the summary
            var rows = [['Status', 'Total']];
            for (var status in summary) {
                rows.push([status.charAt(0).toUpperCase() + status.slice(1), summary[status]]);
            }

            pdf.autoTable({
                head: rows.slice(0, 1),
                body: rows.slice(1),
                startY: 30,
            });

            // Save the PDF
            pdf.save('monthly_summary.pdf');
        }

        // Add an event listener to a button or link for triggering the download
        // For example, you can add a "Download Summary" button
        document.getElementById('downloadSummaryButton').addEventListener('click', downloadMonthlySummary);

    </script>

</body>

</html>
