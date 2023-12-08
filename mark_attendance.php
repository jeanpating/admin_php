<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$attendanceDatabase = "attendancedb";

// Create connection for attendance
$connAttendance = new mysqli($servername, $username, $password, $attendanceDatabase);

// Check connection for attendance
if ($connAttendance->connect_error) {
    die("Connection to attendance database failed: " . $connAttendance->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employeeName = $_POST["employeeName"];
    $status = $_POST["status"];

    // Get current date and time
    $current_date = date("d_m_Y");
    $current_time = date("H:i:s");
    $table_name = "attendance_table_" . $current_date;

    // Log table name for debugging
    error_log("Table Name: " . $table_name);

    // Create table if not exists
    $create_table_query = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255),
        time VARCHAR(255),
        status VARCHAR(255)
    )";
    $result = $connAttendance->query($create_table_query);

    // Log query result for debugging
    error_log("Create Table Query Result: " . var_export($result, true));

    // Insert data into the attendance table
    $insert_query = "INSERT INTO $table_name (name, time, status) VALUES (?, ?, ?)";
    $stmt = $connAttendance->prepare($insert_query);

    if ($stmt) {
        $stmt->bind_param("sss", $employeeName, $current_time, $status);
        $stmt->execute();
        $stmt->close();
        echo "Attendance marked successfully!";
    } else {
        echo "Error: " . $connAttendance->error;
    }
}

$connAttendance->close();
?>
