<?php
// edit_employee_schedule.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scheduledb";
$dbemployee = "employeesdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$connEmployee = new mysqli($servername, $username, $password, $dbemployee);

if ($connEmployee->connect_error) {
    die("Connection failed: " . $connEmployee->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if "employee_id" key is set
    $employeeId = isset($_POST['employee_id']) ? $_POST['employee_id'] : null;
    $subject = $_POST['subject'];
    $time = $_POST['time'];
    $classroom = $_POST['classroom'];

    // Validate form data (add your validation logic here)

    if (isset($_POST['update'])) {
        // Update operation
        $updateScheduleSql = "UPDATE employeesdb.employees SET schedule=? WHERE emp_id=?";
        $stmtUpdateSchedule = $conn->prepare($updateScheduleSql);
        $stmtUpdateSchedule->bind_param("si", $time, $employeeId);

        if ($stmtUpdateSchedule->execute()) {
            echo "Schedule updated successfully";
        } else {
            echo "Error updating schedule in employees table: " . $stmtUpdateSchedule->error;
        }

        $stmtUpdateSchedule->close();
    } elseif (isset($_POST['insert'])) {
        // Insert operation
        $insertScheduleSql = "INSERT INTO scheduledb.employee_schedule (emp_id, subject, time, classroom) VALUES (?, ?, ?, ?)";
        $stmtInsertSchedule = $conn->prepare($insertScheduleSql);
        $stmtInsertSchedule->bind_param("isss", $employeeId, $subject, $time, $classroom);

        if ($stmtInsertSchedule->execute()) {
            echo "Inserted into employee_schedule successfully";
        } else {
            echo "Error inserting into employee_schedule: " . $stmtInsertSchedule->error;
        }

        $stmtInsertSchedule->close();
    } elseif (isset($_POST['delete'])) {
        // Delete operation
        $entryId = $_POST['entry_id'];
        $deleteSql = "DELETE FROM scheduledb.employee_schedule WHERE id = ?";
        $stmtDelete = $conn->prepare($deleteSql);
        $stmtDelete->bind_param("i", $entryId);

        if ($stmtDelete->execute()) {
            echo "Entry deleted successfully";
        } else {
            echo "Error deleting entry: " . $stmtDelete->error;
        }

        $stmtDelete->close();
    }
}

$employeeId = $_GET['employee_id'];
$sql = "SELECT emp_id, name, picture_path, schedule FROM employeesdb.employees WHERE emp_id = $employeeId";
$result = $connEmployee->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee Schedule</title>
    <link rel="stylesheet" href="your-styles.css"> <!-- Include your common styles -->
    <style>
        /* styles.css */

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
        }

        .employee-schedule-container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .employee-picture {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #B6C4B6;
        }

        .employee-schedule-table-container {
            margin-top: 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #4caf50;
            color: #fff;
        }
        .button-container {
            text-align: right;
            margin-top: 20px;
            padding: 10px;
        }

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

        .back-button:hover {
            background-color: #555;
        }
        input {
            border-radius: 10px;
        }
    </style>
</head>

<body>

<?php
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $employeeName = $row['name'];
    $picturePath = $row['picture_path'];
    $schedule = $row['schedule'];

    echo "<div class='employee-schedule-container'>";
    // echo "<img src='$picturePath' alt='$employeeName' class='employee-picture'>";
    echo "<h2>Edit $employeeName's Schedule</h2>";

    // Display the existing schedule information
    echo "<p>Current Schedule: $schedule</p>";

    // Add a form for editing schedule
    echo "<form action='' method='post'>"; // Empty action attribute to submit to the same page

    // Add hidden input for employee ID
    echo "<input type='hidden' name='employee_id' value='$employeeId'>";

    // Add input fields for subject, time, and classroom with correct names
    echo "<label for='subject'>Subject:</label>";
    echo "<input type='text' name='subject' id='subject' value='" . (isset($_POST['subject']) ? $_POST['subject'] : '') . "' required><br>";

    echo "<label for='time'>Time:</label>";
    echo "<input type='text' name='time' id='time' value='" . (isset($_POST['time']) ? $_POST['time'] : '') . "' required><br>";

    echo "<label for='classroom'>Classroom:</label>";
    echo "<input type='text' name='classroom' id='classroom' value='" . (isset($_POST['classroom']) ? $_POST['classroom'] : '') . "' required><br>";

    // Add submit buttons for update, insert, and delete
    echo "<input type='submit' name='update' value='Update' style='background-color: #304D30;'>";
    echo "<input type='submit' name='insert' value='Insert'>";

    echo "</form>";

    // Display the employee_schedule table for the selected employee
    $sqlAllEntries = "SELECT id, emp_id, subject, time, classroom FROM scheduledb.employee_schedule WHERE emp_id = $employeeId";
    $resultAllEntries = $conn->query($sqlAllEntries);

    echo "<div class='employee-schedule-table-container'>";
    echo "<h2>Employee Schedule Table</h2>";

    if ($resultAllEntries && $resultAllEntries->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Employee ID</th><th>Subject</th><th>Time</th><th>Classroom</th><th>Actions</th></tr>";
        while ($entry = $resultAllEntries->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $entry['emp_id'] . "</td>";
            echo "<td>" . $entry['subject'] . "</td>";
            echo "<td>" . $entry['time'] . "</td>";
            echo "<td>" . $entry['classroom'] . "</td>";
            echo "<td>";
            echo "<form action='' method='post'>";
            echo "<input type='hidden' name='entry_id' value='" . $entry['id'] . "'>";
            echo "<input type='submit' name='delete' value='Delete' style='background-color: red;'>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No entries found in the employee_schedule table for the selected employee.";
    }
    ?>
    <hr>
        <div class="button-container">
            <a href='admin.php' class='back-button'>
                Back
            </a>
        </div>
    <?php
    echo "</div>";

    echo "</div>";
} else {
    echo "No schedule information found for the selected employee.";
}

// Close the connections
$conn->close();
$connEmployee->close();
?>


</body>

</html>
