<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";

// Create connection for attendancedb
$connAttendance = new mysqli($servername, $username, $password, "attendancedb");

// Check connection
if ($connAttendance->connect_error) {
    die("Connection failed: " . $connAttendance->connect_error);
}

// SQL query to get summary data grouped by name and status
$sqlSummary = "SELECT name, status, COUNT(*) as total FROM attendance GROUP BY name, status";
$resultSummary = $connAttendance->query($sqlSummary);

// Check if there are results
if ($resultSummary->num_rows > 0) {
    // Create an array to store summary data
    $summaryData = array();

    while ($rowSummary = $resultSummary->fetch_assoc()) {
        // Store data in the array
        $name = $rowSummary['name'];
        $status = $rowSummary['status'];
        $total = $rowSummary['total'];

        $summaryData[] = array('name' => $name, 'status' => $status, 'total' => $total);
    }

    // Close the connection for attendancedb
    $connAttendance->close();
} else {
    // No results found
    echo json_encode(array('error' => 'No data available.'));
    // Close the connection for attendancedb
    $connAttendance->close();
    exit(); // exit the script if there is no data
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Summary</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h2>Attendance Summary</h2>

<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($summaryData as $row) {
            echo "<tr>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>" . $row['total'] . "</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>
