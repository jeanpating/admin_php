<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbnameScheduled = "employeesdb";

// Create connection to scheduledb
$connScheduled = new mysqli($servername, $username, $password, $dbnameScheduled);

// Check connection
if ($connScheduled->connect_error) {
    die("Connection failed: " . $connScheduled->connect_error);
}

// Modify the table to add the emp_name column
$alterTableSql = "ALTER TABLE employeesdb.employees
                  ADD COLUMN position VARCHAR(255) AFTER department";

if ($connScheduled->query($alterTableSql) === TRUE) {
    echo "Table altered successfully to add position column";
} else {
    echo "Error altering table: " . $connScheduled->error;
}

$connScheduled->close();
?>
