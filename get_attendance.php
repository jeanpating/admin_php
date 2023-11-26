<html>
    <head>
    <style>
        .modern-table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            font-size: 16px;
        }

        .modern-table th, .modern-table td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 12px;
        }

        .modern-table th {
            background-color: #f2f2f2;
        }

        .modern-table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .modern-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>

    </head>
<body>
    <?php
    // Replace with your actual database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "attendancedb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get the current date
    $currentDate = date("d_m_Y"); // Format the date as "24_11_2023"

    // Query to get the list of attendance tables
    $tableName = "attendance_table_" . $currentDate;
    $sql = "SELECT * FROM $tableName";
    $result = $conn->query($sql);

    if ($result === false) {
        echo 'Error executing the query: ' . $conn->error;
    } else {
        if ($result->num_rows > 0) {
            // Display the data from the attendance table with dynamic background colors and text color
            echo '<div style="overflow-x:auto;">';  // Add this div for horizontal scrolling
            echo '<table class="modern-table">';
            echo '<thead>';
            echo '<tr><th>id</th><th>name</th><th>time</th><th>status</th></tr>';
            echo '</thead>';
            echo '<tbody>';

            while ($row = $result->fetch_assoc()) {
                // Determine the background color based on the 'status' value
                $backgroundColor = ($row['status'] == 'Early') ? 'green' : (($row['status'] == 'Late') ? 'red' : '');

                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . $row['name'] . '</td>';
                echo '<td>' . $row['time'] . '</td>';
                echo '<td style="background-color: ' . $backgroundColor . '; color: white;">' . $row['status'] . '</td>'; // Add this line for the 'status' column
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo 'No data found in the attendance table for today.';
        }
    }

    $conn->close();
    ?>
</body>
</html>

