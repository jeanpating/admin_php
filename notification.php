<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles/notification.css">
    <title>Attendance Notification</title>
</head>

<body>
    <div class="container">
        <!-- Back button -->
        <a href="javascript:history.go(-1)" class="back-button"><</a>
        <?php
        // get_notifications.php

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            // Get the date parameter from the URL
            $selectedDate = isset($_GET["date"]) ? $_GET["date"] : date("Y-m-d");

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

            // Query to get attendance data for the selected date
            $sql = "SELECT * FROM attendance WHERE DATE(date) = '$selectedDate'";
            $result = $conn->query($sql);

            if ($result === false) {
                echo '<p>Error executing the query: ' . $conn->error . '</p>';
            } else {
                if ($result->num_rows > 0) {
                    // Determine the title based on the date difference
                    $today = date("Y-m-d");
                    $dateDifference = date_diff(date_create($selectedDate), date_create($today))->format('%a');

                    if ($dateDifference == 0) {
                        $title = 'Today';
                    } elseif ($dateDifference == 1) {
                        $title = 'Yesterday';
                    } else {
                        $title = date('F j, Y', strtotime($selectedDate));
                    }

                    // Display the title
                    echo '<h1>' . $title . '</h1>';

                    // Display the data from the attendance table
                    while ($row = $result->fetch_assoc()) {
                        $status = '';
                        
                        // Check the clock value and set the status accordingly
                        if ($row['clock'] == 'AM-TIME-IN' || $row['clock'] == 'PM-TIME-IN') {
                            $status = 'Timed In';
                        } elseif ($row['clock'] == 'AM-TIME-OUT' || $row['clock'] == 'PM-TIME-OUT') {
                            $status = 'Timed Out';
                        }

                        // Display the h2 element with name, time, and status
                        echo '<p>' . '<b>' . $row['time'] . '</b>' . ': '. $row['name'] . ' has ' . $status . '</p    >';
                    }
                } else {
                    echo '<p>No notifications for today.</p>';
                }
            }

            $conn->close();
        }
        ?>
    </div>
</body>

</html>
