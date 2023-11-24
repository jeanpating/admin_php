<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employeesdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employee data
$result = $conn->query("SELECT id, name FROM employees");

if ($result->num_rows > 0) {
    echo "<h1>Employees</h1><ul>";
    while ($row = $result->fetch_assoc()) {
        $employeeId = $row["id"];
        $employeeName = $row["name"];
        echo "<li><a href='javascript:void(0);' onclick='showEmployeeDetails($employeeId)'>$employeeName</a> 
              <button onclick='deleteEmployee($employeeId)'>Delete</button></li>";
    }
    echo "</ul>";
} else {
    echo "<p>No employees found.</p>";
}

// Close the connection
$conn->close();
?>
