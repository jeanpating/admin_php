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

$createTableSql = "CREATE TABLE IF NOT EXISTS scheduledb.employee_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emp_id INT,
    name VARCHAR(255),
    am_time_in VARCHAR(255),
    am_time_out VARCHAR(255),
    pm_time_in VARCHAR(255),
    pm_time_out VARCHAR(255),
    CONSTRAINT fk_employee_schedule_emp_id FOREIGN KEY (emp_id) REFERENCES scheduledb.employees(emp_id) ON UPDATE CASCADE ON DELETE CASCADE
)";


// if ($conn->query($createTableSql) === TRUE) {
//     echo "Table 'employee_schedule' created or already exists.";
// } else {
//     echo "Error creating table: " . $conn->error;
// }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if "employee_id" key is set
    $employeeId = isset($_POST['employee_id']) ? $_POST['employee_id'] : null;
    $employeeName = isset($_POST['name']) ? $_POST['name'] : null;
    $amTimeIn = isset($_POST['am_time_in']) ? $_POST['am_time_in'] : null;
    $amTimeOut = isset($_POST['am_time_out']) ? $_POST['am_time_out'] : null;
    $pmTimeIn = isset($_POST['pm_time_in']) ? $_POST['pm_time_in'] : null;
    $pmTimeOut = isset($_POST['pm_time_out']) ? $_POST['pm_time_out'] : null;
    $alertMessage = isset($_POST['alertMessage']) ? $_POST['alertMessage'] : null;
    $alertClass = isset($_POST['alertClass']) ? $_POST['alertClass'] : null;


    // Validate form data (add your validation logic here)

    if (isset($_POST['update'])) {
        // Update operation
        $updateScheduleSql = "UPDATE scheduledb.employee_schedule SET am_time_in=?, am_time_out=?, pm_time_in=?, pm_time_out=? WHERE emp_id=?";
        $stmtUpdateSchedule = $conn->prepare($updateScheduleSql);
        $stmtUpdateSchedule->bind_param("ssssi", $amTimeIn, $amTimeOut, $pmTimeIn, $pmTimeOut, $employeeId);

        if ($stmtUpdateSchedule->execute()) {
            $alertMessage = "Schedule updated successfully";
            $alertClass = "alert-message-success";
        } else {
            $alertMessage = "Error updating schedule in employees table: " . $stmtUpdateSchedule->error;
            $alertClass = "alert-message-error";
        }
        
        echo "<div class='alert-message $alertClass' style='display: none;'>$alertMessage</div>";

        $stmtUpdateSchedule->close();
    } elseif (isset($_POST['insert'])) {
        //Insert operation
        $insertScheduleSql = "INSERT INTO scheduledb.employee_schedule (emp_id, name, am_time_in, am_time_out, pm_time_in, pm_time_out) VALUES (?, ?, ?, ?, ?, ?)";
        $stmtInsertSchedule = $conn->prepare($insertScheduleSql);
        $stmtInsertSchedule->bind_param("isssss", $employeeId, $employeeName, $amTimeIn, $amTimeOut, $pmTimeIn, $pmTimeOut);
        
        $alertMessage = "";
        if ($stmtInsertSchedule->execute()) {
            echo "<script>alert('Inserted into employee schedule successfully');</script>";
        } else {
            echo "<script>alert('Error inserting into employee schedule: " . $stmtInsertSchedule->error . "');</script>";
        }
        
        echo "<div class='alert-message' style='display: none;'>" . $alertMessage . "</div>";

        $stmtInsertSchedule->close();
    } elseif (isset($_POST['delete'])) {
        // Delete operation
        $entryId = $_POST['entry_id'];
        $deleteSql = "DELETE FROM scheduledb.employee_schedule WHERE id = ?";
        $stmtDelete = $conn->prepare($deleteSql);
        $stmtDelete->bind_param("i", $entryId);

        $alertMessage = "";
        if ($stmtDelete->execute()) {
            echo "<script>alert('Entry deleted successfully');</script>";
        } else {
            echo "<script>alert('Error deleting entry: " . $stmtDelete->error . "');</script>";
        }
        
        echo "<div class='alert-message' style='display: none;'>" . $alertMessage . "</div>";

        $stmtDelete->close();
    }
}

