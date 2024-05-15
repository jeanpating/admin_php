<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
<style>
        body {
            font-size: 5px;
        }
        table {
            border-collapse: collapse;
            width: 48%;
            text-align: left;
            margin-left: auto;
            margin-right: auto;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            height: 10px;
        }

        th {
            background-color: #f2f2f2;
        }
        h1, h2, h3 {
            text-align: center;
        }
        h6 {
            text-indent: 110px;
        }
        h5 {
            text-indent: 40px;
            font-size: 8px;
        }
        p {
            text-indent: 105px;
        }
    </style>
</head>

<body>

    <div class="container">

        <?php   
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
            $employeeId = isset($_POST['emp_id']) ? $_POST['emp_id'] : null;
            $employeeId = filter_var($employeeId, FILTER_VALIDATE_INT);

            if ($employeeId === false) {
                die("Invalid employee ID");
            }

            // Process the selected month and year
            $selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');
            $selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

            $firstDayOfMonth = date("Y-m-d", strtotime("$selectedYear-$selectedMonth-01"));
            $lastDayOfMonth = date("Y-m-t", strtotime("$selectedYear-$selectedMonth-01"));

            $monthRange = $firstDayOfMonth . ' ~ ' . $lastDayOfMonth;

            // Fetch employee details
            $sqlEmployee = "SELECT * FROM employees WHERE emp_id = $employeeId";
            $resultEmployee = $connEmployees->query($sqlEmployee);

            if ($resultEmployee && $resultEmployee->num_rows > 0) {
                $rowEmployee = $resultEmployee->fetch_assoc();

                // Data
                $department = "BAWA Elementary School";
               
                // Output as a table
                echo "<table>";

                // First row with two columns
                echo "<tr>";
                echo "<td>Dept.</td>";
                echo "<td>$department</td>";
                echo "<td>Name</td>"; 
                echo "<td>" . $rowEmployee['name'] . "</td>";              
                echo "</tr>";

                // Second row with two columns
                echo "<tr>";
                echo "<td>Date</td>"; 
                echo "<td>$monthRange</td>";         
                echo "<td>User ID</td>";
                echo "<td>" . $rowEmployee['emp_id'] . "</td>";
                echo "</tr>";

                echo "</table>";

                // Fetch attendance records using the name column from attendancedb
                $employeeName = $rowEmployee['name'];

                // Process the selected month and year
                $selectedMonth = isset($_POST['month']) ? $_POST['month'] : date('m');
                $selectedYear = isset($_POST['year']) ? $_POST['year'] : date('Y');

                $firstDayOfMonth = date("Y-m-d", strtotime("$selectedYear-$selectedMonth-01"));
                $lastDayOfMonth = date("Y-m-t", strtotime("$selectedYear-$selectedMonth-01"));
                
                // Fetch attendance records using the name column from attendancedb
                $sqlAttendance = "SELECT * FROM attendance WHERE name = '$employeeName' AND date BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'";
                $resultAttendance = $connAttendance->query($sqlAttendance);

                if ($resultAttendance === false) {
                    die("Error in SQL query: " . $connAttendance->error);
                }

                if ($resultAttendance && $resultAttendance->num_rows > 0) {
                    // Initialize arrays for TIME-IN and TIME-OUT
                    $amTimeIn = array_fill(1, date('t', strtotime($firstDayOfMonth)), '');
                    $pmTimeOut = array_fill(1, date('t', strtotime($firstDayOfMonth)), '');
                    $amTimeOut = array_fill(1, date('t', strtotime($firstDayOfMonth)), '');
                    $pmTimeIn = array_fill(1, date('t', strtotime($firstDayOfMonth)), '');
                    $amStatus = array_fill(1, date('t', strtotime($firstDayOfMonth)), '');
                    $pmStatus = array_fill(1, date('t', strtotime($firstDayOfMonth)), '');
            
                    // Loop through the attendance records
                    while ($rowAttendance = $resultAttendance->fetch_assoc()) {
                        // Extract day of the month from the date
                        $day = date('j', strtotime($rowAttendance['date']));
            
                        // Determine the type of record based on the clock value
                        $recordType = '';
                        switch ($rowAttendance['clock']) {
                            case 'AM-TIME-IN':
                                $recordType = 'amTimeIn';
                                $amStatus[$day] = $rowAttendance['status'];
                                break;
                            case 'AM-TIME-OUT':
                                $recordType = 'amTimeOut';
                                break;
                            case 'PM-TIME-IN':
                                $recordType = 'pmTimeIn';
                                $pmStatus[$day] = $rowAttendance['status'];
                                break;
                            case 'PM-TIME-OUT':
                                $recordType = 'pmTimeOut';
                                break;
                            default:
                                // Handle unexpected clock values if needed
                                break;
                        }
            
                        // Store details in the corresponding array
                        if (!empty($recordType)) {
                            ${$recordType}[$day] = date('H:i:s', strtotime($rowAttendance['time']));
                        }
            
                        // Calculate Under Time
                        if (!empty($amTimeIn[$day]) && !empty($amTimeOut[$day]) && !empty($pmTimeIn[$day]) && !empty($pmTimeOut[$day])) {
                            $dateTimeAMIn = new DateTime($amTimeIn[$day]);
                            $dateTimeAMOut = new DateTime($amTimeOut[$day]);
                            $dateTimePMIn = new DateTime($pmTimeIn[$day]);
                            $dateTimePMOut = new DateTime($pmTimeOut[$day]);
            
                            $intervalAM = $dateTimeAMOut->diff($dateTimeAMIn);
                            $intervalPM = $dateTimePMOut->diff($dateTimePMIn);
            
                            $status[$day] = ($rowAttendance['clock'] === 'AM-TIME-IN' || $rowAttendance['clock'] === 'AM-TIME-OUT') ? $amStatus[$day] : $pmStatus[$day];
                
                        }
                    }
            
                    // Display attendance records in a table format
                    $currentMonth = date("m", strtotime("$selectedYear-$selectedMonth-01"));
                    $currentYear = date("Y", strtotime("$selectedYear-$selectedMonth-01"));

                    $sqlAttendance = "SELECT * FROM attendance 
                    WHERE name = '$employeeName' 
                    AND MONTH(date) = $currentMonth 
                    AND YEAR(date) = $currentYear";
            
                    $resultAttendance = $connAttendance->query($sqlAttendance);
            
                    if ($resultAttendance === false) {
                        die("Error in SQL query: " . $connAttendance->error);
                    }
            
                    $totalAbsence = 0;
                    $totalLeave = 0;
                    $totalTrip = 0;
                    $totalWork = 0;
            
                    // Array to store dates already counted for work
                    $workDates = [];
            
                    if ($resultAttendance->num_rows > 0) {
                        while ($rowAttendance = $resultAttendance->fetch_assoc()) {
                            $status = $rowAttendance['status'];
                            $date = $rowAttendance['date'];
            
                    // Increment totals based on the status
                    switch ($status) {
                        case 'Absent':
                            $totalAbsence++;
                        break;
                        case 'On-Leave':
                            $totalLeave++;
                        break;
                        case 'On-Official Business':
                            $totalTrip++;
                        break;
                        case 'On-Time':
                        case 'Late':
                        case 'Early':
                        case 'Asynchronous':
                    
                    if (!in_array($date, $workDates)) {
                        $totalWork++;
                        $workDates[] = $date;
                    }
                        break;
                        default:
                        // Ignore other statuses
                            break;
                            }
                        }
                    }

                // Output the totals
                echo "<table border='1'>";
                echo "<tr><th>Absence (Day)</th><th>Leave (Day)</th><th>Trip (Day)</th><th>Work (Day)</th>
                <th>Overtime Normal</th><th>Overtime Special</th><th>Late (Times)</th><th>Late (Minute)</th>
                <th>Early (Times)</th><th>Early (Minute)</th></tr>";
                echo "<tr><td>$totalAbsence</td><td>$totalLeave</td><td>$totalTrip</td><td>$totalWork</td>
                <td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td></tr>";
                echo "</table>";

                    $holidays = [
                        '01-01' => 'New Year\'s Day',
                        '02-09' => 'Lunar New Year Holiday',
                        '02-10' => 'Lunar New Year\'s Day',
                        '02-25' => 'People Power Anniversary',
                        '03-11' => 'Ramadans Start',
                        '03-28' => 'Maundy Thursday',
                        '03-29' => 'Good Friday',
                        '03-30' => 'Black Saturday',
                        '03-31' => 'Easter Sunday',
                        '04-09' => 'The Day of Valor',
                        '05-01' => 'Labour Day',
                        '06-12' => 'Independence Day',
                        '08-21' => 'Ninoy Aquino Day',
                        '08-26' => 'National Heroes Day',
                        '11-01' => 'All Saint\'s Day',
                        '11-02' => 'All Souls Day',
                        '11-30' => 'Bonifacio Day',
                        '12-08' => 'Feast of the Immaculate Conception',
                        '12-24' => 'Christmas Eve',
                        '12-25' => 'Christmas Day',
                        '12-30' => 'Rizal Day',
                        '12-31' => 'New Year\'s Eve'
                    ];
            
                    echo "<h6>Time Card</h6>";
                    echo "<table border='1'>";
                    echo "<tr>
                    <th></th>
                    <th>Before Noon</th>
                    <th></th>
                    <th>After Noon</th>
                    <th></th>
                    <th>Overtime</th>
                    <th></th>
                    </tr>";
                    echo "<tr><th>Date / Weekday</th><th>In</th><th>Out</th><th>In</th><th>Out</th><th>In</th><th>Out</th></tr>";

            
                // Define the first day of the current month
                $firstDayOfMonth = date('N', strtotime("$currentYear-$currentMonth-01"));

                // Mapping of three-letter day names to two-letter versions
                $twoLetterDays = [
                    'Mon' => 'Mo',
                    'Tue' => 'Tu',
                    'Wed' => 'We',
                    'Thu' => 'Th',
                    'Fri' => 'Fr',
                    'Sat' => 'Sa',
                    'Sun' => 'Su'
                ];

                // Loop through each day of the current month
                foreach (range(1, date('t', strtotime("$currentYear-$currentMonth-01"))) as $day) {
                    $formattedDate = sprintf("%02d-%02d", $currentMonth, $day); // 'MM-DD'
                    $weekdayName = date('D', strtotime("$currentYear-$currentMonth-$day")); // "Mon", "Tue", etc.
                    $twoLetterDay = $twoLetterDays[$weekdayName]; // Get the two-letter abbreviation
                    $displayDay = "$day $twoLetterDay";

                    // Check if it's a holiday
                    $isHoliday = isset($holidays[$formattedDate]);
                    $holidayName = $isHoliday ? $holidays[$formattedDate] : ''; // Get the holiday name

                    // Check for specific statuses
                    $specialStatus = ''; // Default empty status

                    if (isset($amStatus[$day])) {
                        $status = $amStatus[$day];
                    } elseif (isset($pmStatus[$day])) {
                        $status = $pmStatus[$day];
                    }

                    if (isset($status)) {
                        switch ($status) {
                            case 'On-Leave':
                            case 'Absent':
                            case 'On-Official Business':
                            case 'Asynchronous':
                                if ($status === 'On-Official Business') {
                                    $specialStatus = 'OOB'; // Abbreviation
                                } elseif ($status === 'Asynchronous') {
                                    $specialStatus = 'Async'; // Abbreviation
                                } else {
                                    $specialStatus = $status;
                                }
                                break;
                            default:
                                // Ignore other statuses
                                break;
                        }
                    }

                    // Output the table row
                    echo "<tr>";
                    echo "<td>$displayDay"; 

                    // Display holiday or special status
                    if ($isHoliday) {
                        echo " - <b>$holidayName</b>";
                    }
                    if ($specialStatus !== '') {
                        echo " - <b>$specialStatus</b>"; // Display special status
                    }

                    echo "</td>";

                    // Display AM Time-in and AM Time-out
                    echo "<td>" . (($amTimeIn[$day] != '00:00:00') ? $amTimeIn[$day] : '') . "</td>";
                    echo "<td>" . (isset($amTimeOut[$day]) ? $amTimeOut[$day] : '') . "</td>";

                    // Display PM Time-in and PM Time-out
                    echo "<td>" . (isset($pmTimeIn[$day]) ? $pmTimeIn[$day] : '') . "</td>";
                    echo "<td>" . (isset($pmTimeOut[$day]) ? $pmTimeOut[$day] : '') . "</td>";
                    
                    // Display Overtime TDs
                    echo "<td></td>";
                    echo "<td></td>";

                    echo "</tr>";
                }

                // End the table
                echo "</table>";

                } else {
                    echo "<p>No attendance records found for the employee in the specified date range.</p>";
                }?>
                <br><br>
                <?php
                echo"<h5>" . "__________" . "<u><b>" . $employeeName . "</u></b>" . "__________" ."</h5>";
                echo"<p>Employee Name</p>";
                
                echo"<h5>" . "__________" . "<u><b>" . "Janeia Fran G. Fider" . "</b></u>" . "__________" ."</h5>";
                echo"<p>In Charge</p>"; 
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
            $html = ob_get_clean(); 

            // Write HTML content to PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Close and output PDF
            $pdf->Output($employeeName. '_DTR_'.$selectedMonth.'.pdf', 'D'); // D for download

            $connEmployees->close();
            $connAttendance->close();
        ?>
    </div>
</body>
</html>
