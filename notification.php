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
        // Display the data from the attendance table
        echo '<h1>Notification</h1>';
        echo '<table border="1">';
        echo '<tr><th>Name</th><th>Status</th></tr>';

        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['name'] . '</td>';
            echo '<td>' . $row['status'] . '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo 'No data found in the attendance table for today.';
    }
}

$conn->close();
?>
