<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbnameScheduled = "scheduledb";

// Create connection to scheduledb
$connScheduled = new mysqli($servername, $username, $password, $dbnameScheduled);

// Check connection
if ($connScheduled->connect_error) {
    die("Connection failed: " . $connScheduled->connect_error);
}

// Modify the table to add the emp_name column
$alterTableSql = "ALTER TABLE scheduledb.employee_schedule
                  ADD COLUMN name VARCHAR(255) AFTER emp_id";

if ($connScheduled->query($alterTableSql) === TRUE) {
    echo "Table altered successfully to add emp_name column";
} else {
    echo "Error altering table: " . $connScheduled->error;
}

$connScheduled->close();
?>
