<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" type="text/css" href="styles/get_employees.css">
</head>

<body>

<div class="container">
    <h2 class="mb-4">Employee List</h2>

    <!-- Search Form -->
    <form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label for="searchInput">Search by Name or Employee ID:</label>
            <input type="text" class="form-control" id="searchInput" name="search" placeholder="Enter name or employee ID">
        </div>
        <button type="submit" class="btn btn-primary" style="
            padding: 10px;
            background-color: #739072;
            color: white;
            border-radius: 5px;
            cursor: pointer;">
            Search
        </button>

        <a href="admin.php" class="searchBackBtn" style="
            padding: 10px;
            margin-left: 15px;
            background-color: #CD8D7A;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;">
            Go Back
        </a>

    </form>

    <div class="list-group">
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $servername = "localhost";
        $username = "root";
        $password = "";
        $employeesDatabase = "employeesdb";
        $attendanceDatabase = "attendancedb";

        // Create connection for employees
        $conn = new mysqli($servername, $username, $password, $employeesDatabase);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Create connection for attendance
        $connAttendance = new mysqli($servername, $username, $password, $attendanceDatabase);

        if ($connAttendance->connect_error) {
            die("Connection to attendance database failed: " . $connAttendance->connect_error);
        }

        $create_table_query = "CREATE TABLE IF NOT EXISTS attendance (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status VARCHAR(255),
            clock VARCHAR(255)
        )";
        $result = $connAttendance->query($create_table_query);

        // Fetch employees from the employees database
        $sql = "SELECT emp_id, name, department FROM employees";

        // Modify the SQL query based on the search criteria
        $search = isset($_GET['search']) ? $_GET['search'] : '';

        if (!empty($search)) {
            $sql .= " WHERE name LIKE '%$search%' OR emp_id LIKE '%$search%'";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $employeeId = $row["emp_id"];
                $employeeName = $row["name"];
                $department = $row["department"];

                // Fetch the attendance status for the current employee from attendancedb
                $current_date = date("Y_m_d");
                $sqlAttendance = "SELECT status FROM attendance WHERE name = '$employeeName' AND date = '$current_date' AND clock ='AM-TIME-IN' OR clock ='PM-TIME-IN'";

                $resultAttendance = $connAttendance->query($sqlAttendance);

                if ($resultAttendance && $resultAttendance->num_rows > 0) {
                    $rowAttendance = $resultAttendance->fetch_assoc();
                    $attendanceStatus = $rowAttendance['status'];
                    $statusColor = ($attendanceStatus === 'Late') ? '#d9a71e' : (($attendanceStatus === 'Early') ? '#1fab36' : '');
                } else {
                    // If attendance status is not available, set defaults
                    $attendanceStatus = '';
                    $statusColor = '';
                }

                // Display employee details and buttons
                echo "<div class='list-group-item'>";
                echo "<a href='get_employee_details.php?emp_id=$employeeId' class='list-group-item-action'>";
                echo "<div class='d-flex w-100 justify-content-between'>";
                echo "<h5 class='mb-1'><b>$employeeName</b></h5>";

                echo "</div>";
                echo "<p class='mb-1'>Employee ID: <b>$employeeId</b>";
                echo "<p class='mb-1'><small>$department</small></p>";
                echo "</a>";
                // Display buttons for Absent and On-Official-Business when the attendance status is empty
                if (empty($attendanceStatus)) {
                    echo "<p class='employee-status' style='color: #ff0000'><small><b>Status: Not Available</b></small></p>";
                    echo "<div class='attendance-buttons'>"; 
                    // Use a span to wrap the buttons, preventing the default behavior of the <a> tag
                    echo "<span onclick='event.stopPropagation();'>";
                    echo "<button class='absent-button' onclick='markAttendance(\"Absent\", \"$employeeName\")'>Absent</button>";
                    echo "<button class='on-business-button' onclick='markAttendance(\"On-Official-Business\", \"$employeeName\")'>On-Official Business</button>";
                    echo "</span>";
                    echo "</div>";
                } else {
                    echo "<p class='employee-status' style='color: $statusColor;'><small><b>Status: $attendanceStatus</b></small></p>";
                }
                echo "</div>";
            }
        } else {
            echo "0 results";
        }

        $conn->close();
        $connAttendance->close();
        ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    function markAttendance(status, employeeName) {
        $.ajax({
            type: "POST",
            url: "mark_attendance.php", // Create a separate PHP file for handling the update
            data: { 
                status: status,
                employeeName: employeeName
            },
            success: function(response) {
                // Handle the response (if needed)
                console.log(response);
            },
            error: function(error) {
                // Handle errors (if needed)
                console.error(error);
            }
        });
    }
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
</body>
</html>
