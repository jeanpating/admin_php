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

// Fetch and display employee names with links to details
$sql = "SELECT emp_id, name, profile_picture_path FROM employees";
$result = $conn->query($sql);

// Your database connection code...

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employeeId = $row['emp_id'];
        $employeeName = $row['name'];
        $profilePicturePath = $row['profile_picture_path'];

        // Open the container div with a specific class for styling
        echo "<div class='employee-list-item-container'>";

        // Display profile picture if available
        if ($profilePicturePath) {
            echo "<img src='$profilePicturePath' alt='$employeeName Profile Picture' class='employee-picture'>";
        }

        // Open a div for the employee name and set a class for styling
        echo "<div class='employee-details'>";

        // Display employee name
        echo "<p class='employee-name' data-employee-id='$employeeId'>$employeeName</p>";

        // Close the employee details div
        echo "</div>";

        // Close the container div
        echo "</div>";
    }
} else {
    echo "<p>No employees found.</p>";
}

// Close the connection
$conn->close();
?>
