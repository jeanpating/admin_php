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
            echo '<p>Error executing the query: ' . $conn->error . '</p>';
        } else {
            if ($result->num_rows > 0) {
                // Display the data from the attendance table
                echo '<h1>Notification</h1>';
                echo '<table>';
                echo '<tr><th>Name</th><th>Status</th></tr>';

                while ($row = $result->fetch_assoc()) {
                    echo '<tr>Today</tr>';
                    echo '<tr>';
                    echo '<td>' . $row['name'] . '</td>';
                    echo '<td>' . $row['status'] . '</td>';
                    echo '</tr>';
                }

                echo '</table>';
            } else {
                echo '<p>No notifications for today.</p>';
            }
        }
        $conn->close();
        ?>
    </div>
</body>

</html>
