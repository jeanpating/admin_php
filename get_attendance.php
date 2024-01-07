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
                $tableHTML .= '<td>' . $row['clock'] . '</td>';
                $tableHTML .= '</tr>';
            }

            $tableHTML .= '</tbody>';
            $tableHTML .= '</table>';
            $tableHTML .= '</div>';
        } else {
            echo '<script>showModal();</script>';
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
            margin: 20px;
            text-align: left;
        }

        label {
            margin-right: 10px;
        }

        select, input {
            margin-right: 10px;
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
            background-color: #739072;
            color: #ffffff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #86A789;
        }


        .modern-table {
            margin-left: auto;
            margin-right: auto;
            width: 70%;
            font-size: 20px;
            border-collapse: collapse;
            
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            transition: transform 0.3s ease-in-out;
        }
        .modern-table th {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .modern-table td {
            padding: 10px;
            background-color: #F1E4C3;
            color: #191919;
        }
        .modern-table:hover {
            background-color: #f5f5f5;
            transform: scale(1.05);
        }




        /* .modern-table {
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
        } */

        a {
            text-decoration: none;
        }
        .back-button {
            background: #CD8D7A ;
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
            background: #DBCC95;
        }
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
    <!-- Modal for no data found -->
    <div class="overlay" id="overlay"></div>
    <div class="modal" id="noDataModal">
        <p>No data found in the attendance table for the selected date.</p>
        <button onclick="closeModal()">OK</button>
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
    </script>

</body>
</html>
