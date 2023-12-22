<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

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
    // DB
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

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the selected date from the form
        $selectedDate = $_POST["selected_date"];

        // Query to get attendance data for the selected date
        $sql = "SELECT * FROM attendance WHERE date = '$selectedDate'";
        $result = $conn->query($sql);

        if ($result === false) {
            echo 'Error executing the query: ' . $conn->error;
        } else {
            if ($result->num_rows > 0) {
                // Display the data from the 'attendance' table with dynamic background colors and text color
                echo '<div style="overflow-x:auto;">';  // Add this div for horizontal scrolling
                echo '<table class="modern-table">';
                echo '<thead>';
                echo '<tr><th>id</th><th>name</th><th>time</th><th>status</th></tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($row = $result->fetch_assoc()) {
                    // Determine the background color based on the 'status' value
                    $backgroundColor = ($row['status'] == 'Early') ? '#1fab36' : (($row['status'] == 'Late') ? '#d9a71e' : '');

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
                echo 'No data found in the attendance table for the selected date.';
            }
        }
    }
    ?>

    <!-- Form for selecting a date -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Select Date: <input type="date" name="selected_date">
        <input type="submit" value="Submit">
    </form>
    <a href="javascript:history.go(-1)" class="back-button"><</a>
    <?php
    $conn->close();
    ?>
</body>
</html>
