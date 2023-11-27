<?php
require_once('tcpdf/tcpdf.php');

error_log('Monthly Summary PDF generation started.');

// Replace with your actual database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendancedb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current month and year
$currentDate = date("m_Y"); // Format the date as "11_2023"


// Extract month and year from the current date
$currentMonth = date("F");  // Full month name, e.g., November
$currentYear = date("Y");

// Query to get the list of attendance tables
$sql = "SHOW TABLES LIKE 'attendance_table_%_$currentDate'";
$result = $conn->query($sql);

if ($result === false) {
    echo 'Error executing the query: ' . $conn->error;
} else {
    $summary = array();

    // Loop through each attendance table
    while ($row = $result->fetch_row()) {
        $tableName = $row[0];

        // Query to get the monthly summary for each table
        $sql = "SELECT name, status, COUNT(*) as total FROM $tableName GROUP BY name, status";
        $tableResult = $conn->query($sql);

        if ($tableResult === false) {
            echo 'Error executing the query: ' . $conn->error;
        } else {
            // Calculate the totals for each employee and status
            while ($tableRow = $tableResult->fetch_assoc()) {
                $name = $tableRow['name'];
                $status = strtolower($tableRow['status']);
                if (!isset($summary[$name])) {
                    $summary[$name] = array(
                        'on_time' => 0,
                        'early' => 0,
                        'late' => 0,
                        'absent' => 0,
                        'on_official_business' => 0,
                        'perfect_attendance' => 'NO'
                    );
                }
                $summary[$name][$status] += $tableRow['total'];

                // Update perfect attendance status
                if ($status === 'absent') {
                    $summary[$name]['perfect_attendance'] = 'NO';
                }
            }
        }
    }

    // Generate PDF
    $pdf = new TCPDF();
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Add content to the PDF
    // Add content to the PDF
    $pdf->SetFont('helvetica', 'B', 16); // Set font to bold and increase font size
    $pdf->Cell(0, 10, 'BAWA ELEMENTARY SCHOOL', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 8); // Reset font to regular
    $pdf->Cell(0, 10, "Monthly Summary - $currentMonth $currentYear", 0, 1, 'C');

    // Create a table to display the summary
    $pdf->SetFillColor(200, 220, 255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.3);

    $header = array('Name', 'On-Time', 'Early', 'Late', 'Absent', 'On-Official Business', 'Perfect Attendance');
    $w = array(40, 20, 20, 20, 20, 30, 40);
    for ($i = 0; $i < count($header); ++$i) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
    }
    $pdf->Ln();

    foreach ($summary as $name => $statusData) {
        $pdf->Cell($w[0], 6, $name, 'LR');
        $pdf->Cell($w[1], 6, $statusData['on_time'], 'LR');
        $pdf->Cell($w[2], 6, $statusData['early'], 'LR');
        $pdf->Cell($w[3], 6, $statusData['late'], 'LR');
        $pdf->Cell($w[4], 6, $statusData['absent'], 'LR');
        $pdf->Cell($w[5], 6, $statusData['on_official_business'], 'LR');
    
        // Check if the employee has no absences for perfect attendance
        $perfectAttendance = ($statusData['absent'] == 0) ? 'YES' : 'NO';
    
        $pdf->Cell($w[6], 6, $perfectAttendance, 'LR', 1, 'R');
    }
    
    // Add a border below the table
    $pdf->Cell(array_sum($w), 0, '', 'T');

    $pdf->Output('monthly_summary.pdf', 'D');
}

$conn->close();
error_log('Monthly Summary PDF generation completed.');
?>
