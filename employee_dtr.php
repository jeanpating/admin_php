<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Include your styles here -->
    <style>
        /* Add your styles here */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
    <!-- Include your styles here -->
</head>

<body>

<div class="container">

<?php
// Include the TCPDF library
require_once('tcpdf/tcpdf.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbnameEmployees = "employeesdb";
$dbnameAttendance = "attendancedb";

// Create connection to employeesdb
$connEmployees = new mysqli($servername, $username, $password, $dbnameEmployees);

// Create connection to attendancedb
$connAttendance = new mysqli($servername, $username, $password, $dbnameAttendance);

// Check connections
if ($connEmployees->connect_error || $connAttendance->connect_error) {
    die("Connection failed: " . $connEmployees->connect_error . " " . $connAttendance->connect_error);
}

// Fetch employee details
$employeeId = isset($_GET['emp_id']) ? $_GET['emp_id'] : null;
$employeeId = filter_var($employeeId, FILTER_VALIDATE_INT);

if ($employeeId === false) {
    die("Invalid employee ID");
}

// Fetch employee details
$sqlEmployee = "SELECT * FROM employees WHERE emp_id = $employeeId";
$resultEmployee = $connEmployees->query($sqlEmployee);

if ($resultEmployee && $resultEmployee->num_rows > 0) {
    $rowEmployee = $resultEmployee->fetch_assoc();

    echo "<h1>Daily Time Record</h1>";
    echo "<p>Employee ID: " . $rowEmployee['emp_id'] . "</p>";
    echo "<p>Name: " . $rowEmployee['name'] . "</p>";
    echo "<p>Department: " . $rowEmployee['department'] . "</p>";

    // Fetch attendance records using the name column from attendancedb
    $employeeName = $rowEmployee['name'];
    $currentDate = date('Y-m-d');
    $firstDayOfMonth = date('Y-m-01', strtotime($currentDate));
    $lastDayOfMonth = date('Y-m-t', strtotime($currentDate));
    
    $sqlAttendance = "SELECT * FROM attendance WHERE name = '$employeeName' AND date BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'";
    // echo "SQL Query: $sqlAttendance<br>";

    $resultAttendance = $connAttendance->query($sqlAttendance);
    if ($resultAttendance === false) {
        die("Error in SQL query: " . $connAttendance->error);
    }

    if ($resultAttendance && $resultAttendance->num_rows > 0) {
        // Initialize arrays for TIME-IN and TIME-OUT
        $amTimeIn = array_fill(1, date('t', strtotime($currentDate)), '');
        $pmTimeOut = array_fill(1, date('t', strtotime($currentDate)), '');
        $amTimeOut = array_fill(1, date('t', strtotime($currentDate)), '');
        $pmTimeIn = array_fill(1, date('t', strtotime($currentDate)), '');
        $underTimeHours = array_fill(1, date('t', strtotime($currentDate)), 0);
        $underTimeMinutes = array_fill(1, date('t', strtotime($currentDate)), 0);

        // Loop through the attendance records
        while ($rowAttendance = $resultAttendance->fetch_assoc()) {
            // Extract day of the month from the date
            $day = date('j', strtotime($rowAttendance['date']));
        
            // Determine the type of record
            $recordType = '';
            if (strpos($rowAttendance['status'], 'AM Time-in') !== false || strpos($rowAttendance['status'], 'Late') !== false) {
                $recordType = 'amTimeIn';
            } elseif (strpos($rowAttendance['status'], 'PM Time-out') !== false || strpos($rowAttendance['status'], 'Late') !== false) {
                $recordType = 'pmTimeOut';
            } elseif (strpos($rowAttendance['status'], 'AM Time-out') !== false) {
                $recordType = 'amTimeOut';
            } elseif (strpos($rowAttendance['status'], 'PM Time-in') !== false) {
                $recordType = 'pmTimeIn';
            } else {
                // Default case, in case the status doesn't match expected values
                echo "Unexpected status: {$rowAttendance['status']}<br>";
                print_r($rowAttendance);  // Output the entire record for further inspection
                echo "<br>";
            }
        
            // Store details in the corresponding array
            if (!empty($recordType)) {
                ${$recordType}[$day] = date('H:i:s', strtotime($rowAttendance['time']));
                // echo "Day: $day, Record Type: $recordType, Time: " . date('H:i:s', strtotime($rowAttendance['time'])) . "<br>";
                // echo "Status: {$rowAttendance['status']}<br>";
            }

            // Display information about each iteration
            // echo "Day: $day, Record Type: $recordType, Time: " . date('H:i:s', strtotime($rowAttendance['time'])) . "<br>";
            
            // Calculate Under Time
            if (!empty($amTimeIn[$day]) && !empty($amTimeOut[$day]) && !empty($pmTimeIn[$day]) && !empty($pmTimeOut[$day])) {
                $dateTimeAMIn = new DateTime($amTimeIn[$day]);
                $dateTimeAMOut = new DateTime($amTimeOut[$day]);
                $dateTimePMIn = new DateTime($pmTimeIn[$day]);
                $dateTimePMOut = new DateTime($pmTimeOut[$day]);

                $intervalAM = $dateTimeAMOut->diff($dateTimeAMIn);
                $intervalPM = $dateTimePMOut->diff($dateTimePMIn);

                // Calculate total hours and minutes for underTime
                $underTimeHours[$day] = $intervalAM->h + $intervalPM->h;
                $underTimeMinutes[$day] = $intervalAM->i + $intervalPM->i;

                // Adjust hours if minutes exceed 60
                if ($underTimeMinutes[$day] >= 60) {
                    $underTimeHours[$day] += floor($underTimeMinutes[$day] / 60);
                    $underTimeMinutes[$day] %= 60;
                }
            }
        }

        // echo "<pre>";
        // print_r($amTimeIn);
        // print_r($pmTimeOut);
        // print_r($amTimeOut);
        // print_r($pmTimeIn);
        // print_r($underTimeHours);
        // print_r($underTimeMinutes);
        // echo "</pre>";

        // Display attendance records in a table format
        echo "<h2>Attendance Records</h2>";
        echo "<table border='1'>";
        echo "<tr><th>DAY</th><th>AM TIME-IN</th><th>AM TIME-OUT</th><th>PM TIME-IN</th><th>PM TIME-OUT</th><th>UNDER TIME (HOURS)</th><th>UNDER TIME (MINUTES)</th></tr>";

        foreach (range(1, date('t', strtotime($currentDate))) as $day) {
            echo "<tr>";
            echo "<td>$day</td>";
            echo "<td>{$amTimeIn[$day]}</td>";
            echo "<td>{$amTimeOut[$day]}</td>";
            echo "<td>{$pmTimeIn[$day]}</td>";
            echo "<td>{$pmTimeOut[$day]}</td>";
            echo "<td>{$underTimeHours[$day]}</td>";
            echo "<td>{$underTimeMinutes[$day]}</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No attendance records found for the employee in the specified date range.</p>";
    }
} else {
    echo "<p>No employee details found.</p>";
}

// Create a PDF instance
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Employee Daily Time Record');
$pdf->SetSubject('Employee Daily Time Record PDF');
$pdf->SetKeywords('TCPDF, PDF, employee, time record');

// Add a page
$pdf->AddPage();

// Output HTML content to PDF
$html = ob_get_clean(); // Get the HTML content from the output buffer
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF
$pdf->Output('Employee_Daily_Time_Record.pdf', 'D'); // D for download

$connEmployees->close();
$connAttendance->close();

?>
<!-- Add your HTML styling and buttons here -->

</form>
</div>
</body>
</html>
