<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbnameScheduled = "attendancedb";

// Create connection to scheduledb
$connScheduled = new mysqli($servername, $username, $password, $dbnameScheduled);

// Check connection
if ($connScheduled->connect_error) {
    die("Connection failed: " . $connScheduled->connect_error);
}

// Modify the table to add the emp_name column
$alterTableSql = "ALTER TABLE attendancedb.attendance
                  ADD COLUMN schedule VARCHAR(255) AFTER name";

if ($connScheduled->query($alterTableSql) === TRUE) {
    echo "Table altered successfully to add schedule column";
} else {
    echo "Error altering table: " . $connScheduled->error;
}

$connScheduled->close();
?>
