<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        body {
            font-size: 11pxpx;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            text-align: center;
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
                    $currentMonth = date("F");
                    $currentYear = date("Y");
            
                    echo "<h2 style='text-align: center;'>Attendance Records ($currentMonth, $currentYear)</h2>";
                    echo "<table border='1'>";
                    echo "<tr><th>DAY</th><th>AM TIME-IN</th><th>AM TIME-OUT</th><th>AM-STATUS</th><th>PM TIME-IN</th><th>PM TIME-OUT</th><th>PM-STATUS</th></tr>";
            
                    $firstDayOfMonth = date('N', strtotime("$currentYear-$currentMonth-01"));

                    foreach (range(1, date('t', strtotime("$currentYear-$currentMonth-01"))) as $day) {
                        $currentDayOfWeek = ($firstDayOfMonth + $day - 1) % 7; // Calculate the day of the week
                    
                        echo "<tr>";
                        echo "<td>$day</td>";
                        echo "<td>" . (isset($amTimeIn[$day]) ? $amTimeIn[$day] : '') . "</td>";
                        echo "<td>" . (isset($amTimeOut[$day]) ? $amTimeOut[$day] : '') . "</td>";
                        echo "<td>";
                    
                        // Check if it's Saturday (0) or Sunday (6)
                        if ($currentDayOfWeek == 0) {
                            echo '<b>'."Sunday".'</b>';
                        } elseif ($currentDayOfWeek == 6) {
                            echo '<b>'."Saturday".'</b>';
                        } else {
                            // Show the regular status
                            echo (isset($amStatus[$day]) ? $amStatus[$day] : '');
                        }
                    
                        echo "</td>";
                        echo "<td>" . (isset($pmTimeIn[$day]) ? $pmTimeIn[$day] : '') . "</td>";
                        echo "<td>" . (isset($pmTimeOut[$day]) ? $pmTimeOut[$day] : '') . "</td>";
                        echo "<td>";
                    
                        // Check if it's Saturday (0) or Sunday (6)
                        if ($currentDayOfWeek == 0) {
                            echo '<b>'."Sunday".'</b>';
                        } elseif ($currentDayOfWeek == 6) {
                            echo '<b>'."Saturday".'</b>';
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
                } }else {
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

                // Write HTML content to PDF
                $pdf->writeHTML($html, true, false, true, false, '');

                // Close and output PDF
                $pdf->Output($employeeName. '_Employee_Daily_Time_Record.pdf', 'D'); // D for download

                $connEmployees->close();
                $connAttendance->close();

            ?>

        </div>
    </body>
</html>
