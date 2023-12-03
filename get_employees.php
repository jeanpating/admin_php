<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "employeesdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employees from the database
$sql = "SELECT emp_id, name, department FROM employees";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<table border='1'><tr><th>Employee ID</th><th>Name</th><th>Department</th><th>Action</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["emp_id"]. "</td>";
        echo "<td>" . $row["name"]. "</td>";
        echo "<td>" . $row["department"]. "</td>";
        // Add a "View" button that redirects to get_employee_details.php
        echo "<td><a href='get_employee_details.php?emp_id=" . $row["emp_id"] . "'>View</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

$conn->close();
?>