$employeeId = isset($_GET['employee_id']) ? $_GET['employee_id'] : null;
$sql = "SELECT emp_id, name, picture_path FROM employeesdb.employees WHERE emp_id = $employeeId";
$result = $connEmployee->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee Schedule</title>
    <style>
        .show-alert {
            display: block;
            background: black;
        }

        .alert-message,
        .alert-message-success,
        .alert-message-error {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #31708f;
            background-color: #d9edf7;
            display: none;
        }

        /* Style for success message */
        .alert-message-success {
            color: #3c763d;
            background-color: #dff0d8;
        }

        /* Style for error message */
        .alert-message-error {
            color: #a94442;
            background-color: #f2dede;
        }
    </style>
    <link rel="stylesheet" href="styles/edit_employee_schedule.css">
</head>

<body>

<?php
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $employeeName = $row['name'];
    $picturePath = $row['picture_path'];

    echo "<div class='employee-schedule-container'>";
    // echo "<img src='$picturePath' alt='$employeeName' class='employee-picture'>";
    echo "<h2>Edit $employeeName's Schedule</h2>";

    // Display the existing schedule information
    // echo "<p>Current Schedule: $schedule</p>";

    // Add a form for editing schedule
    echo "<form action='' method='post'>"; // Empty action attribute to submit to the same page

    // Add hidden input for employee ID
    echo "<input type='hidden' name='employee_id' value='$employeeId'>";

    // Add hidden input for employee Name
    echo "<input type='hidden' name='name' value='$employeeName'>";

    // Add input fields for subject, time, and classroom with correct names
    
    echo "<label for='am_time_in'>AM Time In:</label>";
    echo "<input type='text' name='am_time_in' id='am_time_in' value='" . (isset($_POST['am_time_in']) ? $_POST['am_time_in'] : '') . "' required><br>";
    
    echo "<label for='am_time_out'>AM Time Out:</label>";
    echo "<input type='text' name='am_time_out' id='am_time_out' value='" . (isset($_POST['am_time_out']) ? $_POST['am_time_out'] : '') . "' required><br>";
    
    echo "<label for='pm_time_in'>PM Time In:</label>";
    echo "<input type='text' name='pm_time_in' id='pm_time_in' value='" . (isset($_POST['pm_time_in']) ? $_POST['pm_time_in'] : '') . "' required><br>";
    
    echo "<label for='pm_time_out'>PM Time Out:</label>";
    echo "<input type='text' name='pm_time_out' id='pm_time_out' value='" . (isset($_POST['pm_time_out']) ? $_POST['pm_time_out'] : '') . "' required><br>";

    // Add submit buttons for update, insert, and delete
    echo "<input type='submit' name='update' value='Update' style='background-color: #304D30;'>";
    echo "<input type='submit' name='insert' value='Insert'>";

    echo "</form>";

    // Display the employee_schedule table for the selected employee
    $sqlAllEntries = "SELECT id, emp_id, am_time_in, am_time_out, pm_time_in, pm_time_out FROM scheduledb.employee_schedule WHERE emp_id = $employeeId";
    $resultAllEntries = $conn->query($sqlAllEntries);


    echo "<div class='employee-schedule-table-container'>";
    echo "<h2>Employee Schedule Table</h2>";

    if ($resultAllEntries && $resultAllEntries->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Employee ID</th><th>AM Time In</th><th>AM Time Out</th><th>PM Time In</th><th>PM Time Out</th><th>Actions</th></tr>";
    
        while ($entry = $resultAllEntries->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $entry['emp_id'] . "</td>";
            echo "<td>" . $entry['am_time_in'] . "</td>";
            echo "<td>" . $entry['am_time_out'] . "</td>";
            echo "<td>" . $entry['pm_time_in'] . "</td>";
            echo "<td>" . $entry['pm_time_out'] . "</td>";
            echo "<td>";
            echo "<form action='' method='post'>";
            echo "<input type='hidden' name='entry_id' value='" . $entry['id'] . "'>";
            echo "<input type='submit' name='delete' value='Delete' style='background-color: red;'>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
    
        echo "</table>";
        echo "</div>"; // Close the 'employee-schedule-container' div
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
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var alertMessages = document.querySelectorAll('.alert-message');

        alertMessages.forEach(function (message) {
            if (message.innerText.trim() !== '') {
                message.classList.add('show-alert', message.classList[1]);
                alert(message.innerText); // Display alert
            }
        });
    });
</script>

</body>

</html>
