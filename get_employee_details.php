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
            display: inline-block;
            margin-bottom: 20px;
            text-align: right;
        }

        .employee-details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .employee-picture {
            max-width: 150px;
            max-height: 150px;
            border: 3px solid #ddd;
            border-radius: 50%;
            margin-right: 20px;
        }

        .file-label,
        .change-picture-button {
            font-size: 10px;
            padding: 10px;
            background-color: #333;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            display: inline-block;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }

        .file-label:hover,
        .change-picture-button:hover {
            background-color: #555;
        }

        .file-input {
            display: none;
        }

        /* Modern design for employee details */
        table {
            width: 100%;
        }

        .employee-details th,
        .employee-details td {
            padding: 10px;
            background-color: #ddd;
            border-radius: 5px;
        }

        .employee-details th {
            text-align: left;
            background-color: #333;
            color: white;
        }

        .employee-details p {
            margin: 0;
        }

        .button-container {
            text-align: right;
            margin-top: 20px;
            padding: 10px;
        }

        .edit-button,
        .back-button {
            text-decoration: none;
            margin-right: 20px;
            padding: 10px 20px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .edit-button:hover,
        .back-button:hover {
            background-color: #555;
        }
        .employee-details-container{
            width: 75%;
            float: left;
        }
        .employee-picture-container {
            width: 25%;
            float: right;
        }
        img {
            float: right;
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

    echo "<div class='employee-details-container'>";
    echo "<h1>Employee Details</h1>";
    echo "</div>";

    echo "<div class='employee-picture-container'>";
    echo "<img src='$picturePath' alt='$employeeName Profile Picture' class='employee-picture'>";
    echo "</div>";

    ?>

    <hr>

    <?php    
    // Display employee picture at the top right with border
    if ($picturePath) {
        echo "<div class='employee-details-header'>";
        echo "<div class='employee-name-border'><h2 class='employee-name'><b>$employeeName</b></h2></div>";
    
        // Create a container for both picture and form
        echo "<div class='employee-content-container'>";
        
        //Add a form for changing the picture
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
    // echo ".employee-content-container { display: flex; justify-content: flex-end; align-items: center; }";
    // echo ".employee-picture-container { border: 1px solid #ddd; border-radius: 5px; margin-right: 20px; }";
    echo ".file-label { display: inline-block; margin-right: 20px; }";
    echo ".file-input { display: none; }";
    echo ".file-label, .change-picture-button { padding: 10px; background-color: #333; color: white; border-radius: 5px; cursor: pointer; }";
    echo "</style>";
    ?>
    
    <!--Display all employee details-->
    <table class="employee-details">
        <tr>
            <th>Employee ID</th>
            <td><?php echo $employeeId; ?></td>
        </tr>
        <tr>
            <th>Employee Name</th>
            <td><?php echo $employeeName; ?></td>
        </tr>
        <tr>
            <th>Department</th>
            <td><?php echo $department; ?></td>
        </tr>
        <tr>
            <th>Schedule</th>
            <td><?php echo $schedule; ?></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><?php echo $address; ?></td>
        </tr>
        <tr>
            <th>Contact Number</th>
            <td><?php echo $contactNumber; ?></td>
        </tr>
        <tr>
            <th>Email Address</th>
            <td><?php echo $emailAddress; ?></td>
        </tr>
    </table>
<?php
} else {
    echo "<p>No details found for the employee.</p>";
}



$conn->close();
?>
<hr>
    <!-- Back and Edit buttons -->
    <div class="button-container">
        <a href='admin.php' class='back-button'>
            Back to Employees
        </a>
        <a href='edit_employee_details.php?emp_id=<?php echo $employeeId; ?>' class='edit-button'>
            Edit Employee
        </a>
    </div>
</div>
</body>
</html>