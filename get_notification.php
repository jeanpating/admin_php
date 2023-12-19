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
            width: 300px;
            text-align: center;
        }

        h1 {
            color: #333;
        }

        p {
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Attendance Notification</h1>
        <?php
        // ... Your PHP code here ...
        if ($result === false) {
            echo '<p>Error executing the query: ' . $conn->error . '</p>';
        } else {
            if ($result->num_rows > 0) {
                // Display the data for the notification
                $notificationData = '';
                while ($row = $result->fetch_assoc()) {
                    $notificationData .= $row['name'] . ': ' . $row['status'] . '<br>';
                }

                echo $notificationData;
            } else {
                echo '<p>No data found for the notification.</p>';
            }
        }
        $conn->close();
        ?>
    </div>
</body>

</html>
