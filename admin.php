<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="styles/admin_styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

    <nav>
        
        
    </nav>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="#" id="dashboardLink" class="dashboard">Dashboard</a>
        <a href="#" id="attendanceLink">Attendance</a>
        <a href="#" id="employeesLink">Employees</a>
        <a href="#" id="scheduleLink">Schedule</a>
        <a href="#" id="graphLink">Graph</a>
        <br><br><br><br>
        <a href="notification.php" class="notification">Notification</a>
        <a href="#admin">Admin Profile</a>
    </div>

    <!-- Content area -->
    <div class="content" id="contentContainer">
        Hello WOrld
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
