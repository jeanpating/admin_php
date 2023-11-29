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

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeId = $_POST['employee_id']; // Make sure to include employee_id in your form

    // Check if file was uploaded without errors
    if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] == 0) {
        $targetDir = "uploads/";
        
        // Generate a unique file name using timestamp
        $timestamp = time();
        $targetFile = $targetDir . $timestamp . '_' . basename($_FILES["profile_picture"]["name"]);

        // Move the uploaded file to the destination
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
            // Update the database with the file path
            $sql = "UPDATE employees SET profile_picture_path = '$targetFile' WHERE emp_id = $employeeId";
            if ($conn->query($sql) === TRUE) {
                echo "Profile picture uploaded successfully.";
            } else {
                echo "Error updating database: " . $conn->error;
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "No file uploaded.";
    }
}

// Close the connection
$conn->close();
?>
