<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        header {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .back-link {
            display: block;
            margin-bottom: 20px;
            text-align: right;
        }

        .back-link a {
            text-decoration: none;
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .employee-details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .employee-picture {
            max-width: 100px;
            max-height: 100px;
            border: 1px solid #ddd;
            border-radius: 50%; /* Make it a circle */
        }

        .employee-content-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .employee-picture-container {
            border: 3px solid #ddd;
            border-radius: 50%; /* Make it a circle */
            overflow: hidden;
            margin-right: 20px;
        }

        .file-label, .change-picture-button {
            padding: 10px;
            background-color: #333;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .file-label {
            display: inline-block;
            margin-right: 20px;
        }

        .file-input {
            display: none;
        }

        /* Modern design for employee details */
        .employee-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .employee-details p {
            margin: 0;
            padding: 10px;
            background-color: #ddd;
            border-radius: 5px;
            line-height: 1.5;
        }

        .employee-name {
            font-size: 30px;
        }

        h2 {
            margin-top: 0;
        }
    </style>

</head>

<body>

<div class="container">

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Fetch employee details
$employeeId = isset($_GET['emp_id']) ? $_GET['emp_id'] : null;
$employeeId = filter_var($employeeId, FILTER_VALIDATE_INT);

if ($employeeId === false) {
    die("Invalid employee ID");
}

$sql = "SELECT * FROM employees WHERE emp_id = $employeeId";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $employeeId = $row['emp_id'];
    $employeeName = htmlspecialchars($row['name']);
    $schedule = htmlspecialchars($row['schedule']);
    $picturePath = $row['picture_path'];
    $department = htmlspecialchars($row['department']);
    $address = htmlspecialchars($row['address']);
    $contactNumber = htmlspecialchars($row['contact_number']);
    $emailAddress = htmlspecialchars($row['email_address']);
    // $startingSchedule = htmlspecialchars($row['schedule']);
    // $finalSchedule = htmlspecialchars($row['final_schedule']);

    echo "<h2>Employee Details</h2>";
    ?><hr><?php    
    // Display employee picture at the top right with border
    if ($picturePath) {
        echo "<div class='employee-details-header'>";
        echo "<div class='employee-name-border'><p class='employee-name'><b>$employeeName</b></p></div>";
    
        // Create a container for both picture and form
        echo "<div class='employee-content-container'>";
        
        // Add a border around the profile picture
        echo "<div class='employee-picture-container'>";
        echo "<img src='$picturePath' alt='$employeeName Profile Picture' class='employee-picture'>";
        echo "</div>";
    
        // Add a form for changing the picture
        echo "<form action='' method='post' enctype='multipart/form-data' class='change-picture-form'>";
        echo "<label class='file-label'>";
        echo "<input type='file' name='new_picture' accept='image/*' class='file-input'>";
        echo "Choose a File";
        echo "</label>";
        echo "<input type='submit' value='Change Picture' class='change-picture-button'>";
        echo "</form>";
    
        echo "</div>"; // Close the employee-content-container
    
        echo "</div>"; // Close the employee-details-header
    }
    
    // Add or update the following CSS styles
    echo "<style>";
    echo ".employee-content-container { display: flex; justify-content: flex-end; align-items: center; }";
    // echo ".employee-picture-container { border: 1px solid #ddd; border-radius: 5px; margin-right: 20px; }";
    echo ".file-label { display: inline-block; margin-right: 20px; }";
    echo ".file-input { display: none; }";
    echo ".file-label, .change-picture-button { padding: 10px; background-color: #333; color: white; border-radius: 5px; cursor: pointer; }";
    echo "</style>";
    
    // Display all employee details
    echo "<p>Employee ID: $employeeId</p>";
    echo "<p>Department: $department</p>";
    echo "<p>Schedule: $schedule</p>";
    echo "<p>Address: $address</p>";
    echo "<p>Contact Number: $contactNumber</p>";
    echo "<p>Email Address: $emailAddress</p>";
    // echo "<p>Starting Schedule: $startingSchedule</p>";
    // echo "<p>Final Schedule: $finalSchedule</p>";
} else {
    echo "<p>No details found for the employee.</p>";
}

// Back link
echo "<a href='admin.php' class='back-link'>Back to Employees</a>";

// Handle form submission for changing the picture
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handlePictureChange($conn);
}

function handlePictureChange($conn) {
    $newPicture = $_FILES['new_picture'];

    // Check if a new picture was uploaded
    if ($newPicture['error'] === UPLOAD_ERR_OK) {
        $tempFilePath = $newPicture['tmp_name'];
        $newPicturePath = "profilepics/" . $newPicture['name']; // Modify the path as needed

        // Move the uploaded file to the desired location
        move_uploaded_file($tempFilePath, $newPicturePath);

        // Update the picture path in the database
        $employeeId = $_GET['emp_id'];
        $sql = "UPDATE employees SET picture_path = '$newPicturePath' WHERE emp_id = $employeeId";

        // Perform the update
        if ($conn->query($sql) === TRUE) {
            // Reload the page to reflect the changes
            header("Location: {$_SERVER['PHP_SELF']}?emp_id=$employeeId");
            exit();
        } else {
            echo "Error updating picture: " . $conn->error;
        }
    }
}

$conn->close();
?>


</div>

</body>
</html>