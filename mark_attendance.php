<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $attendanceDatabase = "attendancedb";

    $connAttendance = new mysqli($servername, $username, $password, $attendanceDatabase);

    if ($connAttendance->connect_error) {
        die("Connection to attendance database failed: " . $connAttendance->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $status = $_POST["status"];
        $employeeName = $_POST["employeeName"];

        $current_date = date("d_m_Y");
        $table_name = "attendance_table_" . $current_date;

        $insert_query = "INSERT INTO $table_name (name, time, status) VALUES ('$employeeName', NOW(), '$status')";
        $result = $connAttendance->query($insert_query);

        if ($result) {
            echo "Attendance marked successfully";
        } else {
            echo "Error marking attendance";
        }
    }

    $connAttendance->close();
?>
