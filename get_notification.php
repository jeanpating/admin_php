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
$sql = "SELECT name, status FROM $tableName";
$result = $conn->query($sql);

if ($result === false) {
    echo 'Error executing the query: ' . $conn->error;
} else {
    if ($result->num_rows > 0) {
        // Display the data for the notification
        $notificationData = '';
        while ($row = $result->fetch_assoc()) {
            $notificationData .= $row['name'] . ': ' . $row['status'] . '<br>';
        }

        echo $notificationData;
    } else {
        echo 'No data found for the notification.';
    }
}

$conn->close();
?>
