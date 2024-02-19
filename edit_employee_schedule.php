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
    $createTableSql1 = "CREATE TABLE IF NOT EXISTS scheduledb.employee_schedule (
        
    )";

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
        <link rel="stylesheet" type="text/css" href="styles/edit_employee_schedule.css">
    </head>

    <body>
    <div class="container">
    <?php
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $employeeName = $row['name'];
        $picturePath = $row['picture_path'];
        echo "<div class='form-container'>";
        echo "<div class='employee-schedule-container'>";
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
        echo "<input type='text' name='am_time_in' id='am_time_in' value='" . (isset($_POST['am_time_in']) ? $_POST['am_time_in'] : '') . "' style='border-radius: 10px;' required><br>";
        
        echo "<label for='am_time_out'>AM Time Out:</label>";
        echo "<input type='text' name='am_time_out' id='am_time_out' value='" . (isset($_POST['am_time_out']) ? $_POST['am_time_out'] : '') . "' style='border-radius: 10px;' required><br>";
        
        echo "<label for='pm_time_in'>PM Time In:</label>";
        echo "<input type='text' name='pm_time_in' id='pm_time_in' value='" . (isset($_POST['pm_time_in']) ? $_POST['pm_time_in'] : '') . "' style='border-radius: 10px;' required><br>";
        
        echo "<label for='pm_time_out'>PM Time Out:</label>";
        echo "<input type='text' name='pm_time_out' id='pm_time_out' value='" . (isset($_POST['pm_time_out']) ? $_POST['pm_time_out'] : '') . "' style='border-radius: 10px;' required><br>";

        // Add submit buttons for update, insert, and delete
        echo "<input type='submit' name='update' value='Update' style='background-color: #65B741; color: white; border-radius: 10px;'>";
        echo "<input type='submit' name='insert' value='Insert' style='background-color: #ECB159; color: white; border-radius: 10px;'>";

        echo "</form>";
        
        $sqlAllEntries = "SELECT id, emp_id, am_time_in, am_time_out, pm_time_in, pm_time_out FROM scheduledb.employee_schedule WHERE emp_id = $employeeId";
        $resultAllEntries = $conn->query($sqlAllEntries);

        echo "<h2>Current Schedule</h2>";

        if ($resultAllEntries && $resultAllEntries->num_rows > 0) {
            echo "<table border='1'>";
            echo "<tr><th>AM Time In</th><th>AM Time Out</th><th>PM Time In</th><th>PM Time Out</th><th>Actions</th></tr>";
        
            while ($entry = $resultAllEntries->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $entry['am_time_in'] . "</td>";
                echo "<td>" . $entry['am_time_out'] . "</td>";
                echo "<td>" . $entry['pm_time_in'] . "</td>";
                echo "<td>" . $entry['pm_time_out'] . "</td>";
                echo "<td>";
                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='entry_id' value='" . $entry['id'] . "'>";
                echo "<input type='submit' name='delete' value='Delete' style='background-color: red; color: white; border-radius: 10px;'>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
        
            echo "</table>";
        } else {
            echo "No entries found in the employee_schedule table for the selected employee.";
        }
        ?>

        <?php
        echo "</div>";
    } else {
        echo "No schedule information found for the selected employee.";
    }
    echo"</div>";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if "insert_schedule" key is set
        if (isset($_POST['insert_schedule'])) {
            // Insert operation for 'schedule' table
            $time = $_POST['time'];
            $subject = $_POST['subject'];
            $classroom = $_POST['classroom'];
        
            // Insert data into 'schedule' table with emp_id
            $insertScheduleSql = "INSERT INTO scheduledb.schedule (emp_id, time, subject, classroom) VALUES (?, ?, ?, ?)";
            $stmtInsertSchedule = $conn->prepare($insertScheduleSql);
            $stmtInsertSchedule->bind_param("isss", $employeeId, $time, $subject, $classroom);    

            $alertMessage = "";
            if ($stmtInsertSchedule->execute()) {
                echo "<script>alert('Inserted into schedule successfully');</script>";
                header('Location: ' . $_SERVER['PHP_SELF'] . '?employee_id=' . $employeeId);
                exit();
            } else {
                echo "<script>alert('Error inserting into schedule: " . $stmtInsertSchedule->error . "');</script>";
            }

            echo "<div class='alert-message' style='display: none;'>" . $alertMessage . "</div>";

            $stmtInsertSchedule->close();
        }
    }



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
    <!-- Add this form after the existing form for 'employee_schedule' table -->
    <div class="form-container">
    <form action='' method='post' class="add-schedule-form">
        <h2>Add Schedule Entry</h2>

        <!-- Add input fields for time, subject, and classroom with correct names -->
        <div class="form-group">
            <label for='time'>Time:</label>
            <input type='text' name='time' id='time' required style='border-radius: 10px;'>
        </div>

        <div class="form-group">
            <label for='subject'>Subject:</label>
            <input type='text' name='subject' id='subject' required style='border-radius: 10px;'>
        </div>

        <div class="form-group">
            <label for='classroom'>Classroom:</label>
            <input type='text' name='classroom' id='classroom' required style='border-radius: 10px;'>
        </div>

        <!-- Add submit button for inserting into 'schedule' table -->
        <input type='submit' name='insert_schedule' value='Add Schedule Entry' class="submit-button" style='border-radius: 10px;'>
    </form>

    <!-- Display existing entries for the specific emp_id -->
    <h2>Existing Entries</h2>
    <?php
    $sqlExistingEntries = "SELECT emp_id, time, subject, classroom FROM scheduledb.schedule WHERE emp_id = $employeeId";
    $resultExistingEntries = $conn->query($sqlExistingEntries);

    if (!$resultExistingEntries) {
        // Add this block to show the error if the query fails
        echo "Error: " . $conn->error;
    } elseif ($resultExistingEntries->num_rows > 0) {
        echo "<form action='' method='post'>";
        echo "<table border='1'>";
        echo "<tr><th>Time</th><th>Subject</th><th>Classroom</th><th>Action</th></tr>";

        while ($entry = $resultExistingEntries->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $entry['time'] . "</td>";
            echo "<td>" . $entry['subject'] . "</td>";
            echo "<td>" . $entry['classroom'] . "</td>";
            echo "<td>";
            echo "<input type='hidden' name='entry_id' value='" . $entry['emp_id'] . "'>";
            echo "<input type='submit' name='delete_schedule_entry' value='Delete' style='background-color: red; color: white; border-radius: 10px;'>";
            echo "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</form>";
    } else {
        echo "No existing entries found for the selected employee.";
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if "delete_schedule_entry" key is set
        if (isset($_POST['delete_schedule_entry'])) {
            // Delete operation
            $entryId = $_POST['entry_id'];
            $deleteSql = "DELETE FROM scheduledb.schedule WHERE id = ?";
            $stmtDelete = $conn->prepare($deleteSql);
            $stmtDelete->bind_param("i", $entryId); // Corrected parameter name

            if ($stmtDelete->execute()) {
                echo "<script>alert('Entry Deleted successfully');</script>";
            } else {
                echo "<script>alert('Error deleting entry: " . $stmtDelete->error . "');</script>";
            }

            // Close the prepared statement
            $stmtDelete->close();
        }
    }

    $conn->close();
    $connEmployee->close();
    ?>
    <br>

    <a href='admin.php' class='back-button'>
        Back
    </a>
    </body>

    </html>
