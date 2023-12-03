<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>

    <style>
        body {
            font-family: Arial, sans-serif;
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
            border-radius: 5px;
        }

        .employee-name-border {
            font-weight: Bold;
            text-align: right; /* Align employee name to the right */
        }

        /* Add more styles as needed */
    </style>
</head>

<body>

<div class="container">

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
        $startingSchedule = htmlspecialchars($row['schedule']);
        $finalSchedule = htmlspecialchars($row['final_schedule']);

        echo "<h2>Employee Details</h2>";
        ?><hr><?php    
        // Display employee picture at the top right with border
        if ($picturePath) {
            echo "<div class='employee-details-header'>";
            echo "<div class='employee-name-border'><p class='employee-name'>$employeeName</p></div>";
            echo "<img src='$picturePath' alt='$employeeName's Profile Picture' class='employee-picture'>";
            echo "</div>";
        }

        // Display all employee details
        echo "<p>Employee ID: $employeeId</p>";
        echo "<p>Department: $department</p>";
        echo "<p>Schedule: $schedule</p>";
        echo "<p>Address: $address</p>";
        echo "<p>Contact Number: $contactNumber</p>";
        echo "<p>Email Address: $emailAddress</p>";
        echo "<p>Starting Schedule: $startingSchedule</p>";
        echo "<p>Final Schedule: $finalSchedule</p>";
    } else {
        echo "<p>No details found for the employee.</p>";
    }

    // Back link
    echo "<a href='admin.php' class='back-link'>Back to Employees</a>";

    // Close the connection
    $conn->close();
    ?>

</div>

</body>
</html>
