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
        .downloadButton {
            display: inline-block;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            background-color: #007bff;
            color: #fff;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        /* Change background color on hover for a button-like effect */
        a.downloadButton:hover {
            background-color: #0056b3;
            border-color: #0056b3;
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
                echo '<tr><th>NAME</th><th>TIME</th><th>STATUS</th><th>CLOCK TYPE</th></tr>';
                echo '</thead>';
                echo '<tbody>';

                while ($row = $result->fetch_assoc()) {
                    // Determine the background color based on the 'status' value
                    $backgroundColor = ($row['status'] == 'Early') ? '#1fab36' : (($row['status'] == 'Late') ? '#d9a71e' : '');

                    echo '<tr>';
                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>' . $row['time'] . '</td>';
                    echo '<td style="background-color: ' . $backgroundColor . '; color: white;">' . $row['status'] . '</td>'; // Add this line for the 'status' column
                    echo '<td>' .$row['clock'] . '</td>';
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
    <!--DOWNLOAD SUMMARY-->
    <a href="monthly_summary.php" class="downloadButton">Download Summary</a>

    <!-- Form for selecting a date -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Select Date: <input type="date" name="selected_date">
        <input type="submit" value="Submit" name="submit">
    </form>
    <a href="javascript:history.go(-1)" class="back-button"><</a>
    <?php
    $conn->close();
    ?>
</body>
</html>
