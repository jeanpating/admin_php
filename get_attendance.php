<?php
// DB
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendancedb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Variables to store HTML content
$tableHTML = '';

// Handle form submission for View Attendance
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitAttendance"])) {
    // Get the selected date from the form
    $selectedDate = $_POST["selected_date"];

    // Query to get attendance data for the selected date
    $sql = "SELECT * FROM attendance WHERE date = '$selectedDate'";
    $result = $conn->query($sql);

    if ($result === false) {
        echo 'Error executing the query: ' . $conn->error;
    } else {
        if ($result->num_rows > 0) {
            // Display the data from the 'attendance' table with dynamic background colors and text color
            $tableHTML .= '<div style="overflow-x:auto;">';
            $tableHTML .= '<table class="modern-table">';
            $tableHTML .= '<thead>';
            $tableHTML .= '<tr><th>NAME</th><th>TIME</th><th>STATUS</th><th>CLOCK TYPE</th></tr>';
            $tableHTML .= '</thead>';
            $tableHTML .= '<tbody>';

            while ($row = $result->fetch_assoc()) {
                $backgroundColor = ($row['status'] == 'Early') ? '#1fab36' : (($row['status'] == 'Late') ? '#d9a71e' : '');

                $tableHTML .= '<tr>';
                $tableHTML .= '<td>' . $row['name'] . '</td>';
                $tableHTML .= '<td>' . $row['time'] . '</td>';
                $tableHTML .= '<td style="background-color: ' . $backgroundColor . '; color: white;">' . $row['status'] . '</td>';
                $tableHTML .= '<td>' . $row['clock'] . '</td>';
                $tableHTML .= '</tr>';
            }

            $tableHTML .= '</tbody>';
            $tableHTML .= '</table>';
            $tableHTML .= '</div>';
        } else {
            echo 'No data found in the attendance table for the selected date.';
        }
    }
    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        form {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input[type="date"],
        select,
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #ffffff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            font-size: 16px;
        }

        .modern-table th, .modern-table td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 12px;
        }

        .modern-table th {
            background-color: #f2f2f2;
        }

        .modern-table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .modern-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        a {
            text-decoration: none;
        }
        .back-button {
            background: #5B1515;
            width: 100%;
            padding: 6px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            color: white;
        }
        .back-button:hover {
            background: #992828;
        }
    </style>
</head>
<body>
    <!-- Form for selecting a date -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Select Date: <input type="date" name="selected_date">
        <input type="submit" value="View Attendance" name="submitAttendance">
        <a href="javascript:history.go(-1)" class="back-button">Go back</a>
    </form>

    <!-- Display the table HTML content -->
    <?php echo $tableHTML; ?>

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

</body>
</html>
