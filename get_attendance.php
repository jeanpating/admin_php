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
    $sql = "SELECT name, time, status FROM attendance WHERE date = '$selectedDate' AND (clock ='AM-TIME-IN' OR clock ='PM-TIME-IN')";

    $result = $conn->query($sql);

    if ($result === false) {
        echo 'Error executing the query: ' . $conn->error;
    } else {
        if ($result->num_rows > 0) {
            // Display the data from the 'attendance' table with dynamic background colors and text color
            $tableHTML .= '<div style="overflow-x:auto;">';
            $tableHTML .= '<table class="modern-table">';
            $tableHTML .= '<thead>';
            $tableHTML .= '<tr><th>NAME</th><th>TIME</th><th>STATUS</th></tr>';
            $tableHTML .= '</thead>';
            $tableHTML .= '<tbody>';

            while ($row = $result->fetch_assoc()) {
                $backgroundColor = '';

                if ($row['status'] == 'Early') {
                    $backgroundColor = '#1fab36'; // Green color for Early
                } elseif ($row['status'] == 'Late') {
                    $backgroundColor = '#d9a71e'; // Orange color for Late
                } elseif ($row['status'] == 'Absent') {
                    $backgroundColor = 'red'; // Red color for Absent
                } elseif ($row['status'] == 'On-Official Business') {
                    $backgroundColor = '#7FC7D9'; // Light blue color for On-Official Business
                } elseif ($row['status'] == 'On-Leave') {
                    $backgroundColor = '#EEC759'; // Light yellow color for On-Leave
                }


                $tableHTML .= '<tr>';
                $tableHTML .= '<td>' . $row['name'] . '</td>';
                $tableHTML .= '<td>' . $row['time'] . '</td>';
                $tableHTML .= '<td style="background-color: ' . $backgroundColor . '; color: white;">' . $row['status'] . '</td>';
                $tableHTML .= '</tr>';
            }

            $tableHTML .= '</tbody>';
            $tableHTML .= '</table>';
            $tableHTML .= '</div>';
        } 
    }
    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="styles/get_attendance.css">
</head>
<body>
    <!-- Modal for no data found -->
    <div class="overlay" id="overlay"></div>
    <div class="modal" id="noDataModal">
        <p>No data found in the attendance table for the selected date.</p>
        <button onclick="closeModal()" 
        style="    
        background-color: #4caf50;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;"
        >OK</button>
    </div>
    <!-- Form for selecting a date -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Select Date: <input type="date" name="selected_date">
        <input type="submit" value="View Attendance" name="submitAttendance">
        <a href="javascript:history.go(-1)" class="back-button">Go back</a>
    </form>

    <!-- Display the table HTML content -->
    <hr>
    <?php 
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitAttendance"])) {
        // Check if the form is submitted and 'submitAttendance' is set
        $selectedDate = isset($_POST["selected_date"]) ? $_POST["selected_date"] : null;
    
        if ($selectedDate !== null) {
            // Display the selected date in a paragraph
            echo "<p style='margin-left: 40px;'>Selected Date: " . '<b>' .htmlspecialchars($selectedDate) . '</b>' . "</p>";
        } else {
            echo "<p>No date selected.</p>";
        }
    }
    echo $tableHTML; 
    ?>

    <script>
        // JavaScript function to show the modal
        function showModal() {
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('noDataModal').style.display = 'block';
        }

        // JavaScript function to close the modal
        function closeModal() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('noDataModal').style.display = 'none';
        }

        <?php
        // This part should now be at the end of the body, ensuring it runs after the functions are defined
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitAttendance"]) && $result->num_rows === 0) {
            echo 'showModal();';
        }
        ?>
    </script>
</body>
</html>
