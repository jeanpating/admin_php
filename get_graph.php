
<?php
// get_graph.php

// Replace with your actual database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendancedb";

// Create connection
$connection = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Your SQL query to calculate totals for each status
$current_date = date("Y-m");
$sql = "SELECT status, COUNT(*) as total FROM attendance WHERE DATE_FORMAT(date, '%Y-%m') = '$current_date' and clock ='AM-TIME-IN' GROUP BY status";

// Execute the query
$result = mysqli_query($connection, $sql);

// Check for errors
if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}

// Define all possible statuses
$allStatuses = ['on-time', 'early', 'late', 'absent', 'on-official business', 'perfect attendance'];

// Fetch the data and convert it to JSON
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Create an associative array to store the counts for each status
$statusCounts = [];

// Initialize counts for all statuses to 0
foreach ($allStatuses as $status) {
    $statusCounts[$status] = 0;
}

// Populate counts from the fetched data
foreach ($data as $row) {
    $status = strtolower($row['status']);
    $statusCounts[$status] = (int)$row['total'];
}

// Convert the associative array to a numerically indexed array
$finalData = [];
foreach ($statusCounts as $status => $count) {
    $finalData[] = ['status' => $status, 'total' => $count];
}

// Return JSON response
echo json_encode($finalData);

// Close the database connection
mysqli_close($connection);
?>
