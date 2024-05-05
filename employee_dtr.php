<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        body {
            font-size: 10.5px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            text-align: left;
            margin-left: auto;
            margin-right: auto;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }
        h1, h2, h3 {
            text-align: center;
        }
        h4 {
            text-align: right;
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
                echo "<p>Name: <b>" . $rowEmployee['name'] . "</b></p>";
                echo "<p>Employee ID: <b>" . $rowEmployee['emp_id'] ."</b></p>";
                echo "<p>Department: <b>" . $rowEmployee['department'] . "</b></p>";

                $scheduleSql = "SELECT am_time_in, am_time_out, pm_time_in, pm_time_out FROM scheduledb.employee_schedule WHERE emp_id = $employeeId";
                $scheduleResult = $connEmployees->query($scheduleSql);

                if ($scheduleResult && $scheduleResult->num_rows > 0) {
                    // Display the schedule in a table
                    echo "<h3>Employee's Schedule</h3>";
                    echo "<table border='1'>"; // Added the missing '>' here
                    echo "<tr><th>AM Time In</th><th>AM Time Out</th><th>PM Time In</th><th>PM Time Out</th></tr>";
                
                    while ($scheduleRow = $scheduleResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$scheduleRow['am_time_in']}</td>";
                        echo "<td>{$scheduleRow['am_time_out']}</td>";
                        echo "<td>{$scheduleRow['pm_time_in']}</td>";
                        echo "<td>{$scheduleRow['pm_time_out']}</td>";
                        echo "</tr>";
                    }
                
                    echo "</table>";
                } else {
                    echo "<p>No schedule found for the employee.</p>";
                }
                

                // Fetch attendance records using the name column from attendancedb
                $employeeName = $rowEmployee['name'];
                $currentDate = date('Y-m-d');
                $firstDayOfMonth = date('Y-m-01', strtotime($currentDate));
                $lastDayOfMonth = date('Y-m-t', strtotime($currentDate));
                
                // Fetch attendance records using the name column from attendancedb
                $sqlAttendance = "SELECT * FROM attendance WHERE name = '$employeeName' AND date BETWEEN '$firstDayOfMonth' AND '$lastDayOfMonth'";
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
                    $amStatus = array_fill(1, date('t', strtotime($currentDate)), '');
                    $pmStatus = array_fill(1, date('t', strtotime($currentDate)), '');
            
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
                    $currentMonth = date('m');  // Current month as a number
                    $currentYear = date('Y');  // Current year

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
            
                    echo "<h2 style='text-align: center;'>Attendance Records ($currentMonth, $currentYear)</h2>";
                    echo "<table border='1'>";
                    echo "<tr><th>DAY</th><th>AM TIME-IN</th><th>AM TIME-OUT</th><th>AM-STATUS</th><th>PM TIME-IN</th><th>PM TIME-OUT</th><th>PM-STATUS</th></tr>";
            
                    $firstDayOfMonth = date('N', strtotime("$currentYear-$currentMonth-01"));

                    $firstDayOfMonth = date('N', strtotime("$currentYear-$currentMonth-01"));

                    // Loop through each day of the month
                    foreach (range(1, date('t', strtotime("$currentYear-$currentMonth-01"))) as $day) {
                        $formattedDate = sprintf("%02d-%02d", $currentMonth, $day); // 'MM-DD'
                        $weekdayName = date('D', strtotime("$currentYear-$currentMonth-$day")); // "Mon", "Tue", etc.
                        $displayDay = "$day $weekdayName"; 
                        
                        // Check if it's a holiday
                        $isHoliday = isset($holidays[$formattedDate]);
                        $holidayName = $isHoliday ? $holidays[$formattedDate] : ''; // Get the holiday name
        
                        // Output the table row
                        echo "<tr>";
                        echo "<td>$displayDay"; 
        
                        // Display holiday name if it's a holiday
                        if ($isHoliday) {
                            echo " - <b>$holidayName</b>";
                        }
        
                        echo "</td>";
        
                        // AM Time-in
                        echo "<td>" . (($amTimeIn[$day] != '00:00:00') ? $amTimeIn[$day] : '') . "</td>";
                        // AM Time-out
                        echo "<td>" . (isset($amTimeOut[$day]) ? $amTimeOut[$day] : '') . "</td>";
        
                        // AM-STATUS
                        echo "<td>";
                        if ($isHoliday) {
                            echo isset($amStatus[$day]) ? $amStatus[$day] : '';
                        } 
                        // Changed On-Official Business into OOB because its too long
                        if (isset($amStatus[$day]) && $amStatus[$day] === 'On-Official Business') {
                            echo 'OOB';
                        } else {
                            echo (isset($amStatus[$day]) ? $amStatus[$day] : '');
                        }
                        echo "</td>";
        
                        // PM Time-in
                        echo "<td>" . (isset($pmTimeIn[$day]) ? $pmTimeIn[$day] : '') . "</td>";
                        // PM Time-out
                        echo "<td>" . (isset($pmTimeOut[$day]) ? $pmTimeOut[$day] : '') . "</td>";
        
                        // PM-STATUS
                        echo "<td>";
                        if ($isHoliday) {
                            echo isset($pmStatus[$day]) ? $pmStatus[$day] : '';
                        
                        } else {
                            // Show the regular status
                            echo (isset($pmStatus[$day]) ? $pmStatus[$day] : '');
                        }
                        echo "</td>";
        
                        echo "</tr>";
                    }
                    echo "</table>";
        
                } else {
                    echo "<p>No attendance records found for the employee in the specified date range.</p>";
                }?>
                <br><br>
                <?php
                echo"<h4>__________________</h4>";
                echo"<h4>Employee Signature</h4>"; 
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
                $pdf->Output($employeeName. '_DTR_'.$currentMonth.'.pdf', 'D'); // D for download

                $connEmployees->close();
                $connAttendance->close();

            ?>
        </div>
    </body>
</html>
