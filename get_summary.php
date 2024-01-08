<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Attendance Summary</title>
        <link rel="stylesheet" type="text/css" href="styles/get_summary.css">
    </head>
    <body>
        <!-- Form for selecting a date / Download Monthly Summary -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            Select Month and Year:
            <select name="selected_month">
                <?php
                $months = array("01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December");

                foreach ($months as $monthNum => $monthName) {
                    echo "<option value=\"$monthNum\">$monthName</option>";
                }
                ?>
            </select>

            <select name="selected_year">
                <?php
                $currentYear = date("Y");
                $startYear = $currentYear - 5;

                for ($year = $startYear; $year <= $currentYear; $year++) {
                    echo "<option value=\"$year\">$year</option>";
                }
                ?>
            </select>

            <input type="submit" value="View Monthly Summary" name="submitMonthlySummary">
        </form>

        <?php
        // Handle form submission for View Monthly Summary
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitMonthlySummary"])) {
            // Get the selected month and year from the form
            $selectedMonth = $_POST["selected_month"];
            $selectedYear = $_POST["selected_year"];

            // Redirect to monthly_summary.php with selected parameters
            header("Location: monthly_summary.php?month=$selectedMonth&year=$selectedYear");
            exit();
        }
        ?>

        <?php
        // Database connection parameters
        $servername = "localhost";
        $username = "root";
        $password = "";

        // Create connection for attendancedb
        $connAttendance = new mysqli($servername, $username, $password, "attendancedb");

        // Check connection
        if ($connAttendance->connect_error) {
            die("Connection failed: " . $connAttendance->connect_error);
        }

        // SQL query to get summary data grouped by name and status
        $sqlSummary = "SELECT name,
            SUM(CASE WHEN status = 'On-Time' THEN 1 ELSE 0 END) AS OnTime,
            SUM(CASE WHEN status = 'Early' THEN 1 ELSE 0 END) AS Early,
            SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) AS Late,
            SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS Absent,
            SUM(CASE WHEN status = 'On-Official Business' THEN 1 ELSE 0 END) AS OnOfficialBusiness,
            SUM(CASE WHEN status = 'On-Leave' THEN 1 ELSE 0 END) AS OnLeave
        FROM attendance 
        WHERE clock ='AM-TIME-IN'
        OR clock ='PM-TIME-IN'
        GROUP BY name";

        $resultSummary = $connAttendance->query($sqlSummary);

        // Check if there are results
        if ($resultSummary->num_rows > 0) {
            // Create an array to store summary data
            $summaryData = array();

            while ($rowSummary = $resultSummary->fetch_assoc()) {
                // Store data in the array
                $name = $rowSummary['name'];
                $onTime = $rowSummary['OnTime'];
                $early = $rowSummary['Early'];
                $late = $rowSummary['Late'];
                $absent = $rowSummary['Absent'];
                $onOfficialBusiness = $rowSummary['OnOfficialBusiness'];
                $onLeave = $rowSummary['OnLeave'];

                // Determine Perfect Attendance based on absences
                $perfectAttendance = ($absent == 0) ? 'YES' : 'NO';

                $summaryData[] = array(
                    'name' => $name,
                    'OnTime' => $onTime,
                    'Early' => $early,
                    'Late' => $late,
                    'Absent' => $absent,
                    'OnOfficialBusiness' => $onOfficialBusiness,
                    'OnLeave' => $onLeave,
                    'PerfectAttendance' => $perfectAttendance
                );
            }

            // Close the connection for attendancedb
            $connAttendance->close();
        } else {
            // No results found
            echo json_encode(array('error' => 'No data available.'));
            // Close the connection for attendancedb
            $connAttendance->close();
            exit(); // exit the script if there is no data
        }
        ?>
        <hr style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); border-color: #F1E4C3;">
        <div class='cheader'>
            <h2>Attendance Summary</h2>
            <h3>BAWA Elementary School - <?php echo date("Y F"); ?></h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>On-Time</th>
                    <th>Early</th>
                    <th>Late</th>
                    <th>Absent</th>
                    <th>On-Official Business</th>
                    <th>On-Leave</th>
                    <th>Perfect Attendance</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($summaryData as $row) {
                    echo "<tr>";
                    echo "<td>" . $row['name'] . "</td>";
                    echo "<td>" . $row['OnTime'] . "</td>";
                    echo "<td>" . $row['Early'] . "</td>";
                    echo "<td>" . $row['Late'] . "</td>";
                    echo "<td>" . $row['Absent'] . "</td>";
                    echo "<td>" . $row['OnOfficialBusiness'] . "</td>";
                    echo "<td>" . $row['OnLeave'] . "</td>";
                    echo "<td>" . $row['PerfectAttendance'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

    </body>
</html>

