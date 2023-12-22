<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Notification</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 70%;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        p {
            color: #666;
        }
        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 10px;
            text-decoration: none;
            color: #333;
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
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
                    echo '<table>';
                    echo '<tr><th>Name</th><th>Status</th></tr>';

                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['name'] . '</td>';
                        echo '<td>' . $row['status'] . '</td>';
                        echo '</tr>';
                    }

                    echo '</table>';
                } else {
                    echo '<p>No attendance data for the selected date.</p>';
                }
            }

            $conn->close();
        }
        ?>
    </div>
</body>

</html>
