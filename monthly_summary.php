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

// Get the selected month and year from the URL parameters
$selectedMonth = isset($_GET['month']) ? $_GET['month'] : date("m");
$selectedYear = isset($_GET['year']) ? $_GET['year'] : date("Y");

// Query to get the monthly summary from the attendance table for AM-TIME-IN and PM-TIME-IN
$sql = "SELECT name, status, COUNT(*) as total FROM attendance WHERE DATE_FORMAT(date, '%Y-%m') = '$selectedYear-$selectedMonth' AND (clock ='AM-TIME-IN' OR clock ='PM-TIME-IN' OR status = 'On-Leave') GROUP BY name, status";
$result = $conn->query($sql);

if ($result === false) {
    echo 'Error executing the query: ' . $conn->error;
} else {
    $summary = array();

    // Calculate the totals for each employee and status
    while ($row = $result->fetch_assoc()) {
        $name = $row['name'];
        $status = strtolower($row['status']);
        if (!isset($summary[$name])) {
            $summary[$name] = array(
                'on_time' => 0,
                'early' => 0,
                'late' => 0,
                'absent' => 0,
                'on-official business' => 0,
                'on-leave' => 0,
                'perfect_attendance' => 'NO'
            );
        }
        $summary[$name][$status] += $row['total'];

        // Update perfect attendance status
        if ($status === 'absent') {
            $summary[$name]['perfect_attendance'] = 'NO';
        }
    }

    // Generate PDF
    $pdf = new TCPDF();
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Add content to the PDF
    $pdf->SetFont('helvetica', 'B', 16); // Set font to bold and increase font size
    $pdf->Cell(0, 10, 'BAWA ELEMENTARY SCHOOL', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 8); // Reset font to regular

    // Check if there are no attendance records
    if (empty($summary)) {
        $pdf->Cell(0, 10, "No attendance records for $selectedMonth-$selectedYear", 0, 1, 'C');
    } else {
        $pdf->Cell(0, 10, "Monthly Summary - " . date("F Y", strtotime("$selectedYear-$selectedMonth-01")), 0, 1, 'C');

        // Create a table to display the summary
        $pdf->SetFillColor(200, 220, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.3);

        $header = array('Name', 'On-Time', 'Early', 'Late', 'Absent', 'On-Official Business', 'On-Leave', 'Perfect Attendance');
        $w = array(40, 18, 18, 18, 18, 30, 18, 28);
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
            $pdf->Cell($w[5], 6, $statusData['on-official business'], 'LR');
            $pdf->Cell($w[6], 6, $statusData['on-leave'], 'LR');

            // Check if the employee has no absences for perfect attendance
            $perfectAttendance = ($statusData['absent'] == 0) ? 'YES' : 'NO';

            $pdf->Cell($w[7], 6, $perfectAttendance, 'LR', 1, 'R');
        }

        // Add a border below the table
        $pdf->Cell(array_sum($w), 0, '', 'T');
    }

    $pdf->Output('monthly_summary.pdf', 'D');
}

$conn->close();
error_log('Monthly Summary PDF generation completed.');
?>