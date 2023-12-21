<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scheduledb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if form is submitted
    if (isset($_POST['submit'])) {
        // Get the column name to delete
        $columnToDelete = $_POST['column_to_delete'];

        // SQL to delete the column
        $deleteColumnSql = "ALTER TABLE employee_schedule DROP COLUMN $columnToDelete";

        // Execute the query
        if ($conn->query($deleteColumnSql) === TRUE) {
            echo "Column $columnToDelete deleted successfully.";
        } else {
            echo "Error deleting column: " . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Column</title>
</head>
<body>

    <h2>Delete Column</h2>
    <form method="post" action="">
        <label for="column_to_delete">Column Name to Delete:</label>
        <input type="text" name="column_to_delete" id="column_to_delete" required>
        <br>
        <input type="submit" name="submit" value="Delete Column">
    </form>

</body>
</html>
