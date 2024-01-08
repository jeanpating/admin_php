<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/get_notification.css">
    <title>Attendance Notification</title>
</head>
    <body>
        <div class="container">
            <h1>Attendance Notification</h1>
            <?php
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
