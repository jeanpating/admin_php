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
            <h1>BAWA</h1>
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
        <a href="#admin" class="fa-solid fa-user-gear"> Admin</a>
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
                        // SQL query for attendance from attendancedb
                        $sqlAttendance = "SELECT COUNT(*) as total FROM attendance WHERE date = '$current_date' and (clock ='AM-TIME-IN' OR 'PM-TIME-IN')";
                        $resultAttendance = $connAttendance->query($sqlAttendance);

                        if ($resultAttendance->num_rows > 0) {
                            while ($rowAttendance = $resultAttendance->fetch_assoc()) {
                                echo "<strong>Present</strong><br> " . $rowAttendance['total'];
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

        <h3>Recent attendances</h3>
        <div id="current_date"></div>

        <script>
            // Function to refresh the page
            function refreshPage() {
                // Set the current date
                var currentDate = new Date();
                document.getElementById("current_date").innerHTML = currentDate;
                
                // Use jQuery AJAX to load only the necessary part of the page
                $.ajax({
                    url: 'partial-content.html', // Replace with the actual URL of the partial content
                    success: function(data) {
                        // Replace the content of a specific element with the new data
                        $('#content-container').html($(data).find('#content-container').html());
                    }
                });
            }

            // Refresh the page every second
            setInterval(refreshPage, 1000);
        </script>

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
                $sqlAttendanceTable = "SELECT name, time FROM attendance WHERE date = '$current_date'";
                $resultAttendanceTable = $connAttendance->query($sqlAttendanceTable);

                if ($resultAttendanceTable->num_rows > 0) {
                    echo '<table class="attendance-table">';
                    echo '<tr><th>Name</th><th>Time</th></tr>';

                    while ($rowAttendanceTable = $resultAttendanceTable->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $rowAttendanceTable['name'] . '</td>';
                        echo '<td>' . $rowAttendanceTable['time'] . '</td>';
                        echo '</tr>';
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
    <script>

        // JavaScript to handle sidebar item clicks and apply active state
        const sidebarLinks = document.querySelectorAll('.sidebar a');

        sidebarLinks.forEach(link => {
            link.addEventListener('click', function () {
                // Remove 'active' class from all links
                sidebarLinks.forEach(link => link.classList.remove('active'));
                // Add 'active' class to the clicked link
                this.classList.add('active');

                // Check if the clicked link is the Dashboard link
                if (this.id === 'dashboardLink') {
                    // If it is, navigate to the main page
                    window.location.href = 'admin.php';
                }
            });
        });

        document.getElementById('attendanceLink').addEventListener('click', function () {
            changeTitleAndLoadAttendance();
        });

        document.getElementById('employeesLink').addEventListener('click', function () {
            changeTitle('Employees');
            loadEmployees();
        });

        document.getElementById('scheduleLink').addEventListener('click', function () {
            changeTitle('Schedule');
            loadSchedule();
        });

        document.getElementById('graphLink').addEventListener('click', function () {
            changeTitle('Graph');
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
                //additionalText = 'Attendance List. ' + "(" + dayOfWeek + ", " + formattedDate + ")";
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

        function loadSchedule() {
            // Fetch and display schedule data using AJAX
            var xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    // Display schedule data
                    document.getElementById('contentContainer').innerHTML = this.responseText;

                    // Add click event listener for each employee name to view their schedule
                    var employeeNames = document.getElementsByClassName('employee-name');
                    Array.from(employeeNames).forEach(function (element) {
                        element.addEventListener('click', function () {
                            viewEmployeeSchedule(element.dataset.employeeId);
                        });
                    });
                }
            };

            xhttp.open("GET", "get_schedule.php", true);
            xhttp.send();
        }

        function viewEmployeeSchedule(employeeId) {
            // Fetch and display the schedule for the selected employee using AJAX
            var xhttp = new XMLHttpRequest();

            xhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    // Display employee schedule
                    document.getElementById('contentContainer').innerHTML = this.responseText;
                }
            };

            xhttp.open("GET", "get_employee_schedule.php?employee_id=" + employeeId, true);
            xhttp.send();
        }

        document.getElementById('summaryLink').addEventListener('click', function () {
            changeTitleAndLoadSummary();
        });

        document.getElementById('summaryLink').addEventListener('click', function () {
            changeTitleAndLoadSummary();
        });

        function changeTitleAndLoadSummary() {
            changeTitle('Summary');

            // Fetch summary data using AJAX
            var xhttpSummary = new XMLHttpRequest();

            xhttpSummary.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    var summaryData = JSON.parse(this.responseText);
                    displaySummaryData(summaryData);
                } else if (this.readyState == 4 && this.status != 200) {
                    console.error("AJAX Error:", this.status, this.statusText);
                }
            };

            xhttpSummary.open("GET", "get_summary.php", true);
            xhttpSummary.send();
        }

        function displaySummaryData(data) {
            // Display summary data as needed
            var contentContainer = document.getElementById('contentContainer');

            // Clear the content container
            contentContainer.innerHTML = '';

            // You can handle the data and display it as you see fit for your summary
            // For example, you can create HTML elements and append them to contentContainer
            // based on the structure of your summary data.

            // Example: Display summary data in a div
            var summaryDiv = document.createElement('div');
            summaryDiv.innerHTML = '<h2>Summary Data</h2>' + JSON.stringify(data);

            // Append the summaryDiv to the content container
            contentContainer.appendChild(summaryDiv);
        }

        document.getElementById('graphLink').addEventListener('click', function () {
        changeTitleAndLoadGraph();
        });

        function changeTitleAndLoadGraph() {
            changeTitle('Graph');

            // Fetch graph data using AJAX
            var xhttpGraph = new XMLHttpRequest();

            xhttpGraph.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    var graphData = JSON.parse(this.responseText);
                    displayGraphData(graphData);
                } else if (this.readyState == 4 && this.status != 200) {
                    console.error("AJAX Error:", this.status, this.statusText);
                }
            };

            xhttpGraph.open("GET", "get_graph.php", true);
            xhttpGraph.send();
        }

        function displayGraphData(data) {
            // Display graph data as a line chart and individual cards
            var contentContainer = document.getElementById('contentContainer');

            // Clear the content container
            contentContainer.innerHTML = '';

            // Create a container for the cards
            var cardsContainer = document.createElement('div');
            cardsContainer.classList.add('cards-container', 'clearfix'); // Add clearfix for proper styling

            // Create a card for each trend
            data.forEach(function (item) {
                // Create a card element
                var card = document.createElement('div');
                card.classList.add('card');

                // Create a card body
                var cardBody = document.createElement('div');
                cardBody.classList.add('card-body');

                // Create a heading for the card
                var heading = document.createElement('h2');
                heading.textContent = item.status;

                // Create a paragraph for the total count
                var totalParagraph = document.createElement('p');
                totalParagraph.textContent = 'Total: ' + item.total;

                // Append elements to the card body
                cardBody.appendChild(heading);
                cardBody.appendChild(totalParagraph);

                // Append the card body to the card
                card.appendChild(cardBody);

                // Append the card to the cards container
                cardsContainer.appendChild(card);
            });

            // Append the cards container to the content container
            contentContainer.appendChild(cardsContainer);

            // Create a container for the line chart
            var chartContainer = document.createElement('div');
            chartContainer.classList.add('chart-container');

            // Create a canvas element for the chart
            var canvas = document.createElement('canvas');
            chartContainer.appendChild(canvas);

            // Append the chart container to the content container
            contentContainer.appendChild(chartContainer);

            // Get the 2D context of the canvas
            var ctx = canvas.getContext('2d');

            // Extract labels and data from the provided data
            var labels = data.map(function (item) {
                return item.status;
            });

            var totals = data.map(function (item) {
                return item.total;
            });

            // Create a line chart
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Attendance Trends',
                        data: totals,
                        borderColor: 'rgba(75, 192, 192, 1)', // Adjust the line color
                        borderWidth: 2,
                        fill: false
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
    </script>
</body>
</html>
