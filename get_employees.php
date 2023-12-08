<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <title>Employee List</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            background-color: #ffffff;
            box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }

        .list-group-item {
            background-color: #ffffff;
            border: 2px solid rgba(0, 0, 0, 0.125);
            border-radius: 5px;
            margin-bottom: 10px;
            position: relative;
        }

        .attendance-buttons {
            position: absolute;
            top: 0;
            right: 0;
            display: flex;
            flex-direction: column;
            margin: 10px;
        }

        .absent-button, .on-business-button {
            width: 100%;
            margin-bottom: 5px;
            color: #ffffff; /* Text color white */
            background-color: #ff0000; /* Red */
            border: none;
            padding: 3px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .on-business-button {
            background-color: #4287f5; /* Blue */
        }

        .absent-button {
            background-color: #ff0000; /* Red */
        }

        .absent-button:hover,
        .on-business-button:hover {
            background-color: #555; /* Darker background on hover */
        }

        .absent-button {
            background-color: #ff0000; /* Red */
        }

        .employee-status {
            font-weight: bold;
        }

        @media (max-width: 576px) {
            .attendance-buttons {
                flex-direction: row;
            }

            .absent-button, .on-business-button {
                width: 48%;
                margin-right: 5px;
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4">Employee List</h2>
    <div class="list-group">

        <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $employeesDatabase = "employeesdb";
            $attendanceDatabase = "attendancedb";

            // Create connection for employees
            $conn = new mysqli($servername, $username, $password, $employeesDatabase);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Create connection for attendance
            $connAttendance = new mysqli($servername, $username, $password, $attendanceDatabase);

            // Check connection for attendance
            if ($connAttendance->connect_error) {
                die("Connection to attendance database failed: " . $connAttendance->connect_error);
            }

            $current_date = date("d_m_Y");
            $table_name = "attendance_table_" . $current_date;

            $create_table_query = "CREATE TABLE IF NOT EXISTS $table_name (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255),
                time VARCHAR(255),
                status VARCHAR(255)
            )";
            $result = $connAttendance->query($create_table_query);

            // Fetch employees from the employees database
            $sql = "SELECT emp_id, name, department FROM employees";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $employeeId = $row["emp_id"];
                    $employeeName = $row["name"];
                    $department = $row["department"];

                    // Fetch the attendance status for the current employee from attendancedb
                    $current_date = date("d_m_Y");
                    $table_name = "attendance_table_" . $current_date;
                    $sqlAttendance = "SELECT status FROM $table_name WHERE name = '$employeeName'";
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
                    echo "<h5 class='mb-1'>$employeeName</h5>";
                    echo "<p class='mb-1'><small>$department</small></p>";
                    echo "</div>";
                    echo "<p class='mb-1'>Employee ID: <b>$employeeId</b>";

                    // Display buttons for Absent and On-Official-Business when the attendance status is empty
                    if (empty($attendanceStatus)) {
                        echo "<p class='employee-status' style='color: #ff0000'>Status: Not Available";
                        echo "<div class='attendance-buttons'>";
                        echo "<button class='absent-button' onclick='markAttendance(\"Absent\", \"$employeeName\")'>Absent</button>";
                        echo "<button class='on-business-button' onclick='markAttendance(\"On-Official-Business\", \"$employeeName\")'>On-Official Business</button>";
                        echo "</div>";
                    } else {
                        echo "<p class='employee-status' style='color: $statusColor;'>Status: $attendanceStatus</p>";
                    }

                    echo "</a>";
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

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
