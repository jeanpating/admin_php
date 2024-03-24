<?php
// Include database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "scheduledb";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required parameters are present
    if (isset($_POST['id']) && isset($_POST['time']) && isset($_POST['subject']) && isset($_POST['classroom'])) {
        // Retrieve parameters from POST request
        $entryId = $_POST['id'];
        $newTime = $_POST['time'];
        $newSubject = $_POST['subject'];
        $newClassroom = $_POST['classroom'];

        // Perform database update
        $updateSql = "UPDATE schedule SET time = ?, subject = ?, classroom = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($updateSql);
        $stmtUpdate->bind_param("sssi", $newTime, $newSubject, $newClassroom, $entryId);

        if ($stmtUpdate->execute()) {
            // Update successful
            echo "Entry updated successfully";
        } else {
            // Update failed
            echo "Error updating entry: " . $stmtUpdate->error;
        }

        // Close the prepared statement
        $stmtUpdate->close();
    } else {
        // Required parameters are missing
        echo "Error: Required parameters are missing";
    }
} else {
    // Not a POST request
    echo "Error: Only POST requests are allowed";
}

// Close the database connection
$conn->close();
?>
